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
    '_controller' => 'SimplePageController::indexAction',
)));
$routes->add('home', new Routing\Route('/', array(
    '_controller' => 'SimplePageController::indexAction',
)));

$routes->add('about', new Routing\Route('/about', array(
    '_controller' => 'SimplePageController::aboutAction',
)));




$routes->add('user-create', new Routing\Route('/users/create', array(
    '_controller' => 'User\Controller\UserController::createAction',
)));
$routes->add('user-list', new Routing\Route('/users/list', array(
    '_controller' => 'User\Controller\UserController::indexAction',
)));
$routes->add('user-login', new Routing\Route('/login', array(
    '_controller' => 'User\Controller\UserController::loginAction',
)));
$routes->add('user-profile', new Routing\Route('/users/me', array(
    '_controller' => 'User\Controller\UserController::profileAction',
)));
$routes->add('user-view', new Routing\Route('/users/{id}', array(
    'id' => null,
    '_controller' => 'User\Controller\UserController::viewAction',
)));


$routes->add('post-list', new Routing\Route('/posts/list', array(
    '_controller' => 'Post\Controller\PostController::indexAction',
)));

return $routes;
