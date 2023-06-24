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

class Zephyrtheme_Widget_HomebottomController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->homebottom_img = $homebottom_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.homebottom_img', false);
	$this->view->homebottom_h1 = $homebottom_h1 = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.homebottom_h1', false);
	$this->view->homebottom_h2 = $homebottom_h2 = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.homebottom_h2', false);
	$this->view->homebottom_button = $homebottom_button = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.homebottom_button', false);
  }
}