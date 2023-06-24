<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Store.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Layout_Content_MobileStore extends Engine_Form {

  public function init() {

    $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'mobile-layout', 'module' => 'sitestore'), 'default', true))
            ->setAttrib('class', 'storelayout_layoutbox_menu_editinfo_form')
            ->setAttrib('id', 'storelayout_content_storeinfo')
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'ul'))
            ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('Form')
    ;

    $this->addElement('Text', 'description', array(
        'label' => 'Store Description <span>(meta tag)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'keywords', array(
        'label' => 'Store Keywords <span>(meta tag)</span>',
        'allowEmpty' => false,
        'validators' => array(
        ),
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Hidden', 'mobilecontentstore_id', array(
        'validators' => array(
            array('NotEmpty'),
            array('Int'),
        ),
    ));
  }

}

?>