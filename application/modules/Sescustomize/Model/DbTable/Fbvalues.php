<?php
class Sescustomize_Model_DbTable_Fbvalues extends Engine_Db_Table
{
  function earning($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), new Zend_Db_Expr('SUM(total)'))
      ->group('user_id')
      ->where('type =?','insert');
    if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
    if(!empty($params['month'])) {
      $sum->where('DATE_Format(creation_date,"%Y-%m") <=?',$params['month']); 
    }
    return (int)$sum->query()
      ->fetchColumn(0)
      ;      
  } 
  function earningGroupBy($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), array('totalEarn'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "insert" THEN  total END)')
                                , 'totalRedeem'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "redeem" THEN  total END)')
                                , 'totalBank'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "bank" THEN  total END)')
      ))
      ->group('user_id');
      //->where('type =?','insert');
    if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
    if(!empty($params['month'])) {
      $sum->where('DATE_Format(creation_date,"%Y-%m") <=?',$params['month']); 
    }
    $row = $this
      ->fetchRow($sum);
      
    if($row){
      return $row->totalEarn - $row->totalRedeem - $row->totalBank;
    }
    return 0;      
  }
  
  
  function totalAmount($user_id){
    $sum = $this->select()
      ->from($this->info('name'), array('totalRedeem'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "redeem" THEN  total END)')
                                , 'totalBank'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "bank" THEN  total END)')
      ))
      ->group('user_id');
      //->where('type =?','insert');
      	$sum->where('user_id = ?', $user_id);
    	$row = $this->fetchRow($sum);
    if($row>0){
      return $row->totalRedeem + $row->totalBank;
    }
    return 0;
  }
  
  
  function expend($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), new Zend_Db_Expr('SUM(total)'))
      ->group('user_id')
      ;
   if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
   if(!empty($params['type']))
    $sum->where('type =?',$params['type']);
   else
    $sum->where('type !=?','insert');
   if(!empty($params['month'])) 
      $sum->where('DATE_Format(creation_date,"%Y-%m") =?',$params['month']); 
   return  (int)$sum->query()
      ->fetchColumn(0)
      ;
  }
  
  function currentFb($params = array()){
    $e = $this->earning($params);
    $ex = $this->expend($params);
      return  $e - $ex;
  }
  
  
  function monthlyIncomeLimit($user_id){
		$user_tbl = Engine_Api::_()->getDbTable('users', 'user');
		$select_row = $user_tbl->select('level_id')->where('user_id = ?', $user_id);
		$query = $user_tbl->fetchRow($select_row);
		$level_id = $query['level_id'];
		
		//Get award
		$levels_tbl = Engine_Api::_()->getDbTable('levels', 'authorization');
		$level_row = $levels_tbl->select('monthly_income_limit')->where('level_id = ?', $level_id);
		$row = $levels_tbl->fetchRow($level_row);
		return $row['monthly_income_limit'];
  }
  
}