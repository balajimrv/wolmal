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
class Engine_Payment_Gateway_PayPalAdaptive extends Engine_Payment_Gateway {

    protected $_supportedCurrencies = array(
        // 'ARS', // Supported by 2Checkout, but not by PayPal
        'AUD',
        'BRL', // This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
        'CAD',
        'CHF',
        'CZK', // Not supported by 2Checkout
        'DKK',
        'EUR',
        'GBP',
        'HKD',
        'HUF', // Not supported by 2Checkout
        'ILS', // Not supported by 2Checkout
        //'INR', // Supported by 2Checkout
        'JPY',
        'MXN',
        'MYR', // Not supported by 2Checkout - This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
        'NOK',
        'NZD',
        'PHP', // Not supported by 2Checkout
        'PLN', // Not supported by 2Checkout
        'SEK',
        'SGD', // Not supported by 2Checkout
        'THB', // Not supported by 2Checkout
        'TWD', // Not supported by 2Checkout
        'USD',
        'RUB',
        'TRY',
            //'ZAR', // Supported by 2Checkout
    );
    protected $_supportedLanguages = array(
        'es', 'en', 'de', 'fr', 'nl', 'pt', 'zh', 'it', 'ja', 'pl',
            // Full
            //'es_AR', 'en_AU', 'de_AT', 'en_BE', 'fr_BE', 'nl_BE', 'pt_BR', 'en_CA',
            //'fr_CA', 'zh_CN', 'zh_HK', 'fr_FR', 'de_DE', 'it_IT', 'ja_JP', 'es_MX',
            //'nl_NL', 'pl_PL', 'en_SG', 'es_SP', 'fr_CH', 'de_CH', 'en_CH', 'en_GB',
            //'en_US',
            // Not supported
            //'de_BE', 'zh_SG', 'gsw_CH', 'it_CH', 
    );
    protected $_supportedRegions = array(
        'AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM',
        'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ',
        'BM', 'BT', 'BO', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI',
        'KH', 'CM', 'CA', 'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO',
        'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CY', 'CZ', 'DK', 'DJ',
        'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ',
        'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR',
        'GL', 'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA',
        'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT',
        'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA',
        'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MK', 'MG', 'MW',
        'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD',
        'MC', 'MN', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'AN', 'NC',
        'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS',
        'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR', 'QA', 'RE', 'RO',
        'RU', 'RW', 'SH', 'KN', 'LC', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN',
        'CS', 'SC', 'SL', 'SG', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'ES', 'LK',
        'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL',
        'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE',
        'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH',
        'YE', 'ZM',
    );
    protected $_supportedBillingCycles = array(
        /* 'Day',  'Week',  'SemiMonth',  'Month', 'Year',*/
    );
    // Translation

    protected $_transactionMap = array(
        Engine_Payment_Transaction::REGION => 'LOCALECODE',
        Engine_Payment_Transaction::RETURN_URL => 'RETURNURL',
        Engine_Payment_Transaction::CANCEL_URL => 'CANCELURL',
        // Deprecated?
        Engine_Payment_Transaction::IPN_URL => 'NOTIFYURL',
        Engine_Payment_Transaction::VENDOR_ORDER_ID => 'INVNUM',
        Engine_Payment_Transaction::CURRENCY => 'CURRENCYCODE',
        Engine_Payment_Transaction::REGION => 'LOCALECODE',
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
     * @return Engine_Service_PayPalAdaptive
     */
    public function getService() {
        if (null === $this->_service) {
            $this->_service = new Engine_Service_PayPalAdaptive(array_merge(
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
       } */
    /*
     * Add the code which will redirect an users for payment processing. You can take a reference from Stripe or PayPal code available at same file path with same function name.
     */

    public function getGatewayUrl($type='PAYMENT') {

        // Manual
        if (null !== $this->_gatewayUrl) {
            return $this->_gatewayUrl;
        }
        if ($this->getTestMode()) {
            if($type=='PAYMENT'){
                return 'https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=';
            }
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-preapproval&preapprovalkey=';
        } else {
            if($type=='PAYMENT'){
                return 'https://www.paypal.com/webscr?cmd=_ap-payment&paykey=';
            }
            return 'https://www.paypal.com/cgi-bin/webscr?cmd=_ap-preapproval&preapprovalkey=';
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
        try {
            
            $this->getService()->searchButtons(date('Y-m-d H:i:s', time()));
        } catch (Engine_Service_PayPalAdaptive_Exception $e) {
            if (in_array((int) $e->getCode(), array(10002, 10008, 10101))) {
                throw new Engine_Payment_Gateway_Exception(sprintf('Gateway login ' .
                        'failed. Please double-check ' .
                        'your connection information. ' .
                        'The message was: %1$s', $e->getMessage()));
            }
        }
        return true;
    }

    /**
     * Add the code to create products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "createProduct" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function createProduct($params = array()) {
        return $this->getService()->createProduct($params);
    }

    /**
     * Add the code to edit products/subscriptions plans/packages created in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "editProduct" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function editProduct($productId, $params = array()) {
        return $this->getService()->updateProduct($productId, $params);
    }

    /**
     * Add the code to delete created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "deleteProduct" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function deleteProduct($productId) {
        return $this->getService()->deleteProduct($productId);
    }

    /**
     * Add the code to get detail about created products/subscription plans/packages in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailProduct" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function detailProduct($productId) {
        return $this->getService()->detailProduct($productId);
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin. You can also leave this method as defined here and write the code in method "detailVendorProduct" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function detailVendorProduct($productId) {
        return $this->getService()->detailVendorProduct($productId);
    }

    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "createCoupon" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function createCoupon($params = array()) {
        return $this->getService()->createCoupon($params);
    }

    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "updateCoupon" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function updateCoupon($couponCode, $params = array()) {
        return $this->getService()->updateCoupon($couponCode, $params);
    }

    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site. You can also leave this method as defined here and write the code in method "deleteCoupon" at "application/libraries/Engine/Service/PayPalAdaptive.php"
     */
    public function deleteCoupon($couponCode) {
        return $this->getService()->deleteCoupon($couponCode);
    }

}
