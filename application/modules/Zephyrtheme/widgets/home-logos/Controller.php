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

class Zephyrtheme_Widget_HomelogosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Link Target
	$this->view->feat_linktarget = $feat_linktarget = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat_linktarget', false);
	
	// Company Logo
    $this->view->feat1_img = $feat1_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat1_img', false);
	$this->view->feat2_img = $feat2_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat2_img', false);
	$this->view->feat3_img = $feat3_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat3_img', false);
	$this->view->feat4_img = $feat4_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat4_img', false);
	$this->view->feat5_img = $feat5_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat5_img', false);
	$this->view->feat6_img = $feat6_img = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat6_img', false);
	
	// Company Link
    $this->view->feat1_link = $feat1_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat1_link', false);
	$this->view->feat2_link = $feat2_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat2_link', false);
	$this->view->feat3_link = $feat3_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat3_link', false);
	$this->view->feat4_link = $feat4_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat4_link', false);
	$this->view->feat5_link = $feat5_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat5_link', false);
	$this->view->feat6_link = $feat6_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat6_link', false);
	
	// Company Text
    $this->view->feat1_text = $feat1_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat1_text', false);
	$this->view->feat2_text = $feat2_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat2_text', false);
	$this->view->feat3_text = $feat3_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat3_text', false);
	$this->view->feat4_text = $feat4_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat4_text', false);
	$this->view->feat5_text = $feat5_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat5_text', false);
	$this->view->feat6_text = $feat6_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.feat6_text', false);
	
	$this->view->home_style = $home_style = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.home_style', false);
	
  }
}