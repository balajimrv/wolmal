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
class Sitestoreproduct_Widget_CategoriesHomeBreadcrumbController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);

    if (empty($category_id)) {
      return $this->setNoRender();
    }

    //GET USER SUBJECT    
    $this->view->category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
    
    if(empty($this->view->category)) {
      return $this->setNoRender();
    }
  }

}