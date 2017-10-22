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


// users
$routes->add('user-login', new Routing\Route('/login', array(
    '_controller' => 'User\Controller\UserController::loginAction',
)));
$routes->add('user-logout', new Routing\Route('/logout', array(
    '_controller' => 'User\Controller\UserController::logoutAction',
)));
$routes->add('user-list', new Routing\Route('/users/list', array(
    '_controller' => 'User\Controller\UserController::indexAction',
)));
$routes->add('user-profile', new Routing\Route('/users/me', array(
    '_controller' => 'User\Controller\UserController::profileAction',
)));
$routes->add('user-create', new Routing\Route('/users/create', array(
    '_controller' => 'User\Controller\UserController::createAction',
)));
$routes->add('user-edit', new Routing\Route('/users/{id}/edit', array(
    'id' => null,
    '_controller' => 'User\Controller\UserController::editAction',
)));
$routes->add('user-delete', new Routing\Route('/users/{id}/delete', array(
    'id' => null,
    '_controller' => 'User\Controller\UserController::deleteAction',
)));
$routes->add('user-view', new Routing\Route('/users/{id}', array(
    'id' => null,
    '_controller' => 'User\Controller\UserController::viewAction',
)));


// posts
$routes->add('post-list', new Routing\Route('/posts/list', array(
    '_controller' => 'Post\Controller\PostController::indexAction',
)));
$routes->add('post-mylist', new Routing\Route('/posts/mylist', array(
    '_controller' => 'Post\Controller\PostController::myIndexAction',
)));
$routes->add('post-create', new Routing\Route('/posts/create', array(
    '_controller' => 'Post\Controller\PostController::createAction',
)));
$routes->add('post-edit', new Routing\Route('/posts/{id}/edit', array(
    'id' => null,
    '_controller' => 'Post\Controller\PostController::editAction',
)));
$routes->add('post-delete', new Routing\Route('/posts/{id}/delete', array(
    'id' => null,
    '_controller' => 'Post\Controller\PostController::deleteAction',
)));
$routes->add('post-download', new Routing\Route('/posts/{id}/download', array(
    'id' => null,
    '_controller' => 'Post\Controller\PostController::downloadAction',
)));
$routes->add('post-publish', new Routing\Route('/posts/{id}/publish', array(
    'id' => null,
    '_controller' => 'Post\Controller\PostController::publishAction',
)));
$routes->add('post-view', new Routing\Route('/posts/{id}', array(
    'id' => null,
    '_controller' => 'Post\Controller\PostController::viewAction',
)));




return $routes;
