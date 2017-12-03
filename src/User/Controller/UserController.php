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
    /** @var UserService */
    protected $service;

    public function getService(){
        return new UserService($this->db, $this->user, $this->pass);
    }

    public function setService($db, $user, $pass){
        $this->setConnection($db, $user, $pass);
        $this->service = $this->getService();
    }

    /** Logout View
     *
     *
     * @return Response
     */
    public function logoutAction(){
        session_destroy();
        return $this->redirect('/posts/public');
    }

    /** Login View
     *
     *
     * @return Response
     */
    public function loginAction(){
        $message = '';
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
                    $user = $this->service->getUserByEmail($email);
                    // authenticate
                    if( $user && $user->getIsActive() && $user->getEmail() == $email){
                        $authenticatedUser = $this->service->authenticate($email, $password);
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
                    $this->service->close();
                }
            }
            if(empty($message)){
                $message = 'Přihlášení se nepodařilo.';
            }
            $context = array('message' => $message);
            return $this->render('login.html.twig', $context);
        }

        $users = $this->service->getUsersAll();
        $context = array('users' => $users);
        return $this->render('user/index.html.twig', $context);

    }


    /** List of all users
     *
     * @return Response
     */
    public function indexAction() {
        $this->doAuthorize(array('ADMIN'));
        $users = [];
        try{
            $users = $this->service->getUsersAll();
        }
        catch(PDOException $e)
        {
            return new Response($e->getMessage());
        }
        finally{
            $this->service->close();
        }

        $context = array(
            'users' => $users);
        return $this->render('user/index.html.twig', $context);

    }


    /** Detail of a user
     *
     *
     * @param $id
     * @return Response
     */
    public function viewAction($id) {
        $this->doAuthorize(array('ADMIN'));

        try{
            $user = $this->service->getUserById($id);
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
            $this->service->close();
        }

        $context = array('user' => $user);
        return $this->render('user/view.html.twig', $context);
    }


    /** Authenticated user profile view
     *
     *
     * @return Response
     */
    public function profileAction() {
        $this->doAuthorize(array());
        try{
            $user = $this->service->getUserById($this->authUser->getId());
            $_SESSION['authenticatedUser'] = $user;
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
            $this->service->close();
        }

        $context = array(
            'user' => $user);
        return $this->render('user/profile.html.twig', $context);
    }


    /** Create a new user view
     *
     *
     * @return Response
     */
    public function createAction(){
        $this->doAuthorize(array('ADMIN'));
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
                    catch (\Exception $e){
                        $context['user'] = $user;
                        $context['message'] = $e->getMessage();
                        return $this->render('user/edit.html.twig', $context);
                    }

                    // Create a new user
                    try{
                        $userId = $this->service->createUser($user);
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
                        $this->service->close();
                    }
                }
            }
            return $this->redirect('/users/list');
        }
        // never happen
        return new Response('Bad request', 400);
    }

    /** Edit a user view
     *
     *
     * @param $id
     * @return Response
     */
    public function editAction($id){
        if( $this->isUserAuthenticated && ($id != $this->authUser->getId()) ){
            $this->doAuthorize(array('ADMIN'));
        }

        try{
            $user = $this->service->getUserById($id);
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
            $this->service->close();
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
                    if( $this->authUser->getTypeText() == 'ADMIN' ) {
                        $user->setType(intval($type));
                        $user->setIsActive(intval($isActive));
                    }
                    try{
                        // validation
                        $user->validate();
                    }
                    catch (\Exception $e){
                        $context = array(
                            'user' => $user,
                            'title' => 'Editace uživatele',
                            'typeOpts' => $typeOpts,
                            'isActiveOpts' => $isActiveOpts,
                            'message' => $e->getMessage());
                        return $this->render('user/edit.html.twig', $context);
                    }

                    // save
                    $this->service = $this->getService();
                    try{
                        $this->service->editUser($user);
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
                        $this->service->close();
                    }
                }
            }
            if( $this->authUser->getTypeText() == 'ADMIN' ) {
                return $this->redirect('/users/list');
            }
            return $this->redirect('/users/me');
        }

        // never happen
        return new Response('Bad request', 400);
    }

    /** Delete an existing user view
     *
     *
     * @param $id
     * @return Response
     */
    public function deleteAction($id){
        $this->doAuthorize(array('ADMIN'));
        try{
            $user = $this->service->getUserById($id);
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
            $this->service->close();
        }

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array('user' => $user);
            return $this->render('user/delete.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            if( !empty($csrf) && !empty($user) ) {
                if( $csrf == $_SESSION['csrftoken']){
                    $this->service = $this->getService();
                    try{
                        $this->service->deleteUser($user);
                    }
                    catch(PDOException $e)
                    {
                        $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $this->service->close();
                    }
                }
            }
            return $this->redirect('/users/list');
        }

        // never happen
        return new Response('Bad request', 400);
    }

}