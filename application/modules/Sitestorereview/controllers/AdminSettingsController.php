<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorereview_admin_main', array(), 'sitestorereview_admin_main_global');

    $this->view->form = $form = new Sitestorereview_Form_Admin_Settings_Global();

    if ($this->getRequest()->isPost()) {
      $sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
      if (!empty($sitestoreKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
      }
      if ($_POST['sitestorereview_lsettings']) {
        $_POST['sitestorereview_lsettings'] = trim($_POST['sitestorereview_lsettings']);
      }
    }
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $_POST;

      foreach ($values as $key => $value)
      {
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }

    }	
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestorereview_admin_main', array(), 'sitestorereview_admin_main_widget');
  }

  //ACTION FOR LEVEL SETTINGS
  public function levelAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestorereview_admin_main', array(), 'sitestorereview_admin_main_level');

    //GET LEVEL ID
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    //LEVEL AUTHORIZATION
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    //CREATE FORM
    $this->view->form = $form = new Sitestorereview_Form_Admin_Settings_Level(array(
                'public' => ( in_array($level->type, array('public')) ),
                'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
            ));
    $form->level_id->setValue($level_id);

    //POPULATE DATA
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('sitestorereview_review', $level_id, array_keys($form->getValues())));

    //FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $permissionsTable->setAllowed('sitestorereview_review', $level_id, $values);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  //ACTION FOR REVIEW OF THE DAY
  public function manageDayItemsAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestorereview_admin_main', array(), 'sitestorereview_admin_main_dayitems');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestorereview_Form_Admin_Manage_Filter();
    $store = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {
      if (null == $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    //FETCH DATA
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->getItemOfDayList($values, 'review_id', 'sitestorereview_review');
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($store);
  }

  //ACTION FOR ADDING REVIEW OF THE DAY
  public function addDayItemAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitestorereview_Form_Admin_Settings_AddDayItem();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitestorereview_review');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitestorereview_review';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Review of the Day has been added successfully.'))
      ));
    }
  }

  //ACTION FOR REVIEW OF THE DAY SUGGESTION DROP-DOWN
  public function getDayItemAction() {

    $search_text = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);

    $data = array();

    //GET RESULTS
    $moduleContents = Engine_Api::_()->getItemTable('sitestorereview_review')->getDayItems($search_text, $limit);

    foreach ($moduleContents as $moduleContent) {

      $user = Engine_Api::_()->getItem('user', $moduleContent->owner_id);
      $content_photo = $this->view->itemPhoto($user, 'thumb.icon');

      $data[] = array(
          'id' => $moduleContent->review_id,
          'label' => $moduleContent->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE REVIEW OF THE ENTRY
  public function deleteDayItemAction() {

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE ITEM
        $itemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-settings/delete-day-item.tpl');
  }

  //ACTION FOR MULTI DELETE REVIEW OF THE DAY ENTRIES
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $sitestoreitemofthedays = Engine_Api::_()->getItem('sitestore_itemofthedays', (int) $value);
          if (!empty($sitestoreitemofthedays)) {
            $sitestoreitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }

}

?>