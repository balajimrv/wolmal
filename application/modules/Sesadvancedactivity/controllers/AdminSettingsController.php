<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
  
    $db = Engine_Db_Table::getDefaultAdapter();;
    
    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesbasic.defaultcurrency')) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesbasic.defaultcurrency','USD'); 
    }
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesadvancedactivity_admin_main', array(), 'sesadvancedactivity_admin_main_settings');
    
    $this->view->form = $form = new Sesadvancedactivity_Form_Admin_Settings_General();
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      include_once APPLICATION_PATH . "/application/modules/Sesadvancedactivity/controllers/License.php";
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.pluginactivated')) {
        $db = Engine_Db_Table::getDefaultAdapter();;
        foreach ($values as $key => $value) {
          if($key == 'sesadvancedactivity_composeroptions' &&  Engine_Api::_()->getApi('settings', 'core')->hasSetting($key) ){
            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
					}
					if($value != '')
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        if($error)
        $this->_helper->redirector->gotoRoute(array());
      }
    }
  }
  
  public function feedprivacycleanupAction() {
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->query('DELETE from engine4_activity_stream WHERE action_id NOT IN (SELECT action_id FROM engine4_activity_actions);');
      
      die;
  }
  
  public function welcometabAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesadvancedactivity_admin_main', array(), 'sesadvancedactivity_admin_main_welcomesettings');
    
    $this->view->form = $form = new Sesadvancedactivity_Form_Admin_Settings_WelcomeTab();
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
//         Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
        if($value != '')
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
  
  
  
  public function createAction(){
    $id = $this->_getParam('id',false);
  
    $this->view->form = $form = new Sesadvancedactivity_Form_Admin_Settings_Create();
      if($id){
        $item = Engine_Api::_()->getItem('sesadvancedactivity_filterlist',$id);
        $form->populate($item->toArray());
        $form->setTitle('Edit This Filter');
        $form->submit->setLabel('Edit');
        if(!$item->is_delete){
          $form->removeElement('filtertype'); 
          $form->removeElement('module');  
        }
      }
    // Check if post
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    $db = Engine_Api::_()->getDbtable('filterlists', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();
    // If we're here, we're done
    $this->view->status = true;
    try {
      $filterTable = Engine_Api::_()->getDbtable('filterlists', 'sesadvancedactivity');
      if(empty($id))
       $item = $filterTable->createRow();
      $item->setFromArray($form->getValues());
      $item->save();
      if(!$id){
        $item->order = $item->getIdentity();
        $item->save();
      }
      $db->commit();
    }catch(Exception $e){
      $db->rollBack();
      throw $e;  
    }
    $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh'=> 10,
                    'messages' => array('Filter Type Created Successfully.')
    ));
  }
  public function filterAction(){
     $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesadvancedactivity_admin_main', array(), 'sesadvancedactivity_admin_main_filtersettings');
     $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesadvancedactivity_admin_content_setting_main', array(), 'sesadvancedactivity_admin_main_filtermainsettings');
     $this->view->form = $form = new Sesadvancedactivity_Form_Admin_Settings_Filtersettings();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
        foreach ($values as $key => $value) {
          
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
    }
  }
  
  public function filterContentAction(){
     if(!empty($_POST['order'])){
       $counter = 1;
        foreach($_POST['order'] as $order){
          $item = Engine_Api::_()->getItem('sesadvancedactivity_filterlist',$order);  
          if(!$item)
            continue;
          $item->order = $counter;
          $item->save();
          $counter++;
        }  
     }
     $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesadvancedactivity_admin_main', array(), 'sesadvancedactivity_admin_main_filtersettings');
     $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesadvancedactivity_admin_content_setting_main', array(), 'sesadvancedactivity_admin_main_filtercontentsettings');
     
     $this->view->paginator = Engine_Api::_()->getDbTable('filterlists','sesadvancedactivity')->fetchAll(Engine_Api::_()->getDbTable('filterlists','sesadvancedactivity')->select()->order('order ASC'));
  }
  
  public function enabledAction() {
    $id = $this->_getParam('id');
    if (!empty($id)) {
      $item = Engine_Api::_()->getItem('sesadvancedactivity_filterlist', $id);
      $item->active = !$item->active;
      $item->save();
    }
    
    $this->_redirect('admin/sesadvancedactivity/settings/filter-content');
  }
  public function notificationAction(){
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesadvancedactivity_admin_main', array(), 'sesadvancedactivity_admin_main_feednotification');
    $this->view->form = $form = new Sesadvancedactivity_Form_Admin_Settings_Notification();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
        foreach ($values as $key => $value) {
          if($key == 'sesadvancedactivity_composeroptions' &&  Engine_Api::_()->getApi('settings', 'core')->hasSetting($key) ){
            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
					}
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
    }
  }
  public function deleteAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete Filter?');
    $form->setDescription('Are you sure that you want to delete this filter? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');

    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $item = Engine_Api::_()->getItem('sesadvancedactivity_filterlist', $id)->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Filter Delete Successfully.')
      ));
    }
  }
}