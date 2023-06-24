<?php

class Timeline_Form_Admin_IconUpload extends Engine_Form
{

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ->setAttrib('enctype', 'multipart/form-data')
      ;
    $iconsDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/timeline/';
    if( !is_dir($iconsDir) ) {
        if( !mkdir($iconsDir, 0777, true) ) {
            $this->view->message = 'Timeline temporary directory did not exist and could not be created.';
            return;
        }
    }
    $this->addElement('File', 'icon', array(
      'destination' => $iconsDir,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array('IsImage',
                            array('validator' => 'Count', 'options' => array(false, 1)),
                            array('validator' => 'Size', 'options' => array(false, 'max' => 2097152)),
                            array('validator' => 'ImageSize', 'options' => array(false, 'maxheight' => 65, 'maxwidth' => 65)),
                            array('validator' => 'Extension', 'options' => array(false, 'png'))
                           )
    ));
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Upload',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Hidden', 'content_id', array('required' => true,
                                                    'allowEmpty' => false
                                                   ));
    
    $this->submit->clearDecorators()
                 ->addDecorator('ViewHelper');
    $this->icon->clearDecorators()
               ->addDecorator('File');

  }


}
