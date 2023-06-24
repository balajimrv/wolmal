<?php

class Activitypoints_AdminPointranksController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_pointranks');
      
    $api = Engine_Api::_()->getApi('core', 'activitypoints');
    $r = $this->getRequest();

    $task = $r->get('task', 'main');

    $result = 0;
    $error = 0;
    
    if($task == "dosave") {
    
      $setting_userpoints_enable_pointrank = (int)$r->get('setting_userpoints_enable_pointrank', 0);
      $setting_userpoints_ranktype = (int)$r->get('setting_userpoints_ranktype', 0);
      
      Semods_Utils::setSetting('activitypoints.enable_pointrank', $setting_userpoints_enable_pointrank);
      Semods_Utils::setSetting('activitypoints.ranktype', $setting_userpoints_ranktype);
      
      $point_rank_points = $r->get('point_rank_points');
      $point_rank_text = $r->get('point_rank_text');
      
      $ranks = array();
      
      foreach($point_rank_points as $key => $value ) {
        if(($value !== '')) {
          $ranks[$value] = $point_rank_text[$key];
        }
      }
      
      Engine_Api::_()->getDbTable('pointranks', 'activitypoints')->setRanks($ranks);
      
      $result = 1;
    
    }
    
    $point_ranks = Engine_Api::_()->getDbTable('pointranks', 'activitypoints')->fetchAll();
    
    $point_ranks_count = count($point_ranks);
    
    $this->view->result = $result;
    $this->view->error = $error;
    
    $this->view->point_ranks = $point_ranks;
    $this->view->point_ranks_count = $point_ranks_count;
    $this->view->setting_userpoints_enable_pointrank = Semods_Utils::getSetting('activitypoints.enable_pointrank',0);
    $this->view->setting_userpoints_ranktype = Semods_Utils::getSetting('activitypoints.ranktype',0);
    
    
    
      
  }


}