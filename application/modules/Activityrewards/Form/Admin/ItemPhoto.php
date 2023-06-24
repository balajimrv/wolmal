<?php

class Activityrewards_Form_Admin_ItemPhoto extends Engine_Form
{

  public function init()
  {
    
    $this
      ->setTitle('Photo')
      ->setDescription('Photo');

    $this->addElement('hidden', 'item_id', array(
      'value' => '0'
    ));


    $this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true, // ?
    ));

    
  }
  
  
}