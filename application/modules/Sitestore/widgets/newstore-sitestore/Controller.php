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
class Sitestore_Widget_NewstoreSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->creationLink = $this->_getParam('creationLink', 1);
    
    if($this->view->creationLink) {
    //GET QUICK NAVIGATION
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_quick');
    }
    else {
      //WHO CAN CREATE STORE
      $menusClass = new Sitestore_Plugin_Menus();
      $this->view->canCreateStores = $menusClass->canCreateSitestores();
      $this->view->canCreate = 1;
      if(!$this->view->canCreateStores || ((!Engine_Api::_()->sitestore()->hasPackageEnable() && !Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create")))) {
        $this->view->canCreate = 0;
      }
      
      
    }
    
  }
}

?>