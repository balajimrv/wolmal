<?php

class Activitypoints_TopusersController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

	// Enable Top Users
	if(Semods_Utils::getSetting('activitypoints.enable_topusers',0) == 0) {
      return $this->_helper->_redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
	}

	// Allow public access
	if(Semods_Utils::getSetting('activitypoints.access_topusers',1) == 0) {
	  $this->_helper->requireUser()->isValid();
      //return $this->_helper->_redirector->gotoRoute(array(), 'core_home', true);
	}
	
	if(Engine_Api::_()->user()->getViewer()->getIdentity()) {
	  $this->setupNavigation('activitypoints_topusers');
	}
	
	// MAXIMUM TOP USERS TO DISPLAY
	$max_top_users = Semods_Utils::getSetting('activitypoints.max_topusers',10);
	
	$ranking_base = Semods_Utils::getSetting('activitypoints.topusers_rankby',0);
	$ranking_base = ($ranking_base == 0) ? 'userpoints_totalearned' : 'userpoints_count';
	
	$pointstable = Engine_Api::_()->getDbtable('points', 'activitypoints');
	$pointstable_name = $pointstable->info('name');
	$userstable = Engine_Api::_()->getDbtable('users', 'user');
	$userstable_name = $userstable->info('name');
	
	$select = $userstable->select()
      ->setIntegrityCheck(false)
      ->from($userstable)
      ->join($pointstable_name, "`{$userstable_name}`.`user_id` = `{$pointstable_name}`.`userpoints_user_id`", "$ranking_base as points")
	  ->where('userpoints_totalearned != 0')
	  ->where('userpoints_count != 0')
	  ->order("$ranking_base DESC")
	  ->limit($max_top_users);

    $topusers_exclude = Semods_Utils::getSetting('activitypoints.topusers_exclude','');
    $topusers_exclude = empty($topusers_exclude) ? array() : explode(',',$topusers_exclude);
	if(!empty($topusers_exclude)) {
	  $select->where('(username NOT IN (?) OR username IS NULL)', $topusers_exclude);	  
	}

	$items = $userstable->fetchAll($select);


	$this->view->items = $items;

  }



  public function setupNavigation($active_menu) {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_main', array(), $active_menu);


  }

}