<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:46
 */


class SimplePageController extends Controller
{

    public function indexAction()
    {
        $context = array('name' => 'Home');
        return $this->render('home.html.twig', $context);

    }

    public function aboutAction()
    {
        $context = array('name' => 'About');
        return $this->render('about.html.twig', $context);

    }

    public function loginAction()
    {
        $context = array('name' => 'Login');
        return $this->render('login.html.twig', $context);

    }
}