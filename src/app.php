<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:21
 */


use Symfony\Component\Routing;


$routes = new Routing\RouteCollection();
$routes->add('home1', new Routing\Route('/home', array(
    '_controller' => 'HomeController::indexAction',
    'template'    => 'index.html.twig',
)));
$routes->add('home', new Routing\Route('/', array(
    '_controller' => 'HomeController::indexAction',
    'template'    => 'index.html.twig',
)));

$routes->add('user-create', new Routing\Route('/users/create', array(
    '_controller' => 'User\Controller\UserController::createAction',
)));
$routes->add('user-list', new Routing\Route('/users/list', array(
    '_controller' => 'User\Controller\UserController::indexAction',
    'template'    => 'index.html.twig',
)));
$routes->add('user-view', new Routing\Route('/users/{id}', array(
    'id' => null,
    '_controller' => 'User\Controller\UserController::viewAction',
)));


return $routes;
