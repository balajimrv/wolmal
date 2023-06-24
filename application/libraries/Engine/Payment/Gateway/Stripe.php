<?php

include_once APPLICATION_PATH . '/application/libraries/Stripe/init.php';

class Engine_Payment_Gateway_Stripe extends Engine_Payment_Gateway {

    /**
     * Currencies supported by stripe gateway
     * 
     * @link https://support.stripe.com/questions/which-currencies-does-stripe-support
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
     * Billing cycles supported by stripe gateway
     * 
     */
    protected $_supportedBillingCycles = array(
        'Day', 'Week', 'Month', 'Year'
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
     * @return Engine_Service_Stripe
     */
    public function getService() {
        if (null === $this->_service) {
            $this->_service = new Engine_Service_Stripe(array_merge(
                            $this->getConfig(), array(
                        'testMode' => $this->getTestMode(),
                        'log' => ( true ? $this->getLog() : null ),
                            )
            ));
        }
        return $this->_service;
    }

    /**
     * Set the mode
     * @params $flag
     * @return Engine_Service_Stripe
     */
    public function setTestMode($flag = 0) {
        $config = $this->getConfig();
        $this->_testMode = $flag;
        if (!empty($config) && !empty($config['secret']) && !empty($config['publishable'])) {
            $this->_testMode = (bool) strstr($config['secret'], '_test_') && (bool) strstr($config['publishable'], '_test_');
        }
        return $this;
    }

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
        //PROJECT TICKET ORDER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitecrowdfunding']['order_id']) ? $_SESSION['Payment_Sitecrowdfunding']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitecredit']['order_id']) ? $_SESSION['Payment_Sitecredit']['order_id'] : NULL;
        }

        if ($payment_order_id) {
            $session = new Zend_Session_Namespace('stripe_payment_begin');
            $session->order_id = $payment_order_id;
        }
        // Manual
        if (null !== $this->_gatewayUrl) {
            return $this->_gatewayUrl;
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        return $view->url(array('action' => 'process', 'controller' => 'payment', 'module' => 'sitegateway'), 'default');
    }

    /**
     * IPN handling
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

    /**
     * Process transaction for this gateway
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
        return $data;
    }

    public function test() {
            
        if (isset($_POST['additionalGatewayDetailArray']['stripeGatewayDetail'])) {
            @parse_str($_POST['additionalGatewayDetailArray']['stripeGatewayDetail'], $stripeDetails);
        } elseif (isset($_POST['additionalGatewayDetailArray']['stripe'])) {
            @parse_str($_POST['additionalGatewayDetailArray']['stripe'], $stripeDetails);
        } elseif (isset($_POST['gatewayCredentials'])) {
            @parse_str($_POST['gatewayCredentials'], $stripeDetails);
        }
        
        $publishableKey = isset($_POST['publishable']) ? $_POST['publishable'] : $stripeDetails['publishable'];
        $secretKey = isset($_POST['secret']) ? $_POST['secret'] : $stripeDetails['secret'];
        $testMode = isset($_POST['test_mode']) ? $_POST['test_mode'] : $stripeDetails['test_mode'];
        $secretPreText = 'sk_';
        $publishablePreText = 'pk_';
        $lengthPreText = 3;
        $secretKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid secret key.');
        $publishableKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid publishable key.');
        if ($testMode) {
            $secretPreText = 'sk_test_';
            $publishablePreText = 'pk_test_';
            $lengthPreText = 8;
            $secretKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid secret key for test mode.');
            $publishableKeyError = Zend_Registry::get('Zend_Translate')->_('Please provide valid publishable key for test mode.');
        }

        if (isset($stripeDetails['secret'])) {
            $testMode = 1;
        }
        if (empty($secretKey) || (substr($secretKey, 0, $lengthPreText) != $secretPreText) || (empty($testMode) && strstr($secretKey, '_test_'))) {
            throw new Engine_Payment_Gateway_Exception($secretKeyError);
        }
        if (empty($publishableKey) || (substr($publishableKey, 0, $lengthPreText) != $publishablePreText) || (empty($testMode) && strstr($publishableKey, '_test_'))) {
            throw new Engine_Payment_Gateway_Exception($publishableKeyError);
        }
        $url = 'https://api.stripe.com/v1/charges?key=' . $secretKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (empty($response)) {
            $response = file_get_contents($url);
            $response = json_decode($response, true);
        }
        if (empty($response) || (!empty($response["error"]) && (substr($response["error"]["message"], 0, 24) == "Invalid API Key provided"))) {
            throw new Engine_Payment_Gateway_Exception($secretKeyError);
        }
//        $url = 'https://api.stripe.com/v1/tokens?key=' . $publishableKey;
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        $response = json_decode(curl_exec($ch), true);
//        curl_close($ch);
//
//        if (empty($response)) {
//            $response = file_get_contents($url);
//            $response = json_decode($response, true);
//        }
//        if (empty($response) || (!empty($response["error"]) && (substr($response["error"]["message"], 0, 24) == "Invalid API Key provided"))) {
//            throw new Engine_Payment_Gateway_Exception($publishableKeyError);
//        }
        if (isset($_POST['client_id'])) {
            $clientId = $_POST['client_id'];

            $clientIdError = Zend_Registry::get('Zend_Translate')->_('Please provide valid client id.');
            if (empty($clientId) || substr($clientId, 0, 3) != 'ca_') {
                throw new Engine_Payment_Gateway_Exception($clientIdError);
            }
            $ch = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=$clientId&scope=read_write";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);
            if (empty($response)) {
                $response = file_get_contents($url);
                $response = json_decode($response, true);
            }
            if (!empty($response["error"]) && !empty($response["error"]["message"])) {
                throw new Engine_Payment_Gateway_Exception($clientIdError);
            }
        }
        return true;
    }

    public function createProduct($params = array()) {
        return $this->getService()->createProduct($params);
    }

    public function editProduct($productId, $params = array()) {
        return $this->getService()->updateProduct($productId, $params);
    }

    public function deleteProduct($productId) {
        return $this->getService()->deleteProduct($productId);
    }

    public function detailProduct($productId) {
        return $this->getService()->detailProduct($productId);
    }

    public function detailVendorProduct($productId) {
        return $this->getService()->detailVendorProduct($productId);
    }

    public function getWebHookDatas() {
        return $this->getService()->getWebHookDatas();
    }

    public function createCoupon($params = array()) {
        return $this->getService()->createCoupon($params);
    }

    public function updateCoupon($couponCode, $params = array()) {
        return $this->getService()->updateCoupon($couponCode, $params);
    }

    public function deleteCoupon($couponCode) {
        return $this->getService()->deleteCoupon($couponCode);
    }

}
