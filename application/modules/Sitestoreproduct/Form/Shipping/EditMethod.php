<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditMethod.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Shipping_EditMethod extends Sitestoreproduct_Form_Shipping_AddMethod {

  protected $_location;
  protected $_country;

  public function getLocation() {
    return $this->_location;
  }

  public function setLocation($id) {
    $this->_location = $id;
    return $this;
  }

  public function getCountry() {
    return $this->_country;
  }

  public function setCountry($id) {
    $this->_country = $id;
    return $this;
  }

  public function init() {

    parent::init();

    $this->setTitle('Edit Shipping Method');
    $this->setDescription('Edit shipping method, method will be show to buyer according to the saved values. Shipping mrthods will show to buyer accordingly.');

    $this->addElement('Select', 'country', array(
        'label' => 'Country',
        // 'required' => false,
        'multiOptions' => array($this->_country),
        'attribs' => array('disabled' => 'disabled'),
        'order' => 2,
        'value' => 0
    ));

    if (!strstr($this->_country, "All")) {
      $this->addElement('Select', 'state', array(
          'multiOptions' => array($this->_location),
          'label' => 'Region / State',
          'attribs' => array('disabled' => 'disabled'),
          'order' => 3,
          'value' => 0
      ));
    }else {
      $this->removeElement("state");
    }
    $this->removeElement("all_regions");
    $this->submit->setLabel('Save Changes');
  }
}