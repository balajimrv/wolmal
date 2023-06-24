<?php

class Timeline_Form_Settings extends Engine_Form
{
  public function init() {

    $this->setTitle("Profile Settings")
         ->setDescription("You can choose style for your profile layout.")
         ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    
    $this->addElement('Radio', 'profile_layout', array(
      'label' => "Profile Layout",
      'multiOptions' => array('timeline' => "Timeline profile.",
                              'classic' => "Classic profile."),
      'value' => 'timeline'
      )
    );
    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => "Save Changes",
      'type' => 'submit',
      'ignore' => true,
    ));
   
  }
}
