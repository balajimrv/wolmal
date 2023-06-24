<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_IndexController extends Core_Controller_Action_Standard {

  public function init() {

    if (!$this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid())
      return;

    $id = $this->_getParam('group_id', $this->_getParam('id', null));
    if ($id) {
      $group = Engine_Api::_()->getItem('group', $id);
      if ($group) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
  }

  public function browseAction() {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');

    // Form
    $this->view->formFilter = $formFilter = new Sitemobile_modules_Group_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if (!$viewer || !$viewer->getIdentity()) {
      $formFilter->removeElement('view');
    }

    // Populate options
    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    $formFilter->category_id->addMultiOptions($categories);

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if ($viewer->getIdentity() && @$values['view'] == 1) {
      $values['users'] = array();
      foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if ($user_id) {
      $values['user_id'] = $user_id;
    }


    // Make paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group')
            ->getGroupPaginator($values);

    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(9);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0); 
    $this->view->totalGroups = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalGroups) /9);
    // Render
    if(!$isappajax) 
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
  }

  public function createAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('group', null, 'create')->isValid())
      return;

    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;

    // Create form
    $this->view->form = $form = new Group_Form_Create();

    $form->addElement("dummy", "dummy", array('label'=> 'Profile Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to create a Group from your mobile / tablet without uploading a profile photo for it. You can later upload the profile photo from your Desktop.', 'order' => 3, 'style' => 'display:none;'));

    if(isset($form->photo))
    $form->photo->setAttrib('accept', "image/*");

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
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Create');
      $this->view->form->setAttrib('id', 'form_group_creation');
      Zend_Registry::set('setFixedCreationFormId', '#form_group_creation');
      $this->view->form->removeElement('submit');
      $this->view->form->removeElement('cancel');
      $this->view->form->removeDisplayGroup('buttons');
      $form->setTitle('');
    }
    // Check method/data validitiy
    if (!$this->getRequest()->isPost()) {
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

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();

    $db = Engine_Api::_()->getDbtable('groups', 'group')->getAdapter();
    $db->beginTransaction();

    try {
      // Create group
      $table = Engine_Api::_()->getDbtable('groups', 'group');
      $group = $table->createRow();
      $group->setFromArray($values);
      $group->save();

      // Add owner as member
      $group->membership()->addMember($viewer)
              ->setUserApproved($viewer)
              ->setResourceApproved($viewer);

      // Set photo
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

      $officerList = $group->getOfficerList();

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

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $group, 'group_create');
      if ($action) {
        $activityApi->attachActivity($action, $group);
      }

      // Commit
      $db->commit();

      // Redirect
     // return $this->_helper->redirector->gotoRoute(array('id' => $group->getIdentity()), 'group_profile', true);
       return $this->_forward('success', 'utility', 'core', array(
                   'redirect' => $this->_helper->url->url(array('id' => $group->getIdentity()), 'group_profile', true),
                   'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group has been created successfully.')),
               ));
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function listAction() {
    
  }

  public function manageAction() {
   $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0);
   if(!$isappajax)
    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;

    // Form
    $this->view->formFilter = $formFilter = new Sitemobile_modules_Group_Form_Filter_Manage();
    $this->view->formValues = $values = $formFilter->getValues();

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }
    $this->view->clear_cache = true;
    $viewer = Engine_Api::_()->user()->getViewer();
    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    $select = $membership->getMembershipsOfSelect($viewer);
    $select->where('group_id IS NOT NULL');

    $table = Engine_Api::_()->getItemTable('group');
    $tName = $table->info('name');
    if ($values['view'] == 2) {
      $select->where("`{$tName}`.`user_id` = ?", $viewer->getIdentity());
    }
    if (!empty($values['text'])) {
      $select->where(
              $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $values['text'] . '%') . ' OR ' .
              $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $values['text'] . '%')
      );
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->text = $values['text'];
    $this->view->view = $values['view'];
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(10);
    $this->view->page = $this->_getParam('page', 1);     
    $this->view->totalGroups = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalGroups) /10);
   
    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');
  }

}