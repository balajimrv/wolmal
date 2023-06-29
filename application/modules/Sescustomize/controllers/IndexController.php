<?php

class Sescustomize_IndexController extends Core_Controller_Action_Standard
{
  function requestsAction(){
    $this->_helper->requireUser()->isValid();
    $this->setupNavigation('activitypoints_help');
    
    $table = Engine_Api::_()->getDbTable('reedemrequests','sescustomize');
    $select = $table->select()->where('user_id =?',Engine_Api::_()->user()->getViewer()->getIdentity());
    $this->view->paginator = $paginator =  Zend_Paginator::factory($select);
    	$paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam('page',1) );  
    
    
  }
  public function detailPaymentAction(){
    $id = $this->_getParam('id','');
    $this->view->payment = Engine_Api::_()->getItem('sescustomize_reedemrequests',$id);
    
  }
  function redeemFormAction(){
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id','');
    //$total = Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->earning();
    
    $total = $_SESSION['totalEarn'];
    $exp = Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend();
    $isEarn = $total- $exp;
    if(!$isEarn)
      return $this->_forward('notfound', 'error', 'core');
      
    $this->view->form = $form = new Sescustomize_Form_Redeem();
    
    if (!empty($id)){
      $item = Engine_Api::_()->getItem('sescustomize_reedemrequests',$id);
      if(!$item || $item->user_id != Engine_Api::_()->user()->getViewer()->getIdentity() )
        return $this->_forward('notfound', 'error', 'core');
      $form->populate($item->toArray());
    }
    $db = Engine_Api::_()->getDbTable('reedemrequests', 'sescustomize')->getAdapter();
    $db->beginTransaction();
    $form->balance_total->setValue($total);
    $_POST['balance_total'] = $total;
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }
      $values = $form->getValues(); 
      if($values['amount'] > $total){
        $form->addError("Requested Amount must be less than total balance amount."); 
        return; 
      }  
      $values['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity(); 
      $values['modified_date'] = $values['creation_date'] = date('Y-m-d H:i:s');
      try {
        $table = Engine_Api::_()->getDbTable('reedemrequests', 'sescustomize');
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $db->commit();
        $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
      }catch(Exception $e){
        $db->rollBack();
        throw $e;  
      }
            
    }
    
  }
  public function getTransferAction(){
    $type = $this->view->type = $this->_getParam('type','');
    $month = $this->_getParam('month','');
    $this->view->viewmore = $this->_getParam('viewmore','0');
    $table = Engine_Api::_()->getDbTable('fbvalues','sescustomize');
    $select = $table->select()->where('type =?',$type)
                ->where('user_id =?',Engine_Api::_()->user()->getViewer()->getIdentity())
                ->where('DATE_Format(creation_date,"%Y-%m") =?',$month);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber( $this->_getParam('page',1) );   
    
  }
  public function getUsersAction()
  {
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->month = $month = $this->_getParam('month');
    $this->view->year = $year = $this->_getParam('year');
    $this->view->type = $type = $this->_getParam('type');
    $bridgetable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
    $tableName = $bridgetable->info('name');
    $select = $bridgetable->select()->setIntegrityCheck(false)->from($bridgetable->info('name'), array('user_id'))
                          ->joinLeft($tableName,$tableName.'.parent_id=engine4_sesbasic_bridges_2.bridge_id',array('SUM(engine4_sesbasic_bridges_2.buyer_bb) as buyer_bb','SUM(engine4_sesbasic_bridges_2.buyer_cb) as buyer_cb'))
                          ->where('DATE_FORMAT('.$tableName.'.creation_date,"%Y-%m") =?',$year.'-'.$month)
                          ->where($tableName. '.buyer_user_id =?', $viewerId)
                          ->group($tableName .'.user_id');
    if($type == 'cb')
    $select->where($tableName.'.buyer_cb !=0');
    else
    $select->where($tableName.'.buyer_db !=0');
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);
  }
  public function setupNavigation($active_menu) {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_main', array(), $active_menu);

  }
   public function bridgesAction() {
      
    $this->_helper->requireUser()->isValid();
    $this->setupNavigation('activitypoints_help');
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(isset($_GET['year']))
    $this->view->year = $year  = $_GET['year'];
    else
    $this->view->year = $year  = date('Y');

	$bridgeTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
	$selectTable = $bridgeTable->select()
	               ->from($bridgeTable->info('name'), array("SUM(buyer_bb) as total_bb","SUM(buyer_cb) as total_cb","SUM(buyer_db) as total_db", "creation_date"))
	               ->where("DATE_FORMAT(creation_date,'%Y')=?", $year)
	               ->where('buyer_user_id =?', $viewerId)
	               ->group("YEAR(creation_date)")
	               ->group("MONTH(creation_date)");
	$this->view->bridges = $bridgeTable->fetchAll($selectTable);
	$bArray = array();
	foreach($this->view->bridges as $bridge) {
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_bb'] = $bridge['total_bb'];  
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_cb'] = $bridge['total_cb']; 
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_db'] = $bridge['total_db']; 
	}
	$this->view->bridges = $bArray;  
	
	if($year > 2017){
	
    $selectTable = $bridgeTable->select()
    	               ->from($bridgeTable->info('name'), array("SUM(buyer_bb) as total_full_bb","SUM(buyer_cb) as total_full_cb","SUM(buyer_db) as total_full_db", "creation_date"))
    	               ->where("DATE_FORMAT(creation_date,'%Y') >= ?", 2017) 
    	               ->where("DATE_FORMAT(creation_date,'%Y') <= ?", ($year-1)) 
    	               ->where('buyer_user_id =?', $viewerId)
    	               ->group("YEAR(creation_date)")
    	               ->group("MONTH(creation_date)");
    	$this->view->full_bridges = $bridgeTable->fetchAll($selectTable);
    	
    	$bArray = array();
    	foreach($this->view->full_bridges as $bridge) {
	        $bArray[] = array("total_full_bb"=>$bridge['total_full_bb'],"total_full_cb"=>$bridge['total_full_cb'],"total_full_db"=>$bridge['total_full_db'],"creation_date"=>$bridge['creation_date']);
    	}
    	$this->view->full_bridges = $bArray;
   }
  
  }
   public function referenceMemberAction() {
      
    $this->_helper->requireUser()->isValid();
    $this->setupNavigation('activitypoints_help');
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();

	$inviterTable = Engine_Api::_()->getDbTable('invites', 'inviter');
	$inviterTableName = $inviterTable->info('name');
	
	$userTable = Engine_Api::_()->getItemTable('user');
	$userTableName = $userTable->info('name');
	
	$selectTable = $userTable->select()
	                ->setIntegrityCheck(false)
	               ->from($userTableName, array('displayname','user_id'))
	               ->where($inviterTableName.".user_id =?",$viewerId)
	               ->where($inviterTableName.".new_user_id !=?",0)
	               ->join($inviterTableName,$inviterTableName.'.new_user_id='.$userTableName.'.user_id','');
	$this->view->users = $userTable->fetchAll($selectTable);
  
  }
}
