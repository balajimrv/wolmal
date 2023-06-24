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
class Sitestoreproduct_Widget_InformationSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }
    
    //GET SETTING
    $this->view->showContent = $this->_getParam('showContent', array("ownerPhoto","ownerName","modifiedDate","viewCount","likeCount","commentCount","tags","stores", 'category'));

    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET PRODUCT TAGS
    $this->view->sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
    
    $this->view->storeObj = $storeObj = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
    $this->view->storeCount = Engine_Api::_()->getDbtable('stores', 'sitestore')->countOwnerStores($storeObj->owner_id);
    
    // Categories
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    $categoriesNmae = $tableCategories->getCategory($sitestoreproduct->category_id);
    if (!empty($categoriesNmae->category_name)) {
      $this->view->category_name = $categoriesNmae->category_name;
    }
    $subcategory_name = $tableCategories->getCategory($sitestoreproduct->subcategory_id);
    if (!empty($subcategory_name->category_name)) {
      $this->view->subcategory_name = $subcategory_name->category_name;
    }
    //GET SUB-SUB-CATEGORY
    $subsubcategory_name = $tableCategories->getCategory($sitestoreproduct->subsubcategory_id);
    if (!empty($subsubcategory_name->category_name)) {
      $this->view->subsubcategory_name = $subsubcategory_name->category_name;
    }
    //WORK FOR SHOWING STORE COUNT AS A LINK ENDS HERE
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $this->view->enableLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
  }

}