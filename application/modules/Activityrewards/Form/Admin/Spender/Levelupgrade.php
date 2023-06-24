<?php

class Activityrewards_Form_Admin_Spender_Levelupgrade extends Activityrewards_Form_Admin_Spender
{

  public function init()
  {
    
    parent::init();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this
      ->setTitle('Level Upgrade')
      ->setDescription('User can upgrade his user level by purchasing this item.');


    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'description' => '',
      'value' => '',
      'validators' => array(
        array('NotEmpty', true),
    )));

    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'description' => '',
      'value' => '',
      'editorOptions' => array('editor_deselector' => "mceNoEditor", 'theme_advanced_buttons2'  => 'bold')
    ));

    $this->addElement('Text', 'cost', array(
      'label' => 'Points',
      'description' => '',
      'value' => '0',
    ));


    $levelMultiOptions[0] = 'Any';
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach( $levels as $row )
    {
      $levelMultiOptions[$row->level_id] = $row->getTitle();
    }
    
    $this->addElement('Select', 'level_from', array(
      'label' => 'Level from',
      'description' => 'Select which level user can upgrade from. If a user is not on this level, the purchase will be denied.',
      'multiOptions' => $levelMultiOptions,
      //'value' => ''
    ));
    $this->level_from->getDecorator('Description')->setOption('placement', 'append');

    $levelMultiOptions = array();
    foreach( $levels as $row )
    {
      $levelMultiOptions[$row->level_id] = $row->getTitle();
    }
    
    $this->addElement('Select', 'level_to', array(
      'label' => 'Level to',
      'description' => 'Select which level user will be upgraded to.',
      'multiOptions' => $levelMultiOptions,
      //'value' => ''
    ));
    $this->level_to->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('hidden', 'max_acts', array(
      'value' => '0',
      'order' => '998'
    ));

    $this->addElement('hidden', 'rolloverperiod', array(
      'value' => '0',
      'order' => '997'
    ));



    $this->addElement('Select', 'appear_in_transactions', array(
      'label' => 'Show in transactions?',
      'description' => '',
      'multiOptions' =>   array(
              '0' => 'No',
              '1' => 'Yes',
            ),
      'value' => '1',
    ));
    $this->appear_in_transactions->getDecorator('Description')->setOption('placement', 'append');



    $this->addElement('hidden', 'transact_state', array(
      'value' => '0',
    ));


    $levelMultiOptions = array();
    
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach( $levels as $row )
    {
      $levelMultiOptions[$row->level_id] = $row->getTitle();
    }
    

    $this->addElement('MultiCheckbox', 'levels', array(
      'label' => 'Levels',
      'description' => 'Select which levels can see this offer.',
      'multiOptions' => $levelMultiOptions,
      //'value' => ''
    ));



    $this->addElement('Select', 'enabled', array(
      'label' => 'Enabled',
      //'description' => '',
      'multiOptions' =>   array(
              '0' => 'No',
              '1' => 'Yes',
            ),
      'value' => '1',
    ));

    $this->addElement('Select', 'allow_comments', array(
      'label' => 'Allow Comments / Likes',
      //'description' => '',
      'multiOptions' =>   array(
              '0' => 'No',
              '1' => 'Yes',
            ),
      'value' => '1',
    ));

    $this->addElement('Text', 'tags',array(
      'label'         =>  'Tags (Keywords)',
      'autocomplete'  => 'off',
      'description'   => 'Separate tags with commas.',
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");



    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true, // ?
    ));

    
  }
  
  
  public function setItem($upspender) {
    
    parent::setItem($upspender);
  
    $meta = $upspender->getMetaData();
    
    $this->level_from->setValue(Semods_Utils::g($meta,'level_from',0));
    $this->level_to->setValue(Semods_Utils::g($meta,'level_to',1));
    $this->appear_in_transactions->setValue($meta['t']);
  
  }
  
  public function save($upspender)
  {
    
    parent::save($upspender);
    
    $meta = array();
    $meta['t'] = intval($this->values['appear_in_transactions']);
    $meta['level_from'] = intval($this->values['level_from']);
    $meta['level_to'] = intval($this->values['level_to']);
  
    // check regex http:// or freestyle url?
    
    $upspender->userpointspender_metadata = $meta;
    
    $this->saved_successfully = true;
  
  }
  
  
}