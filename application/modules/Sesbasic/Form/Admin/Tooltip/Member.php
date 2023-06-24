<?php
class Sesbasic_Form_Admin_Tooltip_Member extends Engine_Form {
  public function init() {
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->addElement('MultiCheckbox', 'user_settings_tooltip', array(
      'label' => 'General Tooltip Settings',
			'required'=>true,
			'empty'=>false,
      'multiOptions' => array('title'=>'Title','mainphoto'=>'Main Photo','coverphoto'=>'Cover Photo','location'=>'Location','socialshare'=>'Social Share', 'friendCount' => 'Total Friends', 'mutualFriendCount' => 'Mutual Friend', 'likeButton' => 'Like Button', 'message' => 'Message Button', 'view' => 'View Count', 'like' => 'Like Count', 'follow' => 'Follow Button', 'friendButton' => 'Friend Button', 'age' => 'Member Age', 'profileType' => 'Member profile Type', 'email' => 'Member Email', 'rating' => 'Member Rating'),
			'value' => $settings->getSetting('user.settings.tooltip', array('title','mainphoto','coverphoto', 'socialshare','location', 'friendCount', 'mutualFriendCount', 'likeButton', 'message', 'view', 'like', 'follow', 'friendButton', 'age', 'profileType', 'email', 'rating')),
    ));
    $this->addElement('Button', 'submit', array(
				'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $this->addDisplayGroup(array('submit'), 'buttons');
  }
}