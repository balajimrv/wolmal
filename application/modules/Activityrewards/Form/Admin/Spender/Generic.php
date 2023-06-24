<?php

class Activityrewards_Form_Admin_Spender_Generic extends Activityrewards_Form_Admin_Spender
{

  public function init()
  {
    
    parent::init();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this
      ->setTitle('Generic')
      ->setDescription('This is a generic item which can be used for selling items (such as T-Shirt, iPod, etc).');


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

    $this->addElement('Text', 'max_acts', array(
      'label' => 'Maximum Uses',
      'description' => 'Maximum number of times this item can be purchased during period specified below (e.g. 1 every day), set 0 to disable. See the period rate limit below (Rollover period).',
      'value' => '0',
    ));
    $this->max_acts->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'rolloverperiod', array(
      'label' => 'Rollover period',
      'description' => 'Duration of the limit above (days). Set 0 to make it an all time cap.',
      'value' => '0',
    ));
    $this->rolloverperiod->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Select', 'instock_track', array(
      'label' => 'Use In-Stock?',
      'description' => 'You can limit the amount of items you have available in-stock.',
      'multiOptions' =>   array(
              '0' => 'No',
              '1' => 'Yes',
            ),
      'value' => '0',
    ));
    $this->instock_track->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'instock', array(
      'label' => 'In Stock Quantity',
      'description' => 'If you have enabled the in-stock option, set the current in-stock amount.',
      'value' => '0',
    ));
    $this->instock->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Textarea', 'redirect_url', array(
      'label' => 'Redirect URL',
      'class' => 'mceNoEditor',
      'description' => '(Optional) Where the user will be redirected after paying for this item. Can be good for accessing special pages, etc'
    ));
    $this->redirect_url->getDecorator('Description')->setOption('placement', 'append');


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



    $this->addElement('Select', 'transact_state', array(
      'label' => 'Transaction state',
      'description' => 'If you would like to manually process or confirm this item, you can set the transaction state to Pending and process or confirm it later in the Transactions page.',
      'multiOptions' =>   array(
              '0' => '100016247',
              '1' => '100016248',
            ),
      'value' => '0',
    ));
    $this->transact_state->getDecorator('Description')->setOption('placement', 'append');


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


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true, // ?
    ));
    
  }
  
  
  public function setItem($upspender) {
    
    parent::setItem($upspender);
  
    $meta = $upspender->getMetaData();
    
    $this->redirect_url->setValue($meta['url']);
    $this->appear_in_transactions->setValue($meta['t']);
  
  }
  
  public function save($upspender)
  {
    
    parent::save($upspender);
    
    $meta = array();
    $meta['url'] = trim($this->values['redirect_url']);
    $meta['t'] = intval($this->values['appear_in_transactions']);
  
    // check regex http:// or freestyle url?
    
    $upspender->userpointspender_metadata = $meta;
    
    $this->saved_successfully = true;
  
  }
  
  
}