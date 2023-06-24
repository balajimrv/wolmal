<?php

class Activitypoints_AdminTransactionsController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_transactions');


    $task = $this->_getParam('task', '');    

    if($task == "confirm") {
      $transaction_id = intval($this->_getParam('transaction_id', 0));
    
      $uptransaction = Engine_Api::_()->getDbtable('transactions', 'activitypoints')->complete($transaction_id);
    
    }
    
    
    if($task == "cancel") {
      $transaction_id = intval($this->_getParam('transaction_id',0));
    
      $uptransaction = Engine_Api::_()->getDbtable('transactions', 'activitypoints')->cancel($transaction_id);
    }


    $this->view->formFilter = $formFilter = new Activitypoints_Form_Admin_TransactionsFilter();
    
    $page = $this->_getParam('page',1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $table->info('name');

    $rTable = Engine_Api::_()->getDbtable('transactions', 'activitypoints');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($rName)
      ->join($userTableName, "`{$userTableName}`.`user_id` = `{$rName}`.`uptransaction_user_id`", '*');

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'uptransaction_id',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'uptransaction_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['f_state']) && ($values['f_state'] != -1) )
    {
      $select->where('uptransaction_state = ?', $values['f_state'] );
    }
    
    if( !empty($values['f_title']) )
    {
      $select->where('uptransaction_text LIKE ?', '%' . $values['f_title'] . '%');
    }

    if( !empty($values['f_user']) )
    {
      $select->where('username LIKE ?', '%' . $values['f_user'] . '%');
    }

    if( !empty($values['f_email']) )
    {
      $select->where('email LIKE ?', '%' . $values['f_email'] . '%');
    }

    if( !empty($values['f_displayname']) ) {
      $select->where('displayname LIKE ?', '%' . $values['f_displayname'] . '%');
    }
    
    if( isset($values['f_type']) && ($values['f_type'] != -1) )
    {
      if(strpos($values['f_type'],'_') !== false) {
        $f_type = explode('_', $values['f_type']);
        $select->where('uptransaction_type = ?', $f_type[1] )
               ->where('uptransaction_cat = ?', $f_type[0] );
      } else {
        $select->where('uptransaction_type = ?', $values['f_type'] );
      }
    }

    $valuesCopy = array_filter($values);
    
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

    $this->view->formValues = $valuesCopy;

  }


  public function transactionsmodifyAction()
  {
    if ($this->getRequest()->isPost()) {
      $transactions = $this->_getParam('transactions');
      if(is_array($transactions) && !empty($transactions)) {
        $transaction = implode(',',$transaction);
      }
      Engine_Api::_()->getDbTable('transactions','activitypoints')->delete(array("uptransaction_id IN (?)"=> $transactions));
    }
      
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));

  }


}