<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PayPal.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Gateway_Store_PayPal extends Engine_Payment_Plugin_Abstract {

  protected $_gatewayInfo;
  protected $_gateway;
  // General

  /**
   * Constructor
   */
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
    $this->_gatewayInfo = $gatewayInfo;
  }

  /**
   * Get the service API
   *
   * @return Engine_Service_PayPal
   */
  public function getService() {
    return $this->getGateway()->getService();
  }

  /**
   * Get the gateway object
   *
   * @return Engine_Payment_Gateway
   */
  public function getGateway() {
    if (null === $this->_gateway) {
      $class = 'Engine_Payment_Gateway_PayPal';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
                  'config' => (array) $this->_gatewayInfo->config,
                  'testMode' => $this->_gatewayInfo->test_mode,
                  'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
              ));
      if (!($gateway instanceof Engine_Payment_Gateway)) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }
    return $this->_gateway;
  }

  // Actions

  /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params) {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @return Engine_Payment_Ipn
   */
  public function createIpn(array $params) {
    $ipn = new Engine_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }

  /**
   * Generate href to a page detailing the order
   *
   * @param string $transactionId
   * @return string
   */
  public function getOrderDetailLink($orderId) {
    // @todo make sure this is correct
    // I don't think this works
    if ($this->getGateway()->getTestMode()) {
      // Note: it doesn't work in test mode
      return 'https://www.sandbox.paypal.com/vst/?id=' . $orderId;
    } else {
      return 'https://www.paypal.com/vst/?id=' . $orderId;
    }
  }

  /**
   * Generate href to a page detailing the transaction
   *
   * @param string $transactionId
   * @return string
   */
  public function getTransactionDetailLink($transactionId) {
    // @todo make sure this is correct
    if ($this->getGateway()->getTestMode()) {
      // Note: it doesn't work in test mode
      return 'https://www.sandbox.paypal.com/vst/?id=' . $transactionId;
    } else {
      return 'https://www.paypal.com/vst/?id=' . $transactionId;
    }
  }

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
   * @return array
   */
  public function getOrderDetails($orderId) {
    // We don't know if this is a recurring payment profile or a transaction id,
    // so try both
    try {
      return $this->getService()->detailRecurringPaymentsProfile($orderId);
    } catch (Exception $e) {
      echo $e;
    }

    try {
      return $this->getTransactionDetails($orderId);
    } catch (Exception $e) {
      echo $e;
    }

    return false;
  }

  /**
   * Get raw data about a transaction
   *
   * @param $transactionId
   * @return array
   */
  public function getTransactionDetails($transactionId) {
    return $this->getService()->detailTransaction($transactionId);
  }

  // Forms

  /**
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  public function getAdminGatewayForm() {
    return new Payment_Form_Admin_Gateway_PayPal();
  }

  public function processAdminGatewayForm(array $values) {
    return $values;
  }

  // IPN

  /**
   * Process an IPN
   *
   * @param Engine_Payment_Ipn $ipn
   * @return Engine_Payment_Plugin_Abstract
   */
  public function onIpn(Engine_Payment_Ipn $ipn) {
    $rawData = $ipn->getRawData();

    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'communityad');

    // Find transactions
    $transactionId = null;
    $parentTransactionId = null;
    $transaction = null;
    $parentTransaction = null;

    // Fetch by txn_id
    if (!empty($rawData['txn_id'])) {
      $transaction = $transactionsTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_transaction_id = ?' => $rawData['txn_id'],
              ));
      $parentTransaction = $transactionsTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_parent_transaction_id = ?' => $rawData['txn_id'],
              ));
    }
    // Fetch by parent_txn_id
    if (!empty($rawData['parent_txn_id'])) {
      if (!$transaction) {
        $parentTransaction = $transactionsTable->fetchRow(array(
                    'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                    'gateway_parent_transaction_id = ?' => $rawData['parent_txn_id'],
                ));
      }
      if (!$parentTransaction) {
        $parentTransaction = $transactionsTable->fetchRow(array(
                    'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                    'gateway_transaction_id = ?' => $rawData['parent_txn_id'],
                ));
      }
    }
    // Fetch by transaction->gateway_parent_transaction_id
    if ($transaction && !$parentTransaction &&
            !empty($transaction->gateway_parent_transaction_id)) {
      $parentTransaction = $transactionsTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_parent_transaction_id = ?' => $transaction->gateway_parent_transaction_id,
              ));
    }
    // Fetch by parentTransaction->gateway_transaction_id
    if ($parentTransaction && !$transaction &&
            !empty($parentTransaction->gateway_transaction_id)) {
      $transaction = $transactionsTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_parent_transaction_id = ?' => $parentTransaction->gateway_transaction_id,
              ));
    }
    // Get transaction id
    if ($transaction) {
      $transactionId = $transaction->gateway_transaction_id;
    } else if (!empty($rawData['txn_id'])) {
      $transactionId = $rawData['txn_id'];
    }
    // Get parent transaction id
    if ($parentTransaction) {
      $parentTransactionId = $parentTransaction->gateway_transaction_id;
    } else if ($transaction && !empty($transaction->gateway_parent_transaction_id)) {
      $parentTransactionId = $transaction->gateway_parent_transaction_id;
    } else if (!empty($rawData['parent_txn_id'])) {
      $parentTransactionId = $rawData['parent_txn_id'];
    }

    // Fetch order
    $order = null;

    // Transaction IPN - get order by invoice
    if (!$order && !empty($rawData['invoice'])) {
      $order = $ordersTable->find($rawData['invoice'])->current();
    }

    // Transaction IPN - get order by parent_txn_id
    if (!$order && $parentTransactionId) {
      $order = $ordersTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_transaction_id = ?' => $parentTransactionId,
              ));
    }

    // Transaction IPN - get order by txn_id
    if (!$order && $transactionId) {
      $order = $ordersTable->fetchRow(array(
                  'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                  'gateway_transaction_id = ?' => $transactionId,
              ));
    }

    // Transaction IPN - get order through transaction
    if (!$order && !empty($transaction->order_id)) {
      $order = $ordersTable->find($parentTransaction->order_id)->current();
    }

    // Transaction IPN - get order through parent transaction
    if (!$order && !empty($parentTransaction->order_id)) {
      $order = $ordersTable->find($parentTransaction->order_id)->current();
    }


    // Process generic IPN data 
    // Build transaction info
    if (!empty($rawData['txn_id'])) {
      $transactionData = array(
          'gateway_id' => $this->_gatewayInfo->gateway_id,
      );
      // Get timestamp
      if (!empty($rawData['payment_date'])) {
        $transactionData['timestamp'] = date('Y-m-d H:i:s', strtotime($rawData['payment_date']));
      } else {
        $transactionData['timestamp'] = new Zend_Db_Expr('NOW()');
      }
      // Get amount
      if (!empty($rawData['mc_gross'])) {
        $transactionData['amount'] = $rawData['mc_gross'];
      }
      // Get currency
      if (!empty($rawData['mc_currency'])) {
        $transactionData['currency'] = $rawData['mc_currency'];
      }
      // Get order/user
      if ($order) {
        $transactionData['user_id'] = $order->user_id;
        $transactionData['order_id'] = $order->order_id;
      }
      // Get transactions
      if ($transactionId) {
        $transactionData['gateway_transaction_id'] = $transactionId;
      }
      if ($parentTransactionId) {
        $transactionData['gateway_parent_transaction_id'] = $parentTransactionId;
      }
      // Get payment_status
      switch ($rawData['payment_status']) {
        case 'Canceled_Reversal': // @todo make sure this works

        case 'Completed':
        case 'Created':
        case 'Processed':
          $transactionData['type'] = 'payment';
          $transactionData['state'] = 'okay';
          break;

        case 'Denied':
        case 'Expired':
        case 'Failed':
        case 'Voided':
          $transactionData['type'] = 'payment';
          $transactionData['state'] = 'failed';
          break;

        case 'Pending':
          $transactionData['type'] = 'payment';
          $transactionData['state'] = 'pending';
          break;

        case 'Refunded':
          $transactionData['type'] = 'refund';
          $transactionData['state'] = 'refunded';
          break;
        case 'Reversed':
          $transactionData['type'] = 'reversal';
          $transactionData['state'] = 'reversed';
          break;

        default:
          $transactionData = 'unknown';
          break;
      }

      // Insert new transaction
      if (!$transaction) {
        $transactionsTable->insert($transactionData);
      }
      // Update transaction
      else {
        unset($transactionData['timestamp']);
        $transaction->setFromArray($transactionData);
        $transaction->save();
      }

      // Update parent transaction on refund?
      if ($parentTransaction && in_array($transactionData['type'], array('refund', 'reversal'))) {
        $parentTransaction->state = $transactionData['state'];
        $parentTransaction->save();
      }
    }

    // Process specific IPN data 
    if ($order) {
      // Subscription IPN
      if ($order->source_type == 'userads') {
        $this->onUseradTransactionIpn($order, $ipn);
        return $this;
      }
      // Unknown IPN
      else {
        throw new Engine_Payment_Plugin_Exception('Unknown order type for IPN');
      }
    }
    // Missing order
    else {
      throw new Engine_Payment_Plugin_Exception('Unknown or unsupported IPN ' .
              'type, or missing transaction or order ID');
    }
  }

  /**
   * Create a transaction for user payment request
   *
   * @param User_Model_User $user
   * @param $request_id
   * @param array $params
   * @return Engine_Payment_Gateway_Transaction
   */
  public function createStoreBillTransaction($store_id, $bill_id, $params = array()) {
    //FETCH RESPONSE DETAIL
    $storeBillDetail = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'storebill_id = ?' => $bill_id));
    $store_title = Engine_Api::_()->getItem('sitestore_store', $store_id)->getTitle();
    
    $params['driverSpecificParams']['PayPal'] = array(
        'AMT' => round($storeBillDetail->amount, 2),
        'ITEMAMT' => round($storeBillDetail->amount, 2),
        'ITEMS' => array(
            array(
                'NAME' => $store_title,
                'DESC' => $storeBillDetail->message,
                'AMT' => round($storeBillDetail->amount, 2),
                ),
            ),
        'SOLUTIONTYPE' => 'sole',
        );

    // Create transaction
    $transaction = $this->createTransaction($params);

    return $transaction;
  }

  /**
   * Process return of user order transaction
   *
   * @param Payment_Model_Order $order
   * @param array $params
   */
  public function onStoreBillTransactionReturn(Payment_Model_Order $order, array $params = array() ) {

    $storeBill = $order->getSource();

    if ($storeBill->status == 'pending') {
      return 'pending';
    }

    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      // Cancel order and advertiesment?
      $order->onCancel();
      $storeBill->onPaymentFailure();

      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
              'not been charged. If this is not correct, please try again later.');
    }

    // Check params
    if (empty($params['token'])) {

      // Cancel order and advertiesment?
      $order->onFailure();
      $storeBill->onPaymentFailure();

      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
              'transaction. Please try again later.');
    }

    // Get details
    try {
      $data = $this->getService()->detailExpressCheckout($params['token']);
    } catch (Exception $e) {
      // Cancel order and advertiesment?
      $order->onFailure();
      $storeBill->onPaymentFailure();

      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
              'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
            . print_r($data, true), Zend_Log::INFO);

    // Do payment
    try {
      $rdata = $this->getService()->doExpressCheckoutPayment($params['token'],
                      $params['PayerID'], array(
                  'PAYMENTACTION' => 'Sale',
                  'AMT' => $data['AMT'],
                  'CURRENCYCODE' => $this->getGateway()->getCurrency(),
              ));
    } catch (Exception $e) {
      // Cancel order and advertiesment?
      $order->onFailure();
      $storeBill->onPaymentFailure();

      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
              'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
            . print_r($rdata, true), Zend_Log::INFO);

    // Get payment state
    $paymentStatus = null;
    $orderStatus = null;
    switch (strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS'])) {
      case 'created':
      case 'pending':
        $paymentStatus = 'pending';
        $orderStatus = 'complete';
        break;

      case 'completed':
      case 'processed':
      case 'canceled_reversal': // Probably doesn't apply
        $paymentStatus = 'okay';
        $orderStatus = 'complete';
        break;

      case 'denied':
      case 'failed':
      case 'voided': // Probably doesn't apply
      case 'reversed': // Probably doesn't apply
      case 'refunded': // Probably doesn't apply
      case 'expired':  // Probably doesn't apply
      default: // No idea what's going on here
        $paymentStatus = 'failed';
        $orderStatus = 'failed'; // This should probably be 'failed'
        break;
    }

    // Update order with profile info and complete status?
    $order->state = $orderStatus;
    $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
    $order->save();

    // Insert transaction
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
    $transactionsTable->insert(array(
        'user_id' => $order->user_id,
        'sender_type' => 2,
        'gateway_id' => 2,
        'date' => new Zend_Db_Expr('NOW()'),
        'payment_order_id' => $order->order_id,
        'parent_order_id' => $order->source_id,
        'type' => 'payment',
        'state' => $paymentStatus,
        'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
        'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
        'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE']
    ));

    // Get benefit setting
    $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')
                    ->getBenefitStatus($user);

    // Check payment status
    if ($paymentStatus == 'okay' ||
            ($paymentStatus == 'pending' && $giveBenefit)) {

      $storeBill->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

      // Payment success
      $storeBill->onPaymentSuccess();

      return 'active';
    } else if ($paymentStatus == 'pending') {

      // Update advertiesment info
      $storeBill->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

      // Payment pending
      $storeBill->onPaymentPending();

      return 'pending';
    } else if ($paymentStatus == 'failed') {
      // Cancel order and advertiesment?
      $order->onFailure();
      $storeBill->onPaymentFailure();
      // Payment failed
      throw new Payment_Model_Exception('Your payment could not be ' .
              'completed. Please ensure there are sufficient available funds ' .
              'in your account.');
    } else {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
              'transaction. Please try again later.');
    }
  }

  /**
   * Process ipn of advertiesment transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onUseradTransactionIpn(
  Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();
    $storeBill = $order->getSource();
    $package = $storeBill->getPackage();

    // Get IPN data
    $rawData = $ipn->getRawData();

    // Get tx table
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');


    // Chargeback --------------------------------------------------------------
    if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
      $storeBill->onPaymentFailure(); // or should we use pending?
    }

    // Transaction Type --------------------------------------------------------
    else if (!empty($rawData['txn_type'])) {
      switch ($rawData['txn_type']) {

        case 'express_checkout':

          switch ($rawData['payment_status']) {

            case 'Created': // Not sure about this one
            case 'Pending':
              // @todo this might be redundant
              // Get benefit setting
              $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'communityad')->getBenefitStatus($user);
              if ($giveBenefit) {
                $storeBill->onPaymentSuccess();
              } else {
                $storeBill->onPaymentPending();
              }
              break;

            case 'Completed':
            case 'Processed':
            case 'Canceled_Reversal': // Not sure about this one
              $storeBill->onPaymentSuccess();
              // send notification
              if ($storeBill->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                Engine_Api::_()->communityad()->sendMail("ACTIVE", $storeBill->userad_id);
              }
              break;

            case 'Denied':
            case 'Failed':
            case 'Voided':
            case 'Reversed':
              $storeBill->onPaymentFailure();
              // send notification
              if ($storeBill->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                Engine_Api::_()->communityad()->sendMail("OVERDUE", $storeBill->userad_id);
              }
              break;

            case 'Refunded':
              $storeBill->onRefund();
              // send notification
              if ($storeBill->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                Engine_Api::_()->communityad()->sendMail("REFUNDED", $storeBill->userad_id);
              }
              break;

            case 'Expired': // Not sure about this one
              $storeBill->onExpiration();
              // send notification
              if ($storeBill->didStatusChange()) {
                // SEND OVERDUE MAIL HERE
                Engine_Api::_()->communityad()->sendMail("EXPIRED", $storeBill->userad_id);
              }
              break;

            default:
              throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                              'payment status %1$s', $rawData['payment_status']));
              break;
          }

          break;


        // What is this?
        default:
          throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                          'type %1$s', $rawData['txn_type']));
          break;
      }
    }

    // Payment Status ----------------------------------------------------------
    else if (!empty($rawData['payment_status'])) {
      switch ($rawData['payment_status']) {

        case 'Created': // Not sure about this one
        case 'Pending':
          // Get benefit setting
          $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'communityad')->getBenefitStatus($user);
          if ($giveBenefit) {
            $storeBill->onPaymentSuccess();
          } else {
            $storeBill->onPaymentPending();
          }
          break;

        case 'Completed':
        case 'Processed':
        case 'Canceled_Reversal': // Not sure about this one
          $storeBill->onPaymentSuccess();
          // send notification
          if ($storeBill->didStatusChange()) {
            // SEND OVERDUE MAIL HERE
            Engine_Api::_()->communityad()->sendMail("ACTIVE", $storeBill->userad_id);
          }
          break;

        case 'Denied':
        case 'Failed':
        case 'Voided':
        case 'Reversed':
          $storeBill->onPaymentFailure();
          // send notification
          if ($storeBill->didStatusChange()) {
            // SEND OVERDUE MAIL HERE
            Engine_Api::_()->communityad()->sendMail("OVERDUE", $storeBill->userad_id);
          }
          break;

        case 'Refunded':
          $storeBill->onRefund();
          // send notification
          if ($storeBill->didStatusChange()) {
            // SEND OVERDUE MAIL HERE
            Engine_Api::_()->communityad()->sendMail("REFUNDED", $storeBill->userad_id);
          }
          break;

        case 'Expired': // Not sure about this one
          $storeBill->onExpiration();
          // send notification
          if ($storeBill->didStatusChange()) {
            // SEND OVERDUE MAIL HERE
            Engine_Api::_()->communityad()->sendMail("EXPIRED", $storeBill->userad_id);
          }
          break;

        default:
          throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                          'payment status %1$s', $rawData['payment_status']));
          break;
      }
    }

    // Unknown -----------------------------------------------------------------
    else {
      throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                      'data structure'));
    }

    return $this;
  }

  public function cancelUserad($transactionId, $note = null) {

    return $this;
  }

  // SEv4 Specific

  /**
   * Create a transaction for a subscription
   *
   * @param User_Model_User $user
   * @param Zend_Db_Table_Row_Abstract $storeBill
   * @param Zend_Db_Table_Row_Abstract $package
   * @param array $params
   * @return Engine_Payment_Gateway_Transaction
   */
  public function createSubscriptionTransaction(User_Model_User $user, Zend_Db_Table_Row_Abstract $storeBill, Payment_Model_Package $package, array $params = array()) {

  }

  /**
   * Process return of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param array $params
   */
  public function onSubscriptionTransactionReturn(
  Payment_Model_Order $order, array $params = array()) {

  }

  /**
   * Process ipn of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onSubscriptionTransactionIpn(
  Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

  }

  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @params $transactionId
   * @return Engine_Payment_Plugin_Abstract
   */
  public function cancelSubscription($transactionId, $note = null) {

  }

}