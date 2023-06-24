<?php

class Activitypoints_Widget_ListTopusersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

	// MAXIMUM TOP USERS TO DISPLAY
	$max_top_users = Semods_Utils::getSetting('activitypoints.widget_max_topusers',4);
	
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
	  $select->where('username NOT IN (?)', $topusers_exclude);	  
	}
	  
	$items = $userstable->fetchAll($select);
    
    if( count($items) < 1 )
    {
      return $this->setNoRender();
    }

	$this->view->items = $items;

  }

  public function getCacheKey()
  {
    return true;
  }

}