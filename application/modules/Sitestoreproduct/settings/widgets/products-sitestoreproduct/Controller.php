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
class Sitestoreproduct_Widget_ProductsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    //SITEMOBILE CODE
    $this->view->isajax = $this->_getParam('isajax', false);
    if ($this->view->isajax) {
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->viewmore = $this->_getParam('viewmore', false);
    $this->view->is_ajax_load = true;
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
       $this->getElement()->removeDecorator('Title');
    } 
    }
    
    $params = array();
    $params['popularity'] = $this->view->popularity = $this->_getParam('popularity', 'product_id');
    $params['limit'] = $this->_getParam('itemCount', 3);
    $fea_spo = $this->_getParam('fea_spo', '');
      $params['fea_spo'] = $fea_spo;
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

    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount"));
    $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    $sitestoreproductProductView = Zend_Registry::isRegistered('sitestoreproductProductView') ? Zend_Registry::get('sitestoreproductProductView') : null;

    $this->view->showCategory = $this->_getParam('showCategory', 1);
    $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
    $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
    $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
    $this->view->truncation = $this->_getParam('truncation', 16);
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');

    //GET PRODUCTS
    //SITEMOBILE CODE
    $limit = $this->_getParam('limit', 0);
    if($limit){
      $params['limit']= $limit;
    }

    $params['page'] = $this->_getParam('page', 1);
    $this->view->isajax = $this->_getParam('isajax', 0);
    $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
    $this->view->showinStock =  $params['in_stock'] = $this->_getParam('in_stock', 1);
    $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', array());
    $this->view->postedby = $params['postedby'] =  $this->_getParam('postedby', 0);
    $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '225');
    $this->view->ratingType = $params['ratingType'] = $this->_getParam('ratingType', 'rating_both');
    $this->view->layouts_views = $params['layouts_views'] = $this->_getParam('layouts_views', array("1","2"));
    $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 25);
    $this->view->params = $params;
    $params['paginator'] = true;
    $this->view->products = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->productsBySettings($params);  
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($params['limit']);
    $this->view->paginator = $paginator->setCurrentPageNumber($params['page']);
    

    //DON'T RENDER IF RESULTS IS ZERO
    if ((count($this->view->products) <= 0) || empty($sitestoreproductProductView)) {
      return $this->setNoRender();
    }
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->priceWithTitle = $this->_getParam('priceWithTitle', 0);
  }

}