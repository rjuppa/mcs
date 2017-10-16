<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 16/10/2017
 * Time: 15:23
 */

use \Post\Model\Post;
use \BaseService;
use PDO;
use Exception;

class PostNotFoundException extends Exception{ }
class PostDuplicateEmailException extends Exception{ }

class PostService extends BaseService
{

    private function mapPost($row){
        $post = new Post($row->title, $row->author, $row->body, 'AUTHOR');
        return $post;
    }

    //TODO

}