<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemCreate.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Menu_ItemCreate extends Engine_Form {

  protected $_addType;

  public function getAddType() {
    return $this->_addType;
  }

  public function setAddType($addType) {
    $this->_addType = $addType;
    return $this;
  }

  protected $_menuName;

  public function getMenuName() {
    return $this->_menuName;
  }

  public function setMenuName($menuName) {
    $this->_menuName = $menuName;
    return $this;
  }

  public function init() {
    $this
            ->setTitle('Create ' . $this->_addType)
            ->setAttrib('class', 'global_form_popup')
    ;

    if ($this->_addType == 'Separator') {
      $this->setDescription('Create your custom separator to categories the dashboard menuitems. You can drag these separators up and down to change their order.');

      $this->addElement('Text', 'label', array(
          'label' => 'Label',
              //'required' => true,
              //'allowEmpty' => false,
      ));

      $this->addElement('hidden', 'isseparator', array(
          'value' => 'true',
          'order' => '1',
      ));
      $this->addElement('hidden', 'uri', array(
          'value' => 'Separator',
          'order' => '2',
      ));
    } else {
      $this->setDescription('Create your custom dashboard menuitem. You can drag these menuitems up and down to change their order');

      $this->addElement('Text', 'label', array(
          'label' => 'Label',
          'required' => true,
          'allowEmpty' => false,
      ));

      $this->addElement('Text', 'uri', array(
          'label' => 'URL',
          'required' => true,
          'allowEmpty' => false,
          'style' => 'width: 300px',
              //'validators' => array(
              //  array('NotEmpty', true),
              //)
      ));

      if ($this->getMenuName() == 'core_main') {
//        $this->addElement('Select', 'icon', array(
//            'label' => 'Icon',
//            'description' => 'Note: Not all menus support icons.',
//            'multiOptions' => array("none" => "",
//                "bars" => "Bars",
//                "edit" => "Edit",
//                "arrow-l" => "Arrow-l",
//                "arrow-r" => "Arrow-r",
//                "arrow-u" => "Arrow-u",
//                "arrow-d" => "Arrow-d",
//                "delete" => "Delete",
//                "plus" => "Plus",
//                "minus" => "Minus",
//                "check" => "Check",
//                "gear" => "Gear",
//                "refresh" => "Refresh",
//                "forward" => "Forward",
//                "back" => "Back",
//                "grid" => "Grid",
//                "start" => "Start",
//                "alert" => "Alert",
//                "info" => "Info",
//                "home" => "Home",
//                "search" => "Search",
//                "others" => "Others"),
//            'style' => 'width: 300px',
//            'onchange' => 'javascript:hide_Others(this.value)'
//        ));

        $this->addElement('Text', 'icon', array(
            'label' => 'Icon',
            'description' => 'Enter the URL of icon here. (NOTE: If you have not yet uploaded the icon, then you may upload this icon in the "Admin" > "Layout" > "File & Media Manager" section on your site and then click on "Copy URL" option to copy the url and this way you can use the generated url for this purpose.)',
        ));
      }

      $this->addElement('Checkbox', 'data_rel', array(
          'label' => 'Open in a dialog?',
          'checkedValue' => 'dialog',
          'uncheckedValue' => '',
      ));
    }//ENDIF

    $this->addElement('Checkbox', 'enable_mobile', array(
        'label' => 'Enable in Mobile?',
        'checkedValue' => '1',
        'uncheckedValue' => '0',
        'value' => '1',
    ));

    $this->addElement('Checkbox', 'enable_tablet', array(
        'label' => 'Enable in Tablet?',
        'checkedValue' => '1',
        'uncheckedValue' => '0',
        'value' => '1',
    ));

    if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {

      $this->addElement('Checkbox', 'enable_mobile_app', array(
          'label' => 'Enable in Mobile Application?',
          'checkedValue' => '1',
          'uncheckedValue' => '0',
          'value' => '1',
      ));

      $this->addElement('Checkbox', 'enable_tablet_app', array(
          'label' => 'Enable in Tablet Application?',
          'checkedValue' => '1',
          'uncheckedValue' => '0',
          'value' => '1',
      ));

    }


    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create ' . $this->_addType,
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}