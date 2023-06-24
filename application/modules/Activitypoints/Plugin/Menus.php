<?php

class Activitypoints_Plugin_Menus
{
  
  public function onMenuInitialize_CoreMainPointsTopusers() {

    if( Semods_Utils::getSetting('activitypoints.enable_topusers',0) == 0 )
    {
      return false;
    }

    return true;
  }
  
  public function onMenuInitialize_ActivitypointsTopusers() {

    if( Semods_Utils::getSetting('activitypoints.enable_topusers',0) == 0 )
    {
      return false;
    }

    return true;
    
  }

  // user_profile

  public function onMenuInitialize_UserProfileSendpoints($row)
  {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) )
    {
      return false;
    }

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    // viewer can't send points
    if(($permissionsTable->getAllowed('activitypoints', $viewer->level_id, 'use') == 0) || ($permissionsTable->getAllowed('activitypoints', $viewer->level_id, 'allow_transfer') == 0)) {
      return false;
    }

    // sender can't receive points
    if($permissionsTable->getAllowed('activitypoints', $subject->level_id, 'use') == 0) {
      return false;
    }
    
    return array(
      'label' => $row->label,
      'icon' => 'application/modules/Activitypoints/externals/images/userpoints_coins16.png',
      'route' => 'activitypoints_vault',
      'params' => array(
        'to' => $subject->getIdentity()
      ),
    );
  }


  // user_home

  //public function onMenuInitialize_UserHomePoints($row)
  //{
  //  $viewer = Engine_Api::_()->user()->getViewer();
  //  if( $viewer->getIdentity() )
  //  {
  //    return array(
  //      'label' => $row->label,
  //      'icon' => $row->params['icon'],
  //      'route' => 'activitypoints_vault',
  //    );
  //  }
  //  return false;
  //}

}