<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: GroupController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_GroupController extends Seaocore_Controller_Action_Standard {

  public function init() {
    if (0 !== ($group_id = (int) $this->_getParam('group_id')) &&
            null !== ($group = Engine_Api::_()->getItem('group', $group_id))) {
      Engine_Api::_()->core()->setSubject($group);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('group');
  }

  public function editAction() {
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
      return;
    }

    $group = Engine_Api::_()->core()->getSubject();
    $officerList = $group->getOfficerList();
    $this->view->form = $form = new Group_Form_Edit();
    if (isset($form->photo))
      $form->photo->setAttrib('accept', "image/*");
    $form->addElement("dummy", "dummy", array('label' => 'Profile Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to create a Group from your mobile / tablet without uploading a profile photo for it. You can later upload the profile photo from your Desktop.', 'order' => 7, 'style' => 'display:none;'));

    $this->view->clear_cache = true;
    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    $form->category_id->setMultiOptions($categoryOptions);

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

     if (Engine_Api::_()->sitemobile()->isApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'form_group_edit');
      Zend_Registry::set('setFixedCreationFormId', '#form_group_edit');
      $this->view->form->removeElement('submit');
      $this->view->form->removeElement('cancel');
      $this->view->form->removeDisplayGroup('buttons');
      $form->setTitle('');
    }
    if (!$this->getRequest()->isPost()) {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('officer', 'member', 'registered', 'everyone');
      $actions = array('view', 'comment', 'invite', 'photo', 'event');
      $perms = array();
      foreach ($roles as $roleString) {
        $role = $roleString;
        if ($role === 'officer') {
          $role = $officerList;
        }
        foreach ($actions as $action) {
          if ($auth->isAllowed($group, $role, $action)) {
            $perms['auth_' . $action] = $roleString;
          }
        }
      }

      $form->populate($group->toArray());
      $form->populate($perms);
      return;
    }

    $tempPost = $this->getRequest()->getPost();
    if (isset($tempPost['photo'])) {       
       unset($tempPost['photo']);
      $form->removeElement('photo');      
    }
    if (!$form->isValid($tempPost)) {
      return;
    }
//    if (!$form->isValid($this->getRequest()->getPost())) {
//      return;
//    }

    // Process
    $db = Engine_Api::_()->getItemTable('group')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // Set group info
      $group->setFromArray($values);
      $group->save();

      if (!empty($values['photo'])) {
        $group->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('officer', 'member', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);
      $eventMax = array_search($values['auth_event'], $roles);
      $inviteMax = array_search($values['auth_invite'], $roles);

      foreach ($roles as $i => $role) {
        if ($role === 'officer') {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
        $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
      }

      // Create some auth stuff for all officers
      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
      $auth->setAllowed($group, $officerList, 'topic.edit', 1);

      // Add auth for invited users
      $auth->setAllowed($group, 'member_requested', 'view', 1);

      // Commit
      $db->commit();
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($group) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }



//     // Redirect
    if ($this->_getParam('ref') === 'profile') {
      $this->_redirectCustom($group);
    } else {
      $this->_redirectCustom(array('route' => 'group_general', 'action' => 'manage'));
    }
    // Redirect
//     if ($this->_getParam('ref') === 'profile') {
//       // Redirect to the post
//       // Try to get topic
//       return $this->_forward('success', 'utility', 'core', array(
//                   'redirect' => $group->getHref(),
//                   'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
//               ));
//     } else {
//       $this->_redirectCustom(array('route' => 'group_general', 'action' => 'manage'));
//       // Redirect to the post
//       // Try to get topic
// //      return $this->_forward('success', 'utility', 'core', array(
// //                  'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('route' => 'group_general', 'action' => 'manage')),
// //                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
// //              ));
//     }
  }

  public function deleteAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->getRequest()->getParam('group_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'delete')->isValid())
      return;

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    // Make form
    $this->view->form = $form = new Group_Form_Delete();
    $this->view->clear_cache = true;
    if (!$group) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Group doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $group->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'group_general', true),
                'messages' => Array($this->view->message)
            ));
  }

}