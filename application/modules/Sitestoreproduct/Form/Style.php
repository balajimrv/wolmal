<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Style.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Style extends Engine_Form {

  public function init() {

    $this
            ->setTitle("Edit Product Style")
            ->setDescription("Edit the CSS style of your product using the text area below, and then click 'Save Style' to save changes.")
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Textarea', 'style', array(
        'label' => "Custom Advanced Product Style",
        'description' => "Add your own CSS code above to give your product a more personalized look.",
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Style',
        'type' => 'submit',
         'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formEditStyle.tpl',
                    'class' => 'form element'))),
    ));
  }

}