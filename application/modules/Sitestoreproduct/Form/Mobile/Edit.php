<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_Edit extends Sitestoreproduct_Form_Mobile_Create {

    public $_error = array();
    protected $_item;
    protected $_defaultProfileId;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {

        parent::init();
        $this->setTitle("Edit Product Info")
                ->setDescription("Edit your product below, and then click Save Settings to save changes.");

        if ($this->location)
            $this->removeElement('location');

        $this->addElement('Button', 'submit', array(
            'label' => 'Edit',
            'type' => 'submit',
            'order' => 999,
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formEditProduct.tpl',
                        'class' => 'form element'))),
        ));
    }

}
