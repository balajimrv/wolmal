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
class Sitestoreproduct_Widget_SearchSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $sitestoreproducttable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $sitestoreproductName = $sitestoreproducttable->info('name');

    $categoryTable = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

    $this->view->sitestoreproduct_post = true;

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    if (!isset($params['category_id']))
      $params['category_id'] = 0;
    if (!isset($params['subcategory_id']))
      $params['subcategory_id'] = 0;
    if (!isset($params['subsubcategory_id']))
      $params['subsubcategory_id'] = 0;
    $this->view->category_id = $category_id = $params['category_id'];
    $this->view->subcategory_id = $params['subcategory_id'];
    $this->view->subsubcategory_id = $params['subsubcategory_id'];

    $this->view->categoryInSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitestoreproduct', 'category_id');

     if(!isset($params['profile_type']) && !empty($this->view->category_id) && !empty($this->view->categoryInSearchForm)) {
        $categoryIds = array();
        $categoryIds[] = $this->view->category_id;
        $categoryIds[] = $this->view->subcategory_id;
        $categoryIds[] = $this->view->subsubcategory_id;
        
        $profile_type = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
        if(!empty($profile_type)) {
            $params['profile_type'] = $profile_type;
        }    
    }   
    
    //FORM CREATION
    $this->view->priceFieldType = $priceFieldType = $this->_getParam('priceFieldType', 'slider');
    $this->view->locationDetection = $this->_getParam('locationDetection', 0);
    $this->view->whatWhereWithinmile = $this->_getParam('whatWhereWithinmile', 0);
    $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
    $this->view->subcategoryFiltering = $subcategoryFiltering = $this->_getParam('subcategoryFiltering', 1);
    $currencySymbolPosition = $this->_getParam('currencySymbolPosition', "left");
    $this->view->minPrice = $minPrice = $this->_getParam('minPrice', 0);
    $this->view->maxPrice = $maxPrice = $this->_getParam('maxPrice', 999);    
    $this->view->viewType = $this->_getParam('viewType', 'vertical');
    $priceSettings = array(
        'priceFieldType' => $priceFieldType,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'currencySymbolPosition' => $currencySymbolPosition
    );
    $locationSettings = array(
      'locationDetection' => $this->view->locationDetection,
      'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
      'advancedSearch' => $this->view->advancedSearch,
      'viewType' => $this->view->viewType,
    );
    
    $this->view->widgetSettings = $widgetSettings = array(
        'resultsAction' => $this->_getParam('resultsAction', 'index'),
    );    
    
    $this->view->form = $form = new Sitestoreproduct_Form_Search(array('type' => 'sitestoreproduct_product', 'priceSettings' => $priceSettings, 'subcategoryFiltering' => $subcategoryFiltering, 'locationSettings' => $locationSettings, 'widgetSettings' => $widgetSettings));

    $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) {
      $order = Engine_Api::_()->sitestoreproduct()->showSelectedBrowseBy($this->view->identity);
      $form->orderby->setValue("$order");
    }

    if (isset($params['tag']) && !empty($params['tag'])) {
      $tag = $params['tag'];
      $tag_id = $params['tag_id'];
      $page = 1;
      if (isset($params['page']) && !empty($params['page'])) {
        $page = $params['page'];
      }

      $params['tag'] = $tag;
      $params['tag_id'] = $tag_id;
      $params['page'] = $page;
    }

    $orderBy = $request->getParam('orderby', null);

    if (!empty($orderBy)) {
      $params['orderby'] = $orderBy;
    }
    
    if($request->getParam('titleAjax')) {
      $params['search'] = $request->getParam('titleAjax');
    }    

    if (!empty($params))
      $form->populate($params);

    if (!$viewer) {
      $form->removeElement('show');
    }

    //SHOW PROFILE FIELDS ON DOME READY
    $category_search = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitestoreproduct', 'category_id');
    if (!empty($category_search) && !empty($category_search->display) && !empty($category_id)) {

      $categoryIds = array();
      $categoryIds[] = $category_id;

      //GET PROFILE MAPPING ID
      $this->view->profileType = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

    }  
      
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
    $categories_slug[0] = "";
    if (count($categories) != 0) {
      foreach ($categories as $category) {
        $categories_slug[$category->category_id] = $category->getCategorySlug();
      }
    }
    $this->view->categories_slug = $categories_slug;
  }

}
