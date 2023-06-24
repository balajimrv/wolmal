<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PayPalAdaptive.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_PayPalAdaptive extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('PayPal Adaptive Account Configuration');

        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_ADMIN_GATEWAY_PAYPALADAPTIVE_DESCRIPTION');
        $description = vsprintf($description, array(
            'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
            'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#register',
            'https://www.youtube.com/watch?v=DwStDBkeJwk'
         ));
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);


        // Elements
        $this->addElement('Text', 'email', array(
            'label' => 'Paypal Email',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        
        // Elements
        $this->addElement('Text', 'username', array(
            'label' => 'API Username',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'password', array(
            'label' => 'API Password',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'signature', array(
            'label' => 'API Signature',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        
        $this->addElement('Text', 'application_id', array(
            'label' => 'Application id',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $testModeText = 'Note: If you have selected the test mode option, then please ensure that you have entered correct sandbox credentials.';

        $this->addElement('Checkbox', 'test_mode', array(
            'label' => "Enable Test Mode. [" . $testModeText . "]",
            'value' => 1
        ));
    }

}
