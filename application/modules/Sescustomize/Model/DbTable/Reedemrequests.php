<?php
class Sescustomize_Model_DbTable_Reedemrequests extends Engine_Db_Table{
  
    function isReqExists(){
      $select = $this->select()->from($this->info('name'),'reedemrequest_id')->where('user_id =?',Engine_Api::_()->user()->getViewer()->getIdentity())->where('status =?',0);
      return $select->query()
      ->fetchColumn(0)  ;
    }
}