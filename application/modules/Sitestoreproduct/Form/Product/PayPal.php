<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PayPal.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Product_PayPal extends Engine_Form
{
  public function init()
  {
    parent::init();
    $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0)) {
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account Configuration')));
    }
    else {
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account for receiving payments from %s'), $siteTitle));   
    }
    
    $this->setName('sitestoreproduct_payment_info');
    
    $description = $this->getTranslator()->translate('SITESTOREPRODUCT_FORM_PRODUCT_PAYPAL_DESCRIPTION');
    $description = vsprintf($description, array(
        'https://www.paypal.com/signup/account',
        'https://www.paypal.com/webapps/customerprofile/summary.view',
        'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-api-access',
        'https://developer.paypal.com/docs/classic/api/apiCredentials/',
        'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
        'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'sitestoreproduct',
          'controller' => 'product',
          'action' => 'paymentInfo'
        ), 'default', true),
    ));
    $description = sprintf(Zend_Registry::get('Zend_Translate')->translate('Below, you can configure your Paypal Account to receive payments from %s. This information should be accurately provided and enabled to receive store payments from %s'), $siteTitle, $siteTitle) . ' <br/> ' . $description . '<div id="show_paypal_form_massges"></div>';
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
      'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    // Elements
    $this->addElement('Text', 'username', array(
      'label' => 'API Username',
      'filters' => array(
        'StripTags',
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
      'filters' => array(
        'StripTags',
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'signature', array(
      'label' => 'API Signature',
      'filters' => array(
        'StripTags',
        new Zend_Filter_StringTrim(),
      ),
    ));
    
    $showExtraInfo = true;
    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if( empty($isAdminDrivenStore) ) {
      $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if( empty($isPaymentToSiteEnable) ) {
        $showExtraInfo = false;
      }
    }
    
    if( !empty($showExtraInfo) ) {
        
      // Element: enabled
      $this->addElement('Radio', 'enabled', array(
        'label' => 'Enabled?',
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
          'value' => '0',
      ));

      // Element: execute
      $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
            'viewScript' => '_formSetDivAddress.tpl',
            'class' => 'form element')))
      ));

      $this->addDisplayGroup(array('submit'), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper',
          ),
      ));
    }
  }
}