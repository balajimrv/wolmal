<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_Widget_LocationSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $viewer = Engine_Api::_()->user()->getViewer();

    //check location map enable /disable
    $check_location = Engine_Api::_()->sitestore()->enableLocation();
		$likebox_location = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorelikebox.location', null);
    if (!Engine_Api::_()->core()->hasSubject() || !$check_location) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) || empty($likebox_location)) {
      return $this->setNoRender();
    }
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin) && empty($likebox_location)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    $value['id'] = $sitestore->getIdentity();
    $this->view->location = $location =  Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);
    if (empty($location) || empty($likebox_location)) {
      return $this->setNoRender();
    }
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.location-sitestore', $sitestore->store_id, $layout);
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $sitestore->store_id);
    $this->view->identity_temp = $this->view->identity;
  }
}
?>