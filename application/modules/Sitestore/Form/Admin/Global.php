<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Global extends Engine_Form {

    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
                ->setTitle('Stores - Global Settings')
                ->setDescription('These settings affect all members in your community.');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->addElement('Text', 'sitestore_lsettings', array(
            'label' => 'Enter License key For Stores / Marketplace - Ecommerce',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettings->getSetting('sitestore.lsettings'),
        ));

        $isSitestoredocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
        if (!empty($isSitestoredocumentEnabled)) {
            $this->addElement('Text', 'sitestoredocument_lsettings', array(
                'label' => 'Enter License key For Stores / Marketplace - Documents Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('sitestoredocument.lsettings'),
            ));
        }

        $isSitestoredocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
        if (!empty($isSitestoredocumentEnabled)) {
            $this->addElement('Text', 'sitestoreintegration_lsettings', array(
                'label' => 'Enter License key For Stores / Marketplace - Multiple Listings, Pages, Businesses and Groups Showcase Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('sitestoreintegration.lsettings'),
            ));
        }

        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
                'value' => 1,
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        //Add submit button
        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));


        $this->addElement('Text', 'language_phrases_store', array(
            'label' => 'Singular Store Text',
            'description' => 'Please enter the Singular Text for the term: "store". This text will come at various places like feeds generated, widgets, etc.',
            'allowEmpty' => FALSE,
            'validators' => array(
                array('NotEmpty', true),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.store", "Store"),
        ));

        $this->addElement('Text', 'language_phrases_stores', array(
            'label' => 'Plural Store Text',
            'description' => 'Please enter the Plural Text for the term: "stores". This text will come at various places like Main Navigation Menu, Stores Navigation Menu, widgets, etc.',
            'allowEmpty' => FALSE,
            'validators' => array(
                array('NotEmpty', true),
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("language.phrases.stores", "Stores"),
        ));



        $this->addElement('Text', 'sitestore_manifestUrlP', array(
            'label' => 'Stores URL alternate text for "stores"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "stores" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitestore.manifestUrlP', "stores"),
        ));

        $this->addElement('Text', 'sitestore_manifestUrlS', array(
            'label' => 'Stores URL alternate text for "store"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "store" in the URLs of this plugin.',
            'value' => $coreSettings->getSetting('sitestore.manifestUrlS', "store"),
        ));


        //SETTINGS FOR ENABLED AND DISABLED "ADMIN DRIVEN STORE" AND "USER DRIVEN STORE"
        $this->addElement('Radio', 'is_sitestore_admin_driven', array(
            'label' => 'Setup Type: Admin Stores or Marketplace',
            'description' => 'Please choose the type of Stores setup that you want in your community. You can choose to have either Admin Stores (All stores will be operated and managed by you. Only you will be able to create and sell products.) OR Multiple User-driven Stores / Marketplace with multiple sellers (Users will be able to create stores based on Member Level Settings and sell products.).',
            'multiOptions' => array(
                0 => 'Multiple User-driven Stores (Marketplace with multiple Sellers)',
                1 => 'Admin Stores (Stores operated by you. Choosing this sets Member Level Settings such that only Super Admin can create stores. You can modify these Member Level Settings to allow other administrators / moderators to create or manage stores.)'
            ),
            'onchange' => 'showPaymentForOrders(this.value);',
            'value' => $coreSettings->getSetting('is.sitestore.admin.driven', 0),
        ));
        //PAYMENT GATEWAY
       $stripeconnect=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0);

        $splitGateways = array();
        $escrowGateway = array();
        $normalGateway = array();
        $otherGateways = array();
        $isSitegatewayEnabled = Engine_Api::_()->hasModuleBootstrap('sitegateway');
        if ($isSitegatewayEnabled) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('pluginLike' => 'Sitegateway_Plugin_Gateway_'));
            foreach ($getEnabledGateways as $getEnabledGateway) {
                $gatewayKey = strtolower($getEnabledGateway->title);
                if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive' || $getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                    $escrowGateway[$gatewayKey] = $getEnabledGateway->title;
                    $splitGateways[$gatewayKey] = $getEnabledGateway->title;
                } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && !empty($stripeconnect)) {
                    $splitGateways[$gatewayKey] = $getEnabledGateway->title;
                    $normalGateway['gateway_' . $gatewayKey] = $getEnabledGateway->title;
                    $otherGateways[$gatewayKey] = $getEnabledGateway->title;
                } else {
                    $normalGateway['gateway_' . $gatewayKey] = $getEnabledGateway->title;
                    $otherGateways[$gatewayKey] = $getEnabledGateway->title;
                }
            }
        }
        $normalGateway = array_merge($normalGateway, array(
            'gateway_0' => 'PayPal',
            'gateway_1' => 'By Cheque',
            'gateway_2' => 'Cash on Delivery'
        ));
        $splitGateways = array_merge($splitGateways, array(
            'cheque' => 'By Cheque',
            'cash' => 'Cash on Delivery'
        ));
        $escrowGateway = array_merge($escrowGateway, array(
            'cheque' => 'By Cheque',
            'cash' => 'Cash on Delivery'
        ));
        $tempNormalGateway = $coreSettings->getSetting('sitestore.allowed.payment.gateway');
        if (!empty($tempNormalGateway)) {
            $tempNormalGateway = Zend_Json_Decoder::decode($tempNormalGateway);
        } else {
            $tempNormalGateway = array(0, 1, 2);
        }
        foreach ($tempNormalGateway as $tempGt) {
            $normalGateways[] = 'gateway_' . $tempGt;
        }


        if ($isSitegatewayEnabled) {
            $this->addElement('Radio', 'sitestore_payment_method', array(
                'label' => 'Payment Method',
                'description' => '[Note: ‘By Cheque’ and ‘Cash on Delivery’ , these two payment options will be available for each type of payment method.]',
                'multiOptions' => array('split' => 'Split Immediately', 'escrow' => 'Escrow', 'normal' => 'Normal'),
                'onclick' => 'selectPaymentMethod(this.value)',
                'value' => $coreSettings->getSetting('sitestore.payment.method', 'normal'),
            ));
            $this->sitestore_payment_method->getDecorator("Description")->setOption("placement", "append");

            $this->addElement('MultiCheckbox', 'sitestore_allowed_payment_escrow_gateway', array(
                'label' => 'Escrow Payment Gateways',
                'multiOptions' => $escrowGateway,
                'value' => $coreSettings->getSetting('sitestore.allowed.payment.escrow.gateway'),
            ));

            $this->addElement('MultiCheckbox', 'sitestore_allowed_payment_split_gateway', array(
                'label' => 'Split Payment Gateways',
                'multiOptions' => $splitGateways,
                'value' => $coreSettings->getSetting('sitestore.allowed.payment.split.gateway'),
            ));
        }
        //SETTINGS FOR "DIRECT PAYEMENT TO SELLERS" OR "PAYMENT TO WEBSITE / SITEADMIN"
        $this->addElement('Radio', 'sitestore_payment_for_orders', array(
            'label' => 'Payment for Orders',
            'description' => 'Please choose the default payment flow for orders on your website.',
            'multiOptions' => array(
                0 => 'Direct Payment to Sellers',
                1 => 'Payment to Website / Site Admin'
            ),
            'onchange' => 'showPaymentForOrdersGateway(this.value)',
            'value' => $coreSettings->getSetting('sitestore.payment.for.orders', 0),
        ));

        $this->addElement('MultiCheckbox', 'sitestore_allowed_payment_gateway', array(
            'label' => 'Payment Gateways',
            'multiOptions' => $normalGateway,
            'value' => $normalGateways,
        ));
        try {
            $store_admin_gateways = Zend_Json_Decoder::decode($coreSettings->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1))));
        } catch (Exception $ex) {
            $store_admin_gateways = $coreSettings->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1)));
        }

        $sitestoreproduct_admin_gateway_description = sprintf(Zend_Registry::get('Zend_Translate')->_('Select the payment gateway to be available during checkout process. [To enable payment gateways PayPal and 2Checkout, click %1$shere%2$s.]'), "<a href='" . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . "' target='_blank'>", "</a>");

        //PAYMENT GATEWAY FOR "PAYMENT TO WEBSITE / SITE ADMIN"
        $this->addElement('MultiCheckbox', 'sitestore_admin_gateway', array(
            'label' => 'Payment Gateways',
            'description' => $sitestoreproduct_admin_gateway_description,
            'multiOptions' => array(
                0 => 'By Cheque',
                1 => 'Cash on Delivery'
            ),
            'value' => $store_admin_gateways,
        ));
        $this->sitestore_admin_gateway->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Textarea', 'send_cheque_to', array(
            'label' => 'Send Cheque To',
            'description' => 'Enter your account details which buyers will fill in the cheques for making payments for their orders. This information will be shown when buyers choose "By Cheque" method in the "Payment Information" section during their checkout process. [You can enable / disable this cheque option from Member Level Settings.]',
            'value' => $coreSettings->getSetting('send.cheque.to', ''),
        ));

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {

            $gatewayOptions = array_merge(array('paypal' => 'Paypal'), $otherGateways);

            $this->addElement('Radio', 'sitestoreproduct_paymentmethod', array(
                'label' => "Payment for 'Commissions Bill'",
                'description' => "Select the payment gateway to be available to sellers for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Sellers’ is selected.",
                'multiOptions' => $gatewayOptions,
                'value' => $coreSettings->getSetting('sitestoreproduct.paymentmethod', 'paypal'),
            ));
        }

        //VALUE FOR ENABLE/DISABLE PACKAGE
        $this->addElement('Radio', 'sitestore_package_enable', array(
            'label' => 'Packages',
            'description' => 'Do you want Packages to be activated for stores? Packages can vary based on the features available to the stores created under them. If enabled, users will have to select a package in the first step while creating a new store. Store admins will be able to change their package later. To manage store packages, go to Manage Packages section. (Note: If packages are enabled, then feature settings for stores will depend on packages, and member levels based feature settings will be off. If packages are disabled, then feature settings for stores could be configured for member levels.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showpackageOption(this.value)',
            'value' => $coreSettings->getSetting('sitestore.package.enable', 1),
        ));

        $this->addElement('Radio', 'sitestore_package_view', array(
            'label' => 'Package View',
            'description' => 'Select the view type of packages that will be shown in the first step of store creation.',
            'multiOptions' => array(
                1 => 'Vertical',
                0 => 'Horizontal'
            ),
            'value' => $coreSettings->getSetting('sitestore.package.view', 1),
        ));

        $packageInfoArray = array('price' => 'Price', 'billing_cycle' => 'Billing Cycle', 'duration' => 'Duration', 'featured' => 'Featured', 'sponsored' => 'Sponsored', 'tellafriend' => 'Tell a friend', 'print' => 'Print', 'overview' => 'Rich Overview', 'map' => 'Map', 'contactdetails' => 'Contact Details', 'sendanupdate' => 'Send an Update', 'apps' => 'Apps available', 'description' => 'Description');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
            $packageInfoArray = array_merge($packageInfoArray, array('twitterupdates' => 'Display Twitter Updates'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
            $packageInfoArray = array_merge($packageInfoArray, array('ads' => 'Ads Display'));
        }

        $this->addElement('MultiCheckbox', 'sitestore_package_information', array(
            'label' => 'Package Information',
            'description' => 'Select the information options that you want to be available in package details.',
            'multiOptions' => $packageInfoArray,
            'value' => $coreSettings->getSetting('sitestore.package.information', array_keys($packageInfoArray)),
        ));

        $this->addElement('Radio', 'sitestore_payment_benefit', array(
            'label' => 'Payment Status for Stores Activation',
            'description' => "Do you want to activate stores immediately after payment, before the payment passes the gateways' fraud checks? This may take any time from 20 minutes to 4 days, depending on the circumstances and the gateway. (Note: If you want to manually activate stores, then you can set this while creating a store package.)",
            'multiOptions' => array(
                'all' => 'Activate store immediately.',
                'some' => 'Activate if member has an existing successful transaction, wait if this is their first.',
                'none' => 'Wait until the gateway signals that the payment has completed successfully.',
            ),
            'value' => $coreSettings->getSetting('sitestore.payment.benefit', 'all'),
        ));

        $this->addElement('Select', 'sitestoreproduct_weight_unit', array(
            'label' => "Default Weight Matrix",
            'description' => 'Select the default unit of weight for the products on your site.',
            'multiOptions' => array(
                'lbs' => "Pound",
                "kg" => "Kilogram",
                "gm" => "Gram",
                "oz" => "Ounce"
            ),
            'value' => $coreSettings->getSetting('sitestoreproduct.weight.unit', 'lbs'),
        ));

        $this->addElement('Radio', 'sitestore_manageadmin', array(
            'label' => 'Store Admins',
            'description' => 'Do you want there to be multiple admins for stores on your site? (If enabled, then every Store will be able to have multiple administrators who will be able to manage that Store. Store Admins will have the authority to add other users as administrators of their Store.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.manageadmin', 1),
        ));

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu')) {
            $this->addElement('Radio', 'sitestore_show_menu', array(
                'label' => 'Stores Link',
                'description' => 'Select the location of the main link for Stores.',
                'multiOptions' => array(
                    3 => 'Main Navigation Menu',
                    2 => 'Mini Navigation Menu',
                    1 => 'Footer Menu',
                    0 => 'Member Home Page Left side Navigation'
                ),
                'value' => $coreSettings->getSetting('sitestore.show.menu', 3),
            ));
        }
        //VALUE FOR ENABLE/DISABLE REPORT
        $this->addElement('Radio', 'sitestore_report', array(
            'label' => 'Report as Inappropriate',
            'description' => 'Do you want to allow logged-in members to be able to report stores as inappropriate? (Members will also be able to mention the reason why they find the store inappropriate.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.report', 1),
        ));

        //VALUE FOR ENABLE /DISABLE SHARE
        $this->addElement('Radio', 'sitestore_share', array(
            'label' => 'Community Sharing',
            'description' => 'Do you want to allow members to share stores within your community?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.share', 1),
        ));

        //VALUE FOR ENABLE /DISABLE SHARE
        $this->addElement('Radio', 'sitestore_socialshare', array(
            'label' => 'Social Sharing',
            'description' => 'Do you want social sharing to be enabled for stores? (If enabled, social sharing buttons will be shown on the Profile page of stores.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.socialshare', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitestore_captcha_post', array(
            'label' => 'CAPTCHA For Tell a friend',
            'description' => 'Do you want visitors to enter a validation code in Tell a friend form?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.captcha.post', 1),
        ));
        $this->addElement('Radio', 'sitestore_description_allow', array(
            'label' => 'Allow Description',
            'description' => 'Do you want to allow page owners to write description for their stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.description.allow', 1),
            'onclick' => 'showDescription(this.value)'
        ));

        //VALUE FOR DESCRIPTION
        $this->addElement('Radio', 'sitestore_requried_description', array(
            'label' => 'Description Required',
            'description' => 'Do you want to make Description a mandatory field for stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.requried.description', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitestore_requried_photo', array(
            'label' => 'Profile Photo Required',
            'description' => 'Do you want to make Profile Photo a mandatory field for stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.requried.photo', 0),
        ));

        $this->addElement('Radio', 'sitestore_status_show', array(
            'label' => 'Open / Closed status in Search',
            'description' => 'Do you want the Status field (Open / Closed) in the search form widget? (This widget appears on the "Stores Home" and "Browse Stores" pages, and enables users to search and filter stores.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.status.show', 0),
        ));

        /* $this->addElement('Radio', 'sitestore_profile_search', array(
          'label' => 'Profile Type in Search',
          'description' => 'Do you want the Profile Type field in the search form widget at "Stores Home" and "Browse Stores" stores?',
          'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
          ),
          'value' => $coreSettings->getSetting('sitestore.profile.search', 1),
          )); */

        $this->addElement('Radio', 'sitestore_profile_fields', array(
            'label' => 'Profile Information Fields',
            'description' => 'Do you want to display Profile Information Fields associated with the selected category while creation of stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.profile.fields', 1),
        ));

        //VALUE FOR ENABLE /DISABLE PRICE FIELD
        $this->addElement('Radio', 'sitestore_price_field', array(
            'label' => 'Price Field',
            'description' => 'Do you want the Price field to be enabled for stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.price.field', 0),
        ));

        //VALUE FOR ENABLE /DISABLE LOCATION FIELD
        $this->addElement('Radio', 'sitestore_locationfield', array(
            'label' => 'Location Field',
            'description' => 'Do you want the Location field to be enabled for stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showlocationOption(this.value)',
            'value' => $coreSettings->getSetting('sitestore.locationfield', 1),
        ));


        $this->addElement('Radio', 'sitestore_multiple_location', array(
            'label' => 'Allow Multiple Locations',
            'description' => 'Do you want to allow store admins to enter multiple locations for their Stores? (If you select ‘Yes’, then users will be able to add multiple locations for their Stores from their Store Dashboards.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.multiple.location', 1),
        ));

        //VALUE FOR ENABLE /DISABLE MAP
        $this->addElement('Radio', 'sitestore_location', array(
            'label' => 'Maps Integration',
            'description' => ' Do you want Maps Integration to be enabled for stores? (With this enabled, stores having location information could also be seen on Map. The "Stores Home" and "Browse Stores" also enable you to see the items plotted on Map.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showMapOptions(this.value)',
            'value' => $coreSettings->getSetting('sitestore.location', 1),
        ));

        //VALUE FOR ENABLE /DISABLE Bouncing Animation
        $this->addElement('Radio', 'sitestore_map_sponsored', array(
            'label' => 'Sponsored Items with a Bouncing Animation',
            'description' => 'Do you want the sponsored stores to be shown with a bouncing animation in the Map?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.map.sponsored', 1),
        ));

        $this->addElement('Text', 'sitestore_map_city', array(
            'label' => 'Centre Location for Map at Stores Home and Browse Stores',
            'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Stores Home and Browse Stores when Map View is chosen to view store.(To show the whole world on the map, enter the word "World" below.)',
            'required' => true,
            'value' => $coreSettings->getSetting('sitestore.map.city', "World"),
        ));

        $this->addElement('Select', 'sitestore_map_zoom', array(
            'label' => "Default Zoom Level for Map at Stores Home and Browse Stores",
            'description' => 'Select the default zoom level for the map which is shown on Stores Home and Browse Stores when Map View is chosen to view store. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
            'multiOptions' => array(
                '1' => "1",
                "2" => "2",
                "4" => "4",
                "6" => "6",
                "8" => "8",
                "10" => "10",
                "12" => "12",
                "14" => "14",
                "16" => "16"
            ),
            'value' => $coreSettings->getSetting('sitestore.map.zoom', 1),
            'disableTranslator' => 'true'
        ));

        //VALUE FOR ENABLE /DISABLE Proximity Search
        $this->addElement('Radio', 'sitestore_proximitysearch', array(
            'label' => 'Proximity Search',
            'description' => 'Do you want proximity search to be enabled for stores? (Proximity search will enable users to search for stores within a certain distance from a location.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showlocationKM(this.value)',
            'value' => $coreSettings->getSetting('sitestore.proximitysearch', 1),
        ));

        //VALUE FOR ENABLE /DISABLE Proximity Search IN Kilometer
        $this->addElement('Radio', 'sitestore_proximity_search_kilometer', array(
            'label' => 'Proximity Search Metric',
            'description' => 'What metric do you want to be used for proximity search?',
            'multiOptions' => array(
                0 => 'Miles',
                1 => 'Kilometers'
            ),
            'value' => $coreSettings->getSetting('sitestore.proximity.search.kilometer', 0),
        ));

        $this->addElement('Radio', 'sitestore_multiple_location', array(
            'label' => 'Allow Multiple Locations',
            'description' => 'Do you want to allow store admins to enter multiple locations for their Stores? (If you select ‘Yes’, then users will be able to add multiple locations for their Stores from their Store Dashboards.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.multiple.location', 1),
        ));

        //VALUE FOR COMMENT
        $this->addElement('Radio', 'sitestore_checkcomment_widgets', array(
            'label' => 'Comments',
            'description' => 'Do you want comments to be enabled for stores? (If enabled users will able to comment on store profile page under info tab.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.checkcomment.widgets', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitestore_sponsored_image', array(
            'label' => 'Sponsored Label',
            'description' => 'Do you want to show "SPONSORED" label on the main profile of sponsored stores above the profile picture?',
            'onclick' => 'showsponsored(this.value)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.sponsored.image', 1),
        ));

        //COLOR VALUE FOR SPONSORED
        $this->addElement('Text', 'sitestore_sponsored_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitestore_feature_image', array(
            'label' => 'Featured Label',
            'description' => 'Do you want to show "FEATURED" label on the main profile of featured stores below the profile picture?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showfeatured(this.value)',
            'value' => $coreSettings->getSetting('sitestore.feature.image', 1),
        ));

        //COLOR VALUE FOR FEATURED
        $this->addElement('Text', 'sitestore_featured_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowFeatured.tpl',
                        'class' => 'form element'
                    )))
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitestore_fs_markers', array(
            'label' => 'Featured & Sponsored Markers',
            'description' => 'On Stores Home and Browse Stores how do you want a Store to be indicated as featured and sponsored ?',
            'multiOptions' => array(
                1 => 'Using Labels (See FAQ for customizing the labels)',
                0 => 'Using Icons (See FAQ for customizing the icons)',
            ),
            'value' => $coreSettings->getSetting('sitestore.fs.markers', 1),
        ));

        $this->addElement('Radio', 'sitestore_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show stores according to viewer's network if he has selected any? (If set to no, all the stores will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('sitestore.network', 0),
        ));

        //VALUE FOR Store Dispute Link.
        $this->addElement('Radio', 'sitestore_default_show', array(
            'label' => 'Set Only My Networks as Default in search',
            'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the stores browse and home pages, and enables users to search and filter stores.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('sitestore.default.show', 0),
        ));

        $this->addElement('Radio', 'sitestore_networks_type', array(
            'label' => 'Network selection for Stores',
            'description' => "You have chosen that viewers should only see Stores of their network(s). How should a Store's network(s) be decided?",
            'multiOptions' => array(
                0 => "Store Owner's network(s) [If selected, only members belonging to store owner's network(s) will see the Stores.]",
                1 => "Selected Networks [If selected, store admins will be able to choose the networks of which members will be able to see their Store.]"
            ),
            'value' => $coreSettings->getSetting('sitestore.networks.type', 0),
        ));

        //Order of browse store
        $this->addElement('Radio', 'sitestore_browseorder', array(
            'label' => 'Default ordering on Browse Stores',
            'description' => 'Select the default ordering of stores on the browse stores.',
            'multiOptions' => array(
                1 => 'All stores in descending order of creation.',
                2 => 'All stores in descending order of views.',
                3 => 'All stores in alphabetical order.',
                4 => 'Sponsored stores followed by others in descending order of creation.',
                5 => 'Featured stores followed by others in descending order of creation.',
                6 => 'Sponsored & Featured stores followed by Sponsored stores followed by Featured stores followed by others in descending order of creation.',
                7 => 'Featured & Sponsored stores followed by Featured stores followed by Sponsored stores followed by others in descending order of creation.',
            ),
            'value' => $coreSettings->getSetting('sitestore.browseorder', 1),
        ));

        $this->addElement('Radio', 'sitestore_addfavourite_show', array(
            'label' => 'Linking Stores',
            'description' => 'Do you want members to be able to Link their Stores to other Stores? (Linking is useful to show related Stores. For example, a Chef\'s Store can be linked to the Restaurant\'s Store where he works, or a Store\'s Store can be linked to the Stores of the Brands that it sells. If enabled, a "Link to your Store" link will appear on Stores.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.addfavourite.show', 1),
        ));

        $this->addElement('Radio', 'sitestore_layoutcreate', array(
            'label' => 'Edit Store Layout',
            'description' => 'Do you want to enable store admins to alter the block positions / add new available blocks on the stores profile? (If enabled, then store admins will also be able to add HTML blocks on their stores profile.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.layoutcreate', 0),
        ));

        $this->addElement('Radio', 'sitestore_category_edit', array(
            'label' => 'Edit Store Category',
            'description' => 'Do you want to allow store admins to edit category of their stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showcategoryblock(this.value);',
            'value' => $coreSettings->getSetting('sitestore.category.edit', 0),
        ));

        //$description = Zend_Registry::get('Zend_Translate')->_('Do you want to show categories, subcategories and 3%s level categories with slug in the url.');
        //$description = sprintf($description, "<sup>rd</sup>");
        $this->addElement('Radio', 'sitestore_categorywithslug', array(
            'label' => 'Slug URL',
            'description' => 'Do you want to replace blank-space in your category name by "-" in URL?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.categorywithslug', 1),
        ));

        //$this->sitestore_categorywithslug->getDecorator('Description')->setOptions(array('placement'=> 'PREPEND', 'escape' => false));

        $this->addElement('Radio', 'sitestore_claimlink', array(
            'label' => 'Claim a Store Listing',
            'description' => 'Do you want users to be able to file claims for stores ? (Claims filed by users can be managed from the Manage Claims section.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showclaim(this.value)',
            'value' => $coreSettings->getSetting('sitestore.claimlink', 1),
        ));

        $this->addElement('Radio', 'sitestore_claim_show_menu', array(
            'label' => 'Claim a Store link',
            'description' => 'Select the position for the "Claim a Store" link.',
            'multiOptions' => array(
                2 => 'Show this link on Stores Navigation Menu.',
                1 => 'Show this link on Footer Menu.',
                0 => 'Do not show this link.'
            ),
            'value' => $coreSettings->getSetting('sitestore.claim.show.menu', 2),
        ));

        $this->addElement('Radio', 'sitestore_claim_email', array(
            'label' => 'Notification for Store Claim',
            'description' => 'Do you want to receive e-mail notification when a member claims a store?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.claim.email', 1),
        ));

        $this->addElement('Radio', 'sitestore_virtual_product_shipping', array(
            'label' => 'Shipping Address for Virtual Products',
            'description' => "Do you want to show shipping address for virtual products during checkout process?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.virtual.product.shipping', 1),
        ));

        $this->addElement('Radio', 'sitestore_automatically_like', array(
            'label' => 'Automatic Like',
            'description' => "Do you want members to automatically Like a store they create?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.automatically.like', 1),
        ));

        $this->addElement('Radio', 'sitestore_hide_left_container', array(
            'label' => 'Hide Left / Right Column',
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want the left / right column on the Store Profile page to be hidden when users click on the tabs other than Updates, Info and Overview which are placed in the Tab Container of that page when you have "Advertisements / Community Ads" enbaled to be shown on the Store Profile from "%1$sAd Settings%2$s" section.'), "<a href='" . $view->url(array('module' => 'sitestore', 'controller' => 'settings', 'action' => 'adsettings'), 'admin_default', true) . "' target='_blank'>", '</a>'),
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.hide.left.container', 0),
        ));
        $this->sitestore_hide_left_container->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Radio', 'sitestore_show_tabs_without_content', array(
            'label' => 'Show Tabs without Content',
            'description' => 'Do you want to show the different content based tabs on Store Profile page (like Albums, Videos etc) to the users who do not have permission to add content to those tabs even when there is no content in that tab.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.show.tabs.without.content', 0),
        ));

        $this->addElement('Radio', 'sitestore_slding_effect', array(
            'label' => 'Enable Sliding Effect on Tabs',
            'description' => 'Do you want to enable sliding effect when tabs on Store Profile are clicked?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.slding.effect', 1),
        ));

        $this->addElement('Radio', 'sitestore_mylike_show', array(
            'label' => 'Stores I Like Link',
            'description' => 'Do you want to show the "Stores I Like" link to users? This link appears on "My Store Account" and enables users to see the list of Stores that they have Liked.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.mylike.show', 1),
        ));

        $this->addElement('Text', 'sitestore_store', array(
            'label' => 'Stores Per Page',
            'description' => 'How many stores will be shown per page in "Browse Stores" page?',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettings->getSetting('sitestore.store', 24),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Text', 'sitestoreproduct_navigationtabs', array(
            'label' => 'Tabs in Stores navigation bar',
            'allowEmpty' => false,
            'maxlength' => '2',
            'required' => true,
            'description' => 'How many tabs do you want to show on Stores main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu and their sequence, please visit: "Layout" > "Menu Editor")',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.navigationtabs', 7),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Text', 'sitestore_showmore', array(
            'label' => 'Tabs / Links',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'How many tabs / links do you want to show on stores profile by default? (Note that if there are more tabs / links than the limit entered by you then a "More" tab / link will appear, clicking on which will show the remaining hidden tabs / links. Tabs are available in the tabbed layout, and links in the non-tabbed layout. To choose the layout for Stores on your site, visit the "Store Layout" section.)',
            'value' => $coreSettings->getSetting('sitestore.showmore', 9),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

//    $this->addElement('Text', 'sitestoreshow_navigation_tabs', array(
//        'label' => 'Tabs in Stores navigation bar',
//        'allowEmpty' => false,
//        'maxlength' => '3',
//        'required' => true,
//        'description' => 'How many tabs do you want to show on Stores main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
//        'value' => $coreSettings->getSetting('sitestoreshow.navigation.tabs', 8),
//        'validators' => array(
//            array('Int', true),
//            array('GreaterThan', true, array(0)),
//        ),
//    ));

        $this->addElement('Radio', 'sitestore_postedby', array(
            'label' => 'Posted By',
            'description' => "Do you want to enable Posted by option for the Stores on your site? (Selecting Yes here will display the member's name who has created the store.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.postedby', 0),
        ));

        $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
        $adddescription = '';
        if (!$advfeedmodule)
            $adddescription = "and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>over here</a>";
        $this->addElement('Radio', 'sitestore_postfbstore', array(
            'label' => 'Allow Facebook Page Linking',
            'description' => "Do you want to allow users to link their Facebook Pages with their Stores on your website? If you select 'Yes' over here, then users will see a new block in the 'Marketing' section of their Store Dashboard which will enable them to enter the URL of their Facebook Page. With this, the updates made by users on their Store on your site will also be published on their Facebook Page. Also, the Facebook Like Box for the Facebook Page will be displayed on Store Profile. The Facebook Like Box will:<br /><br /><ul style='margin-left: 20px;'><li>Show the recent posts from the Facebook Page.</li><li>Show how many people already like the Facebook Page.</li><li>Enable visitors to Like the Facebook Page from your site.</li></ul><br /><br />If you do not want to show the Facebook Like Box on Stores with linked Facebook Pages, then simply remove the widget from the 'Layout Editor'. With linked Facebook Page, if Store Admins select 'Publish this on Facebook' option while posting 
their updates, then these updates will be published on their Facebook Profile as well as Facebook Page. (Note: Publishing updates on Facebook Pages via this linking is dependent on the <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'> Advanced Activity Feeds / Wall Plugin</a> " . $adddescription . ".)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.postfbstore', 1),
        ));
        $this->sitestore_postfbstore->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

        $publish_fb_places = array('0' => 1, '1' => 2);
        $publish_fb_places = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.publish.facebook', serialize($publish_fb_places));
        if (is_string($publish_fb_places) && !empty($publish_fb_places)) {
            $publish_fb_places = unserialize($publish_fb_places);
        }
        $this->addElement('MultiCheckbox', 'sitestore_publish_facebook', array(
            'label' => 'Publishing Updates on Facebook',
            'description' => "Choose the places on Facebook where users will be able to publish their updates that they post on Stores of your site.",
            'multiOptions' => array(
                '1' => 'Publish this post on Facebook Page linked with this Store. [Note: This setting will only work if you choose \'Yes\' option for the setting "Allow Facebook Page Linking".]',
                '2' => 'Publish this post on my Facebook Timeline',
            ),
            'value' => $publish_fb_places
        ));

        $this->addElement('Radio', 'sitestore_tinymceditor', array(
            'label' => 'Tinymce Editor',
            'description' => 'Allow TinyMCE editor for discussion message of Stores.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.tinymceditor', 1),
        ));

        $this->addElement('Radio', 'is_section_allowed', array(
            'label' => 'Allow Store Sections',
            'description' => 'Do you want to allow Store Admins to created Sections in their Stores?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('is.section.allowed', 1),
        ));

//     $this->addElement('Radio', 'sitestore_allow_printingtag', array(
//        'label' => 'Allow QR Code Printing Tag',
//        'description' => 'Do you want to allow Store Admins to print QR code printing tag?',
//        'multiOptions' => array(
//            1 => 'Yes',
//            0 => 'No'
//        ),
//        'value' => $coreSettings->getSetting('sitestore.allow.printingtag', 0),
//    ));

        $this->addElement('Radio', 'sitestore_shipping_extra_content', array(
            'label' => 'Display Filters in Shipping Methods Creation',
            'description' => 'Do you want to allow Store Admins to view the filters on shipping method creation page?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.shipping.extra.content', 1),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $field = 'sitestore_code_share';
        $this->addElement('Dummy', "$field", array(
            'label' => 'Social Share Widget Code',
            'description' => "<a class='smoothbox' href='" . $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) . "'>Click here</a> to add your social share code.",
            'ignore' => true,
        ));
        $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

        $this->addElement('Radio', 'sitestore_terms_conditions', array(
            'label' => 'Terms & Conditions',
            'description' => "Do you want to allow Terms & Conditions? [If selected 'Yes', store admins will be be able to write 'Terms & Conditions' for their store from the ‘Terms & Conditions’ tab available on the Stores Dashboard.]" . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/admin/terms_conditions.png" target="_blank" title="View Screenshot" class="buttonlink sitestore_icon_view mleft5"></a>',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.terms.conditions', 0),
        ));
        $this->sitestore_terms_conditions->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Radio', 'sitestore_fixed_text', array(
            'label' => 'Fixed Text on Checkout Process',
            'description' => "Do you want to show fixed text on order review step of checkout process?" . '<a href="' . $view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/admin/fixed_text.png" target="_blank" title="View Screenshot" class="buttonlink sitestore_icon_view mleft5"></a>',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitestore.fixed.text', 0),
            'onchange' => 'showCheckoutFixedText(this.value)',
        ));
        $this->sitestore_fixed_text->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


        $this->addElement('Textarea', 'sitestore_checkout_fixed_text_value', array(
            'label' => 'Fixed Text',
            'description' => "Enter the text which you want to display.",
            'value' => $coreSettings->getSetting('sitestore.checkout.fixed.text.value', ''),
//        'allowEmpty' => false,
//        'required' => true,
        ));

        $this->addElement('Textarea', 'sitestore_defaultpagecreate_email', array(
            'label' => 'Email Alerts for New Stores',
            'description' => 'Please enter comma-separated list, or one-email-per-line for email IDs which should be notified when a new store is created in your marketplace.',
            'value' => $coreSettings->getSetting('sitestore.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
        ));

        $this->addElement('Text', 'sitestore_title_truncation', array(
            'label' => 'Title Truncation Limit',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'What maximum limit should be applied to the number of characters in the title of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
            'value' => $coreSettings->getSetting('sitestore.title.truncation', 18),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Radio', 'sitestore_minimum_shipping_cost', array(
            'label' => 'Minimum Shipping Cost',
            'description' => 'Do you want to allow the store owners to enter Minimum Shipping Cost for their shops?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
