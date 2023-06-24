<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Level.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();
    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('ALBUM_FORM_ADMIN_LEVEL_DESCRIPTION');
    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Albums?',
      'description' => 'Do you want to let users view albums? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow members to view all albums, even private ones.',
        1 => 'Yes, allow viewing of albums.',
        0 => 'No, do not allow albums to be viewed.'
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
    if( !$this->isPublic() ) {			
      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Albums?',
        'description' => 'Do you want to let users create photo albums? If set to no, some other settings on this page may not apply. This is useful if you want users to be able to view albums, but only want certain levels to be able to create albums.',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow creation of albums.',
          0 => 'No, do not allow albums to be created.'
        ),
        'value' => 1,
      ));      
      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Albums?',
        'description' => 'Do you want to let members of this level edit albums?',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all albums.',
          1 => 'Yes, allow members to edit their own albums.',
          0 => 'No, do not allow albums to be edited.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Albums?',
        'description' => 'Do you want to let members of this level delete albums?',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all albums.',
          1 => 'Yes, allow members to delete their own albums.',
          0 => 'No, do not allow members to delete their albums.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }
      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Albums?',
        'description' => 'Do you want to let members of this level comment on albums?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all albums, including private ones.',
          1 => 'Yes, allow members to comment on albums.',
          0 => 'No, do not allow members to comment on albums.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Album Privacy',
        'description' => 'ALBUM_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner'),
      ));
      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Album Comment Options',
        'description' => 'ALBUM_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner'),
      ));
      // Element: auth_tag
      $this->addElement('MultiCheckbox', 'auth_tag', array(
        'label' => 'Album Tag Options',
        'description' => 'ALBUM_FORM_ADMIN_LEVEL_AUTHTAG_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner'),
      ));
			 // Element: download
			$this->addElement('Radio', 'download', array(
        'label' => 'Allow Downloading of Albums & Photos?',
        'description' => 'Do you want to let members download Albums and Photos from your website? [Note: If you choose No, then Download option for the albums and photos from this plugin will be disabled.]',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow downloading of albums and photos.',
          0 => 'No, do not allow albums and photos to be downloaded.'
        ),
        'value' => 1,
      ));
			 // Element: album_rate
      $this->addElement('Radio', 'rating_album', array(
        'label' => 'Allow Rating on Albums?',
        'description' => 'Do you want to let members rate Albums?',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow rating on albums.',
          0 => 'No, do not allow rating on albums.'
        ),
        'value' => 1,
      ));
			 // Element: photo_rate
      $this->addElement('Radio', 'rating_photo', array(
        'label' => 'Allow Rating on Photos?',
        'description' => 'Do you want to let members rate Photos?',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow rating on photos.',
          0 => 'No, do not allow rating on photos.'
        ),
        'value' => 1,
      ));
			 // Element: favourite_album
      $this->addElement('Radio', 'favourite_album', array(
        'label' => 'Allow Adding Albums to Favorite?',
        'description' => 'Allow Adding Albums to Favorite?',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow adding Albums to Favorite.',
          0 => 'No, do not allow adding albums to favorite lists.'
        ),
        'value' => 1,
      ));
			 // Element: favourite_photo
      $this->addElement('Radio', 'favourite_photo', array(
        'label' => 'Do you want to let members add photos to their favorite list.',
        'description' => 'Allow Adding Photos to Favorite?',
        'value' => 1,
        'multiOptions' => array(
          1 => 'Yes, allow adding of photos to favorite lists.',
          0 => 'No, do not allow adding photos to favorite lists.'
        ),
        'value' => 1,
      ));
			 // Element: watermark
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
			$banner_options[] = '';
			$path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
			foreach ($path as $file) {
				if ($file->isDot() || !$file->isFile())
					continue;
				$base_name = basename($file->getFilename());
				if (!($pos = strrpos($base_name, '.')))
					continue;
				$extension = strtolower(ltrim(substr($base_name, $pos), '.'));
				if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
					continue;
				$banner_options['public/admin/' . $base_name] = $base_name;
			}
			$fileLink = $view->baseUrl() . '/admin/files/';
			if (count($banner_options) > 1) {
				$this->addElement('Select', 'watermark', array(
						'label' => 'Add Watermark to Main Photos',
						'description' => 'Choose a photo which you want to be added as watermark on the main photos upload by the members of this level on your website.',
						'multiOptions' => $banner_options,
				));
			} else {
				$description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for watermark. Photo to be chosen for watermark should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";
				//Add Element: Dummy
				$this->addElement('Dummy', 'watermark', array(
						'label' => 'Add Watermark to Main Photos',
						'description' => $description,
				));
				$this->watermark->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		}
		
			 // Element: thumb watermark
			if (count($banner_options) > 1) {
				$this->addElement('Select', 'watermarkthumb', array(
						'label' => 'Add Watermark to Thumb Photos',
						'description' => 'Choose a photo which you want to be added as watermark on the thumb photos upload by the members of this level on your website.',
						'multiOptions' => $banner_options,
				));
			} else {
				$description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for watermark. Photo to be chosen for watermark should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";
				//Add Element: Dummy
				$this->addElement('Dummy', 'watermarkthumb', array(
						'label' => 'Add Watermark to Thumb Photos',
						'description' => $description,
				));
				$this->watermarkthumb->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		}
		
			  // Element: max_albums
      $this->addElement('Text', 'max_albums', array(
        'label' => 'Maximum Allowed Albums',
        'description' => 'Enter the maximum number of albums a member can create. The field must contain an integer, use zero for unlimited.',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
				 'value' => 0,
      ));
      
      $sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
      if(Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock')) && in_array('sesalbum',$sesprofilelock_enable_module)) {        
        // Element: locked video
        $this->addElement('Radio', 'sesalbum_locked', array(
            'label' => 'Allow User to Lock albums?',
            'description' => 'Do you want to let members to locked albums?',
            'multiOptions' => array(
                1 => 'Yes, allow members to locked albums.',
                0 => 'No, do not allow members to locked albums.',
            ),
            'value' => ( $this->isModerator() ? 1 : 1 ),
        ));
      }
		 
    }		
		 // Element: lightbox type
		 $this->addElement('Radio', 'imageviewer', array(
				'label' => 'Lightbox Viewer Type',
				'description' => 'Choose the lighbox viewer type to be enabled for the members of this level.',
				'multiOptions' => array(
						0 => 'Basic Viewer',
						1 => 'Advanced Viewer'
				),
				'value' => 1,
		));			
  }
}