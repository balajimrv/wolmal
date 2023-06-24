<?php
class Sescustomize_Form_Admin_Manage_Invitation extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Enter the Member Id')
      ->setDescription('Please put the member id to add manually.');

    $this->addElement('text', 'member_id', array(
        'label' => 'Member Id',
        'allowEmpty' => false,
        'required' => true,
        'value' => '',
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
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