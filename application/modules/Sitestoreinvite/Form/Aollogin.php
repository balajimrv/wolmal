<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Aollogin.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_Form_Aollogin extends Engine_Form {

  public function init() {
    // Init form
    $this->setTitle('Aol Sign In');
    $this->loadDefaultDecorators();
    $email = Zend_Registry::get('Zend_Translate')->_('Username or Email');
    // Init email
    $this->addElement('Text', 'email', array(
        'label' => $email,
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
        'validators' => array(
            'EmailAddress'
        ),
        'tabindex' => 1,
    ));
    $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
    $password = Zend_Registry::get('Zend_Translate')->_('Password');
    // Init password
    $this->addElement('Password', 'password', array(
        'label' => $password,
        'required' => true,
        'allowEmpty' => false,
        'tabindex' => 2,
        'filters' => array(
            'StringTrim',
        ),
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Sign In',
        'type' => 'submit',
        'ignore' => true,
        'tabindex' => 5,
    ));
  }

}

?>