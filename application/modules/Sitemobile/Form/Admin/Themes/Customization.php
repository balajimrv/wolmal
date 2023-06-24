<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Customization.php 2014-10-09 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemobile_Form_Admin_Themes_Customization extends Engine_Form {
  
  protected $_activeTheme;
  
   public function setactiveTheme($activeTheme) {
    $this->_activeTheme = $activeTheme;
    return $this;
  }

  public function getactiveTheme() {
    return $this->_activeTheme;
  }
  
  public function init() {
    $activeTheme = $this->_activeTheme;  
    $activeThemeId = $activeTheme->theme_id;
    $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_($activeTheme->title)));
       $this->addElement('Radio', 'theme_customization', array(
        'label' => 'Select Theme Color',        
        'multiOptions' => array(
            0 => 'DEFAULT',
            1 => 'LIGHTORANGE',
            2 => 'LIGHTPINK',
            3 => 'LIGHTPURPLE',
            4 => 'Custom Colors (Choosing this option will enable you to customize your theme according to your site.)'
        ),
        'onchange' => 'changeThemeCustomization();',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('theme.customization', 0),
    ));
    
    $this->addElement('Text', 'sitemobile_theme_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_themeColor.tpl',
                    'class' => 'form element'
            )))
    ));
    
    $this->addElement('Text', 'sitemobile_theme_button_border_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_themeButtonBorderColor.tpl',
                    'class' => 'form element'
            )))
    ));
    
    $this->addElement('Text', 'sitemobile_landingpage_signinbtn', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_themeLandingPageSigninButtonColor.tpl',
                    'class' => 'form element'
            )))
    ));
    
    $this->addElement('Text', 'sitemobile_landingpage_signupbtn', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_themeLandingPageSignupButtonColor.tpl',
                    'class' => 'form element'
            )))
    ));
    

    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }

}