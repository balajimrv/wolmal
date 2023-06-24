<?php

class Activitypoints_Form_Admin_Settings extends Engine_Form
{
  public $saved_successfully = FALSE;

  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('Global Activity Points Settings.');



    $this->addElement('Radio', 'enable_topusers', array(
      'label' => '100016111',
      'description' => '100016112',
      'multiOptions' => array(
        1 => '100016113',
        0 => '100016114',
      ),
      'value' => Semods_Utils::getSetting('activitypoints.enable_topusers',0)
    ));

    $this->addElement('Radio', 'access_topusers', array(
      'label' => 'Public access',
      'description' => 'Allow non-logged in users to view the Top Users page?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => Semods_Utils::getSetting('activitypoints.access_topusers',1)
    ));


    $this->addElement('Radio', 'topusers_rankby', array(
      'label' => 'Order Top Users',
      'description' => 'Top Users can be calculated based on Total Points Earned or Current Points Balance. "Total Points Earned" counter only increases based on what a member earns, it is NOT changed when members send each other points. "Current Points Balance" is like a bank balance - it increases based on what a member earns and decreases if member spends points, it is changed when members send each other points.',
      'multiOptions' => array(
        0 => 'Order by Total Points Earned',
        1 => 'Order by Current Points Balance',
      ),
      'value' => Semods_Utils::getSetting('activitypoints.topusers_rankby',0),
    ));

    $this->addElement('text', 'max_topusers', array(
      'label' => 'Number of members to show',
      'description' => 'Maximum number of members to show on the Top Users page.',
      'value' => Semods_Utils::getSetting('activitypoints.max_topusers',10),
    ));
    
    
    $topusers_exclude = Semods_Utils::getSetting('activitypoints.topusers_exclude','');
    $topusers_exclude = empty($topusers_exclude) ? array() : explode(',',$topusers_exclude);

    $field = new Semods_Form_Element_MultiText('topusers_exclude');
    $field->setLabel('Exclude from Top Users')
      ->setDescription("Exclude the following members from Top Users list. Enter usernames.")
      ->setValue($topusers_exclude)
      ->setAttrib('min',2);

    $this->addElement($field);


    $this->addElement('Radio', 'enable_statistics', array(
      'label' => '100016124',
      'description' => '100016125',
      'multiOptions' => array(
        1 => '100016126',
        0 => '100016127',
      ),
      'value' => Semods_Utils::getSetting('activitypoints.enable_statistics',1),
    ));


    $this->addElement('Radio', 'enable_microtransactions', array(
      'label' => 'Enable Micro Transactions?',
      'description' => 'Enable this if you would like to see ALL the points micro activities (such as creating group, creating event, etc) logged in the transactions. Note: This will increase database load.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => Semods_Utils::getSetting('activitypoints.enable_microtransactions',0),
    ));



    
    if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards')) {
       
      $this->addElement('Radio', 'enable_offers', array(
        'label' => '100016116',
        'description' => '100016117',
        'multiOptions' => array(
          1 => '100016118',
          0 => '100016119',
        ),
        'value' => Semods_Utils::getSetting('activityrewards.enable_offers',1),
      ));
  
      $this->addElement('Radio', 'enable_shop', array(
        'label' => '100016120',
        'description' => '100016121',
        'multiOptions' => array(
          1 => '100016122',
          0 => '100016123',
        ),
        'value' => Semods_Utils::getSetting('activityrewards.enable_shop',1),
      ));
      
    }


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    
  }
  
  
  public function saveAdminSettings()
  {

    Semods_Utils::setSetting('activitypoints.enable_topusers', (int)$this->getElement('enable_topusers')->getValue());
    Semods_Utils::setSetting('activitypoints.access_topusers', (int)$this->getElement('access_topusers')->getValue());
    Semods_Utils::setSetting('activitypoints.topusers_rankby', (int)$this->getElement('topusers_rankby')->getValue());
    Semods_Utils::setSetting('activitypoints.max_topusers', (int)$this->getElement('max_topusers')->getValue());
    Semods_Utils::setSetting('activitypoints.enable_statistics', (int)$this->getElement('enable_statistics')->getValue());
    Semods_Utils::setSetting('activitypoints.enable_microtransactions', (int)$this->getElement('enable_microtransactions')->getValue());
    

    if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('activityrewards')) {
      Semods_Utils::setSetting('activityrewards.enable_offers', (int)$this->getElement('enable_offers')->getValue());
      Semods_Utils::setSetting('activityrewards.enable_shop', (int)$this->getElement('enable_shop')->getValue());
    }

    $value = $this->getElement('topusers_exclude')->getValue();
    $value = $this->remove_array_empty_values( $value );
    $this->getElement('topusers_exclude')->setValue($value);
    
    $value = implode(',',  $value );
    
    Semods_Utils::setSetting('activitypoints.topusers_exclude', $value);
    

    $this->addNotice("Settings were saved successfully.");

    $this->saved_successfully = true;

  }


  function remove_array_empty_values($array, $remove_null_number = true) {
    $new_array = array();

    $null_exceptions = array();

    foreach ($array as $key => $value) {
      $value = trim($value);

      if($remove_null_number)
        $null_exceptions[] = '0';

      if(!in_array($value, $null_exceptions) && $value != "")
        $new_array[] = $value;
    }

    return $new_array;
  }

  
}