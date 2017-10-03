<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 02/10/2017
 * Time: 12:22
 */


use \User\Model\User;


class Controller
{
    protected $db = null;
    protected $user = null;
    protected $pass = null;

    public function setConnection($db, $user, $pass){
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
    }
}