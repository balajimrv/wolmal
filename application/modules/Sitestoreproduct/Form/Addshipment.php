<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addshipment.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Addshipment extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Order Shipping Details');
    
    $this->addElement('Text', 'service', array(
      'label' => 'Shipping Service',
      'description' => 'Enter name of the shipping service provider.',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Text', 'title', array(
      'label' => 'Additional Details',
      'description' => 'Enter additional details (if any) for this shipping service.',
//      'allowEmpty' => false,
//      'required' => true,
//      'validators' => array(
//        array('NotEmpty', true),
//        array('StringLength', false, array(1, 64)),
//      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Text', 'tracking_num', array(
      'label' => 'Tracking Number',
      'description' => 'Enter the tracking number of this order as provided by the shipping service.',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    $this->addElement('Textarea', 'note', array(
      'label' => 'Note',
      'description' => 'Enter note about the shipment of this order.',
    ));
    
    $this->addElement('select', 'status', array(
       'label' => 'Status', 
        'multiOptions' => array(
            1 => 'Active',
            2 => 'Completed',
            3 => 'Canceled'
        ),
        'value' => 1,
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }
}