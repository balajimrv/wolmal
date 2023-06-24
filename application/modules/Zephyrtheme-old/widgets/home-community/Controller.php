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

class Zephyrtheme_Widget_HomecommunityController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->home_commimg = $home_commimg = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_commimg', false);
	$this->view->home_commimgshow = $home_commimgshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_commimgshow', false);
	$this->view->home_commbgimg = $home_commbgimg = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_commbgimg', false);
	$this->view->home_commh1 = $home_commh1 = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_commh1', false);
	$this->view->home_commh2 = $home_commh2 = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_commh2', false);
  }
}