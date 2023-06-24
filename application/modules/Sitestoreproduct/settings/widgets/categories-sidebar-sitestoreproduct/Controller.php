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
class Sitestoreproduct_Widget_CategoriesSidebarSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET LIST API
    $this->view->list_api = $sitestoreproduct_api = Engine_Api::_()->sitestoreproduct();

    //GET PRODUCT CATEGORY TABLE
    $this->view->categoryTable = $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();

    //GET TRUNCATION LIMIT
    $this->view->catTruncLimit = 41;
    $this->view->subCatTruncLimit = $this->view->subsubCatTruncLimit = 25;
    $this->view->categoryRouteName = "sitestoreproduct_general_category";//Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $categories = array();
    $category_info = $tableCategory->getCategories(null, 0, 0, 1);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $category_info2 = $tableCategory->getSubcategories($value->category_id);
      foreach ($category_info2 as $subresults) {
        $treesubarray = array();
        $subcategory_info2 = $tableCategory->getSubcategories($subresults->category_id);
        $treesubarrays[$subresults->category_id] = array();
        foreach ($subcategory_info2 as $subvalues) {
          $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
              'tree_sub_cat_name' => $subvalues->category_name,
              'order' => $subvalues->cat_order,
              'file_id' => $subvalues->file_id,
          );
        }

        $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'order' => $subresults->cat_order,
            'file_id' => $subresults->file_id,
        );
        $sub_cat_array[] = $tmp_array;
      }

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'sub_categories' => $sub_cat_array,
          'file_id' => $value->file_id,
      );
    }

    $this->view->categories = $categories;

    if (Count($this->view->categories) <= 0) {
      return $this->setNoRender();
    }

    $params = $request->getParams();
    if (!isset($params['category_id']))
      $params['category_id'] = 0;
    if (!isset($params['subcategory_id']))
      $params['subcategory_id'] = 0;
    if (!isset($params['subsubcategory_id']))
      $params['subsubcategory_id'] = 0;
    $this->view->category_id = $params['category_id'];
    $this->view->subcategory_id = $params['subcategory_id'];
    $this->view->subsubcategory_id = $params['subsubcategory_id'];
    
//    if (!empty($category_id)) {
//      $_GET['category_id'] = $this->view->category = $this->view->category_id = $category_id;
//      $_GET['categoryname'] = $this->view->categoryname = $tableCategory->getCategory($category_id)->category_name;
//
//      $subcategory_id = $request->getParam('subcategory_id', null);
//
//      if (!empty($subcategory_id)) {
//        $_GET['subcategory_id'] = $this->view->subcategory = $this->view->subcategory_id = $subcategory_id;
//        $_GET['subcategoryname'] = $this->view->subcategoryname = $tableCategory->getCategory($subcategory_id)->category_name;
//
//        $subsubcategory_id = $request->getParam('subsubcategory_id', null);
//        
//        if (!empty($subsubcategory_id)) {
//          $_GET['subsubcategory_id'] = $this->view->subsubcategory = $this->view->subsubcategory_id = $subsubcategory_id;
//          $_GET['subsubcategoryname'] = $this->view->subsubcategoryname = $tableCategory->getCategory($subsubcategory_id)->category_name;
//        }
//      }
//    }
  }

}