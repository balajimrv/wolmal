<?php

class Timeline_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    //Add form elements for thumb Dimension

    $this->addElement('Text', 'cover_width', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_width', '1098'),
      'size' => 5,
      'style' => 'width: auto;',
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'label' => 'Width',
      'description' => 'px'
    ));

    $this->addElement('Text', 'cover_height', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_height', '250'),
      'size' => 5,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 10000))),
      'style' => 'width: auto;',
      'label' => 'Height',
      'description' => 'px'
    ));
    $this->cover_width->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->cover_height->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'span', 'class' => 'null', 'escape' => false));
    $this->addDisplayGroup(array('cover_width',
                                 'cover_height'),
                           'thumb_size'
                          );
    $this->thumb_size
          ->addDecorator('viewScript', array(
                                              'viewScript' => '_global_form.tpl',
                                              'placement'  => '',
                                              'data' => array('label' => $this->getView()->translate('Cover size for timeline'),
                                                              'description' => $this->getView()->translate('Enter size of cover for timeline.'))
                                              ));
    
    $this->addElement('Radio', 'cover_smaller_width', array(
      'label' => 'Covers with smaller width',
      'description' => 'Do you want to allow members upload covers with smaller width? Note, smaller images will be scaled with css to cover dimentions. Images may be stretched. We advice to keep this option off.',
      'multiOptions' => array(
        1 => 'Yes, do that.',
        0 => 'No, thanks.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_smaller_width', 0),
    ));

    $this->addElement('hidden', 'task', array('ignore' => true,
                                              'value' => 'save_settings'));
// Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}
