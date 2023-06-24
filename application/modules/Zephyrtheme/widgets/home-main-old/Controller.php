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

class Zephyrtheme_Widget_HomeMainOldController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	
	$this->view->home_imgposition = $home_imgposition = $this->_getParam('home_imgposition');
	$this->view->home_usesignup = $home_usesignup = $this->_getParam('home_usesignup');
	$this->view->home_uselogin = $home_uselogin = $this->_getParam('home_uselogin');
	$this->view->home_img = $home_img = $this->_getParam('home_img');
	$this->view->home_video = $home_video = $this->_getParam('home_video');
	$this->view->home_bgimg = $home_bgimg = $this->_getParam('home_bgimg');
	$this->view->home_h1 = $home_h1 = $this->_getParam('home_h1');
	$this->view->home_h2 = $home_h2 = $this->_getParam('home_h2');
	$this->view->home_signup = $home_signup = $this->_getParam('home_signup');
	$this->view->home_eltop = $home_eltop = $this->_getParam('home_eltop');
	$this->view->home_imageheight = $home_imageheight = $this->_getParam('home_imageheight');
	
	$this->view->header_loginbutton = $header_loginbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_loginbutton', false);
    $this->view->header_signupbutton = $header_signupbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_signupbutton', false);
  }
}