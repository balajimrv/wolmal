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
class Sitestoreproduct_Widget_ThumbListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $subject = Engine_Api::_()->core()->getSubject();
    }else{
      return $this->setNoRender();
    }
    $this->view->popularity = $params['popularity'] = $this->_getParam('popularity', 'product_id');
    $this->view->productTitle = $productTitle = $this->_getParam('productTitle', 1);
    $this->view->linkSee = $linkSee = $this->_getParam('linkSee', 1);
    $this->view->productNonImage = $params['productNonImage'] = $productNonImage = $this->_getParam('productNonImage', true);
    $params['limit'] = $this->_getParam('limit', 5);
    $params['product_id'] = $subject->product_id;
    $params['store_id'] = $subject->store_id;    
    $this->view->paginator = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->ThumbListIcons($params);
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $this->view->storeObj = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

    // GET TOTAL COUNT
    $store_id = $params['store_id'];
    $column_name = "store_id";
    $this->view->totalCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsCountInStore($store_id, 1);
    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}