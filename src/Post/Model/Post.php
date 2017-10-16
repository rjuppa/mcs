<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 16/10/2017
 * Time: 15:22
 */
namespace Post\Model;

use DateTime;

class Post
{
    protected $id;
    protected $title;
    protected $author;
    protected $body;

    public function __construct($title, $author, $body){
        //TODO
    }

}