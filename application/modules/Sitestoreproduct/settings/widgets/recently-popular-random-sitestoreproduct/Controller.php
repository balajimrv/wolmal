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
class Sitestoreproduct_Widget_RecentlyPopularRandomSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {   
    $this->view->params = $param = $params = $this->_getAllParams();
    $params['limit'] = $this->_getParam('limit', 12);
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->priceWithTitle = $this->_getParam('priceWithTitle', 0);
    $this->view->showCategory = $this->_getParam('showCategory', 1);
    $this->view->showLocation = $this->_getParam('showLocation', 0);
    $this->view->postedby = $this->_getParam('postedby', 0);
    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));

    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ?  Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if(empty($currencySymbol)){
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
    
    //GET CORE API
    $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
    if (empty($isAjax)) {
      $showTabArray = $this->_getParam('ajaxTabs', array("recent", "most_reviewed", "most_popular", "featured", "sponsored", "top_selling", "new_arrivals"));

      if($showTabArray) {
        foreach ($showTabArray as $key => $value)
          $showTabArray[$key] = str_replace("ZZZ", "_", $value);
      }
      else {
        $showTabArray = array();
      }
      
      $this->view->tabs = $showTabArray;
      $this->view->tabCount = count($showTabArray);
      if (empty($this->view->tabCount)) {
        return $this->setNoRender();
      }
      $this->view->tabs = $showTabArray = $this->setTabsOrder($showTabArray);
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $layouts_views = $this->_getParam('layouts_views', array("list_view", "grid_view"));

    foreach ($layouts_views as $key => $value)
      $layouts_views[$key] = str_replace("ZZZ", "_", $value);

    $this->view->layouts_views = $layouts_views;
    $this->view->defaultLayout = str_replace("ZZZ", "_", $this->_getParam('defaultOrder', 'list_view'));
    //$this->_getParam('defaultOrder', 'list_view');

    $sitestoreproductTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    if (empty($isAjax)) {
      //GET LISTS
      $productCount = $sitestoreproductTable->hasProducts();

      if (empty($productCount)) {
        return $this->setNoRender();
      }
    }

    $this->view->category_id = 0;
    $this->view->subcategory_id = 0;
    $this->view->subsubcategory_id = 0;

    $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
    $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
    $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $paramsContentType = $this->_getParam('content_type', null);
    $this->view->content_type = $paramsContentType = $paramsContentType ? $paramsContentType : $showTabArray[0];
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }
    
    if (in_array('map_view', $layouts_views)) {
      if (!Engine_Api::_()->sitestoreproduct()->enableLocation()) {
        if (Count($layouts_views) == 1 && in_array('map_view', $layouts_views)) {
          return $this->setNoRender();
        }
        unset($layouts_views[array_search('map_view', $layouts_views)]);
        $this->view->layouts_views = $layouts_views;
      }
    }

    $this->view->paginator = $paginator = $sitestoreproductRecently = $sitestoreproductTable->getProduct($paramsContentType, $params);
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
    if (in_array('map_view', $layouts_views)) {
      
      if ($checkLocation) {
        $product_ids = array();
        $locationProduct = array();
        $this->view->flagSponsored = $this->view->settings->getSetting('sitestoreproduct.map.sponsored', 1);
        foreach ($paginator as $item) {
          if ($item->location) {
            $product_ids[] = $item->product_id;
            $locationProduct[$item->product_id] = $item;
          }
        }

        if (count($product_ids) > 0) {
          $values['product_ids'] = $product_ids;
          $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct')->getLocation($values);
          $this->view->locationsProduct = $locationProduct;
        }
      }
    }
    
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    $this->view->title_truncationList = $this->_getParam('truncationList', 600);
    $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
    $this->view->listViewType = $this->_getParam('listViewType', 'list');
    $paramsLocation = $params;
    if(isset($params) && isset($values) && is_array($values) && is_array($params))
      $paramsLocation = array_merge($params, $values);
    
    $this->view->paramsLocation = $paramsLocation;
    
  }

  public function setTabsOrder($tabs) {

    $tabsOrder['recent'] = $this->_getParam('recent_order', 1);
    $tabsOrder['most_reviewed'] = $this->_getParam('reviews_order', 2);
    $tabsOrder['most_popular'] = $this->_getParam('popular_order', 3);
    $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    $tabsOrder['sponsored'] = $this->_getParam('sponsored_order', 5);
    $tabsOrder['top_selling'] = $this->_getParam('top_selling_order', 6);
    $tabsOrder['new_arrivals'] = $this->_getParam('new_arrival_order', 7);

    $tempTabs = array();
    foreach ($tabs as $tab) {
      $order = $tabsOrder[$tab];
      if (isset($tempTabs[$order]))
        $order++;
      $tempTabs[$order] = $tab;
    }
    ksort($tempTabs);
    $orderTabs = array();
    $i = 0;
    foreach ($tempTabs as $tab)
      $orderTabs[$i++] = $tab;

    return $orderTabs;
  }

}