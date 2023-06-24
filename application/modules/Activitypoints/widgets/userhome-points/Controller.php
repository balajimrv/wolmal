<?php

class Activitypoints_Widget_UserhomePointsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    
    // just to be sure it's not public
    if(!$viewer->getIdentity()) {
      return false;
    }

    // level
    if(Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('activitypoints', $viewer->level_id, 'use') == 0) {
	  $this->setNoRender(true);
      return false;
    }

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

	$points_all = $api->getPoints($viewer->getIdentity());

    $this->view->user_points = $points_all ? $points_all['userpoints_count'] : 0;
    $this->view->user_points_totalearned = $points_all ? $points_all['userpoints_totalearned'] : 0;
    
    $this->view->userpoints_enable_topusers = Semods_Utils::getSetting('activitypoints.enable_topusers',0);
    $this->view->userpoints_enable_pointrank = Semods_Utils::getSetting('activitypoints.enable_pointrank',0);
    $this->view->user_rank = $api->getRank($viewer->getIdentity());
    
    $this->view->user_rank_title = Engine_Api::_()->getDbTable('pointranks','activitypoints')->getRank($viewer);
    $this->view->user_rank_next = Engine_Api::_()->getDbTable('pointranks','activitypoints')->getNextRank($viewer);
    
    $this->view->userpoints_enable_offers = Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards') && (Semods_Utils::getSetting('activityrewards.enable_offers',0) == 1);
    $this->view->userpoints_enable_shop = Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards') && (Semods_Utils::getSetting('activityrewards.enable_shop',0) == 1);

  }

  public function getCacheKey()
  {
    return false;
  }
}