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
class Sesalbum_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Get navigation menu
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesalbum_main');
    if (count($this->view->navigation) == 1) {
      $this->view->navigation = null;
    }
    $this->view->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.taboptions', 6);
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
      return $this->setNoRender();
  }
}