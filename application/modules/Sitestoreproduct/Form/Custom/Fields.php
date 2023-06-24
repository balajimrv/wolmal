<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Custom_Fields extends Fields_Form_Standard {

  public $_error = array();
  protected $_name = 'fields';
  protected $_elementsBelongTo = 'fields';

  public function init() {
    if (!$this->_item) {
      $sitestoreproduct_item = new Sitestoreproduct_Model_Sitestoreproduct(array());
      $this->setItem($sitestoreproduct_item);
    }
    parent::init();

    $this->removeElement('submit');
  }

  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this
              ->addDecorator('FormElements');
    }
  }

}