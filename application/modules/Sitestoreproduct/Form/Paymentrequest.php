<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentrequest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Paymentrequest extends Engine_Form {

  protected $_requestedAmount;
  protected $_totalAmount;
  protected $_remainingAmount;
  protected $_amounttobeRequested;

  public function setRequestedAmount($id) {
    $this->_requestedAmount = $id;
    return $this;
  }
  
  public function setTotalAmount($id) {
    $this->_totalAmount = $id;
    return $this;
  }
  
  public function setRemainingAmount($id) {
    $this->_remainingAmount = $id;
    return $this;
  }
  
  public function setAmounttobeRequested($id) {
    $this->_amounttobeRequested = $id;
    return $this;
  }
  
  public function init() {
    
    $this->setTitle('Payment Request')
         ->setDescription('');
    
    $this->setName('payment_request');
    
    $this->addElement('Text', 'total_amount', array(
        'value' => @round($this->_totalAmount, 2),
        'attribs' => array('disabled' => 'disabled'),
    ));
    
    $this->addElement('Text', 'amount_to_be_requested', array(
        'value' => @round($this->_amounttobeRequested, 2),
        'attribs' => array('disabled' => 'disabled'),
    ));
    
    $this->addElement('Text', 'remaining_amount', array(
        'value' => @round($this->_remainingAmount, 2),
        'attribs' => array('disabled' => 'disabled'),
    ));
    
    $this->addElement('Text', 'last_requested_amount', array(
    'attribs' => array('disabled' => 'disabled'),
    ));
    
    

    $this->addElement('text', 'amount', array(
        'description' => 'Unrequested amount will be added to the "Remaining Amount" of your store.',
        'value' => @round($this->_requestedAmount, 2),
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          array('Float', true),
          array('GreaterThan', false, array(0))
        ),
    ));
    $this->amount->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $this->addElement('Textarea', 'message', array(
        'label' => 'Message',
        'description' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
            
    $this->addElement('Button', 'submit', array(
        'label' => 'Send Request',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }
}