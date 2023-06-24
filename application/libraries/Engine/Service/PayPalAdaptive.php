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
class Engine_Service_PayPalAdaptive extends Zend_Service_Abstract {

    /**
     * The email to login as
     *
     * @var string
     */
    protected $_email;

    /**
     * The username to login as
     *
     * @var string
     */
    protected $_username;

    /**
     * The password to use to login
     *
     * @var string
     */
    protected $_password;

    /**
     * The signature
     *
     * @var string
     */
    protected $_signature;

    /**
     * The certificate
     *
     * @var string
     */
    protected $_certificate;
    protected $_application_id;

    /**
     * Are we in test mode
     * 
     * @var boolean
     */
    protected $_test_mode;

    /**
     * The protocol version to use
     * 
     * @var string
     */
    protected $_version = '65.0';

    /**
     * Preprocess parameters that are arrays?
     * 
     * @var boolean
     */
    protected $_preProcessRequest = true;

    /**
     * Process response lists into arrays?
     * 
     * @var boolean
     */
    protected $_postProcessResponse = true;

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
        if (empty($this->_email) || empty($this->_username) || empty($this->_password) ||
                (empty($this->_signature) && empty($this->_certificate) )) {
            throw new Engine_Service_PayPalAdaptive_Exception('Not all connection ' .
            'options were specified.', 'MISSING_LOGIN');
            throw new Zend_Service_Exception('Not all connection options were specified.');
        }
    }

    /**
     * Get the http client and set default parameters
     *
     */

    /**
     * Get the http client and set default parameters
     *
     * @return Zend_Http_Client
     */
    protected function _prepareHttpClient($method = null) {
        // Get uri
        if ($this->_signature) {
            if ($this->_test_mode) {
                $uri = 'https://api-3t.sandbox.paypal.com/nvp';
            } else {
                $uri = 'https://api-3t.paypal.com/nvp';
            }
        } else {
            if ($this->_test_mode) {
                $uri = 'https://api.sandbox.paypal.com/nvp';
            } else {
                $uri = 'https://api.paypal.com/nvp';
            }
        }

        $client = $this->getHttpClient();
        $client
                ->resetParameters()
                ->setUri($uri)
                ->setMethod(Zend_Http_Client::POST)
        ;

        // Set method
        if (null !== $method) {
            $client->setParameterPost('METHOD', $method);
        }
        // Set version
        if (null !== $this->_version) {
            $client->setParameterPost('VERSION', urlencode($this->_version));
        }
        // Set credentials
        if (null !== $this->_username) {
            $client->setParameterPost('USER', urlencode($this->_username));
        }
        if (null !== $this->_password) {
            $client->setParameterPost('PWD', urlencode($this->_password));
        }
        if (null !== $this->_signature) {
            $client->setParameterPost('SIGNATURE', urlencode($this->_signature));
        }
        return $client;
    }

    /**
     * Check params (This method call is useful, if you want to check some required parameters for some methods.)
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
                throw new Engine_Service_PayPalAdaptive_Exception('Invalid data type', 'UNKNOWN_PARAM');
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
        $params = array_combine(array_map('strtoupper', array_keys($params)), array_values($params));

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
            foreach ($params as $unsupportedParam) {
                if ($paramStr != '')
                    $paramStr .= ', ';
                $paramStr .= $unsupportedParam;
            }
            //trigger_error(sprintf('Unknown param(s): %1$s', $paramStr), E_USER_NOTICE);
            throw new Engine_Service_PayPalAdaptive_Exception(sprintf('Unknown param(s): ' .
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
            throw new Engine_Service_PayPalAdaptive_Exception(sprintf('Missing required ' .
                    'param(s): %1$s', $paramStr), 'MISSING_REQUIRED');
        }

        return $processedParams;
    }

    /**
     * Add the code to create products/plans in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function createProduct(array $params = array()) {
        
    }

    /**
     * Add the code to retrieve created products/plans from this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function retrieveProduct($productId) {
        
    }

    /**
     * Add the code to update created products/plans in this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function updateProduct($productId, $params = null) {
        
    }

    /**
     * Add the code to delete created products/plans of this gateway. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     */
    public function deleteProduct($productId) {
        
    }

    /**
     * Gets product details by vendor product id. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     * 
     * @param string $vendorProductId
     * @return object
     */
    public function detailVendorProduct($vendorProductId) {
        
    }

    /**
     * Used to cancel recurring payment profile. You need to write the code accordingly in this method if you have enabled SocialEngine subscription plans and / or packages for any SocialEngineAddOns plugin.
     *
     * @param string $profileId
     * @return object
     */
    public function cancelRecurringPaymentsProfile($profileId, $note = null) {
        
    }

    /**
     * Write the coupon creation code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */
    public function createCoupon(array $params = array()) {
        
    }

    /**
     * Write the coupon edit code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */
    public function editCoupon($couponCode, $params = null) {
        
    }

    /**
     * Write the coupon deletetion code if this gateway provide discount coupon feature and you have installed the SocialEngineAddOns - Discount Coupons Plugin at your site.
     */
    public function deleteCoupon($couponCode) {
        
    }

    /**
     * Obtains a list of your hosted Website Payments Standard buttons.
     *
     * @link http://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_BMButtonSearch
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function searchButtons($startDate, $endDate = null) {
        // Build params
        if (is_array($startDate)) {
            $params = $startDate;
        } else {
            $params = array();
            $params['STARTDATE'] = $startDate;
            if (null !== $endDate) {
                $params['ENDDATE'] = $endDate;
            }
        }
        // Check params
        $params = $this->_checkParams($params, 'STARTDATE', 'ENDDATE');
        // Send request
        $client = $this->_prepareHttpClient('BMButtonSearch');
        $client
                ->setParameterPost($params)
        ;
        // Process response
        $response = $client->request();
        $responseData = $this->_processHttpResponse($response);

        // Post process response data?
        if ($this->_postProcessResponse) {
            $responseData = $this->_postProcessResponseData($responseData, array(
                'L_HOSTEDBUTTONID%d' => array('BUTTONS', 'HOSTEDBUTTONID'),
                'L_TYPE%d' => array('BUTTONS', 'TYPE'),
                'L_ITEMNAME%d' => array('BUTTONS', 'ITEMNAME'),
                'L_MODIFYDATE%d' => array('BUTTONS', 'MODIFYDATE'),
            ));
        }

        return $responseData;
    }

    /**
     * Process the response
     *
     * @param Zend_Http_Response $response
     * @return array
     * @throws Zend_Service_Exception
     */
    protected function _processHttpResponse(Zend_Http_Response $response) {
        // Hack for logging
        if ($this->_log instanceof Zend_Log) {
            $client = $this->getHttpClient();
            $this->_log->log(sprintf("Request:\n%s\nResponse:\n%s\n", $client->getLastRequest(), $client->getLastResponse()->asString()), Zend_Log::DEBUG);
        }

        // Check HTTP Status code
        if (200 !== $response->getStatus()) {
            throw new Engine_Service_PayPalAdaptive_Exception(sprintf('HTTP Client ' .
                    'returned error status: %1$d', $response->getStatus()), 'HTTP');
        }

        // Check response body
        $responseStr = $response->getBody();
        if (!is_string($responseStr) || '' === $responseStr) {
            throw new Engine_Service_PayPalAdaptive_Exception('HTTP Client returned an ' .
            'empty response', 'IS_EMPTY');
        }

        // Decode response body
        $responseData = array();
        foreach (explode("&", $responseStr) as $tmp) {
            $tmp = explode('=', $tmp, 2);
            if (count($tmp) > 1) {
                $responseData[urldecode($tmp[0])] = urldecode($tmp[1]);
            }
        }

        // Check for valid response
        if (!is_array($responseData) ||
                empty($responseData) ||
                count($responseData) <= 0 ||
                !array_key_exists('ACK', $responseData)) {
            throw new Engine_Service_PayPalAdaptive_Exception('HTTP Client returned ' .
            'invalid NVP response', 'NOT_VALID');
        }

        // Check for response status and message
        if (strtolower($responseData['ACK']) == 'failure') {
            switch (strtolower($responseData['L_SEVERITYCODE0'])) {
                default:
                case 'error':
                    $level = Zend_Log::ERR;
                    break;
            }
            throw new Engine_Service_PayPalAdaptive_Exception(sprintf('API Error: ' .
                    '[%1$d] %2$s - %3$s', $responseData['L_ERRORCODE0'], $responseData['L_SHORTMESSAGE0'], $responseData['L_LONGMESSAGE0']), $responseData['L_ERRORCODE0']);
        }

        return $responseData;
    }

    /**
     * Post-process the response data to support arrays
     * 
     * @param array $responseData
     * @param array $structure
     * @param array $scalarList
     * @return array
     */
    protected function _postProcessResponseData($responseData, $structure, $scalarList = null) {
        // Init
        $foundKeys = array();
        $processedData = array();

        // Process out scalars
        if (is_array($scalarList)) {
            $processedData = array_merge($processedData, array_intersect_key($responseData, array_flip($scalarList)));
            $responseData = array_diff_key($responseData, array_flip($scalarList));
        }

        // Process response lists
        $processedData = array();
        foreach ($structure as $format => $path) {
            foreach ($responseData as $key => $value) {
                if (count(array_filter($parts = sscanf($key, $format), 'is_numeric')) &&
                        vsprintf($format, $parts) == $key) {
                    // Build structure
                    $ref = & $processedData;
                    for ($i = 0, $l = max(count($parts), count($path)); $i < $l; $i++) {
                        if (isset($path[$i])) {
                            if (!isset($ref[$path[$i]])) {
                                $ref[$path[$i]] = array();
                            }
                            $ref = & $ref[$path[$i]];
                            //$last = 0;
                        }
                        if (isset($parts[$i])) {
                            if (!isset($ref[$parts[$i]])) {
                                $ref[$parts[$i]] = array();
                            }
                            $ref = & $ref[$parts[$i]];
                            //$last = 1;
                        }
                    }
                    // Assign value
                    $ref = $value;
                    // Set found
                    $foundKeys[] = $key;
                }
            }
        }

        // Remove processed keys
        $responseData = array_diff_key($responseData, array_flip($foundKeys));

        // Merge in processed data
        $responseData = array_merge($responseData, $processedData);

        return $responseData;
    }

    public function _paypalSend($packet, $call, $transactionType) {
        switch ($transactionType) {
            case 'Payment':
                if ($this->_test_mode) {
                    $apiUrl = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
                } else {
                    $apiUrl = 'https://svcs.paypal.com/AdaptivePayments/';
                }
                break;
            case 'Account':
                if ($this->_test_mode) {
                    $apiUrl = 'https://svcs.sandbox.paypal.com/AdaptiveAccounts/';
                } else {
                    $apiUrl = 'https://svcs.paypal.com/AdaptiveAccounts/';
                }
                break;
        }
	$ipObj = new Engine_IP();
        $headers = array(
            "X-PAYPAL-SECURITY-USERID: " . $this->_username,
            "X-PAYPAL-SECURITY-PASSWORD: " . $this->_password,
            "X-PAYPAL-SECURITY-SIGNATURE: " . $this->_signature,
            "X-PAYPAL-DEVICE-IPADDRESS: ".$ipObj->getRealRemoteAddress(),
            "X-PAYPAL-REQUEST-DATA-FORMAT: JSON",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
            "X-PAYPAL-APPLICATION-ID: " . $this->_application_id);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . $call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($packet));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        return json_decode(curl_exec($ch), TRUE);
    }

}
