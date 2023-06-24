<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Photo_Edit extends Engine_Form {

  protected $_isArray = true;

  public function init() {

    $this->clearDecorators()
            ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
        'label' => 'Title',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div', 'class' => 'sitestores_editphotos_title_input')),
            array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => '')),
        ),
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Caption',
        'rows' => 2,
        'cols' => 120,
       'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div', 'class' => 'sitestores_editphotos_caption_input')),
            array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'sitestores_editphotos_caption_input')),
        ),
    ));

    $this->addElement('Checkbox', 'delete', array(
        'label' => "Delete Photo",
        'decorators' => array(
            'ViewHelper',
            array('Label', array('placement' => 'APPEND')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
        ),
    ));

    $this->addElement('Hidden', 'photo_id', array(
        'validators' => array(
            'Int',
        )
    ));
  }

}

?>