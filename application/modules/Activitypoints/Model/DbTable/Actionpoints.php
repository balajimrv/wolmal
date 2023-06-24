<?php
class Activitypoints_Model_DbTable_Actionpoints extends Engine_Db_Table
{
  protected $_name = 'semods_actionpoints';


  public function removeAction($type) {
    
    $this->delete(array('action_type = ?' => $type));
    
  }

  /*
   @param params[group]  -1 : non displayable in admin
     
  */
  public function updateActionPoints($type, $params) {
    
    $enabled = Semods_Utils::g($params,'enabled',1);
    $points = Semods_Utils::g($params,'points');
    $pointsmax = Semods_Utils::g($params,'pointsmax',0);
    $rolloverperiod = Semods_Utils::g($params,'rolloverperiod',0);
    $group = Semods_Utils::g($params,'group',0);
    
    try {
      
        $values = array('action_type'           => $type,
                        'action_enabled'        => $enabled,
                        'action_points'         => $points,
                        'action_pointsmax'      => $pointsmax,
                        'action_rolloverperiod' => $rolloverperiod,
                        'action_group'          => $group
                        );
        
        $updated = $this->update($values, array('action_type = ?' => $type));
  
        // if values are the same, update can still be 0 => try/catch
        if( $updated < 1 ) {
          $this->insert($values);
        }
        
    } catch( Exception $ex ) {
    }
    
  }
  
  public function get($type, $user_id = 0) {
    
    if($user_id != 0) {
      return $this->getByUser($type, $user_id);
    }

    return $this->getByType($type);

  }

  public function getByType($type) {
    
    $select = $this->getTable()->select()->where('action_type = ?', $type);
    
    $row = $this->getTable()->fetchRow( $select );
    
    if(!$row) {
      return $row;
    }
    
    $row = $row->toArray();

    // empty values because getting by type means user has no values yet    
    $row['userpointcounters_lastrollover'] = 0;
    $row['userpointcounters_amount'] = 0;

    return $row;
    
  }
  
  public function getByUser($type, $user_id) {

    $table_counters = Engine_Api::_()->getDbTable('counters', 'activitypoints');
    $table_counters_name = $table_counters->info('name');

    $table_actionpoints = $this->getTable();
    $table_actionpoints_name = $table_actionpoints->info('name');

    $select = $this->getTable()->select()
              ->setIntegrityCheck(false)
              ->from($table_actionpoints_name)
              ->join($table_counters_name,"`{$table_actionpoints_name}`.`action_id` = `{$table_counters_name}`.`userpointcounters_action_id`", '*')
              ->where("action_type = ?", $type)
              ->where("userpointcounters_user_id = ?", $user_id);
    
    return $this->getTable()->fetchRow($select);  // ->toArray();
    
  }

   public function getTable()
  {
    return $this;
  }

}