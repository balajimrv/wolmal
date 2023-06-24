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
class Sitestore_Widget_HorizontalSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $valueArray = array('street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1), 'state' => $this->_getParam('state', 1));
    $sitestore_street = serialize($valueArray);

    // Make form
    $this->view->form = $form = new Sitestore_Form_Locationsearch(array('value' => $sitestore_street, 'type' => 'sitestore_store'));

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if (!empty($p)) {
      if (isset($p['category']))
        $this->view->category_id = $p['category'];
      if (isset($p['category_id']))
        $this->view->category_id = $p['category_id'];
      if (isset($p['subcategory_id']))
        $this->view->subcategory_id = $p['subcategory_id'];
      if (isset($p['subcategoryname']))
        $this->view->subcategory_name = $p['subcategoryname'];
      if (isset($p['subsubcategory_id']))
        $this->view->subsubcategory_id = $p['subsubcategory_id'];
    }

    if (!$this->view->category_id) {
      $content_id = $this->view->identity;
      $widgetname = 'sitestore.pinboard-browse';
      $filtercategory_id = Engine_Api::_()->sitestore()->getSitestoreCategoryid($content_id, $widgetname);
      if (!empty($filtercategory_id)) {
        $this->view->category_id = $p['category_id'] = $filtercategory_id;
      } else {
        $content_id = $this->view->identity;
        $widgetname = 'sitestore.stores-sitestore';
        $filtercategory_id = Engine_Api::_()->sitestore()->getSitestoreCategoryid($content_id, $widgetname);
        if (!empty($filtercategory_id)) {
          $this->view->category_id = $p['category_id'] = $filtercategory_id;
        }
      }
    }

    if ($this->view->category_id) {
      if (!isset($_GET['category_id']))
        $_GET['category_id'] = $p['category_id'];
      if (!isset($_GET['categoryname'])) {
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
      $this->view->advanced_search = $p['advanced_search'] = 1;
    } elseif (!empty($p) && isset($p['advanced_search'])) {
      $this->view->advanced_search = $p['advanced_search'];
    }

// 		if (!empty($p)) {
// 			$form->populate($p);
//     }
    // Process form

    $form->isValid($p);
    $values = $form->getValues();

    unset($values['or']);
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
    $form->setMethod('GET');
    $browseredirect = $this->_getParam('browseredirect', 'default');
    if ($browseredirect == 'pinboard')
      $form->setAction($this->view->url(array('action' => 'pinboard-browse'), 'sitestore_general', true));
    elseif ($browseredirect == 'default') {
      $form->setAction($this->view->url(array('action' => 'index'), 'sitestore_general', true));
    }

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();

    if ($module == 'sitestoreproduct' && $browseredirect == 'pinboard') {
      $form->setAction($this->view->url(array('action' => 'home'), 'sitestore_general', true));
    }
    elseif ($module == 'sitestoreproduct' && $browseredirect == 'default') {
      $form->setAction($this->view->url(array('action' => 'index'), 'sitestore_general', true));
    }
  }

}
