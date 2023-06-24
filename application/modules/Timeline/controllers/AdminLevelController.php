<?php

class Timeline_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
 
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_level');
    $level_id = $this->_getParam('level_id');
    // Make form
    $this->view->form = $form = new Timeline_Form_Admin_Level();
    
    if( !$this->getRequest()->isPost() )
    {
      if( null !== $level_id )
      {
        $permissionTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $select = $permissionTable->select()->where('level_id = ?', $level_id)->where('type = ?', 'timeline');
        $level_permissions = $permissionTable->fetchAll($select);
        $settings = array();
        
        foreach( $level_permissions as $timeline_permission )
        {
          $settings[$timeline_permission->name] =  $timeline_permission->value;
        }

        $settings = array_merge($settings, array(
          'level_id' => $level_id
        ));

        $form->populate($settings);
      }
      
      return;
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $level_id = $this->_getParam('level_id');
      $values = $form->getValues();
      $permissionTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
      $select = $permissionTable->select()->where('level_id = ?', $level_id)->where('type = ?', 'timeline');
      $level_permissions = $permissionTable->fetchAll($select);
      
      foreach ($values as $key => $value){
              
        $select = $permissionTable->select()->where('level_id = ?', $level_id)->where('type = ?', 'timeline')->where('name = ?', $key);
        $level_permission = $permissionTable->fetchRow($select);

        if($level_permission){
            $level_permission->value = $value;
            $level_permission->params = null;
          $level_permission->save();
        }
        else {
          $permission = $permissionTable->createRow();
          $permission->level_id = $level_id;
          $permission->name = $key;
          $permission->type = 'timeline';
          $permission->value = $value;

          $permission->save();
        }
      }
    }
  }
}