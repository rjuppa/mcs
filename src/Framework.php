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


class UnauthorizedException extends Exception{}
class NotAuthenticatedException extends Exception{}

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
        return 'mysql:host=127.0.0.1;port=3306;dbname=mcs';
    }

    public function getDBUser(){
        return 'admin';
    }

    public function getDBPass(){
        return 'admin'; // getenv('MCS_PASSWORD');
    }

    public function handle(Request $request, $twig)
    {
        $this->matcher->getContext()->fromRequest($request);
        try {
            $route = $this->matcher->match($request->getPathInfo());
            $request->attributes->add($route);
            $controller = $this->controllerResolver->getController($request);

            /** @var Controller $controllerObj */
            $controllerObj = $controller[0];
            $controllerObj->setTwig($twig);
            $controllerObj->setRequest($request);
            $controllerObj->setService($this->getDBString(), $this->getDBUser(), $this->getDBPass());
            $arguments = $this->argumentResolver->getArguments($request, $controller);
            return call_user_func_array($controller, $arguments);
        }
        catch (ResourceNotFoundException $e) {
            $context = array('message' => 'Stránka nebyla nalezena.');

            /** @var SimplePageController $controllerObj */
            $controllerObj = new SimplePageController();
            $controllerObj->setTwig($twig);
            $controllerObj->setRequest($request);
            return $controllerObj->render('404.html.twig', $context);
        }
        catch(NotAuthenticatedException $e){
            return $controllerObj->redirect(sprintf('%s/login'));
        }
        catch(UnauthorizedException $e){
            $context = array('message' => 'Nemáte dostatečná opravnění.');
            /** @var SimplePageController $controllerObj */
            $controllerObj = new SimplePageController();
            $controllerObj->setTwig($twig);
            $controllerObj->setRequest($request);
            return $controllerObj->render('401.html.twig', $context);
        }
        catch (\Exception $e) {
            $context = array('message' => $e->getMessage());

            /** @var SimplePageController $controllerObj */
            $controllerObj = new SimplePageController();
            $controllerObj->setTwig($twig);
            $controllerObj->setRequest($request);
            return $controllerObj->render('500.html.twig', $context);
        }
    }
}