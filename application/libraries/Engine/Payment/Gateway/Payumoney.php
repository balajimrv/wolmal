<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Payumoney.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Engine_Payment_Gateway_Payumoney extends Engine_Payment_Gateway {

    /**
     * Add the currency codes supported by this gateway.
     * 
     */
    protected $_supportedCurrencies = array( 'INR',
    );

    /**
     * Add the language codes supported by this gateway if required.
     * 
     */
    protected $_supportedLanguages = array( 'en',
    );

    /**
     * Add the region codes supported by this gateway if required.
     * 
     */
    protected $_supportedRegions = array('IN',
    );

    /**
     * Add the billing cycles supported by this gateway for recurring payments.
     * 
     */
    protected $_supportedBillingCycles = array(
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
            $this->setGatewayMethod('POST');
        }
    }

    /**
     * Get the service API
     *
     * @return Engine_Service_Payumoney
     */
    public function getService() {

        if (null === $this->_service) {
            $this->_service = new Engine_Service_Payumoney(array_merge(
                            $this->getConfig(), array(
                        'testMode' => $this->getTestMode(),
                        'log' => ( true ? $this->getLog() : null ),
                            )
            ));
        }
         
        return $this->_service;
    }
    
     /** Get Supported currencies 
    * @return $_supportedCurrencies
        public function getSupportedCurrencies(){
        return $this->_supportedCurrencies;
       }*/

    /*
     * code will redirect an users for payment processing.
     */

    public function getGatewayUrl() {

        //STORE PRODUCT ORDER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitestoreproduct']['order_id']) ? $_SESSION['Payment_Sitestoreproduct']['order_id'] : NULL;
        }

        //PAYMENT BY ADMIN FOR STORE OWNER
        if (empty($payment_order_id)) {
            $payment_order_id = isset($_SESSION['Payment_Sitestoreproducts']['order_id']) ? $_SESSION['Payment_Sitestoreproducts']['order_id'] : NULL;
        }

        if (empty($payment_order_id)) {
            //STORE PACKAGES ORDER
            $payment_order_id = isset($_SESSION['Payment_Sitestore']['order_id']) ? $_SESSION['Payment_Sitestore']['order_id'] : NULL;
        }
        
        if ($payment_order_id) {
            $session = new Zend_Session_Namespace('paumoney_payment_begin');
            $session->order_id = $payment_order_id;
        }
       // Manual
        if (null !== $this->_gatewayUrl) {
            return $this->_gatewayUrl;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        return $view->url(array('action' => 'process', 'controller' => 'payumoney', 'module' => 'sitegateway'), 'default');


    }


    /**
     * code for IPN/Webhooks handling. This method will be responsible to receive the notifications (IPN/Webhooks) from this gateway and notification data sholuld be return as an array.
     */
    public function processIpn(Engine_Payment_Ipn $ipn) {
          
    
    }

    /**
     * code for Process the transactions of your new payment gateway. 
     */
    public function processTransaction(Engine_Payment_Transaction $transaction) {
    
        $data = array();
        $rawData = $transaction->getRawData();
       
        if($this->getTestMode()) {
            $data['url'] = 'https://test.payu.in/_payment';
        } else {
            $data['url'] = 'https://secure.payu.in/_payment';
        }
        // Process data ------------------------------------------------------------
        //add transaction id
        $data['txnid']=substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        // Add Merchant key
        $data['key']=$rawData['merchant_key'];
        if( empty($data['key']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Merchant Key'));
            return false;
        }
        //Merchant salt
        $salt = $rawData['salt'];
        if( empty($salt) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Salt'));
            return false;
        }
        $data['salt'] = $rawData['salt'];
        //Customers email
        $data['email']=$rawData['email'];
       /* if( empty($data['email']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Customer Email '));
            return false;
        }*/
        //product info

        $data['productinfo']=$rawData['product_info'];
        if( empty($data['productinfo']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Product Info'));
            return false;
        }

        //customer info
        $data['firstname']=$rawData['customer_name_first'];
        if( empty($data['firstname']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Customer Name'));
            return false;
        }
        $data['lastname']=$rawData['customer_name_last'];
 
        // return url
        $data['surl']= $rawData['return_url'];
        if( empty($data['surl']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Return URL'));
            return false;
        }

        //cancel url
        $data['furl']=$rawData['cancel_url'];
        if( empty($data['furl']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'Cancel URL'));
            return false;
        }

    
        $data['amount']=$rawData['amount'];
        if( empty($data['amount']) ) {
            $this->_throw(sprintf('Missing parameter: %1$s', 'amount'));
            return false;
        }

        $hash = '';
        // Hash Sequence
        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
               
        $hashVarsSeq = explode('|', $hashSequence);
     
        $hash_string = ''; 
        foreach ($hashVarsSeq as $hash_var) {
           $hash_string .= isset($data[$hash_var]) ? $data[$hash_var] : '';
           $hash_string .= '|';
        }
    
        $hash_string .= $salt;
        $hash = strtolower(hash('sha512', $hash_string));
        $data['hash']=$hash;


        return $data;
        
    }

  // method to get url 
    
 
     /**
     * Add the code to authenticate the new payment gateway credentials.
     */
    public function test() {
    }

    /**
     * Add the code to create products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "createProduct" at "application/libraries/Engine/Service/Payumoney.php"
     */
    public function createProduct($params = array()) {
        return $this->getService()->createProduct($params);
    }

    /**
     * Add the code to edit products/subscriptions plans/packages created in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "editProduct" at "application/libraries/Engine/Service/Payumoney.php"
     */
    public function editProduct($productId, $params = array()) {
        return $this->getService()->updateProduct($productId, $params);
    }

    /**
     * Add the code to delete created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "deleteProduct" at "application/libraries/Engine/Service/Payumoney.php"
     */
    public function deleteProduct($productId) {
        return $this->getService()->deleteProduct($productId);
    }

    /**
     * Add the code to get detail about created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailProduct" at "application/libraries/Engine/Service/Payumoney.php"
     */
    public function detailProduct($productId) {
        return $this->getService()->detailProduct($productId);
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailVendorProduct" at "application/libraries/Engine/Service/Payumoney.php"
     */    
    public function detailVendorProduct($productId) {
        return $this->getService()->detailVendorProduct($productId);
    }
    
    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "createCoupon" at "application/libraries/Engine/Service/Payumoney.php"
     */     
    public function createCoupon($params = array()) {
        return $this->getService()->createCoupon($params);
    }    
    
    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "updateCoupon" at "application/libraries/Engine/Service/Payumoney.php"
     */      
    public function updateCoupon($couponCode, $params = array()) {
        return $this->getService()->updateCoupon($couponCode, $params);
    }    
    
    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "deleteCoupon" at "application/libraries/Engine/Service/Payumoney.php"
     */      
    public function deleteCoupon($couponCode) {
        return $this->getService()->deleteCoupon($couponCode);
    }        

}
