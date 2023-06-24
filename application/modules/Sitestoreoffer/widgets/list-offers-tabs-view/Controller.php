<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Widget_ListOffersTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
   
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);  
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    
    if (empty($is_ajax)) {
      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestoreoffer', 'type' => 'offers', 'enabled' => 1));
      $count_tabs = count($tabs);
      $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }
      $activeTabName = $tabs[0]['name'];
    }
    
    //GET CURRENT TIME
		if(!empty($viewer_id)) {
	   	$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer->timezone);
		}
    $currentTime = date("Y-m-d H:i:s");
    $this->view->marginPhoto = $this->_getParam('margin_photo', 12);
    $table = Engine_Api::_()->getItemTable('sitestoreoffer_offer');
    $tableName = $table->info('name');
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore'); 
    $tableStoreName = $tableStore->info('name');
    $select = $table->select()
										->setIntegrityCheck(false)
                    ->from($tableName)
                    ->joinLeft($tableStoreName, "$tableStoreName.store_id = $tableName.store_id", array('title AS store_title', 'photo_id as store_photo_id','offer'))
                    ->where("($tableName.end_settings = 1 AND $tableName.end_time >= '$currentTime' OR $tableName.end_settings = 0)")
                     ->where($tableName . '.status = ?', '1')
                    ->where($tableName . '.public = ?', '1')
                    ->where($tableName . '.approved = ?', '1');
 
    $select = $select
              ->where($tableStoreName . '.closed = ?', '0')
              ->where($tableStoreName . '.approved = ?', '1')
              ->where($tableStoreName . '.declined = ?', '0')
              ->where($tableStoreName . '.draft = ?', '1');
 
    if (!empty($category_id)) {
			$select = $select->where($tableStoreName . '.	category_id =?', $category_id);
		}
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    } 
            
    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestoreoffer', 'type' => 'offers', 'enabled' => 1, 'name' => $activeTabName));
    $this->view->activTab = $activTab = $activeTab['0'];
    switch ($activTab->name) {
      case 'recent_storeoffers':
        break;
      case 'liked_storeoffers':
        $select->order($tableName .'.like_count DESC');
        break;
      case 'viewed_storeoffers':
        $select->order($tableName .'.view_count DESC');
        break;
      case 'commented_storeoffers':
        $select->order($tableName .'.comment_count DESC');
        break;
      case 'featured_storeoffers':
        $select->where($tableName .'.sticky = ?', 1);
        break;
      case 'hot_storeoffers':
        $select->where($tableName .'.hotoffer = ?', 1);
        $select->order('Rand()');
        break;
      case 'popular_storeoffers':
        $select->where($tableName .'.claimed != ?', 0);
        break;
      case 'random_storeoffers':
        $select->order('Rand()');
        break;
    }
 
    if ($activTab->name != 'featured_storeoffers' && $activTab->name != 'random_storeoffers') {
      $select->order('creation_date DESC');
    }
    
		if (!empty($viewer_id)) {
			date_default_timezone_set($oldTz);
		}

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($activTab->limit);
    $paginator->setCurrentPageNumber($this->_getParam('store', 1));
    $this->view->count = $paginator->getTotalItemCount(); 
  }

}

?>
