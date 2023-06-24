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
class Sitestore_Widget_SearchSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $sitestore_searching = Zend_Registry::isRegistered('sitestore_searching') ? Zend_Registry::get('sitestore_searching') : null;

    $sitestoretable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $sitestoreName = $sitestoretable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->locationDetection = $locationDetection = $this->_getParam('locationDetection', 0);
    $widgetSettings = array(
        'locationDetection' => $this->view->locationDetection
    );
 
    $this->view->form = $form = new Sitestore_Form_Search(array('type' => 'sitestore_store', 'widgetSettings' => $widgetSettings));


    //FORM CREATION
    //$this->view->form = $form = new Sitestore_Form_Search(array('type' => 'sitestore_store'));
    $this->view->viewType = $this->_getParam('viewType', 'vertical');

    $sitestore_post = Zend_Registry::isRegistered('sitestore_post') ? Zend_Registry::get('sitestore_post') : null;
    if (!empty($sitestore_post)) {
      $this->view->sitestore_post = $sitestore_post;
    }
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if ((!isset($p['category']) || empty($p['category'])) && (!isset($p['category_id']) || empty($p['category_id']))) {
      $content_id = $this->view->identity;
      $widgetname = 'sitestore.stores-sitestore';
      $filtercategory_id = Engine_Api::_()->sitestore()->getSitestoreCategoryid($content_id, $widgetname);
      if (empty($filtercategory_id))
        $filtercategory_id = Engine_Api::_()->sitestore()->getSitestoreCategoryid($content_id, 'sitestore.pinboard-browse');
      $category = $filtercategory_id;
    } else {
      $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    }

    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);
    $cattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);

    if (!empty($cattemp)) {
      $this->view->category_id = $_GET['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
      if (!empty($row->category_name)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
      }

      $categorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
      $subcategorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
      if (!empty($categorynametemp)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
      }
      if (!empty($subcategorynametemp)) {
        $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
      }
    } else {
      if ($categoryname)
        $this->view->category_name = $_GET['categoryname'] = $categoryname;
      if ($category) {
        $this->view->category_id = $_GET['category_id'] = $category;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
    }

    $subcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);

    if (!empty($subcattemp)) {
      $this->view->subcategory_id = $_GET['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subcategory_name = $row->category_name;
      }
    } else {
      if ($subcategoryname)
        $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;
      if ($subcategory) {
        $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
        }
      }
    }

    $subsubcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);

    if (!empty($subsubcattemp)) {
      $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory');
      $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subsubcategory_name = $row->category_name;
      }
    } else {
      if ($subsubcategoryname)
        $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

      if ($subsubcategory) {
        $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
        }
      }
    }

    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = $categoryname;
      $_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }


    if (!isset($_POST['orderby']) || empty($_POST)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.browseorder', 1);
      switch ($order) {
        case "1":
          $form->orderby->setValue("creation_date");
          break;
        case "2":
          $form->orderby->setValue("view_count");
          break;
        case "3":
          $form->orderby->setValue("title");
          break;
      }
    }
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
    if ($stusShow == 0) {
      $form->removeElement('closed');
    }

    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
      if (!empty($_POST['badge_id'])) {
        $_GET['badge_id'] = $_POST['badge_id'];
      }
    }

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
      $store = 1;
      if (isset($_GET['store']) && !empty($_GET['store'])) {
        $store = $_GET['store'];
      }
      // unset($_GET);
      $_GET['tag'] = $tag;
      $_GET['store'] = $store;
    }
    $rating = Zend_Controller_Front::getInstance()->getRequest()->getParam('orderby', null);
    $_GET['orderby'] = $rating;
    if (!empty($_GET))
      $form->populate($_GET);

    if (!$viewer) {
      $form->removeElement('show');
    }


    //  $form->tag->setValue("");
    if (empty($sitestore_searching)) {
      return $this->setNoRender();
    }
  }

}