<?php

class Sescustomize_Form_Redeem extends Engine_Form {

  public function init() {
    $fbValueUserTable =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize');
   $selectFb = $fbValueUserTable->select()->where('DATE_FORMAT(creation_date,"%Y-%m") =?',date('Y-m'))->where('user_id =?',Engine_Api::_()->user()->getViewer())->where('type =?','insert')->limit(1);
   $fbVal = $fbValueUserTable->fetchRow($selectFb);
    $description = "";
    if($_SESSION['totalEarn'] < 10000)
        $description = 'Minimum Balance EB required to apply for withdrawal as Encashment is 10000';

    $this->setTitle('Amount Reedem Form')
            ->setDescription($description);
    $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    
    $this->addElement('Text', 'balance_total', array(
        'label' => '',
        'description' => '<b style="font-weight:bold;">TOTAL BALANCE</b><br>Your Total Balance Left?', 
        'disabled'=>'disabled',       
    ));
    $this->balance_total->getDecorator('description')->setOption('escape', false);
    $this->addElement('Text', 'amount', array(
        'label' => '',
        'description' => '<b style="font-weight:bold;">REQUESTED AMOUNT</b><br>Amount Requested', 
        'required'=>true,
        'allowEmpty'=>false,
        'validators' => array(
          array('Int', true),
          array('GreaterThan', true, array(0)),
      ),
    ));
    $this->amount->getDecorator('description')->setOption('escape', false);
   $this->addElement('Textarea', 'note', array(
        'label' => 'Note',
        'description' => '', 
    ));
    $this->addElement('Text', 'bank_name', array(
        'label' => 'Bank Name',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
   
   
   $this->addElement('Text', 'ifsc_code', array(
        'label' => 'IFSC Code',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    $this->addElement('Text', 'account_number', array(
        'label' => 'Account Number',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    $this->addElement('Text', 'account_holder_name', array(
        'label' => 'Account Holder Name',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    $this->addElement('Text', 'monile_number', array(
        'label' => 'Mobile Number',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    
    $this->addElement('Text', 'pan_no', array(
        'label' => 'Pan Card No.',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    $this->addElement('Text', 'pan_name', array(
        'label' => 'Name on Pan Card',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    $this->addElement('Text', 'pan_dob', array(
        'label' => 'DOB on Pan Card',
        'required'=>true,
        'allowEmpty'=>false,
        'description' => '', 
    ));
    
    $viewer = Engine_Api::_()->user()->getViewer();
    if($viewer->level_id == 1){
      $this->addElement('Textarea', 'admin_note', array(
          'label' => 'Admin Note',
          'description' => '', 
      ));
    }
    

    if(empty($id)){
// Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }else{
     $this->addElement('Button', 'cancel_form', array(
        'label' => 'Cancel',
        'link' => true,
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));  
  }
  }

}
