<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Searchbox extends Engine_Form {

  protected $_widgetSettings;

  public function getSettings() {
    return $this->_params;
  }

  public function setWidgetSettings($widgetSettings) {
    $this->_widgetSettings = $widgetSettings;
    return $this;
  }

  public function init() {

    $this
            ->setAttribs(array(
                'method' => 'GET',
                'id' => 'searchBox'
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $this->setAction($view->url(array('action' => 'index'), "sitestoreproduct_general", true))->getDecorator('HtmlTag');

    if (!empty($this->_widgetSettings['formElements']) && in_array('textElement', $this->_widgetSettings['formElements'])) {
      $textWidth = $this->_widgetSettings['textWidth'];
      $this->addElement('Text', 'titleAjax', array(
          'label' => '',
          'placeholder' =>  $view->translate('Search...'),
          'autocomplete' => 'off',
          'style' => "width:$textWidth"."px;",
          'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
            )
          ));
    }

    $this->addElement('Hidden', 'product_id', array());

    if (!empty($this->_widgetSettings['formElements']) && in_array('categoryElement', $this->_widgetSettings['formElements']) && !empty($this->_widgetSettings['categoriesLevel'])) {
      $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategoriesHavingNoChield($this->_widgetSettings['categoriesLevel']);
      if (count($categories) != 0) {
        $categories_prepared[0] = "All Categories";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }
        $categoryWidth = $this->_widgetSettings['categoryWidth'];
        $this->addElement('Select', 'ajaxcategory_id', array(
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => $categories_prepared,
            'style' => "width:$categoryWidth"."px;",
        ));
        
        $this->addElement('Hidden', 'category_id', array(
            'order' => 497,
        ));        
        
        $this->addElement('Hidden', 'subcategory_id', array(
            'order' => 498,
        ));

        $this->addElement('Hidden', 'subsubcategory_id', array(
            'order' => 499,
        ));        
        
        $this->addElement('Hidden', 'categoryname', array(
            'order' => 500,
        ));

        $this->addElement('Hidden', 'subcategoryname', array(
            'order' => 501,
        ));

        $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => 502,
        ));        
      }
    }

    if (!empty($this->_widgetSettings['formElements']) && in_array('linkElement', $this->_widgetSettings['formElements'])) {

      $this->addElement('Button', 'submitButton', array(
          'label' => 'Search',
          //'type' => 'submit',
          'ignore' => true,
          'onClick' => 'doSearching()',
          'decorators' => array(
              'ViewHelper',
          ),
      ));      
      
      $url = $view->url(array('action' => 'index'), "sitestoreproduct_general");
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'Advanced',
          'link' => true,
          'title' => 'Advanced Search',
          'href' => $url,
          'prependText' => '  ',
          'decorators' => array(
              'ViewHelper',
          ),
      ));

      $this->addDisplayGroup(array('submitButton', 'cancel'), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper',
          ),
      ));
    }
    else {
      $this->addElement('Button', 'submitButton', array(
          'label' => 'Search',
          //'type' => 'submit',
          'onClick' => 'doSearching()',
          'ignore' => true,
      ));      
    }
  }

}