<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteverify_AdminLevelController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteverify_admin_main', array(), 'siteverify_admin_main_level');

    // WE GET LEVEL ID
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // MAKE FORM
    $this->view->form = $form = new Siteverify_Form_Admin_Level();
    $form->level_id->setValue($id);

    // POPULATE VALUES
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    $prefieldValues = $permissionsTable->getAllowed('siteverify_verify', $id, array_keys($form->getValues()));
    $prefieldValues['verify_limit'] = Engine_Api::_()->authorization()->getPermission($id, 'siteverify_verify', 'verify_limit');
    $form->populate($prefieldValues);

    // CHECK POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    // CHECK VALIDITY
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // PROCESS
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {

      include_once APPLICATION_PATH . '/application/modules/Siteverify/controllers/license/license2.php';

      // COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

}