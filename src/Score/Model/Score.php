<?php
/**
 * Created by PhpStorm.
 * User: radekj
 * Date: 16/10/2017
 * Time: 15:22
 */
namespace Score\Model;

use DateTime;
use User\Model\User;
use Post\Model\Post;

class Score
{
    public static function getRating(){
        return array('5' => 'VÝBORNÝ', '4' => 'VELMI DOBRÝ', '3' => 'DOBRÝ', '2' => 'DOSTATEČNÝ', '1' => 'SLABÝ');
    }


    protected $postId;
    protected $reviewerId;

    /** @var User */
    protected $reviewer;

    protected $ratingOriginality;
    protected $ratingLanguage;
    protected $ratingQuality;
    protected $score;
    protected $note;
    protected $created;


    public function __construct($postId, $reviewerId){
        $this->postId = $postId;
        $this->reviewerId = $reviewerId;
    }

    public function getPostId(){ return $this->postId; }
    public function getReviewerId(){ return $this->reviewerId; }
    public function getReviewer(){ return $this->reviewer; }
    public function getReviewerName(){
        if($this->reviewer){
            return $this->reviewer->getDisplayName();
        }
        return '';
    }
    public function getRatingOriginality(){ return $this->ratingOriginality; }
    public function getRatingLanguage(){ return $this->ratingLanguage; }
    public function getRatingQuality(){ return $this->ratingQuality; }
    public function getScore(){
        $counter = 0;
        $sum = 0;
        if( $this->ratingOriginality > 0){
            $sum += $this->ratingOriginality;
            $counter++;
        }
        if( $this->ratingLanguage > 0){
            $sum += $this->ratingLanguage;
            $counter++;
        }
        if( $this->ratingQuality > 0){
            $sum += $this->ratingQuality;
            $counter++;
        }
        if( $sum > 0 ){
            return round($sum / $counter, 2);
        }
        return -1;
    }
    public function getNote(){ return $this->note; }
    public function getCreated(){ return $this->created; }

    public function setPostId($id){ $this->postId = $id; }
    public function setReviewerId($id){ $this->reviewerId = $id; }
    public function setReviewer($reviewer){ $this->reviewer = $reviewer; }
    public function setRatingOriginality($rating){ $this->validateRating($rating); $this->ratingOriginality = intval($rating); }
    public function setRatingLanguage($rating){ $this->validateRating($rating); $this->ratingLanguage = intval($rating); }
    public function setRatingQuality($rating){ $this->validateRating($rating); $this->ratingQuality = intval($rating); }
    public function setScore($score){ $this->score = $score; }
    public function setNote($note){ $this->note = $note; }
    public function setCreated($created){ $this->created = $created; }

    private function validateRating($rating){
        if (!empty($rating)) {
            $rating = intval($rating);
            if( $rating > -2 && $rating < 6 ){
                return true;
            }
        }
        throw new \Exception('Rating je od 1 do 5.');
    }



}