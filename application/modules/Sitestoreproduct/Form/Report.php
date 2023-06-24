<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Report.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Report extends Engine_Form {
  
  protected $_store_id;
  protected $_store_name;
  
  public function setStoreId($id) {
    $this->_store_id = $id;
    return $this;
  }
  public function setStoreName($name) {
    $this->_store_name = $name;
    return $this;
  }
  
  public function init() {
    $this
        ->setAttrib('id', 'store_report_form')
        ->setAttrib('name', 'store_report_form')
        ->setTitle("Sales Reports")
        ->setDescription("Here, you may view performance report for the sales from your store. You can also view the performance of sales of any desired stores. Report can be viewed over multiple durations and time intervals. Reports can also be viewed for any desired order status. You can also export and save the report.");
   $this->setMethod('get');
    
    $this->addElement('Hidden', 'store_id', array(
          'value' => $this->_store_id
        ));
    
    // SELECT STORE
    $this->addElement('Select', 'select_store', array(
            'label' => 'Select Stores',
            'multiOptions' => array(
                    'current_store' => $this->_store_name,
                    'specific_store' => 'My Particular Stores',
                    'all' => 'My All Stores',
            ),
            'value' => 'current_store',
            'onchange' => 'return onstoreChange($(this))',
    ));

    $this->addElement('Text', 'store_name', array(
            'label' => 'Stores',
            'description' => 'Start typing the name of stores.',
            'filters' => array(
                        'StripTags',
                         new Engine_Filter_Censor(),
                        ),
            'autocomplete' => 'off'));
    Engine_Form::addDefaultDecorators($this->store_name);
    $this->store_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Hidden', 'store_ids', array(
            'required' => true,
            'allowEmpty' => false,
            'order' => 2,
            'validators' => array(
                    'NotEmpty'
            ),
            'filters' => array(
                    'HtmlEntities'
            ),
    ));
    Engine_Form::addDefaultDecorators($this->store_ids);
    
    
     // SELECT REPORT TYPE
    $this->addElement('Select', 'report_depend', array(
            'label' => 'Report Depends On',
            'multiOptions' => array(
                    'order' => 'Order',
                    'product' => 'Product',
            ),
            'value' => 'order',
            'onchange' => 'return onreportDependChange($(this))',
    ));
    
    
    // SELECT PRODUCT
    $this->addElement('Select', 'select_product', array(
            'label' => 'Select Products',
            'multiOptions' => array(
                    'all' => 'All',
                    'specific_product' => 'Specific Product',
            ),
            'value' => 'all',
            'onchange' => 'return onproductChange($(this))',
    ));
    
    $this->addElement('Text', 'product_name', array(
            'label' => 'Products',
            'description' => 'Start typing the name of the products.',
            'autocomplete' => 'off',
             'filters' => array(
                        'StripTags',
                         new Engine_Filter_Censor(),
                )   
            ));
    Engine_Form::addDefaultDecorators($this->product_name);
    $this->product_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
     $this->addElement('Hidden', 'product_ids', array(
            'required' => true,
            'allowEmpty' => false,
            'order' => 6,
            'validators' => array(
                    'NotEmpty'
            ),
            'filters' => array(
                    'HtmlEntities'
            ),
    ));
    Engine_Form::addDefaultDecorators($this->product_ids);
    
//   $this->addElement('Select', 'listing_based_on', array(
//            'label' => 'Listing Based On',
//            'multiOptions' => array(
//                    'date_wise' => 'Date',
//                    'store_wise' => 'Store',
//            ),
//            'value' => 'date_wise',
//    ));
    
    // Order Status
    $this->addElement('Select', 'order_status', array(
            'label' => 'Order Status',
            'multiOptions' => array(
                'all' => 'All',
                '0' => 'Approval Pending',
                '1' => 'Payment Pending',
                '2' => 'Processing',
                '3' => 'On Hold',
                '4' => 'Fraud',
                '5' => 'Completed',
                '6' => 'Canceled',
            ),
            'value' => 'all',

    ));

     // Init chunk
    $this->addElement('Select', 'time_summary', array(
            'label' => 'Time Summary',
            'multiOptions' => array(
                    'Monthly' => 'Monthly',
                    // 'Weekly' => 'Weekly',
                    'Daily' => 'Daily',
            ),
            'onchange' => 'return onChangeTime($(this))',
            'value' => 'Daily',

    ));
 
    $this->addElement('Select', 'month_start', array(
            'label' => '',
            'multiOptions' => array(
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December',
            ),
            'value' => '01',
            'decorators' => array(
                    'ViewHelper'),
    ));

    $this->addElement('Select', 'year_start', array(
          
            'multiOptions' => array(
            ),
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addDisplayGroup(array('month_start', 'year_start'), 'start_group');
    $button_group = $this->getDisplayGroup('start_group');
    $button_group->setDescription('From');
    $button_group->setDecorators(array(
            'FormElements',
            array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'start_group', 'style' => 'display:none;'))
    ));

    $this->addElement('Select', 'month_end', array(
            'multiOptions' => array(
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December',
            ),
            'value' => @date(m),
            'decorators' => array(
                    'ViewHelper'),
    ));

    $this->addElement('Select', 'year_end', array(
           
            'multiOptions' => array(
            ),
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addDisplayGroup(array('month_end', 'year_end'), 'end_group');
    $button_group = $this->getDisplayGroup('end_group');

    $button_group->setDescription(' To ');
    $button_group->setDecorators(array(
            'FormElements',
            array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'end_group', 'style' => 'display:none;'))
    ));

    $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
    $start_cal->setLabel("From");
    $start_cal->setValue(date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') , date('d') -1, date('Y'))));

    $this->addElement($start_cal);

    $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
    $end_cal->setLabel("To");
    $end_cal->setValue(date('Y-m-d H:i:s'));

    $this->addElement($end_cal);

    $this->addDisplayGroup(array('start_cal', 'end_cal'), 'grp2');
    $button_group = $this->getDisplayGroup('grp2');
    $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => 'width:100%;'))
    ));

    $this->addElement('Select', 'format_report', array(
            'label' => 'Format',
            'multiOptions' => array(
                    '0' => "Webpage (.html)",
                    '1' => "Excel (.xls)",
            ),
            'value' => '0',
            'onchange' => 'return onchangeFormat($(this))',
    ));

    // Init submit
    $this->addElement('Button', 'generate_report', array(
            'label' => 'Generate Report',
            'type' => 'submit',
    ));
  }

}