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

    public function logoutAction(Request $request){
        session_destroy();
        return $this->redirect('/home');
    }

    public function loginAction(Request $request)
    {
        $message = '';
        $this->request = $request;
        $service = $this->getService();

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            return $this->render('login.html.twig');
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            if( !empty($csrf) && !empty($email) && !empty($password) ){
                $email = strtolower($email);
                try{
                    $user = $service->getUserByEmail($email);
                    // authenticate
                    if( $user && $user->getEmail() == $email){
                        $authenticatedUser = $service->authenticate($email, $password);
                        if( $authenticatedUser && $authenticatedUser->getEmail() == $email){
                            $_SESSION['authenticatedUser'] = $authenticatedUser;
                            $_SESSION['isUserAuthenticated'] = true;
                            return $this->redirect('/users/me');
                        }
                        else{
                            $message = 'Naplatné heslo nebo email.';
                        }
                    }
                }
                catch(UserNotFoundException $e){
                    $message = 'Neplatný email';
                }
                catch(PDOException $e)
                {
                    $message = sprintf('DB error: %s', $e->getMessage());
                }
                finally{
                    $service->close();
                }
            }
            else{
                $message = 'Přihlášení se nepodařilo.';
            }

            $context = array('message' => $message);
            return $this->render('login.html.twig', $context);
        }

        $users = $service->getUsersAll();
        $context = array('users' => $users);
        return $this->render('user/index.html.twig', $context);

    }

    public function indexAction(Request $request)
    {
        $this->request = $request;
        $service = $this->getService();
        $users = [];
        try{
            $users = $service->getUsersAll();
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
            $context = array('message' => 'Uživatel nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
        }

        $context = array('user' => $user);
        return $this->render('user/view.html.twig', $context);
    }

    public function profileAction(Request $request)
    {
        $this->request = $request;
        $service = $this->getService();
        try{
            $user = $_SESSION['authenticatedUser'];
        }
        catch(UserNotFoundException $e){
            $context = array('message' => 'Uživatel nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
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
        $this->request = $request;
        $isActiveOpts = User::getIsActiveOpts();
        $typeOpts = User::getTypeOpts();
        $context = array(
            'title' => 'Nový uživatel',
            'typeOpts' => $typeOpts,
            'isActiveOpts' => $isActiveOpts);

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){           // GET
            $user = new User();
            $user->setType(1);
            $user->setIsActive(1);
            $context['user'] = $user;
            return $this->render('user/edit.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){          // POST
            $csrf = $_POST['csrftoken'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $type = $_POST['type'];
            $isActive = $_POST['isActive'];

            if( !empty($csrf) && !empty($email) && !empty($lastName) ) {
                if( $csrf == $_SESSION['csrftoken']){
                    $user = new User();
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    $user->setEmail($email);
                    $user->setType(intval($type));
                    $user->setIsActive(intval($isActive));
                    try{
                        // validation
                        $user->validate();
                    }
                    catch (Exception $e){
                        $context['user'] = $user;
                        $context['message'] = $e->getMessage();
                        return $this->render('user/edit.html.twig', $context);
                    }

                    // Create a new user
                    $service = $this->getService();
                    try{
                        $userId = $service->createUser($user);
                    }
                    catch(UserDuplicateEmailException $e){
                        $context['user'] = $user;
                        $context['message'] = 'Duplikátní email.';
                        return $this->render('user/edit.html.twig', $context);
                    }
                    catch(PDOException $e)
                    {
                        if (strpos($e->getMessage(), 'uniq_email') !== false) {
                            $context['user'] = $user;
                            $context['message'] = 'Duplikátní email.';
                            return $this->render('user/edit.html.twig', $context);
                        }
                        $context['message'] = sprintf('DB Chyba: %s', $e->getMessage());
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/users/list');
        }
        // never happen
        return new Response('Bad request', 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $this->request = $request;
        $service = $this->getService();
        try{
            $user = $service->getUserById($id);
        }
        catch(UserNotFoundException $e){
            $context = array('message' => 'Uživatel nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
        }

        $isActiveOpts = User::getIsActiveOpts();
        $typeOpts = User::getTypeOpts();
        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array(
                'user' => $user,
                'title' => 'Editace uživatele',
                'typeOpts' => $typeOpts,
                'isActiveOpts' => $isActiveOpts);
            return $this->render('user/edit.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){      // POST
            $csrf = $_POST['csrftoken'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            //$email = $_POST['email'];   // skip email editing
            $type = $_POST['type'];
            $isActive = $_POST['isActive'];

            if( !empty($csrf) && !empty($user) ) {
                if( $csrf == $_SESSION['csrftoken']){

                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    $user->setType(intval($type));
                    $user->setIsActive(intval($isActive));
                    try{
                        // validation
                        $user->validate();
                    }
                    catch (Exception $e){
                        $context = array(
                            'user' => $user,
                            'title' => 'Editace uživatele',
                            'typeOpts' => $typeOpts,
                            'isActiveOpts' => $isActiveOpts,
                            'message' => $e->getMessage());
                        return $this->render('user/edit.html.twig', $context);
                    }

                    // save
                    $service = $this->getService();
                    try{
                        $service->editUser($user);
                    }
                    catch(PDOException $e)
                    {
                        if (strpos($e->getMessage(), 'uniq_email') !== false) {
                            $context = array(
                                'user' => $user,
                                'title' => 'Editace uživatele',
                                'isActiveOpts' => $isActiveOpts,
                                'typeOpts' => $typeOpts,
                                'message' => 'Duplikátní email.');
                            return $this->render('user/edit.html.twig', $context);
                        }
                        $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/users/list');
        }

        // never happen
        return new Response('Bad request', 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $this->request = $request;
        $service = $this->getService();
        try{
            $user = $service->getUserById($id);
        }
        catch(UserNotFoundException $e){
            $context = array('message' => 'Uživatel nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
        }

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array('user' => $user);
            return $this->render('user/delete.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            if( !empty($csrf) && !empty($user) ) {
                if( $csrf == $_SESSION['csrftoken']){
                    $service = $this->getService();
                    try{
                        $service->deleteUser($user);
                    }
                    catch(PDOException $e)
                    {
                        $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/users/list');
        }

        // never happen
        return new Response('Bad request', 400);
    }

}