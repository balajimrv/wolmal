<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_IndexController extends Core_Controller_Action_Standard {

  public function init() {
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid())
      return;

    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if ($id) {
      $event = Engine_Api::_()->getItem('event', $id);
      if ($event) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }

  public function browseAction() {
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    $filter = $this->_getParam('filter', 'future');
    if ($filter != 'past' && $filter != 'future')
      $filter = 'future';
    $this->view->filter = $filter;

    if ($filter == "future") {
      $this->view->sitemapPageHeaderTitle = $this->view->translate("Upcoming Events");
    } else {
      $this->view->sitemapPageHeaderTitle = $this->view->translate("Past Events");
    }
    Zend_Registry::set('sitemapPageHeaderTitle', $this->view->sitemapPageHeaderTitle);
    // Create form
    $this->view->formFilter = $formFilter = new Sitemobile_modules_Event_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if (!$viewer || !$viewer->getIdentity()) {
      $formFilter->removeElement('view');
    }

    // Populate options
    foreach (Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row) {
      $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
    }
    if (count($formFilter->category_id->getMultiOptions()) <= 1) {
      $formFilter->removeElement('category_id');
    }

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

    if ($filter == "past") {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

    // check to see if request is for specific user's listings
    if (($user_id = $this->_getParam('user'))) {
      $values['user_id'] = $user_id;
    }


    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('event')
            ->getEventPaginator($values);
    $paginator->setItemCountPerPage(9);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->sitemobile()->isApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalEvents = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalEvents) /9);
    //END - SCROLLING WORK
    
    // Render
    if(!$isappajax) 
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
  }

  public function manageAction() {
    // Create form
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid())
      return;

    $this->view->formFilter = $formFilter = new Sitemobile_modules_Event_Form_Filter_Manage();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('events', 'event');
    $tableName = $table->info('name');

    // Only mine
    if (@$values['view'] == 2) {
      $select = $table->select()
              ->where('user_id = ?', $viewer->getIdentity());
    }
    // All membership
    else {
      $membership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $membership->getMembershipsOfSelect($viewer);
    }

    if (!empty($values['text'])) {
      $select->where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
    }

    $select->order('starttime ASC');
    //$select->where("endtime > FROM_UNIXTIME(?)", time());
    $this->view->clear_cache = true;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->text = $values['text'];
    $this->view->view = $values['view'];
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
    
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->sitemobile()->isApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
//    $this->view->searchParams = $formFilter->getValues();
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalEvents = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalEvents) /10);
    //END - SCROLLING WORK
    
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
    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid())
      return;

   if (!Engine_Api::_()->sitemobile()->isApp()) {  
    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
   }

    $viewer = Engine_Api::_()->user()->getViewer();
    $parent_type = $this->_getParam('parent_type');
    $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));
    $this->view->clear_cache = true;
    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
      $this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
      if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid()) {
        return;
      }
    } else {
      $parent_type = 'user';
      $parent_id = $viewer->getIdentity();
    }

    // Create form
    $this->view->parent_type = $parent_type;
    $this->view->form = $form = new Event_Form_Create(array(
                'parent_type' => $parent_type,
                'parent_id' => $parent_id
            ));

    $form->addElement("dummy", "dummy", array('label' => 'Main Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to create an Event from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop.', 'order' => 7, 'style' => 'display:none;'));

    if (isset($form->photo))
      $form->photo->setAttrib('accept', "image/*");

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }
 
  if (Engine_Api::_()->sitemobile()->isApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'form_event_creation');
      $this->view->form->getElement('photo')->setLabel('Choose Photo');
      Zend_Registry::set('setFixedCreationFormId', '#form_event_creation');
      $this->view->form->removeElement('submit');
      $this->view->form->removeElement('cancel');
      $this->view->form->removeDisplayGroup('buttons');
      $form->setTitle('');
    }
    // Not post/invalid
    if (!$this->getRequest()->isPost()) {
      return;
    }
      $tempPost = $this->getRequest()->getPost();
      if (isset($tempPost['photo']))
        $form->removeElement('photo');

    $this->view->clear_cache = true;
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }


    // Process
    $values = $form->getValues();

    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;
    $values['parent_id'] = $parent_id;
    if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host'])) {
      $values['host'] = $group->getTitle();
    }

    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
    $db->beginTransaction();

    try {
      // Create event
      $table = Engine_Api::_()->getDbtable('events', 'event');
      $event = $table->createRow();

      $event->setFromArray($values);
      $event->save();

      // Add owner as member
      $event->membership()->addMember($viewer)
              ->setUserApproved($viewer)
              ->setResourceApproved($viewer);

      // Add owner rsvp
      $event->membership()
              ->getMemberInfo($viewer)
              ->setFromArray(array('rsvp' => 2))
              ->save();

      // Add photo
      if (!empty($values['photo']) && $form->photo) {
        $event->setPhoto($form->photo);
      }

      // Set auth
      $auth = Engine_Api::_()->authorization()->context;

      if ($values['parent_type'] == 'group') {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Add an entry for member_requested
      $auth->setAllowed($event, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $action = $activityApi->addActivity($viewer, $event, 'event_create');

      if ($action) {
        $activityApi->attachActivity($action, $event);
      }
      // Commit
      $db->commit();

      // Redirect
      // return $this->_helper->redirector->gotoRoute(array('id' => $event->getIdentity()), 'event_profile', true);
      
    return $this->_forward('success', 'utility', 'core', array(
            'redirect' => $this->_helper->url->url(array('id' => $event->getIdentity()), 'event_profile', true),
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Event has been created successfully.')),
        ));
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

}
