<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: General.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesadvancedcomment_Form_Admin_Settings_General extends Engine_Form {

  public function init() {
  
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');
        
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$supportTicket = '<a href="http://www.socialenginesolutions.com/tickets" target="_blank">Support Ticket</a>';
		
		$sesSite = '<a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions website</a>';
		
		$descriptionLicense = sprintf('Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);
		
		$this->addElement('Text', "sesadvancedcomment_licensekey", array(
      'label' => 'Enter License key',
      'description' => $descriptionLicense,
      'allowEmpty' => false,
      'required' => true,
      'value' => $settings->getSetting('sesadvancedcomment.licensekey'),
		));
		
		$this->getElement('sesadvancedcomment_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
    if (!$settings->getSetting('sesadvancedcomment.enablesecomment', 0)) {
      $this->addElement('Radio', 'sesadvancedcomment_enablesecomment', array(
          'label' => 'Replace SE Comments widget',
          'description' => 'Do you replace the SocialEngine Comments widget with the "Advanced Nested Comments" widget from this plugin? (If you choose "Yes", then wherever SocialEngine Comments widget is placed will be replaced by the widget from this plugin.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesadvancedcomment.enablesecomment', 1),
      ));
    }
    
    if ($settings->getSetting('sesadvancedcomment.pluginactivated')) {
    
      $this->addElement('Radio', 'sesadvancedcomment_reactionenable', array(
        'label' => 'Enable Reactions on Like',
        'description' => 'Do you want to enable the Reactions when someone Likes any feed or content using the widget from this plugin?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesadvancedcomment.reactionenable', 1),
      ));
      
      $this->addElement('Radio', 'sesadvancedcomment_enablenestedcomments', array(
        'label' => 'Enable Replies on Comments',
        'description' => 'Do you want to enable users to reply on comments on any feed or content using the widget from this plugin?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesadvancedcomment.enablenestedcomments', 1),
      ));

      $this->addElement('Radio', 'sesadvancedcomment_reportenable', array(
        'label' => 'Allow to Report',
        'description' => 'Do you want to enable members to report comments and replies on any feed or content using the widget from this plugin?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesadvancedcomment.reportenable', 1),
      ));
      
      $this->addElement('Radio', 'sesadvancedcomment_editenable', array(
        'label' => 'Allow to Edit Comments',
        'description' => 'Do you want to enable users to edit their comments and replies any feed or content using the widget from this plugin?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesadvancedcomment.editenable', 1),
      ));
      
      
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
			$fileLink = $view->baseUrl() . '/admin/sesadvancedcomment/emotion/gallery';
			
			
      $this->addElement('Radio', 'sesadvancedcomment_enablestickers', array(
        'label' => 'Enable Stickers',
        'description' => 'Do you want to enable Stickers in status updates, comments and replies on your website? [If you choose "Yes", then you can configure the stickers from <a href="admin/sesadvancedcomment/emotion/gallery">here</a>.]',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'escape' => false,
        'onclick' => 'enablestickers(this.value);',
        'value' => $settings->getSetting('sesadvancedcomment.enablestickers', 1),
      ));
      $this->sesadvancedcomment_enablestickers->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      
      
      $this->addElement('Text', "sesadvancedcomment_stickertitle", array(
        'label' => 'Title of Stickers Popup',
        'description' => 'Enter the title of Stickers Popup.',
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesadvancedcomment.stickertitle', 'Sticker Store'),
      ));
      
      $this->addElement('Text', "sesadvancedcomment_stickerdescription", array(
        'label' => 'Description of Stickers Popup',
        'description' => 'Enter the description of Stickers Popup.',
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesadvancedcomment.stickerdescription', 'Find new stickers to send to friends'),
      ));
      
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
			
			$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
			$fileLink = $view->baseUrl() . '/admin/files/';
			
			$this->addElement('Select', 'sesadvancedcomment_backgroundimage', array(
					'label' => 'Background Image in Stickers Popup',
					'description' => 'Choose from below the background image in Stickers popup. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
					'multiOptions' => $banner_options,
					'escape' => false,
					'value' => $settings->getSetting('sesadvancedcomment.backgroundimage', 'public/admin/store-header-bg.png'),
			));
			$this->sesadvancedcomment_backgroundimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      
      
      $this->addElement('MultiCheckbox', 'sesadvancedcomment_enableordering', array(
          'label' => 'Choose Ordering Options',
          'description' => 'Choose from below the ordering options which you want to show for comments.',
          'multiOptions' => array('newest' => 'Newest', 'oldest' => 'Oldest', 'liked' => 'Liked', 'replied' => 'Replied'),
          'value' => unserialize($settings->getSetting('sesadvancedcomment.enableordering', 'a:4:{i:0;s:6:"newest";i:1;s:6:"oldest";i:2;s:5:"liked";i:3;s:7:"replied";}')),
      ));

      $this->addElement('Radio', 'sesadvancedcomment_translate', array(
        'label' => 'Show Translate Option',
        'description' => 'Do you want to show Translate option in comments and replies? [If you choose Yes, then all the comments and replies will have a translate option.]',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick'=>'showLanguage(this.value);',
        'value' => $settings->getSetting('sesadvancedcomment.translate', 0),
      ));
        
      $localeObject = Zend_Registry::get('Locale');

      $languages = Zend_Locale::getTranslationList('language', $localeObject);
      $territories = Zend_Locale::getTranslationList('territory', $localeObject);

      $localeMultiOptions = array();
      foreach( array_keys(Zend_Locale::getLocaleList()) as $key ) {
        $languageName = null;
        if( !empty($languages[$key]) ) {
          $languageName = $languages[$key];
        } else {
          $tmpLocale = new Zend_Locale($key);
          $region = $tmpLocale->getRegion();
          $language = $tmpLocale->getLanguage();
          if( !empty($languages[$language]) && !empty($territories[$region]) ) {
            $languageName =  $languages[$language] . ' (' . $territories[$region] . ')';
          }
        }
        if( $languageName ) {
          if(strpos($key,'_') === false)
          $localeMultiOptions[$key] = $languageName . ' [' . $key . ']';
        }
      }
      $this->addElement('Select', 'sesadvancedcomment_language', array(
        'label' => 'Default Language',
        'description' => 'Choose the language in which you want to translate the feeds.',
        'multiOptions' => $localeMultiOptions,
        'value' => $settings->getSetting('sesadvancedcomment.language', 'en'),
      ));
      
      $this->addElement('MultiCheckbox', 'sesadvancedcomment_enableattachement', array(
          'label' => 'Choose Attachments',
          'description' => 'Choose from below the attachments which you want to enable in the comments and replies on your website.',
          'multiOptions' => array('photos' => 'Attach Photos', 'videos' => 'Attach Videos', 'emotions' => 'Post Emoticons or Stickers'),
          'value' => unserialize($settings->getSetting('sesadvancedcomment.enableattachement', 'a:3:{i:0;s:6:"photos";i:1;s:6:"videos";i:2;s:8:"emotions";}')),
      ));

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