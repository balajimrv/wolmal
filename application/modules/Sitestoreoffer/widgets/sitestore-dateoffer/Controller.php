<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Widget_SitestoreDateofferController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $isajax = $this->_getParam('isajax', '');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    $this->view->tab_show = $tab_show = $tab_show_values = $this->_getParam('tab_show', 1);
    
    if(empty($isajax))
      $this->view->statistics = $this->_getParam('statistics', array("enddate", "couponcode", 'discount' ,'expire'));
    else
      $this->view->statistics = json_decode($this->_getparam('statistics'), true);
    
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    
    if(!empty($viewer_id)) {
    $oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer->timezone);
		 }
    $current_time = date("Y-m-d H:i:s");
    if ( $tab_show == 1 ) {
      $time_duration = date('Y-m-d H:i:s', strtotime('7 days'));
      $sqlTimeStr = ".end_time BETWEEN " . "'" . $current_time . "'" . " AND " . "'" . $time_duration . "'";
    }
    elseif ( $tab_show == 2 ) {
      $time_duration = date('Y-m-d H:i:s', strtotime('1 months'));
      $sqlTimeStr = ".end_time BETWEEN " . "'" . $current_time . "'" . " AND " . "'" . $time_duration . "'" . "";
    }
    elseif ( $tab_show == 3 ) {
      $sqlTimeStr = '';
    }

    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    //NUMBER OF OFFERS IN LISTING
    $this->view->totaloffers = $totalOffers = $this->_getParam('itemCount', 5);
    $user_id = array();
    $this->view->paginator = $paginator = Engine_Api::_()->sitestoreoffer()->tabofferDuration($sqlTimeStr, $totalOffers, $category_id);
    if ( ($paginator->getTotalItemCount() <= 0 ) ) {
      return $this->setNoRender();
    }
    $paginator->setItemCountPerPage($totalOffers);
    $paginator->setCurrentPageNumber($this->_getParam('store', 1));
    $this->view->count = $paginator->getTotalItemCount();

    if (!empty($viewer_id)) {
			date_default_timezone_set($oldTz);
		}

    $this->view->active_tab = $tab_show;
    if ( !empty($isajax) ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->ajaxrequest = $isajax;
  }

}

?>