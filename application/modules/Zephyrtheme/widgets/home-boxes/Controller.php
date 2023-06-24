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

class Zephyrtheme_Widget_HomeboxesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	// Number of boxes
    $this->view->home_icos = $home_icos = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icos', false);
	$this->view->home_icosimgs = $home_icosimgs = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icosimgs', false);
	// Box 1
	$this->view->home_ico_one = $home_ico_one = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_ico_one', false);
	$this->view->home_icoone_link = $home_ico_one_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icoone_link', false);
	// Box 2
	$this->view->home_ico_two = $home_ico_two = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_ico_two', false);
	$this->view->home_icotwo_link = $home_ico_two_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icotwo_link', false);
	// Box 3
	$this->view->home_ico_three = $home_ico_three = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_ico_three', false);
	$this->view->home_icothree_link = $home_ico_three_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icothree_link', false);
	// Box 4
	$this->view->home_ico_four = $home_ico_four = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_ico_four', false);
	$this->view->home_icofour_link = $home_ico_four_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_icofour_link', false);
  }
}