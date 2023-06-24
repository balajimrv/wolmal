<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Admin_Global extends Engine_Form {

  public function init() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');
            
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$supportTicket = '<a href="http://www.socialenginesolutions.com/tickets" target="_blank">Support Ticket</a>';
		$sesSite = '<a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions website</a>';
		$descriptionLicense = sprintf('Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);
		$this->addElement('Text', "sesalbum_licensekey", array(
		'label' => 'Enter License key',
		'description' => $descriptionLicense,
		'allowEmpty' => false,
		'required' => true,
		'value' => $settings->getSetting('sesalbum.licensekey'),
		));
		$this->getElement('sesalbum_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
    if ($settings->getSetting('sesalbum.pluginactivated')) {
      if (!$settings->getSetting('sesalbum.set.landingpage', 0)) {
        $this->addElement('Radio', 'sesalbum_set_landingpage', array(
            'label' => 'Set Welcome Page as Landing Page',
            'description' => 'Do you want to set the Default Welcome Page of this plugin as Landing page of your website?  [This is a one time setting, so if you choose ‘Yes’ and save changes, then later you can manually make changes in the Landing page from Layout Editor.]',
            'onclick' => 'confirmChangeLandingPage(this.value)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sesalbum.set.landingpage', 0),
        ));
      }
      $this->addElement('Radio', 'sesalbum_check_welcome', array(
          'label' => 'Who all users do you want to see this "Welcome Page"?',
          'description' => '',
          'multiOptions' => array(
              0 => 'Only logged in users',
              1 => 'Only non-logged in users',
              2 => 'Both, logged-in and non-logged in users',
          ),
          'value' => $settings->getSetting('sesalbum.check.welcome', 2),
      ));
      $this->addElement('Radio', 'sesalbum_enable_welcome', array(
          'label' => 'Albums Menu Redirection',
          'description' => 'Choose from below where do you want to redirect users when Albums Menu item is clicked in the Main Navigation 
					 Bar.',
          'multiOptions' => array(
              1 => 'Album Welcome Page',
              0 => 'Album Home Page'
          ),
          'value' => $settings->getSetting('sesalbum.enable.welcome', 1),
      ));
      $this->addElement('Radio', 'sesalbum_enable_addphotoshortcut', array(
          'label' => 'Show Add New Photos Icon',
          'description' => 'Do you want to show “Add New Photos” Icon on all the pages of this plugin? This icon will redirect users to Add New Photos page.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.enable.addphotoshortcut', 1),
      ));
			$this->addElement('Radio', 'sesalbum_enable_location', array(
        'label' => 'Enable Location in Album/Photo',
        'description' => 'Choose from below where do you want to enable location in Album/Photo.',
        'multiOptions' => array(
            '1' => 'Yes,Enable Location',
            '0' => 'No,Don\'t Enable Location',
        ),
        'value' => $settings->getSetting('sesalbum.enable.location', 1),
    ));
      $this->addElement('Radio', 'sesalbum_search_type', array(
          'label' => 'Proximity Search Unit',
          'description' => 'Choose the unit for proximity search of albums and photos on your website.',
          'multiOptions' => array(
              1 => 'Miles',
              0 => 'Kilometres'
          ),
          'value' => $settings->getSetting('sesalbum.search.type', 1),
      ));
      $this->addElement('Radio', 'sesalbum_category_enable', array(
          'label' => 'Make Categories Mandatory',
          'description' => 'Do you want to make category field mandatory when users create or edit their albums?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.category.enable', 1),
      ));
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $this->addElement('Radio', 'sesalbum_watermark_enable', array(
          'label' => 'Add Watermark to Photos',
          'description' => 'Do you want to add watermark to photos on your website? If you choose Yes, then you can upload watermark image to be added to the photos on your website from the <a href="' . $view->baseUrl() . "/admin/sesalbum/level" . '">Member Level Settings</a>.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'show_position(this.value)',
          'value' => $settings->getSetting('sesalbum.watermark.enable', 0),
      ));
      $this->addElement('Select', 'sesalbum_position_watermark', array(
          'label' => 'Select the position of watermark',
          'description' => '',
          'multiOptions' => array(
              0 => 'Middle ',
              1 => 'Top Left',
              2 => 'Top Right',
              3 => 'Bottom Right',
              4 => 'Bottom Left',
              5 => 'Top Middle',
              6 => 'Middle Right',
              7 => 'Bottom Middle',
              8 => 'Middle Left',
          ),
          'value' => $settings->getSetting('sesalbum.position.watermark', 0),
      ));
      $this->sesalbum_watermark_enable->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      $this->addElement('Text', "sesalbum_mainheight", array(
          'label' => 'Large Photo Height',
          'description' => "Enter the maximum height of the large main photo (in pixels). [Note: This photo will be shown in the lightbox and on Photo View Page. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesalbum.mainheight', 1600),
      ));
      $this->addElement('Text', "sesalbum_mainwidth", array(
          'label' => 'Large Photo Width',
          'description' => "Enter the maximum width of the large main photo (in pixels). [Note: This photo will be shown in the lightbox and on Photo View Page. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesalbum.mainwidth', 1600),
      ));
      $this->addElement('Text', "sesalbum_normalheight", array(
          'label' => 'Medium Photo Height',
          'description' => "Enter the maximum height of the medium photo (in pixels). [Note: This photo will be shown in the various widgets and pages. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesalbum.normalheight', 500),
      ));
      $this->addElement('Text', "sesalbum_normalwidth", array(
          'label' => 'Medium Photo Width',
          'description' => "Enter the maximum width of the medium photo (in pixels). [Note: This photo will be shown in the various widgets and pages. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesalbum.normalwidth', 500),
      ));
      $this->addElement('Radio', 'sesalbum_album_rating', array(
          'label' => 'Allow Rating on Albums',
          'description' => 'Do you want to allow users to give ratings on albums on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'rating_album(this.value)',
          'value' => $settings->getSetting('sesalbum.album.rating', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratealbum_own', array(
          'label' => 'Allow Rating on Own Albums',
          'description' => 'Do you want to allow users to give ratings on own albums on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratealbum.own', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratealbum_again', array(
          'label' => 'Allow to Edit Rating on Albums',
          'description' => 'Do you want to allow users to edit their ratings on albums on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratealbum.again', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratealbum_show', array(
          'label' => 'Show Previous Rating on Albums',
          'description' => 'Do you want to show previous ratings on albums on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratealbum.show', 1),
      ));
      $this->addElement('Radio', 'sesalbum_photo_rating', array(
          'label' => 'Allow Rating on Photos',
          'description' => 'Do you want to allow users to give ratings on photos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'rating_photo(this.value)',
          'value' => $settings->getSetting('sesalbum.photo.rating', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratephoto_own', array(
          'label' => 'Allow Rating on Own Photos',
          'description' => 'Do you want to allow users to give ratings on own photos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratephoto.own', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratephoto_again', array(
          'label' => 'Allow to Edit Rating on Photos',
          'description' => 'Do you want to allow users to edit their ratings on photos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratephoto.own', 1),
      ));
      $this->addElement('Radio', 'sesalbum_ratephoto_show', array(
          'label' => 'Show Previous Rating on Photos',
          'description' => 'Do you want to show previous ratings on photos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.ratephoto.show', 1),
      ));
			
			$this->addElement('Radio', 'sesalbum_enable_check_privacy', array(
          'label' => 'Hide Albums & Photos from Un-authorized Users',
          'description' => 'Do you want to hide albums and photos to be displayed to un-authorized users in all the widgets and pages? If you choose "Yes", then the albums or photos which are made Private to friends / networks would only be visible to the authorized friends / networks. [Note: If Yes, is chosen then it may slow down the performance of the page on which widgets from this plugin will be placed.] ',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.enable.check.privacy', 0),
      ));
      $this->addElement('Radio', 'sesalbum_wall_profile', array(
          'label' => 'Hide Default Albums',
          'description' => 'Do you want to hide default albums from various plugins and features like Wall Albums, Profile Albums, etc to be displayed in various widgets and pages of this plugin?',
          'multiOptions' => array(
              0 => 'Yes',
              1 => 'No'
          ),
          'value' => $settings->getSetting('sesalbum.wall.profile', 0),
      ));

      $this->addElement('Select', 'sesalbum_taboptions', array(
          'label' => 'Menu Items Count',
          'description' => 'How many menu items do you want to show in the main navigation menu of this plugin?',
          'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
              7 => 7,
              8 => 8,
              9 => 9,
          ),
          'value' => $settings->getSetting('sesalbum.taboptions', 6),
      ));
				//default photos
		$default_photos_main = array();
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
      $default_photos_main['public/admin/' . $base_name] = $base_name;
    }
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';
		//event main photo
    if (count($default_photos_main) > 0) {
			$default_photos = array_merge(array('application/modules/Sesalbum/externals/images/sesalbum_adult.png'=>''),$default_photos_main);
      $this->addElement('Select', 'sesalbum_album_default_adult', array(
          'label' => 'Default Adult Photo for Albums',
          'description' => 'Choose default photo for adult albums on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a target="_blank" href="' . $fileLink . '">File & Media Manager</a>. Leave the field blank if you do not want to change adult default photo.]',
          'multiOptions' => $default_photos,
          'value' => $settings->getSetting('sesalbum.album.default.adult'),
      ));
    } else {
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for adult albums on your website. Photo to be chosen for adult albums on your website should be first uploaded from the "Layout" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manager</a>" section. => There are currently no photo in the File & Media Manager for the adult albums on your website. Please upload the Photo to be chosen for adult albums on your website from the "Layout" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manager</a>" section.') . "</span></div>";
      //Add Element: Dummy
      $this->addElement('Dummy', 'sesalbum_album_default_adult', array(
          'label' => 'Default Adult Photo for albums',
          'description' => $description,
      ));
    }
    $this->sesalbum_album_default_adult->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		
      // Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    } else {
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
