<?php

include_once APPLICATION_PATH . '/application/libraries/Stripe/init.php';

class Engine_Service_Stripe extends Zend_Service_Abstract {

    /**
     * The publishable key
     *
     * @var string
     */
    protected $_publishable;

    /**
     * The secret key
     *
     * @var string
     */
    protected $_secret;

    /**
     * The log to send debug messages to
     * 
     * @var Zend_Log
     */
    protected $_log;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->setOptions($options);

// Force the curl adapter if it's available
        if (extension_loaded('curl')) {
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
//$adapter->setCurlOption(CURLOPT_VERBOSE, false);
            $this->getHttpClient()->setAdapter($adapter);
        }
        $this->getHttpClient()->setConfig(array('timeout' => 15));
    }

    public function setOptions(array $options) {
        foreach ($options as $key => $value) {
            $property = '_' . $key;
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

// Check options
        if (empty($this->_publishable) || empty($this->_secret)) {
            throw new Engine_Service_Stripe_Exception('Not all connection ' .
            'options were specified.', 'MISSING_LOGIN');
            throw new Zend_Service_Exception('Not all connection options were specified.');
        }
    }

    /**
     * Get the http client and set secret key
     *
     */
    protected function _prepareHttpClient() {
        \Stripe\Stripe::setApiKey($this->_secret);
    }

    /**
     * Check params
     *
     * @param array $params
     * @param array $requiredParams
     * @param array $supportedParams
     * @return array
     */
    protected function _checkParams(array $params, $requiredParams = null, $supportedParams = null) {
// Check params
        if (!is_array($params)) {
            if (!empty($params)) {
                throw new Engine_Service_Stripe_Exception('Invalid data type', 'UNKNOWN_PARAM');
            } else {
                $params = array();
            }
        }

// Check required params
        if (is_string($requiredParams)) {
            $requiredParams = array($requiredParams);
        } else if (null === $requiredParams) {
            $requiredParams = array();
        }

// Check supported params
        if (is_string($supportedParams)) {
            $supportedParams = array($supportedParams);
        } else if (null === $supportedParams) {
            $supportedParams = array();
        }

// Nothing to do
        if (empty($requiredParams) && empty($supportedParams) &&
                is_array($requiredParams) && is_array($supportedParams)) {
            return array();
        }

// Build full supported
        if (is_array($supportedParams) && is_array($requiredParams)) {
            $supportedParams = array_unique(array_merge($supportedParams, $requiredParams));
        }

// Run strtoupper on all keys?
        $params = array_combine(array_map('strtolower', array_keys($params)), array_values($params));

// Init
        $processedParams = array();
        $foundKeys = array();

// Process out simple params
        $processedParams = array_merge($processedParams, array_intersect_key($params, array_flip($supportedParams)));
        $params = array_diff_key($params, array_flip($supportedParams));
        $foundKeys = array_merge($foundKeys, array_keys($processedParams));

// Process out complex params
        foreach ($supportedParams as $supportedFormat) {
            foreach ($params as $key => $value) {
                if (count($parts = sscanf($key, $supportedFormat)) > 0) {
                    $foundKeys[] = $supportedFormat;
                    $processedParams[$key] = $value;
                }
            }
        }

// Remove complex params
        $params = array_diff_key($params, $processedParams);

// Anything left is an unsupported param
        if (!empty($params)) {
            $paramStr = '';
            foreach ($params as $key => $unsupportedParam) {
                if ($paramStr != '')
                    $paramStr .= ', ';
                $paramStr .= "$key:" . $unsupportedParam;
            }

            throw new Engine_Service_Stripe_Exception(sprintf('Unknown param(s): ' .
                    '%1$s', $paramStr), 'UNKNOWN_PARAM');
        }

// Let's check required against foundKeys
        if (count($missingRequiredParams = array_diff_key($requiredParams, $foundKeys)) > 0) {
            $paramStr = '';
            foreach ($missingRequiredParams as $missingRequiredParam) {
                if ($paramStr != '')
                    $paramStr .= ', ';
                $paramStr .= $missingRequiredParam;
            }
            throw new Engine_Service_Stripe_Exception(sprintf('Missing required ' .
                    'param(s): %1$s', $paramStr), 'MISSING_REQUIRED');
        }

        return $processedParams;
    }

    /**
     * Used to create a new product.
     *
     * @link https://stripe.com/docs/api/php#create_plan
     * @param array $params
     * @return string ID assigned to the product by stripe.
     */
    public function createProduct(array $params = array()) {

        $infoArray = array();

        //SHOULD WE CREATE PLAN FOR ONE TIME PAYMENT?
        if (empty($params['recurring'])) {
            return;
        } else {
            $recurrence = explode(' ', $params['recurrence']);
            $infoArray['interval_count'] = $recurrence[0];
            $infoArray['interval'] = strtolower($recurrence[1]);
        }

        $infoArray['name'] = $params['name'];
        $infoArray['id'] = $params['vendor_product_id'];
        $infoArray['amount'] = Engine_Api::_()->sitegateway()->getPrice($params['price']);
        $infoArray['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

        //CHECK PARAMS
        $params = $this->_checkParams($infoArray, array(
            'id', 'amount', 'currency', 'interval', 'name'
                ), array('interval_count', 'trial_period_days', 'metadata', 'statement_descriptor'
        ));

        $this->_prepareHttpClient();

        $plan = \Stripe\Plan::create($params);

        return $plan;
    }

    /**
     * Used to retrieve a product.
     *
     * @link https://stripe.com/docs/api#retrieve_plan
     * @param mixed $productId
     * @param array $params
     * @return Engine_Service_Stripe 
     */
    public function retrieveProduct($productId) {

        $this->_prepareHttpClient();

        try {
            $p = \Stripe\Plan::retrieve($productId);
        } catch (Exception $e) {
            return false;
        }

        return $p;
    }

    /**
     * Used to update a product.
     *
     * @link https://stripe.com/docs/api#update_plan
     * @param mixed $productId
     * @param array $params
     * @return Engine_Service_Stripe 
     */
    public function updateProduct($productId, $params = null) {

        $this->_prepareHttpClient();

        $p = $this->retrieveProduct($productId);
        $p->name = $params["name"];
        $p->save();
    }

    /**
     * Used to delete a product.
     *
     * @link https://stripe.com/docs/api/php#delete_plan
     * @param mixed $productId 2CO assigned product ID to delete. Required.
     * @return Engine_Service_Stripe 
     */
    public function deleteProduct($productId) {

        $this->_prepareHttpClient();

        $originalProductInfo = $this->detailVendorProduct($productId);

        if ($originalProductInfo) {
            $plan = \Stripe\Plan::retrieve($productId);
            $plan->delete();
        }
    }

    /**
     * Gets product details by vendor product id
     * 
     * @param string $vendorProductId
     * @return object
     */
    public function detailVendorProduct($vendorProductId) {

        $this->_prepareHttpClient();

        return $this->retrieveProduct($vendorProductId);
    }

    /**
     * Used to retrieve list of all products in account.
     *
     * @link https://stripe.com/docs/api/php#list_plans
     * @param array $params
     * @return object
     */
    public function listProducts(array $params = array()) {

        $this->_prepareHttpClient();

        return \Stripe\Plan::all();
    }

    /**
     * Used to create customer in account.
     *
     * @link https://stripe.com/docs/api/php#create_customer
     * @param array $params
     * @return object
     */
    public function createCustomer(array $params = array()) {

        $this->_prepareHttpClient();

        $customer = \Stripe\Customer::create(array($params));

        return $customer;
    }

    /**
     * Used to retrieve customer in account.
     *
     * @link https://stripe.com/docs/api/php#retrieve_customer
     * @param string $customer_id
     * @return object
     */
    public function retrieveCustomer($customer_id) {

        $this->_prepareHttpClient();

        return \Stripe\Customer::retrieve($customer_id);
    }

    /**
     * Used to edit customer in account.
     *
     * @link https://stripe.com/docs/api/php#update_customer
     * @param array $params
     */
    public function updateCustomer(array $params = array()) {

        $this->_prepareHttpClient();

        $customer = $this->retrieveCustomer($params['customer_id']);
        $customer->description = isset($params['description']) ? $params['description'] : $customer->description;
        $customer->plan = isset($params['plan']) ? $params['plan'] : $customer->plan;
        $customer->save();
    }

    /**
     * Used to delete customer in account.
     *
     * @link https://stripe.com/docs/api/php#delete_customer
     * @param array $params
     */
    public function deleteCustomer(array $params = array()) {

        $this->_prepareHttpClient();

        $customer = \Stripe\Customer::retrieve($params['customer_id']);
        $customer->delete();
    }

    /**
     * Used to create subscription in account.
     *
     * @link https://stripe.com/docs/api/php#create_subscription
     * @param array $params
     * @return object
     */
    public function createSubscription(array $params = array()) {

        $this->_prepareHttpClient();
        $customer = $this->retrieveCustomer($params['customer_id']);
        unset($params['customer_id']);
        $subscription = $customer->subscriptions->create($params);
        
        return $subscription;
    }

    /**
     * Used to update subscription in account.
     *
     * @link https://stripe.com/docs/api/php#update_subscription
     * @param array $params
     */
    public function updateSubscription(array $params = array()) {

        $this->_prepareHttpClient();

        $customer = $this->retrieveCustomer($params['customer_id']);

        $subscription = $customer->subscriptions->retrieve($params['subscription_id']);
        $subscription->plan = $params['plan'];
        $subscription->save();
    }

    /**
     * Used to cancel subscription in account.
     *
     * @link https://stripe.com/docs/api/php#cancel_subscription
     * @param array $params
     */
    public function cancelSubscription(array $params = array()) {

        $this->_prepareHttpClient();

        $customer = $this->retrieveCustomer($params['customer_id']);

        $customer->subscriptions->retrieve($params['subscription_id'])->cancel();
    }

    /**
     * Used to create charge in account.
     *
     * @link https://stripe.com/docs/api/php#create_charge
     * @param array $params
     * @return object
     */
    public function createCharge(array $params = array()) {

        $this->_prepareHttpClient();

        $charge = \Stripe\Charge::create($params, array(
                    "idempotency_key" => uniqid())
        );

        return $charge;
    }

    /**
     * Used to create charge in account.
     *
     * @link https://stripe.com/docs/webhooks
     * @param array $params
     * @return array
     */
    public function getWebHookDatas(array $params = array()) {

        $this->_prepareHttpClient();

        //Retrieve the request's body and parse it as JSON
        $input = @file_get_contents("php://input");
        $event_json = json_decode($input);
        $data = (array) $event_json;

        return $data;
    }

    /**
     * Used to cancel recurring payment profile
     *
     * @param string $profileId
     * @return object
     */
    public function cancelRecurringPaymentsProfile($profileId, $note = null) {

        $params = array();
        $params['customer_id'] = $profileId;

        $customer = $this->retrieveCustomer($params['customer_id']);

        if (!empty($customer) && !empty($customer['subscriptions']['data'][0]->id)) {
            $params['subscription_id'] = $customer['subscriptions']['data'][0]->id;
        } else {
            return $this;
        }

        $this->cancelSubscription($params);

        return $this;
    }

    /**
     * Used to create a new coupon.
     *
     * @link https://stripe.com/docs/api#create_coupon
     * @param array $params
     * @return string ID assigned to the coupon by Stripe.
     */
    public function createCoupon(array $params = array()) {

        $infoArray = array();
        $infoArray['id'] = $params['code'];
        if ($params['discount_type'] == 'percentage') {
            $infoArray['percent_off'] = $params['discount_value'];
        } else {
            $infoArray['amount_off'] = Engine_Api::_()->sitegateway()->getPrice($params['discount_value']);
            $infoArray['currency'] = Engine_Api::_()->sitegateway()->getCurrency();
        }

        if (!empty($params['maximum_per_code'])) {
            $infoArray['max_redemptions'] = $params['maximum_per_code'];
        }

        if (!empty($params['expirydate'])) {

            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $now = new DateTime(date("Y-m-d H:i:s"));
            $ref = new DateTime($view->locale()->toDate($params['expirydate']));
            $diff = $now->diff($ref);
            if ($diff->y > 5) {
                $infoArray['redeem_by'] = strtotime('+5 years');
            }
        }

        $infoArray['duration'] = $params['duration'];
        
        if($infoArray['duration'] == 'repeating') {
            $infoArray['duration_in_months'] = $params['duration_in_months'];
        }

        // Check params
        $params = $this->_checkParams($infoArray, array(
            'duration',
                ), array(
            'id', 'percent_off', 'amount_off', 'currency', 'duration_in_months', 'max_redemptions', 'metadata', 'redeem_by'
        ));

        $this->_prepareHttpClient();

        \Stripe\Coupon::create($params);
    }

    /**
     * Used to retrieve a product.
     *
     * @link https://stripe.com/docs/api#retrieve_coupon
     * @param string $couponCode
     * @return Engine_Service_Stripe 
     */
    public function retrieveCoupon($couponCode) {

        $this->_prepareHttpClient();

        try {
            $coupon = \Stripe\Coupon::retrieve($couponCode);
        } catch (Exception $e) {
            return false;
        }

        return $coupon;
    }

    /**
     * Used to update a coupon.
     * 
     * @link https://stripe.com/docs/api#update_coupon
     * @param string $couponCode
     * @param array $params
     * @return Engine_Service_Stripe 
     */
    public function updateCoupon($couponCode, $params = null) {

        $this->deleteCoupon($couponCode);

        $this->createCoupon($params);
    }

    /**
     * Used to delete a coupon.
     *
     * @link https://stripe.com/docs/api#delete_coupon
     * @param string $couponCode  String value of coupon code for deleting coupon.
     * @return Engine_Service_Stripe 
     */
    public function deleteCoupon($couponCode) {

        $this->_prepareHttpClient();

        $coupon = $this->retrieveCoupon($couponCode);

        if ($coupon) {
            $coupon->delete();
        }
    }

}
