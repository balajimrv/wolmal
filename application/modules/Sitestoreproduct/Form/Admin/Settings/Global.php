<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Products - Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestoreproduct')->hasDirectoryPermissions();
    $storeName = Zend_Controller_Front::getInstance()->getRequest()->getParam('store', 'sitestore');

    if ( !empty($hasLanguageDirectoryPermissions) ) {

      $this->addElement('Text', 'sitestoreproduct_titlesingular', array(
          'label' => 'Singular Product Title',
          'description' => 'Please enter Singular Title for product. This text will come in places like feeds generated, widgets etc.',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'),
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              // array('Alnum', true),
              array('StringLength', true, array(3, 32)),
//              array('Regex', true, array('/^[a-zA-Z0-9-_\s]+$/')),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          //new Engine_Filter_StringLength(array('max' => '32')),
      )));

      $this->addElement('Text', 'sitestoreproduct_titleplural', array(
          'label' => 'Plural Product Title',
          'description' => 'Please enter Plural Title for products. This text will come in places like Main Navigation Menu, Product Main Navigation Menu, widgets etc.',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'),
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              // array('Alnum', true),
              array('StringLength', true, array(3, 32)),
//              array('Regex', true, array('/^[a-zA-Z0-9-_\s]+$/')),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          //new Engine_Filter_StringLength(array('max' => '32')),
      )));

      $this->addElement('Text', 'sitestoreproduct_slugsingular', array(
          'label' => 'Products URL alternate text for "product"',
          'description' => 'Please enter the text below which you want to display in place of "product" in the URLs of this plugin.',
          'allowEmpty' => false,
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.slugsingular', 'product'),
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              // array('Alnum', true),
              array('StringLength', true, array(3, 16)),
//              array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
          ),
      ));

      $this->addElement('Text', 'sitestoreproduct_slugplural', array(
          'label' => 'Products URL alternate text for "products"',
          'description' => 'Please enter the text below which you want to display in place of "products" in the URLs of this plugin.',
          'allowEmpty' => false,
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.slugplural', 'products'),
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              // array('Alnum', true),
              array('StringLength', true, array(3, 16)),
//              array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
          ),
      ));

//    $isPageEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
//    $isBusinessEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
//    if (!empty($isPageEnabled) && !empty($isBusinessEnabled)) {
//      $this->addElement('Radio', 'sitestoreproduct_configured_with', array(
//          'label' => 'Configured Stores / Marketplace Plugin',
//          'description' => "Configured Store / Marketplace Plugin with 'Directory / Page Plugin as Store' OR 'Directory / Business Plugin' as Store.",
//          'multiOptions' => array(
//              'sitestore' => 'Use Directory / Page Plugin as Store',
//              'sitebusiness' => 'Use Directory / Business Plugin as Store'
//          ),
//          'onchange' => 'javascript:fetchPackagesFromStore(this.value);',
//          'value' => $storeName,
//      ));
//    } else {
//      $this->addElement('Hidden', 'sitestoreproduct_configured_with', array('order' => 780, 'value' => 'sitestore'));
//    }
//    $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($storeName);
//    if (!empty($isModEnabled)) {
      $getPageSQL = Engine_Api::_()->getDbtable('packages', $storeName)->getPackagesSql();
      // $getPageSQL->where('modules NOT LIKE \'%"sitestoreproduct"%\'');
      $fetchPackageArray = $getPageSQL->query()->fetchAll();

//      $getPackageArray = array();
//      $temElemValues = array();
//      foreach ($fetchPackageArray as $package) {
//        if (strstr($package['modules'], '"sitestoreproduct"')) {
//          $temElemValues[] = $package['package_id'];
//        }
//        $getPackageArray[$package['package_id']] = $package['title'];
//      }
//
//      if (!empty($getPackageArray)) {
//        $this->addElement('Multiselect', 'enable_store_in_packages', array(
//            'label' => 'Enabled Store in Packages',
//            'Description' => 'Enabled the store in the selected packages list. Which created in selected packages will be able to create products in the store. After enabled it default settings will be apply and you may configured it from <a href="' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestoreproduct', 'controller' => 'store', 'action' => 'package-level'), 'admin_default', false) . '">Package Level Settings</a> settings.',
//            'multiOptions' => $getPackageArray,
//            'value' => $temElemValues
//        ));
//        $this->enable_store_in_packages->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
//      }
      //}
//    $this->addElement('Button', 'submit_lsetting', array(
//        'label' => 'Activate Your Plugin Now',
//        'type' => 'submit',
//        'ignore' => true,
//        'order' => 500,
//    ));

      $this->addElement('Radio', 'sitestoreproduct_cat_widgets', array(
          'label' => 'Widgetized Homepage Creation of Categories',
          'description' => "Do you want to enable the creation of widgetized home pages for all the categories of this plugin? (If you enable this, then all the categories you will create from now will have their own widgetized pages and users will be redirected to these pages when they click on them from Categories home page or breadcrumbs.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.cat.widgets', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_cart_update', array(
          'label' => 'Cart Contents Dropdown',
          'description' => "Show the dropdown tooltip in the header that attractively displays the content of user’s cart.",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.cart.update', 1),
      ));

//    $this->addElement('Text', 'sitestoreproduct_rate_precision', array(
//        'label' => 'Rounding-off Precision',
//        'description' => 'Enter the limit for rounding-off precision after decimal point for the various monetary figures like taxes, shipping charges, etc.',
//        'required' => true,
//        'value' => $settings->getSetting('sitestoreproduct.rate.precision', "2"),
//    ));

      $this->addElement('Radio', 'sitestoreproduct_network', array(
          'label' => 'Browse by Networks',
          'description' => "Do you want to show products according to viewer's network if he has selected any? (If set to no, all the products will be shown.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showDefaultNetwork(this.value)',
          'value' => $settings->getSetting('sitestoreproduct.network', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_default_show', array(
          'label' => 'Set Only My Networks as Default in search',
          'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the products browse and home pages, and enables users to search and filter products.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showDefaultNetworkType(this.value)',
          'value' => $settings->getSetting('sitestoreproduct.default.show', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_networks_type', array(
          'label' => 'Network selection for Products',
          'description' => "You have chosen that viewers should only see Products of their network(s). How should a Product's network(s) be decided?",
          'multiOptions' => array(
              0 => "Product Owner's network(s) [If selected, only members belonging to product owner's network(s) will see the Products.]",
              1 => "Selected Networks [If selected, product owner will be able to choose the networks of which members will be able to see their Product.]"
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.networks.type', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_fs_markers', array(
          'label' => 'Featured, Sponsored and New Markers',
          'description' => 'On Products Home, Browse Products and My Products how do you want a Product to be indicated as Featured, Sponsored and New ? (Note: Products having "New" markers will be indicated by labels only.)',
          'multiOptions' => array(
              1 => 'Using Labels (See FAQ for customizing the labels)',
              0 => 'Using Icons',
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_tinymceditor', array(
          'label' => 'Tinymce Editor',
          'description' => 'Allow TinyMCE editor for discussion message of Products.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.tinymceditor', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_editorprofile', array(
          'label' => 'Editor Profile Link',
          'description' => 'Where do you want to redirect users, when they click on Editors’ photo, name and view profile links?',
          'multiOptions' => array(
              1 => 'On Editor Profile',
              0 => 'On Member Profile',
          ),
          'value' => $settings->getSetting('sitestoreproduct.editorprofile', 1),
      ));

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $localeObject = Zend_Registry::get('Locale');
      $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
      $this->addElement('Dummy', 'sitestoreproduct_currency', array(
          'label' => 'Currency',
          'description' => "<b>" . $currencyName . "</b> <br class='clear' /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true) . "' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
      ));
      $this->getElement('sitestoreproduct_currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

      $this->addElement('Radio', 'sitestoreproduct_openclose', array(
          'label' => 'Open / Closed Status',
          'description' => 'Do you want the Open / Closed Status to be enabled for products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'value' => $settings->getSetting('sitestoreproduct.openclose', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_reviews', array(
          'label' => 'Allow Reviews',
          'description' => "Do you want to allow editors and users to write reviews on products? (Note: From Member Level Settings, you can choose if visitors should be able to review products. You can edit other settings for reviews on your site from 'Reviews & Ratings' section.)",
          'multiOptions' => array(
              3 => 'Yes, allow Editors and Users',
              2 => 'Yes, allow Users only',
              1 => 'Yes, allow Editors only',
              0 => 'No',
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2),
          'onclick' => 'hideOwnerReviews(this.value);'
      ));

      $this->addElement('Radio', 'sitestoreproduct_allowownerreview', array(
          'label' => 'Allow Product Owners to Review',
          'description' => 'Do you want to allow product owners to review and rate products created by them?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.allowownerreview', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_compare', array(
          'label' => 'Enable Comparison',
          'description' => 'Do you want to enable the comparison of products? (If enabled, then users will be able to compare the products.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.compare', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_brands', array(
          'label' => "Use 'Tags' as 'Brand'",
          'description' => "Do you want to use 'Tags' as 'Brand' for the products on your site? (If you select Yes here, then users will be able to enter only one value in the Brand field of their products.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct_brands', 1),
      ));

//    $this->addElement('Radio', 'sitestoreproduct_expiry', array(
//        'label' => 'Product Duration',
//        'description' => 'Do you want fixed duration products on your website? (Fixed Duration products will get expired after certain time and will not appear in home, browse pages and widgets.)',
//        'multiOptions' => array(
//            0 => 'No',
//            1 => 'Yes, Product owners will be able to choose if their products should get expired along with expiry time.',
//            2 => 'Yes, make all products expire after a fixed duration. (You can choose the duration below.)'
//        ),
//        'onchange' => 'showExpiryDuration(this.value)',
//        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.expiry', 0),
//    ));

      $this->addElement('Duration', 'sitestoreproduct_adminexpiryduration', array(
          'label' => 'Duration',
          'description' => 'Select the duration after which Products will expire. (This count will start from the products approval dates. Users will see this duration while creating their products.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adminexpiryduration', array('1', 'week')),
      ));

      $multiOptions = array(
          'day' => 'Day(s)',
          'week' => 'Week(s)',
          'month' => 'Month(s)',
          'year' => 'Year(s)');
      $this->getElement('sitestoreproduct_adminexpiryduration')
              ->setMultiOptions($multiOptions)
      ;

      $this->addElement('Radio', 'sitestoreproduct_categoryedit', array(
          'label' => 'Edit Products Category',
          'description' => 'Do you want to allow product owners to edit category of their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.categoryedit', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_productcode', array(
          'label' => 'Allow Product SKU',
          'description' => 'Do you want to allow SKU for products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_bodyallow', array(
          'label' => 'Allow Description',
          'description' => 'Do you want to allow product owners to write description for their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.bodyallow', 1),
          'onclick' => 'showDescription(this.value)'
      ));

      $this->addElement('Radio', 'sitestoreproduct_bodyrequired', array(
          'label' => 'Description Required',
          'description' => 'Do you want to make Description a mandatory field for products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.bodyrequired', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_mainphoto', array(
          'label' => 'Main Photo Required',
          'description' => 'Do you want to make Main Photo a mandatory field for products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.mainphoto', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_overview', array(
          'label' => 'Allow Overview',
          'description' => 'Do you want to allow product owners to write overview for their products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1),
          'onclick' => 'showOverviewText(this.value)'
      ));

      $this->addElement('Radio', 'sitestoreproduct_overviewcreation', array(
          'label' => 'Overview while Product Creation',
          'description' => 'Do you want to allow product owners to write overview while creating products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overviewcreation', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_quantitybox', array(
          'label' => 'Show Quantity Text Box',
          'description' => 'Do you want to show Quantity text box on Product Profile and Quick view of a product? (If this setting is enabled then buyers will be allowed to enter the product quantity from Product Profile page and Product Quick view.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantitybox', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_vat', array(
          'label' => 'VAT on Products',
          'description' => 'Do you want to allow VAT on products? (In this case seller and site administrator will not be able to add any other tax on products and all existing taxes will be disabled.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0),
          'onclick' => 'showCheckboxSettings(this.value)',
      ));


      $this->addElement('Radio', 'sitestoreproduct_show_checkbox_vat_inclusive', array(
          'label' => 'VAT Inclusive Pricing',
          'description' => 'Do you want to provide a checkbox setting to store owners in store dashboard for VAT inclusive pricing of their products, stating: "Do you want to enter product prices as their Basic Price (excluding VAT) on the product create and edit pages?" ?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.checkbox.vat.inclusive', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_show_checkbox_net_prices', array(
          'label' => 'Price Display wrt VAT',
          'description' => 'Do you want to provide a checkbox setting to store owners in store dashboard for display of pricing of their products, stating: "Do you want Basic Price (excluding VAT) to be displayed for your products at various places?" ?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.checkbox.net.prices', 1),
      ));


//      $this->addElement('Radio', 'sitestoreproduct_quantity', array(
//          'label' => 'Enable Quantity For Product Attributes (configurable & virtual products)',
//          'description' => 'Do you want to allow store owners to specify quantity for various product attributes of their configurable / virtual products? If enabled then store owners will not be allowed to create product attributes with quantity greater than the product's "In Stock Quantity" added in "Inventory". [Note: This setting will work only for configurable / virtual products.]',
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantity', 0),
//      ));

      $this->addElement('Radio', 'sitestoreproduct_combination', array(
          'label' => 'Enable Creation Of Variations',
          'description' => 'Do you want to allow creation of product variations for Configurable / Virtual Products? (Product variations can be created with combinations of "Select Box" type product attributes. Example, you can choose that for a particular shirt, you have a variation like "Color: Red", "Size: Medium" with 30 Quantity. If enabled, and variations are created for a product, then buyers will be able to choosing desired variation of a product while adding it to cart.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1),
      ));

//      $this->addElement('Radio', 'sitestoreproduct_check_combination_quantity', array(
//          'label' => 'Check Quantity Of Combinations',
//          'description' => 'Do you want to allow quantity check for the combination products created via configurable / virtual products? If enabled then products will be created with unlimited quantity for the stores.',
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.check.combination.quantity', 0),
//      ));
//      $this->addElement('Radio', 'sitestoreproduct_product_vat_creator', array(
//          'label' => 'VAT Creation',
//          'description' => 'Who will be able to create VAT?',
//          'multiOptions' => array(
//              1 => 'Site Administrator (If enabled site admin will be able to create VAT from Taxes section in the admin panel.)',
//              0 => "Seller of the Product (If enabled seller will be able to create VAT from the taxes section available in the Store's Dashboard.)"
//          ),
//          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.product.vat.creator', 0),
//      ));

      $this->addElement('MultiCheckbox', "sitestoreproduct_contactdetail", array(
          'label' => 'Contact Detail Options',
          'description' => 'Choose the contact details options from below that you want to be enabled for the products. (Users will be able to fill below chosen details for their products from their Product Dashboard. To disable contact details section from Product dashboard, simply uncheck all the options.)',
          'multiOptions' => array(
              'phone' => 'Phone',
              'website' => 'Website',
              'email' => 'Email',
          ),
          'value' => @unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('temp.sitestoreproduct.contactdetail', array()))
      ));

      $this->addElement('Radio', "sitestoreproduct_metakeyword", array(
          'label' => 'Meta Tags / Keywords',
          'description' => 'Do you want to enable product owners to add Meta Tags / Keywords for their products? (If enabled, then product owners will be able to add them from "Meta Keyword" section of their Product Dashboard.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.metakeyword', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_sponsored', array(
          'label' => 'Sponsored Label',
          'description' => 'Do you want to show the "SPONSORED" label on the main pages of sponsored products below the product title?',
          'onclick' => 'showsponsored(this.value)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsored', 1),
      ));

      $this->addElement('Text', 'sitestoreproduct_sponsoredcolor', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_formImagerainbowSponsred.tpl',
                      'class' => 'form element'
                  )))
      ));

      $this->addElement('Radio', 'sitestoreproduct_featured', array(
          'label' => 'Featured Label',
          'description' => 'Do you want to show the "FEATURED" label on the main pages of featured products below the product title?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showfeatured(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featured', 1),
      ));

      $this->addElement('Text', 'sitestoreproduct_featuredcolor', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_formImagerainbowFeatured.tpl',
                      'class' => 'form element'
                  )))
      ));


      $this->addElement('Radio', 'sitestoreproduct_profiletab', array(
          'label' => 'Tabs Design Type',
          'description' => 'Select the design type for the tabs available on the main pages of products.',
          'multiOptions' => array(
              1 => 'New Tabs',
              0 => 'SocialEnigne - Default Tabs'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.profiletab', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_show_tabs_without_content', array(
          'label' => 'Show Tabs without Content',
          'description' => 'Do you want to show the different content based tabs on Store Product Profile page (like Albums, Videos etc) to the users who do not have permission to add content to those tabs even when there is no content in that tab.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.tabs.without.content', 0),
      ));

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $field = 'sitestoreproduct_code_share';
      $this->addElement('Dummy', "$field", array(
          'label' => 'Social Share Widget Code',
          'description' => "<a class='smoothbox' href='" . $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) . "'>Click here</a> to add your social share code.",
          'ignore' => true,
      ));
      $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));


      $this->addElement('Radio', 'sitestoreproduct_default_local', array(
          'label' => 'Enable Default Locale Settings for Non-Logged-In users ?',
          'description' => "Do you want to enable default Locale settings for non-logged-in users? [If enabled, then all the non-logged-in users will be able to view the price, description, etc of the Stores and Products based on the 'Locale Settings' done by site administrator.]",
          'multiOptions' => array(
              1 => 'Enabled',
              0 => 'Disabled'
          ),
          'value' => $settings->getSetting('sitestoreproduct.default.local', 0),
      ));
      // ELEMENT FOR MULTILANGUAGE
      $this->addElement('Radio', 'sitestoreproduct_multilanguage', array(
          'label' => 'Multiple Languages for Products',
          'description' => "Do you want to enable multiple languages for Products at the time of their creation? (Select 'Yes', only if you have installed multiple language packs from the 'Language Manager' section of the Admin Panel. Selecting 'Yes' over here will enable creation of Products in the multiple languages installed on your site. If you select 'No' over here, then the pack that you've marked as your 'default' pack will be the language displayed for creation of Product.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.multilanguage', 0),
      ));

      //GET EXISTING LANGUAGES ARRAY
      $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
      $this->addElement('MultiCheckbox', 'sitestoreproduct_languages', array(
          'label' => 'Languages',
          'description' => 'Select the languages for which you want users to be able to create Products.',
          'multiOptions' => $localeMultiOptions,
          'value' => $settings->getSetting('sitestoreproduct.languages'),
      ));

      $this->addElement('Radio', 'sitestoreproduct_locationfield', array(
          'label' => 'Location Field',
          'description' => 'Do you want the Location field to be enabled for products?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.locationfield', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_createlocationfield', array(
          'label' => 'Show Location field on product creation',
          'description' => 'Allow user to enter Location on product creation. If you select "No" store location will be associate with the new product. This setting will work if you select "Yes" from above setting.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.createlocationfield', 1),
      ));

      $this->addElement('Radio', 'sitestoreproduct_proximitysearch', array(
          'label' => 'Proximity Search',
          'description' => 'Do you want proximity search to be enabled for products? (Proximity search will enable users to search for products within a certain distance from a location.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.proximitysearch', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_map_sponsored', array(
          'label' => 'Sponsored Products with a Bouncing Animation',
          'description' => 'Do you want the sponsored products to be shown with a bouncing animation in the Map?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.map.sponsored', 0),
      ));


      $sitestoreproductDefaultpagecEmail = $settings->getSetting('sitestoreproduct.defaultproductcreate.email', '');
      if ( !Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitestoreproduct.defaultproductcreate.email') ) {
        $users = Engine_Api::_()->getDbtable('editors', 'sitestoreproduct')->getAllEditors();       
        $emailTempArray = array();
        foreach ( $users as $userId ) {
          if ( isset($userId['user_id']) ) {
            $userObj = Engine_Api::_()->getItem('user', $userId['user_id']);
            if ( !empty($userObj) && isset($userObj->email) )
              $emailTempArray[] = $userObj->email;
          }
        }

        $sitestoreproductDefaultpagecEmail = @implode(",", $emailTempArray);
      }

      $this->addElement('Textarea', 'sitestoreproduct_defaultproductcreate_email', array(
          'label' => 'Email Alerts for New Products',
          'description' => 'Please enter comma-separated list, or one-email-per-line for email IDs which should be notified when a new store product is created in your marketplace.',
          'value' => $sitestoreproductDefaultpagecEmail,
      ));

      $this->addElement('Text', 'sitestoreproduct_map_city', array(
          'label' => 'Centre Location for Map at Products Home and Browse Products',
          'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Products Home and Browse Products when Map View is chosen to view store.(To show the whole world on the map, enter the word "World" below.)',
          'required' => true,
          'value' => $settings->getSetting('sitestoreproduct.map.city', "World"),
      ));

      $this->addElement('Select', 'sitestoreproduct_map_zoom', array(
          'label' => "Default Zoom Level for Map at Products Home and Browse Products",
          'description' => 'Select the default zoom level for the map which is shown on Products Home and Browse Products when Map View is chosen to view Products. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
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
          'value' => $settings->getSetting('sitestoreproduct.map.zoom', 1),
          'disableTranslator' => 'true'
      ));

      $this->addElement('Radio', 'sitestoreproduct_accordian', array(
          'label' => 'Accordion for Product Creation Form',
          'description' => "Do you want to enable accordion for product creation form?",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.accordian', 0),
      ));

      $this->addElement('Radio', 'sitestoreproduct_ipaddress', array(
          'label' => 'Display IpAddress',
          'description' => "Do you want to display Login Ips of buyers on order bills?",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.ipaddress', 1),
      ));

      //Element:sitestoreproduct_show_product_specifications
      $this->addElement('Radio', 'sitestoreproduct_show_product_specifications', array(
          'label' => 'Product Specifications',
          'description' => 'Do you want to show "more info" link to display product specifications on manage cart and checkout process? [If enabled users will be able to view their product details on manage cart and checkout]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.show.product.specifications', 0),
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'order' => 500,
      ));
    }
  }

}