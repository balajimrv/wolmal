<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_CategoryStoresSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {  	

		//GET PARAMETERS FOR SORTING THE RESULTS
    $current_time = date("Y-m-d H:i:s");
		$itemCount = $this->_getParam('itemCount', 0);
		$popularity = $this->_getParam('popularity', 'view_count');
		$interval = $this->_getParam('interval', 'overall');
		$totalStores = $this->_getParam('storeCount', 5);
    $this->view->columnCount = $this->_getParam('columnCount', 2);
		//MAKE TIMING STRING
		if($interval == 'week') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" ;
		}
		elseif($interval == 'month') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
		}
		else {
			$sqlTimeStr = '';
		}
    
		//GET CATEGORIES
    $categories = array();
		if(!empty($itemCount)) {
			$category_info = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories(0, 'category_id', 1, 'store_id', 1, $itemCount);
		}
		else {
			$category_info = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories(0, 'category_id', 1, 'store_id', 1, 0);
		}

    foreach ($category_info as $value) {
      $category_stores_array = array();

			//GET STORE RESULTS
			$category_stores_info = $category_stores_info = Engine_Api::_()->getDbtable('stores', 'sitestore')->storesByCategory($value['category_id'], $popularity, $interval, $sqlTimeStr, $totalStores);

      foreach ($category_stores_info as $result_info) {
        $tmp_array = array('store_id' => $result_info->store_id,
						'imageSrc' => $result_info->getPhotoUrl('thumb.icon'),
            'store_title' => $result_info->title,
            'owner_id' => $result_info->owner_id,
						'populirityCount' => $result_info->populirityCount,
            'slug' => $result_info->getSlug());
        $category_stores_array[] = $tmp_array;
      }
      $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $value->count,
          'category_stores' => $category_stores_array
      );
      $categories[] = $category_array;
    }
    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }
}
?>