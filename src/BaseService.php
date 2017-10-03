<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 02/10/2017
 * Time: 13:42
 */

class BaseService
{
    protected $pdo;

    public function __construct($connstr, $user, $pass)
    {
        try{
            $this->pdo = new PDO($connstr, $user, $pass);
            // Set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            die("ERROR: Could not connect. " . $e->getMessage());
        }
    }

    public function close(){
        $this->pdo = null;
    }
}