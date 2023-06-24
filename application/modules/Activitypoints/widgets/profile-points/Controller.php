<?php

class Activitypoints_Widget_ProfilePointsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // level
    if(Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('activitypoints', $subject->level_id, 'use') == 0) {
	  $this->setNoRender(true);
      return false;
    }

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

	$points_all = $api->getPoints($subject->getIdentity());

    $this->view->user_points_totalearned = $points_all['userpoints_totalearned'];

    $this->view->user_rank = $api->getRank($subject->getIdentity());
    $this->view->user_rank_title = Engine_Api::_()->getDbTable('pointranks','activitypoints')->getRank($subject);


    $this->view->userpoints_enable_pointrank = Semods_Utils::getSetting('activitypoints.enable_pointrank',0);
    $this->view->userpoints_enable_topusers = Semods_Utils::getSetting('activitypoints.enable_topusers',0);

  }

  public function getCacheKey()
  {
    return false;
  }
}