<?php

class Sesadvancedactivity_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract {

  public function execute() {
  
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query('DELETE from engine4_activity_stream WHERE action_id NOT IN (SELECT action_id FROM engine4_activity_actions);');
    
  }
}