<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 16/10/2017
 * Time: 15:23
 */

namespace Post\Service;

use PDO;
use Exception;
use BaseService;

use \Post\Model\Post;


class PostNotFoundException extends Exception{ }
class PostDuplicateTitleException extends Exception{ }

class PostService extends BaseService
{

    private function mapPost($row){
        $post = new Post($row->title, $row->author_id, $row->abstract);
        $post->setId($row->id);
        $post->setFile($row->file_name, $row->file);
        $post->setPublished($row->published);
        $post->setPublishedById($row->published_by_id);
        $post->setCreated($row->created);
        $post->setDeleted($row->deleted);
        return $post;
    }

    /*
     * returns all posts
     */
    public function getPostsAll(){
        $posts = array();
        $sql = "SELECT * FROM posts WHERE deleted=0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $post = $this->mapPost($row);
            array_push($posts, $post);
        }
        return $posts;
    }

    /*
     * returns published posts
     */
    public function getPostsPublished(){
        $posts = array();
        $sql = "SELECT * FROM posts WHERE deleted=0 and published IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $post = $this->mapPost($row);
            array_push($posts, $post);
        }
        return $posts;
    }

    /*
     * returns posts by userId
     */
    public function getPostsByUser($userId){
        $posts = array();
        $sql = "SELECT * FROM posts WHERE deleted=0 and author_id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = intval($userId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $post = $this->mapPost($row);
            array_push($posts, $post);
        }
        return $posts;
    }

    /*
     * returns posts by reviewerId
     */
    public function getPostsByReviewer($reviewerId){
        $posts = array();
        $sql = "SELECT posts.* FROM posts JOIN scores ON scores.post_id=posts.id WHERE posts.deleted=0 AND scores.reviewer_id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = intval($reviewerId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $post = $this->mapPost($row);
            array_push($posts, $post);
        }
        return $posts;
    }

    /*
     * returns a post by ID
     */
    public function getPostById($id = null){
        $sql = "SELECT * FROM posts WHERE deleted=0 AND id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = intval($id);
        $stmt->execute();
        $row = $stmt->fetchObject();
        if(empty($row)){
            throw new PostNotFoundException();
        }
        return $this->mapPost($row);
    }

    /*
     * returns a post by slug
     */
    public function getPostBySlug($slugTitle){
        $sql = "SELECT * FROM posts WHERE deleted=0 AND slug=:slug";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $slug = $slugTitle;
        $stmt->execute();
        $row = $stmt->fetchObject();
        if(empty($row)){
            throw new PostNotFoundException();
        }
        return $this->mapPost($row);
    }

    /*
     * creates a new post
     * returns ID
     */
    public function createPost(Post $post=null){
        $sql = "INSERT INTO posts (title, slug, author_id, abstract) VALUES (:title, :slug, :author_id, :abstract)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $stmt->bindParam(':abstract', $abstract, PDO::PARAM_STR);
        $title = $post->getTitle();
        $slug = $post->getSlug();
        $author_id = $post->getAuthorId();
        $abstract = $post->getAbstract();
        $stmt->execute();
        $last_id = $this->pdo->lastInsertId();
        return $last_id;
    }

    public function attachFile($pid, $fileName, $data) {
        $sql = "UPDATE posts SET file_name=:file_name, file=:file WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':file_name', $filename, PDO::PARAM_STR);
        $stmt->bindParam(':file', $blob, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $blob = $data;
        $filename = $fileName;
        $id = $pid;
        $stmt->execute();
        return true;
    }

    public function editPost(Post $post = null){
        $sql = "UPDATE posts SET title=:title, slug=:slug, author_id=:author_id, abstract=:abstract WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $stmt->bindParam(':abstract', $abstract, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $title = $post->getTitle();
        $slug = $post->getSlug();
        $author_id = $post->getAuthorId();
        $abstract = $post->getAbstract();
        $id = $post->getId();
        $stmt->execute();
        return $this->getPostById($post->getId());
    }

    /*
     * delete an existing post
     */
    public function deletePost(Post $post = null){
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $id = $post->getId();
        $stmt->execute();
    }


}