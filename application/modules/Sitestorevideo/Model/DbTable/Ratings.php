<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "Sitestorevideo_Model_Rating";

	/**
   * Return video rating
   *
   * @param int $video_id
   * @return video rating
   */
	public function rateVideo($video_id) {

		$select = $this->select()
						->from($this->info('name'), array('*', 'AVG(rating) AS avg_rating'))
            ->where('video_id = ?', $video_id)
						->group('video_id');
		$avgRateData = $this->fetchAll($select)->toArray();
		return $avgRateData[0]['avg_rating'];
	}

	/**
   * Check for already rated video by same user
   *
   * @param int $video_id
   * @param int $user_id
   */
  public function checkRated($video_id, $user_id) {
    
    $select = $this->select()
            ->where('video_id = ?', $video_id)
            ->where('user_id = ?', $user_id)
            ->limit(1);
    $row = $this->fetchAll($select);

    if (count($row) > 0)
      return true;
    return false;
  }

	/**
   * Do video rating
   *
   * @param int $video_id
   * @param int $user_id
   * @param int $rating
   * @param int $store_id
   */
  public function setRating($video_id, $user_id, $rating) {
    
    $ratingTableName = $this->info('name');
    $select = $this->select()
            ->from($ratingTableName)
            ->where($ratingTableName . '.video_id = ?', $video_id)
            ->where($ratingTableName . '.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      $this->insert(array(
          'video_id' => $video_id,
          'user_id' => $user_id,
          'rating' => $rating
      ));
    }
  }

	/**
   * Return total video ratings count
   *
   * @param int $video_id
   */
  public function ratingCount($video_id) {
    
    $ratingTableName = $this->info('name');
    $select = $this->select()
            ->from($ratingTableName)
            ->where($ratingTableName . '.video_id = ?', $video_id);
    $row = $this->fetchAll($select);
    $total = count($row);
    return $total;
  }

}
?>