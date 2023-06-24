<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Package_Edit extends Sitestore_Form_Admin_Package_Create {

  public function init() {
    parent::init();

    $this
            ->setTitle('Edit Store Package')
            ->setDescription('Edit your store package over here. Below, you can configure various settings for this package like tell a friend, overview, map, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.')
    ;

    // Disable some elements
    $this->getElement('price')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true)
    ;
    
    $this->getElement('recurrence')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true)
    ;

    $this->getElement('duration')
            ->setIgnore(true)
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true)
    ;
    $this->removeElement('trial_duration');

    // Change the submit label
    $this->getElement('execute')->setLabel('Edit Package');
  }

}

?>