<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');     
    
    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_widget_settings');

    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_submain', array(), 'sitestorealbum_admin_submain_general_tab');

    //GET WIDGET SETTING FORM
    //$this->view->form = $form = new Sitestorealbum_Form_Admin_Widget();

    //CHECK FORM VALIDATION
    //if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
     // $values = $form->getValues();
      //include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    //}
  }

}

?>