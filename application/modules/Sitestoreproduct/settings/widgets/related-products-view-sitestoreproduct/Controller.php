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
class Sitestoreproduct_Widget_RelatedProductsViewSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') && !Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      return $this->setNoRender();
    }

    //GET PRODUCT SUBJECT
    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $subject = Engine_Api::_()->core()->getSubject();
    } elseif (Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      $subject = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //GET VARIOUS WIDGET SETTINGS
    $this->view->title_truncation = $this->_getParam('truncation', 24);
    $related = $this->_getParam('related', 'categories');
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "commentCount", "viewRating"));
    
    if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      $key = array_search('reviewCount', $this->view->statistics);
      if (!empty($key)) {
        unset($this->view->statistics[$key]);
      }
    }

    $params = array();

    If ($related == 'tags') {

      //GET TAGS
      $productTags = $subject->tags()->getTagMaps();

      $params['tags'] = array();
      foreach ($productTags as $tag) {
        $params['tags'][] = $tag->getTag()->tag_id;
      }

      if (empty($params['tags'])) {
        return $this->setNoRender();
      }
    } elseif ($related == 'categories') {
      $params['category_id'] = $subject->category_id;
    } else {
      return $this->setNoRender();
    }

    //FETCH PRODUCTS
    $params['product_id'] = $subject->product_id;
    $params['orderby'] = 'RAND()';
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->widgetProductsData($params);

    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
     $this->view->columnWidth = $this->_getParam('columnWidth', '180');
     $this->view->columnHeight = $this->_getParam('columnHeight', '328');
     $this->view->viewType = $this->_getParam('viewType', 'gridview');
     $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
     $this->view->showinStock = $this->_getParam('in_stock', 1);
  }

}