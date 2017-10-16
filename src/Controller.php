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

    public function setConnection($db, $user, $pass){
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setTwig($twig){
        $this->twig = $twig;
    }

    public function render($template, $context=array())
    {
        //echo $this->twig->render($template, $context);
        $context['baseUrl'] = '/mcs/web';
        $context['frontUrl'] = '/mcs/web/front.php';

        return new Response(
            $this->twig->render($template, $context)
        );
    }

}