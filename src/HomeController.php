<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:46
 */

use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{

    public function indexAction(Request $request)
    {
        $this->request = $request;
        $context = array('name' => 'Home');
        return $this->render('home.html.twig', $context);

    }
}