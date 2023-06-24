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
class Sitestoreproduct_Widget_MostDiscussedProductsController extends Engine_Content_Widget_Abstract
{  
  public function indexAction() {

    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $fea_spo = $this->_getParam('fea_spo', '');
    if ($fea_spo == 'featured') {
      $params['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $params['sponsored'] = 1;
    } elseif($fea_spo == 'newlabel') {
      $params['newlabel'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $params['sponsored'] = 1;
      $params['featured'] = 1;
    }

    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');

      $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
      $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
      $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

    $this->view->truncation = $this->_getParam('truncation', 16);
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');

    //GET PRODUCTS
    $this->view->products = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getDiscussedProduct($params);

    //DON'T RENDER IF RESULTS IS ZERO
    if (count($this->view->products) <= 0) {
      return $this->setNoRender();
    }
    $this->view->viewType = $this->_getParam('viewType', 'listview');
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
  }  
  
}
