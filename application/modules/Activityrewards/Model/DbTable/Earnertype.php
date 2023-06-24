<?php
class Activityrewards_Model_DbTable_Earnertype extends Engine_Db_Table
{
  protected $_name = 'semods_userpointearnertypes';

  public function getTypename($type_id) {
  
    $select = $this->select()
              ->from($this->info('name'))
              ->columns('userpointearnertype_name')
              ->where("userpointearnertype_id = ?", $type_id);
    
    $row = $this->fetchRow($select);
  
    return $row ? $row->userpointearnertype_name : null;
    
  }

  public function getType($type_id) {
  
    $select = $this->select()
              ->from($this->info('name'))
              ->columns('userpointearnertype_type')
              ->where("userpointearnertype_id = ?", $type_id);
    
    $row = $this->fetchRow($select);
  
    return $row ? $row->userpointearnertype_type : null;
    
  }

}