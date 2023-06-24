<?php

class Activitypoints_AdminLevelsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_levels');

    $level_id = $this->_getParam('level_id', 1);
    
    $level = Engine_Api::_()->getItem('authorization_level', $level_id);

    if( !$level instanceof Authorization_Model_Level ) {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
      $level_id = $level->level_id;
    }
    

    // Make form
    $this->view->form = $form = new Activitypoints_Form_Admin_Level();

    $form->level_id->setValue($level_id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($permissionsTable->getAllowed('activitypoints', $level_id, array_keys($form->getValues())));
      
      if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards')) {
        $v = $permissionsTable->getAllowed('activityrewards_spender', $level_id, array("edit"));
        $form->edit_spender->setValue(isset($v["edit"]) ? $v["edit"] : 0);
        
        $v = $permissionsTable->getAllowed('activityrewards_earner', $level_id, array("edit"));
        $form->edit_earner->setValue(isset($v["edit"]) ? $v["edit"] : 0);
      }
      
      return;
    }

   // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $permissionsTable->setAllowed('activitypoints', $level_id, array_diff_key($values, array("edit_spender" => 1,"edit_earner" => 1)));
      
      if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards')) {
        $permissionsTable->setAllowed('activityrewards_spender', $level_id, array("edit" => $values["edit_spender"]));
        $permissionsTable->setAllowed('activityrewards_earner', $level_id, array("edit" => $values["edit_earner"]));
      }
      
//      $permissionsTable->setAllowed('activitypoints', $level_id, $values);
      
      // Commit
      $db->commit();
      
      $form->addNotice("Changed successfully saved.");
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }

}