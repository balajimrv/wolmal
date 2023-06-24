<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Locationsearch extends Fields_Form_Search {

  protected $_searchForm;

  protected $_value;
  
  protected $_atStore = false;

  public function getValue() {
    return $this->_value;
  }

  public function setValue($item) {
    $this->_value = $item;
    return $this;
  }


  public function init() {
  
    $this->_value = unserialize($this->_value);
  
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();
    
    if($module == 'sitestoreproduct') {
      $this->_atStore = true;
    }     

    // Add custom elements
    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => '',
		))
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('POST');
			
		$this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
		
		$this->getMemberTypeElement();
		
		$this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'sitestoreproduct' && $controller == 'index' && $action != 'map') {
			$this->setAction($view->url(array('action' => 'map'), 'sitestoreproduct_general', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
  }
  
  public function getMemberTypeElement() {

    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

//     if (count($options) <= 1) {
//       if (count($options) == 1) {
//         $this->_topLevelId = $profileTypeField->field_id;
//         $this->_topLevelValue = $options[0]->option_id;
//       }
//       return;
//     }

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    asort($multiOptions);

//    $this->addElement('Select', 'profile_type', array(
//			'label' => ($this->_atStore) ? 'Store Profile Type' : 'Store Profile Type',
//			'class' =>
//			'field_toggle' . ' ' .
//			'parent_' . 0 . ' ' .
//			'option_' . 0 . ' ' .
//			'field_' . $profileTypeField->field_id . ' ',
//			'onchange' => 'changeFields($(this));',
//			'multiOptions' => $multiOptions,
//    ));
//    return $this->profile_type;
    return;
  }
  
  public function getAdditionalOptionsElement() {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();

		$subform = new Zend_Form_SubForm(array(
			'name' => 'extra',
			'order' => 19999999,
			'decorators' => array(
				'FormElements',
			)
		));
		Engine_Form::enableForm($subform);
		
	  $i = -5000;
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
		$this->addElement('Text', 'search', array(
			'label' => 'What',
			'autocomplete' => 'off',
			'description' => ($this->_atStore) ? '(Enter keywords or Product name)' : '(Enter keywords or Product name)',
			'order' => 1,
      'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
		));
		$this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

	  $this->addElement('Text', 'sitestoreproduct_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 2,
			'onclick' => 'locationStore();',
      'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
		));
		$this->sitestoreproduct_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0);
		if (!empty($enableLocation)) {
			$enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.proximitysearch', 0);

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
			    'order' => 3,
				));
			}
	  }

    //Check for Location browse store.

    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => 4,
      'onclick' => ($action == 'map') ? 'return locationSearch();':''
    ));
		
		// Element: cancel
    $this->addElement('Cancel', 'advances_search', array(
			'label' => 'Advanced search',
			'ignore' => true,
			'link' => true,
			'order' => 5,
			'onclick' => 'advancedSearchSitestores();',
			'decorators' => array('ViewHelper'),
    ));

		$this->addElement('hidden', 'advanced_search', array(
			'value' => 0
		));

    $this->addDisplayGroup(array('advances_search', 'done','locationmiles', 'search', 'done', 'sitestoreproduct_location'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'style' => 'width:100%;'))
    ));

    $group2 = array();

    if (!empty($this->_value['street'])) {
			$this->addElement('Text', 'sitestoreproduct_street', array(
				'label' => 'Street',
				'autocomplete' => 'off',
				'order' => 12,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
			));
			$group2[] = 'sitestoreproduct_street';
		}

		if (!empty($this->_value['city'])) {
			$this->addElement('Text', 'sitestoreproduct_city', array(
				'label' => 'City',
				'autocomplete' => 'off',
				'order' => 13,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
			));
			$group2[] = 'sitestoreproduct_city';
		}

		if (!empty($this->_value['state'])) {
			$this->addElement('Text', 'sitestoreproduct_state', array(
				'label' => 'State',
				'autocomplete' => 'off',
				'order' => 14,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
			));
			$group2[] = 'sitestoreproduct_state';
		}

		if (!empty($this->_value['country'])) {
			$this->addElement('Text', 'sitestoreproduct_country', array(
				'label' => 'Country',
				'autocomplete' => 'off',
				'order' => 15,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
			));
			$group2[] = 'sitestoreproduct_country';
		}

// 		if (!empty($this->_value['postalcode'])) {
// 			$this->addElement('Dummy', 'or', array(
// 				'label' => 'or',
// 				'order' => 15,
// 			));
//       $group2[] = 'or';
//       
// 			//postal code.
// 			$this->addElement('Text', 'sitestore_postalcode', array(
// 				'label' => 'Postal code',
// 				'autocomplete' => 'off',
// 				'order' => 16,
// 			));
// 			$group2[] = 'sitestore_postalcode';
// 		}

		if(!empty($group2)) {
			$this->addDisplayGroup($group2, 'grp2');
			$button_group = $this->getDisplayGroup('grp2');
			$button_group->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'style' => 'width:100%;'))
			));
		}
    if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store")){            
    $multiOPtionsOrderBy = array(
			'' => '',
			'creation_date' => 'Most Recent',
			'view_count' => 'Most Viewed',
			'comment_count' => 'Most Commented',
			'like_count' => 'Most Liked',
			'title' => "Alphabetical"
    );
    }else{
      $multiOPtionsOrderBy = array(
			'' => '',
			'creation_date' => 'Most Recent',
			'view_count' => 'Most Viewed',
//			'comment_count' => 'Most Commented',
			'like_count' => 'Most Liked',
			'title' => "Alphabetical"
    );
    }
    
    $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if (!empty($sitestorereviewEnabled)) {
      $multiOPtionsOrderBy['review_count'] = "Most Reviewed";
      $multiOPtionsOrderBy['rating'] = "Highest Rated";
    }

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => $multiOPtionsOrderBy,
        'order' => 6,
    ));

//		$sitestoreofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
//    if (!empty($sitestoreofferEnabled)) {
//      $this->addElement('Select', 'offer_type', array(
//				'label' => ($this->_atStore) ? 'Stores With Offers' : 'Stores With Offers',
//				'multiOptions' => array(
//					'' => '',
//					'all' => 'All Offers',
//					'hot' => 'Hot Offers',
//					'featured' => 'Featured Offers',
//				),
//				'order' => 7,
//      ));
//    }


    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$show_multiOptions = array();
      $show_multiOptions["1"] = ($this->_atStore) ? 'Everyone\'s Products' : 'Everyone\'s Products';
      $show_multiOptions["2"] = ($this->_atStore) ? 'Only My Friends\' Products' : 'Only My Friends\' Products';
      $show_multiOptions["4"] = ($this->_atStore) ? 'Products I Like' : 'Products I Like';
      $show_multiOptions["5"] = ($this->_atStore) ? 'Featured Products' : 'Featured Products';
      $value_deault = 1;
		
      if (!empty($viewer_id)) {
      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => $show_multiOptions,
          'order' => 8,
          'value' => $value_deault,
      ));
    }

		$this->addElement('Checkbox', 'has_photo', array(
						'label' => ($this->_atStore) ? 'Only Products With Photos' : 'Only Products With Photos',
						'order' => 10,
		));

    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->addElement('Checkbox', 'has_review', array(
              'label' => ($this->_atStore) ? 'Only Products With Reviews' : 'Only Products With Reviews',
              'order' => 11,
      ));
    }
    
    
		// prepare categories
		$categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories();
		if (count($categories) != 0) {
			$categories_prepared[0] = "";
			foreach ($categories as $category) {
				$categories_prepared[$category->category_id] = $category->category_name;
			}

			// category field
			$this->addElement('Select', 'category_id', array(
				'label' => 'Category',
				'order' => 20,
				'multiOptions' => $categories_prepared,
				'onchange' => "location_subcategoryies(this.value, '', '', '');",
			));
		}

		$this->addElement('Select', 'subcategory_id', array(
			'RegisterInArrayValidator' => false,
			'order' => 21,
			'decorators' => array(array('ViewScript', array(
				'viewScript' => 'application/modules/Sitestore/views/scripts/_Locationsubcategory.tpl',
				'class' => 'form element')))
		));

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

    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));
    
    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));




    $this->addDisplayGroup(array('orderby', 'show', 'closed', 'has_photo', 'has_review', 'offer_type', 'profile_type', 'category_id', 'subcategory_id'), 'grp1');
    $button_group = $this->getDisplayGroup('grp1');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'style' => 'width:100%;'))
    ));

    return $this;
  }
}