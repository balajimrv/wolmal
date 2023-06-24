<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_IndexController extends Seaocore_Controller_Action_Standard {

  public function indexAction() {
    
  }

  public function homeAction() {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid())
        return;
    }

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $this->addResetContentTriggerEvent();
    // Render
    $this->_helper->content
            ->setNoRender()
            ->setEnabled()
    ;
  }
  
  public function friendsAction() {
    
      $viewer = $subject = Engine_Api::_()->user()->getViewer();
      if (empty($viewer) || !$viewer->getIdentity() || !Engine_Api::_()->sitemobile()->isApp()) {
        return false;
      }
       $isApp = Engine_Api::_()->sitemobile()->isApp();
      //When Suggestion Plugin Enabled.
      $isSuggestionEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'suggestion' );
      if(!empty($isSuggestionEnabled) && $isApp){ 
      Zend_Registry::set('sitemobileNavigationName', 'suggestion_main_app') ;
      }
      //Zend_Registry::set('setFixedCreationForm', true);
      //Zend_Registry::set('setFixedCreationFormBack', 'back');
      //Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Friends'));
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
      // Render
      if (!$this->_getParam('isappajax')) {
      $this->_helper->content
              // ->setNoRender()
              ->setEnabled()
      ;
      }
       

    // Don't render this if friendships are disabled
    if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
      return;
    }

    // Get subject and check auth
    
    if (!$subject->authorization()->isAllowed($viewer, 'view')) { 
      return;
    }
    $displayname = $this->_getParam('displayname'); 
    // Multiple friend mode
     $table = Engine_Api::_()->getItemTable('user');
     $userTableName = $table->info('name');
     //$table = Engine_Api::_()->getDbtable('users', 'user');
     $membershiptable = Engine_Api::_()->getDbtable('membership', 'user');
     $membershipTableName = $membershiptable->info('name');
     $select = $membershiptable->select()
            //->setIntegrityCheck(false)
            ->from($membershipTableName);
    //$select = $subject->membership()->getMembersOfSelect();
    if($displayname)
    $select->join($userTableName, "`{$userTableName}`.`user_id` = `{$membershipTableName}`.`resource_id`", null)
           ->where("(`{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    $select->where("{$membershipTableName}.user_id = ?", $viewer->getIdentity())
            ->where("{$membershipTableName}.active = ?", 1);
           
    $this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);
    $sitemobileProfileFriend = Zend_Registry::isRegistered('sitemobileProfileFriend') ?  Zend_Registry::get('sitemobileProfileFriend') : null;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 15));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalUsers) / 15); 
    $this->view->autoContentLoad = $this->_getParam('isappajax', 0) ? 0 :1;  
    if (($paginator->getTotalItemCount() <= 0) || empty($sitemobileProfileFriend)) {
      return;
    }
    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;
//      if (!$this->_executeSearch()) {
//        // throw new Exception('error');
//      }

      if ($this->_getParam('ajax')) {
        $this->renderScript('_browseFriends.tpl');
      }
  }

  public function browseAction() {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid())
        return;
    }
    $isApp = Engine_Api::_()->sitemobile()->isApp();
    //When Suggestion Plugin Enabled.
    $isSuggestionEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'suggestion' );
    if(!empty($isSuggestionEnabled) && !$isApp){ 
    Zend_Registry::set('sitemobileNavigationName', 'suggestion_main') ;
    }
//    if($isApp) { 
//      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
//      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
//        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
//      }
//      
//      Zend_Registry::set('setFixedCreationForm', true);
//      Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Browse Members'));
//    }
    // Render
    if (!$this->_getParam('isappajax')) {
    $this->_helper->content
            // ->setNoRender()
            ->setEnabled()
    ;
    }
    if (!$this->_executeSearch()) {
      // throw new Exception('error');
    }
    
    if ($this->_getParam('ajax')) {
      $this->renderScript('_browseUsers.tpl');
    }
  }

  protected function _executeSearch() {
    // Check form
    $form = new Sitemobile_modules_User_Form_Filter_Search(array(
                'type' => 'user'
            ));

    if (!$form->isValid($this->_getAllParams())) {
      $this->view->error = true;
      $this->view->totalUsers = 0;
      $this->view->userCount = 0;
      $this->view->page = 1;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int) $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $isappajax = (bool) $this->_getParam('isappajax', false);
    $options = $form->getValues();

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach ($options as $k => $v) {
      if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
        continue;
      } else if (false !== strpos($k, '_field_')) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if (false !== strpos($k, '_alias_')) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Contruct query
    $select = $table->select()
            //->setIntegrityCheck(false)
            ->from($userTableName)
            ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
            //->group("{$userTableName}.user_id")
            ->where("{$userTableName}.search = ?", 1)
            ->where("{$userTableName}.enabled = ?", 1)
            ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if (isset($has_photo) && !empty($has_photo)) {
      $select->where($userTableName . '.photo_id != ?', "0");
    }

    if (isset($is_online) && !empty($is_online)) {
      $select
              ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
              ->group("engine4_user_online.user_id")
              ->where($userTableName . '.user_id != ?', "0");
    }

    // Add displayname
    if (!empty($displayname)) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach ($searchParts as $k => $v) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(15);
    $paginator->setCurrentPageNumber($page);

    $this->view->page = $page;
    $this->view->autoContentLoad = $isappajax ? 0 :1;
    $this->view->ajax = $ajax;
    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalUsers) / 15);
    $this->view->userCount = $paginator->getCurrentItemCount();
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
    $this->view->formValues = array_filter($originalOptions);
    $this->view->clear_cache = true;
    return true;
  }

}