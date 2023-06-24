<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MangoPay.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Engine_Payment_Gateway_MangoPay extends Engine_Payment_Gateway {

    /**
     * Add the currency codes supported by this gateway.
     * 
     */
    protected $_supportedCurrencies = array(
        'EUR',
        'GBP',
        'SEK',
        'NOK',
        'DKK',
        'CHF',
        'PLN',
    );

    /**
     * Add the language codes supported by this gateway if required.
     * 
     */
    protected $_supportedLanguages = array(
    );

    /**
     * Add the region codes supported by this gateway if required.
     * 
     */
    protected $_supportedRegions = array(
        'FI', 'AT', 'BE', 'BG', 'ES', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
        'GF', 'DE', 'GI', 'GR', 'GP', 'HU', 'IS', 'IE', 'IT', 'LV', 'LI', 'LT',
        'LU', 'MT', 'MQ', 'YT', 'MC', 'NL', 'NO', 'PL', 'PT', 'RE', 'RO', 'BL',
        'MF', 'PM', 'SK', 'SI', 'ES', 'SE', 'CH', 'GB', 'US', 'CA'
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
            $this->setGatewayMethod('GET');
        }
    }

    /** Get Supported currencies 
     * @return $_supportedCurrencies

      public function getSupportedCurrencies(){
      return $this->_supportedCurrencies;
      } */

    /**
     * Get the service API
     *
     * @return Engine_Service_MangoPay
     */
    public function getService() {
        if (null === $this->_service) {
            $this->_service = new Engine_Service_MangoPay(array_merge(
                            $this->getConfig(), array(
                        'testMode' => $this->getTestMode(),
                        'log' => ( true ? $this->getLog() : null ),
                            )
            ));
        }

        return $this->_service;
    }

    /*
     * Add the code which will redirect an users for payment processing. You can take a reference from Stripe or PayPal code available at same file path with same function name.
     */

    public function getGatewayUrl() {

        // Manual
        if (null !== $this->_gatewayUrl) {
            return $this->_gatewayUrl;
        }
    }

    /**
     * If required, add the code here for IPN/Webhooks handling. This method will be responsible to receive the notifications (IPN/Webhooks) from this gateway and notification data sholuld be return as an array.
     */
    public function processIpn(Engine_Payment_Ipn $ipn) {

        //This method should return an array.
        return array();
    }

    /**
     * Add the code here for Process the transactions of your new payment gateway. 
     */
    public function processTransaction(Engine_Payment_Transaction $transaction) {
        
    }

    /**
     * Add the code to authenticate the new payment gateway credentials.
     */
    public function test() {
        $client = $this->getService()->viewClient();
    }

    /**
     * Add the code to create products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "createProduct" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function createProduct($params = array()) {
        return $this->getService()->createProduct($params);
    }

    /**
     * Add the code to edit products/subscriptions plans/packages created in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "editProduct" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function editProduct($productId, $params = array()) {
        return $this->getService()->updateProduct($productId, $params);
    }

    /**
     * Add the code to delete created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "deleteProduct" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function deleteProduct($productId) {
        return $this->getService()->deleteProduct($productId);
    }

    /**
     * Add the code to get detail about created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailProduct" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function detailProduct($productId) {
        return $this->getService()->detailProduct($productId);
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailVendorProduct" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function detailVendorProduct($productId) {
        return $this->getService()->detailVendorProduct($productId);
    }

    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "createCoupon" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function createCoupon($params = array()) {
        return $this->getService()->createCoupon($params);
    }

    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "updateCoupon" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function updateCoupon($couponCode, $params = array()) {
        return $this->getService()->updateCoupon($couponCode, $params);
    }

    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "deleteCoupon" at "application/libraries/Engine/Service/MangoPay.php"
     */
    public function deleteCoupon($couponCode) {
        return $this->getService()->deleteCoupon($couponCode);
    }

    /**
     * Set the mode
     * @params $flag
     * @return Engine_Service_MangoPay
     */
    public function setTestMode($flag = 0) {

        $this->_testMode = $flag;
        return $this;
    }

}
