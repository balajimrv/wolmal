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
include_once APPLICATION_PATH . '/application/libraries/MangoPay/MangoPay/Autoloader.php';
require_once APPLICATION_PATH . '/application/libraries/MangoPay/vendor/autoload.php';

class Engine_Service_MangoPay extends Zend_Service_Abstract {

    /**
     * Here You Can Define the API keys of your new payment gateway which will use later in this file. i.e. The Secret key for Stripe gateway.
     *
     * @var string
     */
    protected $_client_id;
    protected $_client_password;
    protected $_temporary_path = APPLICATION_PATH . '/temporary/';
    protected $mangoPayApi;
    protected $_test_mode;

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

        //Configure the client id and password
        $this->mangoPayApi = new \MangoPay\MangoPayApi();
        $this->mangoPayApi->Config->ClientId = $this->_client_id;
        $this->mangoPayApi->Config->ClientPassword = $this->_client_password;
        $this->mangoPayApi->Config->TemporaryFolder = $this->_temporary_path;
        if ($this->_test_mode) {
            $this->mangoPayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        } else {
            $this->mangoPayApi->Config->BaseUrl = 'https://api.mangopay.com';
        }
        $this->mangoPayApi->Config->CurlResponseTimeout = 20; //The cURL response timeout in seconds (its 30 by default)
        $this->mangoPayApi->Config->CurlConnectionTimeout = 60; //The cURL connection timeout in seconds (its 80 by default)
        $this->mangoPayApi->Config->CertificatesFilePath = ''; //Absolute path to file holding one or more certificates to verify the peer with (if empty, there won't be any verification of the peer's certificate)
    }

    /**
     * Set credentials for your gateway. i.e. here we have set the publishable and secret key for stripe gateway. 
     */
    public function setOptions(array $options) {
        foreach ($options as $key => $value) {
            $property = '_' . $key;
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        // Check options
        if (empty($this->_client_id) || empty($this->_client_password)) {
            throw new Engine_Service_MangoPay_Exception('Not all connection ' .
            'options were specified.', 'MISSING_LOGIN');
            throw new Zend_Service_Exception('Not all connection options were specified.');
        }
    }

    /**
     * Get the http client and set default parameters
     *
     */
    protected function _prepareHttpClient() {
        
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
                throw new Engine_Service_MangoPay_Exception('Invalid data type', 'UNKNOWN_PARAM');
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

            throw new Engine_Service_MangoPay_Exception(sprintf('Unknown param(s): ' .
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
            throw new Engine_Service_MangoPay_Exception(sprintf('Missing required ' .
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

    public function viewClient() {
        try {
            $Client = $this->mangoPayApi->Clients->Get();
        } catch (MangoPay\Libraries\ResponseException $e) {
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $Client;
    }

    public function createMangoPayUser($params) {
        try {
            $UserNatural = new \MangoPay\UserNatural();
            $UserNatural->PersonType = "NATURAL";
            $UserNatural->FirstName = $params['first_name'];
            $UserNatural->LastName = $params['last_name'];
            $UserNatural->Birthday = $params['birthday'];
            $UserNatural->Nationality = $params['nationality'];
            $UserNatural->CountryOfResidence = $params['residence'];
            $UserNatural->Email = $params['email'];
            $UserNatural->Tag = $params['tag']; 
            $result = $this->mangoPayApi->Users->Create($UserNatural);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function updateMangoPayUser($params) {
        try {

            $UserNatural = new \MangoPay\UserNatural();
            $UserNatural->Id = $params['Id'];
            $UserNatural->FirstName = $params['first_name'];
            $UserNatural->LastName = $params['last_name'];
            $UserNatural->Birthday = $params['birthday'];
            $UserNatural->Nationality = $params['nationality'];
            $UserNatural->CountryOfResidence = $params['residence'];
            $UserNatural->Email = $params['email'];
            $UserNatural->Tag = $params['tag'];

            $UserNatural->Address = new \MangoPay\Address();

            $UserNatural->Address->AddressLine1 = $params['OwnerAddress'];
            $UserNatural->Address->AddressLine2 = $params['OwnerAddress2'];
            $UserNatural->Address->City = $params['City'];
            $UserNatural->Address->Region = $params['Region'];
            $UserNatural->Address->PostalCode = $params['PostalCode'];
            $UserNatural->Address->Country = $params['Country'];

            $result = $this->mangoPayApi->Users->Update($UserNatural);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function createMangoPayWallet($params) {

        try {
            $Wallet = new \MangoPay\Wallet();
            $Wallet->Tag = $params['tag'];
            $Wallet->Owners = $params['owner'];
            $Wallet->Description = $params['description'];
            $Wallet->Currency = $params['currency'];

            $result = $this->mangoPayApi->Wallets->Create($Wallet);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function updateMangoPayWallet($params) {

        try {
            $Wallet = new \MangoPay\Wallet();
            $Wallet->Tag = $params['tag'];
            $Wallet->Description = $params['description'];
            $Wallet->Id = $params['wallet_id'];
            $result = $this->mangoPayApi->Wallets->Update($Wallet);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function createPayIn($params) {
        try {

            $PayIn = new \MangoPay\PayIn();
            $PayIn->CreditedWalletId = $params['credited_wallet_id'];
            $PayIn->AuthorId = $params['author_id'];
            $PayIn->DebitedFunds = new \MangoPay\Money();
            $PayIn->DebitedFunds->Currency = $params['db_currency'];
            $PayIn->DebitedFunds->Amount = $params['db_amount'];
            $PayIn->Fees = new \MangoPay\Money();
            $PayIn->Fees->Currency = $params['fees_currency'];
            $PayIn->Fees->Amount = $params['fees_amount'];
            $PayIn->ReturnURL = $params['return_url'];
            $PayIn->PaymentType = "CARD";
            $PayIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsCard();
            $PayIn->PaymentDetails->CardType = "CB_VISA_MASTERCARD";
            $PayIn->ExecutionType = "WEB";
            $PayIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsWeb();
            $PayIn->Culture = 'EN';
            $PayIn->Tag = $params['tag'];
            $result = $this->mangoPayApi->PayIns->Create($PayIn);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function getPayInDetail($PayInId) {
        try {
            $PayIn = $this->mangoPayApi->PayIns->Get($PayInId);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $PayIn;
    }

    public function createMangoPayBankAccount($params, $userId) {
        $BankAccount = new \MangoPay\BankAccount();
        $BankAccount->Type = $params['Type'];
        $BankAccount->OwnerName = $params['OwnerName'];
        $BankAccount->OwnerAddress = new \MangoPay\Address();
        $BankAccount->OwnerAddress->AddressLine1 = $params['OwnerAddress'];
        $BankAccount->OwnerAddress->AddressLine2 = $params['OwnerAddress2'];
        $BankAccount->OwnerAddress->City = $params['City'];
        $BankAccount->OwnerAddress->Region = $params['Region'];
        $BankAccount->OwnerAddress->PostalCode = $params['PostalCode'];
        $BankAccount->OwnerAddress->Country = $params['Country'];

        switch ($params['Type']) {
            case 'IBAN' :
                $BankAccount->Details = new MangoPay\BankAccountDetailsIBAN();
                $BankAccount->Details->IBAN = $params['IBAN'];
                $BankAccount->Details->BIC = $params['BIC'];
                break;
            case 'GB' :
                $BankAccount->Details = new MangoPay\BankAccountDetailsGB();
                $BankAccount->Details->SortCode = $params['SortCode'];
                $BankAccount->Details->AccountNumber = $params['AccountNumber'];
                break;
            case 'US' :
                $BankAccount->Details = new MangoPay\BankAccountDetailsUS();
                $BankAccount->Details->ABA = $params['ABA'];
                $BankAccount->Details->AccountNumber = $params['AccountNumber'];
                $BankAccount->Details->DepositAccountType = $params['DepositAccountType'];
                break;
            case 'CA' :
                $BankAccount->Details = new MangoPay\BankAccountDetailsCA();
                $BankAccount->Details->BankName = $params['BankName'];
                $BankAccount->Details->BranchCode = $params['BranchCode'];
                $BankAccount->Details->AccountNumber = $params['AccountNumber'];
                $BankAccount->Details->InstitutionNumber = $params['InstitutionNumber'];
                break;
            case 'OTHER' :
                $BankAccount->Details = new MangoPay\BankAccountDetailsOTHER();
                $BankAccount->Details->BIC = $params['BIC'];
                $BankAccount->Details->AccountNumber = $params['AccountNumber'];
                $BankAccount->Details->Country = $params['Country'];
                break;
        }
        try {
            $result = $this->mangoPayApi->Users->CreateBankAccount($userId, $BankAccount);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function viewBankAccount($UserId, $BankAccountId) {
        $BankAccount = $this->mangoPayApi->Users->GetBankAccount($UserId, $BankAccountId);
        return $BankAccount;
    }

    public function createPayout($params) {
        $PayOut = new \MangoPay\PayOut();
        $PayOut->AuthorId = $params['user_id'];
        $PayOut->DebitedWalletID = $params['wallet_id'];
        $PayOut->DebitedFunds = new \MangoPay\Money();
        $PayOut->DebitedFunds->Currency = $params['currency'];
        $PayOut->DebitedFunds->Amount = $params['amount'];
        $PayOut->Fees = new \MangoPay\Money();
        $PayOut->Fees->Currency = $params['currency'];
        $PayOut->Fees->Amount = $params['fees_amount'];
        $PayOut->PaymentType = "BANK_WIRE";
        $PayOut->Tag = $params['tag'];
        $PayOut->MeanOfPaymentDetails = new \MangoPay\PayOutPaymentDetailsBankWire();
        $PayOut->MeanOfPaymentDetails->BankAccountId = $params['bank_account_id'];
        try {
            $result = $this->mangoPayApi->PayOuts->Create($PayOut);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

    public function viewWallet($WalletId) {

        try {
            $Wallet = $this->mangoPayApi->Wallets->Get($WalletId);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $Wallet;
    }

    public function viewPayout($PayOutId) {
        try {
            $PayOut = $this->mangoPayApi->PayOuts->Get($PayOutId);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $PayOut;
    }

    public function createRefund($PayInId, $params) {
        try {
            $Refund = new \MangoPay\Refund();
            $Refund->Tag = $params['tag'];
            $Refund->AuthorId = $params['user_id'];
            $Result = $this->mangoPayApi->PayIns->CreateRefund($PayInId, $Refund);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $Result;
    }

    public function createKycDocument($user_id, $params) {
        try {
            $KycDocument = new \MangoPay\KycDocument();
            $KycDocument->Type = $params['document_type'];
            $KycDocument->Tag = $params['tag'];
            $Result = $this->mangoPayApi->Users->CreateKycDocument($user_id, $KycDocument);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $Result;
    }

    public function createKycPage($params) {
        try {
            $userId = $params['user_id'];
            $kycDocument = $this->createKycDocument($userId, $params);
            $KYCDocumentId = $kycDocument->Id;
            $file = $params['page'];
            $this->mangoPayApi->Users->CreateKycPageFromFile($userId, $KYCDocumentId, $file);
            $Result = $this->updateKyc($userId, $KYCDocumentId);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $Result;
    }

    public function updateKyc($userId, $kycDocumentId) {
        $kycDocument = new MangoPay\KycDocument();
        $kycDocument->Id = $kycDocumentId;
        $kycDocument->Status = \MangoPay\KycDocumentStatus::ValidationAsked;
        $result = $this->mangoPayApi->Users->UpdateKycDocument($userId, $kycDocument);
        return $result;
    }

    public function getKycdocuments($userId) {
        try {
            $pagination = new MangoPay\Pagination();
            $sort = new MangoPay\Sorting();
            $sort->AddField('CreationDate', 'DESC');
            $pagination->ItemsPerPage = 50;
            $result = $this->mangoPayApi->Users->GetKycDocuments($userId, $pagination, $sort);
        } catch (MangoPay\Libraries\ResponseException $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        } catch (MangoPay\Libraries\Exception $e) {
            throw new Engine_Service_MangoPay_Exception($e->GetMessage(), $e->GetCode());
        }
        return $result;
    }

}
