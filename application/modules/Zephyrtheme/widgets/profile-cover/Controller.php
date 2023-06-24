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

class Zephyrtheme_Widget_ProfileCoverController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_profile');
	  
	// Identity
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();  
	$subject = Engine_Api::_()->core()->getSubject('user');
	$this->view->auth = ( $subject->authorization()->isAllowed(null, 'view') );
	$this->view->current_cover = Engine_Api::_()->zephyrtheme()->getProfileCover($subject);
  }
}