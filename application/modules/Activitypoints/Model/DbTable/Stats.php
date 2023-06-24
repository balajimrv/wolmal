<?php
class Activitypoints_Model_DbTable_Stats extends Engine_Db_Table
{
  protected $_name = 'semods_userpointstats';
  
  public function update($user_id, $type, $amount = 1) {
    
    $db = $this->getTable()->getAdapter();
    $tableName = $this->getTable()->info("name");

    $sql = "INSERT INTO `{$tableName}` (userpointstat_$type, userpointstat_user_id, userpointstat_date)
              VALUES ( ?, ?, ? )
              ON DUPLICATE KEY UPDATE
              userpointstat_$type = userpointstat_$type + ?";
    
    $now = time();
    
    $values = array("userpointstat_$type"   => $amount,
                    'userpointstat_user_id' => $user_id,
                    //'userpointstat_date'    => 'UNIX_TIMESTAMP(CURDATE())',                     
                    'userpointstat_date'    => $now,
                    );

    $values2 = array("userpointstat_$type"   => $amount,
                    );

    $db->query($sql, array_merge(array_values($values), array_values($values2)));
    
  }


  public function getTable()
  {
    return $this;
  }

}