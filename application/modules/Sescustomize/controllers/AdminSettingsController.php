<?php

class Sescustomize_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_settings');
    $this->view->form = $form = new Sescustomize_Form_Admin_Global();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $oldValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescustomize.bridges.value');
      $values = $form->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        if($key == "sescustomize_bridges_value"){
          $day = date('d');
          //if($day >= 25){
             $nextMonth = date('m-Y',strtotime('-1 Months',time()));
          //}else{
            // $nextMonth = date('m-Y',time());
          //}
          $getResult = Engine_Api::_()->sescustomize()->getValue($nextMonth);
          if(!$getResult)
            $insertId = Engine_Api::_()->sescustomize()->insertValue($nextMonth,$value);
          else
            Engine_Api::_()->sescustomize()->updateValue($getResult['bbvalue_id'],$value);
        }
      }
      $form->addNotice('Your changes have been saved.');
      $this->_helper->redirector->gotoRoute(array());
    }
  }

}
