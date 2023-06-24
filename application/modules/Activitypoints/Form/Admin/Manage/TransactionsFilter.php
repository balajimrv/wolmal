<?php

class Activitypoints_Form_Admin_Manage_TransactionsFilter extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    $f_title = new Zend_Form_Element_Text('f_title');
    $f_title
      ->setLabel('Description')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $earnerTypes = array();
    $rows = Engine_Api::_()->getDbTable('earnertype','activitypoints')->fetchAll("userpointearnertype_type >= 100");
    foreach($rows as $row) {
      $earnerTypes['1_'.$row->userpointearnertype_type] = $row->userpointearnertype_typename;
    }

    $spenderTypes = array();
    $rows = Engine_Api::_()->getDbTable('spendertype','activitypoints')->fetchAll("userpointspendertype_type >= 100");
    foreach($rows as $row) {
      $spenderTypes['2_'.$row->userpointspendertype_type] = $row->userpointspendertype_typename;
    }
    
    $typeMultiOptions = array( -1                => 'All',
                                0                => 'Points Tranfer',
                                1                => 'Micro Transaction',
                                );

    if(!empty($earnerTypes)) {
      $typeMultiOptions['Earning Points'] = $earnerTypes;
    }

    if(!empty($spenderTypes)) {
      $typeMultiOptions['Points Shop'] = $spenderTypes;
    }

    $f_type = new Zend_Form_Element_Select('f_type');
    $f_type
      ->setLabel('Type')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions( $typeMultiOptions );

    $transactionMultiOptions = array(-1 => 'All',
                                      0 => 'Completed',
                                      1 => 'Pending',
                                      2 => 'Cancelled',
                                      );

    $f_state = new Zend_Form_Element_Select('f_state');
    $f_state
      ->setLabel('Transaction status')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($transactionMultiOptions);


    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'id', array(
      'value' => 0,
    ));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    
    $this->addElements(array(
      $f_title,
      $f_type,
      $f_state,
      $submit,
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}