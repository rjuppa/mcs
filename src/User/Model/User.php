<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 01/10/2017
 * Time: 23:47
 */

namespace User\Model;

use DateTime;

class User
{
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $passwordHash;
    protected $isActive;
    protected $type;        // ADMIN, AUTHOR, REVIEWER
    protected $created;
    protected $deleted;

    public $types = array('ADMIN', 'AUTHOR', 'REVIEWER');


//    public function __construct($firstName, $lastName, $email, $typeStr)
//    {
//        // validation
//        $this->validateEmail($email);
//        $this->validateLastName($lastName);
//
//        $this->firstName = $firstName;
//        $this->lastName = $lastName;
//        $this->email = $email;
//        $this->type = $this->setTypeText(strtoupper($typeStr));
//        $this->isActive = true;
//        $this->created = new DateTime();
//    }

    public function __construct(){
        $this->type = 1;    //AUTHOR
        $this->isActive = true;
        $this->created = new DateTime();
    }

    public static function withValidation( $firstName, $lastName, $email, $typeStr ) {
        $instance = new self();
        // validation
        $instance->validateEmail($email);
        $instance->validateLastName($lastName);

        $instance->firstName = $firstName;
        $instance->lastName = $lastName;
        $instance->email = $email;
        $instance->type = $instance->setTypeText(strtoupper($typeStr));
        $instance->isActive = true;
        $instance->created = new DateTime();
        return $instance;
    }

    public static function getTypeOpts(){
        return array('1' => 'AUTOR', '2' => 'REVIEWER', '4' => 'ADMIN');
    }

    public static function getIsActiveOpts(){
        return array('1' => 'ANO', '0' => 'NE');
    }


    public function getId(){ return $this->id; }
    public function getFirstName(){ return $this->firstName; }
    public function getLastName(){ return $this->lastName; }
    public function getEmail(){ return $this->email; }
    public function getType(){ return $this->type; }
    public function getIsActive(){ return $this->isActive; }
    public function getCreated(){ return $this->created; }

    public function getViewUrl(){ return sprintf('%s/users/%s', FRONT_URL, $this->id); }
    public function getEditUrl(){ return sprintf('%s/users/%s/edit', FRONT_URL, $this->id); }
    public function getDeleteUrl(){ return sprintf('%s/users/%s/delete', FRONT_URL, $this->id); }

    public function setId($id){ return $this->id = $id; }
    public function setType($typeInt){ return $this->type = $typeInt; }
    public function setFirstName($firstName){ return $this->firstName = $firstName; }
    public function setLastName($lastName){ $this->validateLastName($lastName); return $this->lastName = $lastName; }
    public function setEmail($email){ $this->validateEmail($email); return $this->email = $email; }
    public function setIsActive($isActive){ return $this->isActive = $isActive; }
    public function setCreated($created){ return $this->created = $created; }
    public function setDeleted($deleted){ return $this->deleted = $deleted; }


    public function getTypeText(){
        switch ($this->type){
            case 1: return 'AUTHOR';
            case 2: return 'REVIEWER';
            case 4: return 'ADMIN';
        }
        throw new \Exception('User Type is not valid. (Supported: ADMIN, AUTHOR, REVIEWER)');
    }

    public function setTypeText($typeString){
        switch ($typeString){
            case 'AUTHOR': return 1;
            case 'REVIEWER': return 2;
            case 'ADMIN': return 4;
        }
        throw new \Exception('User Type is not valid. (Supported: ADMIN, AUTHOR, REVIEWER)');
    }

    public function getIsActiveText(){
        if ($this->isActive) {
            return 'ANO';
        }
        else{
            return 'NE';
        }
    }

    public function validate(){
        // validation
        $this->validateEmail($this->email);
        $this->validateLastName($this->lastName);
    }

    private function validateEmail($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        throw new \Exception('Email is not valid.');
    }

    private function validateLastName($lastName){
        if (!empty($lastName)) {
            return true;
        }
        throw new \Exception('LastName is required.');
    }

    public function isLeapYear($year = null)
    {
        if (null === $year) {
            $year = date('Y');
        }

        return 0 == $year % 400 || (0 == $year % 4 && 0 != $year % 100);
    }

    public function isAdmin(){
        return $this->type == 'ADMIN';
    }

    public function isAuthor(){
        return $this->type == 'AUTHOR';
    }

    public function isReviewer(){
        return $this->type == 'REVIEWER';
    }

}