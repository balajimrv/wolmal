<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PayPalAdaptive.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_PayPalAdaptive extends Engine_Form {

    public function init() {
        parent::init();

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Adaptive Account Configuration')));


        $this->setName('sitestoreproduct_payment_info_paypaladaptive');
        $adminGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
        $isTestMode = isset($adminGateway->config['test_mode']) && !empty($adminGateway->config['test_mode']);
        $description = $this->getTranslator()->translate('SITESTOREPRODUCT_FORM_PAYPALADAPTIVE_DESCRIPTION');
        $description = vsprintf($description, array(
            'https://www.paypal.com/signup/account',
            'https://www.paypal.com/webapps/customerprofile/summary.view',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-api-access',
            'https://developer.paypal.com/docs/classic/api/apiCredentials/',
            'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify',
            (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'sitestoreproduct',
                'controller' => 'product',
                'action' => 'paymentInfo'
                    ), 'default', true),
        ));
        $descriptionNote = "";
        $primary = 1;//Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.paypaladaptiveprimaryreceiver', 1);
        if ($primary == 1) {
            $username = "";
            if(isset($adminGateway->config['username'])){
                $username = $adminGateway->config['username'];
            }
            $descriptionNote = $this->getTranslator()->translate('SITESTOREPRODUCT_FORM_PAYPALADAPTIVE_DESCRIPTION_NOTE');
            $descriptionNote = vsprintf($descriptionNote, array($username));
        }
        $description = sprintf(Zend_Registry::get('Zend_Translate')->translate('Below, you can configure your Paypal Account to start receiving payments. This information should be accurately provided and enabled.'), $siteTitle) . ' <br/> ' . $description . ' <br/> ' . $descriptionNote . '<div id="show_paypaladaptive_form_massges"></div>';

        if ($isTestMode) {
            $description .='<br /><div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->translate('Please enter the sandbox credential.')) . '</span></div>';
        }
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'email', array(
            'label' => 'Paypal Email',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            ),
        ));
        $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
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
    }

}
