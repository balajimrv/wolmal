<?php
class Zephyrtheme_Form_Cover extends Engine_Form
{
    public function init()
    {
        $this
            ->setTitle('Profile cover')
			->setDescription('Upload an image that is at least 1100px wide.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('class', 'global_form_box')
            ->setAttrib('name', 'profile-cover')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'zephyrtheme',
                'controller' => 'index',
                'action' => 'add-cover'), 'default', true));

        $this->addElement('FancyUpload', 'cover_file');
        $this->cover_file->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement'  => ''
            ));
        $type_name = 'images';
        $exts = '*.jpg; *.jpeg; *.gif; *.png;';
        $types_array = array('*.jpg; *.jpeg; *.gif; *.png');

        $this->cover_file->getDecorator('viewScript')->setOption('data', array('max_files' => 1,
            'file_types' => "'$type_name': '$exts'",
            'extradata' => array('isajax' => true),
            'file_types_array' => $types_array ));
    }
}