<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 02/10/2017
 * Time: 12:22
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \User\Model\User;


class Controller
{
    protected $db = null;
    protected $user = null;
    protected $pass = null;
    protected $request = null;
    protected $routes = null;

    /** @var User $authUser  */
    protected $authUser = null;
    protected $isUserAuthenticated = false;


    public function setConnection($db, $user, $pass){
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setTwig($twig){
        $this->twig = $twig;
    }

    public function setRequest($request){
        $this->request = $request;
        if( !empty($_SESSION['authenticatedUser'])){
            /** @var User $user */
            $user = $_SESSION['authenticatedUser'];
            if( $user->getId() > 0){
                $this->authUser = $user;
                $this->isUserAuthenticated = true;
            }
        }
    }

    public function generateCSRFToken(){
        return md5(uniqid(rand(), TRUE));
    }

    public function render($template, $context=array())
    {
        $context['baseUrl'] = BASE_URL;
        $context['frontUrl'] = FRONT_URL;
        if( empty($_SESSION['csrftoken'])){
            $_SESSION['csrftoken'] = $this->generateCSRFToken();
        }
        $context['csrftoken'] = $_SESSION['csrftoken'];
        if( !empty($_SESSION['isUserAuthenticated'])
            && !empty($_SESSION['authenticatedUser']) && $_SESSION['authenticatedUser']->getId() > 0) {
            $context['isUserAuthenticated'] = true;
            $context['authenticatedUser'] = $_SESSION['authenticatedUser'];
        }
        else {
            $context['isUserAuthenticated'] = false;
            $context['authenticatedUser'] = null;
        }

        return new Response(
            $this->twig->render($template, $context)
        );
    }

    public function redirect($url){
        $url = strtolower($url);
        $start = substr( $url, 0, 5 );
        $base = substr( FRONT_URL, 0, 5 );
        if( $start != $base ){
            // fix url - add base
            $url = sprintf('%s%s', FRONT_URL, $url);
        }
        $headers = array('Location' => $url);
        return new Response('', 302, $headers);
    }

    public function loginRequired(){
        if (!$this->isUserAuthenticated) {
            $this->redirect(sprintf('%s/login'));
        }
    }

}