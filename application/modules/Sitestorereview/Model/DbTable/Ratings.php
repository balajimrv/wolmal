<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = 'Sitestorereview_Model_Rating';

	/**
   * Return review ratings data
   *
   * @param Int review_id
   * @return Zend_Db_Table_Select
   */
	public function ratingsData($review_id) {
    $select = $this->select()
									->from($this->info('name'), array('reviewcat_id', 'rating'))
									->where("review_id = ?", $review_id);
    return $this->fetchAll($select)->toArray();
	}

  /**
   * Returns a rating datas according to store_id
   *
   * @param Int store_id
   * @return Zend_Db_Table_Select
   */
  public function ratingbyCategory($store_id) {

		//RETURN IF STORE ID IS EMPTY
    if (empty($store_id)) {
      return;
    }

    $tableRatingName = $this->info('name');
    $tableCategory = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview');
    $tableCategoryName = $tableCategory->info('name');
    $select = $this
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from($tableRatingName, array('AVG(rating) AS avg_rating'))
                    ->joinLeft($tableCategoryName, "$tableRatingName.reviewcat_id = $tableCategoryName.reviewcat_id", array('reviewcat_name'))
                    ->where($tableRatingName . ".rating != ?", 0)
                    ->where("store_id = ?", $store_id)
                    ->group($tableRatingName.'.reviewcat_id');
    return $this->fetchAll($select)->toArray();

  }

  /**
   * Returns a rating datas according to review_id
   *
   * @param Int review_id
   * @return Zend_Db_Table_Select
   */
  public function profileRatingbyCategory($review_id) {

		//RETURN IF REVIEW ID IS EMPTY
    if (empty($review_id)) {
      return;
    }

		//GET RATING TABLE NAME
    $tableRatingName = $this->info('name');

		//GET REVIEW PARAMETER TABLE INFO
    $tableCategory = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview');
    $tableCategoryName = $tableCategory->info('name');

		//MAKE QUERY
    $select = $this
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from($tableRatingName, array('rating'))
                    ->joinLeft($tableCategoryName, "$tableRatingName.reviewcat_id = $tableCategoryName.reviewcat_id", array('reviewcat_name'))
                    ->where("review_id = ?", $review_id);

		//RETURN RESULTS
    return $this->fetchAll($select)->toArray();
  }

  /**
   * If store category is updated than update review and rating entries
   *
   * @param Int store_id, pre_cat_id, curr_cat_id
   */
  public function editStoreCategory($store_id, $pre_cat_id, $curr_cat_id) {

		//DELETE ENTRIES BELONGS TO THIS STORE ID
		$this->delete(array('reviewcat_id != ?' => 0, 'store_id = ?' => $store_id));

		//JUST UPDATE CATEGORY ID
		$this->update(array('category_id' => $curr_cat_id), array('category_id = ?' => $pre_cat_id, 'store_id = ?' => $store_id, 'reviewcat_id = ?' => 0));
  }

  /**
   * Update overall store rating
   *
   * @param Int store_id
   */
  public function storeRatingUpdate($store_id) {

		//RETURN IF STORE ID IS EMPTY
    if (empty($store_id)) {
      return;
    }

    //UPDATE STORE RATING AVERAGE IN STORE TABLE
    $avg_rating = $this
                    ->select()
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->where("reviewcat_id = ?", 0)
                    ->where("store_id = ?", $store_id)
                    ->group('store_id')
										->query()
                    ->fetchColumn();

    //if (!empty($avg_rating)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->rating = round($avg_rating, 4);
      $sitestore->save();
    //}
  }

  /**
   * Return rating corrosponding to store_id and review_id
   *
   * @param Int store_id, review_id
   */
	public function getRating($store_id, $review_id) {

		//FETCH DATA
    $rating = $this->select()
                    ->from($this->info('name'), 'rating')
                    ->where('store_id = ?', $store_id)
                    ->where('review_id = ?', $review_id)
                    ->where('reviewcat_id = ?', 0)
										->query()
                    ->fetchColumn();

		//RETURN DATA
    return $rating;
	}
}
?>