<?php

class Activitypoints_AdminGivepointsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    global $activitypoints_disable_hooks;
    $activitypoints_disable_hooks = true;
    
    $action_group_types = Activitypoints_Api_Core::$action_group_types;
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_givepoints');
      
    $api = Engine_Api::_()->getApi('core', 'activitypoints');
    $session = new Zend_Session_Namespace('Activitypoints_Givepoints');
    $r = $this->getRequest();
    


    $task = $r->get('task');
    empty($task) ? $task = 'main' :0;
    
    if($task == 'main') {
      $session->subject = $this->view->translate(100016623);
      $session->message = $this->view->translate(100016624);
    }
    
    $this->view->sent = $r->get('sent');

    $subject = $r->get('subject');
    $message = $r->get('message');
    
    empty($subject) ? $subject = $session->subject :0;
    empty($message) ? $message = $session->message :0;

    $page = $r->get('page',0);

    
    $sendto_type = (int)$r->get('sendtotype',0);
    $level_id = (int)$r->get('level',0);
    $subnet_id = (int)$r->get('subnet',0);
    $username = $r->get('username',0);
    $send_message = (bool)$r->get('send_message',0);
    $set_points = (int)$r->get('set_points',0);
    
    $is_error = 0;
    $error_message = '';
    $result = 0;
    
    $points = (int)$r->get('points',0);
    
    // users per batch
    $items_per_page = 25;
    
    // TBD: save suggested from_user_id in settings, setting_userpoints_admin_from_user_id
    $from_user_id_suggest = $r->get('from_user_id_suggest','');
    $from_user_id = $from_user_id_suggest != 0 ? $from_user_id_suggest : $r->get('from_user_id','');


    $table_points = Engine_Api::_()->getDbTable('points','activitypoints');
    $table_points_name = $table_points->info('name');
    $table_users = Engine_Api::_()->getDbTable('users','user');
    $table_users_name = $table_users->info('name');
    $table_networks = Engine_Api::_()->getDbTable('networks','network');
    $table_networks_name = $table_networks->info('name');
    
    if(($task == "dogivepoints") ||($task == "dogivepointspaged")) {

      $session->subject = $subject;
      $session->message = $message;
    
    
    
      $admin_user = Engine_Api::_()->user()->getUser($from_user_id);

      if($send_message && !$admin_user->getIdentity()) {
        $is_error = 1;
        $error_message = 100016625; // TODO -> Message author user doesn't exist
      } else {
        
        $select = null;
    
        switch($sendto_type) {
    
          // All users ..
          // would be great to just inc all pointscoint, but there are some that have no rows
          case 0:
            
            $select = $table_users->select()
                  ->where("enabled = 1")
                  ->where("verified = 1")
                  ->limit($items_per_page, $page * $items_per_page);
    
            break;
    
    
    
          // All users on level..
          case 1:

            $select = $table_users->select()
                  ->where("enabled = 1")
                  ->where("verified = 1")
                  ->where("level_id = ?", $level_id)
                  ->limit($items_per_page, $page * $items_per_page);
    
            break;
    
    
    
          // All users on subnet..
          case 2:

            $select = $table_users->select()
              ->setIntegrityCheck(false)
              ->from($table_users_name)
              ->join($table_networks_name, "`{$table_users_name}`.`user_id` = `{$table_networks_name}`.`user_id`")
              ->where("`{$table_networks_name}`.resource_id = ?",$subnet_id)
              ->where("enabled = 1")
              ->where("verified = 1")
              ->where("resource_approved = 1")  // ?
              ->where("user_approved = 1")  // ?
              ->where("active = 1")  // ?
              ->limit($items_per_page, $page * $items_per_page);
    
            break;
    
    
    
          // Specific user
          case 3:

            $happy_user = Engine_Api::_()->user()->getUser($username);

            if(!$happy_user->getIdentity()) {
              $is_error = 1;
              $error_message = 100016625;
            } else {
    
              if($set_points) {
                $api->setPoints( $happy_user->getIdentity(), $points );
              } else {
                $api->addPoints( $happy_user->getIdentity(), $points );
              }
              
              if($send_message && ($happy_user->getIdentity() != $admin_user->getIdentity())) {
                
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                  $admin_user,
                  array( $happy_user->getIdentity() ),
                  $subject,
                  $message
                );

                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $happy_user,
                  $admin_user,
                  $conversation,
                  'message_new'
                );
                
              }
    
            }
            break;
    
        }
        
        if($select) {

          $rows = $table_users->fetchAll($select);
          foreach($rows as $row) {
            
            if($set_points) {
              $api->setPoints( $row->user_id, $points );
            } else {
              $api->addPoints( $row->user_id, $points );
            }

            if($send_message && ($row->user_id != $admin_user->getIdentity())) {
              
              $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $admin_user,
                array( $row->user_id ),
                $subject,
                $message
              );

              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $row,
                $admin_user,
                $conversation,
                'message_new'
              );
              
            }
              
          }
          
        }
        
    
      }
    
      if($is_error == 0) {
        
        // something was processed
        if(!empty($select) && (count($rows) > 0)) {

          // redirect to next page
          return $this->_helper->_redirector->gotoRoute(array('action'        => 'index',
                                                              'module'        => 'activitypoints',
                                                              'controller'    => 'givepoints',
                                                              'sendtotype'    => $sendto_type,
                                                              'level_id'      => $level_id,
                                                              'subnet_id'     => $subnet_id,
                                                              'username'      => $username,
                                                              'send_message'  => $send_message,
                                                              'set_points'    => $set_points,
                                                              'points'        => $points,
                                                              'page'          => $page + 1,
                                                              'task'          => 'dogivepoints',
                                                              'from_user_id_suggest' => $from_user_id_suggest,
                                                              'from_user_id'    =>  $from_user_id
                                                             ),
                                                        'admin_default', true
                                                        );
          
        } else {

//echo $page;exit;
          return $this->_helper->_redirector->gotoRoute(array('action' => 'index',
                                                              'module'  => 'activitypoints',
                                                              'controller'  => 'givepoints',
                                                              'sent'  => 1
                                                             ),
                                                        'admin_default', true
                                                        );
          
        }


        $result = 1;
      }
    
    
    }
    
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	$levels = $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC'));

    // @tbd - only enabled
    $subnets = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
    
    
    // GET FIRST USERS - TBD - need user with message sending perms ?
    
    $from_users_suggest = $table_users->fetchAll( $table_users->select()->where('enabled = 1')->where('verified = 1')->order('user_id ASC')->limit(20) );
    if(!$from_users_suggest) {
      $from_users_suggest = array();
    }
    
    
    $this->view->from_users_suggest = $from_users_suggest;
    $this->view->from_user_id_suggest = $from_user_id_suggest;
    $this->view->from_user_id = $from_user_id;
    
    $this->view->levels = $levels;
    $this->view->subnets = $subnets;
    
    $this->view->sendtotype = $sendto_type;
    $this->view->level = $level_id;
    $this->view->subnet = $subnet_id;
    $this->view->username = $username;
    
    $this->view->subject = $subject;
    $this->view->message = $message;
    $this->view->send_message = $send_message;
    $this->view->set_points = $set_points;
    $this->view->points = $points;
    
    $this->view->is_error = $is_error;
    $this->view->error_message = $error_message;
    $this->view->result = $result;
      
  }


}