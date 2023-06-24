<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */
class Zephyrtheme_Widget_FooterMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

	$this->view->navigation = $navigation = Engine_Api::_()
	->getApi('menus', 'core')
	->getNavigation('core_footer');
	
	// Languages
	$translate    = Zend_Registry::get('Zend_Translate');
	$languageList = $translate->getList();
	// Prepare default langauge
	$defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
	if( !in_array($defaultLanguage, $languageList) ) {
	  if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
		$defaultLanguage = 'en';
	  } else {
		$defaultLanguage = null;
	  }
	}
	// Prepare language name list
	$languageNameList  = array();
	$languageDataList  = Zend_Locale_Data::getList(null, 'language');
	$territoryDataList = Zend_Locale_Data::getList(null, 'territory');
	foreach( $languageList as $localeCode ) {
	  $languageNameList[$localeCode] = Engine_String::ucfirst(Zend_Locale::getTranslation($localeCode, 'language', $localeCode));
	  if (empty($languageNameList[$localeCode])) {
		if( false !== strpos($localeCode, '_') ) {
		  list($locale, $territory) = explode('_', $localeCode);
		} else {
		  $locale = $localeCode;
		  $territory = null;
		}
		if( isset($territoryDataList[$territory]) && isset($languageDataList[$locale]) ) {
		  $languageNameList[$localeCode] = $territoryDataList[$territory] . ' ' . $languageDataList[$locale];
		} else if( isset($territoryDataList[$territory]) ) {
		  $languageNameList[$localeCode] = $territoryDataList[$territory];
		} else if( isset($languageDataList[$locale]) ) {
		  $languageNameList[$localeCode] = $languageDataList[$locale];
		} else {
		  continue;
		}
	  }
	}
	$languageNameList = array_merge(array(
	  $defaultLanguage => $defaultLanguage
	), $languageNameList);
	$this->view->languageNameList = $languageNameList;
	
	
	// Logo
    $this->view->footer_logo = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_logo', false);
	// Footer Layout
    $this->view->footer_logged = $footer_logged = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_logged', false);
	$this->view->footer_notlogged = $footer_notlogged = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_notlogged', false);
	// GoToTop Button
    $this->view->footer_gototop = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_gototop', false);
	// Social Icons
    $this->view->footer_socialicons = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_socialicons', false);
	$this->view->footer_facebook = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_facebook', false);
	$this->view->footer_twitter = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_twitter', false);
	$this->view->footer_linkedin = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_linkedin', false);
	$this->view->footer_google = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_google', false);
	$this->view->footer_youtube = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_youtube', false);
	$this->view->footer_vimeo = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_vimeo', false);
	$this->view->footer_tumblr = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_tumblr', false);
	$this->view->footer_pinterest = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_pinterest', false);
	$this->view->footer_instagram = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_instagram', false);
	$this->view->footer_flickr = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.footer_flickr', false);
  }
  public function getCacheKey()
  {
    //return true;
  }
}