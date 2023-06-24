<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: StoreBillPaymentController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_StoreBillPaymentController extends Core_Controller_Action_Standard {

  protected $_session;
  
  public function init() {
    // Get session
    $this->_session = new Zend_Session_Namespace('Store_Bill_Payment_Sitestoreproduct');
  }

  public function processAction() {

   $this->_session->store_id = $this->_getParam('store_id');
   $this->_session->bill_id = $this->_getParam('bill_id'); 
    // Process
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    
    $gateway_id = 2;  
    $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', 'paypal');
    if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $paymentMethod != 'paypal') {
        $gatewayPlugin = "Sitegateway_Plugin_Gateway_".ucfirst($paymentMethod);
        $gateway_id = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gatewayPlugin));
    }    

    // Create order
    $ordersTable->insert(array(
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'gateway_id' => $gateway_id,
        'state' => 'pending',
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'source_type' => 'sitestoreproduct_storebill',
        'source_id' => $this->_session->bill_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    $gateway = Engine_Api::_()->getItem('sitestoreproduct_storepaypalbill', $gateway_id);

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();

    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    // Prepare transaction
    $params = array();
//    $params['language'] = $this->_user->language;
    $params['vendor_order_id'] = $order_id;

    $params['return_url'] = $schema . $host
            . $this->view->url(array('action' => 'return', 'controller' => 'store-bill-payment', 'module' => 'sitestoreproduct'), 'default')
            . '?order_id=' . $order_id
            . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return', 'controller' => 'store-bill-payment', 'module' => 'sitestoreproduct'), 'default')
            . '?order_id=' . $order_id
            . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
            . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
            . '?order_id=' . $order_id;
$currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $supportedCurrency =$gatewayPlugin->getSupportedCurrencies();
        $isSupported = true;
        if (!in_array($currentCurrency, $supportedCurrency))           
                   $isSupported = false;
       $this->view->isSupported = $isSupported;
       if($isSupported){
    // Process transaction
    $transaction = $plugin->createStoreBillTransaction($this->_session->store_id, $this->_session->bill_id, $params);

    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    unset($this->_session->user_order_id);
    $this->view->transactionMethod = $transactionMethod;
    // Handle redirection
    if ($transactionMethod == 'GET' && !Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }
  }
  }

  public function returnAction() {

    // Get order
    if (!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
            !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
            $order->source_type != 'sitestoreproduct_storebill' ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    $gateway_id = 2;  
    $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', 'paypal');
    if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && $paymentMethod != 'paypal') {
        $gatewayPlugin = "Sitegateway_Plugin_Gateway_".ucfirst($paymentMethod);
        $gateway_id = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => $gatewayPlugin));
    }        
    
    $gateway = Engine_Api::_()->getItem('sitestoreproduct_storepaypalbill', $gateway_id);
    
    if( !$gateway )
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);

    // Get gateway plugin
    $plugin = $gateway->getPlugin();

    unset($this->_session->errorMessage);

    try {
      $status = $plugin->onStoreBillTransactionReturn($order, $this->_getAllParams());
    } catch (Payment_Model_Exception $e) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }

//    $this->_success->succes_id = $user_order->parent_id;
    return $this->_finishPayment($status);
  }

  protected function _finishPayment($state = 'active') {

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    // $store_id = $this->_session->store_id;
      
     $store_id = $this->_getParam('store_id'); 
    $this->_session->unsetAll();
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if ($state == 'free') {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state, 'store_id' => $store_id));
    }
  }

  public function finishAction() {
    $session = new Zend_Session_Namespace('Sitestoreproduct_Store_Bill_Payment_Detail');

    if (!empty($session->sitestoreproductStoreBillPaymentDetail))
      $session->sitestoreproductStoreBillPaymentDetail = '';

    $paymentDetail = array();
    $paymentDetail['state'] = $this->_getParam('state');
    
    if( !empty($this->_session->errorMessage) ) {
      $paymentDetail['errorMessage'] = $this->_session->errorMessage;
    }

    $session->sitestoreproductStoreBillPaymentDetail = $paymentDetail;
    
    return $this->_helper->redirector->gotoRoute(array('action' => 'store', 'store_id' => $this->_getParam('store_id'), 'type' => 'product', 'menuId' => 54, 'method' => 'store-transaction', 'tab' => 1), 'sitestore_store_dashboard', true);
  }

//  public function detailTransactionAction() {
//    $transaction_id = $this->_getParam('transaction_id', null);
//
//    if (empty($transaction_id)) {
//      return $this->_forward('notfound', 'error', 'core');
//    }
//
//    $transaction = Engine_Api::_()->getItem('sitestoreproduct_transaction', $transaction_id);
//    $gateway = Engine_Api::_()->getItem('payment_gateway', $transaction->gateway_id);
//
//    $link = null;
//    if ($this->_getParam('show-parent')) {
//      if (!empty($transaction->gateway_parent_transaction_id)) {
//        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_parent_transaction_id);
//      }
//    } else {
//      if (!empty($transaction->gateway_transaction_id)) {
//        $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
//      }
//    }
//
//    if ($link) {
//      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
//    } else {
//      die();
//    }
//  }

}