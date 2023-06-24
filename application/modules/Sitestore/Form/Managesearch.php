<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Managesearch.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Managesearch extends Fields_Form_Search {

  protected $_searchForm;

  protected $_hasMobileMode = false;

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }

  public function init() {

    $this->setAttribs(array('id' => 'filter_form'));
    $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
    // Add custom elements
    $this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();

    $this->getDecorator('HtmlTag')->setOption('class', '');
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

    $this->addElement('Hidden', 'start_date', array(
            'order' => $i--,
    ));

    $this->addElement('Hidden', 'end_date', array(
            'order' => $i--,
    ));
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'price');
    if (!empty($row) && !empty($row->display)) {
      $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
      if (!empty($enablePrice)) {

        $subformPrice = new Zend_Form_SubForm(array(
                    'description' => "Price",
                    'order' => $row->order,
                    'decorators' => array(
                            'FormElements',
                            array('Description', array('placement' => 'PREPEND', 'tag' => 'span')),
                            array('HtmlTag', array('tag' => 'li', 'class' => 'browse-range-wrapper'))
                    )
            ));
        Fields_Form_Standard::enableForm($subformPrice);
        Engine_Form::enableForm($subformPrice);

        $maxparams['options']['decorators'] = $minparams['options']['decorators'] = array('ViewHelper');
        $minparams['options']['placeholder'] = 'min';
        $maxparams['options']['placeholder'] = 'max';
        $subformPrice->addElement('text', 'min', $minparams['options']);
        $subformPrice->addElement('text', 'max', $maxparams['options']);
        $this->addSubForm($subformPrice, 'price');
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
                    'order' => $row->order,
                    'decorators' => array(
                            'ViewHelper',
                            array('Label', array('tag' => 'span')),
                            array('HtmlTag', array('tag' => 'li'))
                    ),
            ));
          }
        }
        $this->addElement('Text', 'location', array(
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
      }
    }
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'search');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Text', 'search', array(
              'label' => 'Search Stores',
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
//                      'comment_count' => 'Most Commented',
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
    }
    
    elseif(!empty($row) && !empty($row->display)) {
     if(Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store")){
        $this->addElement('Select', 'orderby', array(
              'label' => 'Browse By',
              'multiOptions' => array(
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
                      'creation_date' => 'Most Recent',
                      'view_count' => 'Most Viewed',
//                      'comment_count' => 'Most Commented',
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
              'value' => 'creation_date'
      ));
    }
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'show');
    if (!empty($row) && !empty($row->display)) {
      $this->addElement('Select', 'show', array(
              'label' => 'Show',
              'multiOptions' => array(
                      '1' => 'Everyone\'s Posts',
                      '2' => 'Only My Friends\' Posts',
              ),
              'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
              'order' => $row->order,
              'decorators' => array(
                      'ViewHelper',
                      array('Label', array('tag' => 'span')),
                      array('HtmlTag', array('tag' => 'li'))
              ),
      ));
    } else {
      $this->addElement('hidden', 'show', array(
              'value' => 1
      ));
    }
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'closed');
    if (!empty($row) && !empty($row->display)) {
//      $enableStatus = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
//      if ($enableStatus) {
      $this->addElement('Select', 'closed', array(
              'label' => 'Status',
              'multiOptions' => array(
                      '' => 'All Stores',
                      '0' => 'Only Open Stores',
                      '1' => 'Only Closed Stores',
              ),
              'onchange' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
              'order' => $row->order,
              'decorators' => array(
                      'ViewHelper',
                      array('Label', array('tag' => 'span')),
                      array('HtmlTag', array('tag' => 'li'))
              ),
      ));
//      }
    }
   
    $row = $this->_searchForm->getFieldsOptions('sitestore', 'category_id');
    if (!empty($row) && !empty($row->display)) {

			if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $onChangeEvent = "subcategoryies(this.value, '', '');";
        $categoryFiles = 'application/modules/Sitestore/views/scripts/_Subcategory.tpl';
      }
      else {
        $onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
        $categoryFiles = 'application/modules/Sitestore/views/sitemobile/scripts/_Subcategory.tpl';
      }

	    // prepare categories
	    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
	    if (count($categories) != 0) {
	      $categories_prepared[0] = "";
	      foreach ($categories as $category) {
	        $categories_prepared[$category->category_id] = $category->category_name;
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
              'label' => 'Only Stores With Photos',
              'order' => $row->order,
              'decorators' => array(
                      'ViewHelper',
                      array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
                      array('HtmlTag', array('tag' => 'li'))
              ),
      ));
    }
    $subform->addElement('Button', 'done', array(
            'label' => 'Search',
            'type' => 'submit',
						'onclick' => $this->gethasMobileMode() ? '' : 'searchSitestores();',
            'ignore' => true,
    ));

    $this->addSubForm($subform, $subform->getName());

    return $this;
  }

}
?>