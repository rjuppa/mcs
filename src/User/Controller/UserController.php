<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:46
 */

namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Controller;
use PDOException;

use \User\Model\User;
use \User\Service\UserService;
use \User\Service\UserNotFoundException;
use \User\Service\UserDuplicateEmailException;

class UserController extends Controller
{
    protected $service;

    public function getService(){
        return new UserService($this->db, $this->user, $this->pass);
    }

    public function indexAction(Request $request)
    {
        $service = $this->getService();
        $users = array();
        try{
            $users = $service->getUsersAll();
            foreach($users as $user){
                print_r('-------------<br><pre>');
                print_r($user->getEmail());
                print_r('-------------</pre></br>');
            }

        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        return new Response('All users...');
    }


    public function createAction(Request $request)
    {
        $service = $this->getService();
        $user = new User('Josef','Dvorak','pepa@dvorak.cz','AUTHOR');
        try{
            $userId = $service->createUser($user);
            $user = $service->getUserById($userId);
        }
        catch(UserDuplicateEmailException $e){
            return new Response('Duplicate email.');
        }
        catch(PDOException $e)
        {
            if (strpos($e->getMessage(), 'uniq_email') !== false) {
                return new Response('Error: Duplicate email');
            }
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        return new Response('New User was created. email='.$user->getEmail());
    }

    public function updateAction(Request $request, $id)
    {
        $service = $this->getService();
        try{
            $user = $service->getUserById($id);
            $user->setFirstName('Edited');
            $service->updateUser($user);
            $user = $service->getUserById($id);
        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        return new Response('New User was created. email='.$user->getEmail());
    }

    public function viewAction(Request $request, $id)
    {

        $service = $this->getService();
        try{
            $user = $service->getUserById($id);
        }
        catch(UserNotFoundException $e){
            return new Response('User not found.');
        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        return new Response('User= ' . $user->getEmail());
    }

    public function deleteAction(Request $request, $id)
    {
        $service = $this->getService();
        try{
            $user = $service->getUserById($id);
            if($user){
                $service->deleteUser($user);
            }
        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        return new Response('Object was deleted.');
    }


}