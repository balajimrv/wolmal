<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Compare_Settings extends Engine_Form {

  public function init() {

    $this->setTitle('Comparison Settings');

    $request = Zend_Controller_Front::getInstance()->getRequest();

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.compare', 1))) {
      $this->addElement('Dummy', 'note', array(
          'description' => '<div class="tip"><span>' . Zend_Registry::get('Zend_Translate')->_("You have not allowed comparison of products. Please go to the 'Global Settings' section of this plugin to allow the comparison.") . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
              ))
      ));
    } else {
      $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList(0);
      $categoriesCount = count($categories);

      if (empty($categoriesCount)) {

        $this->addElement('Dummy', 'note', array(
            'description' => '<div class="tip"><span>' . Zend_Registry::get('Zend_Translate')->_("You have not yet created any category for '$productTypesName' yet. Please create some categories for this product from 'Categories' section, to configure its comparison settings.") . '</span></div>',
            'decorators' => array(
                'ViewHelper', array(
                    'description', array('placement' => 'APPEND', 'escape' => false)
                ))
        ));
      } else {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', 0);

        $this->addElement('Select', 'category_id', array(
            'label' => "Category",
            'value' => $category_id,
            'onchange' => 'changeCategory(this)',
        ));

        $this->addElement('Select', 'subcategory_id', array(
            'label' => "Sub-category",
            'value' => 1,
            'onchange' => 'changeCategory(this)',
        ));

        $this->addElement('Select', 'subsubcategory_id', array(
            'label' => "3rd Level Category",
            'value' => 1,
            'onchange' => 'changeCategory(this)',
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3) {
          $this->addElement('Checkbox', 'editor_rating', array(
              'label' => "Show Editor Rating.",
              'description' => "Editor Rating",
              'value' => 1,
              'onchange' => 'toggleEditorParm(this)',
          ));
        } else {
          $this->addElement('hidden', 'editor_rating', array(
              'value' => 0,
          ));
        }
        $this->addElement('MultiCheckbox', 'editor_rating_fields', array(
            'label' => "Editor Rating Parameters",
            'description' => 'Choose the rating parameters (rated by editors) from below that you want to display under the "Editor Ratings" section on products comparison page.',
            'multiOptions' => array()
        ));
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3) {
          $this->addElement('Checkbox', 'user_rating', array(
              'label' => "Show User Ratings.",
              'description' => "User Ratings",
              'value' => 1,
              'onchange' => 'toggleUserParm(this)',
          ));
        } else {
          $this->addElement('hidden', 'user_rating', array(
              'value' => 0,
          ));
        }
        $this->addElement('MultiCheckbox', 'user_rating_fields', array(
            'label' => "User Rating Parameters",
            'description' => 'Choose the rating parameters (rated by users) from below that you want to display under the "User Ratings" section on products comparison page.',
            'multiOptions' => array()
        ));
        $this->addElement('Dummy', 'field_dummy_1', array(
            'label' => "Product Information",
            'description' => 'Choose the options from below that you want to display under the "Information" section on products comparison page.)'
        ));
        $this->addElement('Checkbox', 'tags', array(
            'label' => "Tags",
            'value' => 1,
        ));

        $this->addElement('Checkbox', 'price', array(
            'label' => "Price",
            'value' => 1,
        ));

        $this->addElement('Dummy', 'field_dummy_3', array(
            'label' => "",
        ));

        $this->addElement('MultiCheckbox', 'custom_fields', array(
            'multiOptions' => array()
        ));

        $this->addElement('Dummy', 'field_dummy_2', array(
            'label' => "Product Statistics",
            'description' => 'Choose the options from below that you want to be display under the "Statistics" section on products comparison page.)'
        ));
        $this->addElement('Checkbox', 'views', array(
            'label' => "Total Views",
            'value' => 1,
        ));
        $this->addElement('Checkbox', 'comments', array(
            'label' => "Total Comments",
            'value' => 1,
        ));
        $this->addElement('Checkbox', 'likes', array(
            'label' => "Total Likes",
            'value' => 1,
        ));
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) > 1) {
          $this->addElement('Checkbox', 'reviews', array(
              'label' => "Total Reviews",
              'value' => 1,
          ));
        } else {
          $this->addElement('hidden', 'reviews', array(
              'value' => 0,
          ));
        }
        $this->addElement('Checkbox', 'summary', array(
            'label' => "Show description of products.",
            'description' => 'Product Summary',
            'value' => 1,
        ));
        $this->addElement('Checkbox', 'enabled', array(
            'label' => "Yes, enable comparison of products.",
            'description' => 'Enable Comparison',
            'value' => 1,
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));
      }
    }
  }

}