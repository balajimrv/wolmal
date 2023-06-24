<?php

class Timeline_Form_Upload extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle(Zend_Registry::get('Zend_Translate')->_("Upload a cover for timeline"))
  //    ->setDescription(Zend_Registry::get('Zend_Translate')->_("Mark himself."))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'timeline',
                                                                                    'controller' => 'ajax',
                                                                                    'action' => 'upload'), 'default', true))
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('id', 'form-upload');

        $this->addElement('FancyUpload', 'file');
        $this->file->clearDecorators()
                   ->addDecorator('FormFancyUpload')
                   ->addDecorator('viewScript', array(
                                                      'viewScript' => '_FancyUpload.tpl',
                                                      'placement'  => ''
                                                      ));
        $type_name = 'images';
        $exts = '*.jpg; *.jpeg; *.gif; *.png;';
        $types_array = array('*.jpg; *.jpeg; *.gif; *.png');

        $this->file->getDecorator('viewScript')->setOption('data', array('max_files' => 1,
                                                                         'file_types' => "'$type_name': '$exts'",
                                                                         'extradata' => array('isajax' => true),
                                                                         'file_types_array' => $types_array ));

  }
  
}
