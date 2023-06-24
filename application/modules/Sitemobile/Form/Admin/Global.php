<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );
  
  public function init() {

    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

    $this
            ->setTitle('Global Settings')
            ->setName('sitemobile_global')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitemobile_lsettings', array(
        'label' => 'Mobile / Tablet Plugin License Key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettingsApi->getSetting('sitemobile.lsettings'),
    ));
    
    $isSitemobileandroidappEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobileandroidapp');
    $isSitemobileiosappEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobileiosapp');
    
    if(!empty($isSitemobileandroidappEnable)){
      $this->addElement('Text', 'sitemobileandroidapp_lsettings', array(
          'label' => 'Mobile Android Application License Key',
          'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
          'value' => $coreSettingsApi->getSetting('sitemobileandroidapp.lsettings'),
      ));
    }
    
    if(!empty($isSitemobileiosappEnable)) {
      $this->addElement('Text', 'sitemobileiosapp_lsettings', array(
          'label' => 'Mobile IOS Application License Key',
          'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
          'value' => $coreSettingsApi->getSetting('sitemobileiosapp.lsettings'),
      ));
    }    

    if (APPLICATION_ENV == 'production') {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true,
        'order' => 500,
    ));

    // init site title
    $this->addElement('Text', 'sitemobile_site_title', array(
        'label' => 'Site Title',
        'description' => 'Give your community in mobile / tablet a unique name. This will appear in the heading throughout most of your site when viewed in mobile / tablet.',
        'value' => $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'))
    ));

    $this->addElement('Radio', 'sitemobile_enabel_tablet', array(
        'label' => 'Tablet Mode',
        'description' => "Do you want Tablet Mode to be enabled on your site? If enabled, your site will automatically open in Tablet Mode when opened in Tablet.",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->sitemobile()->enabelTablet()
    ));
    // $this->site_title->getDecorator('Description')->setOption('placement', 'append');
    //do you want to be show the dashboard menu on this pages
    $this->addElement('MultiCheckbox', 'sitemobile_dashboard_display', array(
        'label' => 'Show Dashboard Icon',
        'description' => "Do you want to show the dashboard icon on Login and Signup pages of your site?",
        'multiOptions' => array(
            'login' => 'Yes, show icon on Login page',
            'signup' => 'Yes, show icon on Signup page',
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.dashboard.display', array('login', 'signup'))
    ));

    $this->addElement('Radio', 'sitemobile_login_ajax', array(
        'label' => 'Login via AJAX',
        'description' => "Do you want to enable Login on your site via AJAX? (Note: Select No, if SSL(https://) is used in the URL of the Login page of your site.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.login.ajax', 1)
    ));

    $captcha_options = array(
        1 => 'Yes, make members complete the CAPTCHA form.',
        0 => 'No, do not show a CAPTCHA form.',
    );
    $this->addElement('Radio', 'sitemobile_spam_signup', array(
        'label' => 'Validation Code when Signing Up',
        'description' => "Require new users to enter validation code when signing up? [Note: This plugin already prevents spamming on your site, so it is not recommended to enable this setting to stop spamming.]",
        'multiOptions' => $captcha_options,
        'value' => $coreSettingsApi->getSetting('sitemobile.spam.signup', 0),
    ));


    $this->addElement('Radio', 'sitemobile_spam_login', array(
        'label' => 'Validation Code when Signing In',
         'description' => "Require users to enter validation code when signing in? [Note: This plugin already prevents spamming on your site, so it is not recommended to enable this setting to stop spamming.]",
        'multiOptions' => $captcha_options,
        'value' => $coreSettingsApi->getSetting('sitemobile.spam.login', 0),
    ));

    $this->addElement('Radio', 'sitemobile_scroll_autoload', array(
        'label' => 'Auto-Loading Notifications On-scroll',
        'description' => "Do you want to enable auto-loading of notifications when users scroll down to the bottom of their Notifications pages?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.scroll.autoload', 1)
    ));

    $this->addElement('Radio', 'sitemobile_tinymceditor', array(
        'label' => 'TinyMCE Editor',
        'description' => 'Allow TinyMCE editor at various places like blog description, group discussion, etc.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('sitemobile.tinymceditor', 0),
    ));

    $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

    // Element: submit
    $this->addElement('Button', 'save', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}