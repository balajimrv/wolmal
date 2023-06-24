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
class Sitestoreproduct_Form_Review_Search extends Sitestoreproduct_Form_Searchfields {

  protected $_fieldType = 'sitestoreproduct_review';
  protected $_searchForm;

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setAction($view->url(array(), "sitestoreproduct_review_browse", true));

    parent::init();

    $order = 1;

    $this->addElement('Text', 'search', array(
        'label' => 'Search',
        'order' => $order++,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if ($viewer_id) {
      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => array('' => "Everyone's Reviews", 'friends_reviews' => "My Friends' Reviews", 'self_reviews' => "My Reviews", 'featured' => "Featured Reviews"),
          'order' => $order++,
      ));
    }

    $reviewTypeAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2);
    if ($reviewTypeAllowed == 3) {
      $elementType = 'Select';
      $elementValue = '';
    } elseif ($reviewTypeAllowed == 2) {
      $elementType = 'Hidden';
      $elementValue = 'user';
    } elseif ($reviewTypeAllowed == 1) {
      $elementType = 'Hidden';
      $elementValue = 'editor';
    }

    $this->addElement($elementType, 'type', array(
        'label' => 'Reviews Written By',
        'multiOptions' => array('' => 'Everyone', 'editor' => 'Editors', 'user' => 'Users'),
        'onchange' => "addReviewTypeOptions(this.value);",
        'order' => $order++,
        'value' => $elementValue,
    ));

    $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList(0);
    $categoriesMultiOptions = array();
    $categoriesMultiOptions[0] = '';
    foreach ($categories as $category) {
      $categoriesMultiOptions[$category->category_id] = Zend_Registry::get('Zend_Translate')->_($category->category_name);
    }

    $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'RegisterInArrayValidator' => false,
        'order' => $order++,
        'onchange' => 'showFields(this.value, 1); addOptions(this.value, "cat_dependency", "subcategory_id", 0);',
        'multiOptions' => $categoriesMultiOptions,
    ));

    $this->addElement('Select', 'subcategory_id', array(
        'RegisterInArrayValidator' => false,
        'order' => $order++,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/review/_browse_search_category.tpl',
                    'class' => 'form element')))
    ));

    $this->addElement('Hidden', 'categoryname', array(
        'order' => $order++,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
        'order' => $order++,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
        'order' => $order++,
    ));

    $this->getMemberTypeElement();

    $this->addElement('Select', 'order', array(
        'label' => 'Browse By',
        'order' => $order++ + 50000,
        'multiOptions' => array(
            'recent' => 'Most Recent',
            'rating_highest' => 'Highest Rating',
            'rating_lowest' => 'Lowest Rating',
            'helpfull_most' => 'Most Helpful',
            'replay_most' => 'Most Reply',
            'view_most' => 'Most Viewed'
        ),
    ));
    $this->addElement('Select', 'rating', array(
        'label' => 'Ratings',
        'order' => $order++ + 50000,
        'multiOptions' => array(
            '' => '',
            '5' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 5),
            '4' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 4),
            '3' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 3),
            '2' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 2),
            '1' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 1),
        ),
    ));

    $this->addElement('Checkbox', 'recommend', array(
        'label' => 'Only Recommended Reviews',
        'order' => $order++ + 50000,
    ));
    $this->addElement('Hidden', 'page', array(
        'value' => '1',
        'order' => $order++ + 50000,
    ));
    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'order' => $order++ + 50000,
        'type' => 'Submit',
        'ignore' => true,
    ));
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

}