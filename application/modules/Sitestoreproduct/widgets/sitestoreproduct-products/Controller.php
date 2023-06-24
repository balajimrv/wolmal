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
class Sitestoreproduct_Widget_SitestoreproductProductsController extends Engine_Content_Widget_Abstract {

  public function indexAction() 
  {
    $params = array();
    if (Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      $getStoreSubject = Engine_Api::_()->core()->getSubject('sitestore_store');
      $store_id = $getStoreSubject->store_id;
      $params['store_id'] = !empty($store_id)? $store_id: 0;
    }

    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
      $params['product_id'] = $sitestoreproduct->product_id;
      $this->view->category_id = $params['category_id'] = $sitestoreproduct->category_id;
      $params['subcategory_id'] = $sitestoreproduct->subcategory_id;
      $params['subsubcategory_id'] = $sitestoreproduct->subsubcategory_id;
    }
    else
    {
      $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
      $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
      $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
    }
    
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->ratingType = $params['ratingType'] = $this->_getParam('ratingType', 'rating_avg');
    $this->view->popularity = $params['popularity'] = $this->_getParam('popularity', 'last_order_all');
    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
    $this->view->truncation = $this->_getParam('truncation', 16);
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['product_type'] = $this->_getParam('product_type', 'all');
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    $this->view->sitestoreproduct_products = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getSitestoreproductProducts($params);
    
    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
    
    //DON'T RENDER IF NO DATA
    if (Count($this->view->sitestoreproduct_products) <= 0) {
      return $this->setNoRender();
    }
  }
}