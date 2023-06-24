<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Search extends Sitestoreproduct_Form_Searchfields {

  protected $_fieldType = 'sitestoreproduct_product';
  protected $_searchForm;
  protected $_priceSettings;
  protected $_subcategoryFiltering;
  protected $_locationSettings;
  protected $_hasMobileMode = false;
  protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }
    
  public function getPriceSettings() {
    return $this->_priceSettings;
  }

  public function setPriceSettings($priceSettings) {
    $this->_priceSettings = $priceSettings;
    return $this;
  }

  public function getSubcategoryFiltering() {
    return $this->_priceSettings;
  }

  public function setSubcategoryFiltering($subcategoryFiltering) {
    $this->_subcategoryFiltering = $subcategoryFiltering;
    return $this;
  }

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }

  public function getLocationSettings() {
    return $this->_locationSettings;
  }

  public function setLocationSettings($locationSettings) {
    $this->_locationSettings = $locationSettings;
    return $this;
  }

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'sitestoreproducts_browse_filters field_search_criteria',
                'method' => 'GET'
    ));
    parent::init();

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->loadDefaultDecorators();

    $this->getMemberTypeElement();

    $this->getAdditionalOptionsElement();

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    
    $resultsAction = isset($this->_widgetSettings['resultsAction']) ? $this->_widgetSettings['resultsAction'] : 'index';

    if ($module == 'sitestoreproduct' && $controller == 'index' && $action == 'manage') {
      $this->setAction($view->url(array('action' => 'manage'), "sitestoreproduct_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesitestoreproducts_criteria');
    } else { 
      $this->setAction($view->url(array('action' => $resultsAction), "sitestoreproduct_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesitestoreproducts_criteria');
    }       
  }

  public function getMemberTypeElement() {

    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('hidden', 'profile_type', array(
        'order' => -1000001,
        'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_' . $profileTypeField->field_id . ' ',
        'onchange' => 'changeFields($(this));',
        'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }

  public function getAdditionalOptionsElement() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $i = 99980;
    $orderwhatWhereWithinmile = -1000;
    $this->addElement('Hidden', 'page', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag_id', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'city', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'start_date', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'end_date', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'categoryname', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Latitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Longitude', array(
        'order' => $i++,
    ));

    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'search');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Text', 'search', array(
//          'label' => 'Name / Keyword',
//          'order' => $row->order,
          'label' => empty($this->_locationSettings['whatWhereWithinmile']) ? 'Name / Keyword' : 'What',
          'order' => empty($this->_locationSettings['whatWhereWithinmile']) ? $row->order : $orderwhatWhereWithinmile,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
      ));
    }

    //GET API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'orderby');
    if (!empty($row) && !empty($row->display)) {

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2) {
        if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
              'comment_count' => "Most Commented",
              'review_count' => "Most Reviewed",
              'rating_avg' => "Most Rated",
          );
        } else {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
              'review_count' => "Most Reviewed",
              'rating_avg' => "Most Rated",
          );
        }
      } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
        if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
              'comment_count' => "Most Commented",
              'rating_avg' => "Most Rated",
          );
        } else {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
              'rating_avg' => "Most Rated",
          );
        }
      } else {
        if (Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product")) {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
              'comment_count' => "Most Commented",
          );
        } else {
          $multiOptionsOrderBy = array(
              '' => "",
              'price_low_to_high' => 'Price low to high',
              'price_high_to_low' => 'Price high to low',
              'discount_amount' => 'Most Discounted',
              'title' => "Alphabetic",
              'product_id' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => "Most Liked",
//            'comment_count' => "Most Commented",
          );
        }
      }

      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => $multiOptionsOrderBy,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestoreproducts();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    } else {
      $this->addElement('hidden', 'orderby', array(
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'closed');
    if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      $this->addElement('Select', 'closed', array(
          'label' => 'Status',
          'multiOptions' => array(
              '' => Zend_Registry::get('Zend_Translate')->_("All Products"),
              '0' => Zend_Registry::get('Zend_Translate')->_("Only Open Products"),
              '1' => Zend_Registry::get('Zend_Translate')->_("Only Closed Products")
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestoreproducts();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'show');
    if (!empty($row) && !empty($row->display)) {
      $show_multiOptions = array();
      $show_multiOptions["1"] = "Everyone's Products";
      $show_multiOptions["2"] = "Only My Friends' Products";
      $show_multiOptions["4"] = "Products I Like";
      $value_deault = 1;
      $enableNetwork = $settings->getSetting('sitestoreproduct.network', 0);
      if (empty($enableNetwork)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

        if (!empty($viewerNetwork)) {
          $show_multiOptions["3"] = 'Only My Networks';
          $browseDefaulNetwork = $settings->getSetting('sitestoreproduct.default.show', 0);

          if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
            $value_deault = 3;
          } elseif (isset($_GET['show'])) {
            $value_deault = $_GET['show'];
          }
        }
      }
      $reviewApi = Engine_Api::_()->sitestoreproduct();
      $expirySettings = $reviewApi->expirySettings();
      if ($expirySettings) {
        //$show_multiOptions["only_expiry"] = "Only Expired Products";
      }

      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => $show_multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestoreproducts();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'value' => $value_deault,
      ));
    } else {
      $this->addElement('hidden', 'show', array(
          'value' => 1
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'price');
    if (!empty($row) && !empty($row->display)) {

      if ($this->_priceSettings['priceFieldType'] != 'slider') {
        $subform = new Engine_Form(array(
            'description' => 'Price',
            'elementsBelongTo' => 'price',
            'order' => $row->order,
            'decorators' => array(
                'FormElements',
                array('Description', array('placement' => 'PREPEND', 'tag' => 'label', 'class' => 'form-label')),
                //array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li', 'class' => '', 'id' => 'integer-wrapper'))
            )
        ));
        //Engine_Form::enableForm($subform);
        //unset($params['options']['label']);     
        $params['options']['decorators'] = array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')));
        $params['options']['decorators'] = array('ViewHelper');
        if ($this->gethasMobileMode())
          $params['options']['placeholder'] = 'min';
        $subform->addElement('text', 'min', $params['options']);
        if ($this->gethasMobileMode())
          $params['options']['placeholder'] = 'max';
        $subform->addElement('text', 'max', $params['options']);
        $this->addSubForm($subform, 'price');
      }
      else {
        $this->addElement('Hidden', 'minPrice', array('order' => 8743));
        $this->addElement('Hidden', 'maxPrice', array('order' => 9777));
        $this->addElement('Text', 'priceSlider', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_slider.tpl',
                        'minPrice' => $this->_priceSettings['minPrice'],
                        'maxPrice' => $this->_priceSettings['maxPrice'],
                        'currencySymbolPosition' => $this->_priceSettings['currencySymbolPosition'],
                        'class' => 'form element'
                    ))), 'order' => $row->order,
           'filters' => array(
                      'StripTags',
                      new Engine_Filter_Censor(),
        )
        ));
      }
    }

    // LOCATION WORK

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'location');
    if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {

      $advancedSearchOrder = $row->order;
      $this->addElement('Text', 'location', array(
          'label' => (isset($this->_locationSettings['whatWhereWithinmile']) && empty($this->_locationSettings['whatWhereWithinmile'])) ? 'Location' : 'Where',
          'order' => (isset($this->_locationSettings['whatWhereWithinmile']) && empty($this->_locationSettings['whatWhereWithinmile'])) ? $row->order : ++$orderwhatWhereWithinmile,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
              //'value' => $location,
      ));

      $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($_GET['location'])) {
        $this->location->setValue($_GET['location']);
      } elseif (isset($_GET['locationSearch'])) {
        $this->location->setValue($_GET['locationSearch']);
      } elseif (isset($myLocationDetails['location'])) {
        $this->location->setValue($myLocationDetails['location']);
      }

      if (isset($_GET['location']) || isset($_GET['locationSearch'])) {

        Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
      }

      if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($this->_locationSettings['locationDetection']) && empty($this->_locationSettings['locationDetection'])) {
        $this->location->setValue('');
      }

      $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'proximity');
      if (!empty($row) && !empty($row->display)) {
				$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
				if ($flage) {
					$locationLable = "Within Kilometers";
					$locationOption = array(
						'0' => '',
						'1' => '1 Kilometer',
						'2' => '2 Kilometers',
						'5' => '5 Kilometers',
						'10' => '10 Kilometers',
						'20' => '20 Kilometers',
						'50' => '50 Kilometers',
						'100' => '100 Kilometers',
						'250' => '250 Kilometers',
						'500' => '500 Kilometers',
						'750' => '750 Kilometers',
						'1000' => '1000 Kilometers',
					);
				} else {
        $locationLable = "Within Miles";
        $locationOption = array(
            '0' => '',
            '1' => '1 Mile',
            '2' => '2 Miles',
            '5' => '5 Miles',
            '10' => '10 Miles',
            '20' => '20 Miles',
            '50' => '50 Miles',
            '100' => '100 Miles',
            '250' => '250 Miles',
            '500' => '500 Miles',
            '750' => '750 Miles',
            '1000' => '1000 Miles',
        );
				}
        $advancedSearchOrder = $row->order + 1;
        $this->addElement('Select', 'locationmiles', array(
            'label' => (isset($this->_locationSettings['whatWhereWithinmile']) && empty($this->_locationSettings['whatWhereWithinmile'])) ? $locationLable : $locationLable,
            'multiOptions' => $locationOption,
            'value' => 0,
            'order' => (isset($this->_locationSettings['whatWhereWithinmile']) && empty($this->_locationSettings['whatWhereWithinmile'])) ? $row->order + 1 : ++$orderwhatWhereWithinmile,
            'decorators' => $this->gethasMobileMode() ? array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
                    ) : array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array(array("img" => "HtmlTag"), array(
                        "tag" => "img",
                        "openOnly" => true,
                        "src" => "./application/modules/Seaocore/externals/images/help.gif",
                        "align" => "middle",
                        "class" => "sitestoreproduct_locationmiles_tip",
                        "placement" => "APPEND",
                        'title' => $view->translate('Radius targeting (also known as proximity targeting or "Target a radius") allows you to search content within a certain distance from the selected location, rather than choosing individual city, region, or country. If you want to search content in specific city, region, or country then simply do not select this option.'),
                        'onclick' => 'showRadiusTip();return false;'
                    )),
                array('HtmlTag', array('tag' => 'li')),
                    ),
        ));

        if (isset($_GET['locationmiles'])) {
          $this->locationmiles->setValue($_GET['locationmiles']);
        } elseif (isset($_GET['locationmilesSearch'])) {
          $this->locationmiles->setValue($_GET['locationmilesSearch']);
        } elseif (isset($myLocationDetails['locationmiles'])) {
          $this->locationmiles->setValue($myLocationDetails['locationmiles']);
        }
      }

      $rowStreet = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'street');
      if (!empty($rowStreet) && !empty($rowStreet->display)) {
        $this->addElement('Text', 'sitestoreproduct_street', array(
            'label' => 'Street',
            'order' => $rowStreet->order,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
        ));
      }

      $rowCity = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'city');
      if (!empty($rowCity) && !empty($rowCity->display)) {
        $this->addElement('Text', 'sitestoreproduct_city', array(
            'label' => 'City',
            'order' => $rowCity->order,
            'placeholder' => '',
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
        ));
      }
      $rowState = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'state');
      if (!empty($rowState) && !empty($rowState->display)) {
        $this->addElement('Text', 'sitestoreproduct_state', array(
            'label' => 'State',
            'order' => $rowState->order,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
        ));
      }
      $rowCountry = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'country');
      if (!empty($rowCountry) && !empty($rowCountry->display)) {
        $this->addElement('Text', 'sitestoreproduct_country', array(
            'label' => 'Country',
            'order' => $rowCountry->order,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
        ));
      }
    }
    
    if ($this->_locationSettings['viewType'] == 'horizontal' && $this->_locationSettings['whatWhereWithinmile'] && !$this->_locationSettings['advancedSearch']) {
            $advancedSearch = $this->_locationSettings['advancedSearch'];
            $this->addElement('Cancel', 'advances_search', array(
                'label' => 'Advanced search',
                'ignore' => true,
                'link' => true,
                'order' => ++$orderwhatWhereWithinmile,
                'onclick' => "advancedSearchLists($advancedSearch, 0);",
                'decorators' => array('ViewHelper'),
            ));

            $this->addElement('hidden', 'advanced_search', array(
                'value' => 0
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'category_id');
    if (!empty($row) && !empty($row->display)) {

      $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          if (!empty($this->_subcategoryFiltering)) {
            $onChangeEvent = "showFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
            $categoryFiles = 'application/modules/Sitestoreproduct/views/scripts/_subCategory.tpl';
          } else {
            $onChangeEvent = "";
            $categoryFiles = '';
          }
        } else {
          $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
          $categoryFiles = 'application/modules/Sitestoreproduct/views/sitemobile/scripts/_subCategory.tpl';
        }

        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'order' => $row->order,
            'multiOptions' => $categories_prepared,
            'onchange' => $onChangeEvent,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))),
        ));

        if (!empty($this->_subcategoryFiltering) || !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $this->addElement('Select', 'subcategory_id', array(
              'RegisterInArrayValidator' => false,
              'order' => $row->order + 1,
              'decorators' => array(array('ViewScript', array(
                          'viewScript' => $categoryFiles,
                          'class' => 'form element')))
          ));
        }
      }
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'has_photo');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Checkbox', 'has_photo', array(
          'label' => "Only Products With Photos",
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'discount');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Radio', 'discount', array(
          'label' => "Discount",
          'order' => $row->order,
          'multiOptions' => array(
              '0_10' => 'Upto 10%',
              '10_20' => '10% - 20%',
              '20_30' => '20% - 30%',
              '30_40' => '30% - 40%',
              '40_50' => '40% - 50%',
              '50_100' => 'More than 50%',
          ),
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'PREPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'in_stock');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Checkbox', 'in_stock', array(
          'label' => "Exclude Out of Stock Products",
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestoreproduct', 'has_review');
    if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) {

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3) {
        $multiOptions = array(
            '' => '',
            'rating_avg' => 'Any Reviews',
            'rating_editor' => 'Editor Reviews',
            'rating_users' => 'User Reviews',
        );
      } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2) {
        $multiOptions = array(
            '' => '',
            'rating_users' => 'User Reviews',
        );
      } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
        $multiOptions = array(
            '' => '',
            'rating_editor' => 'Editor Reviews',
        );
      }

      $this->addElement('Select', 'has_review', array(
          'label' => "Products Having",
          'multiOptions' => $multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestoreproducts();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'value' => '',
      ));
    }

    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'submit',
        'onclick' => $this->gethasMobileMode() ? '' : 'searchSitestoreproducts();',
        'ignore' => true,
        'order' => 999999999,
        'decorators' => array(
            'ViewHelper',
            //array('Label', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'li'))
        ),
    ));
  }

}