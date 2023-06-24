<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Form_Create extends Engine_Form {

  public function init() {
    $this->setTitle('Manage Form')
            ->setDescription('Here you can manage the form of the Form App for your Store. You can add questions to your form to gather relevant information from visitors to your Store. Responses of visitors on this form will be emailed to you.');
    $this->addElement('Checkbox', 'storeformactive', array(
        'label' => 'Activate Form for your Store. If activated, the form tab will be displayed on the profile of your Store with the questions configured by you.',
        'description' => 'Activate Form',
        'value' => 1,
    ));

    $this->addElement('Text', 'title', array(
        'label' => 'Form Title',
        'description' => 'Enter the title for the form on your Store.',
        'required' => true,
        'value' => 'Leave your Feedback',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Form Description',
        'description' => 'Enter the description for the form on your Store. This will appear below the Form title.',
        'allowEmpty' => false,
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Checkbox', 'activeyourname', array(
        'label' => "Enable the 'Your Name' field for the form. If enabled, this will be the first field of the form and will be pre-filled with the name of the logged-in visitor to your Store. It will be a compulsory field.",
        'description' => "Name Field",
        'value' => 1,
    ));

    $this->addElement('Checkbox', 'activeemail', array(
        'label' => 'Enable the "Your Email" field for the form. If enabled, this will be the second field of the form and will be pre-filled with the email of the logged-in visitor to your Store. It will be a compulsory field.',
        'description' => 'Email Field',
        'value' => 1,
    ));

    $this->addElement('Checkbox', 'activeemailself', array(
        'label' => 'Enable visitors to email a copy of the filled form to themselves. If enabled, a checkbox for this will appear at the end of the form.',
        'description' => 'Email to Self',
        'value' => 1,
    ));

    $this->addElement('Checkbox', 'activemessage', array(
        'label' => 'Enable the "Message" field for the form. If enabled, this will be the third field of the form. It will be a compulsory field.',
        'description' => 'Message Field',
        'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
    ));
  }

}
?>
