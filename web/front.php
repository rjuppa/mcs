<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 22:14
 */

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;


define('BASE_URL',     '/mcs/web');
define('FRONT_URL',    '/mcs/web/front.php');
define('PROJ_PATH',    '/opt/lampp/htdocs/mcs');


session_start();

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/router.php';
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$a = $_GET;
$b = $_POST;
require __DIR__.'/../src/Framework.php';
$framework = new Framework($matcher, $controllerResolver, $argumentResolver);
$response = $framework->handle($request, $twig);
$response->send();
