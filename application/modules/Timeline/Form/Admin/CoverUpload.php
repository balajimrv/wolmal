<?php

class Timeline_Form_Admin_CoverUpload extends Engine_Form
{

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setTitle('Upload Default Cover')
      ;
    $tmp_dest = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/timeline/';
    if( !is_dir($tmp_dest) ) {
        if( !mkdir($tmp_dest, 0777, true) ) {
            throw new Engine_Exception('Timeline temporary directory did not exist and could not be created.');
        }
    }
    $this->addElement('File', 'cover', array(
      'destination' => $tmp_dest,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array('IsImage',
                            array('validator' => 'Count', 'options' => array(false, 1))
                           )
    ));
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_smaller_width', 0)) {
        $this->cover->addValidator('ImageSize', false, array('minwidth' => Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_width', '1098')) );
    }
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Upload',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
	
    $this->addElement('Dummy','file-desc', array(
                                                    'description' => $this->getView()->translate('Image minimal width: %dpx, minimal height: %dpx.', Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_width', '1098'), Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_height', '250'))
    ));
    $this->addElement('hidden', 'task', array('ignore' => true,
                                              'value' => 'upload_cover'));


  }


}
