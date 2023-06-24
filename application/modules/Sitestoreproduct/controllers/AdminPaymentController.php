<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPaymentController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminPaymentController extends Core_Controller_Action_Admin {

  protected $_navigation;
  protected $_user_id;
  //User_Model_User
  protected $_user;
  // Zend_Session_Namespace
  protected $_session;
  // Payment_Model_Order
  protected $_order;
  // Payment_Model_Userad
  protected $_user_request;
  protected $_success;

  public function init() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_user_id = $this->_user->getIdentity();

    // Get user and session
    $this->_session = new Zend_Session_Namespace('Payment_Sitestoreproducts');
    $this->_success = new Zend_Session_Namespace('Payment_Success');

    // Check viewer and user
    if (!$this->_user_request) {
      if (!empty($this->_session->user_request_id)) {
        $this->_user_request = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $this->_session->user_request_id);
      }
    }
  }

  public function indexAction() {
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_payment');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();

    $page = $this->_getParam('page', 1);

    $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct');
    $paymentRequestTableName = $paymentRequestTable->info('name');
  
    $pageTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $pageTable->info('name');

    $select = $paymentRequestTable->select()
            ->setIntegrityCheck(false)
            ->from($paymentRequestTableName)
            ->joinLeft($pageTableName, "$paymentRequestTableName.store_id = $pageTableName.store_id", array("$pageTableName.title"))
            ->group($paymentRequestTableName . '.request_id');

    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

     $values = array_merge(array('order' => 'request_id','order_direction' => 'DESC'), $values);
     
       if (!empty($_POST['title'])) {
      $title = $_POST['title'];
    } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
      $title = $_GET['title'];
    } else {
      $title = '';
    }
    
      if (!empty($_POST['request_date'])) {
      $request_date = $_POST['request_date'];
    } elseif (!empty($_GET['request_date']) && !isset($_POST['post_search'])) {
      $request_date = $_GET['request_date'];
    } else {
      $request_date = '';
    }
    
      if (!empty($_POST['response_date'])) {
      $response_date = $_POST['response_date'];
    } elseif (!empty($_GET['response_date']) && !isset($_POST['post_search'])) {
      $response_date = $_GET['response_date'];
    } else {
      $response_date = '';
    }
      if ($_POST['request_min_amount'] != '') {
      $request_min_amount = $_POST['request_min_amount'];
    } elseif ($_GET['request_min_amount'] != '' && !isset($_POST['post_search'])) {
      $request_min_amount = $_GET['request_min_amount'];
    } else {
      $request_min_amount = '';
    }
    
      if ($_POST['request_max_amount'] != '') {
      $request_max_amount = $_POST['request_max_amount'];
    } elseif ($_GET['request_max_amount'] != '' && !isset($_POST['post_search'])) {
      $request_max_amount = $_GET['request_max_amount'];
    } else {
      $request_max_amount = '';
    }
    
      if ($_POST['response_min_amount'] != '') {
      $response_min_amount = $_POST['response_min_amount'];
    } elseif ($_GET['response_min_amount'] != '' && !isset($_POST['post_search'])) {
      $response_min_amount = $_GET['response_min_amount'];
    } else {
      $response_min_amount = '';
    }
    
      if ($_POST['response_max_amount'] != '') {
      $response_max_amount = $_POST['response_max_amount'];
    } elseif (!empty($_GET['response_max_amount']) && !isset($_POST['post_search'])) {
      $response_max_amount = $_GET['response_max_amount'];
    } else {
      $response_max_amount = '';
    }
    
      if (!empty($_POST['request_status'])) {
      $request_status = $_POST['request_status'];
    } elseif (!empty($_GET['request_status']) && !isset($_POST['post_search'])) {
      $request_status = $_GET['request_status'];
    } else {
      $request_status = '';
    }
    
    
    // searching
    $this->view->title = $values['title'] = $title;
    $this->view->request_date = $values['request_date'] = $request_date;
    $this->view->response_date = $values['response_date'] = $response_date;
    $this->view->request_min_amount = $values['request_min_amount'] = $request_min_amount;
    $this->view->request_max_amount = $values['request_max_amount'] = $request_max_amount;
    $this->view->response_min_amount = $values['response_min_amount'] = $response_min_amount;
    $this->view->response_max_amount = $values['response_max_amount'] = $response_max_amount;
    $this->view->request_status = $values['request_status'] = $request_status;

    if (!empty($title)) {
      $select->where($pageTableName . '.title  LIKE ?', '%' . trim($title) . '%');
    }

    if (!empty($request_date)) {
      $select->where("CAST($paymentRequestTableName.request_date AS DATE) =?", trim($request_date));
    }

    if (!empty($response_date)) {
      $select->where("CAST($paymentRequestTableName.response_date AS DATE) =?", trim($response_date));
    }

    if ($request_min_amount != '') {
      $select->where("$paymentRequestTableName.request_amount >=?", trim($request_min_amount));
    }

    if ($request_max_amount != '') {
      $select->where("$paymentRequestTableName.request_amount <=?", trim($request_max_amount));
    }

    if ($response_min_amount != '') {
      $select->where("$paymentRequestTableName.response_amount >=?", trim($response_min_amount));
    }

    if ($response_max_amount != '') {
      $select->where("$paymentRequestTableName.response_amount <=?", trim($response_max_amount));
    }

    if (!empty($request_status)) {     
      $request_status--;
      $select->where($paymentRequestTableName . '.request_status LIKE ? ', '%' . $request_status . '%');
    }

     //ASSIGN VALUES TO THE TPL
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'request_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
  }

  public function processPaymentAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_payment');

    $this->view->payment_req_obj = $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $this->_getParam('request_id'));
    if( empty($payment_req_obj) )
      return $this->_forward('notfound', 'error', 'core');

    if( $payment_req_obj->request_status == 1 )
    {
      $this->view->sitestoreproduct_payment_req_delete = true;
      return ;
    }
    
    $AdminPaymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', "payumoney");
   
    $gateway_id = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->getSellerStoreGateway($payment_req_obj->store_id, $AdminPaymentMethod);
   
   
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $payment_req_obj->store_id);
    $this->view->userObj = Engine_Api::_()->getItem('user', $this->view->sitestore->owner_id);
    
    if( empty($sitestore) )
      return $this->_forward('notfound', 'error', 'core');

    if (empty($gateway_id)) {
      $this->view->gateway_disable = 1;
    } else {
      $this->_session = new Zend_Session_Namespace('Payment_Sitestoreproducts');
      $this->_session->user_request_id = $payment_req_obj->request_id;

      $this->view->form = $form = new Sitestoreproduct_Form_Admin_Payment_PaymentTransfer(array('amount' => @round($payment_req_obj->request_amount, 2)));

      if (!$this->getRequest()->isPost()) {
        return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    
      $values = $form->getValues();

      if( @round($values['user_req_amount'], 2) != @round($payment_req_obj->request_amount, 2) )
      {
        $user_previous_req_amount = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($values['user_req_amount']);
        $user_current_req_amount = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment_req_obj->request_amount);
        $form->addError("Seller has changed the requested amount from $user_previous_req_amount to $user_current_req_amount. So, please review the changed amount before making the payment.");

        $form->user_req_amount->setValue($payment_req_obj->request_amount);
        return;
      }
      
      if ($values['amount'] > $payment_req_obj->request_amount) {
        $form->addError('You can not approve an amount greater than the requested amount. So, please enter an amount less than or equal to the requested amount to approve the payment.');
        return;
      }
      
        $gateway = Engine_Api::_()->getItem('sitestoreproduct_paymentreq', $gateway_id); 

        $payment_gateway_id = Engine_Api::_()->hasModuleBootstrap('sitegateway') ? Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gateway->plugin, 'columnName' => 'gateway_id')) : 2;            

      //UPDATE 
      Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->update(array(
                    'response_amount' => @round($values['amount'], 2),
                    'response_message' => $values['response_message'],
                    'response_date' => new Zend_Db_Expr('NOW()'),
                    'payment_flag' => 1,
                    'gateway_id' => $payment_gateway_id
                    ),array('request_id =? ' => $payment_req_obj->request_id));
      
      return $this->_helper->redirector->gotoRoute(array('module' => 'sitestoreproduct', 'controller' => 'payment', 'action' => 'process', 'gateway_id' => $gateway_id), "admin_default", true);
    }
  }

  public function processAction() {
    if (!$this->_user_request) {
      $this->_session->unsetAll();
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }

    $request_id = $this->_session->user_request_id;
    $user_gateway_id = $this->_getParam('gateway_id');

    if (empty($user_gateway_id)) {
      $this->view->user_gateway_disable = 1;
      return;
    }

    // Get order
    if (!$request_id ||
            !($user_request = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id))) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }

    // Process
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->request_id)) {
      $previousOrder = $ordersTable->find($this->_session->request_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }

    $gateway = Engine_Api::_()->getItem('sitestoreproduct_paymentreq', $user_gateway_id);

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();     
        
    $gateway_id = Engine_Api::_()->hasModuleBootstrap('sitegateway') ? Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gateway->plugin, 'columnName' => 'gateway_id')) : 2;
    
    // Create order
    $ordersTable->insert(array(
        'user_id' => $this->_user_id,
        'gateway_id' => $gateway_id,
        'state' => 'pending',
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'source_type' => 'sitestoreproduct_paymentrequest',
        'source_id' => $request_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];
        if(isset($_SERVER['HTTPS']))
        $schema = 'https://';
    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $params['vendor_order_id'] = $order_id;

    $params['return_url'] = $schema . $host
            . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestoreproduct'), 'admin_default', true)
            . '?order_id=' . $order_id
            . '&state=' . 'return'
            . '&gateway_id=' . $user_gateway_id;
    $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestoreproduct'), 'admin_default', true)
            . '?order_id=' . $order_id
            . '&state=' . 'cancel'
            . '&gateway_id=' . $user_gateway_id;
    $params['ipn_url'] = $schema . $host
            . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'admin_default', true)
            . '?order_id=' . $order_id
            . '&gateway_id=' . $user_gateway_id;
    
    $params['source_type'] = 'sitestoreproduct_paymentrequest';

    // Process transaction
 $currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $supportedCurrency =$gatewayPlugin->getSupportedCurrencies();
        $isSupported = true;
        if (!in_array($currentCurrency, $supportedCurrency))           
                   $isSupported = false;
       $this->view->isSupported = $isSupported;
       if($isSupported){
    $transaction = $plugin->createUserRequestTransaction($this->_user, $request_id, $params);
   
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    unset($this->_session->user_request_id);

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }
  }
  }

  public function returnAction() {

    $user_gateway_id = $this->_getParam('gateway_id');
    // Get order
    if (!$this->_user ||
            !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
            !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
            $order->user_id != $this->_user->getIdentity() ||
            $order->source_type != 'sitestoreproduct_paymentrequest' ||
            !($user_request = $order->getSource()) ||
            !($gateway = Engine_Api::_()->getItem('sitestoreproduct_paymentreq', $user_gateway_id))) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }

    // Get gateway plugin
    $plugin = $gateway->getPlugin();

    unset($this->_session->errorMessage);

    try {
      $status = $plugin->onUserRequestTransactionReturn($order, $this->_getAllParams());
    } catch (Payment_Model_Exception $e) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }

    $this->_success->succes_id = $user_request->request_id;
    return $this->_finishPayment($status);
  }

  protected function _finishPayment($state = 'active') {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->_user;

    // No user?
    if (!$this->_user) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }

    // Log the user in, if they aren't already
    if (($state == 'active' || $state == 'free') &&
            $this->_user &&
            !$this->_user->isSelf($viewer) &&
            !$viewer->getIdentity()) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if ($state == 'free') {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
    }
  }

  public function finishAction() {
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_payment');

    $this->view->state = $state = $this->_getParam('state');

    $this->view->error = $error_message = $this->_session->errorMessage;

    if (isset($this->_success->succes_id)) {
      $request_id = $this->_success->succes_id;
      
      if( $state == 'active' )
      {
        $payment_request_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);
        $store_id = $payment_request_obj->store_id;
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $user = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();

        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $store_name = '<a href="'.$newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref().'">'. $sitestore->getTitle().'</a>';
        
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'sitestoreproduct_payment_request_approve', array(
              'object_title' => $sitestore->getTitle(),
              'object_name' => $store_name,
              'response_amount' => Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment_request_obj->response_amount),
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $sitestore->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me'), 'sitestore_store_dashboard', false),
        ));
      }
  
      unset($this->_success->succes_id);
    }
  }

  public function detailTransactionAction() {
    $transaction_id = $this->_getParam('transaction_id');
    $transaction = Engine_Api::_()->getItem('sitestoreproduct_transaction', $transaction_id);
    $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);

    $link = null;
    if ($this->_getParam('show-parent')) {
      if (!empty($transaction->gateway_parent_transaction_id)) {
        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
      }
    } else {
      if (!empty($transaction->gateway_transaction_id)) {
        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
      }
    }

    if ($link) {
      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
    } else {
      die();
    }
  }
  
  public function deletePaymentRequestAction()
  {
    $request_id = $this->_getParam('request_id', null);
    $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);
    
    $store_id = $payment_req_obj->store_id;
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if( empty($request_id) || empty($payment_req_obj) || empty($sitestore) )
      return $this->_forward('notfound', 'error', 'core');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct');
    $remaining_amount = $remaining_amount_table_obj->fetchRow(array('store_id = ?' => $store_id))->remaining_amount;
    $remaining_amount += $payment_req_obj->request_amount;
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try 
    {
      $payment_req_obj->request_status = 1;
      $payment_req_obj->save();

      //UPDATE REMAINING AMOUNT
      $remaining_amount_table_obj->update(
                    array('remaining_amount' => $remaining_amount),
                    array('store_id =? ' => $store_id));
      $db->commit();
    }
    catch (Exception $e) 
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request deleted successfully.'))
    ));
  }
  
  public function viewPaymentRequestAction()
  {
    $this->view->request_id = $request_id = $this->_getParam('request_id', null);
    $this->view->payment_req_obj = $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id); 
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $payment_req_obj->store_id); 
    $this->view->user_obj = Engine_Api::_()->getItem('user', $this->view->sitestore->owner_id);
    
    if( empty($sitestore) || empty($request_id) || empty($payment_req_obj) )
      return $this->_forward('notfound', 'error', 'core');
    
    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }
}