<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Create extends Engine_Form {

  public $_error = array();
  protected $_defaultProfileId;
  protected $_storeId;
  protected $_productType;
  protected $_allowedProductTypes;

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }
  
  public function setAllowedProductTypes($allowedProductTypes) {
    $this->_allowedProductTypes = $allowedProductTypes;
    return $this;
  }

  public function getStoreId() {
    return $this->_storeId;
  }

  public function setPageId($store_id) {
    $this->_storeId = $store_id;
    return $this;
  }

  public function getProductType() {
    return $this->_productType;
  }

  public function setProductType($product_type) {
    $this->_productType = $product_type;
    return $this;
  }

  public function init() {
    $tempOrder = 0;
    $divId = 1;
    $product_type = $this->getProductType();
    $note = '';
    $oldTz = date_default_timezone_get();
    date_default_timezone_set(Engine_Api::_()->user()->getViewer()->timezone);
    $date = (string) date('Y-m-d');
    $endDate = (string) date('Y-m-d', strtotime("+1 Month"));
    date_default_timezone_set($oldTz);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $expirySettings = Engine_Api::_()->sitestoreproduct()->expirySettings();
    
    $allowCombinations = $coreSettings->getSetting('sitestoreproduct.combination', 1);
    //$allowCombinationsQuantity = $coreSettings->getSetting('sitestoreproduct.check.combination.quantity', 0);
    
    if ($expirySettings == 2) {
      $translate = Zend_Registry::get('Zend_Translate');
      $duration = $coreSettings->getSetting('sitestoreproduct.adminexpiryduration', array('1', 'week'));
      ;
      $typeStr = $translate->translate(array($duration[1], $duration[1] . 's', $duration[0]));
      $note = sprintf($translate->translate('Note: your product will be expired in %2$s %3$s after an approval or it may be changed by admin.'), $duration[0], $typeStr);
    }

    $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("2. Create New Product")))
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Create your product by configuring the various properties below.")))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitestoreproducts_create');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);

    $tempOrder++;
    $this->addElement('Text', 'informationHeading', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_createFormHeading.tpl',
                    'heading' => $view->translate('General Information  '),
                    'class' => 'form element',
                    'div_open' => 1,
                    'div_id' => $divId++,
                    'first_heading' => 1
                ))),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )
    ));

    //ELEMENT PACKAGE
    $tempOrder++;
    $temProductType = @ucfirst($this->_productType);
    $this->addElement('Dummy', 'productType', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Product Type'),
        'value' => $this->_productType,
        'description' => '<b style="font-size:15px;margin-top:-6px;" class="fleft">' . Zend_Registry::get('Zend_Translate')->_($temProductType) . '</b>'
    ));
    $this->productType->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    // Element: Title
    $tempOrder++;
    $this->addElement('Text', 'title', array(
        'label' => "Product Title",
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));

    $sitestoreproduct_api = Engine_Api::_()->sitestoreproduct();
    $multilanguage_allow = $coreSettings->getSetting('sitestoreproduct.multilanguage', 0);
    $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
    $languages = $coreSettings->getSetting('sitestoreproduct.languages', null);
    $total_allowed_languages = Count($languages);

    if (empty($total_allowed_languages)) {
      $languages[$defaultLanguage] = Zend_Registry::get('Zend_Translate')->_('English');
    }
    $localeMultiOptions = $sitestoreproduct_api->getLanguageArray();
    
    if( !empty($languages) ) {
    foreach ($languages as $label) {

      $lang_name = $label;
      if (isset($localeMultiOptions[$label])) {
        $lang_name = $localeMultiOptions[$label];
      }

      $required_title_body = false;
      $allowEmpty_title_body = true;
      if ($defaultLanguage == $label || empty($multilanguage_allow) || $total_allowed_languages == 1) {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }
      if ($label == 'en') {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }

      $title_field = "title_$label";

      $title_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Product Title in %s"), $lang_name);

      if (empty($multilanguage_allow) || (!empty($multilanguage_allow) && $total_allowed_languages <= 1)) {
        $title_field = "title";
        $title_label = "Title";
      } elseif ($label == 'en' && $total_allowed_languages > 1) {
        $title_field = "title";
      }

      $tempOrder++;
      $this->addElement('Text', "$title_field", array(
          'label' => $title_label,
          'allowEmpty' => $allowEmpty_title_body,
          'required' => $required_title_body,
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
    }}
    // Element: product_code    
    $allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);
    if (empty($product_id) && !empty($allowProductCode)) {
      $tempOrder++;
      $this->addElement('Text', 'product_code', array(
          'label' => "Product SKU",
//          'allowEmpty' => false,
          'autocomplete' => 'off',
//          'required' => true,
          'validators' => array(
//              array('NotEmpty', true),
              array('StringLength', true, array(1, 256))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->product_code->addValidator(new Zend_Validate_Db_NoRecordExists(
              Engine_Db_Table::getTablePrefix() . 'sitestoreproduct_products', 'product_code'
      ));
      $tempOrder++;
      $this->addElement('dummy', 'product_code_msg', array('value' => 0));
      $this->product_code->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    }
    
    if ($product_type == 'grouped' || $product_type == 'bundled') {
      if( $product_type == 'bundled' ) {
        $allowedProductTypes = array();
        foreach( $this->_allowedProductTypes as $allowedProductType )
        {
          switch ($allowedProductType) 
          {
            case 'simple':
              $allowedProductTypes['simple'] = 'Simple Product';
              break;

            case 'configurable':
              $allowedProductTypes['configurable'] = 'Configurable Product';
              break;

            case 'virtual':
              $allowedProductTypes['virtual'] = 'Virtual Product';
              break;

            case 'downloadable':
              $allowedProductTypes['downloadable'] = 'Downloadable Product';
              break;
          }
        }

        $this->addElement('MultiCheckbox', 'bundle_product_type', array(
              'label' => 'Product Types',
              'description' => "Choose the product types of which you want to add products in this bundled product.",
              'RegisterInArrayValidator' => false,
              'multiOptions' => $allowedProductTypes,
              'allowEmpty' => false,
              'required' => true,
              'order' => $tempOrder++,
              'escape' => false,
              'onchange' => 'bundleProductTypes();',
              'value' => @array_keys($allowedProductTypes)
          ));
      }
    
      if( $product_type == 'bundled' ) {
        $autoSuggestDescription = 'Enter the name of products in the auto-suggest box below. (Note: You can only select products belonging to the product types selected by you above.)';
      } else if( $product_type == 'grouped' ) {
        $autoSuggestDescription = "Enter the name of products in the auto-suggest box below. (Note: You can select only 'Simple Products'.)";
      }
      
      $this->addElement('Text', 'product_name', array(
          'label' => "Select Products",
          'order' => $tempOrder,
          'description' => $autoSuggestDescription,
          'autocomplete' => 'off',
          'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
          ));
      Engine_Form::addDefaultDecorators($this->product_name);
      $tempOrder++;
      
      $this->addElement('Hidden', 'product_ids', array(
          'label' => '',
          'order' => $tempOrder,
          'filters' => array(
              'HtmlEntities'
          ),
      ));
      Engine_Form::addDefaultDecorators($this->product_ids);
    }

    if ($product_type == 'simple' || $product_type == 'configurable' || $product_type == 'bundled') {

      $weightUnit = $coreSettings->getSetting('sitestoreproduct.weight.unit', 'lbs');
      
      if ($product_type == 'bundled') {
        $tempOrder++;
        $this->addElement('Radio', 'enable_shipping', array(
              'label' => "Enable Shipping",
              'description' => "Do you want to ship this product to buyer's shipping address?",
              'multiOptions' => array("1" => "Yes", "0" => "No"),
              'value' => 1,
          )
        );
        
        $tempOrder++;
        $this->addElement('Radio', 'weight_type', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Product Weight (in %s)'), $weightUnit),
            'multiOptions' => array(
                "1" => "Dynamic Weight", "0" => "Fixed Weight"
            ),
            'value' => 1,
            'onclick' => 'showWeightType();',
             ));
        
        $product_weight_text = sprintf(Zend_Registry::get('Zend_Translate')->_('Fixed Product Weight (in %s)'), $weightUnit);
      }
      else
        $product_weight_text = sprintf(Zend_Registry::get('Zend_Translate')->_('Product Weight (in %s)'), $weightUnit);

      $tempOrder++;
      $this->addElement('Text', 'weight', array(
          'label' => $product_weight_text,
          'description' => "Enter the weight of this product.",
          'allowEmpty' => false,
          'validators' => array(
              array('NotEmpty', true),
              array('Float', true),
              array('regex', true, array(
                      'pattern' => '/^(?:\d+|\d*\.\d+)$/',
                      'messages' => array(
                          'regexNotMatch' => 'Please enter valid weight.'
                      )))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->weight->getDecorator("Description")->setOption("placement", "append");
    }

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    if ($coreSettings->getSetting('sitestoreproduct_brands', 1)) {
      $tagsLable = Zend_Registry::get('Zend_Translate')->_('Brand');
      $tagsDescription = '';
    } else {
      $tagsLable = Zend_Registry::get('Zend_Translate')->_('Tags (Keywords)');
      $tagsDescription = Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.');
    }
    $tempOrder++;
    $this->addElement('Text', 'tags', array(
        'label' => $tagsLable,
        'autocomplete' => 'off',
        'description' => $tagsDescription,
        'filters' => array(
		      'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    
    
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
    
 if (!empty($this->_isCopyProduct) || !$this->_item || (isset($this->_item->category_id) && empty($this->_item->category_id)) || ($this->_item && $coreSettings->getSetting('sitestoreproduct.categoryedit', 1))) {
      $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }

        $tempOrder++;
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => $categories_prepared,
            'onchange' => "showFields($(this).value, 1); subcategories(this.value, '', '');",
        ));

        $tempOrder++;
        $this->addElement('Select', 'subcategory_id', array(
            'RegisterInArrayValidator' => false,
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('Select', 'subsubcategory_id', array(
            'RegisterInArrayValidator' => false,
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addDisplayGroup(array(
            'subcategory_id',
            'subsubcategory_id',
                ), 'Select', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_formSubcategory.tpl',
                        'class' => 'form element')))
        ));
      }
    }

    if (!$this->_item) {
      $customFields = new Sitestoreproduct_Form_Custom_Standard(array(
          'item' => 'sitestoreproduct_product',
          'productType' => $this->_productType,
          'decorators' => array(
              'FormElements'
      )));
    } else {
      $customFields = new Sitestoreproduct_Form_Custom_Standard(array(
          'item' => $this->getItem(),
          'productType' => $this->_productType,
          'decorators' => array(
              'FormElements'
      )));
    }

    $customFields->removeElement('submit_addtocart');
    if ($customFields->getElement($defaultProfileId)) {
      $customFields->getElement($defaultProfileId)
              ->clearValidators()
              ->setRequired(false)
              ->setAllowEmpty(true);
    }

    $this->addSubForms(array(
        'fields' => $customFields
    ));
    
   
    $allowOverview = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitestoreproduct_product', "overview");
    $allowEdit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitestoreproduct_product', "edit");

    $description = 'Short Description';
//    if ($coreSettings->getSetting('sitestoreproduct.overview', 1) && $coreSettings->getSetting('sitestoreproduct.overviewcreation', 1) && $allowOverview && $allowEdit && !$this->_item) {
//      $description = 'Short Description';
//    } else {
//      $description = 'Short Description';
//    }

    $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestoreproduct_product', "photo");
    $isSectionAllowed = $coreSettings->getSetting('is.section.allowed', 1);
    $allowBody = $coreSettings->getSetting('sitestoreproduct.bodyallow', 1);
    if ($coreSettings->getSetting('sitestoreproduct.overview', 1) && $coreSettings->getSetting('sitestoreproduct.overviewcreation', 1) && $allowOverview && $allowEdit && (!$this->_item || !empty($this->_isCopyProduct))) {
      $allowProductOverview = true;
    } else {
      $allowProductOverview = false;
    }
    
    $isDateSelectoreAllowed = $coreSettings->getSetting('sitestorereservation.dateselector', 0);
    if ($product_type == 'virtual' && !empty($isDateSelectoreAllowed)) {
      $virtualProductOptions = true;
    } else {
      $virtualProductOptions = false;
    }
    
    if( !empty($allowed_upload) || !empty($isSectionAllowed) || !empty($allowBody) || !empty($allowProductOverview) || !empty($virtualProductOptions) ) {
      $this->addElement('Text', 'descriptionHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_createFormHeading.tpl',
                        'heading' => $view->translate('Other Properties'),
                        'class' => 'form element',
                        'div_open' => 1,
                        'div_close' => 1,
                        'div_id' => $divId++,
                    ))),
	   'filters' => array(
		'StripTags',
           	 new Engine_Filter_Censor(),
        	),
            ));
    }
    
    if(!empty($isSectionAllowed)){
      $this->addElement('Select', 'section_id', array(
                 'label' => 'Section',
                 'decorators' => array(array('ViewScript', array(
                 'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_formSections.tpl',
                             'class' => 'form element', 
                     'store_id' => $this->getStoreId()))),

          ));
    }
    
    if(!empty($virtualProductOptions)){
      $this->addElement('Radio', 'virtual_product_date_selector', array(
                 'label' => 'Date Selector',
                 'description' => 'Do you want to enable date selector option for this product?',
                 'multiOptions' => array(
                      1 => 'Yes',
                      0 => 'No'
                  ),
                  'value' => 1
          ));
    }
    
    if ($allowBody) {
      if ($coreSettings->getSetting('sitestoreproduct.bodyrequired', 1)) {

        $tempOrder++;
        $this->addElement('textarea', 'body', array(
            'label' => $description,
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
//                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
      } else {

        $tempOrder++;
        $this->addElement('textarea', 'body', array(
            'label' => 'Description',
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
//                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
      }
    }

    if( !empty($languages) ) {
    foreach ($languages as $label) {

      $lang_name = $label;
      if (isset($localeMultiOptions[$label])) {
        $lang_name = $localeMultiOptions[$label];
      }

      $required_title_body = false;
      $allowEmpty_title_body = true;
      if ($defaultLanguage == $label || empty($multilanguage_allow) || $total_allowed_languages == 1) {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }
      if ($label == 'en') {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }

      $body_field = "body_$label";
      $body_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Short Description in %s"), $lang_name);

      if (empty($multilanguage_allow) || (!empty($multilanguage_allow) && $total_allowed_languages <= 1)) {
        $body_field = "body";
        $body_label = "Short Description";
      } elseif ($label == 'en' && $total_allowed_languages > 1) {
        $body_field = "body";
      }
      $tempOrder++;

      if ($allowBody) {
        if ($coreSettings->getSetting('sitestoreproduct.bodyrequired', 1)) {

          $tempOrder++;
          $this->addElement('textarea', $body_field, array(
              'label' => $body_label,
              'required' => $required_title_body,
              'allowEmpty' => $allowEmpty_title_body,
              'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
              'filters' => array(
                  'StripTags',
//                  new Engine_Filter_HtmlSpecialChars(),
                  new Engine_Filter_EnableLinks(),
                  new Engine_Filter_Censor(),
              ),
          ));
        } else {

          $tempOrder++;
          $this->addElement('textarea', $body_field, array(
              'label' => $body_label,
              'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
              'filters' => array(
                  'StripTags',
//                  new Engine_Filter_HtmlSpecialChars(),
                  new Engine_Filter_EnableLinks(),
                  new Engine_Filter_Censor(),
              ),
          ));
        }
      }
    }
    }
    
    if ($allowProductOverview) {
      $tempOrder++;
      $this->addElement('Textarea', 'overview', array(
          'label' => 'Overview',
          'filters' => array(new Engine_Filter_Censor()),
      ));
    }
    
    if( !empty($languages) ) {
    foreach ($languages as $label) {

      $lang_name = $label;
      if (isset($localeMultiOptions[$label])) {
        $lang_name = $localeMultiOptions[$label];
      }

      $required_title_body = false;
      $allowEmpty_title_body = true;
      if ($defaultLanguage == $label || empty($multilanguage_allow) || $total_allowed_languages == 1) {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }
      if ($label == 'en') {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }

      $overview_field = "overview_$label";
      $overview_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Overview in %s"), $lang_name);

      if (empty($multilanguage_allow) || (!empty($multilanguage_allow) && $total_allowed_languages <= 1)) {
        $overview_field = "overview";
        $overview_label = "Overview";
      } elseif ($label == 'en' && $total_allowed_languages > 1) {
        $overview_field = "overview";
      }
      $tempOrder++;

      if ($allowProductOverview) {
        $tempOrder++;
        $this->addElement('Textarea', $overview_field, array(
            'label' => $overview_label,
            'filters' => array(new Engine_Filter_Censor()),
        ));
      }
    }
    }
    
    
    if ($allowed_upload) {
      $tempOrder++;
      $isMainPhotoRequired = $coreSettings->getSetting('sitestoreproduct.mainphoto', 1);

      // IF COPY PRODUCT, THEN SHOW MAIN PRODUCTS IMAGES
      if( !empty($this->_isCopyProduct) ) {
        $this->addElement('Text', 'imageDiv', array(
            'label' => 'Images',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_copyImages.tpl',
                        'class' => 'form element',
                        'product_id' => $this->_item->getIdentity()
                    ,
                ))),
        ));

        $this->addElement('File', 'image', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Browse Image'),
            'description' => Zend_Registry::get('Zend_Translate')->_("Browse and choose an image for your product. Max file size allowed : ") . (int) ini_get('upload_max_filesize') . Zend_Registry::get('Zend_Translate')->_(" MB. File types allowed: jpg, jpeg, png, gif. You can upload maximum 5 new images. You can upload unlimited images after creating product. (The recommended dimension for the image of product is: 400 x 500 pixels to enable image zoom feature.)"),
            'validators' => array(
                array('Extension', false, 'jpg,png,gif,jpeg')
            ),
            'onchange' => 'imageupload()',
        ));
        $this->image->getDecorator("Description")->setOption("placement", "append");
      } else{
        $this->addElement('File', 'photo', array(
            'label' => 'Main Photo',
            'description' => 'Upload the photo. (The recommended dimension for the photo of product is: 400 x 500 pixels to enable image zoom feature.)',
        ));
    
        if( !empty($isMainPhotoRequired) ) {
          $this->photo->setAllowEmpty(false);
          $this->photo->setRequired(true);
        }
        $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $this->photo->getDecorator("Description")->setOption("placement", "append");
      }
    }
    
    // LOCATION
    if ($coreSettings->getSetting('sitestoreproduct.locationfield', 0)) {
      
      $locationFieldType = ($coreSettings->getSetting('sitestoreproduct.createlocationfield', 1) ? 'Text' : 'Hidden');
      
      $options = array(
            'label' => 'Location',
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
      ));
      
      if($locationFieldType == 'Hidden') {
        $options['order'] = $elementOrder = 706000;
      }
      
      $this->addElement($locationFieldType, 'location', $options);
      
      if($locationFieldType != 'Hidden') {
        $this->location->getDecorator('Description')->setOption('placement', 'append');
      }

      $this->addElement('Hidden', 'locationParams', array('order' => 800000));
      
      include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php'; 
      
      $storeId = $this->getStoreId();
      if(!empty($storeId)) {
          $store = Engine_Api::_()->getItem('sitestore_store', $storeId);
          if(!empty($store->location)) {
              $this->location->setValue("$store->location");
          }
      }
    }

    if ($product_type != 'grouped') {
      $this->addElement('Text', 'pricingHeading', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_createFormHeading.tpl',
                      'heading' => $view->translate('Price & Discounts'),
                      'class' => 'form element',
                      'div_open' => 1,
                      'div_close' => 1,
                      'div_id' => $divId++,
                  ))),
      ));
      
      $localeObject = Zend_Registry::get('Locale');
      $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
      $this->addElement('Text', 'price', array(
          'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
          'description' => "Enter the price of the product.",
          'allowEmpty' => false,
          'maxlength' => 12,
          'validators' => array(
              array('NotEmpty', true),
              array('GreaterThan', false, array(-1))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->price->getDecorator("Description")->setOption("placement", "append");
      
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0)){
        $this->addElement('Text', 'special_vat', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Special VAT'), $currencyName),
            'description' => "Here you can enter a special VAT for this product. This VAT is used for this product in all later calculations (cart, order, invoice). Please leave empty, if you do not want to Special VAT.",
            'validators' => array(
                  array('Int', true),
                  array('Between', true, array('min' => 0, 'max' => 99, 'inclusive' => true)),
              )
            ));
      }  
      
      // START DOWNPAYMENT WORK FOR DIRECT PAYMENT MODE
      $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
      $isDownPaymentEnable = $coreSettings->getSetting('sitestorereservation.downpayment', 0);
      if( !empty($directPayment) && !empty($isDownPaymentEnable) ) {
        $this->addElement('Radio', 'downpayment', array(
            'label' => 'Downpayment',
            'description' => "Do you want to enable downpayment for this product?",
            'multiOptions' => array(
                "1" => "Yes", "0" => "No"
            ),
            'value' => 1,
            'onclick' => 'showDownpayment();',
        ));
          
        $this->addElement('Text', 'downpaymentvalue', array(
            'label' => 'Downpayment Value (%)',
            'description' => 'Enter the value of the downpayment. (Do not add any symbol. For 10% downpayment value, enter downpayment value as 10. You can only enter downpayment percentage between 1 and 100.)',
            'validators' => array(
                  array('Float', true),
                  array('Between', true, array('min' => 1, 'max' => 100, 'inclusive' => true)),
              ),
            'value' => 1,
        ));
      }
      // END DOWNPAYMENT WORK FOR DIRECT PAYMENT MODE
    }
    
    $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
    if( $product_type == 'virtual' && !empty($isSitestorereservationModuleExist) ) {
      $priceTag = unserialize($coreSettings->getSetting('sitestorereservation.pricerange', 'a:6:{i:0;s:5:"fixed";i:1;s:8:"per_hour";i:2;s:7:"per_day";i:3;s:6:"weekly";i:4;s:7:"monthly";i:5;s:6:"yearly";}'));
      if( !empty($priceTag) ) {
        $priceTagOptions = array();
        $priceTagOptions['0'] = 'Select';
        foreach($priceTag as $priceTagValue) {
          switch($priceTagValue){
            case 'fixed':
              $priceTagOptions['fixed'] = 'Fixed';
              break;
            case 'per_hour':
              $priceTagOptions['per_hour'] = 'Per Hour';
              break;
            case 'per_day':
              $priceTagOptions['per_day'] = 'Per Day';
              break;
            case 'weekly':
              $priceTagOptions['weekly'] = 'Weekly';
              break;
            case 'monthly':
              $priceTagOptions['monthly'] = 'Monthly';
              break;
            case 'yearly':
              $priceTagOptions['yearly'] = 'Yearly';
              break;
          }
        }

        if( !empty($priceTag) && !empty($priceTagOptions)) {
          $this->addElement('Select', 'virtual_product_price_range', array(
              'label' => 'Price Basis / Rate',
              'description' => Zend_Registry::get('Zend_Translate')->_("Choose the basis for above price."),
              'multiOptions' => $priceTagOptions,
          ));
        }
      }
    }
    
    if ($product_type != 'grouped') {
      $userTaxes = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getTaxByStore($this->getStoreId(), $product_type);
      if (!empty($userTaxes)) {
        $userTaxArray = array();
        $view_details = Zend_Registry::get('Zend_Translate')->_("view details");
        foreach ($userTaxes as $key => $tax) {
          $userTaxArray[$tax['tax_id']] = $tax['title'] . '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Smoothbox.open(\'' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestoreproduct', 'controller' => 'tax', 'action' => 'view-rate', 'tax_id' => $tax['tax_id'], 'store_id' => $this->getStoreId(), 'title' => $tax['title']), 'default', true) . '\');">' . $view_details . '</a>';
        }

        $this->addElement('MultiCheckbox', 'user_tax', array(
            'label' => 'Taxes',
            'description' => "Choose the taxes which you want to apply on this product.",
            'RegisterInArrayValidator' => false,
            'multiOptions' => $userTaxArray,
            'escape' => false
        ));
      }

      $this->addElement('Radio', 'discount', array(
          'label' => 'Discount',
          'description' => "Do you want to allow discount on this product?",
          'multiOptions' => array(
              "1" => "Yes", "0" => "No"
          ),
          'onclick' => 'showDiscount();',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));

      $this->addElement('Select', 'handling_type', array(
          'label' => 'Discount Type',
          'description' => "Select the type of discount.",
          'multiOptions' => array('0' => 'Fixed', '1' => 'Percent'),
          'value' => 1,
          'onchange' => 'showDiscountType();'
      ));

      $this->addElement('Text', 'discount_rate', array(
          'label' => 'Discount Value (%)',
          'allowEmpty' => false,
          'maxlength' => 6,
          'validators' => array(
              array('NotEmpty', true),
              array('Float', true),
              array('Between', false, array('min' => '0', 'max' => '100', 'inclusive' => false)),
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));

      $localeObject = Zend_Registry::get('Locale');
      $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
      $this->addElement('Text', 'discount_price', array(
          'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Discount Value (%s)'), $currencyName),

          'allowEmpty' => false,
          'validators' => array(
              array('Float', true),
              array('GreaterThan', false, array(0))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));

      $discount_start = new Engine_Form_Element_CalendarDateTime('discount_start_date');
      $discount_start->setLabel('Apply Discount from Date');
      $discount_start->setAllowEmpty(false);
      $discount_start->setValue($date . ' 00:00:00');
      $this->addElement($discount_start);

      $this->addElement('Radio', 'discount_permanant', array(
          'label' => 'Apply Discount to Date',
          'multiOptions' => array("1" => "No end date.", "0" => "End discount on a specific date. (Please select date by clicking on the calendar icon below.)"),
          'description' => "When should discount end?",
          'value' => 1,
          'onclick' => "showDiscountEndDate();",
      ));

      $discount_end = new Engine_Form_Element_CalendarDateTime('discount_end_date');
      $discount_end->setAllowEmpty(false);
      $discount_end->setValue($endDate . ' 00:00:00');
      $this->addElement($discount_end);
      
      $this->addElement('Select', 'user_type', array(
          'label' => 'Allow Discount',
          'description' => 'Who may avail this discount?',
          'multiOptions' => array('0' => 'Everyone', '2' => 'All Registered Members', '1' => 'Public'),
          'value' => 0
      ));
      
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0)) {
        $sellingPrice = "";
        if(!empty($product_id)){
          $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
          if(!empty($productPricesArray) && isset($productPricesArray['display_product_price'])){
            $sellingPrice = $productPricesArray['display_product_price'];
          }
        }
        
        $this->addElement('Dummy', 'product_selling_price', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Selling Price'),
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_productSellingPrice.tpl',
                        'heading' => $view->translate('Selling Price'),
                        'class' => 'form element',
                        'currency_name' => $currencyName,
                        'selling_price' => $sellingPrice
                    ))),
        ));
      }
    }
    
    if( $product_type == 'virtual' )
      $showProductInventory = $coreSettings->getSetting('sitestorereservation.productinventory', 1);
    else
      $showProductInventory = true;

    if ($product_type != 'grouped' && !empty($showProductInventory)) {
    $this->addElement('Text', 'inventoryHeading', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_createFormHeading.tpl',
                    'heading' => $view->translate('Inventory'),
                    'class' => 'form element',
                    'div_open' => 1,
                    'div_close' => 1,
                    'div_id' => $divId++,
                ))),
    ));

      $this->addElement('Text', 'min_order_quantity', array(
          'label' => 'Minimum Order Quantity',
          'description' => "Enter the minimum order quantity for this product. (Buyers will have to add this minimum quantity of this product into their carts to purchase.)",
          'maxlength' => 5,
          'required' => true,
          'validators' => array(
              array('Int', true),
              array('GreaterThan', false, array(0))
          ),
          'value' => 1,
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->min_order_quantity->getDecorator("Description")->setOption("placement", "append");


      $this->addElement('Text', 'max_order_quantity', array(
          'label' => 'Maximum Order Quantity',
          'description' => "Enter the maximum order quantity for this product. (Buyers will not be able to purchase more quantity of this product than this maximum quantity.)",
          'maxlength' => 5,
          'validators' => array(
              array('Int', true),
              array('GreaterThan', false, array(-1))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->max_order_quantity->getDecorator("Description")->setOption("placement", "append");      

//      if(!(($this->_productType == 'configurable' || $this->_productType == 'virtual') && !empty($allowCombinations) && !empty($allowCombinationsQuantity))){
      $this->addElement('Radio', 'stock_unlimited', array(
          'label' => "Unlimited Quantity In Stock",
          'description' => "Do you have unlimited quantity of this product in your stock?",
          'multiOptions' => array(
              "1" => "Yes", "0" => "No"
          ),
          'value' => 1,
          'onclick' => 'showStock();',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));


      $this->addElement('Text', 'in_stock', array(
          'label' => 'In Stock Quantity',
          'allowEmpty' => false,
          'maxlength' => 5,
          'validators' => array(
              array('NotEmpty', true),
              array('Int', false),
              array('GreaterThan', true, array(0))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      //}

      $this->addElement('Radio', 'out_of_stock', array(
          'label' => "Show when Out of Stock",
          'description' => "Do you want to show this product at various places even when this product goes out of stock?",
          'multiOptions' => array(
              "1" => "Yes", "0" => "No"
          ),
          'value' => 1,
          'onclick' => 'showOutOfStock();',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));

      $this->addElement('Select', 'out_of_stock_action', array(
          'label' => "Allow to Contact if Out of Stock",
          'description' => "Do you want to allow buyers to contact you via emails, if this product goes out of stock?",
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => array('0' => 'No',
              '1' => 'Yes')
      ));
    }

    $this->addElement('Text', 'availabilityHeading', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_createFormHeading.tpl',
                    'heading' => $view->translate('Availability'),
                    'class' => 'form element',
                    'div_open' => 1,
                    'div_close' => 1,
                    'div_id' => $divId++,
                ))),
    ));


    $start = new Engine_Form_Element_CalendarDateTime('start_date');
    $start->setAllowEmpty(false);
    $start->setLabel("Product Start Date");
    $start->setValue($date . ' 00:00:00');
    $this->addElement($start);

    $this->addElement('Radio', 'end_date_enable', array(
        'label' => "Product End date",
        'multiOptions' => array("0" => "No end date.", "1" => "End product show to a specific date. (Please select date by clicking on the calendar icon below.)"),
        'description' => "When should this product end?",
        'value' => 0,
        'onclick' => "showEndDate();",
    ));
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $end->setValue($endDate . ' 00:00:00');
    $this->addElement($end);

    $this->addElement('Text', 'privacyHeading', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_createFormHeading.tpl',
                    'heading' => $view->translate('Privacy'),
                    'class' => 'form element',
                    'div_open' => 1,
                    'div_close' => 1,
                    'div_id' => $divId++,
                ))),
    ));

    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me',
    );

    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_product', $user, "auth_view");
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    if (count($view_options) >= 1) {
      $this->addElement('Select', 'auth_view', array(
          'label' => 'View Privacy',
          'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this product?"),
          'multiOptions' => $view_options,
          'value' => key($view_options),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    } else {
      $this->addElement('Hidden', 'auth_view', array(
          'value' => "everyone"
      ));
    }

    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_product', $user, "auth_comment");
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    if (count($comment_options) >= 1) {
      $this->addElement('Select', 'auth_comment', array(
          'label' => 'Comment Privacy',
          'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this product?"),
          'multiOptions' => $comment_options,
          'value' => key($comment_options),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    } else {
      $this->addElement('Hidden', 'auth_comment', array('value' => "everyone"));
    }

    $availableLabels = array(
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me',
    );
//     $photo_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_product', $user, "auth_photo");
//     $photo_options = array_intersect_key($availableLabels, array_flip($photo_options));
// 
//     if (count($photo_options) >= 1) {
//       $this->addElement('Select', 'auth_photo', array(
//           'label' => 'Photo Privacy',
//           'description' => Zend_Registry::get('Zend_Translate')->_("Who may upload photos for this product?"),
//           'multiOptions' => $photo_options,
//           'value' => key($photo_options),
//       ));
//       $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
//     } else {
//       $this->addElement('Hidden', 'auth_photo', array(
//           'value' => 'owner',
//       ));
//     }

//     $videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
//     if ($videoEnable) {
// 
//       $video_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestoreproduct_product', $user, "auth_video");
//       $video_options = array_intersect_key($availableLabels, array_flip($video_options));
// 
//       if (count($video_options) >= 1) {
//         $this->addElement('Select', 'auth_video', array(
//             'label' => 'Video Privacy',
//             'description' => Zend_Registry::get('Zend_Translate')->_("Who may create videos for this product?"),
//             'multiOptions' => $video_options,
//             'value' => key($video_options),
//         ));
//         $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
//       } else {
//         $this->addElement('Hidden', 'auth_video', array(
//             'value' => 'owner',
//         ));
//       }
//     }

    //NETWORK BASE PAGE VIEW PRIVACY
    if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
      // Make Network List
      $table = Engine_Api::_()->getDbtable('networks', 'network');
      $select = $table->select()
              ->from($table->info('name'), array('network_id', 'title'))
              ->order('title');
      $result = $table->fetchAll($select);

      $networksOptions = array('0' => 'Everyone');
      foreach ($result as $value) {
        $networksOptions[$value->network_id] = $value->title;
      }

      if (count($networksOptions) > 0) {
        $this->addElement('Multiselect', 'networks_privacy', array(
            'label' => 'Networks Selection',
            'description' => Zend_Registry::get('Zend_Translate')->_("Select the networks, members of which should be able to see your product. (Press Ctrl and click to select multiple networks. You can also choose to make your product viewable to everyone.)"),
            'multiOptions' => $networksOptions,
            'value' => array(0)
        ));
      } else {
        
      }
    }
    
    $this->addElement('Text', 'othersHeading', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_createFormHeading.tpl',
                    'heading' => $view->translate('Others'),
                    'class' => 'form element',
                    'div_open' => 1,
                    'div_close' => 1,
                    'div_id' => $divId++,
                ))),
    ));    

    
    $this->addElement('Select', 'draft', array(
        'label' => 'Status',
        'multiOptions' => array("0" => "Published", "1" => "Saved As Draft"),
        'description' => 'If this product is published, it cannot be switched back to draft mode.',
        'onchange' => 'checkDraft();'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Checkbox', 'search', array(
        'label' => "Enable this Product",
    ));
    
    // Element:  allow_purchase
//    $this->addElement('Checkbox', 'allow_purchase', array(
//        'label' => "Allow Sell of this Product",
//        'value' => '1'
//    ));

    $this->addElement('Hidden', 'product_type', array(
        'value' => $product_type,
        'order' => 998,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formCreateProduct.tpl',
                    'class' => 'form element'))),
    ));
  }
}
