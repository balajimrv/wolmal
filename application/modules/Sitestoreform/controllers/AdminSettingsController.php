<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
    
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreform');    
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreform_admin_main', array(), 'sitestoreform_admin_main_settings');

    $this->view->form = $form = new Sitestoreform_Form_Admin_Global();
    
    if ($this->getRequest()->isPost()) {
      $sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
      if (!empty($sitestoreKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
      }
      if ($_POST['sitestoreform_lsettings']) {
        $_POST['sitestoreform_lsettings'] = trim($_POST['sitestoreform_lsettings']);
      }
    }
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      foreach ($values as $key => $value)
      {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }

      // It is only for installtion time use after it remove
    if (Engine_Api::_()->sitestore()->hasPackageEnable() && isset($values['include_in_package']) && !empty($values['include_in_package'])){
        Engine_Api::_()->sitestore()->oninstallPackageEnableSubMOdules('sitestoreform');
      }
    }
  }

  //ACTION FOR FAQ
  public function faqAction() {
    
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreform');    
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestoreform_admin_main', array(), 'sitestoreform_admin_main_faq');
  }
}
?>