<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Package_Create extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Create New Store Package')
            ->setDescription('Create a new store package over here. Below, you can configure various settings for this package like tell a friend, overview, map, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

    // Element: title
    $this->addElement('Text', 'title', array(
        'label' => 'Package Name',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
    ));

    // Element: description
    $this->addElement('Textarea', 'description', array(
        'label' => 'Package Description',
        'validators' => array(
            array('StringLength', true, array(0, 250)),
        )
    ));

    // Element: level_id
    $multiOptions = $saleToAccessLevels = array('0' => 'All Levels');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->type == 'public') {
        $saleToAccessLevels[$level->getIdentity()] = $level->getTitle();
        continue;
      }
      $multiOptions[$level->getIdentity()] = $level->getTitle();
      $saleToAccessLevels[$level->getIdentity()] = $level->getTitle();
    }
    $this->addElement('Multiselect', 'level_id', array(
        'label' => 'Member Levels',
        'description' => 'Select the Member Levels to which this Package should be available. Only users belonging to the selected Member Levels will be able to create store of this package.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $multiOptions,
        'value' => array('0')
    ));
    
    $storeAsService = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.store.as.service', 0);
    if( empty($storeAsService) ) {
      $checkedProductTypes = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
    } else {
      $checkedProductTypes = array('virtual');
    }
    $this->addElement('MultiCheckbox', 'product_type', array(
        'label' => 'Product Types',
        'description' => 'Select the product types to be available in this package. (Users will be able to select the below chosen types in the first step while creating a new product in this package.)',
        'RegisterInArrayValidator' => false,
        'multiOptions' => array(
            'simple' => 'Simple Products',
            'grouped' => 'Grouped Products',
            'configurable' => 'Configurable Products',
            'virtual' => 'Virtual Products',
            'bundled' => 'Bundled Products',
            'downloadable' => 'Downloadable Products'
        ),
        'onclick' => 'isDownloadable()',
        'value' => $checkedProductTypes
    ));
    
    

    $this->addElement('Text', 'sitestoreproduct_main_files', array(
        'label' => 'Maximum Files to be Uploaded',
        'description' => 'Enter the maximum number of files to be uploaded from the stores of this package (Enter 0 for unlimited files).',        
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 0, 'max' => 9999, 'inclusive' => true)),
            ),
        'value' => 5,
    ));

    $this->addElement('Text', 'sitestoreproduct_sample_files', array(
        'label' => 'Maximum Sample Files to Upload',
        'description' => 'Enter the maximum number of sample files to be uploaded from the stores of this package (Enter 0 for unlimited files).',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 0, 'max' => 9999, 'inclusive' => true)),
            ),
        'value' => 5,
    ));


    $filesize = (int) ini_get('upload_max_filesize') * 1024;
    $description = Zend_Registry::get('Zend_Translate')->_('Enter the maximum file size in KB allowed for the stores of this package. Valid values are from 1 to %s KB.');
    $description = sprintf($description, $filesize);
    $this->addElement('Text', 'filesize_main', array(
        'label' => 'Maximum File Size',
        'description' => $description,
        'allowEmpty' => false,
        'validators' => array(
            array('Between', true, array('min' => 1, 'max' => $filesize, 'inclusive' => true)),
        ),
        'value' => $filesize
    ));

    $description = Zend_Registry::get('Zend_Translate')->_('Enter the maximum file size of sample files in KB allowed for the stores of this package. Valid values are from 1 to %s KB.');
    $description = sprintf($description, $filesize);
    $this->addElement('Text', 'filesize_sample', array(
        'label' => 'Maximum File Size of Sample Files',
        'description' => $description,
        'allowEmpty' => false,
        'validators' => array(
            array('Between', true, array('min' => 1, 'max' => $filesize, 'inclusive' => true)),
        ),
        'value' => $filesize
    ));
    

    $this->addElement('Text', 'max_product', array(
        'label' => 'Maximum Number of Products',
        'description' => 'Enter the maximum number of products that stores of this package can create (Enter 0 for unlimited products).',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(-1)),
        ),
        'value' => 25
    ));

    // Element : allow_selling_products
    $this->addElement('Radio', 'allow_selling_products', array(
        'label' => 'Selling Products',
        'description' => 'Do you want to allow Store owners of Stores of this package to sell their products?',
        'multiOptions' => array(
            '1' => 'Yes, allow store owners of Stores of this package to sell their products.',
            '0' => 'No, do not allow store owners of Stores of this package to sell their products but only to display them.',
        ),
        'value' => 1,
        'onchange' => 'showSellingOptions();'
    ));
    
    $this->addElement('Multiselect', 'sale_to_access_levels', array(
        'label' => 'Sale to Access Levels',
        'description' => 'Select the Member Levels of users to which store owners of Stores of this Store Package should be only allowed to sell their Products.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $saleToAccessLevels,
        'value' => array('0')
    ));
    
    // Element : allow_non_selling_product_price
    $this->addElement('Radio', 'allow_non_selling_product_price', array(
        'label' => 'Non Selling Product Price',
        'description' => 'Do you want to allow Store owners of Stores of this package to show price on product profile, preview and widgets?',
        'multiOptions' => array(
            '1' => 'Yes, allow store owners of Stores of this package to show price on product profile, preview and widgets.',
            '0' => 'No, do not allow store owners of Stores of this package to show price on product profile, preview and widgets.',
        ),
        'value' => 1,
    ));

    $this->addElement('Select', 'comission_handling', array(
        'label' => 'Commission Type',
        'description' => 'Select the type of commission. This commission will be applied on all the orders placed for products from the stores of this package.',
        'multiOptions' => array(
            1 => 'Percent',
            0 => 'Fixed'
        ),
        'value' => 1,
        'onchange' => 'showComissionType();'
    ));

    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Text', 'comission_fee', array(
        'label' => 'Commission Value (' . $currencyName . ')',
        'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)',
        'allowEmpty' => false,
        'value' => 1,
    ));

    $this->addElement('Text', 'comission_rate', array(
        'label' => 'Commission Value (%)',
        'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)',
        // 'allowEmpty' => false,
         'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 0, 'max' => 100, 'inclusive' => true)),
            ),
        'value' => 1,
    ));
    

    // Element: online_payment_threshold
    $this->addElement('Text', 'online_payment_threshold', array(
        'label' => "Maximum Price Threshold for Online Payment ($currencyName)",
        'description' => 'Enter the maximum subtotal threshold for online payment. If subtotal amount (product price X quantity) ordered from this store becomes more than threshold amount then online payment gateways will not show for the order. Purchasers will still be able to use other payment modes (cheque, cash) if you have allowed any of them.[ Enter 0 for unlimited threshold. ]',
        'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
        'value' => 0,
    ));
    
    // Element: transfer_threshold
    $this->addElement('Text', 'transfer_threshold', array(
        'label' => "Payment Threshold Amount ($currencyName)",
        'description' => 'Enter the payment threshold amount. Store owners of stores of this package will be able to request you for their payments when the total amount of their Store’s sales becomes more than this threshold amount.',
        'allowEmpty' => false,
        'required' => true,
        'value' => 100,
    ));



    // Element: price
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
    $this->addElement('Text', 'price', array(
        'label' => 'Price',
        'description' => 'The amount to charge from the store owner. Setting this to zero will make this a free package.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Float', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0.00',
    ));

    // Element: recurrence @ todo

    $this->addElement('Duration', 'recurrence', array(
        'label' => 'Billing Cycle',
        'description' => 'How often should Stores of this package be billed? (You can choose the payment for this package to be one-time or recurring.)',
        'required' => true,
        'allowEmpty' => false,
        //'validators' => array(
        //array('Int', true),
        //array('GreaterThan', true, array(0)),
        //),
        'value' => array(1, 'month'),
    ));
    //unset($this->getElement('recurrence')->options['day']);
    //$this->getElement('recurrence')->options['forever'] = 'One-time';
    // Element: duration
    $this->addElement('Duration', 'duration', array(
        'label' => 'Billing Duration',
        'description' => 'When should this package expire? For one-time packages, the package will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycle for the period of time specified here.',
        'required' => true,
        'allowEmpty' => false,
        //'validators' => array(
        //  array('Int', true),
        //  array('GreaterThan', true, array(0)),
        //),
        'value' => array('0', 'forever'),
    ));

    // renew
    $this->addElement('Checkbox', 'renew', array(
        'description' => 'Renew Link',
        'label' => 'Store creators will be able to renew their stores of this package before expiry. (Note: Renewal link after expiry will only be shown for stores of paid packages, i.e., packages having a non-zero value of Price above.)',
        'value' => 0,
        'onclick' => 'javascript:setRenewBefore();',
    ));
    $this->addElement('Text', 'renew_before', array(
        'label' => 'Renewal Frame before Store Expiry',
        'description' => 'Show store renewal link these many days before expiry.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Int', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0',
    ));

    // auto aprove
    $this->addElement('Checkbox', 'approved', array(
        'description' => "Auto-Approve",
        'label' => 'Auto-Approve stores of this package. These stores will not need admin moderation approval before going live.',
        'value' => 0,
    ));

    // Element:sponsored
    $this->addElement('Checkbox', 'sponsored', array(
        'description' => "Sponsored",
        'label' => 'Make stores of this package as Sponsored. Note: A change in this setting later on will only apply on new stores that are created in this package.',
        'value' => 0,
    ));

    // Element:featured
    $this->addElement('Checkbox', 'featured', array(
        'description' => "Featured",
        'label' => 'Make stores of this package as Featured. Note: A change in this setting later on will only apply on new stores that are created in this package.',
        'value' => 0,
    ));

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1)) {
        $this->addElement('Checkbox', 'ads', array(
            'description' => "Community Ads Display",
            'label' => "Display Community Ads in stores of this package at various positions as configured in Ad Settings. (With this setting, you can choose to grant privilege of being ad-free to stores of special / paid packages.)",
            'value' => 1,
        ));
      } else {
        $this->addElement('dummy', 'ads', array(
            'label' => "Community Ads Display",
            'description' => 'You cannot configure this setting for packages because you have disabled Community Ads display for stores using "Community Ads in this plugin" field in Ad Settings.',
        ));
      }
    } else {
      $desc_modules = Zend_Registry::get('Zend_Translate')->_('To be able to configure this setting for stores on your website, please install and enable the <a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">"Advertisements / Community Ads Plugin"</a> which enables rich, interactive and effective advertising on your website. With this setting, you can choose to grant privilege of being ad-free to stores of special / paid packages, while showing community ads on stores of other packages.');
      $this->addElement('dummy', 'ads', array(
          'label' => "Community Ads Display",
          'description' => $desc_modules,
      ));

      $this->ads
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    // Element:tellafriend
    $this->addElement('Checkbox', 'tellafriend', array(
        'description' => "Tell a friend",
        'label' => 'Display "Tell a friend" link on profile of stores of this package. (Using this, users will be able to email a store to their friends.)',
        'value' => 0,
    ));

    // Element:print
    $this->addElement('Checkbox', 'print', array(
        'description' => 'Print',
        'label' => 'Display "Print" link on profile of stores of this package. (Using this, users will be able to print the information of stores.)',
        'value' => 0,
    ));

    // Element: overview
    $this->addElement('Checkbox', 'overview', array(
        'description' => "Overview",
        'label' => 'Enable Overview for stores of this package. (Using this, users will be able to create rich profiles for their stores using WYSIWYG editor.)',
        'value' => 0,
    ));

    // Element:map
    $this->addElement('Checkbox', 'map', array(
        'description' => "Location Map",
        'label' => 'Enable Location Map for stores of this package. (Using this, users will be able to specify detailed location for their stores and the corresponding Map will be shown on the Store Profile.)',
        'value' => 0,
    ));

    // Element:insights
//    $this->addElement('Checkbox', 'insights', array(
//        'description' => "Insights",
//        'label' => 'Show Insights for stores of this package to their admins. (Insights for stores show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. Using this, store admins will also be getting periodic, auto-generated emails containing Store insights.)',
//        'value' => 0,
//    ));

    // Element:contact_details
    $this->addElement('Checkbox', 'contact_details', array(
        'description' => "Contact Details",
        'label' => 'Enable Contact Details for stores of this package. (Using this, store admins will be able to mention contact details for their stores\' entity. These contact details will also be displayed on store profile and browse stores.)',
        'value' => 0,
    ));

//    // Element:foursquare
//    $this->addElement('Checkbox', 'foursquare', array(
//        'description' => "Save To Foursquare Button",
//        'label' => "Enable 'Save to foursquare' buttons for stores of this package. (Using this, 'Save to foursquare' buttons will be shown on profiles of stores having location information. These buttons will enable store visitors to add the store's place or tip to their foursquare To-Do List. Store Admins will get the option for enabling this in the \"Marketing\" section of their Dashboard.)",
//        'value' => 0,
//    ));

    // Element:Twitter
    $sitestoretwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter');
    if ($sitestoretwitterEnabled) {
      $this->addElement('Checkbox', 'twitter', array(
          'description' => "Display Twitter Updates",
          'label' => "Enable displaying of Twitter Updates for stores of this package. (Using this, store admins will be able to display their Twitter Updates on their Store profile. Store Admins will get the option for entering their Twitter username in the \"Marketing\" section of their Dashboard. From the Layout Editor, you can choose to place the Twitter Updates widget either in the Tabs container or in the sidebar on Store Profile.)",
          'value' => 0,
      ));
    }

    // Element:sendupdate
    $this->addElement('Checkbox', 'sendupdate', array(
        'description' => "Send an Update",
        'label' => "Enable 'Send an Update' for stores of this package. (Using this, store admins will be able send updates for their stores' entity to users who Like their store. Store Admins will get this option in the \"Marketing\" section of their Dashboard.",
        'value' => 0,
    ));

    // Element:modules
    $includeModules = Engine_Api::_()->sitestore()->getEnableSubModules('adminPackages');
    if (!empty($includeModules)) {
        $getActionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        if(($getActionName == 'edit') && array_key_exists("sitestoreproduct", $includeModules) ) {
          $includeModules["sitestoreproduct"] = $includeModules["sitestoreproduct"] . '&nbsp;&nbsp;<a href="' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestoreproduct', 'controller' => 'store', 'action' => 'package-level'), 'admin_default', false) . '" target="_blank">store configurations</a>';
        }
      
      $desc_modules = sprintf(Zend_Registry::get('Zend_Translate')->_("Select the apps that should be available to stores of this package."));
      $this->addElement('MultiCheckbox', 'modules', array(
          'description' => $desc_modules,
          'label' => 'Apps',
          'multiOptions' => $includeModules,
          'RegisterInArrayValidator' => false,
          'escape' => false,
          'value' => 0,
      ));
      $this->modules
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }
    
    
    // Element : profile
    $this->addElement('Radio', 'profile', array(
        'label' => 'Profile Information',
        'description' => 'Allow Profile Information for stores of this package. Below, you can also choose whether all, or restricted, profile information should be available to stores of this package. (Using this, store admins will be able to add additional information about their store depending on its category. This non-generic additional information will help others know more specific details about the store. You can create new Profile Types & fields for stores on your site, and associate them with the Categories of stores from the "Profile Fields" and "Category-Store Profile Mapping" sections. Such a mapping will enable you to configure profile fields for storees depending on the category they belong to.)',
        'multiOptions' => array(
            '1' => 'Yes, allow profile information with ALL available fields.',
            '0' => 'No, do not allow profile information for stores of this package.',
            '2' => 'Yes, allow profile information with RESTRICTED fields. (Below, you can choose the profile fields that should be available. With this configuration, you can give access to more profile fields to packages of higher cost.)',
        ),
        'value' => 1,
        'onclick' => 'showprofileOption(this.value)',
    ));

    //Add Dummy element for using the tables
    $this->addElement('Dummy', 'profilefield', array(
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_profilefield.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Checkbox', 'update_list', array(
        'description' => 'Show in "Other available Packages" List',
        'label' => "Show this package in the list of 'Other available Packages' which gets displayed to the users for upgrading the package of a Store at Store dashboard. (This will be useful in case you are creating a free package or a test package and you want it to be used by the users only once for a limited period of time and do not want to show it during package upgrdation.)",
        'value' => 1,
    ));
    // Element: enabled
    $this->addElement('hidden', 'enabled', array(
        'value' => 1,
    ));


    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Create Package',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'ignore' => true,
        'link' => true,
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
        'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        )
    ));
  }

}

?>