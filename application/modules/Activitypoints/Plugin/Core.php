<?php

class Activitypoints_Plugin_Core
{

  public function onSemodsAddActivity($event)
  {

    $payload = $event->getPayload();

    
    $user = $payload['subject'];
    $actiontype_name = $payload['type'];

    $api = Engine_Api::_()->getApi('core', 'activitypoints');
  
    // TBD - special treatments,
    $excluded_actions = array();
    
    $amount = 1;
    
    // amount = number of photos
    if($actiontype_name == 'album_photo_new') {
      $amount = $payload['params']['count'];
    }

    if(!in_array($actiontype_name, $excluded_actions)) {
       $api->updatePoints( $user->getIdentity(), $actiontype_name, $amount );
    }
    
  }
  
  //SES Custom work
  public function addActivity($event)
  {
      $this->onSemodsAddActivity($event);
  }
  //SES Custom work
  
  
  public function onFriendsinviterRefer($event) {
    
    $payload = $event->getPayload();

    $referrer = $payload['referrer'];
    $new_user = $payload['new_user'];
    
    Engine_Api::_()->activitypoints()->updatePoints( $referrer->getIdentity(), "refer" );
    
  }

  public function onFriendsinviterStats($event) {
    
    $payload = $event->getPayload();
    
    $user_id = isset($payload['user']) ? $payload['user']->getIdentity() : $payload['user_id'];
    $invites_count = $payload['invites_count'];
    
    Engine_Api::_()->activitypoints()->updatePoints( $user_id, "invite", $invites_count );
    
  }
  

  public function onAlbumCreateAfter($event)
  {

    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'newalbum';

    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }

  public function onCoreLikeCreateAfter($event)
  {

    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'like';

    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }
  

  public function onForumTopicCreateAfter($event)
  {

    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'forum_topic';

    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }

  public function onForumPostCreateAfter($event)
  {

    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'forum_post';

    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }
  

  public function onMessagesMessageCreateAfter($event)
  {

    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'message';

    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }

  public function onMusicSongCreateAfter($event)
  {
  
    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'newmusic';
  
    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }

  public function onActivityCommentCreateAfter($event)
  {
  
    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'comment_activity';
  
    Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
    
  }
  

  public function onFieldsValuesSave($event)
  {

    $payload = $event->getPayload();
    
    $item = $payload['item'];
    $values = $payload['values'];
    
    $user = Engine_Api::_()->user()->getViewer();
    $actiontype_name = 'fields_change_generic';


    if( ($item instanceof User_Model_User) && ($user->getIdentity() == $item->getIdentity()) ) {

      Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
      
    }

    
  }

  
  

  //public function onVideoRatingCreateAfter($event)
  //{
  //
  //  $user = Engine_Api::_()->user()->getViewer();
  //  $actiontype_name = 'video_rate';
  //
  //  Engine_Api::_()->getApi('core', 'activitypoints')->updatePoints( $user->getIdentity(), $actiontype_name );
  //  
  //}

  
  public function onUserDeleteBefore($event)
  {
  
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {

      // Remove counters
      Engine_Api::_()->getDbtable('counters', 'activitypoints')->delete( array('userpointcounters_user_id = ?' => $payload->getIdentity() ) );
    
      // Remove transactions
      Engine_Api::_()->getDbtable('transactions', 'activitypoints')->delete( array('uptransaction_user_id = ?' => $payload->getIdentity() ) );
    
      // Remove user points
      Engine_Api::_()->getDbtable('points', 'activitypoints')->delete( array('userpoints_user_id = ?' => $payload->getIdentity() ) );
      
    }
  }
  
}