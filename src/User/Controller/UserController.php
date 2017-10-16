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

    public function loginAction(Request $request)
    {
        $this->request = $request;
        $service = $this->getService();

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array();
            return $this->render('login.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            if( !empty($csrf) && !empty($email) && !empty($password) ){
                try{
                    $user = $service->getUserByEmail($email);
                }
                catch(UserNotFoundException $e){
                    $message = 'Naplatný email';
                }
                catch(PDOException $e)
                {
                    $message = sprintf('DB error: %s', $e->getMessage());
                }
                finally{
                    $service->close();
                }

                // authenticate
                if( $user && $user->getEmail() == $email){
                    $authenticatedUser = $service->authenticate($email, $password);
                    if( $authenticatedUser && $authenticatedUser->getEmail() == $email){
                        session_start();
                        $_SESSION['user'] = $authenticatedUser;
                        $context = array('message' => $message);
                        return $this->render('user/profile.html.twig', $context);
                    }
                }
            }
            else{
                $message = 'Přihlášení se nepodařilo.';
            }

            $context = array('message' => $message);
            return $this->render('login.html.twig', $context);
        }



        $users = $service->getUsersAll();
        try{
            //$users = $service->getUsersAll();
            //foreach($users as $user){
            //    $users[$user->getEmail()] = $user->getEmail();
            //}

        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        $context = array(
            'users' => $users);
        return $this->render('user/index.html.twig', $context);

    }

    public function indexAction(Request $request)
    {
        $this->request = $request;
        $service = $this->getService();
        $users = $service->getUsersAll();
        try{
            //$users = $service->getUsersAll();
            //foreach($users as $user){
            //    $users[$user->getEmail()] = $user->getEmail();
            //}

        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $service->close();
        }

        $context = array(
            'users' => $users);
        return $this->render('user/index.html.twig', $context);

    }

    public function viewAction(Request $request, $id)
    {
        $this->request = $request;
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

        $context = array(
            'user' => $user);
        return $this->render('user/view.html.twig', $context);
    }

    public function profileAction(Request $request)
    {

        $service = $this->getService();
        try{
            $user = $service->getUserById(3);
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

        $context = array(
            'user' => $user);
        return $this->render('user/profile.html.twig', $context);
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