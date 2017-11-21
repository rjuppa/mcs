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
    public const AUTHOR = 1;
    public const REVIEWER = 2;
    public const ADMIN = 4;

    public const ROLE_NAMES = array('ADMIN', 'AUTHOR', 'REVIEWER');

    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $passwordHash;
    protected $isActive;
    protected $type;        // ADMIN, AUTHOR, REVIEWER
    protected $created;
    protected $deleted;

    public function __construct(){
        $this->type = self::AUTHOR;    //AUTHOR
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
    public function getDisplayName(){ return sprintf('%s %s', $this->firstName, $this->lastName); }
    public function getEmail(){ return $this->email; }
    public function getType(){ return $this->type; }
    public function getIsActive(){ return $this->isActive; }
    public function getCreated(){ return $this->created; }

    public function getViewUrl(){ return sprintf('%s/users/%s', FRONT_URL, $this->id); }
    public function getEditUrl(){ return sprintf('%s/users/%s/edit', FRONT_URL, $this->id); }
    public function getDeleteUrl(){ return sprintf('%s/users/%s/delete', FRONT_URL, $this->id); }

    public function setId($id){ $this->id = intval($id); }
    public function setType($typeInt){$this->type = intval($typeInt);}
    public function setFirstName($firstName){ $this->firstName = $firstName; }
    public function setLastName($lastName){ $this->validateLastName($lastName); $this->lastName = $lastName; }
    public function setEmail($email){ $this->validateEmail($email); $this->email = $email; }
    public function setIsActive($isActive){ $this->isActive = $isActive; }
    public function setCreated($created){ $this->created = $created; }
    public function setDeleted($deleted){ $this->deleted = $deleted; }


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
            case 'AUTHOR': return self::AUTHOR;
            case 'REVIEWER': return self::REVIEWER;
            case 'ADMIN': return self::ADMIN;
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

    public function isAdmin(){
        return intval($this->type) == self::ADMIN;
    }

    public function isAuthor(){
        return intval($this->type) == self::AUTHOR;
    }

    public function isReviewer(){
        return intval($this->type) == self::REVIEWER;
    }

    public function isReviewerOrAdmin(){
        return intval($this->type) == self::REVIEWER || intval($this->type) == self::ADMIN;
    }

}