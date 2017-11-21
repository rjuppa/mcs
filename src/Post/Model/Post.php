<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 16/10/2017
 * Time: 15:22
 */
namespace Post\Model;

use DateTime;
use User\Model\User;
use Score\Model\Score;


class Post
{
    protected $id;
    protected $title;
    protected $slug;
    protected $authorId;

    /** @var User */
    protected $author;

    protected $abstract;
    protected $file;
    protected $fileName;

    protected $published;
    protected $publishedById;

    /** @var User */
    protected $publishedBy;
    protected $deleted;
    protected $created;

    protected $isMy = false;

    protected $scores = array();
    protected $hasUserScore = false;
    protected $userScore = array();
    protected $reviewerCount = 0;
    protected $countOfReviews = 0;


    public function __construct($title, $authorId, $abstract){
        $this->title = $title;
        $this->authorId = intval($authorId);
        $this->slug = sprintf('%s-%s', $authorId, $this->slugify($title));
        $this->abstract = $abstract;
        $this->file = null;
        $this->fileName = null;
        $this->published = null;
        $this->publishedById = null;
        $this->publishedBy = null;
        $this->deleted = 0;
        $this->created = new DateTime();
    }

    private function slugify($sname){
        // create slug
        $sname = strtolower($sname);
        $sname = $this->fixedCzech($sname);
        $sname = preg_replace('/\s+/', '-', $sname);
        $sname = preg_replace('/[^a-zA-Z0-9-]/', '', $sname);
        return $sname;
    }

    private function fixedCzech($sname){
        // remove czech chars
        $sname = str_replace('á', 'a', $sname);
        $sname = str_replace('í', 'i', $sname);
        $sname = str_replace('é', 'e', $sname);
        $sname = str_replace('ý', 'y', $sname);
        $sname = str_replace('ž', 'z', $sname);
        $sname = str_replace('ř', 'r', $sname);
        $sname = str_replace('č', 'c', $sname);
        $sname = str_replace('š', 's', $sname);
        $sname = str_replace('ě', 'e', $sname);
        $sname = str_replace('ť', 't', $sname);
        $sname = str_replace('ď', 'd', $sname);
        $sname = str_replace('ň', 'n', $sname);
        $sname = str_replace('ú', 'u', $sname);
        $sname = str_replace('ů', 'u', $sname);
        return $sname;
    }


    public function getId(){ return $this->id; }
    public function getTitle(){ return $this->title; }
    public function getSlug(){ return $this->slug; }
    public function getAuthorId(){ return $this->authorId; }
    public function getAuthor(){ return $this->author; }
    public function getAuthorName(){ if(empty($this->author)) {return '';} else {return $this->author->getDisplayName();} }
    public function isMyPost(){ return $this->isMy; }
    public function getAbstract(){ return $this->abstract; }
    public function getFile(){ return $this->file; }
    public function getFileName(){ return $this->fileName; }
    public function getPublished(){ return $this->published; }
    public function getPublishedById(){ return $this->publishedById; }
    public function getPublishedBy(){ return $this->publishedBy; }
    public function getPublishedByName(){ if(empty($this->publishedBy)) {return '';} else {return $this->publishedBy->getDisplayName();} }
    public function isDeleted(){ return $this->deleted; }
    public function getCreated(){ return $this->created; }
    public function getScores(){ return $this->scores; }
    public function hasRating(){ return count($this->scores) > 0; }
    public function getUserScore(){ return $this->hasUserScore; }
    public function getCountOfReviews(){ return $this->countOfReviews; }
    public function getReviewerCount(){ return $this->reviewerCount; }
    public function getRating(){
        // calculates mean values of ratings
        $originality = 0;
        $language = 0;
        $quality = 0;
        $total = 0;

        /** @var Score $score */
        $score = null;
        $count = count($this->scores);
        if( $count > 0){
            $origCount = 0;
            $langCount = 0;
            $qualCount = 0;
            foreach ($this->scores as $score){
                if( $score->getRatingOriginality() > 0 ){
                    $originality += $score->getRatingOriginality();
                    $origCount++;
                }
                if( $score->getRatingLanguage() > 0 ){
                    $language += $score->getRatingLanguage();
                    $langCount++;
                }
                if( $score->getRatingQuality() > 0 ){
                    $quality += $score->getRatingQuality();
                    $qualCount++;
                }
            }
            $count = $origCount > 1 ? $origCount : 1;
            $originality = round($originality / $count, 2);
            $count = $langCount > 1 ? $langCount : 1;
            $language = round($language / $count, 2);
            $count = $qualCount > 1 ? $qualCount : 1;
            $quality = round($quality / $count, 2);
            $total = round(($originality + $language + $quality) / 3, 2);
        }

        $rating = array(
            'originality' => sprintf('%s', $originality),
            'language' => sprintf('%s', $language),
            'quality' => sprintf('%s', $quality),
            'total' => sprintf('%s', $total));
        return $rating;
    }

    public function getTotal(){
        $rating = $this->getRating();
        return $rating['total'];
    }

    public function renderOriginality(){
        $rating = $this->getRating();
        $temp = '';
        for( $i=0; $i<5; $i++){
            if( $i<$rating['originality'] ){
                $temp .= '<span class="fa fa-star"></span>';
            }
            else{
                $temp .= '<span class="fa fa-star-empty"></span>';
            }
        }
        return $temp;
    }

    public function renderLanguage(){
        $rating = $this->getRating();
        $temp = '';
        for( $i=0; $i<5; $i++){
            if( $i<$rating['language'] ){
                $temp .= '<span class="fa fa-star"></span>';
            }
            else{
                $temp .= '<span class="fa fa-star-empty"></span>';
            }
        }
        return $temp;
    }

    public function renderQuality(){
        $rating = $this->getRating();
        $temp = '';
        for( $i=0; $i<5; $i++){
            if( $i<$rating['quality'] ){
                $temp .= '<span class="fa fa-star"></span>';
            }
            else{
                $temp .= '<span class="fa fa-star-empty"></span>';
            }
        }
        return $temp;
    }

    public function renderScore(){
        $rating = $this->getRating();
        $temp = '';
        for( $i=0; $i<5; $i++){
            if( $i<$rating['total'] ){
                $temp .= '<span class="fa fa-star"></span>';
            }
            else{
                $temp .= '<span class="fa fa-star-empty"></span>';
            }
        }
        return $temp;
    }

    public function renderOriginalityOptions(){
        $rating = Score::getRating();
        $temp = '<option value="0">-------</option>\n';
        foreach ($rating as $index=>$name){
            $selected = '';
            if($this->hasUserScore && !empty($this->userScore)) {
                /** @var Score $score */
                $score = $this->userScore;
                if ($score->getRatingOriginality() == $index) {
                    $selected = 'selected';
                }
            }
            $temp .= sprintf('<option value="%s" %s>%s</option>', $index, $selected, $name);
        }
        return $temp;
    }

    public function renderLanguageOptions(){
        $rating = Score::getRating();
        $temp = '<option value="0">-------</option>\n';
        foreach ($rating as $index=>$name){
            $selected = '';
            if($this->hasUserScore && !empty($this->userScore)) {
                /** @var Score $score */
                $score = $this->userScore;
                if ($score->getRatingLanguage() == $index) {
                    $selected = 'selected';
                }
            }
            $temp .= sprintf('<option value="%s" %s>%s</option>', $index, $selected, $name);
        }
        return $temp;
    }

    public function renderQualityOptions(){
        $rating = Score::getRating();
        $temp = '<option value="0">-------</option>\n';
        foreach ($rating as $index=>$name){
            $selected = '';
            if($this->hasUserScore && !empty($this->userScore)) {
                /** @var Score $score */
                $score = $this->userScore;
                if ($score->getRatingQuality() == $index) {
                    $selected = 'selected';
                }
            }
            $temp .= sprintf('<option value="%s" %s>%s</option>', $index, $selected, $name);
        }
        return $temp;
    }

    public function getViewUrl(){ return sprintf('%s/posts/%s', FRONT_URL, $this->id); }
    public function getEditUrl(){ return sprintf('%s/posts/%s/edit', FRONT_URL, $this->id); }
    public function getDeleteUrl(){ return sprintf('%s/posts/%s/delete', FRONT_URL, $this->id); }
    public function getDownloadUrl(){ return sprintf('%s/posts/%s/download', FRONT_URL, $this->id); }
    public function getPublishUrl(){ return sprintf('%s/posts/%s/publish', FRONT_URL, $this->id); }


    public function setId($id){ return $this->id = $id; }
    public function setTitle($title){
        $this->validateTitle($title);
        $this->slug = sprintf('%s-%s', $this->authorId, $this->slugify($title));
        return $this->title = $title;
    }
    public function setAuthor(User $author){ $this->author = $author; }
    public function setAuthorId($authorId){ $this->authorId = intval($authorId); }
    public function setMyPost($userId){ $this->isMy = $this->authorId == $userId; }

    public function setAbstract($abstract){ $this->validateAbstract($abstract); return $this->abstract = $abstract; }
    public function setFile($filename, $file){ $this->fileName = $filename; $this->file = $file; }
    public function setPublished($published){ $this->published = $published; }
    public function setPublishedById($publishedById){ $this->publishedById = intval($publishedById); }
    public function setPublishedBy(User $publishedBy){ $this->publishedBy = $publishedBy; }
    public function setDeleted($deleted){ $this->deleted = intval($deleted); }
    public function setCreated($created){ $this->created = $created; }
    public function setScores($scores){
        $this->scores = $scores;
        $this->reviewerCount = count($this->scores);
        $this->countOfReviews = 0;
        foreach ($scores as $score){
            if( $score->getScore() > 0 ){
                $this->countOfReviews++;
            }
        }
    }

    public function setUserScore($userId){
        /** @var Score $score */
        $score = null;
        $this->hasUserScore = false;
        $count = count($this->scores);
        if( $count > 0){
            foreach ($this->scores as $score){
                if( $score->getReviewerId() == $userId ){
                    $this->hasUserScore = true;
                    $this->userScore = $score;
                    break;
                }

            }
        }
    }

    public function validate(){
        // validation
        $this->validateAbstract($this->abstract);
        $this->validateTitle($this->title);
    }

    private function validateAbstract($abstract){
        if (!empty($abstract)) {
            return true;
        }
        throw new \Exception('Abstract je povinný.');
    }

    private function validateTitle($title){
        if (!empty($title)) {
            return true;
        }
        throw new \Exception('Název je povinný.');
    }

    private function validateFile($fileName, $file){
        if (!empty($fileName) && !empty($file)) {
            return true;
        }
        throw new \Exception('Soubor je povinný.');
    }

}