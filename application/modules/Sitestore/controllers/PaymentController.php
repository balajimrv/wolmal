<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_PaymentController extends Core_Controller_Action_Standard {

    /**
     * @var User_Model_User
     */
    protected $_user;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Payment_Model_Order
     */
    protected $_order;

    /**
     * @var Payment_Model_Gateway
     */
    protected $_gateway;

    /**
     * @var Sitestore_Model_Store
     */
    protected $_store;

    /**
     * @var Payment_Model_Package
     */
    protected $_package;
    protected $_success;

    public function init() {

        // Get user and session
        $this->_user = Engine_Api::_()->user()->getViewer();

        // If no user, redirect to home?
        if (!$this->_user || !$this->_user->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }
        // If there are no enabled gateways or packages, disable
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
                Engine_Api::_()->getDbtable('packages', 'sitestore')->getEnabledNonFreePackageCount() <= 0) {
            return $this->_forward('show-error');
        }
        $this->_session = new Zend_Session_Namespace('Payment_Sitestore');
        $this->_success = new Zend_Session_Namespace('Payment_Sitestore_Success');
    }

    public function indexAction() {
        return $this->_forward('gateway');
    }

    public function showErrorAction() {
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestoreproduct_main');
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
            $this->view->show = 1;
        } else {
            $this->view->show = 0;
        }
    }

    public function gatewayAction() {
        // Get subscription
        $storeId = $this->_getParam('store_id', $this->_session->store_id);
        if (!$storeId ||
                !($store = Engine_Api::_()->getItem('sitestore_store', $storeId))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }
        $this->view->store = $store;

        // Check subscription status
        if ($this->_checkStoreStatus($store)) {
            return;
        }

        $existManageAdmin = Engine_Api::_()->sitestore()->isStoreOwner($store);

        // Get subscription
        if (!$this->_user ||
                !( $storeId = $this->_getParam('store_id', $this->_session->store_id)) ||
                !($store = Engine_Api::_()->getItem('sitestore_store', $storeId)) ||
                !$existManageAdmin ||
                !($package = Engine_Api::_()->getItem('sitestore_package', $store->package_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }
        $this->view->store = $store;
        $this->view->package = $package;

        // Unset certain keys
        unset($this->_session->gateway_id);
        unset($this->_session->order_id);

        // Gateways
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()
                ->where('enabled = ?', 1)
        ;
        $gateways = $gatewayTable->fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) {
            if ($gateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay' || $gateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive')
                continue;
            // Check billing cycle support
            if (!$package->isOneTime()) {
                $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                    continue;
                }
            }
            $gatewayPlugins[] = array(
                'gateway' => $gateway,
                'plugin' => $gateway->getGateway(),
            );
        }
        $this->view->gateways = $gatewayPlugins;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestoreproduct_main');
    }

    public function processAction() {
        // Get gateway
        $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
        if (!$gatewayId ||
                !($gateway = Engine_Api::_()->getItem('sitestore_gateway', $gatewayId)) ||
                !($gateway->enabled)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
        }
        $this->view->gateway = $gateway;

        // Get subscription
        $storeId = $this->_getParam('store_id', $this->_session->store_id);
        if (!$storeId ||
                !($store = Engine_Api::_()->getItem('sitestore_store', $storeId))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }
        $this->view->store = $store;

         // Get package
        $package = $store->getPackage();
        if (!$package || $package->isFree()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }
        $this->view->package = $package;

        // Check subscription?
        if ($this->_checkStoreStatus($store)) {
            return;
        }

        // Process
        // Create order
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }
        $ordersTable->insert(array(
            'user_id' => $this->_user->getIdentity(),
            'gateway_id' => $gateway->gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'sitestore_store',
            'source_id' => $store->store_id,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        // Unset certain keys
        unset($this->_session->package_id);
        unset($this->_session->store_id);
        unset($this->_session->gateway_id);


        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();


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
        $localeParts = explode('_', $this->_user->language);
        if (count($localeParts) > 1) {
            $params['region'] = $localeParts[1];
        }
        $params['vendor_order_id'] = $order_id;
        $params['return_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestore'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
                . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitestore'), 'default')
                . '?order_id=' . $order_id
                . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
                . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'sitestore'), 'default')
                . '?order_id=' . $order_id;
        // Process transaction
        $transaction = $plugin->createStoreTransaction($this->_user, $store, $package, $params);

        // Pull transaction params
        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData();

        // Handle redirection
        if ($transactionMethod == 'GET') {
            $transactionUrl .= '?' . http_build_query($transactionData);
            return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
        }

        // Post will be handled by the view script
    }

    public function returnAction() {
        // Get order
        if (!$this->_user ||
                !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
                !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
                $order->user_id != $this->_user->getIdentity() ||
                $order->source_type != 'sitestore_store' ||
                !($store = $order->getSource()) ||
                !($package = $store->getPackage()) ||
                !($gateway = Engine_Api::_()->getItem('sitestore_gateway', $order->gateway_id))) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        }

        $this->_store = $store;
        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        // Process return
        unset($this->_session->errorMessage);
        try {
            $status = $plugin->onStoreTransactionReturn($order, $this->_getAllParams());
           
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }
        $this->_success->succes_id = $store->store_id;
        return $this->_finishPayment($status);
    }

    public function finishAction() {

        $this->view->status = $status = $this->_getParam('state');
        $this->view->error = $this->_session->errorMessage;
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestoreproduct_main');
        if (isset($this->_success->succes_id)) {
            $this->view->id = $this->_success->succes_id;
            unset($this->_success->succes_id);
        }
    }

    protected function _checkStoreStatus(
    Zend_Db_Table_Row_Abstract $store = null) {
        if (!$this->_user) {
            return false;
        }

        if (null == $store) {
            $store = Engine_Api::_()->getItem('sitestore_store', $this->_session->store_id);
        }


        if ($store->getPackage()->isFree()) {
            $this->_finishPayment('free');
            return true;
        }

        return false;
    }

    protected function _finishPayment($state = 'active') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $store = $this->_store;

        // No user?
//    if (!$this->_store) {
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }
// @todo: work all here Sitestore
        // Log the user in, if they aren't already
//    if (($state == 'active' || $state == 'free') &&
//        $this->_store &&
//        !$viewer->getIdentity()) {
//      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
//      Engine_Api::_()->user()->setViewer();
//      $viewer = $this->_user;
//    }
        // Handle email verification or pending approval
//    if ($viewer->getIdentity() && (!$viewer->enabled || !$viewer->verified)) {
//      Engine_Api::_()->user()->setViewer(null);
//      Engine_Api::_()->user()->getAuth()->getStorage()->clear();
//
//      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
//      $confirmSession->approved = $viewer->enabled;
//      $confirmSession->verified = $viewer->verified;
//      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
//    }
//
//    // Clear session
//    $errorMessage = $this->_session->errorMessage;
//    $userIdentity = $this->_session->user_id;
//    $this->_session->unsetAll();
//    $this->_session->user_id = $userIdentity;
//    $this->_session->errorMessage = $errorMessage;
        // Redirect

        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'controller' => 'payment', 'state' => $state), 'sitestore_extended', true);
        }
    }

}

?>
