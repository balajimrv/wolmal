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

class Zephyrtheme_Widget_HeaderMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	// Logo
    $this->view->header_logo = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_logo', false);
	
	// Identity
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

	// Menus 
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');
	$this->view->mininavigation = $mininavigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');
	$this->view->profilenavigation = $profilenavigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_edit');
	  
	$require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){ $this->view->search_check = true; }
      else{ $this->view->search_check = false; }
    }
    else $this->view->search_check = true;
	
	if( $viewer->getIdentity() )
    {
	  $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->hasNotifications($viewer);
	  $this->view->messageCount = Engine_Api::_()->zephyrtheme()->getUnreadMessageCount($viewer);
      $this->view->requestCount = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->hasNotifications($viewer, 'friend');
	}
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()) {
      $navigation->removePage($navigation->findOneBy('route', 'user_general'));
    }
	
	
    $this->view->header_layout = $header_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_layout', false);
    $this->view->mainmenu_dropdown = $mainmenu_dropdown = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.mainmenu_dropdown', false);
	$this->view->header_fixed = $header_fixed = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_fixed', false);
    $this->view->header_loginbutton = $header_loginbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_loginbutton', false);
    $this->view->header_signupbutton = $header_signupbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_signupbutton', false);
	$this->view->header_iconsshow = $header_iconsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_iconsshow', false);
	$this->view->header_banner = $header_banner = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_banner', false);
	$this->view->header_mainmenu = $header_mainmenu = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_mainmenu', false);
	$this->view->header_iconssize = $header_iconssize = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_iconssize', false);
	$this->view->use_googlefonts = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.use_googlefonts', false);
	$this->view->google_font = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.google_font', false);
	$this->view->google_headingsfont = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.google_headingsfont', false);
  }
  public function getCacheKey()
  {
    //return true;
  }
}