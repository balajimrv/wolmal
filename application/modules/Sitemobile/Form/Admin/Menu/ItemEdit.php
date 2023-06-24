<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemEdit.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Menu_ItemEdit extends Sitemobile_Form_Admin_Menu_ItemCreate {

  protected $_addType;

  public function getAddType() {
    return $this->_addType;
  }

  public function setAddType($addType) {
    $this->_addType = $addType;
    return $this;
  }

  public function init() {
    parent::init();
    $this->setTitle('Edit ' . $this->_addType);
    $this->submit->setLabel('Edit ' . $this->_addType);
  }

}