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
class Sitestoreproduct_Widget_CategoryProductsSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET PARAMETERS FOR SORTING THE RESULTS
    $params = array();
    $itemCount = $this->_getParam('itemCount', 0);
    $params['popularity'] = $popularity = $this->_getParam('popularity', 'view_count');
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['limit'] = $totalPages = $this->_getParam('productCount', 5);
    $this->view->title_truncation = $this->_getParam('truncation', 25);
    $sitestoreproductCatProductReview = Zend_Registry::isRegistered('sitestoreproductCatProductReview') ?  Zend_Registry::get('sitestoreproductCatProductReview') : null;
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    //GET CATEGORIES
    $categories = array();
    $category_info = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategorieshasproducts(0, 'category_id', $itemCount);

    foreach ($category_info as $value) {
      $category_products_array = array();

      $params['category_id'] = $value['category_id'];

      //GET PAGE RESULTS
      $category_products_info = $category_products_info = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->productsBySettings($params);

      foreach ($category_products_info as $result_info) {
        $tmp_array = array('product_id' => $result_info->product_id,
            'imageSrc' => $result_info->getPhotoUrl('thumb.icon'),
            'product_title' => $result_info->getTitle(),
            'owner_id' => $result_info->owner_id,
            'populirityCount' => $result_info->$popularity,
            'slug' => $result_info->getSlug());
        $category_products_array[] = $tmp_array;
      }
      $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          //'count' => $value->count,
          'category_products' => $category_products_array
      );
      $categories[] = $category_array;
    }
    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0) || empty($sitestoreproductCatProductReview)) {
      return $this->setNoRender();
    }
  }

}