<?php

class Zephyrtheme_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application) {
    parent::__construct($application);
	
	// Responsive Layout
	$this->initViewHelperPath();
	$this->responsive_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.responsive_layout', false);
	$headMeta = new Zend_View_Helper_HeadMeta();
	if($this->responsive_layout == 'true') { 
	  $headMeta->prependName('viewport', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;');
	}
	
	// Google Fonts
	$this->use_googlefonts = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.use_googlefonts', false);
		
	$this->googlefontset_cyrillicext = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_cyrillicext', false);
	$this->googlefontset_cyrillic = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_cyrillic', false);
	$this->googlefontset_greekext = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_greekext', false);
	$this->googlefontset_greek = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_greek', false);
	$this->googlefontset_vietnamese = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_vietnamese', false);
	$this->googlefontset_latinext = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.googlefontset_latinext', false);
	
	$fontsets = ($this->googlefontset_latinext) ? ',latin-ext' : '';
	$fontsets .= ($this->googlefontset_cyrillicext) ? ',cyrillic-ext' : '';
	$fontsets .= ($this->googlefontset_cyrillic) ? ',cyrillic' : '';
	$fontsets .= ($this->googlefontset_greekext) ? ',greek-ext' : '';
	$fontsets .= ($this->googlefontset_greek) ? ',greek' : '';
	$fontsets .= ($this->googlefontset_vietnamese) ? ',vietnamese' : '';

	if($this->use_googlefonts == 'true'){
		$this->google_font = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.google_font', false);
		$this->google_headingsfont = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.google_headingsfont', false);
		$headLink = new Zend_View_Helper_HeadLink();
		if(!empty($this->google_font)){
			$google_fontfix = str_replace(' ', '+', trim(ucwords(strtolower($this->google_font))));
			$headLink->appendStylesheet('https://fonts.googleapis.com/css?family='.$google_fontfix.':400,700&subset=latin'.$fontsets);
		}
		if(!empty($this->google_headingsfont)){
			$google_headingsfontfix = str_replace(' ', '+', trim(ucwords(strtolower($this->google_headingsfont))));
			$headLink->appendStylesheet('https://fonts.googleapis.com/css?family='.$google_headingsfontfix.':400,700&subset=latin'.$fontsets);
		}
	}
  }
}