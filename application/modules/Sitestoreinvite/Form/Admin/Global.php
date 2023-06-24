<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Inviter Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Dummy', 'yahoo_settings_temp', array(
        'label' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formcontactimport.tpl',
                    'class' => 'form element'
            )))
    ));

		$this->addElement('Radio', 'storeinvite_friend_invite_enable', array(
        'label' => 'Web Accounts for Import & Invite',
        'description' => "Do you want users of your site to be able to invite their friends to your site using all the available web accounts on your site? (If you select 'Yes' over here, then you will be able to choose below the various web accounts.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('storeinvite.friend.invite.enable', 1),
        
    ));

    $webmail_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('storeinvite.show.webmail', 0);
    if (!empty($webmail_values)) {
      $webmail_values = unserialize($webmail_values);
    }
    $this->addElement('MultiCheckbox', 'storeinvite_show_webmail', array(
        'label' => 'Web Account services',
        'description' => "Select the web account services that you want to be available to users of your site for inviting their friends.",
        'multiOptions' => array(
            'gmail' => 'Gmail',
            'yahoo' => 'Yahoo',
            'window_mail' => 'Window Live',
            'aol' => 'AOL',
            'facebook_mail' => 'Facebook',
            'linkedin_mail' => 'Linkedin',
            'twitter_mail' => 'Twitter',
        ),
        'value' => $webmail_values
    ));
	
	 $this->addElement('Text', 'sitestoreinvite_manifestUrl', array(
        'label' => 'Store Invites URL alternate text for "store-invites"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "storeinvites" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreinvite.manifestUrl', "store-invites"),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>