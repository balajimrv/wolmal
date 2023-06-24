<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claim.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Claim extends Engine_Form {

  public function init() {

    $this->setTitle("Claim a Store")
            ->setDescription('Below, you can file a claim for a store on this community that you believe should be owned by you. Your request will be sent to the administration.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitestores_create');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('action' => 'terms'), 'sitestore_claimstores', true);
    
    $this->addElement('Text', 'title', array(
        'label' => 'Store Name',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Start typing the name of the store',
        'autocomplete' => 'off',
'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        ));

    Engine_Form::addDefaultDecorators($this->title);
    $this->title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    $this->addElement('Hidden', 'store_id', array());

    $this->addElement('Text', 'nickname', array(
        'label' => 'Your Name',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    $this->addElement('Text', 'email', array(
        'label' => 'Your Email',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));
    $this->email->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'about', array(
        'label' => 'About You and the Store',
        'required' => true,
        'allowEmpty' => false,
        'value' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'contactno', array(
        'label' => 'Contact Number',
        'description' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Textarea', 'usercomments', array(
        'label' => 'Comments',
        'description' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("I have read and agree to the <a href='javascript:void(0);' onclick=window.open('%s','mywindow','width=500,height=500')>terms of
service</a>."), $url);

    $this->addElement('Checkbox', 'terms', array(
        'label' => 'Terms of Service',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            'notEmpty',
            array('GreaterThan', false, array(0)),
        ),
        'value' => 0
    ));
    $this->terms->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
    $this->terms->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
            ->addDecorator('DivDivDivWrapper');

    $this->addElement('Button', 'send', array(
        'label' => 'Send',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'send',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>