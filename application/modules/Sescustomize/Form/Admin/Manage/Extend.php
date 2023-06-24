<?php
class Sescustomize_Form_Admin_Manage_Extend extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Deactivate Account')
      ->setDescription('After taking action on this member it will be will be deactivated automatically after completeing 5 years from joining date.?');


    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Deactivate',
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

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}