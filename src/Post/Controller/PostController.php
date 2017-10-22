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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\File;
use Controller;
use PDOException;
use Exception;

use \User\Model\User;
use \User\Service\UserService;

use \Post\Model\Post;
use \Post\Service\PostService;
use \Post\Service\PostNotFoundException;
use \Post\Service\PostDuplicateTitleException;
use \Score\Model\Score;
use \Score\Service\ScoreService;
use \Score\Service\ScoreNotFoundException;


define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk

// Read a file and display its content chunk by chunk
function readfile_chunked($filename, $retbytes = TRUE) {
    $buffer = '';
    $cnt    = 0;
    $handle = fopen($filename, 'rb');

    if ($handle === false) {
        return false;
    }

    while (!feof($handle)) {
        $buffer = fread($handle, CHUNK_SIZE);
        echo $buffer;
        ob_flush();
        flush();

        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }

    $status = fclose($handle);

    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }

    return $status;
}

class PostController extends Controller
{
    protected $service;
    protected $target = '/';

    public function getService(){
        return new PostService($this->db, $this->user, $this->pass);
    }

    public function getUserService(){
        return new UserService($this->db, $this->user, $this->pass);
    }

    public function getScoreService(){
        return new ScoreService($this->db, $this->user, $this->pass);
    }

    public function indexAction()
    {
        $service = $this->getService();
        $userService = $this->getUserService();
        $posts = [];
        try{
            if( !$this->isUserAuthenticated ){
                $posts = $service->getPostsPublished();                 // public posts
            }
            /** @var Post $post */
            $post = null;
            foreach($posts as $post){
                if( !empty($post->getAuthorId()) ){
                    $user = $userService->getUserById($post->getAuthorId());
                    $post->setAuthor($user);
                }
                if( !empty($post->getPublishedById()) ){
                    $user = $userService->getUserById($post->getPublishedById());
                    $post->setPublishedBy($user);
                }
                if( !empty($this->authUser)){
                    $post->setMyPost($this->authUser->getId());
                }
            }
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
            $userService->close();
        }
        $context = array('posts' => $posts);
        return $this->render('post/index.html.twig', $context);
    }

    public function myIndexAction()
    {
        /** @var PostService $service */
        $service = $this->getService();
        $userService = $this->getUserService();
        $posts = [];
        try{
            if($this->authUser->isAuthor()){
                $posts = $service->getPostsByUser($this->authUser->getId());   // author posts
            }
            if($this->authUser->isReviewer()){
                $posts = $service->getPostsByReviewer($this->authUser->getId());     // reviewer posts
            }
            if($this->authUser->isAdmin()){
                $posts = $service->getPostsAll();                   // all posts
            }

            /** @var Post $post */
            $post = null;
            foreach($posts as $post){
                if( !empty($post->getAuthorId()) ){
                    $user = $userService->getUserById($post->getAuthorId());
                    $post->setAuthor($user);
                }
                if( !empty($post->getPublishedById()) ){
                    $user = $userService->getUserById($post->getPublishedById());
                    $post->setPublishedBy($user);
                }
                if( !empty($this->authUser)){
                    $post->setMyPost($this->authUser->getId());
                }
            }
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
            $userService->close();
        }
        $context = array('posts' => $posts);
        return $this->render('post/index.html.twig', $context);
    }

    public function viewAction($id){

        // POST
        if( $_SERVER['REQUEST_METHOD'] == 'POST') {
            $csrf = $_POST['csrftoken'];
            $originality = $_POST['originality'];
            $language = $_POST['language'];
            $quality = $_POST['quality'];

            if (!empty($csrf) && !empty($originality) && !empty($language) && !empty($quality)) {
                if ($csrf == $_SESSION['csrftoken']) {
                    $scoreService = $this->getScoreService();

                    /** @var Score $score */
                    $score = null;

                    // Update existing OR Create new rating
                    $service = $this->getService();
                    try {
                        $score = $scoreService->getScoreById($id, $this->authUser->getId());
                        if( empty($score)){
                            // create a new score
                            $score = new Score($id, $this->authUser->getId());
                            $score->setRatingOriginality($originality);
                            $score->setRatingLanguage($language);
                            $score->setRatingQuality($quality);
                            $scoreService->createScore($score);
                        }
                        else{
                            // update
                            $score->setRatingOriginality($originality);
                            $score->setRatingLanguage($language);
                            $score->setRatingQuality($quality);
                            $scoreService->editScore($score);
                        }
                    } catch (PDOException $e) {
                        $context['message'] = sprintf('DB Chyba: %s', $e->getMessage());
                        return $this->render('500.html.twig', $context);
                    } finally {
                        $scoreService->close();
                    }
                }
            }
        }

        // open connection
        $service = $this->getService();
        $userService = $this->getUserService();
        $scoreService = $this->getScoreService();

        try{
            // load a post
            $post = $service->getPostById($id);
            if( !empty($post->getAuthorId()) ){
                $user = $userService->getUserById($post->getAuthorId());
                $post->setAuthor($user);
            }
            if( !empty($post->getPublishedById()) ){
                $user = $userService->getUserById($post->getPublishedById());
                $post->setPublishedBy($user);
            }
            $scores = $scoreService->getScoresForPost($id);
            if( !empty($scores)){
                $post->setScores($scores);
                if( $this->isUserAuthenticated ) {
                    $post->setUserScore($this->authUser->getId());
                }
            }
            if( $this->isUserAuthenticated ){
                $post->setMyPost($this->authUser->getId());
            }
        }
        catch(PostNotFoundException $e){
            $context = array('message' => 'Příspěvek nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
            $userService->close();
            $scoreService->close();
        }
        $context = array('post' => $post);
        return $this->render('post/view.html.twig', $context);
    }

    public function createAction()
    {
        // LOGIN REQUIRED
        $this->loginRequired();

        $rating = Score::getRating();
        $context = array(
            'title' => 'Nový příspěvek',
            'rating' => $rating,
        );

        // GET
        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $post = new Post('', $this->authUser->getId(), '');
            $context['post'] = $post;
            return $this->render('post/edit.html.twig', $context);
        }

        // POST
        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            $title = $_POST['title'];
            $abstract = $_POST['abstract'];

            if( !empty($csrf) && !empty($title) && !empty($abstract) ) {
                if( $csrf == $_SESSION['csrftoken']){
                    $post = new Post($title, $this->authUser->getId(), $abstract);
                    try{
                        // validate a new post
                        $post->validate();
                    }
                    catch (Exception $e){
                        $context['post'] = $post;
                        $context['message'] = $e->getMessage();
                        return $this->render('post/edit.html.twig', $context);
                    }

                    // Create a new post
                    $service = $this->getService();
                    try{
                        $postId = $service->createPost($post);

                        // SAVE ATTACHMENT TO DB
                        if( !empty($_FILES['file'])) {
                            // handle an uploaded file
                            $tmpName = $_FILES['file']['tmp_name'];
                            $fp = fopen($tmpName, 'rb'); // read binary
                            $file = file_get_contents($_FILES['file']['tmp_name']);
                            $filename = addslashes($_FILES['file']['name']);
                            $service->attachFile($postId, $filename, $file);
                        }
                    }
                    catch(PostDuplicateTitleException $e){
                        $context['post'] = $post;
                        $context['message'] = 'Duplikátní název příspěvku.';
                        return $this->render('post/edit.html.twig', $context);
                    }
                    catch(PDOException $e)
                    {
                        if (strpos($e->getMessage(), 'UNIQ_SLUG') !== false) {
                            $context['post'] = $post;
                            $context['message'] = 'Duplikátní název příspěvku.';
                            return $this->render('post/edit.html.twig', $context);
                        }
                        $context['message'] = sprintf('DB Chyba: %s', $e->getMessage());
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/posts/mylist');
        }
        // never happen
        return new Response('Bad request', 400);
    }

    public function editAction($id)
    {
        // LOGIN REQUIRED
        $this->loginRequired();

        $service = $this->getService();
        $userService = $this->getUserService();
        try{
            $post = $service->getPostById($id);
            if( !empty($post->getAuthorId()) ){
                $user = $userService->getUserById($post->getAuthorId());
                $post->setAuthor($user);
            }
            if( !empty($post->getPublishedById()) ){
                $user = $userService->getUserById($post->getPublishedById());
                $post->setPublishedBy($user);
            }
            if( !empty($authUser)){
                $post->setMyPost($authUser->getId());
            }
        }
        catch(PostNotFoundException $e){
            $context = array('message' => 'Příspěvek nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
            $userService->close();
        }

        $rating = Score::getRating();

        // GET
        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array(
                'post' => $post,
                'title' => 'Editace příspěvku',
                'rating' => $rating,
            );
            return $this->render('post/edit.html.twig', $context);
        }

        // POST
        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            $title = $_POST['title'];
            $abstract = $_POST['abstract'];

            if( !empty($csrf) && !empty($post) ) {
                if( $csrf == $_SESSION['csrftoken']){

                    $post->setTitle($title);
                    $post->setAbstract($abstract);
                    try{
                        // validation
                        $post->validate();
                    }
                    catch (Exception $e){
                        $context = array(
                            'post' => $post,
                            'title' => 'Editace příspěvku',
                            'rating' => $rating,
                            'message' => $e->getMessage());
                        return $this->render('post/edit.html.twig', $context);
                    }

                    // SAVE ATTACHMENT TO DB
                    $service = $this->getService();
                    try{
                        $service->editPost($post);

                        if( !empty($_FILES['file'])) {
                            // handle an uploaded file
                            $tmpName  = $_FILES['file']['tmp_name'];
                            $fp = fopen($tmpName, 'rb'); // read binary

                            $file = file_get_contents($_FILES['file']['tmp_name']); //SQL Injection defence!
                            $filename = addslashes($_FILES['file']['name']);
                            $service->attachFile($post->getId(), $filename, $file);
                        }
                    }
                    catch(PDOException $e)
                    {
                        if (strpos($e->getMessage(), 'UNIQ_SLUG') !== false) {
                            $context = array(
                                'post' => $post,
                                'title' => 'Editace příspěvku',
                                'rating' => $rating,
                                'message' => 'Duplikátní název příspěvku.');
                            return $this->render('post/edit.html.twig', $context);
                        }
                        $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/posts/mylist');
        }

        // never happen
        return new Response('Bad request', 400);
    }

    public function deleteAction($id)
    {
        // LOGIN REQUIRED
        $this->loginRequired();

        $service = $this->getService();
        try{
            $post = $service->getPostById($id);
            if( !empty($authUser)){
                $post->setMyPost($authUser->getId());
            }
        }
        catch(PostNotFoundException $e){
            $context = array('message' => 'Příspěvek nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
        }

        if( $_SERVER['REQUEST_METHOD'] == 'GET'){
            $context = array('post' => $post);
            return $this->render('post/delete.html.twig', $context);
        }

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            $csrf = $_POST['csrftoken'];
            if( !empty($csrf) && !empty($post) ) {
                if( $csrf == $_SESSION['csrftoken']){
                    $service = $this->getService();
                    try{
                        $service->deletePost($post);
                    }
                    catch(PDOException $e)
                    {
                        $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
                        return $this->render('500.html.twig', $context);
                    }
                    finally{
                        $service->close();
                    }
                }
            }
            return $this->redirect('/posts/mylist');
        }

        // never happen
        return new Response('Bad request', 400);
    }

    public function publishAction($id)
    {
        // LOGIN REQUIRED
        $this->loginRequired();
        if( !$this->authUser->isAdmin()){
            $context = array('message' => 'Nemáte oprávnění publikovat.');
            return $this->render('404.html.twig', $context);
        }

        /** @var Post $post */
        $post = null;
        $service = $this->getService();
        try {
            $post = $service->getPostById($id);
            $post->setPublished = new DateTime();
            $post->setPublishedById = $this->authUser->getId();
            $service->editPost($post);

        } catch (PostNotFoundException $e) {
            $context = array('message' => 'Příspěvek nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        } catch (PDOException $e) {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        } finally {
            $service->close();
        }
    }

    public function downloadAction ($id){
        $service = $this->getService();
        try{
            $post = $service->getPostById($id);
        }
        catch(PostNotFoundException $e){
            $context = array('message' => 'Příspěvek nebyl nalezen.');
            return $this->render('404.html.twig', $context);
        }
        catch(PDOException $e)
        {
            $context = array('message' => sprintf('DB Chyba: %s', $e->getMessage()));
            return $this->render('500.html.twig', $context);
        }
        finally{
            $service->close();
        }

        $this->target = sprintf('%s/var/temp/%s', PROJ_PATH, $post->getFileName());
        $fp = fopen($this->target,'w+');
        fwrite($fp, $post->getFile());
        fclose($fp);

        $response = new StreamedResponse();
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setCallback(function () {
            $filePath = $this->target;
            $downloadable_file_stream = readfile_chunked($filePath);
            $downloadable_file_stream_contents = stream_get_contents($downloadable_file_stream);
            echo $downloadable_file_stream_contents;
            flush();
        });
        return $response;
    }
}