<?php

class Sescustomize_Api_Core extends Core_Api_Abstract
{
  function getValue($date){
    $db = Engine_Db_Table::getDefaultAdapter();
    $result = $db->query('SELECT * FROM engine4_sescustomize_bbvalues WHERE date = "'.$date.'"')->fetch();
    return $result;
  }
  
  function insertValue($date,$value){
    $db = Engine_Db_Table::getDefaultAdapter();
    $result = $db->query('INSERT INTO engine4_sescustomize_bbvalues (`date`,`value`) VALUES ("'.$date.'","'.$value.'")');
    return $db->lastInsertId();
  }
  
  function updateValue($id,$value){
    $db = Engine_Db_Table::getDefaultAdapter();
    $update = $db->query('UPDATE engine4_sescustomize_bbvalues SET value = "'.$value.'" WHERE bbvalue_id = '.$id);
    return $update;
  }
  
  function getLastTotal($year){
    $db = Engine_Db_Table::getDefaultAdapter();
    $result = $db->query('SELECT * FROM engine4_sescustomize_bbvalues WHERE date = "'.$date.'"')->fetch();
    return $result;
  }
  
  function canUserInvite() {
	
      $viewer = Engine_Api::_()->user()->getViewer();
      if($viewer->getIdentity()) {
          $handShakeCount = $this->usedHandshakeCount();
		  $showAlways = '';
    	  switch($viewer->level_id) {
    		 case '6':
    		 $totalHandshake = '100';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 100 days'));
    		 break;
    		 case '7':
    		 $totalHandshake = '200';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 200 days'));
    		 break;
    		 case '8':
    		 $totalHandshake = '300';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 300 days'));
    		 break;
    		 case '9':
    		 $totalHandshake = '500';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 500 days'));
    		 break;
    		 case '10':
    		 $totalHandshake = '500';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 5 years'));
    		 break;
    		 case '11':
    		 $totalHandshake = '1000';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 5 years'));
    		 break;
    		 case '12':
    		 $totalHandshake = '2000';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 5 years'));
    		 break;
    		 case '13':
    		 $totalHandshake = '4000';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 5 years'));
    		 break;
    		 case '14':
    		 $totalHandshake = '5000';
    		 $endDate = date('Y-m-d H:i:s', strtotime($viewer->active_date. ' + 5 years'));
    		 break;
    		 default:
			 $showAlways = 1;
    	  }
    	  if((($handShakeCount == $totalHandshake) || (time() > strtotime($endDate))) && empty($showAlways)) {
    	    return false;
    	  }
    	  return true;
	  }
	  else{
	    return false;
	  }
    }
    function usedHandshakeCount() {
      $inviterTable = Engine_Api::_()->getDbTable('invites', 'inviter');
      return $inviterTable->select()->from($inviterTable->info('name'), "COUNT('user_id')")
      ->where('user_id =?',Engine_Api::_()->user()->getViewer()->getIdentity())
      ->group('user_id')
      ->query()
      ->fetchColumn();
    }
    function getUserBridges($userId,$type) {
        $bridgeTable = Engine_Api::_()->getDbTable('bridges','sesbasic');
        $select = $bridgeTable->select();
        if($type == 'bb')
        $select->from($bridgeTable->info('name'),"SUM(buyer_bb) as bridge_count");
		elseif($type == 'bbmonth')
        $select->from($bridgeTable->info('name'),"SUM(buyer_bb) as bridge_count")->where("DATE_FORMAT(creation_date,'%Y-%m')=?", date("Y-m"));
        elseif($type == 'cb')
        $select->from($bridgeTable->info('name'),"SUM(buyer_cb) as bridge_count");
        elseif($type == 'db')
        $select->from($bridgeTable->info('name'),"SUM(buyer_db) as bridge_count");
        $data = $select->where('buyer_user_id =?',$userId)->query()->fetchColumn();
        if(!$data) return 0;else return $data;
    }
}

