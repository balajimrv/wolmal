<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddDayItem.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Form_Admin_Settings_AddDayItem extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Add a Review of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Review from the auto-suggest Review field. The selected Review will be displayed as "Review of the Day" for this duration and if more than one reviews are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Review')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:300px;');

    // init to
    $this->addElement('Hidden', 'resource_id', array());

    $this->addElements(array(
        $label,
    ));

    $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
    $starttime->setLabel("Start Date");
    $starttime->setAllowEmpty(false);
    $starttime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($starttime);


    //Start End date work
    $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
    $endtime->setLabel("End Date");
    $endtime->setAllowEmpty(false);
    $endtime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($endtime);
    //End End date work
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Add Review',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        //'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitestore_general', true),
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
?>