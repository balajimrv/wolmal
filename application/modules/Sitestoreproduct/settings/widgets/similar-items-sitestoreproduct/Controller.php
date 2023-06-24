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
class Sitestoreproduct_Widget_SimilarItemsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() { 

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') && !Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      return $this->setNoRender();
    }

    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();
    } elseif (Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->viewType = $this->_getParam('viewType', 0);
    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "commentCount", "viewRating"));
    if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      $key = array_search('reviewCount', $this->view->statistics);
      if (!empty($key)) {
        unset($this->view->statistics[$key]);
      }
    }

    $values = array();
    $values['product_id'] = $sitestoreproduct->product_id;
    $values['limit'] = $this->_getParam('itemCount', 3);
    $values['similar_items_order'] = 1;
    $this->view->title_truncation = $this->_getParam('truncation', 24);
    $values['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');

    $similar_items = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, 'similar_items');
    $similarItems = array();
    if (!empty($similar_items)) {
      $similarItems = Zend_Json_Decoder::decode($similar_items);
    }

    if (!empty($similar_items) && !empty($similarItems) && Count($similarItems) >= 0) {
      $values['similarItems'] = $similarItems;
      $this->view->products = $productTable->getProduct('', $values);
    } else {
      
      if ($sitestoreproduct->subsubcategory_id) {
        $values['subsubcategory_id'] = $sitestoreproduct->subsubcategory_id;
      } elseif ($sitestoreproduct->subcategory_id) {
        $values['subcategory_id'] = $sitestoreproduct->subcategory_id;
      } elseif ($sitestoreproduct->category_id) {
        $values['category_id'] = $sitestoreproduct->category_id;
      } else {
        return $this->setNoRender();
      }

      $this->view->products = $productTable->getProduct('', $values);
    }
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    //DON'T RENDER IF RESULTS IS ZERO
    if (count($this->view->products) <= 0) {
      return $this->setNoRender();
    }
  }

}
