<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Style.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Style extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Edit Store Style')
            ->setDescription('Edit the CSS style of your store using the text area below, and then click "Save Style" to save changes.')
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'editstyle');

    // Element: style
    $this->addElement('Textarea', 'style', array(
        'label' => 'Custom Advanced Store Style',
        'description' => 'Add your own CSS code above to give your store a more personalized look.',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');
    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Style',
        'type' => 'submit',
    ));
  }

}

?>