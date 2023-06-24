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
class Sitestoreproduct_Widget_CategoriesHomeController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->show3rdlevelCategory = $show3rdlevelCategory = $this->_getParam('show3rdlevelCategory', 1);
    $this->view->show2ndlevelCategory = $show2ndlevelCategory = $this->_getParam('show2ndlevelCategory', 1);
    $request = Zend_Controller_Front::getInstance()->getRequest();
    if($request->getParam('showCount')) {
      $this->view->showCount = $showCount = 1;
    }
    else {
      $this->view->showCount = $showCount = $this->_getParam('showCount', 0);
    }
    $orderBy = $this->_getParam('orderBy', 'category_name');
    $this->view->viewType = $this->_getParam('viewType', 'expanded');

    $showAllCategories = $this->_getParam('showAllCategories', 1);

    $sitestoreproductCategoriesHome = Zend_Registry::isRegistered('sitestoreproductCategoriesHome') ?  Zend_Registry::get('sitestoreproductCategoriesHome') : null;
    
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');

    $this->view->categories = $categories = array();

    //GET PRODUCT TABLE
    $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

    if ($showAllCategories) {

      $category_info = $tableCategory->getCategories(null, 0, 0, 1, 0, $orderBy);
      foreach ($category_info as $value) {

        $sub_cat_array = array();

        if (!empty($show2ndlevelCategory)) {

          $category_info2 = $tableCategory->getSubcategories($value->category_id);

          foreach ($category_info2 as $subresults) {

            if (!empty($show3rdlevelCategory)) {

              $subcategory_info2 = $tableCategory->getSubcategories($subresults->category_id);
              $treesubarrays[$subresults->category_id] = array();
              foreach ($subcategory_info2 as $subvalues) {
                if ($showCount) {
                  $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                      'tree_sub_cat_name' => $subvalues->category_name,
                      'count' => $tableSitestoreproduct->getProductsCount($subvalues->category_id, 'subsubcategory_id', 1),
                      'order' => $subvalues->cat_order,
                  );
                } else {
                  $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                      'tree_sub_cat_name' => $subvalues->category_name,
                      'order' => $subvalues->cat_order,
                  );
                }
              }

              if ($showCount) {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $tableSitestoreproduct->getProductsCount($subresults->category_id, 'subcategory_id', 1),
                    'order' => $subresults->cat_order);
              } else {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'order' => $subresults->cat_order);
              }
            } else {
              if ($showCount) {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'count' => $tableSitestoreproduct->getProductsCount($subresults->category_id, 'subcategory_id', 1),
                    'order' => $subresults->cat_order);
              } else {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'order' => $subresults->cat_order);
              }
            }
          }
        }

        if ($showCount) {
          $categories[] = $category_array = array('category_id' => $value->category_id,
              'category_name' => $value->category_name,
              'order' => $value->cat_order,
              'count' => $tableSitestoreproduct->getProductsCount($value->category_id, 'category_id', 1),
              'sub_categories' => $sub_cat_array
          );
        } else {
          $categories[] = $category_array = array('category_id' => $value->category_id,
              'category_name' => $value->category_name,
              'order' => $value->cat_order,
              'sub_categories' => $sub_cat_array
          );
        }
      }
    } else {
      $category_info = $tableCategory->getCategorieshasproducts(0, 'category_id');
      foreach ($category_info as $value) {

        $sub_cat_array = array();

        if (!empty($show2ndlevelCategory)) {

          $category_info2 = $tableCategory->getCategorieshasproducts($value->category_id, 'subcategory_id');

          foreach ($category_info2 as $subresults) {

            if (!empty($show3rdlevelCategory)) {

              $subcategory_info2 = $tableCategory->getCategorieshasproducts($subresults->category_id, 'subsubcategory_id');
              $treesubarrays[$subresults->category_id] = array();
              foreach ($subcategory_info2 as $subvalues) {
                if ($showCount) {
                  $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                      'tree_sub_cat_name' => $subvalues->category_name,
                      'order' => $subvalues->cat_order,
                      'count' => $tableSitestoreproduct->getProductsCount($subvalues->category_id, 'subsubcategory_id', 1),
                  );
                } else {
                  $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                      'tree_sub_cat_name' => $subvalues->category_name,
                      'order' => $subvalues->cat_order
                  );
                }
              }

              if ($showCount) {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $tableSitestoreproduct->getProductsCount($subresults->category_id, 'subcategory_id', 1),
                    'order' => $subresults->cat_order);
              } else {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'order' => $subresults->cat_order);
              }
            } else {
              if ($showCount) {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'count' => $tableSitestoreproduct->getProductsCount($subresults->category_id, 'subcategory_id', 1),
                    'order' => $subresults->cat_order);
              } else {
                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'order' => $subresults->cat_order);
              }
            }
          }
        }

        if ($showCount) {
          $categories[] = $category_array = array('category_id' => $value->category_id,
              'category_name' => $value->category_name,
              'order' => $value->cat_order,
              'sub_categories' => $sub_cat_array,
              'count' => $tableSitestoreproduct->getProductsCount($value->category_id, 'category_id', 1),
          );
        } else {
          $categories[] = $category_array = array('category_id' => $value->category_id,
              'category_name' => $value->category_name,
              'order' => $value->cat_order,
              'sub_categories' => $sub_cat_array
          );
        }
      }
    }

    //SEND CATEGORIES TO TPL
    $this->view->categories = $categories;
    $this->view->totalCategories = count($this->view->categories);

    //SET NO RENDER
    if (($this->view->totalCategories <= 0) || empty($sitestoreproductCategoriesHome)) {
      return $this->setNoRender();
    }
  }

}
