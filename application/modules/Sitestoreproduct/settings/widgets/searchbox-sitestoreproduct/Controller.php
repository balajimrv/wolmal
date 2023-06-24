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
class Sitestoreproduct_Widget_SearchboxSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $widgetSettings = array();
//    $widgetSettings['formElements'] = $this->_getParam('formElements', array("textElement","categoryElement","linkElement"));
    $widgetSettings['formElements'] = $this->_getParam('formElements', array("textElement","categoryElement"));
    $widgetSettings['textWidth'] = $this->_getParam('textWidth', 580);
    $widgetSettings['categoryWidth'] = $this->_getParam('categoryWidth', 220);
    $this->view->categoriesLevel = $widgetSettings['categoriesLevel'] = $this->_getParam('categoriesLevel', array("category"));
    
    $this->view->categoryElementExist = 0;
    if(!empty($widgetSettings['formElements']) && in_array("categoryElement", $widgetSettings['formElements']) && !empty($widgetSettings['categoriesLevel'])) {
      $this->view->categoryElementExist = 1;
    }
    
    if(empty($widgetSettings['formElements']) || (!in_array("textElement", $widgetSettings['formElements']) && !in_array("categoryElement", $widgetSettings['formElements']))) {
      return $this->setNoRender();
    }

    //PREPARE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Searchbox(array('widgetSettings' => $widgetSettings));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $form->populate($params);
  }

}
