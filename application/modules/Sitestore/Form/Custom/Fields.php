<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Custom_Fields extends Fields_Form_Standard {

  public $_error = array();
  protected $_name = 'fields';
  protected $_elementsBelongTo = 'fields';

  public function init() {
    global $sitestore_custom_field;
    if (!empty($sitestore_custom_field)) {
      // custom sitestore fields
      if (!$this->_item) {
        $sitestore_item = new Sitestore_Model_Store(array());
        $this->setItem($sitestore_item);
      }
      parent::init();

      $this->removeElement('submit');
    } else {
      exit();
    }
  }

  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('FormElements');
    }
  }

}

?>