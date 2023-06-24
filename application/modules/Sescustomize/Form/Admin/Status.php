<?php

class Sescustomize_Form_Admin_Status extends Engine_Form {

  public function init() {
     $this->setTitle('Payment Approve/Disapprove')
            ->setDescription('');
    
    $values = array(
        'label' => 'Admin Note',
        'description' => '',
        'allowEmpty' => true,
        'required' => false,
    );
    $this->addElement('Textarea', "admin_note", $values);
    
     //Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'type' => 'submit',
        'ignore' => true
    ));

  }
}