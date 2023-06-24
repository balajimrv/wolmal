<?php
class Activityrewards_Model_DbTable_Spendertype extends Engine_Db_Table
{
  protected $_name = 'semods_userpointspendertypes';
  
  public function getTypename($type_id) {
  
    $select = $this->select()
              ->from($this->info('name'))
              ->columns('userpointspendertype_name')
              ->where("userpointspendertype_id = ?", $type_id);
    
    $row = $this->fetchRow($select);
  
    return $row ? $row->userpointspendertype_name : null;
    
  }

  public function getType($type_id) {
  
    $select = $this->select()
              ->from($this->info('name'))
              ->columns('userpointspendertype_type')
              ->where("userpointspendertype_id = ?", $type_id);
    
    $row = $this->fetchRow($select);
  
    return $row ? $row->userpointspendertype_type : null;
    
  }

}