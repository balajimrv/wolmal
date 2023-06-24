<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Plugin_Dashboardmenus {

    private function getStoreId() {
        //GET STORE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }
        return $store_id;
    }

    public function onMenuInitialize_SitestoreDashboardGetstarted($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'name' => 'sitestore_dashboard_getstarted',
            'tab' => 12,
            'action' => 'get-started',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardEditinfo($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_edit',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_editinfo',
            'tab' => 1,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardProfileinfo($row) {

        //GET STORE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        $profileTypePrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'profile');
        if (empty($profileTypePrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'profile-type',
//        'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_editinfo',
            'tab' => 10,
            'params' => array(
                'store_id' => $sitestore->getIdentity(),
                'profile_type' => $sitestore->profile_type
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardProfilepicture($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'profile-picture',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_profilepicture',
            'tab' => 22,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardManageproducts($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_product_general',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_manageproducts',
            'tab' => 62,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardManagesections($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        $isSectionsAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.section.allowed', 1);
        if (empty($isSectionsAllowed)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_general',
            'action' => 'sections',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_managesections',
            'tab' => 88,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardManageorders($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_general',
            'action' => 'manage-order',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_manageorders',
            'actionName' => 'manage-order',
            'tab' => 55,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardShippingmethods($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_general',
            'action' => 'shipping-methods',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_shippingmethods',
            'tab' => 51,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardTaxes($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
        if (!empty($isVatAllow)) {
            $vatCreator = false; //Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.product.vat.creator', 0);
            if (!empty($vatCreator))
                return false;
            else
                $taxActionName = 'vat';
        } else {
            $taxActionName = 'index';
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_tax_general',
            'action' => $taxActionName,
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_taxes',
            'tab' => 52,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardPaymentaccount($row) {

        //GET STORE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

//    $viewer = Engine_Api::_()->user()->getViewer();
//    if ( $sitestore->owner_id != $viewer->getIdentity() || $viewer->level_id != 1 ) {
//      return false;
//    }

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        if (!empty($directPayment)) {
            return false;
        }

        return array(
            'label' => 'Payment Account',
            'route' => 'sitestoreproduct_product_general',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_paymentaccount',
            'tab' => 53,
            'params' => array(
                'store_id' => $sitestore->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardPaymentmethod($row) {

        //GET STORE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

//    $viewer = Engine_Api::_()->user()->getViewer();
//    if ( $sitestore->owner_id != $viewer->getIdentity() || $viewer->level_id != 1 ) {
//      return false;
//    }

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        if (empty($directPayment)) {
            return false;
        }

        return array(
            'label' => 'Payment Methods',
            'route' => 'sitestoreproduct_product_general',
            'action' => 'payment-info',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_paymentmethod',
            'tab' => 53,
            'params' => array(
                'store_id' => $sitestore->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardPaymentrequests($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        if (!empty($directPayment)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_product_general',
            'action' => 'payment-to-me',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_paymentrequests',
            'tab' => 56,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardYourbill($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }
        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        if (empty($directPayment)) {
            return false;
        }

        $commission = Engine_Api::_()->sitestoreproduct()->getOrderCommission($store_id);
        if (!empty($commission[1])) {
            return array(
                'label' => $row->label,
                'route' => 'sitestoreproduct_product_general',
                'action' => 'your-bill',
                'class' => 'ajax_dashboard_enabled',
                'name' => 'sitestore_dashboard_yourbill',
                'tab' => 56,
                'params' => array(
                    'store_id' => $store_id
                ),
            );
        } else
            return false;;
    }

    public function onMenuInitialize_SitestoreDashboardTransactions($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        if (!empty($directPayment)) {
            $transactionActionName = 'store-transaction';
        } else {
            $transactionActionName = 'transaction';
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_product_general',
            'action' => $transactionActionName,
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_transactions',
            'tab' => 54,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardSalesstatistics($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_product_general',
            'action' => 'store-dashboard',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_salesstatistics',
            'tab' => 60,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardGraphstatistics($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_statistics_general',
//        'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_graphstatistics',
            'tab' => 57,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardSalesreports($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_report_general',
            'action' => 'index',
//        'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_salesreports',
            'tab' => 61,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardOverview($row) {

        //GET PAGE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }
        $overviewPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
        if (empty($overviewPrivacy)) {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'overview',
            'name' => 'sitestore_dashboard_overview',
            'tab' => 2,
            'params' => array(
                'store_id' => $sitestore->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardContact($row) {

        //GET PAGE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        $contactPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
        if (empty($contactPrivacy)) {
            return false;
        }

        $contactSpicifyFileds = 0;
        $storeOwner = Engine_Api::_()->user()->getUser($sitestore->owner_id);
        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $storeOwner, 'contact_detail');
        $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email',);
        $options_create = array_intersect_key($availableLabels, array_flip($view_options));
        if (!empty($options_create)) {
            $contactSpicifyFileds = 1;
        }

        if (empty($contactSpicifyFileds)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'contact',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_contact',
            'tab' => 13,
            'params' => array(
                'store_id' => $sitestore->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardLocations($row) {

        //GET PAGE ID AND SITESTORE OBJECT
        $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        if ($sitestore->getType() !== 'sitestore_store') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        $mapPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
        if (empty($mapPrivacy) || !Engine_Api::_()->sitestore()->enableLocation()) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'all-location',
            'name' => 'sitestore_dashboard_locations',
            'tab' => 4,
            'params' => array(
                'store_id' => $sitestore->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardApps($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->sitestore()->getEnabledSubModules()) {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'app',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_apps',
            'tab' => 16,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardMarketing($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'marketing',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_marketing',
            'tab' => 20,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardManagenotifications($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'notification-settings',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_managenotifications',
            'tab' => 31,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardManageadmins($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_manageadmins',
            'action' => 'index',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_manageadmins',
            'tab' => 11,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardFeaturedadmins($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'featured-owners',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_featuredadmins',
            'tab' => 17,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardEditlayout($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->sitestore_layoutcreate) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_layout',
            'name' => 'sitestore_dashboard_editlayout',
//        'tab' => 17,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardImportproducts($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        } else {
            $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (empty($store->approved)) {
                return false;
            }
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestoreproduct_import_general',
            'action' => 'index',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_importproducts',
            'actionName' => 'import',
            'tab' => 89,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardEditstyle($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->sitestore()->allowStyle()) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitestore_dashboard',
            'action' => 'edit-style',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_editstyle',
            'tab' => 3,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardPackages($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
            return false;
        }
        return array(
            'label' => $row->label,
            'route' => 'sitestore_packages',
            'action' => 'update-package',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_packages',
            'tab' => 15,
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

    public function onMenuInitialize_SitestoreDashboardTermsConditions($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }

        $isTermsConditions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.terms.conditions', 0);

        if (!empty($isTermsConditions)) {
            return array(
                'label' => $row->label,
                'route' => 'sitestoreproduct_general',
                'action' => 'terms-and-conditions',
                'name' => 'sitestore_dashboard_terms_conditions',
                'tab' => 90,
                'params' => array(
                    'store_id' => $store_id
                ),
            );
        }
    }

    public function onMenuInitialize_SitestoreDashboardKycupload($row) {

        $store_id = $this->getStoreId();
        if (empty($store_id)) {
            return false;
        }
        $gateways = array();
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $gateways = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.split.gateway', array());
        } else if ($paymentMethod == 'escrow') {
            $gateways = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.escrow.gateway', array());
        }
        if (!in_array('mangopay', $gateways)) {
            return false;
        }
        return array(
            'label' => 'Upload KYC',
            'route' => 'sitestore_dashboard',
            'action' => 'upload-kyc',
            'class' => 'ajax_dashboard_enabled',
            'name' => 'sitestore_dashboard_kycupload',
            'params' => array(
                'store_id' => $store_id
            ),
        );
    }

}