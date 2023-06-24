<?php
class Zephyrtheme_Form_Admin_SliderItemCreate extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Create Slider Item')
            ->setAttrib('class', 'global_form_popup');

        // Get available files
        $image_options = array('' => 'Default Text/Image');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach( $it as $file ) {
            if( $file->isDot() || !$file->isFile() ) continue;
            $basename = basename($file->getFilename());
            if( !($pos = strrpos($basename, '.')) ) continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if( !in_array($ext, $imageExtensions) ) continue;
            $image_options['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Dummy', 'img_preview', array(
            'label' => 'Preview background',
            'content' => '<td class="image"><div style="overflow: hidden; max-height: 200px;"><img src="http://placehold.it/1x1" id="img_preview" style="width: 100%; height: auto;"></div></td>',
            'decorators' => array(
                'Label', 'ViewHelper',
            ),
            'ignore' => true
        ));

        $this->addElement('Select', 'image_path', array(
            'label' => 'Background Image',
            'value' => '',
            'multiOptions' => $image_options
        ));

        $this->addElement('Select', 'illustration_path', array(
            'label' => 'Illustration',
            'value' => '',
            'multiOptions' => $image_options
        ));

		$this->addElement('Text', 'video', array(
            'label' => 'Video Embed Code(*This will replace the illustration chosen above)',
            'value' => ''
        ));
		
        $this->addElement('Text', 'title', array(
            'label' => 'First Text Line',
            'value' => ''
        ));

        $this->addElement('Text', 'subtitle', array(
            'label' => 'Second Text Line',
            'value' => ''
        ));
		
		$this->addElement('Text', 'color', array(
            'label' => 'Text Color',
            'value' => '#FFFFFF'
        ));

        $this->addElement('Select', 'text_align', array(
            'label' => 'Text Position',
            'value' => 'right',
            'multiOptions' => array('left' => 'Left', 'right' => 'Right')
        ));

        $this->addElement('MultiCheckbox', 'show_item', array(
            'label' => 'What elements to show on this slide',
            'value' => array('image', 'firsthl', 'secondhl', 'loginbtn', 'signupbtn'),
            'multiOptions' => array(
                'image' => 'Image/Video',
                'firsthl' => 'First Headline(H1)',
                'secondhl' => 'Second Headline(H2)',
                'loginbtn' => 'Login Button',
                'signupbtn' => 'Signup Button'
            )
        ));

        $this->addElement('Text', 'colonetop', array(
            'label' => 'Margin-Top for area that contains Image/Video',
            'value' => '100px'
        ));

        $this->addElement('Text', 'coltwotop', array(
            'label' => 'Margin-Top for area that contains the Headlines',
            'value' => '100px'
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Create',
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