<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Widget_SitestoreAlbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $values = array();
    $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $subcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);

    if ($category)
      $_GET['category'] = $category;
    if ($subcategory)
      $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
      $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
      $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
      $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;

    //GET VALUE BY POST TO GET DESIRED SITESTORES
    if (!empty($_GET)) {
      $values = $_GET;
    }

    if (($category) != null) {
      $this->view->category = $values['category'] = $category;
      $this->view->subcategory = $values['subcategory'] = $subcategory;
      $this->view->subsubcategory = $values['subsubcategory'] = $subsubcategory;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }

    if (($category_id) != null) {
      $this->view->category_id = $values['category'] = $category_id;
      $this->view->subcategory_id = $values['subcategory'] = $subcategory_id;
      $this->view->subsubcategory_id = $values['subsubcategory'] = $subsubcategory_id;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }


    $form = new Sitestorealbum_Form_Searchwidget(array('type' => 'sitestore_store'));
    $values['order_browse'] = $form->getValues();
    $this->view->assign($values);


    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id =  0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }
    
    $this->view->itemCount = $totalAlbums = $values['itemCount'] ? $values['itemCount'] : $this->_getParam('itemCount', 20);
    $values['sponsoredalbum'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('sponsoredalbum', null);
    $widgetType = 'browsealbum';
    //GET ALBUMS DATA
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($values,$widgetType);

     $paginator->setItemCountPerPage($totalAlbums);
     if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
       $this->view->paginator = $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('store'));
     }else{
       $this->view->paginator = $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));
     }
     //$this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('store'));
  }

}
?>