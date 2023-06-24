<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Rating";

  /**
   * Update rating in the product table
   *
   * @param Int $resource_id
   * @param Varchar $resource_type
   * @return Updated rating
   */
  public function listRatingUpdate($resource_id, $resource_type) {

    //RETURN IF RESOURCE ID IS EMPTY
    if (empty($resource_id) || empty($resource_type)) {
      return;
    }
    $tableRatingName = $this->info('name');
    $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->info('name');
    $rating_avg = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
            ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
            ->where($tableRatingName . ".ratingparam_id = ?", 0)
            ->where($tableRatingName . ".resource_id = ?", $resource_id)
            ->where($tableRatingName . ".resource_type = ?", $resource_type)
            ->where($tableRatingName . ".rating != ?", 0)
            ->where($tableReviewName . ".status = ?", 1)
            ->group($tableRatingName . '.resource_id')
            ->query()
            ->fetchColumn();

    $rating_editor = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
            ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
            ->where($tableRatingName . ".ratingparam_id = ?", 0)
            ->where($tableRatingName . ".resource_id = ?", $resource_id)
            ->where($tableRatingName . ".resource_type = ?", $resource_type)
            ->where($tableRatingName . ".type = ?", 'editor')
            ->where($tableRatingName . ".rating != ?", 0)
            ->where($tableReviewName . ".status = ?", 1)
            ->group($tableRatingName . '.resource_id')
            ->query()
            ->fetchColumn();

    $rating_users = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
            ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
            ->where($tableRatingName . ".ratingparam_id = ?", 0)
            ->where($tableRatingName . ".resource_id = ?", $resource_id)
            ->where($tableRatingName . ".resource_type = ?", $resource_type)
            ->where($tableRatingName . ".type in (?) ", array('user', 'visitor'))
            ->where($tableRatingName . ".rating != ?", 0)
            ->where($tableReviewName . ".status = ?", 1)
            ->group($tableRatingName . '.resource_id')
            ->query()
            ->fetchColumn();


    $list = Engine_Api::_()->getItem($resource_type, $resource_id);
    $list->rating_avg = round($rating_avg, 4);
    $list->rating_editor = round($rating_editor, 4);
    $list->rating_users = round($rating_users, 4);
    $list->save();
  }

  /**
   * Get rating by category
   *
   * @param Int $resource_id
   * @param Varchar $type
   * @param Varchar $resource_type
   * @return Get rating by category
   */
  public function ratingbyCategory($resource_id, $type = null, $resource_type) {

    //RETURN IF PAGE ID IS EMPTY
    if (empty($resource_id) || empty($resource_type)) {
      return;
    }

    $tableRatingName = $this->info('name');
    $tableRatingParamsName = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->info('name');
    $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->info('name');
    $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($tableRatingName, array('AVG(rating) AS avg_rating', 'ratingparam_id'))
            ->joinLeft($tableRatingParamsName, "$tableRatingName.ratingparam_id = $tableRatingParamsName.ratingparam_id", array('ratingparam_name'))
            ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
            ->where($tableRatingName . ".rating != ?", 0)
            ->where($tableRatingName . ".resource_id = ?", $resource_id)
            ->where($tableRatingName . ".resource_type = ?", $resource_type)
            ->where($tableReviewName . ".status = ?", 1)
            ->group($tableRatingName . '.ratingparam_id');

    if ($type == 'editor') {
      $select->where("$tableReviewName.type =?", $type);
    } elseif ($type == 'user' || $type == 'visitor') {
      $select->where("$tableReviewName.type in (?)", array($type, 'visitor'));
    }
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Get ratings
   *
   * @param Int $review_id
   * @param Int $viewer_id
   * @param Int $resource_id
   * @param Int $ratingparam_id
   * @return Get ratings
   */
  public function ratingsData($review_id, $viewer_id=null, $resource_id=null, $ratingparam_id=-1) {

    $select = $this->select()
            ->from($this->info('name'), array('ratingparam_id', 'rating', 'user_id'))
            ->where("review_id = ?", $review_id);

    if (!empty($resource_id)) {
      $select->where("resource_id =?", $resource_id);
    }

    if (!empty($viewer_id)) {
      $select->where("user_id =?", $viewer_id);
    }
    if ($ratingparam_id != -1) {
      $select->where("ratingparam_id =?", $ratingparam_id);
    }
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Get profile rating
   *
   * @param Int $review_id
   * @param Int $viewer_id
   * @return Get profile rating
   */
  public function profileRatingbyCategory($review_id, $viewer_id=null) {

    //RETURN IF REVIEW ID IS EMPTY
    if (empty($review_id)) {
      return;
    }

    //GET RATING TABLE NAME
    $tableRatingName = $this->info('name');

    //GET REVIEW PARAMETER TABLE INFO
    $tableRatingParamsName = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->info('name');

    //MAKE QUERY
    $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($tableRatingName, array('rating'))
            ->joinLeft($tableRatingParamsName, "$tableRatingName.ratingparam_id = $tableRatingParamsName.ratingparam_id", array('ratingparam_name'))
            ->where("review_id = ?", $review_id);

    if (!empty($viewer_id)) {
      $select->where("user_id =?", $viewer_id);
    }
    //RETURN RESULTS
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Create Rating Data
   *
   * @param Array $postData
   * @param Varchar $type
   * @return Created Rating Data
   */
  public function createRatingData($postData, $type) {

    $str = "";
    //DO ENTRY IN REVIEW RATING TABLE
    foreach ($postData as $key => $ratingdata) {
      if (empty($ratingdata))
        continue;
      if (strstr($key, 'update_review_rate_')) {
        $string_exist = strstr($key, 'update_review_rate_');
        $str = 'update_review_rate_';
      } else {
        $string_exist = strstr($key, 'review_rate_');
        $str = 'review_rate_';
      }
      if ($string_exist) {
        if ($str)
          $ratingparam_id = explode($str, $key);
        $reviewRating = $this->createRow();
        $reviewRating->review_id = $postData['review_id'];
        $reviewRating->user_id = $postData['user_id'];
        $reviewRating->category_id = $postData['category_id'];
        $reviewRating->resource_id = $postData['resource_id'];
        $reviewRating->resource_type = $postData['resource_type'];
        $reviewRating->ratingparam_id = $ratingparam_id[1];
        $reviewRating->rating = $ratingdata;
        $reviewRating->type = $type;
        $reviewRating->save();
      }
    }
  }

  /**
   * Number of user rating
   *
   * @param Int $resource_id
   * @param Varchar $type
   * @param Int $ratingparam_id
   * @param Int $value
   * @param Int $user_id
   * @param Varchar $resource_type
   * @param Array $params
   * @return Number of user rating
   */
  public function getNumbersOfUserRating($resource_id, $type = null, $ratingparam_id=0, $value=0, $user_id = 0, $resource_type=null, $params=array()) {

    $tableReviewName = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->info('name');
    $tableRatingName = $this->info('name');
    $select = $this
            ->select()->setIntegrityCheck(false)
            ->from($tableRatingName, new Zend_Db_Expr('COUNT(rating_id)'))
            ->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
            ->where("$tableRatingName.ratingparam_id = ?", $ratingparam_id)
            ->where($tableReviewName . ".status = ?", 1);

    if ($resource_id) {
      $select->where("$tableRatingName.resource_id = ?", $resource_id);
    }

    if ($resource_type) {
      $select->where("$tableRatingName.resource_type = ?", $resource_type);
    }

    if ($value) {
      $select->where("$tableRatingName.rating = ?", $value);
    }

    $select->where("$tableRatingName.rating <> ?", 0);

    if ($type == 'editor') {
      $select->where("$tableReviewName.type =?", $type);
    } elseif ($type == 'user' || $type == 'visitor') {
      $select->where("$tableReviewName.type in (?)", array($type, 'visitor'));
    }

    if ($user_id) {
      $select->where("user_id = ?", $user_id);
    }

    //GET PRODUCT TABLE NAME
    $productTable = Engine_Api::_()->getItemtable($resource_type);
    $sitestoreproductTableName = $productTable->info('name');
    $primary = current($productTable->info("primary"));
    $select->join($sitestoreproductTableName, "$tableReviewName.resource_id = $sitestoreproductTableName.$primary", array());
    
    $select->limit(1);

    $rating_users = $select
            ->query()
            ->fetchColumn();
    return $rating_users ? $rating_users : 0;
  }

  /**
   * If product category is updated than update review and rating entries
   *
   * @param Int resource_id, pre_cat_id, curr_cat_id, resource_type
   */
  public function editProductCategory($resource_id, $pre_cat_id, $curr_cat_id, $resource_type) {

    //DELETE ENTRIES BELONGS TO THIS LISt ID 
    $this->delete(array('ratingparam_id != ?' => 0, 'resource_id = ?' => $resource_id, 'resource_type = ?' => $resource_type));

    //JUST UPDATE CATEGORY ID
    $this->update(array('category_id' => $curr_cat_id), array('category_id = ?' => $pre_cat_id, 'resource_id = ?' => $resource_id, 'resource_type = ?' => $resource_type, 'ratingparam_id = ?' => 0));
  }
  
  public function getOverallRating($resource_type = 'sitestoreproduct_product', $resource_id = 0) {
    
    $overallRating = $this->select()
                          ->from($this->info('name'), 'rating')
                          ->where('ratingparam_id = ?', 0)
                          ->where('resource_type = ?', $resource_type)
                          ->where('review_id = ?', $resource_id)
                          ->query()
                          ->fetchColumn();
    
    return $overallRating;
  }

}