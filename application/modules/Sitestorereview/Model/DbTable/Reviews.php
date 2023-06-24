<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reviews.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Model_DbTable_Reviews extends Engine_Db_Table {

  protected $_rowClass = "Sitestorereview_Model_Review";

  /**
   * Return review data for checking that viewer has been posted a review or not
   *
   * @param Int store_id
   * @param Int viewer_id
   * @return Zend_Db_Table_Select
   */
  public function canPostReview($store_id, $viewer_id) {

    //MAKE QUERY
    $hasPosted = $this->select()
                    ->from($this->info('name'), array('review_id'))
                    ->where('store_id = ?', $store_id)
                    ->where('owner_id = ?', $viewer_id)
                    ->query()
                    ->fetchColumn();

    //RETURN RESULTS
    return $hasPosted;
  }

  /**
   * Return average recommendetion for store reviews
   *
   * @param Int store_id
   * @return Zend_Db_Table_Select
   */
  public function getAvgRecommendation($store_id) {

    //MAKE QUERY
    $select = $this->select()
                    ->from($this->info('name'), array('*', 'AVG(recommend) AS avg_recommend'))
                    ->where('store_id = ?', $store_id)
                    ->group('store_id');

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Return total reviews for store
   *
   * @param Int store_id
   * @return Zend_Db_Table_Select
   */
  public function totalReviews($store_id) {

    //MAKE QUERY
    $totalReviews = $this->select()
                    ->from($this->info('name'), array('COUNT(*) AS count'))
                    ->where('store_id = ?', $store_id)
                    ->query()
                    ->fetchColumn();

    //RETURN RESULTS
    return $totalReviews;
  }

  /**
   * Return store reviews
   *
   * @param Int store_id
   * @return Zend_Db_Table_Select
   */
  public function storeReviews($store_id) {

    //MAKE QUERY
    $select = $this->select()
										->where('store_id = ?', $store_id)
										->order('modified_date DESC');
    //RETURN RESULTS
    return Zend_Paginator::factory($select);
  }

  /**
   * Get reviews to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($search_text, $limit=10) {

    //GET STORE TABLE NAME
    $tableStoreName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');

    //GET ITEM TABLE NAME
    $itemTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($itemTableName, array('review_id', 'title', 'owner_id', 'store_id'))
                    ->joinLeft($tableStoreName, "$tableStoreName.store_id = $itemTableName.store_id", array(''))
                    ->where($tableStoreName . '.closed = ?', '0')
                    ->where($tableStoreName . '.declined = ?', '0')
                    ->where($tableStoreName . '.approved = ?', '1')
                    ->where($tableStoreName . '.draft = ?', '1')
                    ->where($itemTableName . '.title  LIKE ? ', '%' . $search_text . '%')
                    ->order($itemTableName . '.title ASC')
                    ->limit($limit);

    //RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Return store reviews
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function reviewRatingData($params = array(),$widgetType = null) {

    //GET STORE TABLE NAME
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');

    //GET RATING TABLE NAME
    $tableRatingName = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->info('name');

    //GET REVIEW TABLE NAME
    $tableReviewName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($tableReviewName)
                    ->joinLeft($tableRatingName, "$tableRatingName.review_id = $tableReviewName.review_id", array('rating'));


    if (isset($params['store_id']) && !empty($params['store_id'])) {
      $select = $select->where($tableReviewName . '.store_id = ?', $params['store_id']);
    }

    if (isset($params['zero_count']) && !empty($params['zero_count'])) {
      $select = $select->where($tableReviewName . '.' . $params['zero_count'] . '!= ?', 0);
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($tableReviewName . '.' . $params['orderby']);
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      if (!isset($params['start_index']))
        $params['start_index'] = 0;
      $select->limit($params['limit'], $params['start_index']);
    }

    if (isset($params['store_validation']) && !empty($params['store_validation'])) {

      $select = $select->joinLeft($tableStoreName, "$tableStoreName.store_id = $tableReviewName.store_id", array('store_id', 'title AS store_title', 'photo_id'))
                      ->where($tableStoreName . '.search = ?', '1')
                      ->where($tableStoreName . '.closed = ?', '0')
                      ->where($tableStoreName . '.approved = ?', '1')
                      ->where($tableStoreName . '.declined = ?', '0')
                      ->where($tableStoreName . '.draft = ?', '1');
      if (!empty($params['category_id'])) {
				$select->where($tableStoreName . '.category_id = ?', $params['category_id']);
			}
			
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    
    if($widgetType == 'browsereview') {
      if (!empty($params['title'])) {
				$select->where($tableStoreName . ".title LIKE ? ", '%' . $params['title'] . '%');
			}

      if (!empty($params['search_review'])) {
				$select->where($tableReviewName . ".title LIKE ? ", '%' . $params['search_review'] . '%');
      }
     
      if ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'view_count') || !empty($params['viewedreview'])) {
			$select = $select
											->order($tableReviewName .'.view_count DESC')
											->order($tableReviewName .'.creation_date DESC');
			} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'comment_count') || !empty($params['commentedreview'])) {
				$select = $select
												->order($tableReviewName .'.comment_count DESC')
												->order($tableReviewName .'.creation_date DESC');
			} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'rating')) {
				$select = $select
												->order($tableRatingName .'.rating DESC')
												->order($tableReviewName .'.creation_date DESC');
			} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'like_count') || !empty($params['likedreview'])) {
				$select = $select
												->order($tableReviewName .'.like_count DESC')
												->order($tableReviewName .'.creation_date DESC');
			}

      if (!empty($params['category_id'])) {
				$select->where($tableStoreName . '.category_id = ?', $params['category_id']);
			}

			if (!empty($params['subcategory'])) {
				$select->where($tableStoreName . '.subcategory_id = ?', $params['subcategory']);
			}

			if (!empty($params['subcategory_id'])) {
				$select->where($tableStoreName . '.subcategory_id = ?', $params['subcategory_id']);
			}

			if (!empty($params['subsubcategory'])) {
				$select->where($tableStoreName . '.subsubcategory_id = ?', $params['subsubcategory']);
			}

			if (!empty($params['subsubcategory_id'])) {
				$select->where($tableStoreName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
			}
     
      if((isset($params['show']) && $params['show'] == 'featured')) {
				$select = $select
												->where($tableReviewName . '.featured = ?', 1)
												->order($tableReviewName .'.creation_date DESC');
			}
      elseif((isset($params['show']) && $params['show'] == 'my_friend_review')) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $membership_table = Engine_Api::_()->getDbtable( 'membership' , 'user' ) ;
				$member_name = $membership_table->info( 'name' ) ;
        $select->joinInner( $member_name , "$member_name . resource_id = $tableReviewName . owner_id" , NULL )
								->where( $member_name . '.user_id = ?' , $viewer_id )
								->where( $member_name . '.active = ?' , 1 );
      }
      elseif (isset($params['show']) && $params['show'] == 'Networks') {
				$select = $tableStore->getNetworkBaseSql($select, array('browse_network' => 1));
			}
			
			elseif (isset($params['show']) && $params['show'] == 'my_like') {
				$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
				$select
              ->join($likeTableName, "$likeTableName.resource_id = $tableStoreName.store_id")
							->where($likeTableName . '.poster_type = ?', 'user')
							->where($likeTableName . '.poster_id = ?', $viewer_id)
              ->where($likeTableName . '.resource_type = ?', 'sitestore_store');
			}
      
      if(empty($params['orderby_browse'])) {
				$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.order', 1);
				switch ($order) {
					case "1":
						$select->order($tableReviewName . '.creation_date DESC');
						break;
					case "2":
						$select->order($tableReviewName . '.title');
						break;
					case "3":
						$select->order($tableReviewName . '.featured' . ' DESC');
						break;
				}
      }
    }
    
    $select = $select->order($tableReviewName . '.review_id DESC')->where($tableRatingName . '.reviewcat_id = ?', 0);

    //Start Network work
    if (!isset($params['store_id']) || empty($params['store_id'])) {
     $select = $tableStore->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $tableReviewName . ".review_id"));
    }
    //End Network work
    //RETURN RESULTS
    if ((isset($params['featured']) && !empty($params['featured'])) || ($widgetType == 'browsereview')) {
      if($widgetType != 'browsereview') {
				$select = $select->where($tableReviewName . '.featured = ?', 1);
      }
      if($widgetType == 'featuredcarousel') {
        return $this->fetchAll($select);
      }
      else {
      return Zend_Paginator::factory($select);
      }
    } else {
      return $this->fetchAll($select);
    }
  }

  /**
   * Return review of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function reviewOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $reviewOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->info('name');

		//GET STORE TABLE NAME
		$storeTableName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');

    //GET REVIEW TABLE NAME
    $reviewTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($reviewTableName, array('review_id', 'title', 'store_id', 'owner_id', 'body'))
                    ->join($reviewOfTheDayTableName, $reviewTableName . '.review_id = ' . $reviewOfTheDayTableName . '.resource_id')
										->join($storeTableName, $reviewTableName . '.store_id = ' . $storeTableName . '.store_id', array(''))
										->where($storeTableName.'.approved = ?', '1')
										->where($storeTableName.'.declined = ?', '0')
										->where($storeTableName.'.draft = ?', '1')
                    ->where('resource_type = ?', 'sitestorereview_review')
                    ->where('start_date <= ?', $date)
                    ->where('end_date >= ?', $date)
                    ->order('Rand()');

		//STORE SHOULD BE AUTHORIZED
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($storeTableName.'.expiration_date  > ?', date("Y-m-d H:i:s"));

		//STORE SHOULD BE AUTHORIZED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
    if ($stusShow == 0) {
      $select->where($storeTableName.'.closed = ?', '0');
    }

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  /**
   * Return top reviewers
   *
   * @param int itemCount
   * @return Zend_Db_Table_Select
   */
  public function topReviewers($itemCount,$category_id) {

    //GET USER TABLE INFO
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $tableUserName = $tableUser->info('name');
    
    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
    
    //GET REVIEW TABLE NAME
    $tableReviewName = $this->info('name');

    //MAKE QUERY
    $select = $tableUser->select()
                    ->setIntegrityCheck(false)
                    ->from($tableUserName, array('user_id', 'username', 'displayname', 'photo_id'))
                    ->join($tableReviewName, "$tableUserName.user_id = $tableReviewName.owner_id", array('COUNT(engine4_sitestorereview_reviews.review_id) AS review_count', 'MAX(engine4_sitestorereview_reviews.review_id) as max_review_id'));
    if(!empty($category_id)) {
			$select ->join($storeTableName, "$tableReviewName.store_id = $storeTableName.store_id", array())
							->where($storeTableName . '.category_id = ?', $category_id);
    }
    $select->group($tableUserName . ".user_id")
						->order('review_count DESC')
						->order('user_id DESC')
						->limit($itemCount);

    //RETURN THE RESULTS
    return $tableUser->fetchAll($select);
  }
 
  public function topcreatorData($limit = null,$category_id) {

    //REVIEW TABLE NAME
    $reviewTableName = $this->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title','store_id'))
                    ->join($reviewTableName, "$storeTableName.store_id = $reviewTableName.store_id", array("COUNT($storeTableName.store_id) AS item_count"))
                    ->where($storeTableName.'.approved = ?', '1')
										->where($storeTableName.'.declined = ?', '0')
										->where($storeTableName.'.draft = ?', '1')
                    ->group($reviewTableName . ".store_id")
                    ->order('item_count DESC')
                    ->limit($limit);

    if (!empty($category_id)) {
      $select->where($storeTableName . '.category_id = ?', $category_id);
    }
    
    return $select->query()->fetchAll();
  }

}
?>