<?php
class Activitypoints_Model_DbTable_Counters extends Engine_Db_Table
{
  
  public static $ADDING_SUCCESS = 0;
  public static $ADDING_MORE_THAN_MAX = 1;
  public static $ADDING_PARTIAL = 2;
  public static $ADDING_LIMIT_REACHED = 3;
  public static $ADDING_NO_ACTION_DATA = 4;
  public static $ADDING_NO_ACTION_TAKEN = 5;
  
  
  protected $_name = 'semods_userpointcounters';
  
  public $_partial_amount;
  public $_points_to_add;
  public $_action_name;
  
  
  public function get($user_id = 0) {

    if($user_id == 0) {

      $result = $this->fetchAll();

    } else {
      
      $result = $this->fetchAll(array("userpointcounters_user_id = ?"  => $user_id));
      
    }
    
    return $result ? $result : array();
    
  }
  
  public function quotas($params) {
    
    $type = $params['type'];
    $user_id = $params['user_id'];
    $amount = $params['amount'];
    $update_total_earned = Semods_Utils::g($params,'update_total_earned',true);
    $allow_partial = Semods_Utils::g($params,'allow_partial',true);
    
    $action_data = Engine_Api::_()->getDbTable('actionpoints', 'activitypoints')->get($type, $user_id);

    // NO POINTS AWARDED
    if( $action_data && ($action_data['action_points'] == 0) && (Semods_Utils::g($params,'action_points',0) == 0) && (Semods_Utils::g($params,'points_to_add',0) == 0)) {
      return self::$ADDING_NO_ACTION_TAKEN;
    }

    // THIS USER HAS NO RECORD OF THIS ACTIVITY, FETCH ACTIVITY DATA
    if(!$action_data) {
      $action_data = Engine_Api::_()->getDbTable('actionpoints', 'activitypoints')->get($type);
    }

    // check if not reached max points / rollover date
    // if action_pointsmax is 0 - ignore
    // otherwise if empty userpointcounters_lastrollover or userpointcounters_lastrollover + rollover_period >= current_time  ==> rollover and assign amount
    // otherwise if userpointcounters_amount + amount > action_pointsmax ==> STOP

    $sql = '';
    $table = $this;
    $db = $table->getAdapter();
    $tableName = $table->info("name");


    if(!$action_data) {
      return self::$ADDING_NO_ACTION_DATA;
    }

    isset($params['action_rolloverperiod']) ? $action_data['action_rolloverperiod'] = $params['action_rolloverperiod'] : 0;
    isset($params['action_pointsmax']) ? $action_data['action_pointsmax'] = $params['action_pointsmax'] : 0;
    isset($params['action_points']) ? $action_data['action_points'] = $params['action_points'] : 0;

    $points_to_add = isset($params['points_to_add']) ? $params['points_to_add'] : $amount * $action_data['action_points'];

    if(($action_data['action_points'] == 0) && ($points_to_add == 0)) {
      return self::$ADDING_NO_ACTION_TAKEN;
    }

    $now = time();

    
    // No max limit
    if($action_data['action_pointsmax'] == 0) {

      // @tbd $update_total_earned
      $sql = "INSERT INTO `{$tableName}` (
                userpointcounters_user_id,
                userpointcounters_action_id,
                userpointcounters_amount, 
                userpointcounters_cumulative )
              VALUES (
                ?,
                ?,
                ?,
                ? )
              ON DUPLICATE KEY UPDATE
                userpointcounters_amount = userpointcounters_amount + ?,
                userpointcounters_cumulative = userpointcounters_cumulative + ?";

      $values = array('userpointcounters_user_id'     => $user_id,
                      'userpointcounters_action_id'   => $action_data['action_id'], 
                      'userpointcounters_amount'      => $points_to_add,
                      'userpointcounters_cumulative'  => $points_to_add,
                      );

      $values2 = array(
                      'userpointcounters_amount'      => $points_to_add,
                      'userpointcounters_cumulative'  => $points_to_add,
                      );
      
    } else {
                

      // TIME FOR ROLLOVER OR NEVER HAD POINTS FOR THIS ACTION
      if(empty($action_data['userpointcounters_lastrollover']) || ( ($action_data['action_rolloverperiod'] != 0) && ($now - intval($action_data['userpointcounters_lastrollover']) >= intval($action_data['action_rolloverperiod']) )) ) {

        // CUT IF ADDING MORE THAN MAX ?
        if($points_to_add > $action_data['action_pointsmax'] ) {
          
          if($allow_partial) {

            $points_to_add = $action_data['action_pointsmax'];

          } else {

            return self::$ADDING_MORE_THAN_MAX;

          }
          
        }
  
        
        if($update_total_earned) {

          $sql = "INSERT INTO `{$tableName}` (
                    userpointcounters_user_id,
                    userpointcounters_action_id,
                    userpointcounters_lastrollover,
                    userpointcounters_amount, 
                    userpointcounters_cumulative )
                  VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ? )
                  ON DUPLICATE KEY UPDATE
                    userpointcounters_lastrollover = ?,
                    userpointcounters_amount = ?,
                    userpointcounters_cumulative = userpointcounters_cumulative + ?";

          $values = array('userpointcounters_user_id'       => $user_id,
                          'userpointcounters_action_id'     => $action_data['action_id'],
                          'userpointcounters_lastrollover'  => $now,
                          'userpointcounters_amount'        => $points_to_add,
                          'userpointcounters_cumulative'    => $points_to_add,
                          );
          $values2 = array(
                          'userpointcounters_lastrollover'  => $now,
                          'userpointcounters_amount'        => $points_to_add,
                          'userpointcounters_cumulative'    => $points_to_add,
                          );
          
          
        } else {
  
          $sql = "INSERT INTO `{$tableName}` (
                    userpointcounters_user_id,
                    userpointcounters_action_id,
                    userpointcounters_lastrollover,
                    userpointcounters_amount )
                  VALUES (
                    ?,
                    ?,
                    ?,
                    ? )
                  ON DUPLICATE KEY UPDATE
                    userpointcounters_lastrollover = ?,
                    userpointcounters_amount = ?";
          
          $values = array('userpointcounters_user_id'     => $user_id,
                          'userpointcounters_action_id'   => $action_data['action_id'],
                          'userpointcounters_lastrollover'=> $now,
                          'userpointcounters_amount'      => $points_to_add,
                          );
    
          $values2 = array(
                          'userpointcounters_lastrollover'=> $now,
                          'userpointcounters_amount'      => $points_to_add,
                          );
          
        }
  
      } else {
        // ROLLOVER DATE NOT REACHED, SEE IF HIT MAX
  
        if($action_data['userpointcounters_amount'] + $points_to_add <= $action_data['action_pointsmax'] ) {
        // DIDN'T HIT MAX, OK
        
        // this one adds partial amount
        } elseif ((($amount > 1) || !$allow_partial) && ($action_data['action_pointsmax'] - $action_data['userpointcounters_amount'] > 0)) {

          // HIT MAX, ADD PARTIAL (? CHECK IF AMOUNT > 1 AND WE CAN STILL SQUEEZE SOME)
  
          $this->_partial_amount = $points_to_add = $action_data['action_pointsmax'] - $action_data['userpointcounters_amount'];


          if(!$allow_partial) {
            return self::$ADDING_PARTIAL;
          }
  
        } else {
  
          return self::$ADDING_LIMIT_REACHED;
  
        }

  
        if($update_total_earned) {

          $table->update(array(
            'userpointcounters_amount' => new Zend_Db_Expr('userpointcounters_amount + ' . $table->getAdapter()->quote($points_to_add)),
            'userpointcounters_cumulative' => new Zend_Db_Expr('userpointcounters_cumulative + ' . $table->getAdapter()->quote($points_to_add)),
          ), array(
            'userpointcounters_user_id = ?' => $user_id,
            'userpointcounters_action_id = ?' => $action_data['action_id'],
          ));
          
        } else {
  
          $table->update(array(
            'userpointcounters_amount' => new Zend_Db_Expr('userpointcounters_amount + ' . $table->getAdapter()->quote($points_to_add))
          ), array(
            'userpointcounters_user_id = ?' => $user_id,
            'userpointcounters_action_id = ?' => $action_data['action_id'],
          ));
          
        }
  
      }
      
    }

    $this->_points_to_add = $points_to_add;
    $this->_action_data = $action_data;

    // update quotas, if needed
    // negative is OK
    if( $points_to_add != 0 ) {
      
      if(!empty($sql)) {
        $db->query($sql, array_merge(array_values($values), array_values($values2)));
      }

    }
    
    return self::$ADDING_SUCCESS;
   
  }
  
  public function reset($user_id, $action_type) {

    $action_data = Engine_Api::_()->getDbTable('actionpoints', 'activitypoints')->get($action_type);
    
    if($action_data) {
  
      $this->update(array(
        'userpointcounters_amount' => 0,
        'userpointcounters_lastrollover'  => time()
      ), array(
        'userpointcounters_user_id = ?' => $user_id,
        'userpointcounters_action_id = ?' => $action_data['action_id'],
      ));
      
    }
    
  }
    

}