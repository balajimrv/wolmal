<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_OptionsSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER THIS IF NOT AUTHORIZED
    $sitestore_options = Zend_Registry::isRegistered('sitestore_options') ? Zend_Registry::get('sitestore_options') : null;
		if (empty($sitestore_options)) {
      return $this->setNoRender();
    }

		//DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET NAVIGATION
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_gutter');
  }
}

?>