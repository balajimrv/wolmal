<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_AdminSettingsController extends Core_Controller_Action_Admin {

  public function __call($method, $params) {
      /*
       * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
       * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
       * REMEMBER:
       *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
       *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
       */
      if (!empty($method) && $method == 'Sitemobile_Form_Admin_Global') {

      }
      return true;
  }
    
  public function indexAction() {
    $beforeActivateRemoveElement = array('sitemobile_site_title','sitemobile_enabel_tablet', 'sitemobile_dashboard_display', 'sitemobile_scroll_autoload', 'sitemobile_tinymceditor', 'save', 'is_remove_note','sitemobile_login_ajax','sitemobile_spam_signup','sitemobile_spam_login');
    $afterActivateRemoveElement = array('environment_mode', 'submit_lsetting');
    $db = Engine_Db_Table::getDefaultAdapter();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (isset($data['sitemobile_compatible']) && !empty($data['sitemobile_compatible'])) {
        $modulename = $data['package']['name'];
        $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$modulename','1')");
      }
    }
    
    if ($this->getRequest()->isPost()) {
      if ($_POST['sitemobile_lsettings']) {
        $_POST['sitemobile_lsettings'] = trim($_POST['sitemobile_lsettings']);
      }
    }
    
    include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license1.php";
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.isActivate', 0))
      $this->view->notIntegratedModules = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getManageModulesList(array('integrated' => 0));
  }

  // Redirect to Mobile Settings.
  public function mobileAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_settings');

    //GET SUB-NAVIGATION
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main_settings', array(), 'sitemobile_admin_main_settings_mobile');

    $this->view->form = $form = new Sitemobile_Form_Admin_Mobile();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license2.php";
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function tabletAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_settings');

    //GET SUB-NAVIGATION
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main_settings', array(), 'sitemobile_admin_main_settings_tablet');

    $this->view->form = $form = new Sitemobile_Form_Admin_Tablet();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license2.php";
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_faq');
    $this->view->faq_id = $faq_id = $this->_getParam('faq_id', 'faq_1');
  }

  public function readmeAction() {
    
  }

}