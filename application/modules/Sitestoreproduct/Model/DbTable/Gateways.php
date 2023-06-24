<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Gateways.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Gateways extends Engine_Db_Table {

    protected $_name = 'sitestoreproduct_gateways';
    protected $_rowClass = 'Sitestoreproduct_Model_Gateway';
    protected $_serializedColumns = array('config');
    protected $_cryptedColumns = array('config');
    static private $_cryptKey;

    public function getEnabledGatewayCount() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->where('enabled = ?', 1)
                        ->query()
                        ->fetchColumn()
        ;
    }

    public function getEnabledGateways() {
        return $this->fetchAll($this->select()->where('enabled = ?', true));
    }

    // Inline encryption/decryption
    public function insert(array $data) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::insert($data);
    }

    public function update(array $data, $where) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::update($data, $where);
    }

    protected function _fetch(Zend_Db_Table_Select $select) {
        $rows = parent::_fetch($select);

        foreach ($rows as $index => $data) {
            // Decrypt each column
            foreach ($this->_cryptedColumns as $col) {
                if (!empty($rows[$index][$col])) {
                    $rows[$index][$col] = self::_decrypt($rows[$index][$col]);
                }
            }
            // Unserialize
            $rows[$index] = $this->_unserializeColumns($rows[$index]);
        }

        return $rows;
    }

    // Crypt Utility

    static private function _encrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();
        $cryptData = mcrypt_encrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);

        return $cryptData;
    }

    static private function _decrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();
        $cryptData = mcrypt_decrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);
        $cryptData = rtrim($cryptData, "\0");

        return $cryptData;
    }

    static private function _getCryptKey() {
        if (null === self::$_cryptKey) {
            $key = Engine_Api::_()->getApi('settings', 'core')->core_secret
                    . '^'
                    . Engine_Api::_()->getApi('settings', 'core')->payment_secret;
            self::$_cryptKey = substr(md5($key, true), 0, 8);
        }

        return self::$_cryptKey;
    }

    public function getSellerStoreGateway($store_id, $method) {


        return $this->select()->from($this->info('name'), 'gateway_id')->where('store_id =? AND enabled = 1', $store_id)->where('plugin like ?', '%' . $method . '%')
                        ->query()->fetchColumn();
    }

    public function getStoreGateway($store_id) {
        return $this->select()->from($this->info('name'), 'gateway_id')->where('store_id =? AND enabled = 1', $store_id)->query()->fetchColumn();
    }

    /**
     * Return PayPal gateway id, if exist
     *
     * @param int $store_id
     * @return int
     */
    public function isPayPalGatewayEnable($store_id) {
        $select = $this->select()
                ->from($this->info('name'), 'gateway_id')
                ->where("plugin = 'Payment_Plugin_Gateway_PayPal'")
                ->where("enabled = 1")
                ->where("store_id = ?", $store_id);

        return $select->query()->fetchColumn();
    }

    public function mangoPayConfigSettings($data, $sitestore) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $mangoPayUserId = null;
        $mangoPayWalletId = null;
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //This array will contain Config data
        $configArr = array();
        $config = array();
        $returnArr = array('error' => 0, 'error_message' => '', 'gateway_id' => 0);
        $adminGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $currencyCode = $sitegatewayApi->getCurrency();
        $supportedCurrencies = $adminGateway->getGateway()->getsupportedCurrencies();
        if (!in_array($currencyCode, $supportedCurrencies)) {
            $returnArr['error'] = 1;
            $returnArr['error_message'] = "Selected currency is not supported.";
            return $returnArr;
        }

        $sitestore_gateway_obj = $this->fetchRow(array('store_id = ?' => $sitestore->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        if (empty($sitestore_gateway_obj)) {
            $row = $this->createRow();
            $row->store_id = $sitestore->store_id;
            $row->user_id = $viewer_id;
            $row->email = $data['email'];
            $row->title = 'MangoPay';
            $row->description = '';
            $row->plugin = 'Sitegateway_Plugin_Gateway_MangoPay';
            $row->test_mode = $adminGateway->config['test_mode'];
            $row->save();
            $sitestore_gateway_obj = $row;
        } else {
            $sitestore_gateway_obj->email = $data['email'];
            $sitestore_gateway_obj->test_mode = $adminGateway->config['test_mode'];
            $sitestore_gateway_obj->save();
            $config = $sitestore_gateway_obj->config;
            if (isset($config[$mode]['mangopay_user_id']) && !empty($config[$mode]['mangopay_user_id'])) {
                $mangoPayUserId = $config[$mode]['mangopay_user_id'];
                if (isset($config[$mode]['mangopay_wallet_id']) && !empty($config[$mode]['mangopay_wallet_id'])) {
                    $mangoPayWalletId = $config[$mode]['mangopay_wallet_id'];
                }
            } else {
                $mangoPayUserId = null;
                $mangoPayWalletId = null;
            }
        }
        //Check for MangoPay User id if already exist

        $params = array();
        $params['first_name'] = $firstName = $data['first_name'];
        $params['last_name'] = $lastName = $data['last_name'];
        $params['email'] = $data['email'];
        $params['birthday'] = mktime(0, 0, 0, $data['birthday']['month'], $data['birthday']['day'], $data['birthday']['year']);
        $params['nationality'] = $data['nationality'];
        $params['residence'] = $data['residence'];
        $params['tag'] = "Seller Account";
        if (!isset($config[$mode])) {
            $config[$mode] = array();
        }
        $configArr = array_merge($config[$mode], $params);
        try {
            if (empty($mangoPayUserId)) {
                //Create MangoPay User for Seller
                $mangoPayUser = $adminGateway->getService()->createMangoPayUser($params);
            } else {
                //Update MangoPay User for Seller
                $params['Id'] = $mangoPayUserId;
                $mangoPayUser = $adminGateway->getService()->updateMangoPayUser($params);
            }
            $mangoPayUserId = $mangoPayUser->Id;
            $name = ($firstName . " " . $lastName);
            //Create Seller Wallet at MangoPay
            //Building Wallet packet
            if (empty($mangoPayWalletId)) {
                $params = array();
                $params['owner'] = array($mangoPayUserId);
                $params['description'] = $view->translate('This is Seller ( %s ) wallet for Project %s.', $name, $sitestore->getTitle());
                $params['currency'] = $sitegatewayApi->getCurrency();
                $params['tag'] = $view->translate('Seller Wallet');
                $mangoPayWallet = $adminGateway->getService()->createMangoPayWallet($params);
                $mangoPayWalletId = $mangoPayWallet->Id;
            } else {
                $params = array();
                $params['description'] = $view->translate('This is Seller ( %s ) wallet for Project %s.', $name, $sitestore->getTitle());
                $params['tag'] = $view->translate('Seller Wallet');
                $params['wallet_id'] = $mangoPayWalletId;
                $adminGateway->getService()->updateMangoPayWallet($params);
            }
            $cf = $sitestore_gateway_obj->config;
            $cf[$mode] = array_merge($configArr, array('mangopay_wallet_id' => $mangoPayWalletId, 'mangopay_user_id' => $mangoPayUserId));
            $sitestore_gateway_obj->enabled = true;
            $sitestore_gateway_obj->config = $cf;
            $sitestore_gateway_obj->save();
            $returnArr['gateway_id'] = $sitestore_gateway_obj->gateway_id;
        } catch (Exception $ex) {
            $returnArr['error'] = 1;
            $returnArr['error_message'] = $ex->GetMessage() . "" . $ex->GetCode();
        }
        return $returnArr;
    }

    public function setMangoPayBankDetails($data, $sitestore) {
        $sitestore_gateway_obj = $this->fetchRow(array('store_id = ?' => $sitestore->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $currencyCode = $sitegatewayApi->getCurrency();
        $supportedCurrencies = $adminGateway->getGateway()->getsupportedCurrencies();
        if (!in_array($currencyCode, $supportedCurrencies)) {
            $returnArr['error'] = 1;
            $returnArr['error_message'] = "Selected currency is not supported.";
            return $returnArr;
        }
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $type = $data['account_type'];
        $error = false;
        $returnArray = array('error' => 0, 'errorMessage' => '');
        if (!$sitestore_gateway_obj || !isset($sitestore_gateway_obj->config[$mode]) || empty($sitestore_gateway_obj->config[$mode]) || !isset($sitestore_gateway_obj->config[$mode]['mangopay_user_id']) || empty($sitestore_gateway_obj->config[$mode]['mangopay_user_id'])) {
            $returnArray['error'] = 1;
            $returnArray['errorMessage'] = $view->translate("Invalid Operation");
            return $returnArray;
        }
        if (isset($sitestore_gateway_obj->config[$mode]['bank_account_id']) && strlen($sitestore_gateway_obj->config[$mode]['bank_account_id']) > 0) {
            return $returnArray;
        }
        $data['owner_name'] = trim($data['owner_name']);
        $data['owner_address'] = trim($data['owner_address']);
        $data['city'] = trim($data['city']);
        $data['region'] = trim($data['region']);
        $data['postal_code'] = trim($data['postal_code']);
        $data['country'] = trim($data['country']);
        //CHECKING FOR VALID DATA
        if (empty($data['owner_name']) || empty($data['owner_address']) || empty($data['city']) || empty($data['region']) || empty($data['postal_code']) || empty($data['country'])) {
            $returnArray['error'] = 1;
            $returnArray['errorMessage'] = $view->translate("Please complete all field - it is required.");
            return $returnArray;
        }

        $params['OwnerName'] = $data['owner_name'];
        $params['OwnerAddress'] = $data['owner_address'];
        $params['OwnerAddress2'] = empty($data['owner_address2']) ? '' : $data['owner_address2'];
        $params['City'] = $data['city'];
        $params['Region'] = $data['region'];
        $params['PostalCode'] = $data['postal_code'];
        $params['Country'] = $data['country'];
        $params['Type'] = $data['account_type'];
        switch ($type) {
            case 'IBAN' :
                if (empty($data['iban']) || empty($data['bic'])) {
                    $error = true;
                }
                $params['IBAN'] = $data['iban'];
                $params['BIC'] = $data['bic'];
                break;
            case 'GB' :
                if (empty($data['sort_code']) || empty($data['account_number'])) {
                    $error = true;
                }
                $params['SortCode'] = $data['sort_code'];
                $params['AccountNumber'] = $data['account_number'];
                break;
            case 'US' :
                if (empty($data['deposit_account_type']) || empty($data['aba']) || empty($data['us_account_number'])) {
                    $error = true;
                }
                $params['ABA'] = $data['aba'];
                $params['AccountNumber'] = $data['us_account_number'];
                $params['DepositAccountType'] = $data['deposit_account_type'];
                break;
            case 'CA' :
                if (empty($data['branch_code']) || empty($data['bank_name']) || empty($data['institution_number']) || empty($data['ca_account_number'])) {
                    $error = true;
                }
                $params['BankName'] = $data['bank_name'];
                $params['BranchCode'] = $data['branch_code'];
                $params['AccountNumber'] = $data['ca_account_number'];
                $params['InstitutionNumber'] = $data['institution_number'];

                break;
            case 'OTHER' :
                if (empty($data['other_bic']) || empty($data['other_account_number'])) {
                    $error = true;
                }
                $params['BIC'] = $data['other_bic'];
                $params['AccountNumber'] = $data['other_account_number'];
                break;
        }
        if ($error) {
            $returnArray['error'] = 1;
            $returnArray['errorMessage'] = "<ul class='form-notices'><li>" . $view->translate("Please complete all field - it is required.") . "</li></ul>";
            return $returnArray;
        }
        $mangPayUserId = $sitestore_gateway_obj->config[$mode]['mangopay_user_id'];
        try {
            $bankDetail = $adminGateway->getService()->createMangoPayBankAccount($params, $mangPayUserId);
            $data['bank_account_id'] = $bankDetail->Id;
            if (isset($sitestore_gateway_obj->config[$mode]) && is_array($sitestore_gateway_obj->config[$mode])) {
                $config = array_merge($sitestore_gateway_obj->config[$mode], $data);
                $cf = $sitestore_gateway_obj->config;
                $cf[$mode] = $config;
                $sitestore_gateway_obj->config = $cf;
                $sitestore_gateway_obj->save();
            }
        } catch (Exception $ex) {
            $returnArray['error'] = 1;
            $returnArray['errorMessage'] = $ex->GetMessage() . "" . $ex->GetCode();
        }
        return $returnArray;
    }

}
