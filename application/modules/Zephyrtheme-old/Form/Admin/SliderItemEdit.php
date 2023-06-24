<?php
class Zephyrtheme_Form_Admin_SliderItemEdit extends Zephyrtheme_Form_Admin_SliderItemCreate
{
    public function init()
    {
        parent::init();
        $this->setTitle('Edit Slider Item');
        $this->submit->setLabel('Save Changes');
    }
}