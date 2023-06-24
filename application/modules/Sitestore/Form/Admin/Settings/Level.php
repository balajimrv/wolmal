<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Member Level Settings')
            ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below. (Note: If packages are enabled from global settings, then some member level settings will not be available as those feature settings for stores will now depend on packages.)");

    $isEnabledPackage = Engine_Api::_()->sitestore()->hasPackageEnable();

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Stores?',
        'description' => 'Do you want to let members view stores? If set to no, some other settings on this store may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all stores, even private ones.',
            1 => 'Yes, allow viewing of stores.',
            0 => 'No, do not allow stores to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {
      // Element: create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Stores?',
          'description' => 'Do you want to let members create stores? If set to no, some other settings on this store may not apply. This is useful if you want members to be able to view stores, but only certain levels to be able to create stores.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of stores.',
              0 => 'No, do not allow stores to be created.'
          ),
          'value' => 1,
      ));

      
      // Element: edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Stores?',
          'description' => 'Do you want to let members edit stores? If set to no, some other settings on this store may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all stores.',
              1 => 'Yes, allow members to edit their own stores.',
              0 => 'No, do not allow members to edit their stores.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Stores?',
          'description' => 'Do you want to let members delete stores? If set to no, some other settings on this store may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all stores.',
              1 => 'Yes, allow members to delete their own stores.',
              0 => 'No, do not allow members to delete their stores.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Stores?',
          'description' => 'Do you want to let members of this level comment on stores?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all stores, including private ones.',
              1 => 'Yes, allow members to comment on stores.',
              0 => 'No, do not allow members to comment on stores.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1)) {
        $ownerTitle = "Store Admins";
      } else {
        $ownerTitle = "Just Me";
      }

      $privacyArray = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacyValueArray = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if ($sitestorememberEnabled) {
        $privacyArray['member'] = 'Store Members Only';
        $privacyValueArray[] = 'member';
      }
      $privacyArray['owner'] = $ownerTitle;
      $privacyValueArray[] = 'owner';

      //START SUBSTORE WORK.      
      
      // Element:sub create
//      $this->addElement('Radio', 'sspcreate', array(
//          'label' => 'Allow Creation of Sub Stores?',
//          'description' => 'Do you want to let members create sub stores? If set to no, some other settings on this store may not apply. This is useful if you want members to be able to create sub stores, but only certain levels to be able to create sub stores.',
//          'multiOptions' => array(
//              1 => 'Yes, allow creation of sub stores.',
//              0 => 'No, do not allow sub stores to be created.'
//          ),
//          'value' => 1,
//      ));
    }
      // Element: allow_buy
      $this->addElement('Radio', 'allow_buy', array(
          'label' => 'Allow Buying of Products',
          'description' => 'Do you want members of this member level to be able to buy products on your site?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));
      if (!$this->isPublic()) {
      // Element: allow_check
//      $this->addElement('Radio', 'allow_check', array(
//          'label' => 'Allow Payment by Cheque',
//          'description' => 'Do you want members of this member level to be able to make payment for their orders by cheque?',
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => 1,
//      ));

      if( empty($isEnabledPackage) ){

      $localeObject = Zend_Registry::get('Locale');
      $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        // Element: allow_store_create
        $this->addElement('Radio', 'allow_store_create', array(
            'label' => 'Products in Stores',
            'description' => 'Do you want Products to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Products in Stores.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'createStore()',
            'value' => 1,
        ));

        //PRODUCT TYPE FOR DIFFERENT PRODUCTS
        $productType = array(
            'simple' => 'Simple Products',
            'grouped' => 'Grouped Products',
            'configurable' => 'Configurable Products',
            'virtual' => 'Virtual Products',
            'bundled' => 'Bundled Products',
            'downloadable' => 'Downloadable Products'
        );

        $this->addElement('MultiCheckbox', 'product_type', array(
            'label' => 'Product Types',
            'description' => 'Select the product types to be available in this package. (Users will be able to select the below chosen types in the first step while creating a new product in this package.)',
            'RegisterInArrayValidator' => false,
            'multiOptions' => $productType,
            'value' => array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable'),
            'onclick' => 'isDownloadable()'
        ));

        $this->addElement('Text', 'sitestoreproduct_main_files', array(
            'label' => 'Maximum Files to be Uploaded',
            'description' => 'Enter the maximum number of files to be uploaded from the stores of this package (Enter 0 for unlimited files).',
           // 'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                array('Between', true, array('min' => 0, 'max' => 9999, 'inclusive' => true)),
            ),
            'value' => 5,
        ));

        $this->addElement('Text', 'sitestoreproduct_sample_files', array(
            'label' => 'Maximum Sample Files to Upload',
            'description' => 'Enter the maximum number of sample files to be uploaded from the stores of this package (Enter 0 for unlimited files).',
           // 'allowEmpty' => false,
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
           // 'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                array('Between', true, array('min' => 1, 'max' => $filesize, 'inclusive' => true)),
            ),
            'value' => $filesize
        ));


        $description = Zend_Registry::get('Zend_Translate')->_('Enter the maximum file size of sample files in KB allowed for the stores of this package. Valid values are from 1 to %s KB.');
        $description = sprintf($description, $filesize);
        $this->addElement('Text', 'filesize_sample', array(
            'label' => 'Maximum File Size of Sample Files',
            'description' => $description,
          //  'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                array('Between', true, array('min' => 1, 'max' => $filesize, 'inclusive' => true)),
            ),
            'value' => $filesize
        ));

        // Element: max
        $this->addElement('Text', 'max_product', array(
            'label' => 'Maximum Number of Products',
            'description' => 'Enter the maximum number of products that stores of this package can create (Enter 0 for unlimited products).',
           // 'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 25
        ));

        // Element : allow_selling_products
//        $this->addElement('Radio', 'allow_selling_products', array(
//            'label' => 'Selling Products',
//            'description' => 'Do you want to allow Store owners of Stores of this level to sell their products?',
//            'multiOptions' => array(
//                '1' => 'Yes, allow store owners of Stores of this level to sell their products.',
//                '0' => 'No, do not allow store owners of Stores of this level to sell their products but only to display them.',
//            ),
//            'value' => 1,
//            'onchange' => 'showSellingOptions();'
//        ));
        
        // Element: comission_handling
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

        // Element: comission_fee
        $this->addElement('Text', 'comission_fee', array(
            'label' => 'Commission Value (' . $currencyName . ')',
            'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)',
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 1,
        ));

        // Element: comission_rate
        $this->addElement('Text', 'comission_rate', array(
            'label' => 'Commission Value ( % )',
            'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)',
           // 'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
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
           // 'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 100,
        ));
      }
      
      $privacy_array = array(
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if ($sitestorememberEnabled) {
        $privacy_array['member'] = 'Store Members Only';
        $privacy_value_array[] = 'member';
      }
      $privacy_array['like_member'] = 'Who Liked This Store';
      $privacy_value_array[] = 'like_member';

      $privacy_array['owner'] = $ownerTitle;
      $privacy_value_array[] = 'owner';
      
      // Element: auth_substore create
//      $this->addElement('MultiCheckbox', 'auth_sspcreate', array(
//          'label' => 'Sub-Store Creation Options',
//          'description' => 'Your users can choose from any of the options checked below when they decide who can create the sub-stores in their stores. If you do not check any options, everyone will be allowed to create sub-stores.',
//          'multiOptions' => $privacy_array
//      ));
      //Element: substore

      
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Store Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their stores. These options appear on your members\' "Open a New Store" and "Edit Store" pages. If you do not check any options, everyone will be allowed to view stores.',
          'multiOptions' => $privacyArray,
          'value' => $privacyValueArray
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
          'label' => 'Store Comment Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their stores. These options appear on your members\' "Open a New Store" and "Edit Store" pages. If you do not check any options, everyone will be allowed to post comments on stores.',
          'multiOptions' => $privacyArray,
          'value' => $privacyValueArray
      ));
    }

    if (!$this->isPublic() && empty($isEnabledPackage)) {
      $privacy_array = array(
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if ($sitestorememberEnabled) {
        $privacy_array['member'] = 'Store Members Only';
        $privacy_value_array[] = 'member';
      }
      $privacy_array['like_member'] = 'Who Liked This Store';
      $privacy_value_array[] = 'like_member';

      $privacy_array['owner'] = $ownerTitle;
      $privacy_value_array[] = 'owner';


      //Element: approved
      $this->addElement('Radio', 'approved', array(
          'label' => 'Store Approval Moderation',
          'description' => 'Do you want new store to be automatically approved?',
          'multiOptions' => array(
              1 => 'Yes, automatically approve store.',
              0 => 'No, site admin approval will be required for all stores.'
          ),
          'value' => 1,
      ));

      //Element: sponsored
      $this->addElement('Radio', 'sponsored', array(
          'label' => 'Store Sponsored Moderation',
          'description' => 'Do you want new store to be automatically made sponsored?',
          'multiOptions' => array(
              1 => 'Yes, automatically make store sponsored.',
              0 => 'No, site admin will be making store sponsored.'
          ),
          'value' => 0,
      ));

      //Element: featured
      $this->addElement('Radio', 'featured', array(
          'label' => 'Store Featured Moderation',
          'description' => 'Do you want new store to be automatically made featured?',
          'multiOptions' => array(
              1 => 'Yes, automatically make store featured.',
              0 => 'No, site admin will be making store featured.'
          ),
          'value' => 0,
      ));

      $this->addElement('Radio', 'tfriend', array(
          'label' => 'Tell a friend',
          'description' => 'Do you want to show "Tell a friend" link on the Profile page of stores created by members of this level? (Using this feature, viewers will be able to email the store to their friends.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'print', array(
          'label' => 'Print',
          'description' => 'Do you want to show "Print Store" link on the Profile page of stores created by members of this level? (If set to no, viewers will not to be able to print information of the stores.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'overview', array(
          'label' => 'Overview',
          'description' => 'Do you want to enable Overview for stores created by members of this level? (If set to no, neither the overview widget will be shown on the Store Profile nor members will be able to compose or edit the overview of their stores.)',
          'multiOptions' => array(
              //2 => 'Yes, show overview of the stores, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'map', array(
          'label' => 'Location Map',
          'description' => 'Do you want to enable Location Map for stores created by members of this level? (If set to no, neither the map widget will be shown on the Store Profile nor members will be able to specify location of their stores to be shown in the map.)',
          'multiOptions' => array(
              //2 => 'Yes show map of the stores, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));


//      $this->addElement('Radio', 'insight', array(
//          'label' => 'Insights',
//          'description' => 'Do you want to allow members of this level to view insights of their stores? (Insights for stores show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. If set to no, neither insights will be shown nor the periodic, auto-generated emails containing Store insights will be send to the store admins who belong to this level.)',
//          'multiOptions' => array(
//              //2 => 'Yes, allow them to view the insights of the stores, including private ones.',
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          //'value' => ( $this->isModerator() ? 2 : 1 ),
//          'value' => 1,
//      ));


      $this->addElement('Radio', 'contact', array(
          'label' => 'Contact Details',
          'description' => 'Do you want to enable Contact Details for the stores created by members of this level? (If set to no, neither the contact details will be shown on the info and browse stores nor members will be able to mention them for their stores\' entity.)',
          'multiOptions' => array(
              //2 => 'Yes, enable contact details for the stores, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'contactoption(this.value)',
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));


      $this->addElement('MultiCheckbox', 'contact_detail', array(
          'label' => 'Specific Contact Details',
          'description' => 'Which of the following contact details you want to be specified by members of this level in the "Contact Details" section of the Store Dashboard?',
          'multiOptions' => array(
              'phone' => 'Phone',
              'website' => 'Website',
              'email' => 'Email',
          ),
          'value' => array('phone', 'website', 'email')
      ));

//      $this->addElement('Radio', 'foursquare', array(
//          'label' => 'Save To Foursquare Button',
//          'description' => "Do you want to enable 'Save to foursquare' buttons for the stores created by members of this level? (Using this, 'Save to foursquare' buttons will be shown on profiles of stores having location information. These buttons will enable store visitors to add the store's place or tip to their foursquare To-Do List. Store Admins will get this option in the \"Marketing\" section of their Dashboard.)",
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => 1,
//      ));
      // Element:Twitter
      $sitestoretwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter');
      if ($sitestoretwitterEnabled) {
        $this->addElement('Radio', 'twitter', array(
            'label' => 'Display Twitter Updates',
            'description' => "Enable displaying of Twitter Updates for stores of this package. (Using this, store admins will be able to display their Twitter Updates on their Store profile. Store Admins will get the option for entering their Twitter username in the \"Marketing\" section of their Dashboard. From the Layout Editor, you can choose to place the Twitter Updates widget either in the Tabs container or in the sidebar on Store Profile.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }

      $this->addElement('Radio', 'sendupdate', array(
          'label' => 'Send an Update',
          'description' => "Do you want to enable 'Send an Update' for the stores created by members of this level? (Using this, store admins will be able to send an update for their stores' entity. Store Admins will get this option in the \"Marketing\" section of their Dashboard.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));
      $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorelikebox');
      if ($sitestoreFormEnabled) {
        $this->addElement('Radio', 'likebox', array(
            'label' => 'External Embeddable Badge / Like Box',
            'description' => "Do you want store admins to be able to generate code for Embeddable Badges / Like Boxes for stores created by a member of this level? (If enabled, store admins of such stores will be able to generate code to embed their external store badges in other websites / blogs to promote their store from Marketing section of store dashboard. Store Admins will also have to belong to this member level to generate code.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }
      //START SITESTOREBADGES PLUGIN WORK
      $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge');
      if ($sitestoreFormEnabled) {
        $this->addElement('Radio', 'badge', array(
            'label' => 'Badge Requesting',
            'description' => 'Do you want store admins to be able to request a badge for their store created by a member of this level? (If enabled, store admins of such stores will be able to request a badge from their store dashboard. You will be able to manage badge requests and assign badges from the admin panel of Badges Extension. Store Admins will also have to belong to this member level to request a badge.)',
            'multiOptions' => array(
                //2 => 'Yes, Private ones also',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }

      //START SITESTOREDOCUMENT PLUGIN WORK
      $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
      if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore'))) || $sitestoreDocumentEnabled) {
        $this->addElement('Radio', 'sdcreate', array(
            'label' => 'Documents in Stores',
            'description' => 'Do you want Documents to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Documents in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create documents in all stores, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_sdcreate', array(
            'label' => 'Document Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the documents in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITESTOREDOCUMENT PLUGIN WORK
      //START SITESTOREEVENT PLUGIN WORK
			if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
        $this->addElement('Radio', 'secreate', array(
            'label' => 'Events in Stores',
            'description' => 'Do you want Events to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Events in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create events in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        //START SITESTOREEVENT PLUGIN WORK
        $this->addElement('MultiCheckbox', 'auth_secreate', array(
            'label' => 'Event Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the events in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle
//                 )
        ));
      }
      //END SITESTOREEVENT PLUGIN WORK
      //START SITESTOREOFFER PLUGIN WORK
      $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
      if ($sitestoreFormEnabled) {
        $this->addElement('Radio', 'form', array(
            'label' => 'Form',
            'description' => 'Do you want Forms to be available to Stores created by members of this level? (The Form on a Store will contain questions added by store admins. If set to No, neither the form widget will be shown on the Store Profile nor the store admins will be able to add questions to the Form from Store Dashboard. Store Admins will also have to belong to this member level to manage form.)',
            'multiOptions' => array(
                //2 => 'Yes, Private ones also',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }
      //END SITESTOREOFFER PLUGIN WORK
      //START SITESTOREINVITE PLUGIN WORK
      $sitestoreInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite');
      if ($sitestoreInviteEnabled) {
        $this->addElement('Radio', 'invite', array(
            'label' => 'Invite & Promote',
            'description' => 'Do you want members of this level to be able to invite their friends to the stores? (If set to no, "Invite your Friends" link will not appear on the Store Profile of their stores.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }
      //END SITESTOREINVITE PLUGIN WORK
      //START SITESTORENOTE PLUGIN WORK
      $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
      if ($sitestoreNoteEnabled) {
        $this->addElement('Radio', 'sncreate', array(
            'label' => 'Notes in Stores',
            'description' => 'Do you want Notes to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Notes in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create notes in all stores, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_sncreate', array(
            'label' => 'Note Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the notes in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITESTORENOTE PLUGIN WORK
      //START SITESTOREOFFER PLUGIN WORK
      $sitestoreOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
      if ($sitestoreOfferEnabled) {
        $this->addElement('Radio', 'offer', array(
            'label' => 'Offer',
            'description' => 'Do you want to let members of this level to show offers for their stores? (If set to no, neither the offer widget will be shown on their Store Profiles nor they will be able to create them for their stores.)',
            'multiOptions' => array(
                //2 => 'Yes, allow them to create offers in the stores, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }
      //END SITESTOREOFFER PLUGIN WORK
            
      //START DISCUSSION PRIVACY WORK
      $sitestoreDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
      if ($sitestoreDiscussionEnabled) {
        $this->addElement('Radio', 'sdicreate', array(
            'label' => 'Discussion Topics in Stores',
            'description' => 'Do you want Discussion Topics to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to post discussion topics in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow photo uploading to all stores, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_sdicreate', array(
            'label' => 'Discussion Topics Post Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can post the discussion topics in their store. If you do not check any options, everyone will be allowed to post.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END DISCUSSION PRIVACY WORK     
      
      //START PHOTO PRIVACY WORK
      $sitestoreAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
      if ($sitestoreAlbumEnabled) {
        $this->addElement('Radio', 'spcreate', array(
            'label' => 'Photos in Stores',
            'description' => 'Do you want Photos to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to upload Photos in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow photo uploading to all stores, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_spcreate', array(
            'label' => 'Photo Upload Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can upload the photos in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END PHOTO PRIVACY WORK
      //START SITESTOREPOLL PLUGIN WORK
      $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
      if ($sitestorePollEnabled) {
        $this->addElement('Radio', 'splcreate', array(
            'label' => 'Polls in Stores',
            'description' => 'Do you want Polls to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Polls in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create polls in all stores, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_splcreate', array(
            'label' => 'Poll Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the polls in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITESTOREPOLL PLUGIN WORK
      //START SITESTOREVIDEO PLUGIN WORK
      $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
      if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore'))) ||$sitestoreVideoEnabled) {
        $this->addElement('Radio', 'svcreate', array(
            'label' => 'Videos in Stores',
            'description' => 'Do you want Videos to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Videos in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create videos in all stores, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_svcreate', array(
            'label' => 'Video Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the videos in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
        //END SITESTOREVIDEO PLUGIN WORK
        // Element : profile
        $this->addElement('Radio', 'profile', array(
            'label' => 'Profile Creation',
            'description' => 'Do you want members of this level to create profiles for their stores? (Using this feature, members will be able to create a profile for their Store and fill the corresponding details which will be displayed on info stores. If set to no, "Profile Types" link will not be shown on the Store Dashboard.)',
            'multiOptions' => array(
                '1' => 'Allow profile creation with all custom Fields.',
                '2' => 'Allow profile creation with only below selected custom Fields.',
                '0' => 'Do not allow the custom profile creation.',
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
      

      //START SITESTOREMUSIC PLUGIN WORK
      $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
      if ($sitestoreMusicEnabled) {
        $this->addElement('Radio', 'smcreate', array(
            'label' => 'Music in Stores',
            'description' => 'Do you want Music to be available to Stores created by members of this level? This setting will also apply to ability of users of this level to create Music in Stores.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create notes in all stores, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_smcreate', array(
            'label' => 'Music Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the music in their store. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITESTOREMUSIC PLUGIN WORK
      
      //START SITESTOREINTREGRATION PLUGIN WORK//
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
        $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration'          )->getIntegrationItems(); 
        foreach ($mixSettingsResults as $modNameValue) {

          $Params = Engine_Api::_()->sitestoreintegration()->integrationParams($modNameValue['resource_type'], '', '', $modNameValue['item_title']);

          $title = $Params['level_setting_title'];
          	$singular = $Params['singular'];
          	$plugin_name = $Params['plugin_name'];

					$description = 'Do you want to let members of this level to add ' . $singular . ' from "'.$plugin_name .'" to Stores / Marketplace - Ecommerce? (If set to Yes, then store admins will get this option in the “Apps” section of their dashboard.)';
					
					$description = Zend_Registry::get('Zend_Translate')->_($description);

          $this->addElement('Radio', $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'], array(
              'label' => $title,
              'description' => $description,
              'multiOptions' => array(
                  1 => 'Yes',
                  0 => 'No',
              ),
              'value' => 1,
          ));
        }
      }
      //END SITESTOREINTREGRATION PLUGIN WORK//      
      
      //START SITESTOREMEMBER PLUGIN WORK
      $sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if ($sitestoreMemberEnabled) {
        $this->addElement('Radio', 'smecreate', array(
            'label' => 'Member in Stores',
            'description' => 'Do you want Member to be available to Stores join by members of this level? This setting will also apply to ability of users of this level to join Member in Stores.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => 1,
        ));
      }
      //START SITESTOREMEMBER PLUGIN WORK
    }

    if (!$this->isPublic()) {
      // Element: style
      $this->addElement('Radio', 'style', array(
          'label' => 'Allow Custom CSS Styles?',
          'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their stores by altering their CSS styles.',
          'multiOptions' => array(
              1 => 'Yes, enable custom CSS styles.',
              0 => 'No, disable custom CSS styles.',
          ),
          'value' => 1,
      ));
    }
    // Element: claim
    $this->addElement('Radio', 'claim', array(
        'label' => 'Claim Stores',
        'description' => 'Do you want members of this level to be able to claim stores? (This will also depend on other settings for claiming like in global settings, manage claims, setting while creation of store, etc.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 1,
    ));
    if (!$this->isPublic()) {
      // Element: max
      $this->addElement('Text', 'max', array(
          'label' => 'Maximum Allowed Stores',
          'description' => 'Enter the maximum number of stores that members of this level can create. This field must contain an integer; use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));
    }

  }

}

?>