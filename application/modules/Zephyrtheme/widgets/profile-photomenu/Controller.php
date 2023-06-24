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

class Zephyrtheme_Widget_ProfilePhotomenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
	$this->view->user = $subject;
	
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_profile');
	  
	$this->view->mpmShowphoto = $mpmShowphoto = $this->_getParam('mpmShowphoto');
	$this->view->mpmShowmenu = $mpmShowmenu = $this->_getParam('mpmShowmenu');
  }
}