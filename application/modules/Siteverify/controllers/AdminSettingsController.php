<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteverify_AdminSettingsController extends Core_Controller_Action_Admin {

  public function __call($method, $params) {
    /*
     * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
     * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
     * REMEMBER:
     *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
     *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
     */

    if (!empty($method) && $method == 'Siteverify_Form_Admin_Global') {
      
    }
    return true;
  }
  
  public function indexAction() {
    $pluginName = 'siteverify';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    $this->view->isModsSupport = Engine_Api::_()->siteverify()->isModulesSupport();
    include_once APPLICATION_PATH . '/application/modules/Siteverify/controllers/license/license1.php';
  }

  public function faqAction() {
    //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteverify_admin_main', array(), 'siteverify_admin_main_faq');
  }
}