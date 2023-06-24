<?php
class Sesbasic_Form_Admin_Tooltip_Event extends Engine_Form {
  public function init() {
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->addElement('MultiCheckbox', 'sesevent_event_settings_tooltip', array(
      'label' => 'General Tooltip Settings',
			'required'=>true,
			'empty'=>false,
      'multiOptions' => array('title'=>'Title','mainphoto'=>'Main Photo','coverphoto'=>'Cover Photo','category'=>'Category','location'=>'Location','socialshare'=>'Social Share','hostedby'=>'Hosted By','startendtime'=>'Start & End Time'),
			'value' => $settings->getSetting('sesevent.event.settings.tooltip', array('title','mainphoto','coverphoto','category','socialshare','location','hostedby','startendtime','buybutton')),
    ));
    //,'buybutton' => 'Buy Button (if ticket extention installed )'
    $this->addElement('Button', 'submit', array(
				'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $this->addDisplayGroup(array('submit'), 'buttons');
  }
}