<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_PaymentController extends Core_Controller_Action_Standard {

    protected $_user;
    protected $_session;
    protected $_order;
    protected $_gateway;
    protected $_subscription;
    protected $_user_order;
    protected $_success;
    protected $_store_gateway_id;

    public function init() {

        // Get user and session
        $this->_session = new Zend_Session_Namespace('Payment_Sitestoreproduct');
        $this->_success = new Zend_Session_Namespace('Payment_Success');

        // Check viewer and user
        if (!$this->_user_order) {
            if (!empty($this->_session->user_order_id)) {
                $this->_user_order = Engine_Api::_()->getItem('sitestoreproduct_order', $this->_session->user_order_id);
            }
        }
    }

    public function processAction() {
                if (!$this->_user_order) {
            $this->_session->unsetAll();
            return $this->_helper->redirector->gotoRoute(array(), 'sitestoreproduct_general', true);
        }

        $parent_order_id = $this->_session->user_order_id;

        if (!empty($this->_session->checkout_store_id)) {
            $store_id = $this->_session->checkout_store_id;

            $plugin = 'Payment_Plugin_Gateway_PayPal';
            if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                $gatewayObject = Engine_Api::_()->getItem('payment_gateway', $this->_user_order->gateway_id);
                if (!empty($gatewayObject)) {
                    $plugin = $gatewayObject->plugin;
                }
            }

            $store_gateway_id = Engine_Api::_()->getDbtable('storegateways', 'sitestoreproduct')->getPayPalGatewayId($store_id, $plugin);
            if (empty($store_gateway_id))
                return $this->_helper->redirector->gotoRoute(array(), 'sitestoreproduct_general', true);
            else
                $this->_store_gateway_id = $store_gateway_id;
        }

        // Get order
        if (!$parent_order_id ||
                !($user_order = Engine_Api::_()->getItem('sitestoreproduct_order', $parent_order_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'sitestoreproduct_general', true);
        }

        // Process
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }

        if (!empty($this->_session->downpayment_make_payment)) {
            $sourceType = 'sitestoreproduct_orderdownpayment';
            $sourceId = $this->_session->downpayment_make_payment;
            $gateway_id = 2;
        } else {
            $sourceType = 'sitestoreproduct_order';
            $sourceId = $parent_order_id;
            $gateway_id = $user_order->gateway_id;
        }

        // Create order
        $ordersTable->insert(array(
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'gateway_id' => $gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        if (empty($store_id))
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
        else
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_storegateway', $store_gateway_id);

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && $gateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
            $stripeGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id'));
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripechargemethod', 1) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0)) {
                if ($gateway_id == $stripeGatewayId) {
                    $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
                }
            } elseif (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripechargemethod', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0)) {
                $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $user_order->store_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                if ($gateway_id == $stripeGatewayId) {
                    $gateway = Engine_Api::_()->getItem('sitestoreproduct_gateway', $storeGatewayObj->gateway_id);
                }
            }
        } elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway') && ($gateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive' || $gateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay')) {
            $gateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway($gateway->plugin);
        }
        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }

        $schema = 'https://';
         if(isset($_SERVER['HTTPS']))
        $schema = 'https://';
        
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
//    $params['language'] = $this->_user->language;
        $params['vendor_order_id'] = $order_id;

        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestoreproduct'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestoreproduct'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
                . '?order_id=' . $order_id;

        if (!empty($store_id)) {
            $params['store_id'] = $store_id;

            $params['return_url'] .= '&store_id=' . $store_id . '&store_gateway_id=' . $store_gateway_id;
            $params['cancel_url'] .= '&store_id=' . $store_id . '&store_gateway_id=' . $store_gateway_id;
            $params['ipn_url'] .= '&store_id=' . $store_id . '&store_gateway_id=' . $store_gateway_id;
        }

        if (!empty($this->_session->downpayment_make_payment)) {
            $params['downpayment_make_payment'] = 1;
        } else {
            $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            if (!empty($isDownPaymentEnable) && !empty($user_order->is_downpayment)) {
                $params['isDownPaymentEnable'] = 1;
            } else {
                $params['isDownPaymentEnable'] = 0;
            }
        }

        $params['source_type'] = $sourceType;
        $currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $supportedCurrency =$gatewayPlugin->getSupportedCurrencies();
        $isSupported = true;
        if (!in_array($currentCurrency, $supportedCurrency))           
                   $isSupported = false;
       $this->view->isSupported = $isSupported;
       if($isSupported){
        // Process transaction
        $transaction = $plugin->createUserOrderTransaction($parent_order_id, $params, $this->_user);

        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData();

        unset($this->_session->user_order_id);
        $this->view->transactionMethod = $transactionMethod;
        // Handle redirection
        if ($transactionMethod == 'GET' && !Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
            if (isset($transaction->mangoPayRedirectUrl) && strlen($transaction->mangoPayRedirectUrl) > 0) {
                $transactionUrl = $transaction->mangoPayRedirectUrl;
            } elseif (isset($transaction->payKey)) {
                $transactionUrl .=$transaction->payKey;
                $this->_session->payKey = $transaction->payKey;
            } else {
                $transactionUrl .= '?' . http_build_query($transactionData);
            }

            return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
        }
    }
    }

    public function returnAction() {
 
        // Get order
        if (!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
                ($order->source_type != 'sitestoreproduct_order' && $order->source_type != 'sitestoreproduct_orderdownpayment') ||
                !($user_order = $order->getSource())) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $store_gateway_id = $this->_getParam('store_gateway_id');
        if (empty($store_gateway_id))
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $order->gateway_id);
        else
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_storegateway', $store_gateway_id);

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && $gateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive') {
            $gateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway($gateway->plugin);
        }

         
      

        
        if (!$gateway)
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        
        
        // Get gateway plugin
        $plugin = $gateway->getPlugin();

        unset($this->_session->errorMessage);

        try {
            if (!empty($this->_session->downpayment_make_payment)) {
                $params = array_merge($this->_getAllParams(), array('remainingAmountPayment' => 1));
            } else {
                $params = $this->_getAllParams();
            }

            if (isset($this->_session->payKey) && strlen($this->_session->payKey) > 0) {
                $params['payKey'] = $this->_session->payKey;
            }

            print_r($params);
            $status = $plugin->onUserOrderTransactionReturn($order, $params);

        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        if (!empty($this->_session->downpayment_make_payment)) {
            $this->_success->succes_id = $user_order->order_id;
        } else {
            $this->_success->succes_id = $user_order->parent_id;
        }
        return $this->_finishPayment($status);
    }

    protected function _finishPayment($state = 'active') {

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $remainingAmountPayment = $this->_session->downpayment_make_payment;
        $this->_session->unsetAll();
        $this->_session->errorMessage = $errorMessage;
        $this->_session->downpayment_make_payment = $remainingAmountPayment;

        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
        }
    }

    public function finishAction() {
        $session = new Zend_Session_Namespace('Sitestoreproduct_Order_Payment_Detail');

        if (!empty($session->sitestoreproductOrderPaymentDetail))
            $session->sitestoreproductOrderPaymentDetail = '';

        $paymentDetail = array('success_id' => $this->_success->succes_id, 'state' => $this->_getParam('state'), 'errorMessage' => $this->_session->errorMessage);

        if (!empty($this->_session->downpayment_make_payment)) {
            $paymentDetail = array_merge($paymentDetail, array('downpayment_make_payment' => 1));
            unset($this->_session->downpayment_make_payment);
        }

        $session->sitestoreproductOrderPaymentDetail = $paymentDetail;

        return $this->_helper->redirector->gotoRoute(array('action' => 'success'), 'sitestoreproduct_general', false);
    }

    public function detailTransactionAction() {
        $transaction_id = $this->_getParam('transaction_id', null);

        if (empty($transaction_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

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

}
