<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PayPalAdaptive.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

/**
 * All the methods defined in this file will be required to update as per your new payment gateway requirement. However for your better understanding, we have given some sample code under some methods of Stripe gateway. You have to change these method's code according to your gateway requirement.
 */
class Sitegateway_Plugin_Gateway_PayPalAdaptive extends Sitegateway_Plugin_Gateway_Abstract {

    protected $_gatewayInfo;
    protected $_gateway;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) {
        $this->_gatewayInfo = $gatewayInfo;
    }

    /**
     * Method to get the gateway object.
     *
     * @return Engine_Payment_Gateway
     */
    public function getGateway() {

        if (null === $this->_gateway) {
            $class = 'Engine_Payment_Gateway_PayPalAdaptive';
            Engine_Loader::loadClass($class);
            $gateway = new $class(array(
                'config' => (array) $this->_gatewayInfo->config,
                'testMode' => $this->_gatewayInfo->test_mode,
                'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
            ));
            if (!($gateway instanceof Engine_Payment_Gateway)) {
                throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
            }
            $this->_gateway = $gateway;
        }

        return $this->_gateway;
    }

    /**
     * You must to define this method to process a transaction. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.

     *
     * @param User_Model_User $user
     * @param Zend_Db_Table_Row_Abstract $resourceObject
     * @param Zend_Db_Table_Row_Abstract $package
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    protected function createResourceTransaction($user, $resourceObject, $package, $params = array()) {

        $params['fixed'] = true;
        $productInfo = $this->getService()->detailVendorProduct($package->getGatewayIdentity());
        if (!empty($productInfo)) {
            $params['product_id'] = $productInfo['product_id'];
        }
        $params['quantity'] = 1;

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Process return of an user after order transaction. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserOrderTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        $user_order = $order->getSource();
        $user = $order->getUser();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($user_order->payment_status == 'pending') {
            return 'pending';
        }
        // Check for cancel state - the user cancelled the transaction
        if ($params['state'] == 'cancel') {
            // Cancel order
            $order->onCancel();
            $user_order->onPaymentFailure();
            // Error
            throw new Payment_Model_Exception('Your payment has been cancelled and not been charged. If this is not correct, please try again later.');
        }

        $requestEnvelope = array(
            'errorLanguage' => 'en_US',
            'detailLevel' => 'ReturnAll'
        );

        $paypalSitegatewayMethod = $settings->getSetting('sitegateway.paypaladaptivepaymentmethod', 'split');


        if ($paypalSitegatewayMethod == 'escrow') {
            $approvalDetails = array(
                'preapprovalKey' => $params['preapprovalKey'],
                'requestEnvelope' => $requestEnvelope
            );
            $response = $this->_paypalSend($approvalDetails, 'PreapprovalDetails', 'Payment'); 

            //SAVE THE PAYMENT STATUS FOR USER ORDER (HERE approved IS TRUE OR FALSE ONLY)
            if ($response['approved'] == 'true') {
                $user_order->gateway_type = 'escrow';
                $user_order->payment_status = 'authorised';
                $user_order->preapproval_key = $params['preapprovalKey'];
                $user_order->save();
                $status = 'active';
            } else {
                $user_order->payment_status = 'failed';
                $user_order->save();
                $status = 'failed';
            }

            //DO THE WORK RELATED ANY SPECIFIC MODULE
            if ($order->source_type == 'sitecrowdfunding_backer') { 
                if ($status == 'active' && isset($params['reward_id']) && !empty($params['reward_id'])) {
                    $user_order->reward_id = $params['reward_id'];
                    $user_order->save();
                } 
            } elseif ($order->source_type == 'sitestoreproduct_order') {
                
            } 
            return $status;
        }

        $paymentDetailRequestPacket = array(
            'payKey' => $params['payKey'],
            'requestEnvelope' => $requestEnvelope
        );
        $response = $this->_paypalSend($paymentDetailRequestPacket, 'PaymentDetails', 'Payment');

        if ($response['status'] == 'ERROR' || $response['status'] == 'REVERSALERROR' || (isset($response['responseEnvelope']['ack']) && $response['responseEnvelope']['ack'] == 'Failure')) {
            // Cancel order
            $order->onCancel();
            $user_order->onPaymentFailure();
            // Error
            throw new Payment_Model_Exception('Your payment has been failed. Please try again later.');
        }
        // Let's log it
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($params, true), Zend_Log::INFO);
        $payout = "";
        if ($order->source_type == 'sitecrowdfunding_backer') {
            $payout = $settings->getSetting('sitecrowdfunding.payment.method', 'split');
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $payout = $settings->getSetting('sitestore.payment.method', 'split');
        } elseif ($order->source_type == 'siteeventticket_order') {
            $payout = $settings->getSetting('siteeventticket.payment.method', 'split');
        }
        $payout_status = '';
        switch (strtolower($response['status'])) {
            case 'created':
            case 'pending':
            case 'processing':
                $paymentStatus = 'pending';
                $orderStatus = 'complete';
                $payout_status = 'pending';
                break;

            case 'completed':
            case 'incomplete': //Incomplete in case of delayed payment

                $paymentStatus = 'okay';
                $orderStatus = 'complete';
                $payout_status = 'success';
                break;

            default: // No idea what's going on here
                $paymentStatus = 'failed';
                $orderStatus = 'failed'; // This should probably be 'failed'
                $payout_status = 'failed';
                break;
        }
        if (isset($user_order->gateway_type) && !empty($payout)) {
            $user_order->gateway_type = $payout;
            $user_order->save();
        }
        if ($payout == 'split') {
            if (isset($user_order->payout_status)) {
                $user_order->payout_status = $payout_status;
                $user_order->save();
            }
        } else {
            $payout_status = "";
        }

        $gateway_profile_id = $gateway_order_id = null;
        if (isset($response['paymentInfoList']) && isset($response['paymentInfoList']['paymentInfo']) && is_array($response['paymentInfoList']['paymentInfo'])) {
            if (isset($response['paymentInfoList']['paymentInfo'][0]['transactionId'])) {
                $gateway_profile_id = $gateway_order_id = $response['paymentInfoList']['paymentInfo'][0]['transactionId'];
            } else {
                $gateway_profile_id = $gateway_order_id = $response['paymentInfoList']['paymentInfo'][1]['transactionId'];
            }
        }
        // Update order with profile info and complete status?
        $order->state = $orderStatus;
        $order->gateway_order_id = $gateway_order_id;
        $order->save();
        $dateColumnName = "date";
        $amount = null;
        $otherColumns = array();
        //Insert transaction for (siteeventticket_order: "Tickets Purchase of Advanced Events" and sitestoreproduct_order: "Products Purchase of Stores / Marketplace")
        if ($order->source_type == 'siteeventticket_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_order') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        } elseif ($order->source_type == 'sitecrowdfunding_backer') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
            $orderTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
            $orderIdColumnName = 'source_id';
            $amount = $user_order->amount;
            $dateColumnName = "timestamp";
            $otherColumns = array('source_type' => $order->source_type);
            if ($order->source_type == 'sitecrowdfunding_backer' && ($paymentStatus == 'okay' || $paymentStatus == 'pending' ) && isset($params['reward_id']) && !empty($params['reward_id'])) {
                $user_order->reward_id = $params['reward_id'];
                $user_order->save();
            }
        }
        if (is_null($amount)) {
            $amount = $user_order->grand_total;
        }

        $transactionParams = array_merge(array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive')),
            "$dateColumnName" => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
                ), $otherColumns);


        $transactionsTable->insert($transactionParams);
        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type, 'resource_id' => $order->source_id, 'gateway_payment_key' => $params['payKey'], 'payout_status' => $payout_status));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user); 
        
        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            // Update order info
            $user_order->gateway_profile_id = $gateway_profile_id;
            $user_order->save();
            // Payment success
            $user_order->onPaymentSuccess();
            return 'active';
        } else if ($paymentStatus == 'pending') {
            // Update order info
            $user_order->gateway_id = $this->_gatewayInfo->gateway_id;
            $user_order->gateway_profile_id = $gateway_profile_id;
            // Payment pending
            $user_order->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order
            $order->onFailure();
            $user_order->onPaymentFailure();

            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your transaction. Please try again later.');
        }
    }

    /**
     * You must to define this method to process return of subscription transactions. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    protected function onResourceTransactionReturn(Payment_Model_Order $order, array $params = array()) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }

        // Get related info
        $user = $order->getUser();
        $moduleObject = $order->getSource();
        $package = $moduleObject->getPackage();

        $moduleName = explode("_", $package->getType());
        $moduleName = $moduleName['0'];

        /**
         * Here 'communityad' is a name of module for "Advertisements / Community Ads" plugin.
         */
        if ($moduleName == 'communityad') {
            // Check subscription state
            if ($moduleObject->payment_status == 'trial') {
                return 'active';
            } elseif ($moduleObject->payment_status == 'pending') {
                return 'pending';
            }
        } else {
            // Check subscription state
            if ($moduleObject->status == 'trial') {
                return 'active';
            } elseif ($moduleObject->status == 'pending') {
                return 'pending';
            }
        }

        $isOneTimeMethodExist = method_exists($package, 'isOneTime');

        // Check for processed
        if ($isOneTimeMethodExist && !$package->isOneTime()) {
            if (empty($params['subscription_id']) || empty($params['customer_id']) || (!empty($params['subscription_id']) && $params['subscription_id'] == 'undefined') || (!empty($params['customer_id']) && $params['customer_id'] == 'undefined')) {
                $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg2);
            }

            $gateway_order_id = $params['subscription_id'];
            $gateway_profile_id = $params['customer_id'];
        } else {
            if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
                $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
                throw new Payment_Model_Exception($error_msg2);
            }

            $gateway_order_id = $params['charge_id'];
            $gateway_profile_id = NULL;
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($params, true), Zend_Log::INFO);

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        /**
         * Here 'siteeventpaid' is a name of module for "Advanced Events - Events Booking, Tickets Selling & Paid Events" plugin.
         * Here 'sitereviewpaidlisting' is a name of module for "Multiple Listing Types - Paid Listings Extension" plugin.
         * You can leave empty such code of blocks if you have not enabled these plugins.
         */
        if ($moduleName == 'siteeventpaid' || $moduleName == 'sitereviewpaidlisting') {
            $parentPluginName = explode("_", $moduleObject->getType());
            $parentPluginName = $parentPluginName['0'];
            $otherinfo = Engine_Api::_()->getDbTable('otherinfo', $parentPluginName)->getOtherinfo($order->source_id);
            $otherinfo->gateway_id = $this->_gatewayInfo->gateway_id;
            $otherinfo->gateway_profile_id = $gateway_profile_id;
            $otherinfo->save();
        } elseif ($moduleName == 'communityad') {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_order_id;
            $moduleObject->save();
        } else {
            $moduleObject->gateway_id = $this->_gatewayInfo->gateway_id;
            $moduleObject->gateway_profile_id = $gateway_profile_id;
            $moduleObject->save();
        }

        // Insert transaction
        $transactionsTable = Engine_Api::_()->getDbtable('transactions', $moduleName);
        $transactionParams = array(
            'user_id' => $order->user_id,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive')),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'order_id' => $order->order_id,
            'type' => 'payment',
            'state' => 'okay',
            'gateway_transaction_id' => $gateway_order_id,
            'amount' => $package->price,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Enable now
        if ($giveBenefit) {

            //This is the same as sale_id  
            $moduleObject->onPaymentSuccess();

            // send notification
            if ($moduleObject->didStatusChange()) {

                /*
                 * Here 'payment_subscription' is used for SocialEngine signup subscription plans.
                 */
                if ($order->source_type == 'payment_subscription') {

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("ACTIVE", $moduleObject->getIdentity());
                }
            }

            return 'active';
        }

        // Enable later
        else {

            //This is the same as sale_id  
            $moduleObject->onPaymentPending();

            // send notification
            if ($moduleObject->didStatusChange()) {

                /*
                 * Here 'payment_subscription' is used for SocialEngine signup subscription plans.
                 */
                if ($order->source_type == 'payment_subscription') {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
                        'subscription_title' => $package->title,
                        'subscription_description' => $package->description,
                        'subscription_terms' => $package->getPackageDescription(),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                    ));
                } else {
                    Engine_Api::_()->$moduleName()->sendMail("PENDING", $moduleObject->getIdentity());
                }
            }

            return 'pending';
        }
    }

    /**
     * You must to define this method to process return of a site admin after commissions/bills payment (if you have enabled the "Payment to Website / Site Admin" flow). You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onResourceBillTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg1);
        }

        // Get related info
        $user = $order->getUser();
        $moduleBill = $order->getSource();

        $moduleName = explode("_", $moduleBill->getType());
        $moduleName = $moduleName['0'];

        // Check subscription state
        if ($moduleBill->status == 'trial') {
            return 'active';
        } else
        if ($moduleBill->status == 'pending') {
            return 'pending';
        }

        if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $moduleBill->gateway_id = $this->_gatewayInfo->gateway_id;
        $moduleBill->gateway_profile_id = $gateway_profile_id;
        $moduleBill->save();

        $paymentStatus = 'okay';

        /*
         * Here 'siteeventticket_eventbill' is used for "Advanced Events - Events Booking, Tickets Selling & Paid Events" commissions.
         * Here 'sitestoreproduct_storebill' is used for "Stores / Marketplace - Ecommerce" commissions. 
         */
        if ($order->source_type == 'siteeventticket_eventbill') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_storebill') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        }

        // Insert transaction
        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 2,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $moduleBill->amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            $moduleBill->gateway_profile_id = $gateway_profile_id;

            // Payment success
            $moduleBill->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            // Update advertiesment info
            $moduleBill->gateway_profile_id = $gateway_profile_id;

            // Payment pending
            $moduleBill->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $moduleBill->onPaymentFailure();
            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * Method to process payment transaction after payment request made by sellers (if you have enabled the "Direct Payment to Sellers" flow). You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param Payment_Model_Order $order
     * @param array $params
     */
    public function onUserRequestTransactionReturn(Payment_Model_Order $order, array $params = array()) {

        $user_request = $order->getSource();
        $user = $order->getUser();

        if ($user_request->payment_status == 'pending') {
            return 'pending';
        }

        if (empty($params['charge_id']) || (!empty($params['charge_id']) && $params['charge_id'] == 'undefined')) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_('There was an error processing your transaction. Please try again later.');
            throw new Payment_Model_Exception($error_msg2);
        }

        // Let's log it
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($params, true), Zend_Log::INFO);

        $gateway_profile_id = $gateway_order_id = $params['charge_id'];

        // Update order with profile info and complete status?
        $order->state = 'complete';
        $order->gateway_order_id = $gateway_order_id;
        $order->save();

        $user_request->payment_status = 'active';
        $user_request->gateway_profile_id = $gateway_order_id;
        $user_request->save();

        $paymentStatus = 'okay';

        //Insert transaction
        /*
         * Here 'siteeventticket_paymentrequest' is used for "Advanced Events - Events Booking, Tickets Selling & Paid Events" payment requests.
         * Here 'sitestoreproduct_paymentrequest' is used for "Stores / Marketplace - Ecommerce" payment requests. 
         */
        if ($order->source_type == 'siteeventticket_paymentrequest') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'siteeventticket');
            $orderIdColumnName = 'order_id';
        } elseif ($order->source_type == 'sitestoreproduct_paymentrequest') {
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
            $orderIdColumnName = 'parent_order_id';
        }

        $transactionParams = array(
            'user_id' => $order->user_id,
            'sender_type' => 1,
            'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive')),
            'date' => new Zend_Db_Expr('NOW()'),
            'payment_order_id' => $order->order_id,
            "$orderIdColumnName" => $order->source_id,
            'type' => 'payment',
            'state' => $paymentStatus,
            'gateway_transaction_id' => $gateway_profile_id,
            'amount' => $user_request->response_amount,
            'currency' => Engine_Api::_()->sitegateway()->getCurrency()
        );
        $transactionsTable->insert($transactionParams);

        $transactionParams = array_merge($transactionParams, array('resource_type' => $order->source_type));
        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);

        // Get benefit setting
        $giveBenefit = $transactionsTable->getBenefitStatus($user);

        // Check payment status
        if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) {

            $user_request->gateway_profile_id = $gateway_profile_id;

            // Payment success
            $user_request->onPaymentSuccess();

            return 'active';
        } else if ($paymentStatus == 'pending') {

            $user_request->gateway_profile_id = $gateway_profile_id;

            // Payment pending
            $user_request->onPaymentPending();

            return 'pending';
        } else if ($paymentStatus == 'failed') {
            // Cancel order and advertiesment?
            $order->onFailure();
            $user_request->onPaymentFailure();

            // Payment failed
            throw new Payment_Model_Exception('Your payment could not be completed due to some reason, Please contact to site admin.');
        } else {
            // This is a sanity error and cannot produce information a user could use to correct the problem.
            throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
        }
    }

    /**
     * You must to define this method for processing of IPN/Webhooks requests. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. Here, we have given some sample code for various actions performed under IPN/Webhooks. You need to update the code according to your gateway requirement.
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    protected function onResourceTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {

        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg7 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg7);
        }

        // Get related info
        $user = $order->getUser();
        $moduleObject = $order->getSource();
        $package = $moduleObject->getPackage();

        $moduleName = explode("_", $package->getType());
        $moduleName = $moduleName['0'];

        // Get IPN data
        $rawData = $ipn->getData();

        // switch message_type
        switch ($rawData['type']) {

            case 'charge.refunded':
                // Payment Refunded
                $moduleObject->onRefund();
                // send notification
                if ($moduleObject->didStatusChange()) {

                    /*
                     * Here 'payment' module is used for SocialEngine signup subscription plans.
                     */
                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_refunded', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("REFUNDED", $moduleObject->getIdentity());
                    }
                }

                break;

            case 'customer.subscription.deleted':

                $moduleObject->onCancel();
                // send notification
                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_cancelled', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("CANCELLED", $moduleObject->getIdentity());
                    }
                }
                break;

            case 'customer.subscription.updated':
                $moduleObject->onPaymentSuccess();

                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("RECURRENCE", $moduleObject->getIdentity());
                    }
                }

                $this->cancelSubscriptionOnExpiry($moduleObject, $package);

                break;

            case 'invoice.payment_failed':
                $moduleObject->onPaymentFailure();

                if ($moduleObject->didStatusChange()) {

                    if ($moduleName == 'payment') {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_overdue', array(
                            'subscription_title' => $package->title,
                            'subscription_description' => $package->description,
                            'subscription_terms' => $package->getPackageDescription(),
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
                        ));
                    } else {
                        Engine_Api::_()->$moduleName()->sendMail("OVERDUE", $moduleObject->getIdentity());
                    }
                }
                break;

            default:
                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                        'type %1$s', $rawData['type']));
                break;
        }

        return $this;
    }

    /**
     * You must to define this method to cancel a created package / sign-up subscription plan. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @params $transactionId
     * @return Engine_Payment_Plugin_Abstract
     */
    protected function cancelResourcePackage($transactionId, $note = null) {

        $profileId = null;

        if ($transactionId instanceof Siteevent_Model_Event) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($transactionId->getIdentity())->gateway_profile_id;
        } elseif ($transactionId instanceof Sitereview_Model_Listing) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = Engine_Api::_()->getDbTable('otherinfo', 'sitereview')->getOtherinfo($transactionId->getIdentity())->gateway_profile_id;
        } elseif (($transactionId instanceof Sitepage_Model_Page) || ($transactionId instanceof Sitebusiness_Model_Business) || ($transactionId instanceof Sitegroup_Model_Group) || ($transactionId instanceof Sitestore_Model_Store) || ($transactionId instanceof Payment_Model_Subscription)) {
            $package = $transactionId->getPackage();
            if ($package->isOneTime()) {
                return $this;
            }
            $profileId = $transactionId->gateway_profile_id;
        } else if (is_string($transactionId)) {
            $profileId = $transactionId;
        } else {

            return $this;
        }

        try {
            $r = $this->getService()->cancelRecurringPaymentsProfile($profileId, $note);
        } catch (Exception $e) {
            
        }

        return $this;
    }

    /**
     * Common method for cancelling a package/sign-up subscription on its expiry. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @params $moduleObject
     * @params $package
     * @return Engine_Payment_Plugin_Abstract
     */
    public function cancelSubscriptionOnExpiry($moduleObject, $package) {

        if ($package->isOneTime() || empty($package->duration) || empty($package->duration_type) || $package->duration_type == 'forever') {
            return $this;
        }

        $totalDaysPerCycle = $package->recurrence * Engine_Api::_()->sitegateway()->totalDaysInPeriod($package->recurrence_type);

        $totalDays = $package->duration * Engine_Api::_()->sitegateway()->totalDaysInPeriod($package->duration_type);

        $diff_days = round(strtotime(date('Y-m-d H:i:s')) - strtotime($moduleObject->creation_date)) / 86400;
        $remainingDays = round($totalDays - $diff_days);

        if ($remainingDays < 0) {
            return $this;
        }

        if ($remainingDays < $totalDaysPerCycle) {

            $moduleName = explode("_", $moduleObject->getType());
            $moduleName = $moduleName['0'];

            if ($moduleName == 'siteevent' || $moduleName == 'sitereview') {
                $profileId = Engine_Api::_()->getDbTable('otherinfo', $moduleName)->getOtherinfo($moduleObject->getIdentity())->gateway_profile_id;
            } elseif ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore' || $moduleName == 'payment') {
                $profileId = $moduleObject->gateway_profile_id;
            } else {
                return $this;
            }

            try {

                $r = $this->getService()->cancelRecurringPaymentsProfile($profileId);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onUserOrderTransactionIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        // Check that gateways match
        if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
            $error_msg7 = Zend_Registry::get('Zend_Translate')->_('Gateways do not match');
            throw new Engine_Payment_Plugin_Exception($error_msg7);
        }

        // Get related info
        $user = $order->getUser();
        $user_order = $order->getSource();

        // Get IPN data
        $rawData = $ipn->getData();
        // switch message_type
        switch ($rawData['type']) {

            case 'charge.refunded':
                // Payment Refunded
                $user_order->onRefund();

                break;

            default:
                throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
                        'type %1$s', $rawData['message_type']));
                break;
        }

        return $this;
    }

    /**
     * Create a transaction object from given parameters. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @return Engine_Payment_Transaction
     */
    public function createResourceBillTransaction($object_id, $bill_id, $params = array()) {

        $params['product_id'] = $object_id;
        $params['quantity'] = 1;

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    public function _paypalSend($packet, $call, $type) {
        return $this->getService()->_paypalSend($packet, $call, $type);
    }

    public function payout($payKey) {
        $data = array(
            'payKey' => $payKey,
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            )
        );
        $response = $this->_paypalSend($data, 'ExecutePayment', 'Payment');
        return $response;
    }

    public function refundOrder($preapprovalKey) {
        $data = array(
            'preapprovalKey' => $preapprovalKey, 
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            )
        );
        $response = $this->_paypalSend($data, 'CancelPreapproval', 'Payment');
        return $response;
    }

    public function payoutOrder($user_order, $params) {

        $sitegatewayApi = Engine_Api::_()->sitegateway();
        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $sellerGateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $user_order->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
        }
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
        $settingApi = Engine_Api::_()->getApi('settings', 'core');

        //Find Primary Receiver
        $pRecv = 1; //$settingApi->getSetting('sitegateway.paypaladaptiveprimaryreceiver', 1);
        $pReceiver = "seller";
        if ($pRecv == 2) {
            $pReceiver = "siteowner";
        }

        //Find setting value who will pay the paypal fee 
        $feespr = $settingApi->getSetting('sitegateway.paypaladaptivechangemethod', 1);
        $feesPayer = 'EACHRECEIVER';
        if ($feespr == 1) {
            $feesPayer = 'PRIMARYRECEIVER';
        } elseif ($feespr == 2) {
            $feesPayer = 'SECONDARYONLY';
        }

        $amount = $user_order->amount;
        $sellerEmailId = $sellerGateway->email;
        $siteownerEmailId = $adminGateway->config['email'];
        //building receiverlist
        $receivers = array();
        if ($pReceiver == 'siteowner') {
            $siteownerPrimary = true;
            $sellerPrimary = false;
            $siteownerAmt = $amount;
            $sellerAmt = ($amount - $user_order->commission_value);
        } else {
            $siteownerPrimary = false;
            $sellerPrimary = true;
            $siteownerAmt = $user_order->commission_value;
            $sellerAmt = $amount;
        }
        $receivers = array(
            array(
                'email' => $siteownerEmailId,
                'amount' => $siteownerAmt,
                'primary' => $siteownerPrimary
            ),
            array(
                'email' => $sellerEmailId,
                'amount' => $sellerAmt,
                'primary' => $sellerPrimary
            )
        );
        $data = array(
            'actionType' => 'PAY',
            'currencyCode' => $sitegatewayApi->getCurrency(),
            'feesPayer' => $feesPayer,
            'preapprovalKey' => $user_order->preapproval_key,
            'receiverList' => array(
                'receiver' => $receivers
            ),
            'returnUrl' => 'https://www.socialengineaddons.com/', //No need for this url but paypal give error if we left blank
            'cancelUrl' => 'https://www.socialengineaddons.com/', //No need for this url but paypal give error if we left blank
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            ),
            'reverseAllParallelPaymentsOnError' => true
        );
        $response = $this->_paypalSend($data, 'Pay', 'Payment');
        return $response;
    }

    //CROWDFUNDING BACKER PREAPPROVAL FOR BACKED AMOUNT
    public function userOrderPreApproval($order_id, $params) {

        if ($params['source_type'] == 'sitecrowdfunding_backer') {
            $order = Engine_Api::_()->getItem('sitecrowdfunding_backer', $order_id);
        } else if ($params['source_type'] == 'sitestoreproduct_order') {
            
        }

        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $requestEnvelope = array(
            'errorLanguage' => 'en_US',
            'detailLevel' => 'ReturnAll'
        );

        // 1 YEAR TIME FOR PREAPPROVAL
        $days = 365;
        $startDate = date('Y-m-d\Z');
        $endDate = date("Y-m-d\Z", strtotime('+'.$days.'days'));

        $createPacket = array(
            'currencyCode' => $sitegatewayApi->getCurrency(),
            'startingDate' => $startDate,
            'endingDate' => $endDate,
            'maxTotalAmountOfAllPayments' => $params['amount'],
            'memo' => $params['description'],
            'returnUrl' => $params['return_url'],
            'cancelUrl' => $params['cancel_url'],
            'requestEnvelope' => $requestEnvelope
        );

        $response = $this->_paypalSend($createPacket, 'Preapproval', 'Payment');
        if (isset($response['error']) && isset($response['error'][0]['message'])) {
            throw new Payment_Model_Exception('Your preapproval has been failed.' . $response['error'][0]['message']);
        }
        if (!isset($response['preapprovalKey'])) {
            throw new Payment_Model_Exception('Your preapproval has been failed.');
        }
        return $response['preapprovalKey'];
    }

    /**
     * Method to create a transaction for an order. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param $parent_order_id
     * @param array $params
     * @param User_Model_User $user
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserOrderTransaction($parent_order_id, array $params = array(), $user = NULL) {
        $order = Engine_Api::_()->getItem($params['source_type'], $parent_order_id);
        $settingApi = Engine_Api::_()->getApi('settings', 'core');
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $payoutMethod = $settingApi->getSetting('sitegateway.paypaladaptivepaymentmethod', 'split');
        $callingMethod = '';
        if ($params['source_type'] == 'siteeventticket_order') {
            if ($payoutMethod == 'escrow') {
                throw new Payment_Model_Exception('Escrow payment method is not implemented in Advanced Event plugin');
            }
            $sellerGateway = Engine_Api::_()->getDbtable('gateways', 'siteeventticket')->fetchRow(array('event_id = ?' => $order->event_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
            $item = Engine_Api::_()->getItem('siteevent_event', $order->event_id);
            $identifier = 'E_' . $order->order_id;
            $description = "Book ticket for event " . $item->getTitle();
            $totalAmt = $order->grand_total;
            $commissionAmt = ($totalAmt - $order->commission_value);
            $is_primary_recv = array(
                'siteowner' => array(
                    'siteowner' => array('title' => 'Sale of Event ticket on behalf of Seller', 'desc' => ''),
                    'seller' => array('title' => 'Sale of Event ticket', 'desc' => ''),
                ),
                'seller' => array(
                    'siteowner' => array('title' => 'Commision for Event ticket', 'desc' => ''),
                    'seller' => array('title' => 'Sale of Event ticket', 'desc' => ''),
                )
            );
        } elseif ($params['source_type'] == 'sitestoreproduct_order') {
            $sellerGateway = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $order->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
            $item = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
            $identifier = 'S_' . $order->order_id;
            $description = "Buy of product";
            $is_primary_recv = array(
                'siteowner' => array(
                    'siteowner' => array('title' => 'Sale of Store product on behalf of Seller', 'desc' => ''),
                    'seller' => array('title' => 'Sale of Store product', 'desc' => ''),
                ),
                'seller' => array(
                    'siteowner' => array('title' => 'Commision for Store product', 'desc' => ''),
                    'seller' => array('title' => 'Sale of Store product', 'desc' => ''),
                )
            );
        } elseif ($params['source_type'] == 'sitecrowdfunding_backer') {
            $sellerGateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $order->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $order->project_id);
            $params['amount'] = $amount = $order->amount;
            $identifier = 'B_' . $order->backer_id;
            $params['description'] = $description = "Back on project " . $item->title;
            $is_primary_recv = array(
                'siteowner' => array(
                    'siteowner' => array('title' => 'Back amount received for Crowdfunding Project on the behalf of seller', 'desc' => ''),
                    'seller' => array('title' => 'Back on Crowdfunding Project', 'desc' => ''),
                ),
                'seller' => array(
                    'siteowner' => array('title' => 'Commision for Crowdfunding Project', 'desc' => ''),
                    'seller' => array('title' => 'Back on Crowdfunding Project', 'desc' => ''),
                )
            );
            //CALL THIS FUNCTION TO HAVE USER'S PREAPPROVAL FOR FUTURE PAYMENTS 

            if ($payoutMethod == 'escrow') {
                $preapprovalKey = $this->userOrderPreApproval($order->backer_id, $params);
                $callingMethod = 'preapproval';
            }
        }

        if ($callingMethod != 'preapproval') {
            if (!isset($amount))
                $amount = $order->grand_total;


            $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
            $actionType = 'PAY_PRIMARY';
            if ($payoutMethod == 'split') {
                $actionType = 'PAY';
            }

            //Find Primary Receiver
            $pRecv = 1; //$settingApi->getSetting('sitegateway.paypaladaptiveprimaryreceiver', 1);
            $pReceiver = "seller";
            if ($pRecv == 2) {
                $pReceiver = "siteowner";
            }
            //Find setting value who will pay the paypal fee
            //Fee payer
            $feespr = $settingApi->getSetting('sitegateway.paypaladaptivechangemethod', 1);
            $feesPayer = 'EACHRECEIVER';
            if ($feespr == 1) {
                $feesPayer = 'PRIMARYRECEIVER';
            } elseif ($feespr == 2) {
                $feesPayer = 'SECONDARYONLY';
            }
            $sellerEmailId = $sellerGateway->email;
            $siteownerEmailId = $adminGateway->config['email'];

            $shippingCharge = 0;
            if (isset($order->shipping_price) && !empty($order->shipping_price)) {
                $shippingCharge = $order->shipping_price;
            }
            //building receiverlist
            $receivers = array();
            if ($pReceiver == 'siteowner') {
                $siteownerPrimary = true;
                $sellerPrimary = false;
                $siteownerAmt = $amount;
                $sellerAmt = ($amount - $order->commission_value);
            } else {
                $siteownerPrimary = false;
                $sellerPrimary = true;
                $siteownerAmt = $order->commission_value;
                $sellerAmt = $amount;
            }
            $receivers = array(
                array(
                    'email' => $siteownerEmailId,
                    'amount' => $siteownerAmt,
                    'primary' => $siteownerPrimary
                ),
                array(
                    'email' => $sellerEmailId,
                    'amount' => $sellerAmt,
                    'primary' => $sellerPrimary
                )
            );

            $receiverOptions = array(
                array(
                    'receiver' => array('email' => $siteownerEmailId),
                    'invoiceData' => array(
                        'item' => array(
                            array(
                                'name' => $is_primary_recv[$pReceiver]['siteowner']['title'],
                                'identifier' => $identifier,
                                'price' => $siteownerAmt,
                                'itemPrice' => $siteownerAmt,
                                'itemCount' => 1
                            )
                        ),
                    ),
                    'description' => $is_primary_recv[$pReceiver]['siteowner']['desc']
                ),
                array(
                    'receiver' => array('email' => $sellerEmailId),
                    'invoiceData' => array(
                        'item' => array(
                            array(
                                'name' => $is_primary_recv[$pReceiver]['seller']['title'],
                                'identifier' => $identifier,
                                'price' => ($sellerAmt - $shippingCharge),
                                'itemPrice' => ($sellerAmt - $shippingCharge),
                                'itemCount' => 1
                            )
                        ),
                        'totalShipping' => $shippingCharge
                    ),
                    'description' => $is_primary_recv[$pReceiver]['seller']['desc']
                ),
            );

            $requestEnvelope = array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            );
            $createPacket = array(
                'actionType' => $actionType,
                'currencyCode' => $sitegatewayApi->getCurrency(),
                'feesPayer' => $feesPayer,
                'receiverList' => array(
                    'receiver' => $receivers
                ),
                'memo' => $description,
                'returnUrl' => $params['return_url'],
                'cancelUrl' => $params['cancel_url'],
                'requestEnvelope' => $requestEnvelope
            );


            $response = $this->_paypalSend($createPacket, 'Pay', 'Payment');
            if (isset($response['error']) && isset($response['error'][0]['message'])) {
                throw new Payment_Model_Exception('Your payment has been failed.' . $response['error'][0]['message']);
            }
            if (!isset($response['payKey'])) {
                throw new Payment_Model_Exception('Your payment has been failed.');
            }
            $payKey = $response['payKey'];
            //SET PAYMENT DETAILS
            $detailsPacket = array(
                'requestEnvelope' => $requestEnvelope,
                'payKey' => $payKey,
                'receiverOptions' => $receiverOptions
            );
            $response = $this->_paypalSend($detailsPacket, 'SetPaymentOptions', 'Payment');

            $params['payKey'] = $payKey;
        } else {
            $params['preapprovalKey'] = $preapprovalKey;
        }

        //Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Method to create a transaction for a payment request made by sellers. You need to write the code accordingly in this method if you have enabled "Advanced Events - Events Booking, Tickets Selling & Paid Events" and / or "Stores / Marketplace - Ecommerce" plugins.
     *
     * @param User_Model_User $user
     * @param $request_id
     * @param array $params
     * @return Engine_Payment_Gateway_Transaction
     */
    public function createUserRequestTransaction(User_Model_User $user, $request_id, array $params = array()) {

        //FETCH RESPONSE DETAIL
        if ($params['source_type'] == 'siteeventticket_paymentrequest') {
            $response = Engine_Api::_()->getDbtable('paymentrequests', 'siteeventticket')->getResponseDetail($request_id);
        } elseif ($params['source_type'] == 'sitestoreproduct_paymentrequest') {
            $response = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getResponseDetail($request_id);
        }

        $params = array_merge($params, array(
            'AMT' => @round($response[0]['response_amount'], 2),
            'ITEMAMT' => @round($response[0]['response_amount'], 2),
            'SOLUTIONTYPE' => 'sole',
        ));

        // Create transaction
        $transaction = $this->createTransaction($params);

        return $transaction;
    }

    /**
     * Method to process an IPN/Webhooks request for an order transaction.
     *
     * @param Engine_Payment_Ipn $ipn
     * @return Engine_Payment_Plugin_Abstract
     */
    public function onIpn(Engine_Payment_Ipn $ipn) {

        //You can get the notification data from below code
        $rawData = $ipn->getData();

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');

        $order = null;

        //You need to fetch the order object using order id sent by this gateway in notification. For reference you can see the below code for Stripe: Transaction IPN - get order by subscription_id
        if (!$order && !empty($rawData['data']->object->id)) {
            $gateway_order_id = $rawData['data']->object->id;

            $order = $ordersTable->fetchRow(array(
                'gateway_id = ?' => $this->_gatewayInfo->gateway_id,
                'gateway_order_id = ?' => $gateway_order_id,
            ));
        }

        if ($order) {
            return $this->onResourceIpn($order);
        } else {
            $error_msg17 = Zend_Registry::get('Zend_Translate')->_('Unknown or unsupported IPN type, or missing transaction or order ID');
            throw new Engine_Payment_Plugin_Exception($error_msg17);
        }
    }

    /**
     * Method to generate link for an order details page using orderId.
     *
     * @param string $orderId
     * @return string
     */
    public function getOrderDetailLink($orderId) {
        
    }

    /**
     * Method to generate link for an order details page using transactionId.
     *
     * @param string $transactionId
     * @return string
     */
    public function getTransactionDetailLink($transactionId) {
        if ($this->getGateway()->getTestMode()) {
            return 'https://www.sandbox.paypal.com/vst/?id=' . $transactionId;
        } else {
            return 'https://www.paypal.com/vst/?id=' . $transactionId;
        }
    }

    /**
     * Get raw data about an order or recurring payment profile.
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderDetails($orderId) {

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
     * Get raw data about a transaction.
     *
     * @param $transactionId
     * @return array
     */
    public function getTransactionDetails($transactionId) {
        return $this->getService()->detailTransaction($transactionId);
    }

    /**
     * Get the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function getAdminGatewayForm() {
        return new Sitegateway_Form_Admin_Gateway_PayPalAdaptive();
    }

    /**
     * Process the form for editing the gateway credentials.
     *
     * @return Engine_Form
     */
    public function processAdminGatewayForm(array $values) {
        return $values;
    } 

    public function payoutAllPreapproval($params) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');

        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
            $orders = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getAllBackers(array('project_id' => $params['project_id'], 'gateway_id' => $adminGateway->gateway_id));
        }

        $success = false;
        $failed = false;
        foreach ($orders as $k => $order) {
            if (empty($order->preapproval_key))
                continue;
            if ($params['resource_type'] == 'sitecrowdfunding_backer') {
                $params['resource_id'] = $order->backer_id;
            }
            $data = $this->payoutPreapproval($params);
            if ($data['return'] == 0) {
                $failed = true;
            } else {
                $success = true;
            }
        }
        
        $item->payout_status = 'payout';
        $item->save();
        $message = ""; 
        if ($success && $failed) { 
            //Partially Success
            $message = $view->translate("Preapproval Partially payout in Paypal");
        } elseif ($success && $failed == false) {
            //all success 
            $message = $view->translate("All Preapproval successfully payout in Paypal");
        } elseif ($success == false && $failed == true) {
            //failed 
            $message = $view->translate("All Preapproval payout failed in Paypal");
        } 
        return $message;
    }

    public function payoutPreapproval($params) {   
        //Get Order model
        $user_order = Engine_Api::_()->getItem($params['resource_type'], $params['resource_id']);
        $data = array('return' => 1, 'message' => 'Success');
        if (empty($user_order)) {
            $data['return'] = 3;
            $data['message'] = 'Order not found';
            return $data;
        }
        $response = array();
        if ($user_order->payout_status != 'success') { 
             $response = $this->payoutOrder($user_order, $params); 
        } else {
            $data['return'] = 1;
            $data['message'] = 'Payout is already done for this Transaction.';
            return $data;
        }
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($response, true), Zend_Log::INFO);
        $payout_status = '';
        if (!empty($response)) { 
            if ($response['responseEnvelope']['ack'] == 'Failure') {
                $data['return'] = 0;
                $data['message'] = isset($response['error'][0]['message']) ? $response['error'][0]['message'] : ''; 
                } else {
                    switch (strtolower($response['paymentExecStatus'])) {
                        case 'created':
                        case 'pending':
                        case 'processing':
                            $paymentStatus = 'pending';
                            $orderStatus = 'complete';
                            $payout_status = 'pending';
                            $data['return'] = 2;
                            $data['message'] = 'Pending';
                            break;

                        case 'completed':
                            $paymentStatus = 'okay';
                            $orderStatus = 'complete';
                            $payout_status = 'success';
                            break;
                        case 'incomplete' :
                        case 'error' :
                        case 'reversalerror' :
                            $payout_status = $orderStatus = $paymentStatus = 'failed';
                            $data['return'] = 0;
                            $data['message'] = 'Failed';
                            break;
                    } 
                
                //GATEWAY TYPE EITHER ESCROW OR SPLIT
                $user_order->payout_status = $payout_status;
                $user_order->save();
                 
                if ($params['resource_type'] == 'sitecrowdfunding_backer') {
                    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
                    $orderIdColumnName = 'payment_order_id';
                    $order = Engine_Api::_()->getItem('payment_order', $user_order->order_id);
                    $amount = $user_order->amount;
                } elseif ($params['resource_type'] == 'sitestoreproduct_order') {
                    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct');
                    $orderIdColumnName = 'parent_order_id';
                }
                if (is_null($amount)) {
                    $amount = $user_order->grand_total;
                }

                $gateway_profile_id = $gateway_order_id = $response['payKey'];
                $order->state = $orderStatus;
                $order->gateway_order_id = $gateway_order_id;
                $order->save();

                // Get benefit setting
                $giveBenefit = $transactionsTable->getBenefitStatus($user);

                // Check payment status
                if ($paymentStatus == 'okay' || ($paymentStatus == 'pending' && $giveBenefit)) { 
                    // Update order info
                    $user_order->gateway_profile_id = $gateway_profile_id;
                    $user_order->save(); 
                    // Payment success
                    $user_order->onPaymentSuccess();
                } else if ($paymentStatus == 'pending') { 
                    // Update order info 
                    $user_order->gateway_profile_id = $gateway_profile_id;
                    $user_order->save();
                    // Payment pending
                    $user_order->onPaymentPending();  
                } else if ($paymentStatus == 'failed') {
                    // Cancel order
                    $order->onFailure();
                    $user_order->onPaymentFailure(); 
                }

                $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding');
                //STORE THE PAYKEY AS TRANSACTION ID BECAUSE THERE ARE TWO TRANSACTION KEY FOR THIS PAYOUT THAN WHICH ONE TO STORE 
                $transactionParams = array(
                    'user_id' => $order->user_id,
                    'gateway_id' => Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'gateway_id', 'plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive')),
                    "timestamp" => new Zend_Db_Expr('NOW()'),
                    "$orderIdColumnName" => $order->order_id,
                    "source_id" => $order->source_id,
                    'type' => 'payment',
                    'state' => $paymentStatus,
                    'gateway_transaction_id' => $gateway_profile_id,
                    'amount' => $amount,
                    'currency' => Engine_Api::_()->sitegateway()->getCurrency(),
                    'source_type' => $order->source_type
                );

                $transactionsTable->insert($transactionParams);
                $transactionParams = array_merge($transactionParams, array('resource_type' => $params['resource_type'], 'resource_id' => $order->source_id, 'gateway_payment_key' => $response['payKey'], 'payout_status' => $payout_status));
                Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
            }
        }
        return $data;
    }



    public function refundAllPreapproval($params) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');

        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
            $orders = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getAllBackers(array('project_id' => $params['project_id'], 'gateway_id' => $adminGateway->gateway_id));
        }

        $success = false;
        $failed = false;
        foreach ($orders as $k => $order) {
            if (empty($order->preapproval_key))
                continue;
            if ($params['resource_type'] == 'sitecrowdfunding_backer') {
                $params['resource_id'] = $order->backer_id;
            }
            $data = $this->refundPreapproval($params);
            if ($data['return'] == 0) {
                $failed = true;
            } else {
                $success = true;
            }
        }
        
        $item->payout_status = 'refund';
        $item->save();
        $message = ""; 
        if ($success && $failed) { 
            //Partially Success
            $message = $view->translate("Preapproval Partially refund in Paypal");
        } elseif ($success && $failed == false) {
            //all success 
            $message = $view->translate("All Preapproval successfully refund in Paypal");
        } elseif ($success == false && $failed == true) {
            //failed 
            $message = $view->translate("All Preapproval refund failed in Paypal");
        } 
        return $message; 
    }

    public function refundPreapproval($params) {

        //Get Order model
        $user_order = Engine_Api::_()->getItem($params['resource_type'], $params['resource_id']);
        $data = array('return' => 1, 'message' => 'Success');
        if (empty($user_order)) {
            $data['return'] = 3;
            $data['message'] = 'Order not found';
            return $data;
        }
        $response = array();
        if ($user_order->refund_status != 'success') {

            $response = $this->refundOrder($user_order->preapproval_key);  
         } else {
            $data['return'] = 1;
            $data['message'] = 'Refund is already done for this Order.';
            return $data;
        }
        $this->getGateway()->getLog()->log('Return (PayPalAdaptive): '
                . print_r($response, true), Zend_Log::INFO);
  
        
        if (!empty($response)) {
            if ($response['responseEnvelope']['ack'] == 'Failure' || $response['responseEnvelope']['ack'] == 'FailureWithWarning') {
                $refund_status = 'failed';
                $data['return'] = 0;
                $data['message'] = 'Failed';
            } else {
               $refund_status = 'success';
               $user_order->onRefund();
            } 
            $user_order->refund_status = $refund_status; 
            $user_order->save();
        }
        return $data;
    }


        /*
     * Call this function via Admin gateway
     */

    public function refundAllTransaction($params) {
        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
        $payoutArr = array();
        $transactions = Engine_Api::_()->getDbtable('transactions', 'sitegateway')->getAllTransactions(array('project_id' => $params['project_id'], 'gateway_id' => $adminGateway->gateway_id, 'gateway_payment_key' => '', 'resource_type' => $params['resource_type']));
        $success = false;
        $failed = false;
        foreach ($transactions as $k => $transaction) {
            $data = $this->refundTransaction($params, $transaction);

            if ($data['return'] == 0) {

                $failed = true;
            } else {
                $success = true;
            }
        }
        $item->payout_status = 'refund';
        $item->save();
        $message = "";
        if ($success && $failed) {
            //Partially Success
            $message = $view->translate("Partially transaction refund in Paypal");
        } elseif ($success == true && $failed == false) {
            //all success
            $message = $view->translate("All transaction successfully refund in Paypal");
        } elseif ($success == false && $failed == true) {
            //failed
            $message = $view->translate("All transaction failed refund in Paypal");
        }
        return $message;
    }

    public function refundTransaction($params, $transaction = null) {
        $settingApi = Engine_Api::_()->sitegateway();
        $data = array('return' => 1, 'message' => 'Success');
        if (empty($transaction) && isset($params['resource_type']) && isset($params['resource_id']) && !empty($params['resource_type']) && !empty($params['resource_id'])) {
            $transaction = $settingApi->getTransaction($params['resource_type'], $params['resource_id']);
        }
        if (empty($transaction)) {
            $data['return'] = 3;
            $data['message'] = 'Transaction not found';
            return $data;
        }
        $result = array();
        if ($transaction->refund_status != 'success') {
            $result = $this->refund($transaction->gateway_payment_key);
        } else {
            $data['return'] = 1;
            $data['message'] = 'Amount is already refunded for this transaction';
        }
        if (!empty($result)) {
            if ($result['responseEnvelope']['ack'] == 'Failure') {
                $refund_status = 'failed';
                $data['return'] = 0;
                $data['message'] = 'Failed';
            } else {
                if (isset($result['refundInfoList']['refundInfo'][0]['refundTransactionStatus'])) {
                    $refund_status = 'success';
                } elseif (isset($result['refundInfoList']['refundInfo'][1]['refundTransactionStatus'])) {
                    $refund_status = 'success';
                } else {
                    $refund_status = 'failed';
                    $data['return'] = 0;
                    $data['message'] = 'Failed';
                }
            }
            $transaction->refund_status = $refund_status;
            $transaction->save();
            $order = Engine_Api::_()->getItem($params['resource_type'], $transaction->resource_id);
            if ($order && isset($order->refund_status)) {
                $order->refund_status = $refund_status;
                $order->save();
            }
        }
        return $data;
    }

    public function payoutAllTransaction($params) {
        if ($params['resource_type'] == 'sitecrowdfunding_backer') {
            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $params['project_id']);
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
        $payoutArr = array();
        $transactions = Engine_Api::_()->getDbtable('transactions', 'sitegateway')->getAllTransactions(array('project_id' => $params['project_id'], 'gateway_id' => $adminGateway->gateway_id, 'gateway_payment_key' => '', 'resource_type' => $params['resource_type']));
        $success = false;
        $failed = false;
        foreach ($transactions as $k => $transaction) {
            $data = $this->payoutTransaction($params, $transaction);
            if ($data['return'] == 0) {
                $failed = true;
            } else {
                $success = true;
            }
        }
        $item->payout_status = 'payout';
        $item->save();
        $message = "";
        if ($success && $failed) {
            //Partially Success
            $message = $view->translate("Partially transaction payout in Paypal");
        } elseif ($success == true && $failed == false) {
            //all success
            $message = $view->translate("All transaction successfully payout in Paypal");
        } elseif ($success == false && $failed == true) {
            //failed
            $message = $view->translate("All transaction failed payout in Paypal");
        }
        return $message;
    }

    public function payoutTransaction($params, $transaction = null) {
        $settingApi = Engine_Api::_()->sitegateway();
        $data = array('return' => 1, 'message' => 'Success');
        if (empty($transaction) && isset($params['resource_type']) && isset($params['resource_id']) && !empty($params['resource_type']) && !empty($params['resource_id'])) {
            $transaction = $settingApi->getTransaction($params['resource_type'], $params['resource_id']);
        }
        if (empty($transaction)) {
            $data['return'] = 3;
            $data['message'] = 'Transaction not found';
            return $data;
        }
        $result = array();
        if ($transaction->payout_status != 'success') {
            $result = $this->payout($transaction->gateway_payment_key);
        } else {
            $data['return'] = 1;
            $data['message'] = 'Payout is already done for this Transaction.';
        }
        $payout_status = "";
        if (!empty($result)) {
            if ($result['responseEnvelope']['ack'] == 'Failure') {
                $data['return'] = 0;
                $data['message'] = isset($result['error'][0]['message']) ? $result['error'][0]['message'] : '';
            } else {
                switch ($result['paymentExecStatus']) {
                    case 'CREATED' :
                        $payout_status = 'pending';
                        $data['return'] = 2;
                        $data['message'] = 'Pending';
                        break;
                    case 'COMPLETED' :
                        $payout_status = 'success';
                        break;
                    case 'INCOMPLETE' :
                    case 'ERROR' :
                    case 'REVERSALERROR' :
                        $payout_status = 'failed';
                        $data['return'] = 0;
                        $data['message'] = 'Failed';
                        break;
                    default :
                        $payout_status = 'failed';
                        $data['return'] = 0;
                        $data['message'] = 'Failed';
                }
            }
            if (!empty($payout_status)) {
                $transaction->payout_status = $payout_status;
                $transaction->save();
                $order = Engine_Api::_()->getItem($params['resource_type'], $transaction->resource_id);
                if ($order && isset($order->payout_status)) {
                    $order->payout_status = $payout_status;
                    $order->save();
                }
            }
        }
        return $data;
    }

}
