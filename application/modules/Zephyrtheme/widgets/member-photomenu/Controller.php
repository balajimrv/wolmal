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

class Zephyrtheme_Widget_MemberPhotomenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	// Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }
	
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_home');
	  
	$this->view->mpmShowheadline = $mpmShowheadline = $this->_getParam('mpmShowheadline');
	$this->view->mpmShowphoto = $mpmShowphoto = $this->_getParam('mpmShowphoto');
	$this->view->mpmShowmenu = $mpmShowmenu = $this->_getParam('mpmShowmenu');
  }
  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale() . sprintf('%d', $viewer->photo_id);
  }
}