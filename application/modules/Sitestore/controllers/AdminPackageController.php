<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminPackageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminPackageController extends Core_Controller_Action_Admin {

    public function init() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_package');

        $this->_viewer = Engine_Api::_()->user()->getViewer();
        $this->_viewer_id = $this->_viewer->getIdentity();
    }

    //ACTION FOR MANAGE PACKAGE LISTINGS
    public function indexAction() {

        $this->view->canCreate = $canCreate = 1;
        if (Engine_Api::_()->sitestore()->enablePaymentPlugin()) {

            //TEST CURL SUPPORT
            if (!function_exists('curl_version') ||
                    !($info = curl_version())) {
                $this->view->error = $this->view->translate('The PHP extension cURL' .
                        'does not appear to be installed, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.');
            }
            //TEST CURL SSL SUPPORT
            else if (!($info['features'] & CURL_VERSION_SSL) ||
                    !in_array('https', $info['protocols'])) {
                $this->view->error = $this->view->translate('The installed version of' .
                        'the cURL PHP extension does not support HTTPS, which is required' .
                        'for interaction with payment gateways. Please contact your' .
                        'hosting provider.');
            }
            //CHECK FOR ENABLE PAYMENT GATEWAYS
            else if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $this->view->error = $this->view->translate('There are currently no enabled payment gateways. You must %1$senable payment gatways%2$s before creating a paid package.', '<a href="' .
                        $this->view->escape($this->view->url(array('module' => 'payment', 'controller' => 'gateway'))) .
                        '"  target="_blank" >', '</a>');
            }
        } else {
            $this->view->canCreate = $canCreate = 0;
            $this->view->error = $this->view->translate('You have not install or enable "Payment" module. Please install or enable "Payment" module to create or edit package.');
        }

        //INITILIZE SELECT
        $table = Engine_Api::_()->getDbtable('packages', 'sitestore');
        $storeName = Engine_Api::_()->getItemtable('sitestore_store')->info("name");
        $select = $table->select();

        //FILTER FORM
        $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Package_Filter();

        //PROCESS FORM
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        }
        if (empty($filterValues['order'])) {
            $select->order("order");
            $filterValues['order'] = 'package_id';
        }
        if (empty($filterValues['direction'])) {

            $filterValues['direction'] = 'DESC';
        }
        $this->view->filterValues = $filterValues;
        $this->view->order = $filterValues['order'];
        $this->view->direction = $filterValues['direction'];

        //ADD FILTER VALUES
        if (!empty($filterValues['query'])) {
            $select->where('title LIKE ?', '%' . $filterValues['query'] . '%');
        }

        if (isset($filterValues['enabled']) && '' != $filterValues['enabled']) {
            $select->where('enabled = ?', $filterValues['enabled']);
        }

        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'ASC';
            }
            $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }

        //GET DATA
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $tempCount = $paginator->getTotalItemCount();
        if (!empty($tempCount) && $tempCount == 1)
            $this->view->showCountError = true;

        //GET STORES TOTALS FOR EACH PACKAGE
        $memberCounts = array();
        foreach ($paginator as $item) {
            $memberCounts[$item->package_id] = Engine_Api::_()->getDbtable('stores', 'sitestore')
                    ->select()
                    ->from('engine4_sitestore_stores', new Zend_Db_Expr('COUNT(*)'))
                    ->where('package_id = ?', $item->package_id)
                    ->query()
                    ->fetchColumn();
        }
        $this->view->memberCounts = $memberCounts;
    }

    //ACTION FOR PACKAGE CREATE
    public function createAction() {

        if (!Engine_Api::_()->sitestore()->enablePaymentPlugin()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitestore_Form_Admin_Package_Create();

        //GET SUPPORTED BILLING CYCLES
        $gateways = array();
        $supportedBillingCycles = array();
        $partiallySupportedBillingCycles = array();
        $fullySupportedBillingCycles = null;
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

//          foreach ($gatewaysTable->fetchAll() as $gateway){
//          $gateways[$gateway->gateway_id] = $gateway->title;
//          $gatewaySupportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
//          if(count($gatewaySupportedBillingCycles)==0)
//          continue;
//          $supportedBillingIndex[$gateway->title] = $gatewaySupportedBillingCycles;
//          $supportedBillingCycles[$gateway->gateway_id] = $gatewaySupportedBillingCycles;
//          $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//          if (null == $fullySupportedBillingCycles) {
//          $fullySupportedBillingCycles = $supportedBillingCycles[$gateway->gateway_id];
//          } else {
//          $fullySupportedBillingCycles = array_intersect($fullySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
//          }
//          }
//
//          $partiallySupportedBillingCycles = array_diff($partiallySupportedBillingCycles, $fullySupportedBillingCycles);
//
//          $multiOptions = array();
//          if(count($fullySupportedBillingCycles)>0){
//          $multiOptions = array_combine(array_map('strtolower', $fullySupportedBillingCycles), $fullySupportedBillingCycles);
//          }

        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gatewaySupportedBillingCycles = $gateway->getGateway()->getSupportedBillingCycles();
            $gateways[$gateway->gateway_id] = $gateway->title;
            $supportedBillingIndex[$gateway->title] = $gatewaySupportedBillingCycles;
            $supportedBillingCycles[$gateway->gateway_id] = $gatewaySupportedBillingCycles;
            $partiallySupportedBillingCycles = array_merge($partiallySupportedBillingCycles, $supportedBillingCycles[$gateway->gateway_id]);
        }
        $partiallySupportedBillingCycles = array_unique($partiallySupportedBillingCycles);
        $multiOptions = array_combine(array_map('strtolower', $partiallySupportedBillingCycles), $partiallySupportedBillingCycles);
        unset($multiOptions['one-time']);
        $this->view->gateways = $gateways;
        $this->view->supportedBillingIndex = $supportedBillingIndex;
        $form->getElement('recurrence')
                ->setMultiOptions($multiOptions);
        $form->getElement('recurrence')->options['forever'] = 'One-time';
        //$form->getElement('recurrence')->options['day'] = 'Day';

        $form->getElement('duration')
                ->setMultiOptions($multiOptions);
        $form->getElement('duration')->options/* ['Fully Supported'] */['forever'] = 'Forever';

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
            $form->getElement('ads');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
            $form->getElement('twitter');
        }

        //FORM VALDIATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $this->getRequest()->getPost();

        $tmp = $values['recurrence'];
        unset($values['recurrence']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['recurrence'] = (int) $tmp[0];
        $values['recurrence_type'] = $tmp[1];

        if (!isset($values['ads'])) {
            $values['ads'] = 0;
        }

        if (!isset($values['twitter'])) {
            $values['twitter'] = 0;
        }

        if ($values['price'] > 0) {

            //FOR NOT ENABLE GATEWAYS
            if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0) {
                $form->getDecorator('errors')->setOption('escape', false);

                $error = $this->view->translate('You have not enabled a payment gateway yet. Please %1$senable payment gateways%2$s  before creating a paid package.', '<a href="' . $this->view->baseUrl() . '/admin/payment/gateway" ' . " target='_blank'" . '">', '</a>');
                $this->view->status = false;
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                return $form->addError($error);
            }
        }

        if (in_array("downloadable", $values['product_type'])) {
            if (empty($values['filesize_main'])) {
                $error = Zend_Registry::get('Zend_Translate')->_('Allow File Size to Upload - It\'s required field.');
                return $form->addError($error);
            }

            if (empty($values['filesize_sample'])) {
                $error = Zend_Registry::get('Zend_Translate')->_('Allow Sample File Size to Upload-It\'s required field.');
                return $form->addError($error);
            }
        }
        if (!is_array($values['product_type'])) {
            $error = Zend_Registry::get('Zend_Translate')->_('Please select Product Types - It\'s required.');
            return $form->addError($error);
        }

        //for member level seting work
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }

        $tmp = $values['duration'];
        unset($values['duration']);
        if (empty($tmp) || !is_array($tmp)) {
            $tmp = array(null, null);
        }
        $values['duration'] = (int) $tmp[0];
        $values['duration_type'] = $tmp[1];
        if (isset($values['modules']))
            $values['modules'] = serialize($values['modules']);
        else
            $values['modules'] = serialize(array());

        $profileFields = array();
        if ($values['profile'] == 2) {
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }
        $values['profilefields'] = serialize($profileFields);

        //for 'Sale to Access Levels' seting work
        if (@in_array('0', $values['sale_to_access_levels'])) {
            $values['sale_to_access_levels'] = 0;
        } else {
            $values['sale_to_access_levels'] = implode(',', $values['sale_to_access_levels']);
        }

        $productValues = array();
        $productValues['product_type'] = $values['product_type'];
        $productValues['sitestoreproduct_main_files'] = $values['sitestoreproduct_main_files'];
        $productValues['sitestoreproduct_sample_files'] = $values['sitestoreproduct_sample_files'];
        $productValues['filesize_main'] = $values['filesize_main'];
        $productValues['filesize_sample'] = $values['filesize_sample'];
        $productValues['max_product'] = $values['max_product'];
        $productValues['comission_handling'] = $values['comission_handling'];
        $productValues['comission_fee'] = $values['comission_fee'];
        $productValues['comission_rate'] = $values['comission_rate'];
        $productValues['transfer_threshold'] = $values['transfer_threshold'];
        $productValues['allow_selling_products'] = $values['allow_selling_products'];
        $productValues['allow_non_selling_product_price'] = $values['allow_non_selling_product_price'];
        $productValues['online_payment_threshold'] = $values['online_payment_threshold'];
        $productValues['sale_to_access_levels'] = $values['sale_to_access_levels'];
        $values['store_settings'] = @serialize($productValues);
        unset($values['product_type']);
        unset($values['sitestoreproduct_main_files']);
        unset($values['sitestoreproduct_sample_files']);
        unset($values['filesize_main']);
        unset($values['filesize_sample']);
        unset($values['max_product']);
        unset($values['comission_handling']);
        unset($values['comission_fee']);
        unset($values['comission_rate']);
        unset($values['transfer_threshold']);
        unset($values['allow_selling_products']);
        unset($values['allow_non_selling_product_price']);
        unset($values['online_payment_threshold']);
        unset($values['sale_to_access_levels']);



        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitestore');
        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {

            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';

            // Create package in gateways?
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();
                    // Check billing cycle support
                    if (!$package->isOneTime()) {
                        $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                        if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                            continue;
                        }
                    }
                    if (method_exists($gatewayPlugin, 'createProduct')) {
                        $gatewayPlugin->createProduct($package->getGatewayParams());
                    }
                }

                //START This code use for coupon edit when Create a new package and select all those coupon which have select all option for this package type.
                $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon');
                if (!empty($moduleEnabled)) {
                    Engine_Api::_()->getDbtable('coupons', 'sitecoupon')->editCouponsAfterCreateNewPackage($package->getType());
                }
                //END COUPON WORK.
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR PACKAGE EDIT
    public function editAction() {

        if (!Engine_Api::_()->sitestore()->enablePaymentPlugin()) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET PACKAGES
        if (null == ($packageIdentity = $this->_getParam('package_id')) ||
                !($package = Engine_Api::_()->getDbtable('packages', 'sitestore')->find($packageIdentity)->current())) {
            throw new Engine_Exception('No package found');
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitestore_Form_Admin_Package_Edit();

        $values = $package->toArray();

        if (!empty($values['store_settings'])) {
            $getProductSettings = @unserialize($values['store_settings']);
            unset($values['store_settings']);
            $values = @array_merge($values, $getProductSettings);
        }

        $values['recurrence'] = array($values['recurrence'], $values['recurrence_type']);

        $values['duration'] = array($values['duration'], $values['duration_type']);

        unset($values['recurrence_type']);

        unset($values['duration_type']);
        $values['level_id'] = explode(',', $values['level_id']);

        if (isset($values['sale_to_access_levels']))
            $values['sale_to_access_levels'] = explode(',', $values['sale_to_access_levels']);

        $otherValues = array(
            'price' => $values['price'],
            'recurrence' => $values['recurrence'],
            'duration' => $values['duration'],
        );
        $values['modules'] = unserialize($values['modules']);
        $allow_selling_previous = array_key_exists('allow_selling_products', $values) ? $values['allow_selling_products'] : 1;
        $form->populate($values);
        $profileFields = array();
        if ($values['profile'] == 2) {
            $profileFields = unserialize($values['profilefields']);
        }
        $session = new Zend_Session_Namespace('profileFields');
        $session->profileFields = $profileFields;

        //CHECK METHOD DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        if (isset($session->profileFields)) {
            unset($session->profileFields);
        }

        //HACK EM UP
        $form->populate($otherValues);

        //PROCESS
        $values = $form->getValues();

        //for member level seting work
        if (@in_array('0', $values['level_id'])) {
            $values['level_id'] = 0;
        } else {
            $values['level_id'] = implode(',', $values['level_id']);
        }

        //for 'Sale to Access Levels' seting work
        if (@in_array('0', $values['sale_to_access_levels'])) {
            $values['sale_to_access_levels'] = 0;
        } else {
            $values['sale_to_access_levels'] = implode(',', $values['sale_to_access_levels']);
        }

        unset($values['price']);
        unset($values['recurrence']);
        unset($values['recurrence_type']);
        unset($values['duration']);
        unset($values['duration_type']);
        unset($values['trial_duration']);
        unset($values['trial_duration_type']);

        if (in_array("downloadable", $values['product_type'])) {
            if (empty($values['filesize_main'])) {
                $error = Zend_Registry::get('Zend_Translate')->_('Allow File Size to Upload - It\'s required field.');
                return $form->addError($error);
            }

            if (empty($values['filesize_sample'])) {
                $error = Zend_Registry::get('Zend_Translate')->_('Allow Sample File Size to Upload-It\'s required field.');
                return $form->addError($error);
            }
        }
        if (!is_array($values['product_type'])) {
            $error = Zend_Registry::get('Zend_Translate')->_('Please select Product Types - It\'s required.');
            return $form->addError($error);
        }

        if (isset($values['modules']))
            $values['modules'] = serialize($values['modules']);
        else
            $values['modules'] = serialize(array());
        $profileFields = array();
        if ($values['profile'] == 2) {
            $i = 0;
            foreach ($_POST as $key => $value) {
                if (@strstr($key, '_profilecheck_') != null && $value) {
                    $tc = @explode("_profilecheck_", $key);
                    $profileFields[] = "1_" . $tc[0] . "_" . $value;
                }
            }
        }

        $productValues = array();
        $productValues['product_type'] = $values['product_type'];
        $productValues['sitestoreproduct_main_files'] = $values['sitestoreproduct_main_files'];
        $productValues['sitestoreproduct_sample_files'] = $values['sitestoreproduct_sample_files'];
        $productValues['filesize_main'] = $values['filesize_main'];
        $productValues['filesize_sample'] = $values['filesize_sample'];
        $productValues['max_product'] = $values['max_product'];
        $productValues['comission_handling'] = $values['comission_handling'];
        $productValues['comission_fee'] = $values['comission_fee'];
        $productValues['comission_rate'] = $values['comission_rate'];
        $productValues['allow_selling_products'] = $values['allow_selling_products'];
        $productValues['allow_non_selling_product_price'] = $values['allow_non_selling_product_price'];
        $productValues['online_payment_threshold'] = $values['online_payment_threshold'];
        $productValues['transfer_threshold'] = $values['transfer_threshold'];
        $productValues['sale_to_access_levels'] = $values['sale_to_access_levels'];
        $values['store_settings'] = @serialize($productValues);
        unset($values['product_type']);
        unset($values['sitestoreproduct_main_files']);
        unset($values['sitestoreproduct_sample_files']);
        unset($values['filesize_main']);
        unset($values['filesize_sample']);
        unset($values['max_product']);
        unset($values['comission_handling']);
        unset($values['comission_fee']);
        unset($values['comission_rate']);
        unset($values['allow_selling_products']);
        unset($values['allow_non_selling_product_price']);
        unset($values['online_payment_threshold']);
        unset($values['transfer_threshold']);
        unset($values['sale_to_access_levels']);

        $values['profilefields'] = serialize($profileFields);

        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitestore');

//    if($productValues['allow_selling_products'] != $allow_selling_previous){
//       $this->allowSellingProducts($packageIdentity, $productValues['allow_selling_products']);
//    }

        $db = $packageTable->getAdapter();
        $db->beginTransaction();

        try {

            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';

            //CREATE PACKAGE IN GATEWAYS
            if (!$package->isFree()) {
                $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
                foreach ($gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway) {
                    $gatewayPlugin = $gateway->getGateway();

                    //CHECK BILLING CYCLE SUPPORT
                    if (!$package->isOneTime()) {
                        $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                        if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                            continue;
                        }
                    }
                    if (!method_exists($gatewayPlugin, 'createProduct') ||
                            !method_exists($gatewayPlugin, 'editProduct') ||
                            !method_exists($gatewayPlugin, 'detailVendorProduct')) {
                        continue;
                    }

                    //IF IT THROWS AN EXCEPTION, OR RETURNS EMPTY, ASSUME IT DOESN'T EXIST?
                    try {
                        $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
                    } catch (Exception $e) {
                        $info = false;
                    }
                    //CREATE
                    if (!$info) {
                        $gatewayPlugin->createProduct($package->getGatewayParams());
                    }
                    //EDIT
                    else {
                        $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    //ACTION FOR SHOW THE PACKAGE DETAILS
    public function packgeDetailAction() {
        $id = $this->_getParam('id');
        if (empty($id)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $this->view->package = Engine_Api::_()->getItem('sitestore_package', $id);
    }

    //ACTION FOR PACKAGE UPDATION
    public function updateAction() {

        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            try {
                foreach ($values['order'] as $key => $value) {

                    $package = Engine_Api::_()->getItem('sitestore_package', (int) $value);
                    if (!empty($package)) {
                        $package->order = $key + 1;
                        $package->save();
                    }
                }
                $db->commit();
                $this->_helper->redirector->gotoRoute(array('action' => 'index'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR MAKE PACKAGES ENABLE/DISABLE
    public function enabledAction() {
        $id = $this->_getParam('id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $package = Engine_Api::_()->getItem('sitestore_package', $id);
        if ($package->enabled == 0) {
            try {
                $package->enabled = 1;
                $package->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sitestore/package');
        } else {
            if ($this->getRequest()->isPost()) {
                try {
                    $package->enabled = 0;
                    $package->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
                ));
            }
        }
    }

//  protected function allowSellingProducts($packageIdentity, $allow_selling){
//    
//    $storeTable = Engine_Api::_()->getDbTable('stores', 'sitestore');
//    $storeTableName = $storeTable->info('name');
//    
//    $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
//    $productTableName = $productTable->info('name');
//    
//    $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
//    
//    $select = $productTable->select()
//            ->setIntegrityCheck(false)
//            ->from($productTableName)
//            ->join($storeTableName, "($storeTableName.store_id = $productTableName.store_id)", NULL)
//            ->where($storeTableName . '.package_id = ?', $packageIdentity);
//
//     $productsObj = $productTable->fetchAll($select);
//     
//      foreach($productsObj as $product){
//      $productTable->update(array('allow_purchase' => $allow_selling), array('product_id = ?' => $product->product_id));
//      if (empty($allow_selling)) {
//        $cartProductTable->delete(array('product_id = ?' => $product->product_id));
//      }
//    }
//  }
    //ACTION FOR MANAGING THE PACKAGE-PLAN MAPPING
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_package');

        //FETCH MAPPING DATA
        $tablePlanmaps = Engine_Api::_()->getDbtable('planmaps', 'sitestore')->info('name');
        $table = Engine_Api::_()->getDbtable('packages', 'payment');
        $storePackageTableName = Engine_Api::_()->getDbtable('packages', 'sitestore')->info('name');

        $paymentPackageTableName = $table->info('name');
        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($paymentPackageTableName)
                ->joinLeft($tablePlanmaps, "$paymentPackageTableName.package_id = $tablePlanmaps.plan_id", array('package_id as map_package_id', 'planmap_id'))
                ->joinLeft($storePackageTableName, "$tablePlanmaps.package_id = $storePackageTableName.package_id", array('title as store_package_title'))
                ->where($paymentPackageTableName . '.enabled = ?', 1);
        $temp_result = $table->fetchAll($select);
        $this->view->plans = $temp_result;
    }

    //ACTION FOR MAP THE PACKAGE WITH PLAN
    public function mapAction() {

        $this->_helper->layout->setLayout('admin-simple');

        $table = Engine_Api::_()->getDbtable('packages', 'sitestore');
        $storeName = Engine_Api::_()->getItemtable('sitestore_store')->info("name");

        //GENERATE THE FORM
        $form = $this->view->form = new Sitestore_Form_Admin_Package_Map();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                //SAVE THE NEW MAPPING
                $row = Engine_Api::_()->getDbtable('planmaps', 'sitestore')->createRow();
                $row->package_id = $values['package'];
                $row->plan_id = $this->_getParam('plan_id');
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }

        $this->renderScript('admin-package/map.tpl');
    }

    //ACTION FOR DELETE MAPPING 
    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');

        //GET MAPPING ID
        $this->view->planmap_id = $planmap_id = $this->_getParam('planmap_id');

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET MAPPING ITEM
                $sitestore_planmap = Engine_Api::_()->getItem('sitestore_planmap', $planmap_id);

                //DELETE MAPPING
                $sitestore_planmap->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
            ));
        }
        $this->renderScript('admin-package/delete.tpl');
    }

}
?>


