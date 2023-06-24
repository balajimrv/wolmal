<?php
class Sescustomize_AdminTransactionController extends Core_Controller_Action_Admin {
  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_transaction');
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main_transaction', array(), 'sescustomize_admin_main_trans');
    $table = Engine_Api::_()->getDbTable('reedemrequests','sescustomize');
    $select = $table->select()->where('status =?',0);
    $this->view->paginator = $table->fetchAll($select);
  }
  public function paymentMadeAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_transaction');
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main_transaction', array(), 'sescustomize_admin_main_tranmade');
    $table = Engine_Api::_()->getDbTable('reedemrequests','sescustomize');
    $select = $table->select()->where('status =?',1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber( $this->_getParam('page',1) ); 
        
  }
  public function detailPaymentAction(){
    $id = $this->_getParam('id','');
    $this->view->payment = Engine_Api::_()->getItem('sescustomize_reedemrequests',$id);
    
  }
  public function statusAction(){
    $type = $this->_getParam('type',''); 
    $id = $this->_getParam('id','');
    $item = Engine_Api::_()->getItem('sescustomize_reedemrequests',$id);
    $this->view->form = $form = new Sescustomize_Form_Admin_Status();
    if($type == 1)
      $form->setTitle('Payment Approve');
    else
      $form->setTitle('Payment Reject');
    $form->populate($item->toArray());
    if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
      $item->admin_note = $_POST['admin_note'];
      $item->status = $type;
      $item->save();
      if($type == 1){
        $message = "Payment Approved."; 
        $titleMessage = "approved";
      }else{
        $message = "Payment Rejected.";
        $titleMessage  = "rejected";
      }
      if($type == 1){
        //insert into reedem table //type bank
        $ebvaluestable = Engine_Api::_()->getDbtable('ebvalues', 'sescustomize');
        $ebvaluestable->insert(array(
          'user_id' => $item->user_id,
          'total' => @round($item->amount, 2),
          'type'=>'bank',
          'creation_date' => date('Y-m-d H:i:s'),
        ));
        
        
      }
      
    $itemUrl = '<a href="/view-request/">' . "view" . '</a>';
    Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification(Engine_Api::_()->getItem('user',$item->user_id), Engine_Api::_()->user()->getViewer(), 
    Engine_Api::_()->user()->getViewer(), 'sescustomize_reedemrequest', array('item_url' => $itemUrl,'title'=>$titleMessage));
      
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array($message)
      ));
    }
     
  }
  
}