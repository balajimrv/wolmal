<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Search extends Fields_Form_Search {

  protected $_searchForm;
  protected $_hasMobileMode = false;
  protected $_atStore = false;
  protected $_widgetSettings;
 
  public function getWidgetSettings() {
      return $this->_widgetSettings;
  }

  public function setWidgetSettings($widgetSettings) {
      $this->_widgetSettings = $widgetSettings;
      return $this;
  }

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }

  public function init() {
    // Add custom elements
    $this->setAttribs(array(
    'id' => 'filter_form',
    'class' => 'global_form_box',
    ))->setMethod('GET');    
    
    parent::init();    
    
    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
    
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    
    if($module == 'sitestoreproduct') {
      $this->_atStore = true;
    }    
    
    //if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.profile.search', 1)) {
    $this->getMemberTypeElement();
    //}
    //$this->getDisplayNameElement();
    $this->getAdditionalOptionsElement();
    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $this->setAction($view->url(array('action' => 'index'), 'sitestore_general', true))->getDecorator('HtmlTag')->setOption('class', '');     
    
  }

  public function getMemberTypeElement() {
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'profile_type');
    if (empty($row) || empty($row->display)) {
      return;
    }
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    if (count($options) <= 1) {
      if (count($options) == 1) {
        $this->_topLevelId = $profileTypeField->field_id;
        $this->_topLevelValue = $options[0]->option_id;
      }
      return;
    }

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    asort($multiOptions);

    $this->addElement('Select', 'profile_type', array(
        'label' => ($this->_atStore) ? 'Store Profile Type' : 'Store Profile Type',
        // 'order' => 99,
        'order' => $row->order,
        'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_' . $profileTypeField->field_id . ' ',
        'onchange' => 'changeFields($(this));',
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'li'))
        ),
        'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }

  public function getDisplayNameElement() {
    $this->addElement('Text', 'displayname', array(
        'label' => 'Name',
        'order' => -1000000,
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'li'))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
            //'onkeypress' => 'return submitEnter(event)',
    ));
    return $this->displayname;
  }

  public function getAdditionalOptionsElement() {
    $subform = new Zend_Form_SubForm(array(
                'name' => 'extra',
                'order' => 19999999,
                'decorators' => array(
                    'FormElements',
                )
            ));
    Engine_Form::enableForm($subform);

    //   public function getAdditionalOptionsElement() {
    $i = -5000;

    $this->addElement('Hidden', 'store', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'tag', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'alphabeticsearch', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'start_date', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'end_date', array(
        'order' => $i--,
    ));
        
    $this->addElement('Hidden', 'latitude', array(
        'order' => $i++,
    ));
 
    $this->addElement('Hidden', 'longitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Latitude', array(
        'order' => $i++,
    ));

    $this->addElement('Hidden', 'Longitude', array(
        'order' => $i++,
    ));

    $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(); 

    
    
    
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'price');
    if (!empty($row) && !empty($row->display) && empty($this->_atStore)) {

      $searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
      $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
      if (!empty($enablePrice)) {

        $subformPrice = new Zend_Form_SubForm(array(
                    'description' => "Price",
                    // 'order' => $i--,
                    'order' => $row->order,
                    'decorators' => array(
                        'FormElements',
                        array('Description', array('placement' => 'PREPEND', 'tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li', 'class' => 'browse-range-wrapper'))
                    )
                ));
        Fields_Form_Standard::enableForm($subformPrice);
        Engine_Form::enableForm($subformPrice);

        $params['options']['decorators'] = array('ViewHelper');
        $params['options']['placeholder'] = 'min';
        $subformPrice->addElement('text', 'min', $params['options']);
        $params['options']['placeholder'] = 'max';
        $subformPrice->addElement('text', 'max', $params['options']);
        $this->addSubForm($subformPrice, 'sitestore_price');
      }
    }

    $rowLocation = $this->_searchForm->getFieldsOptions('sitestore', 'location');
    if (!empty($rowLocation) && !empty($rowLocation->display)) {
      $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
      if (!empty($enableLocation)) {
        $row = $this->_searchForm->getFieldsOptions('sitestore', 'locationmiles');
        if (!empty($row) && !empty($row->display)) {
          $enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximitysearch', 1);
          if (!empty($enableProximitysearch)) {
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
            $this->addElement('Select', 'locationmiles', array(
                'label' => $locationLable,
                'multiOptions' => $locationOption,
                'value' => '0',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
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
        }
        $this->addElement('Text', 'sitestore_location', array(
            'label' => 'Location',
            'order' => $rowLocation->order,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        ));

        $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        if (isset($_GET['sitestore_location'])) {
         $this->sitestore_location->setValue($_GET['sitestore_location']);
        } elseif (isset($_GET['locationSearch'])) {
          $this->sitestore_location->setValue($_GET['locationSearch']);
        } elseif (isset($myLocationDetails['location'])) {
          $this->sitestore_location->setValue($myLocationDetails['location']);
          if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && empty($myLocationDetails['location']) && isset($_GET['is_ajax_load'])){ 
            $this->sitestore_location->setValue(true);
          }
        }

        if (isset($_GET['sitestore_location']) || isset($_GET['locationSearch'])) {
          Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
        }

        if (!isset($_GET['sitestore_location']) && !isset($_GET['locationSearch']) && isset($this->_widgetSettings['locationDetection']) && empty($this->_widgetSettings['locationDetection'])) {
          $this->sitestore_location->setValue('');
        }     
        
        
        $rowLocation = $this->_searchForm->getFieldsOptions('sitestore', 'street');
        if (!empty($rowLocation) && !empty($rowLocation->display)) {
          $this->addElement('Text', 'sitestore_street', array(
              'label' => 'Street',
              'order' => $rowLocation->order,
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
              'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
          ));
        }

        $rowLocation = $this->_searchForm->getFieldsOptions('sitestore', 'city');
        if (!empty($rowLocation) && !empty($rowLocation->display)) {
          $this->addElement('Text', 'sitestore_city', array(
              'label' => 'City',
              'order' => $rowLocation->order,
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
              'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
          ));
        }
        $rowLocation = $this->_searchForm->getFieldsOptions('sitestore', 'state');
        if (!empty($rowLocation) && !empty($rowLocation->display)) {
          $this->addElement('Text', 'sitestore_state', array(
              'label' => 'State',
              'order' => $rowLocation->order,
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
              'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
          ));
        }
        $rowLocation = $this->_searchForm->getFieldsOptions('sitestore', 'country');
        if (!empty($rowLocation) && !empty($rowLocation->display)) {
          $this->addElement('Text', 'sitestore_country', array(
              'label' => 'Country',
              'order' => $rowLocation->order,
              'decorators' => array(
                  'ViewHelper',
                  array('Label', array('tag' => 'span')),
                  array('HtmlTag', array('tag' => 'li'))
              ),
              'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
          ));
        }
      }
    }
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'search');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Text', 'search', array(
          'label' => ($this->_atStore) ? 'Search Stores' : 'Search Stores',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
          'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
      ));
      
        if (isset($_GET['search'])) {
            $this->search->setValue($_GET['search']);
        } elseif (isset($_GET['titleAjax'])) {
            $this->search->setValue($_GET['titleAjax']);
        }        
    }

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'badge_id');
    if (!empty($row) && !empty($row->display)) {
      if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorebadge.seaching.bybadge', 1)) {

        $params = array();
        $params['search_code'] = 1;
        $badgeData = Engine_Api::_()->getDbTable('badges', 'sitestorebadge')->getBadgesData($params);
        if (!empty($badgeData)) {
          $badgeData = $badgeData->toArray();
          $badgeCount = Count($badgeData);

          if (!empty($badgeCount)) {
            $badge_options = array();
            $badge_options[0] = '';
            foreach ($badgeData as $key => $name) {
              $badge_options[$name['badge_id']] = $name['title'];
            }

            $this->addElement('Select', 'badge_id', array(
                'label' => 'Badge',
                'multiOptions' => $badge_options,
                'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
          }
        }
      }
    }
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'orderby');
    $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if (!empty($row) && !empty($row->display) && !empty($sitestorereviewEnabled)) {
      if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store")){
          $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'comment_count' => 'Most Commented',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
              'review_count' => "Most Reviewed",
              'rating' => "Highest Rated",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      )); 
     
      }else{
      $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
              'review_count' => "Most Reviewed",
              'rating' => "Highest Rated",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));    
      }
    } elseif (!empty($row) && !empty($row->display)) {
        if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store")){
        $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'comment_count' => 'Most Commented',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
      }else{
          $this->addElement('Select', 'orderby', array(
          'label' => 'Browse By',
          'multiOptions' => array(
              '' => '',
              'creation_date' => 'Most Recent',
              'view_count' => 'Most Viewed',
              'like_count' => 'Most Liked',
              'title' => "Alphabetical",
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
        
      }
    } else {
      $this->addElement('hidden', 'orderby', array(
      ));
    }

    $sitestoreofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'offer_type');
    if (!empty($row) && !empty($row->display) && !empty($sitestoreofferEnabled)) {
      $this->addElement('Select', 'offer_type', array(
          'label' => ($this->_atStore) ? 'Stores With Offers' : 'Stores With Offers',
          'multiOptions' => array(
              '' => '',
              'all' => 'All Offers',
              'hot' => 'Hot Offers',
              'featured' => 'Featured Offers',
          ),
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('tag' => 'span')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'show');
    if (!empty($row) && !empty($row->display)) {
      $show_multiOptions = array();
      $show_multiOptions["1"] = ($this->_atStore) ? 'Everyone\'s Stores' : 'Everyone\'s Stores';
      $show_multiOptions["2"] = ($this->_atStore) ? 'Only My Friends\' Stores' : 'Only My Friends\' Stores';
      $show_multiOptions["4"] = ($this->_atStore) ? 'Stores I Like' : 'Stores I Like';
      $show_multiOptions["5"] = ($this->_atStore) ? 'Featured Stores' : 'Featured Stores'; 
      $value_deault = 1;
      $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.network', 0);
      if (empty($enableNetwork)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

        if (!empty($viewerNetwork) || Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
          $show_multiOptions["3"] = 'Only My Networks';
          $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.default.show', 0);

          if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
            $value_deault = 3;
          } elseif (isset($_GET['show'])) {
            $value_deault = $_GET['show'];
          }
        }
      }

      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => $show_multiOptions,
          'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
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
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'closed');
    if (!empty($row) && !empty($row->display)) {
      $enableStatus = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
      if ($enableStatus) {
        $this->addElement('Select', 'closed', array(
            'label' => 'Status',
            'multiOptions' => array(
                '' => ($this->_atStore) ? 'All Stores' : 'All Stores',
                '0' => ($this->_atStore) ? 'Only Open Stores' : 'Only Open Stores',
                '1' => ($this->_atStore) ? 'Only Closed Stores' : 'Only Closed Stores',
            ),
            'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
            'order' => $row->order,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
        ));
      }
    }

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'category_id');
    if (!empty($row) && !empty($row->display)) {
      // prepare categories
      $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          $onChangeEvent = "subcategoryies(this.value, '', '');";
          $categoryFiles = 'application/modules/Sitestore/views/scripts/_Subcategory.tpl';
        } else {
          $onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
          $categoryFiles = 'application/modules/Sitestore/views/sitemobile/scripts/_Subcategory.tpl';
        }

        // category field
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
      }

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => $row->order + 1,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));

      $this->addElement('Select', 'subsubcategory_id', array(
          'RegisterInArrayValidator' => false,
          'order' => $row->order + 1,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));
    } else {
      $this->addElement('Hidden', 'category_id', array(
          'order' => $i--,
      ));

      $this->addElement('Hidden', 'subcategory_id', array(
          'order' => $i--,
      ));

      $this->addElement('Hidden', 'subsubcategory_id', array(
          'order' => $i--,
      ));
    }

    $this->addElement('Hidden', 'category', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategory', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'categoryname', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
        'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
        'order' => $i--,
    ));

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'has_photo');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Checkbox', 'has_photo', array(
          'label' => ($this->_atStore) ? 'Only Stores With Photos' : 'Only Stores With Photos',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('sitestore', 'has_review');
    if (!empty($row) && !empty($row->display) && (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->addElement('Checkbox', 'has_review', array(
          'label' => ($this->_atStore) ? 'Only Stores With Reviews' : 'Only Stores With Reviews',
          'order' => $row->order,
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
              array('HtmlTag', array('tag' => 'li'))
          ),
      ));
    }

    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoregeolocation')) {
      $row = $this->_searchForm->getFieldsOptions('sitestore', 'has_currentlocation');
      if (!empty($row) && !empty($row->display)) {
        $this->addElement('Checkbox', 'has_currentlocation', array(
            'label' => 'Only current place and range',
            'order' => $row->order,
            'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
                array('HtmlTag', array('tag' => 'li'))
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.default', 1)
        ));
      }
    }
    $subform->addElement('Button', 'done', array(
        'label' => 'Search',
        'type' => 'submit',
        'onclick' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'li'))
        ),
    ));

    $this->addSubForm($subform, $subform->getName());

    return $this;
  }

}

?>
