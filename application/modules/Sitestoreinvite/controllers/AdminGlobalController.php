<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminglobalController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_AdminGlobalController extends Core_Controller_Action_Admin {

  public function globalAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_settings');

    //GET NAVIGATION
    $this->view->navigationStoreGlobal = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main_settings', array(), 'sitestore_admin_main_global_invite');

    $this->view->form = $form = new Sitestoreinvite_Form_Admin_Global();

    if ($this->getRequest()->isPost()) {
      $sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
      if (!empty($sitestoreKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
      }
      if ($_POST['sitestoreinvite_lsettings']) {
        $_POST['sitestoreinvite_lsettings'] = trim($_POST['sitestoreinvite_lsettings']);
      }
    }
    //START LANGUAGE WORK
    Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
    //END LANGUAGE WORK
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $_POST;
      foreach ($values as $key => $value) {
        if ($key == 'storeinvite_show_webmail') {
          $value = serialize($value);
        }
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }
    }
  }

  public function appconfigsAction() {

//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//            ->getNavigation('sitestoreinvite_admin_main', array(), 'sitestoreinvite_admin_main_global');
  }

}

?>