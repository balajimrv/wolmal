<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Address extends Engine_Form {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    // custom sitestore fields
     
    if (!$this->_item) {
      $sitestore_item = new Sitestore_Model_Store(null);
      $this->setItem($sitestore_item);
    }
    parent::init();

    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.multiple.location', 1);

    if (!empty($multipleLocation)) {
			$this->addElement('Text', 'locationname', array(
					'label' => 'Location Title',
					'description' => 'Eg: Headquarter, Main Store',
          'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
					//'filters' => array('StripTags', new Engine_Filter_Censor()
			));
			$this->locationname->getDecorator('Description')->setOption('placement', 'append');
    }
    
    // LOCATION
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1)) {
      $this->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              )));
      $this->location->getDecorator('Description')->setOption('placement', 'append');
      $this->addElement('Hidden', 'locationParams', array( 'order' => 800000));
      
      
      include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';      
    }
    
    if (!empty($multipleLocation)) {
			$this->addElement('Checkbox', 'main_location', array(
					//'description' => 'Main Location',
					'label' => 'Associate this location with my store. (Note: If you select this option, then this location will display under the Info tab on your store profile.)',
					'value' => 0,
			));
		}
    
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {
			$this->addElement('Checkbox', 'product_location', array(
					'label' => 'Synchronize this location for all my store products. (Note: If you select this option, then location of all the products of this store will be edited by this location.)',
					'value' => 0,
          'onclick' => 'showUpdateWarning()',
			));
		}    
		
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Location',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }
}