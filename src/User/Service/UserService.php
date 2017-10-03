<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 02/10/2017
 * Time: 12:03
 */
namespace User\Service;

use \User\Model\User;
use \BaseService;
use PDO;
use Exception;

class UserNotFoundException extends Exception{ }
class UserDuplicateEmailException extends Exception{ }

class UserService extends BaseService
{

    private function mapUser($row){
        $user = new User($row->first_name, $row->last_name, $row->email, 'AUTHOR');
        $user->setId($row->id);
        $user->setType($row->type);
        $user->setIsActive($row->is_active);
        $user->setCreated($row->created);
        $user->setDeleted($row->deleted);
        return $user;
    }

    public function getUsersAll(){
        $users = array();
        $sql = "SELECT * FROM users WHERE deleted=0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $user = $this->mapUser($row);
            array_push($users, $user);
        }
        return $users;
    }

    public function getUserById($id = null){
        $sql = "SELECT * FROM users WHERE deleted=0 AND id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = intval($id);
        $stmt->execute();
        $row = $stmt->fetchObject();
        if(empty($row)){
            throw new UserNotFoundException();
        }
        return $this->mapUser($row);
    }

    public function getUserByEmail($email = null){
        $sql = "SELECT * FROM users WHERE deleted=0 AND email=:email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $email = strtolower($email);
        $stmt->execute();
        $row = $stmt->fetchObject();
        if(empty($row)){
            throw new UserNotFoundException();
        }
        return $this->mapUser($row);
    }

    public function createUser(User $user = null){
        $sql = "INSERT INTO users (first_name, last_name, email) VALUES (:first_name, :last_name, :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();
        $stmt->execute();
        $last_id = $this->pdo->lastInsertId();
        return $last_id;
    }

    public function updateUser($user = null){

    }

    public function deleteUser($user = null){

    }
}