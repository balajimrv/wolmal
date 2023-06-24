<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Combination.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_Combination extends Sitestoreproduct_Form_Combination {

    public function init() {
        parent::init();

        $this->setAttrib("data-ajax", "false");

        // cancel
        $this->removeDisplayGroup('buttons');
        $this->removeElement('cancel');
        $cancelHref = Zend_Registry::get("Zend_View")->url(array(
            'module' => 'sitestoreproduct',
            'controller' => 'siteform',
            'action' => 'product-category-attributes-mobile',
            'product_id' => $this->_product_id,
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
        $this->addDisplayGroup(array('submit', 'cancel_mobile'), 'buttons', array(
            'order' => 10002,
        ));
    }

}
