<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminEditorsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminEditorsController extends Core_Controller_Action_Admin {

  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_admin_reviewmain', array(), 'sitestoreproduct_admin_reviewmain_editors');

    //GET PAGE
    $page = $this->_getParam('page', 1);

    //FILTER FORM
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();

    //GET EDITOR TABLE
    $this->view->tableEditor = $tableEditor = Engine_Api::_()->getDbtable('editors', 'sitestoreproduct');
    $tableEditorName = $tableEditor->info('name');

    $this->view->totalEditors = $tableEditor->getEditorsCount(0);

    //GET USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $tableUserName = $tableUser->info('name');

    //SELECTING THE USERS WHOSE PAGE CAN BE CLAIMED
    $select = $tableUser->select()
            ->setIntegrityCheck(false)
            ->from($tableEditorName, array('editor_id', 'designation', 'details', 'about', 'badge_id', 'super_editor'))
            ->join($tableUserName, $tableUserName . '.user_id = ' . $tableEditorName . '.user_id');

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //VALUES
    $values = array_merge(array(
        'order' => "$tableEditorName.user_id",
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    //SELECT
    $select->order((!empty($values['order']) ? $values['order'] : "$tableEditorName.user_id" ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $select->group($tableEditorName . '.user_id');

    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR ADDING THE EDITORS
  public function createAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_admin_reviewmain', array(), 'sitestoreproduct_admin_reviewmain_editors');

    //FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Editors_Create();

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET VALUES
      $values = $form->getValues();

      //GET USER ID
      $user_id = $values['user_id'];

      //GET EDITORS TABLE
      $tableEditor = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');

      $same_user_editor_id = $tableEditor->select()
              ->from($tableEditor->info('name'), 'editor_id')
              ->where('user_id = ?', $user_id)
              ->query()
              ->fetchColumn();

      //CHECK USER ID
      if ($user_id == 0 || !empty($same_user_editor_id)) {
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_('This is not a valid user name. Please select a user name from the auto-suggest.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $editor = $tableEditor->createRow();
        $editor->user_id = $user_id;
        $editor->designation = $values['designation'];
        $editor->details = $values['details'];
        $editor->email_notify = $values['email_notify'];
        $editor->save();

        //GET EDITOR DETAILS
        $getDetails = $tableEditor->getEditorDetails($editor->user_id);
        $getCount = Count($getDetails);
        $count = 0;
        $product_type = "";
        $Zend_router = Zend_Controller_Front::getInstance()->getRouter();
        $http = _ENGINE_SSL ? 'https://' : 'http://';

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $newEditor = Engine_Api::_()->getItem('user', $user_id);

        $host = $_SERVER['HTTP_HOST'];
        $editor_page_url = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $editor->getHref();
        $viewer_page_url = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $viewer->getHref();
        $viewer_fullhref = '<a href="' . $viewer_page_url . '">' . $viewer->getTitle() . '</a>';

        Engine_Api::_()->getApi('mail', 'core')->sendSystem($newEditor->email, 'SITESTOREPRODUCT_EDITOR_ASSIGN_EMAIL', array(
            'sender' => $viewer_fullhref,
            'editor_page_url' => $editor_page_url,
            'queue' => true
        ));

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('module' => 'sitestoreproduct', 'controller' => 'editors', 'action' => 'manage'), "admin_default", true);
    }
  }

  //ACTION FOR GETTING THE MEMBER
  function getMemberAction() {

    //GET SETTINGS
    $featured_editor = $this->_getParam('featured_editor', 0);
    $text = $this->_getParam('text');
    $limit = $this->_getParam('limit', 40);

    //FETCH USER LIST
    $userLists = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getMembers($text, $limit, $featured_editor);

    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($userLists as $userList) {
        $content_photo = $this->view->itemPhoto($userList, 'thumb.icon');
        $data[] = array('id' => $userList->user_id, 'label' => $userList->getTitle(), 'photo' => $content_photo);
      }
    } else {
      foreach ($userLists as $userList) {
        $content_photo = $this->view->itemPhoto($userList, 'thumb.icon');
        $data[] = array('id' => $userList->user_id, 'label' => $userList->getTitle(), 'photo' => $content_photo);
      }
    }
    return $this->_helper->json($data);
  }

  //ADD EDITORS DETAILS
  public function editAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET USER ID AND EDITOR
    $editor_id = $this->_getParam('editor_id');
    $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);

    //GENERATE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Editors_Edit(array('item' => $editor));
    $form->populate($editor->toArray());

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET VALUES
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //GET EDITOR TABLE
        $tableEditor = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');

        $tableEditor->update(array('email_notify' => $values['email_notify'], 'designation' => $values['designation'], 'details' => $values['details']), array('user_id = ?' => $editor->user_id));

          //IF EDITOR IS NOT EXIST
          $isExist = $tableEditor->isEditor($editor->user_id);
          if (empty($isExist)) {
            $editorNew = $tableEditor->createRow();
            $editorNew->user_id = $editor->user_id;
            $editorNew->designation = $values['designation'];
            $editorNew->details = $values['details'];
            $editorNew->email_notify = $values['email_notify'];
            $editorNew->save();
          }
          
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'))
      ));
    }

    $this->renderScript('admin-editors/edit.tpl');
  }

  //ACTION FOR MAKING THE EDITOR AS A SUPER EDITOR
  public function superEditorAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    $editor_id = $this->_getParam('editor_id');

    $this->view->super_editor = $this->_getParam('super_editor', 0);

    if ($this->getRequest()->isPost()) {

      $editorTable = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');
      $db = $editorTable->getAdapter();
      $db->beginTransaction();

      try {

        $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);

        $editorTable->update(array('super_editor' => 0), array('super_editor = ?' => 1));
        $editorTable->update(array('super_editor' => 1), array('user_id = ?' => $editor->user_id));

        //IF EDITOR IS NOT EXIST
        $isExist = $editorTable->isEditor($editor->user_id);
        if (empty($isExist)) {
          $editorNew = $editorTable->createRow();
          $editorNew->user_id = $editor->user_id;
          $editorNew->designation = $editor->designation;
          $editorNew->details = $editor->details;
          $editorNew->about = $editor->about;
          $editorNew->super_editor = 1;
          $editorNew->save();
        }
        
        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Super Editor has been made successfully.'))
      ));
    }

    $this->renderScript('admin-editors/super-editor.tpl');
  }

  //ACTION FOR REMOVING EDITORS
  public function deleteAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET EDTITOR ID AND DETAILS
    $this->view->editor_id = $editor_id = $this->_getParam('editor_id');
    $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);

    if ($editor->super_editor) {
      return;
    }

    //GET FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Editors_Map();

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $editorTable = Engine_Api::_()->getDbtable('editors', 'sitestoreproduct');
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
        $reviewTableName = $reviewTable->info('name');
        $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $productTableName = $productTable->info('name');

        foreach ($form->getValues() as $key => $value) {

            $select = $reviewTable->select()
                    ->setIntegrityCheck(false)
                    ->from($reviewTableName, 'review_id')
                    ->joinInner($productTableName, "$reviewTableName.resource_id = $productTableName.product_id", array())
                    ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
                    ->where($reviewTableName . '.type = ?', 'editor')
                    ->where($reviewTableName . '.owner_id = ?', $editor->user_id);
            ;
            $reviews = $reviewTable->fetchAll($select);

            if (!empty($value)) {
              foreach ($reviews as $review) {
                $reviewTable->update(array('owner_id' => $value), array('review_id = ?' => $review->review_id));
                Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct')->update(array('user_id' => $value), array('review_id = ?' => $review->review_id, 'type' => 'editor'));
              }
            } else {
              foreach ($reviews as $review) {
                Engine_Api::_()->getItem('sitestoreproduct_review', $review->review_id)->delete();
              }
            }
          
          $editorTable->delete(array('user_id = ?' => $editor->user_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Editor has been deleted successfully.'))
      ));
    }
    //OUTPUT
    $this->renderScript('admin-editors/delete.tpl');
  }

}
