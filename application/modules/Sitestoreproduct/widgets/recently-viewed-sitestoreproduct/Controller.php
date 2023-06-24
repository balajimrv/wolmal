<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitestoreproduct_Widget_RecentlyViewedSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) {
      return $this->setNoRender();
    }

    $params = array();

    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "viewRating"));
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $sitestoreproductRecentlyViewed = Zend_Registry::isRegistered('sitestoreproductRecentlyViewed') ?  Zend_Registry::get('sitestoreproductRecentlyViewed') : null;
    

      if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
        $key = array_search('reviewCount', $this->view->statistics);
        if (!empty($key)) {
          unset($this->view->statistics[$key]);
        }
      }
    

    $params['limit'] = $this->_getParam('count', 3);
    $fea_spo = $this->_getParam('fea_spo', '');
    if ($fea_spo == 'featured') {
      $params['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $params['sponsored'] = 1;
    } elseif ($fea_spo == 'newlabel') {
      $params['newlabel'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $params['sponsored'] = 1;
      $params['featured'] = 1;
    }
    $params['show'] = $this->_getParam('show', 1);
    $params['viewer_id'] = $viewer_id;
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->title_truncation = $this->_getParam('truncation', 16);

    //GET PRODUCTS
    $this->view->products = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->recentlyViewed($params);

    //DONT RENDER IF SITESTOREPRODUCT COUNT IS ZERO
    if ((count($this->view->products) <= 0) || empty($sitestoreproductRecentlyViewed)) {
      return $this->setNoRender();
    }
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
  }

}
