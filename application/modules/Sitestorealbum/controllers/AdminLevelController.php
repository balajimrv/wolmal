<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_AdminLevelController extends Core_Controller_Action_Admin {

  //ACTION FOR LEVEL SETTINGS
  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');     
    
    //MAKE NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_main_level');

    //GET LEVEL
    if (null !== ($level_id = $this->_getParam('level_id', $this->_getParam('id')))) {
      $level = Engine_Api::_()->getItem('authorization_level', $level_id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    //GET LEVEL ID
    $level_id = $level->level_id;

    //MAKE FORM
    $this->view->form = $form = new Sitestorealbum_Form_Admin_Settings_Level(array(
                'public' => ( in_array($level->type, array('public')) ),
                'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
            ));
    $form->level_id->setValue($level_id);

    //GET PERMISSION TABLE
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($permissionsTable->getAllowed('sitestore_album', $level_id, array_keys($form->getValues())));
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    //GET DB
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      //SET PERMISSION
      $permissionsTable->setAllowed('sitestore_album', $level_id, $values);
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

}

?>