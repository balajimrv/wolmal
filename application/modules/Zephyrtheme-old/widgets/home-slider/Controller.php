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

class Zephyrtheme_Widget_HomesliderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $slider_items_table = Engine_Api::_()->getDbtable('sliderItems', 'zephyrtheme');
    $slider_items = $slider_items_table->fetchAll($slider_items_table->select());
    $this->view->slider_items = $slider_items;
	
	$this->view->header_loginbutton = $header_loginbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_loginbutton', false);
    $this->view->header_signupbutton = $header_signupbutton = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.header_signupbutton', false);
	
	$this->view->home_slider_height = $home_slider_height = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_slider_height', false);
	$this->view->home_slider_time = $home_slider_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_slider_time', false);
  }
}