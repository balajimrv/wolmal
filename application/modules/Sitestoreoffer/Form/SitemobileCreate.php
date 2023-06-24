<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Form_SitemobileCreate extends Engine_Form {

  public function init() {

    //GET STORE ID
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);

    //GET TAB ID
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GET VIEW
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET URL
    $url = $view->item('sitestore_store', $store_id)->getHref(array('tab' => $tab_id));

    $this->setTitle('Add a New Coupon')
            ->setAttrib('id', 'submit_form')
            ->setDescription("Enter your coupon's details below, click 'Preview' to view your coupon.");
    $this->addElement('text', 'title', array(
        'label' => 'Coupon Title',
        'required' => true,
        'maxlength' => 100,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100'))
        ),
    ));

    $this->addElement('textarea', 'description', array(
        'label' => 'Description',
        'description' => 'To make this coupon more relevant for users and to mention its terms and conditions, enter its description. Terms and conditions can contain information such as coupon code used by staff at the store etc.',
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'url', array(
        'label' => 'Coupon URL', 'style' => 'width:200px;',
        'description' => 'If your coupon is online or has a URL, then please enter it here.',
        'filters' => array(
            array('PregReplace', array('/\s*[a-zA-Z0-9]{2,5}:\/\//', '')),
        )
    ));

    $this->addElement('Text', 'coupon_code', array(
        'label' => 'Coupon Code',
        'description' => 'If your coupon requires a coupon code for redemption, then please add it here. (Note: Coupon code should be in between 4 and 16 characters in length and can contain alphabets, numbers, or a combination of both. Special characters other than hyphen (-) are not allowed.)',
        'maxlength' => 16,
        'validators' => array(
            array('StringLength', true, array(4, 16)),
            array('Regex', true, array('/^[a-zA-Z0-9-_ ]+$/')),
        ),
    ));
    $this->coupon_code->getValidator('Regex')->setMessage('Please enter valid coupon code.', 'regexNotMatch');

    $this->addElement('File', 'photo', array(
        'label' => 'Coupon Picture',
        'description' => "<span id='loading_image' style='display:none;'></span> ",
            //'onchange' => 'imageupload()',
    ));
    $this->photo->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $this->photo->addValidator('Extension', false, 'jpg,png,gif');

    $this->addElement('text', 'claim_count', array(
        'label' => 'Claims',
        'description' => 'Enter the maximum number of times this coupon can be claimed by members. (Enter 0 for unlimited. Note: You will not be able to edit claims once this coupon expires.)',
        'required' => true,
    ));

    $this->addElement('Radio', 'end_settings', array(
        'id' => 'end_settings',
        'label' => 'End Date',
        'description' => 'When will this coupon end?',
        'onclick' => "updateTextFields(this.value)",
        'multiOptions' => array(
            "0" => "Never. This coupon does not have an end date.",
            "1" => "This coupon ends on a specific date. (Select the date by clicking on the calendar icon below.)",
        ),
        'value' => 0
    ));
    $date = (string) date('Y-m-d');
    $this->addElement('CalendarDateTime', 'end_time', array(
        'value' => $date . ' 00:00:00',
    ));

    $this->addElement('Submit', 'execute', array(
        'label' => 'Post',
        //'onclick' => "showdetail(this)",
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));

    $this->addElement('Hidden', 'photo_id_filepath', array(
        'value' => 0,
        'order' => 854
    ));

    $this->addElement('Hidden', 'imageName', array(
        'order' => 992
    ));

    $this->addElement('Hidden', 'imageenable', array(
        'value' => 0,
        'order' => 991
    ));
  }

}

?>