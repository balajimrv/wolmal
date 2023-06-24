<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Jobs.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_Plugin_Task_Jobs extends Core_Plugin_Task_Abstract {

  public function execute() {
  
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('actions','sesadvancedactivity');
    $tableName = $table->info('name');
    $select = $table->select()->from($tableName)->where('schedule_time IS NOT NULL AND schedule_time != ""')->where('schedule_time <= ?',date('Y-m-d H:i:s'));
    $db = Engine_Db_Table::getDefaultAdapter();
    $results = $table->fetchAll($select);
    foreach($results as $result) {
      $table->resetActivityBindings($result);
      $db->query("CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM `engine4_activity_actions` WHERE action_id = ".$result->getIdentity());
      $db->query("UPDATE tmptable_1 SET action_id = NULL;");
      $db->query("INSERT INTO `engine4_activity_actions` SELECT * FROM tmptable_1;");
      $action_id = $db->lastInsertId();
      $db->query("DROP TEMPORARY TABLE IF EXISTS tmptable_1;");
      
      if(!$action_id)
        continue;
        
      $action = Engine_Api::_()->getItem('sesadvancedactivity_action',$action_id);
      
      //Notification Work
      $item = Engine_Api::_()->getItem('user', $action->subject_id);
      if($item) {
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedactivity_scheduled_live');
      }
      
      $db->query("UPDATE engine4_sesbasic_locations SET resource_id = ".$action_id ." WHERE resource_type = 'activity_action' AND resource_id =".$result->getIdentity());
      $db->query("UPDATE engine4_activity_stream SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $db->query("UPDATE engine4_activity_attachments SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $db->query("UPDATE engine4_sesadvancedactivity_buysells SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $db->query("UPDATE engine4_sesadvancedactivity_hashtags SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $db->query("UPDATE engine4_sesadvancedactivity_tagusers SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $db->query("UPDATE engine4_sesadvancedactivity_targetpost SET action_id = ".$action_id ." WHERE action_id =".$result->getIdentity());
      $action->date = date('Y-m-d H:i:s');
      $action->schedule_time = '';
      $action->save();
      $result->delete();
    }  
  }
}