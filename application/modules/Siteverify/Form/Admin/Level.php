<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteverify_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    $this->setTitle("Member Level Settings")
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // PREPARE USER LEVELS
    $levelOptions = array();
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->getTitle() == "Public") {
        continue;
      }
      $levelOptions[$level->level_id] = $level->getTitle();
    }

    // ELEMENT:LEVEL ID
    $this->addElement('Select', 'level_id', array(
        'label' => 'Member Level',
        'multiOptions' => $levelOptions,
        'onchange' => 'javascript:fetchLevelSettings(this.value);',
        'ignore' => true,
    ));

    //VALUE FOR ALLOW TO VERIFY
    $this->addElement("Radio", "allow_verify", array(
        'label' => "Allow Verification by other Members",
        'Description' => "Do you want members of this member level to be verified by other members? If set to no, some other settings on this page might not apply.",
        'multiOptions' => array(
            1 => 'Yes, these members can be verified by others.',
            0 => 'No, these members cannot be verified by others.',
        ),
        'onchange' => 'javascript:allowVerify();',
        'value' => 1,
    ));

    $this->addElement('MultiCheckbox', 'auth_verify', array(
        'description' => 'Select the member levels which will be able to verify members of this member level.',
        'multiOptions' => $levelOptions,
        'value' => array('0', '1', '2', '3'),
    ));

    //VALUE FOR ADMIN APPROVE
    $this->addElement("Radio", "admin_approve", array(
        'label' => "Admin Approval for Verifications",
        'Description' => 'Whenever members of this member level are verified by others, then do you want these verifications to be approved by you the site admin? If yes, then an email and notification will be sent to site admin whenever a member of this member level gets verified by someone. (The content of this email can be customized from the "Settings" > "Mail Templates" section.)',
        'multiOptions' => array(
            1 => 'Yes, site admin approval will be required to verify a member of this member level.',
            0 => 'No, automatically approve verifications.',
        ),
        'value' => 0,
    ));


    //VALUE FOR ADMIN APPROVE
    $this->addElement("Radio", "allow_unverify", array(
        'label' => "Allow Verification Cancellations",
        'Description' => "Do you want other members to be able to cancel their verifications for members of this member level?",
        'multiOptions' => array(
            1 => 'Yes, allow members to cancel their verifications for members of this member level.',
            0 => 'No, do not allow verification cancellations; A verification once done should be final.',
        ),
        'value' => 1,
    ));

    //VALUE FOR  VERIFY LIMIT
    $this->addElement("Text", "verify_limit", array(
        'label' => "Verification Threshold",
        'description' => 'Enter the threshold limit of verifications count after which a member of this member level will be marked as "Verified".',
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('Int', true),
            array('Between', false, array('min' => '0', 'max' => '100', 'inclusive' => false)),
        ),
    ));

    //ADD SUBMIT BUTTON
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}