<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Widget_SearchSitestoreofferController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET THE COLUMN WHICH YOU WANT TO SHOW ON THE SEARCH WIDGET
    $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4"));

    $sitestore_searching = Zend_Registry::isRegistered('sitestore_searching') ? Zend_Registry::get('sitestore_searching') : null;

    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    $sitestoretable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $sitestoreName = $sitestoretable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

    //FORM CREATION
    $this->view->form = $form = new Sitestoreoffer_Form_Search(array('type' => 'sitestore_store'));
    $populateValue = 0;
    $hotoffer = Zend_Controller_Front::getInstance()->getRequest()->getParam('hotoffer',null);
    $orderby = Zend_Controller_Front::getInstance()->getRequest()->getParam('orderby', null);
    $sponsoredoffer = Zend_Controller_Front::getInstance()->getRequest()->getParam('sponsoredoffer');
    if(!empty($hotoffer)) {
      $populateValue = 1;
			$form->orderby->setValue("hotoffer");
    }
    elseif($orderby == 'comment') {
      $populateValue = 1;
			$form->orderby->setValue("comment_count");
    }
    elseif($orderby == 'popular') {
      $populateValue = 1;
			$form->orderby->setValue("claimed");
    }
    elseif($orderby == 'like') {
      $populateValue = 1;
			$form->orderby->setValue("like_count");
    }
    elseif($orderby == 'view') {
      $populateValue = 1;
			$form->orderby->setValue("view_count");
    }
    elseif($hotoffer != null) {
      $populateValue = 1;
			$form->orderby->setValue("creation_date");
    }
    if(!empty($sponsoredoffer)) {
       $populateValue = 1;
			$form->orderby->setValue("sponsored coupon");
    }
    $sitestore_post = Zend_Registry::isRegistered('sitestore_post') ? Zend_Registry::get('sitestore_post') : null;
    if ( !empty($sitestore_post) ) {
      $this->view->sitestore_post = $sitestore_post;
    }

    $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);
    $cattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);

    if ( !empty($cattemp) ) {
      $this->view->category_id = $_GET['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
      if ( !empty($row->category_name) ) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
      }

      $categorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
      $subcategorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
      if ( !empty($categorynametemp) ) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
      }
      if ( !empty($subcategorynametemp) ) {
        $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
      }
    }
    else {
      if ( $categoryname )
        $this->view->category_name = $_GET['categoryname'] = $categoryname;
      if ( $category ) {
        $this->view->category_id = $_GET['category_id'] = $category;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
        if ( !empty($row->category_name) ) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
    }

    $subcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);

    if ( !empty($subcattemp) ) {
      $this->view->subcategory_id = $_GET['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
      if ( !empty($row->category_name) ) {
        $this->view->subcategory_name = $row->category_name;
      }
    }
    else {
      if ( $subcategoryname )
        $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;
      if ( $subcategory ) {
        $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
        if ( !empty($row->category_name) ) {
          $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
        }
      }
    }

    $subsubcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);

    if ( !empty($subsubcattemp) ) {
      $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
      if ( !empty($row->category_name) ) {
        $this->view->subsubcategory_name = $row->category_name;
      }
    }
    else {
      if ( $subsubcategoryname )
        $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

      if ( $subsubcategory ) {
        $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
        if ( !empty($row->category_name) ) {
          $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
        }
      }
    }

    if ( empty($categoryname) ) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = $categoryname;
      $_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }

    if ((!isset($_POST['orderby']) || empty($_POST)) && empty($populateValue)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.order', 1);
      if($order == 1) {
				$form->orderby->setValue("creation_date");
      }
    }

    if ( !empty($_GET) )
      $form->populate($_GET);

    if ( empty($sitestore_searching) ) {
      return $this->setNoRender();
    }
  }

}

?>