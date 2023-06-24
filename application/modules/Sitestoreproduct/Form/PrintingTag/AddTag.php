<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddTag.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_PrintingTag_AddTag extends Engine_Form {

  public function init() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $store_id = $request->getParam('store_id', null);

    $this->setTitle('Add New Printing Tag');
    $this->setName('create_printing_tag');
    $this->setDescription("Create a unique Printing Tag for your products by filling the information below.");
//    $this->setAttrib('style', 'padding-bottom:30px;');

    $this->addElement('Text', 'tag_name', array(
        'label' => 'Tag Title',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->addElement('Text', 'width', array(
        'label' => 'Width (cm)',
        'description' => '<span id="div_width_message" class="description" style="display:none;">Choose a value between 1cm and 16cm.</span>',
        'allowEmpty' => false,
        'maxlength' => 6,
        'onBlur' => 'previewSize();',
        'onkeyup' => 'previewSize();',
        'validators' => array(
            array('NotEmpty', true),
            array('Float', true),
            array('Between', false, array('min' => '1','max' => '16','inclusive' => true)),
        ),
        'attribs' => array('style' => 'width:50px;'),
        'value' => 9.0,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            )));
    $this->width->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Text', 'height', array(
        'label' => 'Height (cm)',
        'description' => '<span id="div_height_message" class="description" style="display:none;">Choose a value between 1cm and 25cm.</span>',
        'allowEmpty' => false,
        'maxlength' => 6,
        'attribs' => array('style' => 'width:50px;'),
        'onBlur' => 'previewSize();',
        'onkeyup' => 'previewSize();',
        'validators' => array(
            array('NotEmpty', true),
            array('Float', true),
            array('Between', false, array('min' => '1','max' => '25','inclusive' => true)),
        ),
        'value' => 4.0,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            )));
    $this->height->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $allowedDetails['title'] = 'Title <span class="editfont_icon"><a class="seaocore_icon sitestoreproduct_editfont_icon" href="javascript:void(0)" onClick="editStyle(\'title\')" title="Change Title - Font Family, Size and Colors"></a></span>';
    $allowedDetails['category'] = 'Category <span class="editfont_icon"><a class="seaocore_icon sitestoreproduct_editfont_icon" href="javascript:void(0)" onClick="editStyle(\'category\')" title="Change Category - Font Family, Size and Colors"></a></span>';    
    $allowedDetails['price'] = 'Price <span class="editfont_icon"><a class="seaocore_icon sitestoreproduct_editfont_icon" href="javascript:void(0)" onClick="editStyle(\'price\')" title="Change Price - Font Family, Size and Colors"></a></span>';
    $allowedDetails['qr'] = 'QR Code <span class="editfont_icon"><a class="seaocore_icon sitestoreproduct_editfont_icon" href="javascript:void(0)" onClick="editStyle(\'qr\')" title="Change QR Code - Font Family, Size and Colors"></a></span>';
    $this->addElement('MultiCheckbox', 'details', array(
        'label' => 'Product Fields',
        'description' => "Select the given Product Fields that you want to be the part of your printing tag.",
        'RegisterInArrayValidator' => false,
        'multiOptions' => $allowedDetails,
        'allowEmpty' => false,
        'onclick' => 'checkDetail();',
        'value' => array('title', 'category', 'price', 'qr'),
        'escape' => false,
    ));

    $this->addElement('Dummy', 'dummy_communityad_title', array(
        'label' => 'Configuration Panel',
        'description' => "Drag and drop your selected Product Fields to generate a unique printing tag in the configuration panel below:",
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_printingTagPreview.tpl',
                    'class' => 'form element'
            ))),
    ));
    
//    $this->addElement('Text', 'font_settings', array('value' => 'Hello', 'order' => 876));
    
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formTagSubmit.tpl',
                    'class' => 'form element'))),
    ));
  }

}