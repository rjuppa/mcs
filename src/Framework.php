<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:53
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;


class Framework
{
    protected $matcher;
    protected $controllerResolver;
    protected $argumentResolver;

    public function __construct(UrlMatcher $matcher, ControllerResolver $controllerResolver, ArgumentResolver $argumentResolver)
    {
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    public function getDBString(){
        return 'mysql:host=localhost;port=3307;dbname=mcs';
    }

    public function getDBUser(){
        return 'mcs';
    }

    public function getDBPass(){
        return getenv('MCS_PASSWORD');
    }

    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);
        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));
            $controller = $this->controllerResolver->getController($request);
            $controllerObj = $controller[0];
            $controllerObj->setConnection($this->getDBString(), $this->getDBUser(), $this->getDBPass());
            $arguments = $this->argumentResolver->getArguments($request, $controller);
            return call_user_func_array($controller, $arguments);

        } catch (ResourceNotFoundException $e) {
            return new Response('Not Found', 404);

        } catch (\Exception $e) {
            return new Response('An error occurred:'.$e->getMessage(), 500);
        }
    }
}