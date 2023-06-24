<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_CategoriesSitestoreController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->sitestore_categories = $sitestore_categories = Zend_Registry::isRegistered('sitestore_categories') ? Zend_Registry::get('sitestore_categories') : null;

    //SET NO RENDER
    if (empty($sitestore_categories)) {
      return $this->setNoRender();
    }

    $categories = array();
    $this->view->getModName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {

      $category_info = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories(1);
      foreach ($category_info as $value) {
        $sub_cat_array = array();
        $category_info2 = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories($value['category_id'], 'subcategory_id', 0, 'subcategory_id', null, 0, 0);
        foreach ($category_info2 as $subresults) {
          $treesubarray = array();
          $subcategory_info2 = Engine_Api::_()->getDbtable('categories', 'sitestore')->getAllCategories($subresults['category_id'], 'subcategory_id', 0, 'subcategory_id', null, 0, 0);
          $treesubarrays[$subresults->category_id] = array();
          foreach ($subcategory_info2 as $subvalues) {
            $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                'tree_sub_cat_name' => $subvalues->category_name,
                'order' => $subvalues->cat_order,
            );
            $treesubarrays[$subresults->category_id][] = $treesubarray;
          }

          $tmp_array = array('sub_cat_id' => $subresults->category_id,
              'sub_cat_name' => $subresults->category_name,
              'tree_sub_cat' => $treesubarrays[$subresults->category_id],
              'order' => $subresults->cat_order);
          $sub_cat_array[] = $tmp_array;
        }

        $category_array = array('category_id' => $value->category_id,
            'category_name' => $value->category_name,
            'order' => $value->cat_order,
            'sub_categories' => $sub_cat_array);
        $categories[] = $category_array;
      }

      $this->view->categories = $categories;
    } else {
      $categoriesAll = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategoriesByLevel();
      foreach ($categoriesAll as $category) {
        $categories[$category->cat_dependency][] = $category;
      }
      $this->view->categories = $categories;
      $this->view->categoriesCounter = count($categories[0]);
    }


    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }

    $this->view->subcategorys = 0;
    $this->view->category = 0;
    $this->view->subsubcategorys = 0;
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);

    if ($request->getParam('category')) {

      $categoryidtemp = $request->getParam('category');
      if ($request->getParam('subcategory')) {
        $subcategoryidtemp = $request->getParam('subcategory');
      } else {
        $subcategoryidtemp = $request->getParam('subcategory_id');
      }
      if ($request->getParam('subsubcategory')) {
        $subsubcategoryidtemp = $request->getParam('subsubcategory');
      } else {
        $subsubcategoryidtemp = $request->getParam('subsubcategory_id');
      }
      if (!empty($categoryidtemp)) {
        $this->view->category = $categoryidtemp;
        $this->view->subcategorys = $subcategoryidtemp;
        $this->view->subsubcategorys = $subsubcategoryidtemp;
      }
    } elseif ($request->getParam('category_id')) {
      $categoryid = $request->getParam('category_id');
      $subcategoryid = $request->getParam('subcategory_id');
      $subsubcategoryid = $request->getParam('subsubcategory_id');

      if (!empty($categoryid)) {
        $_GET['category_id'] = $this->view->category = $categoryid;
        $_GET['categoryname'] = $categoryname;
      }

      if (!empty($subcategoryid)) {
        $_GET['subcategory_id'] = $this->view->subcategorys = $subcategoryid;
        $_GET['subcategoryname'] = $subcategoryname;
      }

      if (!empty($subsubcategoryid)) {
        $_GET['subsubcategory_id'] = $this->view->subsubcategorys = $subsubcategoryid;
        $_GET['subsubcategoryname'] = $subcategoryname;
      }

      if (!empty($_GET)) {
        if (!empty($_GET['subcategory_id'])) {
          $this->view->subcategorys = $_GET['subcategory_id'];
        }
        if (!empty($_GET['category_id'])) {
          $this->view->category = $_GET['category_id'];
        }
        if (!empty($_GET['subsubcategory_id'])) {
          $this->view->subsubcategorys = $_GET['subsubcategory_id'];
        }
      }
    }

    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = $this->view->category = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }
  }

}

?>