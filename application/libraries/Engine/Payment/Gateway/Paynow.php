<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Paynow.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Engine_Payment_Gateway_Paynow extends Engine_Payment_Gateway {

    /**
     * Add the currency codes supported by this gateway.
     * 
     */
    protected $_supportedCurrencies = array(
        'AED',
        'AFN', //UNSUPPORTED ON AMERICAN EXPRESS
        'ALL',
        'AMD',
        'ANG',
        'AOA', //UNSUPPORTED ON AMERICAN EXPRESS
        'ARS', //UNSUPPORTED ON AMERICAN EXPRESS
        'AUD',
        'AWG',
        'AZN',
        'BAM',
        'BBD',
        'BDT',
        'BGN',
        'BIF',
        'BMD',
        'BND',
        'BOB', //UNSUPPORTED ON AMERICAN EXPRESS
        'BRL', //UNSUPPORTED ON AMERICAN EXPRESS
        'BSD',
        'BWP',
        'BZD',
        'CAD',
        'CDF',
        'CHF',
        'CLP', //UNSUPPORTED ON AMERICAN EXPRESS
        'CNY',
        'COP', //UNSUPPORTED ON AMERICAN EXPRESS
        'CRC', //UNSUPPORTED ON AMERICAN EXPRESS
        'CVE',
        'CZK', //UNSUPPORTED ON AMERICAN EXPRESS
        'DJF', //UNSUPPORTED ON AMERICAN EXPRESS
        'DKK',
        'DOP',
        'DZD',
        'EGP',
        'ETB',
        'EUR',
        'FJD',
        'FKP', //UNSUPPORTED ON AMERICAN EXPRESS
        'GBP',
        'GEL',
        'GIP',
        'GMD',
        'GNF', //UNSUPPORTED ON AMERICAN EXPRESS
        'GTQ', //UNSUPPORTED ON AMERICAN EXPRESS
        'GYD',
        'HKD',
        'HNL', //UNSUPPORTED ON AMERICAN EXPRESS
        'HRK',
        'HTG',
        'HUF', //UNSUPPORTED ON AMERICAN EXPRESS
        'IDR',
        'ILS',
        'INR', //UNSUPPORTED ON AMERICAN EXPRESS
        'ISK',
        'JMD',
        'JPY',
        'KES',
        'KGS',
        'KHR',
        'KMF',
        'KRW',
        'KYD',
        'KZT',
        'LAK', //UNSUPPORTED ON AMERICAN EXPRESS
        'LBP',
        'LKR',
        'LRD',
        'LSL',
        'MAD',
        'MDL',
        'MGA',
        'MKD',
        'MNT',
        'MOP',
        'MRO',
        'MUR', //UNSUPPORTED ON AMERICAN EXPRESS
        'MVR',
        'MWK',
        'MXN', //UNSUPPORTED ON AMERICAN EXPRESS
        'MYR',
        'MZN',
        'NAD',
        'NGN',
        'NIO', //UNSUPPORTED ON AMERICAN EXPRESS
        'NOK',
        'NPR',
        'NZD',
        'PAB', //UNSUPPORTED ON AMERICAN EXPRESS
        'PEN', //UNSUPPORTED ON AMERICAN EXPRESS
        'PGK',
        'PHP',
        'PKR',
        'PLN',
        'PYG', //UNSUPPORTED ON AMERICAN EXPRESS
        'QAR',
        'RON',
        'RSD',
        'RUB',
        'RWF',
        'SAR',
        'SBD',
        'SCR',
        'SEK',
        'SGD',
        'SHP', //UNSUPPORTED ON AMERICAN EXPRESS
        'SLL',
        'SOS',
        'SRD', //UNSUPPORTED ON AMERICAN EXPRESS
        'STD',
        'SVC', //UNSUPPORTED ON AMERICAN EXPRESS
        'SZL',
        'THB',
        'TJS',
        'TOP',
        'TRY',
        'TTD',
        'TWD',
        'TZS',
        'UAH',
        'UGX',
        'USD',
        'UYU', //UNSUPPORTED ON AMERICAN EXPRESS
        'UZS',
        'VND',
        'VUV',
        'WST',
        'XAF',
        'XCD',
        'XOF', //UNSUPPORTED ON AMERICAN EXPRESS
        'XPF', //UNSUPPORTED ON AMERICAN EXPRESS
        'YER',
        'ZAR',
        'ZMW',
    );

    /**
     * Add the language codes supported by this gateway if required.
     * 
     */
    protected $_supportedLanguages = array(
    );

    /**
     * Add the language codes supported by this gateway if required.
     * 
     */
    protected $_transaction = array(
    );

    /**
     * Add the region codes supported by this gateway if required.
     * 
     */
    protected $_supportedRegions = array(
    );

    /**
     * Add the billing cycles supported by this gateway for recurring payments.
     * 
     */
//    protected $_supportedBillingCycles = array(
//        'One-time'
//    );
    protected $_supportedBillingCycles = array(
    /* 'Day', */ 'Week', /* 'SemiMonth',*/ 'Month', 'Year','One-time'
  );
    protected $_transactionMap = array(
        Engine_Payment_Transaction::RETURN_URL => 'RETURNURL',
        Engine_Payment_Transaction::CANCEL_URL => 'CANCELURL',
        Engine_Payment_Transaction::IPN_URL => 'NOTIFYURL',
        Engine_Payment_Transaction::VENDOR_ORDER_ID => 'INVNUM',
        Engine_Payment_Transaction::CURRENCY => 'CURRENCYCODE',
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = null) {
        parent::__construct($options);

        if (null === $this->getGatewayMethod()) {
            $this->setGatewayMethod('GET');
        }
    }

    /**
     * Get the service API
     *
     * @return Engine_Service_Paynow
     */
    public function getService() {

        if (null === $this->_service) {
            $this->_service = new Engine_Service_Paynow(array_merge(
                            $this->getConfig(), array(
                        'testMode' => $this->getTestMode(),
                        'log' => ( true ? $this->getLog() : null ),
                            )
            ));
        }

        return $this->_service;
    }

    /*
     * Add the code which will redirect an users for payment processing. You can take a reference from Paynow or PayPal code available at same file path with same function name.
     */

    public function getGatewayUrl() {
        //EVENT PACKAGES ORDER
        $payment_order_id = isset($_SESSION['Payment_Siteevent']['order_id']) ? $_SESSION['Payment_Siteevent']['order_id'] : NULL;

        //EVENT TICKET ORDER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Siteeventticket']['order_id']) ? $_SESSION['Payment_Siteeventticket']['order_id'] : NULL;
        }

        //PAYMENT BY ADMIN FOR EVENT OWNER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Siteeventtickets']['order_id']) ? $_SESSION['Payment_Siteeventtickets']['order_id'] : NULL;
        }

        //COMMISSION PAYMENT
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Event_Bill_Payment_Siteeventticket']['order_id']) ? $_SESSION['Event_Bill_Payment_Siteeventticket']['order_id'] : NULL;
        }

        //STORE PRODUCT ORDER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitestoreproduct']['order_id']) ? $_SESSION['Payment_Sitestoreproduct']['order_id'] : NULL;
        }

        //PAYMENT BY ADMIN FOR STORE OWNER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitestoreproducts']['order_id']) ? $_SESSION['Payment_Sitestoreproducts']['order_id'] : NULL;
        }

        //COMMISSION PAYMENT
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Store_Bill_Payment_Sitestoreproduct']['order_id']) ? $_SESSION['Store_Bill_Payment_Sitestoreproduct']['order_id'] : NULL;
        }

        //COMMUNITYADS PAYMENT
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Userads']['order_id']) ? $_SESSION['Payment_Userads']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //LISTING PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitereview']['order_id']) ? $_SESSION['Payment_Sitereview']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //SUBSCRIPTION PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Subscription']['order_id']) ? $_SESSION['Payment_Subscription']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //PAGE PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitepage']['order_id']) ? $_SESSION['Payment_Sitepage']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //BUSINESS PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitebusiness']['order_id']) ? $_SESSION['Payment_Sitebusiness']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //GROUP PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitegroup']['order_id']) ? $_SESSION['Payment_Sitegroup']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //STORE PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitestore']['order_id']) ? $_SESSION['Payment_Sitestore']['order_id'] : NULL;
        }

        $data = $_SESSION['transaction']->getRawData();
        // Manual
        if (null !== $this->_gatewayUrl) {
            return $this->_gatewayUrl;
        }

        $fields_string = $this->createMsg($data, $data['integration_key']);
        try {
            //open connection
            $ch = curl_init();
            $url = 'https://www.paynow.co.zw/Interface/InitiateTransaction';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //execute post
            $result = curl_exec($ch);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        if ($result) {
            $msg = $this->parseMsg($result);

            //first check status, take appropriate action
            if ($msg["status"] == 'Error') {
                // header("Location: $checkout_url");
                exit;
            } else if ($msg["status"] == 'Ok') {

                //second, check hash
                $validateHash = $this->createHash($msg, $data['integration_key']);
                if ($validateHash != $msg["hash"]) {
                    $error = "Paynow reply hashes do not match : " . $validateHash . " - " . $msg["hash"];
                } else {
                    $theProcessUrl = $msg["browserurl"];
                }
            } else {
                //unknown status or one you dont want to handle locally
                $error = "Invalid status in from Paynow, cannot continue.";
            }
        } else {
            $error = curl_error($ch);
        }

        //close connection
        curl_close($ch);

        if ($payment_order_id) {
            $session = new Zend_Session_Namespace('paynow_payment_begin');
            $session->order_id = $payment_order_id;
            $session->pollurl = $msg["pollurl"];
        }

        return $theProcessUrl;
    }

    public function parseMsg($msg) {
        $parts = explode("&", $msg);
        $result = array();
        foreach ($parts as $i => $value) {
            $bits = explode("=", $value, 2);
            $result[$bits[0]] = urldecode($bits[1]);
        }

        return $result;
    }

    public function urlIfy($fields) {
        $delim = "";
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $delim . $key . '=' . $value;
            $delim = "&";
        }

        return $fields_string;
    }

    public function createHash($values, $MerchantKey) {
        $string = "";
        foreach ($values as $key => $value) {
            if (strtoupper($key) != "HASH") {
                $string .= $value;
            }
        }
        $string .= $MerchantKey;

        $hash = hash("sha512", $string);
        return strtoupper($hash);
    }

    public function createMsg($values, $MerchantKey) {
        $fields = array();
        foreach ($values as $key => $value) {
            $fields[$key] = urlencode($value);
        }

        $fields["hash"] = urlencode($this->createHash($values, $MerchantKey));

        $fields_string = $this->urlIfy($fields);
        return $fields_string;
    }

    /**
     * If required, add the code here for IPN/Webhooks handling. This method will be responsible to receive the notifications (IPN/Webhooks) from this gateway and notification data sholuld be return as an array.
     */
    public function processIpn(Engine_Payment_Ipn $ipn) {

        // Get raw data
        $rawData = $ipn->getRawData();

        // Log raw data
        $this->_log(print_r($rawData, true), Zend_Log::DEBUG);

        $rawData = $this->getWebHookDatas($rawData);

        if (!empty($rawData) && !empty($rawData['type'])) {
            $this->_log('IPN Validation Succeeded');
            return $rawData;
        }

        return false;
    }

    public function getWebHookDatas() {
        return $this->getService()->getWebHookDatas();
    }

    /**
     * Add the code here for Process the transactions of your new payment gateway. 
     */
    public function processTransaction(Engine_Payment_Transaction $transaction) {
        $data = array();
        $rawData = $transaction->getRawData();

        // Driver-specific params
        if (isset($rawData['driverSpecificParams'])) {
            if (isset($rawData['driverSpecificParams'][$this->getDriver()])) {
                $data = array_merge($data, $rawData['driverSpecificParams'][$this->getDriver()]);
            }
            unset($rawData['driverSpecificParams']);
        }

        // Add default currency
        if (empty($rawData['currency']) && ($currency = $this->getCurrency())) {
            $rawData['currency'] = $currency;
        }

        // Process abtract translation map
        $tmp = array();
        $data = array_merge($data, $this->_translateTransactionData($rawData, $tmp));
        $rawData = $tmp;

        if (!empty($transaction->vendor_order_id)) {
            $order = Engine_Api::_()->getItem('payment_order', $transaction->vendor_order_id);
            $data['source_type'] = $order->source_type;
            $data['source_id'] = $order->source_id;
        }

        $_SESSION['transaction'] = $transaction;
        return $data;
    }

    /**
     * Add the code to authenticate the new payment gateway credentials.
     */
    public function test() {
        if (isset($_POST['additionalGatewayDetailArray']['paynowGatewayDetail'])) {
            @parse_str($_POST['additionalGatewayDetailArray']['paynowGatewayDetail'], $paynowDetails);
        } elseif (isset($_POST['gatewayCredentials'])) {
            @parse_str($_POST['gatewayCredentials'], $paynowDetails);
        }

        $integration_key = isset($_POST['integration_key']) ? $_POST['integration_key'] : $paynowDetails['integration_key'];
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : $paynowDetails['integration_id'];

        $testMode = isset($_POST['test_mode']) ? $_POST['test_mode'] : $paynowDetails['test_mode'];

        $integrationKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid integration key.');
        $integrationIdError = Zend_Registry::get('Zend_Translate')->_('Please provide valid integration id.');

        if ($testMode) {
            $integrationKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid integration key for test mode.');
            $integrationIdError = Zend_Registry::get('Zend_Translate')->_('Please provide valid integration id for test mode.');
        }

        if (isset($paynowDetails['integration_id'])) {
            $testMode = 1;
        }

        if (empty($integrationIdError) || (empty($testMode))) {
            throw new Engine_Payment_Gateway_Exception($integrationIdError);
        }

        if (empty($integrationKeyError) || (empty($testMode))) {
            throw new Engine_Payment_Gateway_Exception($integrationKeyError);
        }

        try {
            
        } catch (Engine_Service_Paynow_Exception $e) {
            throw new Engine_Payment_Gateway_Exception(sprintf('Gateway login ' .
                    'failed. Please double-check ' .
                    'your connection information. ' .
                    'The message was: %1$s', $e->getMessage()));
        }

        return true;
    }

    /**
     * Add the code to create products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "createProduct" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function createProduct($params = array()) {
        return $this->getService()->createProduct($params);
    }

    /**
     * Add the code to edit products/subscriptions plans/packages created in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "editProduct" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function editProduct($productId, $params = array()) {
        return $this->getService()->updateProduct($productId, $params);
    }

    /**
     * Add the code to delete created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "deleteProduct" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function deleteProduct($productId) {
        return $this->getService()->deleteProduct($productId);
    }

    /**
     * Add the code to get detail about created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailProduct" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function detailProduct($productId) {
        return $this->getService()->detailProduct($productId);
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailVendorProduct" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function detailVendorProduct($productId) {
        return $this->getService()->detailVendorProduct($productId);
    }

    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "createCoupon" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function createCoupon($params = array()) {
        return $this->getService()->createCoupon($params);
    }

    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "updateCoupon" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function updateCoupon($couponCode, $params = array()) {
        return $this->getService()->updateCoupon($couponCode, $params);
    }

    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "deleteCoupon" at "application/libraries/Engine/Service/Paynow.php"
     */
    public function deleteCoupon($couponCode) {
        return $this->getService()->deleteCoupon($couponCode);
    }

}
