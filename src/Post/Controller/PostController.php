<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:46
 */

namespace Post\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Controller;
use PDOException;

use \User\Model\User;
use \User\Service\UserService;
use \User\Service\UserNotFoundException;
use \User\Service\UserDuplicateEmailException;

class PostController extends Controller
{
    protected $service;

    public function getService(){
        return new UserService($this->db, $this->user, $this->pass);
    }

    public function indexAction(Request $request)
    {
        $this->request = $request;
        $service = $this->getService();

        $context = array(
            'posts' => []);
        return $this->render('post/index.html.twig', $context);

    }

}