<?php

class Semods_Form_Admin_Settings extends Engine_Form
{

  public function init()
  {
    
    $this
      ->setTitle('General Settings')
      ->setDescription('General Settings');


    $this->addElement('Radio', 'statistics_disable', array(
      'label' => 'Module Collisions',
      'description' => "Would you like to automatically detect and help resolve any module collisions by sending anonymous statistics?",
      'multiOptions' => array(
        0 => 'Yes, enable module collision resolving.',
        1 => 'No, disable module collision resolving.',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semods.statistics.disable', 1)
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    
  }
  
  
  public function saveAdminSettings()
  {

    // Save settings
    Engine_Api::_()->getApi('settings', 'core')->semods = $this->getValues();

    $this->addNotice('Settings were successfully saved.');
    
  }
  
  
}