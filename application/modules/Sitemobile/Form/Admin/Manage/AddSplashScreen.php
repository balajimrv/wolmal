<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddSplashScreen.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Manage_AddSplashScreen extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Splash screen')
            ->setDescription('Upload an image for the mobile splash screens and view its preview. Splash screen views only in iPhone and iPad. Splash screen appears during start-up, before loading of site. For mobiles that do not have splash screens, please add a Startup Image from the "Layout" >> "File & Media Manager" and then choose appropriate settings for "Startup Image" widget on "Startup Page".')
            ->setAttrib('class', 'global_form_popup')
    ;

    $this->addElement('select', 'key', array(
        'label' => 'Chose the type for this you want to add screen',
        'multioptions' => array(
            '320x460' => 'iPhone (320x460)',
            '640x920' => 'iPhone (Retina) (640x920)',
            '640x1096' => 'iPhone 5 (640x1096)',
            '768x1004' => 'iPad  portrait (768x1004)',
            '1024x748' => 'iPad landscape (1024x748)',
            '1536x2008' => 'iPad  (Retina) portrait (1536x2008)',
            '2048x1496' => 'iPad  (Retina) landscape (2048x1496)',
        ),
    ));
    $this->addElement('File', 'photo', array(
        'label' => 'Upload an image',
        'required' => true
    ));


//    $this->addElement('Text', 'title', array(
//        'label' => 'Title (This title is only for your indicative purpose, and will not be displayed to users.)',
//        'allowEmpty' => false,
//        'required' => true,
//        'autofocus' => 'autofocus',
//    ));
//    $this->addElement('Text', 'width', array(
//        'label' => 'Width',
//        'allowEmpty' => false,
//        'required' => true,
//        'autofocus' => 'autofocus',
//        'validators' => array(
//            array('Int', true)
//        ),
//    ));
//
//    $this->addElement('Text', 'height', array(
//        'label' => 'Height',
//        'allowEmpty' => false,
//        'required' => true,
//        'autofocus' => 'autofocus',
//        'validators' => array(
//            array('Int', true)
//        ),
//    ));
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Image',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
