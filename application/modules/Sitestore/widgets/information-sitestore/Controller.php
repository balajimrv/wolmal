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
class Sitestore_Widget_InformationSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {    
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->showContent = array("ownerPhoto", "ownerName", "modifiedDate", "viewCount","likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName", "stores");

    //GET SETTING
    $this->view->showContent = $this->_getParam('showContent', array("ownerPhoto", "ownerName", "modifiedDate", "viewCount","likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName", "stores"));

    if(empty($this->view->showContent))
     return $this->setNoRender(); 
   
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestore');
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
    	$this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    else {
      $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject()->getParent();
    }
    $categoriesNmae = $tableCategories->getCategory($sitestore->category_id);
    if (!empty($categoriesNmae->category_name)) {
      $this->view->category_name = $categoriesNmae->category_name;
    }
    $subcategory_name = $tableCategories->getCategory($sitestore->subcategory_id);
    if (!empty($subcategory_name->category_name)) {
      $this->view->subcategory_name = $subcategory_name->category_name;
    }
    //GET SUB-SUB-CATEGORY
    $subsubcategory_name = $tableCategories->getCategory($sitestore->subsubcategory_id);
    if (!empty($subsubcategory_name->category_name)) {
      $this->view->subsubcategory_name = $subsubcategory_name->category_name;
    }
    $this->view->sitestoreTags = $sitestore->tags()->getTagMaps();
    $owner_id = $sitestore->owner_id;
    
    $count = Engine_Api::_()->getDbtable('stores', 'sitestore')->countOwnerStores($owner_id);
    $this->view->storeCount = $count;
    //WORK FOR SHOWING STORE COUNT AS A LINK ENDS HERE
    
  }
}
?>