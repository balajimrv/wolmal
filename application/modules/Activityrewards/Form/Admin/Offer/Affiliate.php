<?php

class Activityrewards_Form_Admin_Offer_Affiliate extends Activityrewards_Form_Admin_Offer
{

  public function init()
  {
    
    parent::init();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this
      ->setTitle('Affiliate')
      ->setDescription('Affiliate');


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
      'value' => '',
    ));

    $this->addElement('Text', 'max_acts', array(
      'label' => 'Maximum Uses',
      'description' => 'Maximum number this item can be participated in, set 0 to disable. Also see rate limit.',
      'value' => '0',
    ));
    $this->max_acts->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'rolloverperiod', array(
      'label' => 'Rollover period',
      'description' => '',
      'value' => '0',
    ));
    $this->rolloverperiod->getDecorator('Description')->setOption('placement', 'append');





    $this->addElement('Select', 'transact_state', array(
      'label' => 'Points Added',
      'description' => 'Points Added',
      'multiOptions' =>   array(
              '0' => 'Immediately',
              '1' => 'Require action',
            ),
      'value' => '',
    ));
    $this->transact_state->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Textarea', 'affiliate_url', array(
      'label' => 'Affiliate URL',
      'class' => 'mceNoEditor',
      'description' => '(Available parameters: [userid], [username], [transactionid])'  // @todo - lang file -> activityrewards.csv
    ));
    $this->affiliate_url->getDecorator('Description')->setOption('placement', 'append');


    $levelMultiOptions = array();
    $level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	$levels = $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC'));
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
      'label' => 'Allow Comments',
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
  
  
  public function setItem($upearner) {
    
    parent::setItem($upearner);

    $meta = $upearner->getMetaData();
    
    $this->affiliate_url->setValue($meta['url']);

  }
  
  public function save($upearner)
  {
    
    parent::save($upearner);
    
    $meta = array();
    $meta['url'] = trim($this->values['affiliate_url']);

    // check regex http:// or freestyle url?
    
    $upearner->userpointearner_metadata = $meta;
    
    $this->saved_successfully = true;

  }
  
  
}