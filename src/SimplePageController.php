<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:46
 */

use Symfony\Component\HttpFoundation\Request;


class SimplePageController extends Controller
{

    public function indexAction(Request $request)
    {
        $this->request = $request;
        $context = array('name' => 'Home');
        return $this->render('home.html.twig', $context);

    }

    public function aboutAction(Request $request)
    {
        $this->request = $request;
        $context = array('name' => 'About');
        return $this->render('about.html.twig', $context);

    }

    public function loginAction(Request $request)
    {
        $this->request = $request;
        $context = array('name' => 'Login');
        return $this->render('login.html.twig', $context);

    }
}