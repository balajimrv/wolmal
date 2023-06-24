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
class Sitestoreproduct_Widget_PinboardProductsSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->params = $this->_getAllParams();
    $this->view->params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
    if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
      $this->view->params['noOfTimes'] = 1000;

    if ($this->_getParam('autoload', true)) {
      $this->view->autoload = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->autoload = false;
        if ($this->_getParam('contentpage', 1) > 1)
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
        $this->getElement()->removeDecorator('Title');
        //return;
      }
    } else {
      $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
      if ($this->_getParam('contentpage', 1) > 1) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
    }

    $params = array();
    $params['popularity'] = $this->view->popularity = $this->_getParam('popularity', 'product_id');
    $params['limit'] = $this->_getParam('itemCount', 3);
    $fea_spo = $this->_getParam('fea_spo', '');
    if ($fea_spo == 'featured') {
      $params['featured'] = 1;
    } elseif ($fea_spo == 'newlabel') {
      $params['newlabel'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $params['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $params['sponsored'] = 1;
      $params['featured'] = 1;
    }
    
    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    $this->view->newIcon = $this->_getParam('newIcon', 1);
    $this->view->postedby = $this->_getParam('postedby', 1);
    $this->view->commentSection = $this->_getParam('commentSection', 0);
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "ratingStar", "productCreationTime"));
    $this->view->truncationDescription = $this->_getParam('truncationDescription', 0);
    $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');

		$this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
		$params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
		$params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['paginator'] = 1;
    //GET PRODUCTS
    $this->view->products = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->productsBySettings($params);
    $this->view->totalCount = $paginator->getTotalItemCount();

    $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
    $paginator->setItemCountPerPage($params['limit']);
    //DON'T RENDER IF RESULTS IS ZERO
//    if ($this->view->totalCount <= 0) {
//      return $this->setNoRender();
//    }
    
    $this->view->countPage = $paginator->count();
    if ($this->view->params['noOfTimes'] > $this->view->countPage)
      $this->view->params['noOfTimes'] = $this->view->countPage;
    
    $this->view->show_buttons = $this->_getParam('show_buttons', array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
    
  }

}