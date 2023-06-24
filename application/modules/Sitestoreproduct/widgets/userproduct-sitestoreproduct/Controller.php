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
class Sitestoreproduct_Widget_UserproductSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "commentCount"));

    //GET PRODUCT SUBJECT
    $this->view->product = $product = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      $key = array_search('reviewCount', $this->view->statistics);
      if (!empty($key)) {
        unset($this->view->statistics[$key]);
      }
    }

    $limit = $this->_getParam('count', 3);
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->truncation = $this->_getParam('truncation', 24);

    $this->view->products = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->userProduct($product->owner_id, $product->product_id, $limit);

    if (count($this->view->products) <= 0) {
      return $this->setNoRender();
    }
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    
    //SET WIDGET TITLE
    $element = $this->getElement();
    $translate = Zend_Registry::get('Zend_Translate');
    $element->setTitle(sprintf($translate->translate($element->getTitle()), $product->getOwner()->getTitle()));
  }

}