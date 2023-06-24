<?php

class Sescustomize_Form_Admin_Global extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');
    
    $values = array(
        'label' => 'Enter Bridges Value',
        'description' => $descriptionLicense,
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sescustomize.bridges.value', 10),
    );
    
    $day = date('d');
   // if($day >= 25){
       $nextMonth = date('m-Y',strtotime('-1 Months',time()));
//    }else{
    //   $nextMonth = date('m-Y',time());
  //  }
    
    $getResult = Engine_Api::_()->sescustomize()->getValue($nextMonth);
    if($getResult)
      $values = array_merge(array('disabled'=>'true'),$values);    
    $this->addElement('Text', "sescustomize_bridges_value", $values);
    $this->getElement('sescustomize_bridges_value')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    //Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
