<?php
    /**
     * Created by PhpStorm.
     * User: radekj
     * Date: 02/10/2017
     * Time: 12:03
     */
namespace Score\Service;

use \Score\Model\Score;
use \User\Model\User;
use \BaseService;
use PDO;
use Exception;

class ScoreNotFoundException extends Exception{ }
class ScoreDuplicateException extends Exception{ }

class ScoreService extends BaseService
{

    /*
     * map DB row into Score
     * returns a score
     */
    private function mapScore($row){
        $score = new Score($row->post_id, $row->reviewer_id);
        $score->setRatingOriginality($row->rating_originality);
        $score->setRatingLanguage($row->rating_language);
        $score->setRatingQuality($row->rating_quality);
        $score->setScore($row->score);
        $score->setNote($row->note);
        $score->setCreated($row->created);
        return $score;
    }

    /*
     * returns a single score
     */
    public function getScoreById($postId, $userId){
        $sql = "SELECT * FROM scores WHERE post_id=:post_id AND reviewer_id=:user_id;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $post_id = intval($postId);
        $user_id = intval($userId);
        $stmt->execute();
        $row = $stmt->fetchObject();
        if(empty($row)){
            return null;
        }
        return $this->mapScore($row);
    }

    /*
     * returns all scores for a post
     */
    public function getScoresForPost($postId){
        $scores = array();
        $sql = "SELECT * FROM scores WHERE post_id=:post_id;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $post_id = intval($postId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS);
        foreach($result as $row){
            $score = $this->mapScore($row);
            array_push($scores, $score);
        }
        return $scores;
    }

    public function editScore(Score $score){
        $sql = "UPDATE scores SET rating_originality=:orig, rating_language=:lang, rating_quality=:quality WHERE post_id=:post_id AND reviewer_id=:reviewer_id;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':orig', $orig, PDO::PARAM_INT);
        $stmt->bindParam(':lang', $lang, PDO::PARAM_INT);
        $stmt->bindParam(':quality', $quality, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewerId, PDO::PARAM_INT);
        $orig = $score->getRatingOriginality();
        $lang = $score->getRatingLanguage();
        $quality = $score->getRatingQuality();
        $postId = $score->getPostId();
        $reviewerId = $score->getReviewerId();
        $stmt->execute();
        return $this->getScoreById($postId, $reviewerId);
    }

    public function createScore(Score $score){
        $sql = "INSERT INTO scores (post_id, reviewer_id, rating_originalty, rating_language, rating_quality) VALUES (:post_id, :reviewer_id, :orig, :lang, :quality)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':orig', $orig, PDO::PARAM_INT);
        $stmt->bindParam(':lang', $lang, PDO::PARAM_INT);
        $stmt->bindParam(':quality', $quality, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewerId, PDO::PARAM_INT);
        $orig = $score->getRatingOriginality();
        $lang = $score->getRatingLanguage();
        $quality = $score->getRatingQuality();
        $postId = $score->getPostId();
        $reviewerId = $score->getReviewerId();
        $stmt->execute();
        $last_id = $this->pdo->lastInsertId();
        return $last_id;
    }

}