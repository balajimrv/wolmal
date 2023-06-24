<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_BannerCategoryController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->bannerImage = $this->_getParam('sesalbum_categorycover_photo');
		$this->view->description = $this->_getParam('description','');
		$this->view->title = $this->_getParam('title','');
  }
}