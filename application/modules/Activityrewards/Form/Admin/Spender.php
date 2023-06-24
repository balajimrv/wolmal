<?php

abstract class Activityrewards_Form_Admin_Spender extends Engine_Form
{
  public $saved_successfully = FALSE;
  
  protected $values;
  
  public function init() {

    // Zend_Form seems to have a bug with same order elements 
    $this->addElement('hidden', 'item_id', array(
      'value' => '0',
      'order' => 1999
    ));

    $this->addElement('hidden', 'offer_type', array(
      'value' => '0',
      'order' => 1998
    ));

    $this->addElement('hidden', 'newitem', array(
      'value' => '0',
      'order' => 1997
    ));
    
  }
  
  public function setItem($upspender) {

    $this->item_id->setValue($upspender->userpointspender_id);
    $this->title->setValue($upspender->userpointspender_title);
    $this->description->setValue($upspender->userpointspender_body);
    $this->cost->setValue($upspender->userpointspender_cost);
    $this->transact_state->setValue($upspender->userpointspender_transact_state);
    $this->enabled->setValue($upspender->userpointspender_enabled);
    $this->allow_comments->setValue($upspender->userpointspender_comments_allowed);
    
    $this->getElement('instock_track') ? $this->instock_track->setValue($upspender->userpointspender_instock_track) :0;
    $this->getElement('instock') ? $this->instock->setValue($upspender->userpointspender_instock) :0;
    $this->max_acts->setValue($upspender->userpointspender_max_acts);
    $this->rolloverperiod->setValue($upspender->userpointspender_rolloverperiod);

  }
  
  public function save($upspender)
  {
    
    // handle save for tags
    $values = $this->values = $this->getValues();

    $upspender->userpointspender_title = $values['title'];
    $upspender->userpointspender_body = $values['description'];
    
    $upspender->userpointspender_cost = $values['cost'];
    $upspender->userpointspender_transact_state = empty($values['transact_state']) ? 0 : $values['transact_state'];
    $upspender->userpointspender_enabled = $values['enabled'];
    $upspender->userpointspender_comments_allowed = $values['allow_comments'];
    
    $upspender->userpointspender_instock = !empty($values['instock']) ? $values['instock'] : 0;
    $upspender->userpointspender_instock_track = !empty($values['instock_track']) ? $values['instock_track'] : 0;
    $upspender->userpointspender_max_acts = $values['max_acts'];
    $upspender->userpointspender_rolloverperiod = $values['rolloverperiod'];

  }
  
  
}