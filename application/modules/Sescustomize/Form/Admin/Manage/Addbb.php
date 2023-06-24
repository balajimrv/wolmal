<?php
class Sescustomize_Form_Admin_Manage_Addbb extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Enter BB')
      ->setDescription('Please put the BB to add manually.');

    $this->addElement('text', 'buyer_bb', array(
        'label' => 'Business Bridges',
        'allowEmpty' => false,
        'required' => true,
        'value' => '',
        'validators' => array(
           array('NotEmpty', true),
        )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}