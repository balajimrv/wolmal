<?php

abstract class Activityrewards_Form_Admin_Offer extends Engine_Form
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
  
  public function setItem($upearner) {

    $this->item_id->setValue($upearner->userpointearner_id);
    $this->title->setValue($upearner->userpointearner_title);
    $this->description->setValue($upearner->userpointearner_body);
    $this->cost->setValue($upearner->userpointearner_cost);
    $this->transact_state->setValue($upearner->userpointearner_transact_state);
    $this->enabled->setValue($upearner->userpointearner_enabled);
    $this->allow_comments->setValue($upearner->userpointearner_comments_allowed);
    
    $this->getElement('instock_track') ? $this->instock_track->setValue($upearner->userpointearner_instock_track) :0;
    $this->getElement('instock') ? $this->instock->setValue($upearner->userpointearner_instock) :0;
    $this->max_acts->setValue($upearner->userpointearner_max_acts);
    $this->rolloverperiod->setValue($upearner->userpointearner_rolloverperiod);

  }
  
  public function save($upearner)
  {

    $values = $this->values = $this->getValues();

    $upearner->userpointearner_title = $values['title'];
    $upearner->userpointearner_body = $values['description'];
    
    $upearner->userpointearner_cost = $values['cost'];
    $upearner->userpointearner_transact_state = $values['transact_state'];
    $upearner->userpointearner_enabled = $values['enabled'];
    $upearner->userpointearner_comments_allowed = $values['allow_comments'];
    
    $upearner->userpointearner_instock = !empty($values['instock']) ? $values['instock'] : 0;
    $upearner->userpointearner_instock_track = !empty($values['instock_track']) ? $values['instock_track'] : 0;
    $upearner->userpointearner_max_acts = $values['max_acts'];
    $upearner->userpointearner_rolloverperiod = $values['rolloverperiod'];

    
  }
  
  
}