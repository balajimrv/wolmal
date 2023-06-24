<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Field.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_Field extends Sitestoreproduct_Form_Field {

    protected $_productId;

    public function setProductId($value) {
        $this->_productId = $value;
    }

    public function init() {
        parent::init();

        $this->setAttrib("id", "configurable_field_form");
        $this->setAttrib("data-ajax", "false");
        $this->type->setAttrib("onchange", "document.getElementById('buttons-wrapper').style.display='none';document.getElementById('configurable_field_form').method='get';document.getElementById('configurable_field_form').submit();");

        // cancel
        $this->removeDisplayGroup('buttons');
        $this->removeElement('cancel');
        $cancelHref = Zend_Registry::get("Zend_View")->url(array(
            'module' => 'sitestoreproduct',
            'controller' => 'siteform',
            'action' => 'index-mobile',
            'product_id' => $this->_productId,
            'option_id' => Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($this->_productId),
                ), 'default', true
        );
        $this->addElement('Button', 'cancel_mobile', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => "window.location.href='{$cancelHref}';return false;",
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 10001,
            'ignore' => true,
        ));
        $this->addDisplayGroup(array('execute', 'cancel_mobile'), 'buttons', array(
            'order' => 10002,
        ));
    }

}
