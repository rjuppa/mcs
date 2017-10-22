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

    /*
     * map DB row into User
     * returns a user
     */
    private function mapUser($row){
        $user = User::withValidation($row->first_name, $row->last_name, $row->email, 'AUTHOR');
        $user->setId($row->id);
        $user->setType($row->type);
        $user->setIsActive($row->is_active);
        $user->setCreated($row->created);
        $user->setDeleted($row->deleted);
        return $user;
    }

    /*
     * authenticate user when login
     * returns an authenticated user
     */
    public function authenticate($email, $pass){
        $hash = null;
        $user = $this->getUserByEmail($email);
        if( $user ){
            $hash = $this->getHashByEmail($email);
        }
        else{
            throw new UserNotFoundException();
        }

        if( $pass == $hash ){  //md5($pass) == $hash
            // success
            return $user;
        }

        // Login failed
        return null;
    }

    /*
     * returns all users
     */
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

    /*
     * returns a user by ID
     */
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

    /*
     * returns a user by email
     */
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

    /*
     * returns a user hash by email
     */
    public function getHashByEmail($email){
        $email = strtolower($email);
        $sql = "SELECT password_hash FROM users WHERE deleted=0 AND is_active=1 AND email=:email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $hash = $stmt->fetchColumn(0);
        if(empty($hash)){
            throw new UserNotFoundException();
        }
        return $hash;
    }

    /*
     * creates a new user
     * returns ID
     */
    public function createUser(User $user=null){
        $sql = "INSERT INTO users (first_name, last_name, email, type, is_active) VALUES (:first_name, :last_name, :email, :type, :is_active)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_INT);
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();
        $type = $user->getType();
        $is_active = $user->getIsActive();
        $stmt->execute();
        $last_id = $this->pdo->lastInsertId();
        return $last_id;
    }

    public function editUser(User $user = null){
        $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, type=:type, is_active=:is_active WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_INT);
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();
        $type = $user->getType();
        $is_active = $user->getIsActive();
        $id = $user->getId();
        $stmt->execute();
        return $this->getUserById($user->getId());
    }

    /*
     * delete an existing user
     */
    public function deleteUser(User $user = null){
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = $user->getId();
        $stmt->execute();
    }
}