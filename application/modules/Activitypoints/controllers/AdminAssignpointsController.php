<?php

class Activitypoints_AdminAssignpointsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $action_group_types = Activitypoints_Api_Core::$action_group_types;
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_assignpoints');
    
    $result = 0;
    $task = $this->getRequest()->getPost('task','');

    $table_actionpoints = Engine_Api::_()->getDbTable('actionpoints','activitypoints');
    
    if($task == "dosave") {
      
      $actions = $this->getRequest()->getPost('actions');
      $actionsmax = $this->getRequest()->getPost('actionsmax');
      $actionsrollover = $this->getRequest()->getPost('actionsrollover');
      $actionsname = $this->getRequest()->getPost('actionsname');
    
      foreach($actions as $key => $value){
    
        // days -> seconds
        $rollover_period = intval($actionsrollover[$key]) * 86400;
        
        // new, previously unknown actiontype
        if( intval($key) == 0 ) {
          
          $values = array('action_type'           => $key,
                          'action_name'           => $actionsname[$key],
                          'action_points'         => intval($value),
                          'action_pointsmax'      => intval($actionsmax[$key]),
                          'action_rolloverperiod' => $rollover_period
                         );

          $table_actionpoints->insert($values);
          

        } else {

          
          // unknown item, changing name
          if(isset($actionsname[$key])) {

            $values = array(
                            'action_name'           => $actionsname[$key],
                            'action_points'         => intval($value),
                            'action_pointsmax'      => intval($actionsmax[$key]),
                            'action_rolloverperiod' => $rollover_period
                           );
  
            $table_actionpoints->update($values, array('action_id = ?'  => intval($key)) );
            

          } else {

            $values = array(
                            'action_points'         => intval($value),
                            'action_pointsmax'      => intval($actionsmax[$key]),
                            'action_rolloverperiod' => $rollover_period
                           );
  
            $table_actionpoints->update($values, array('action_id = ?'  => intval($key)) );
          }
        }
        
      }
    
      $result = 1;
    
    }

    
    $table_actiontypes = Engine_Api::_()->getDbTable('actionTypes','activity');
    $table_actionpoints = Engine_Api::_()->getDbTable('actionpoints','activitypoints');

    $sql =  "SELECT A.type,
                    P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod, P.action_module, P.action_custom 
             FROM `{$table_actiontypes->info('name')}` A
             LEFT JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             UNION SELECT A.type, P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod, P.action_module, P.action_custom
             FROM `{$table_actiontypes->info('name')}` A
             RIGHT JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             WHERE P.action_group >= 0
             ORDER BY action_group DESC, action_id";

    $db = $table_actiontypes->getAdapter();
    
    //echo $sql;exit;
    $statement = $db->query($sql);

    $actions = array();
    $action_types = array();
    $action_group_previd = -1;
    $action_group_id = -1;
    
    $ignore_actions = array('forum_topic_reply','forum_topic_create');
    
    while($row = $statement->fetch()) {
      
      if(in_array($row['action_name'],$ignore_actions)) {
        continue;
      }

      if(($row['action_custom'] == 1) && Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled($row['action_module'])) {
        $row['action_requiredplugin'] = null;
      }

    
      $action_group_id = $row['action_group'];
      if($action_group_id != $action_group_previd) {
        if($action_group_previd != -1) {
          $actions[] = $action_group;
          $action_types[] = $action_group_types[intval($action_group_previd)];
          $action_group = array();
        } else {
          
        }
        $action_group_previd = $action_group_id;
      }
    
      // seconds -> days
      $row['action_rolloverperiod'] = $row['action_rolloverperiod'] / 86400; 
    
      $action_group[] = $row;
    
    }
    
    if(!empty($action_group)) {
      $actions[] = $action_group;
      $action_types[] = $action_group_types[intval($action_group_previd)];
    }
    
    //echo "<pre>";
    //print_r($actions);
    //exit;
    
    $this->view->actions = $actions;
    $this->view->action_types = $action_types;
    
    $this->result = $result;
    $this->error = 0;
    
  }


}