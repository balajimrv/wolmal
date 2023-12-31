<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 10086 2013-09-16 19:27:24Z andres $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Authorization_Form_Admin_Level_Edit extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
        ->setTitle('Member Level Settings')
        ->setDescription("AUTHORIZATION_FORM_ADMIN_LEVEL_EDIT_DESCRIPTION");
        
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'allowEmpty' => true,
      'required' => false,
    ));
	
	$this->addElement('Text', 'monthly_income_limit', array(
      'label' => 'Monthly Income Limit',
      'allowEmpty' => false,
      'required' => false,
    ));
	
	$this->addElement('Text', 'award', array(
      'label' => 'Award',
      'allowEmpty' => false,
      'required' => false,
    ));
	
	$this->addElement('Text', 'reward', array(
      'label' => 'Reward',
      'allowEmpty' => false,
      'required' => false,
    ));
	
	$this->addElement('Text', 'level_order', array(
      'label' => 'Level Order',
      'allowEmpty' => false,
      'required' => true,
    ));

    if( !$this->isPublic() ) {

      // Element: edit
      if( $this->isModerator() ) {
        $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Profile Moderation',
          'required' => true,
          'multiOptions' => array(
            2 => 'Yes, allow members in this level to edit other profiles and settings.',
            1 => 'No, do not allow moderation.'
          ),
          'value' => 0,
        ));
      }

      // Element: style
      $this->addElement('Radio', 'style', array(
        'label' => 'Allow Profile Style',
        'required' => true,
        'multiOptions' => array(
          2 => 'Yes, allow members in this level to edit other custom profile styles.',
          1 => 'Yes, allow custom profile styles.',
          0 => 'No, do not allow custom profile styles.'
        ),
        'value' => 1,
      ));
      if( !$this->isModerator() ) {
        unset($this->getElement('style')->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Account Deletion?',
        'multiOptions' => array(
          2 => 'Yes, allow members in this level to delete other users.',
          1 => 'Yes, allow members to delete their account.',
          0 => 'No, do not allow account deletion.',
        ),
        'value' => 1,
      ));
      if( !$this->isModerator() ) {
        unset($this->getElement('delete')->options[2]);
      }
      $this->delete->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: activity
      if( $this->isModerator() ) {
        $this->addElement('Radio', 'activity', array(
          'label' => 'Allow Activity Feed Moderation',
          'required' => true,
          'multiOptions' => array(
            1 => 'Yes, allow members in this level to delete any feed item.',
            0 => 'No, do not allow moderation.'
          ),
          'value' => 0,
        ));
      }

      // Element: block
      $this->addElement('Radio', 'block', array(
        'label' => 'Allow Blocking?',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_BLOCK_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        )
      ));
      $this->block->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Profile Viewing Options',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'    => 'Everyone',
          'registered'  => 'All Registered Members',
          'network'     => 'My Network',
          'member'      => 'My Friends',
          'owner'       => 'Only Me',
        ),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Profile Commenting Options',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'multiOptions' => array(
          'registered'  => 'All Registered Members',
          'network'     => 'My Network',
          'member'      => 'My Friends',
          'owner'       => 'Only Me',
        )
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: search
      $this->addElement('Radio', 'search', array(
        'label' => 'Search Privacy Options',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_SEARCH_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
      ));
      $this->search->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: status
      $this->addElement('Radio', 'status', array(
        'label' => 'Allow status messages?',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_STATUS_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        )
      ));

      $this->addElement('Text', 'activity_edit_time', array(
        'label' => 'Maximum Allowed time for editing status posts?',
        'description' => 'Enter the maximum allowed time (in minutes) for which members will be able to edit their status posts via activity feed.'
        . ' The field must contain an integer between 1 and 1000000, or 0 for unlimited.',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));

      // Element: username
      $this->addElement('Radio', 'username', array(
        'label' => 'Allow username changes?',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_USERNAME_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        )
      ));
      $this->username->getDecorator('Description')->setOption('placement', 'PREPEND');

      // Element: quota
      $this->addElement('Select', 'quota', array(
        'label' => 'Storage Quota',
        'required' => true,
        'multiOptions' => Engine_Api::_()->getItemTable('storage_file')->getStorageLimits(),
        'value' => 0, // unlimited
        'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_QUOTA_DESCRIPTION'
      ));

      // Element: commenthtml
      $this->addElement('Text', 'commenthtml', array(
        'label' => 'Allow HTML in Comments?',
        'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_COMMENTHTML_DESCRIPTION'
      ));

      // Element: messages_auth
      $this->addElement('Radio', 'messages_auth', array(
        'label' => 'Allow messaging?',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_MESSAGESAUTH_DESCRIPTION',
        'multiOptions' => array(
          'everyone' => 'Everyone',
          'friends' => 'Friends Only',
          'none' => 'Disable messaging',
        )
      ));
      
      // Element: messages_editor
      $this->addElement('Radio', 'messages_editor', array(
        'label' => 'Use editor for messaging?',
        'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_MESSAGEEDITOR_DESCRIPTION',
        'multiOptions' => array(
          'editor' => 'Editor',
          'plaintext' => 'Plain Text',
        )
      ));
      
      $this->messages_auth->getDecorator('Description')->setOption('placement', 'PREPEND');
    }
  }
}
