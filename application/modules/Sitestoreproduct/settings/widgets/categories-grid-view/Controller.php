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
class Sitestoreproduct_Widget_CategoriesGridViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $showSubCategoryCount = $this->_getParam('showSubCategoriesCount', 5);
    $this->view->count = $count = $this->_getParam('showCount', 0);
    
    $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');
    $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $this->view->columnWidth = $this->_getParam('columnWidth', 234);
    $this->view->columnHeight = $this->_getParam('columnHeight', 216);
    $this->view->storage = Engine_Api::_()->storage();
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $this->view->category_id = $category_id = Zend_Registry::isRegistered('sitestoreproductCategoryId') ?  Zend_Registry::get('sitestoreproductCategoryId') : null;
    if( empty($category_id) )
      $categories = $tableCategory->getCategories(null, 0, 0, 1, 0, "cat_order");
    else
      $categories = $tableCategory->getSubcategories($category_id);      
        
    foreach ($categories as $category) {
      $subcategory_info2 = $tableCategory->getSubcategories($category->category_id);
      $SubCategoryArray = $tempSubCategoryArray = array();
      if( !empty($subcategory_info2) ) {
        $tempFlag = 0;
        foreach($subcategory_info2 as $subCategory) {
          if( $tempFlag == $showSubCategoryCount )
            break;
          else
              $tempFlag++;
          $tempSubCategoryArray['sub_category_id'] = $subCategory->getIdentity();
          $tempSubCategoryArray['title'] = $subCategory->getTitle();
          $tempSubCategoryArray['order'] = $subCategory->cat_order;
          $tempSubCategoryArray['count'] = !empty($count)? $tableSitestoreproduct->getProductsCount($subCategory->category_id, 'subcategory_id', 1): false;
          $tempSubCategoryArray['subcat_dependency'] = !empty($subCategory->subcat_dependency)? $subCategory->subcat_dependency: 0;
          $tempSubCategoryArray['root_category_id'] = !empty($category->cat_dependency)? $category->cat_dependency: 0;
          
          $SubCategoryArray[] = $tempSubCategoryArray;
        }
      }
      $tempCategoryParams['category_id'] = $category->getIdentity();
      $tempCategoryParams['title'] = $category->getTitle();
      $tempCategoryParams['order'] = $category->cat_order;
      $tempCategoryParams['photo_id'] = $category->photo_id;
      $tempCategoryParams['subCategories'] = $SubCategoryArray;
      $tempCategoryParams['count'] = !empty($count)? $tableSitestoreproduct->getProductsCount($category->category_id, 'category_id', 1): false;
      $categoryParams[] = $tempCategoryParams;      
    }
    $this->view->categoryParams = $categoryParams;
  }
}
