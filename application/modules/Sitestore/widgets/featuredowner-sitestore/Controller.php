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
class Sitestore_Widget_FeaturedownerSitestoreController extends Seaocore_Content_Widget_Abstract {
  protected $_childCount;
  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    $manageadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    if (empty($isManageAdmin) || empty($manageadmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//FETCH FEATURED ADMIN
    $this->view->featuredowners = $featuredowners = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->featuredAdmins($sitestore->store_id);
		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			// Set item count per store and current store number
			$this->view->featuredowners = $featuredowners->setItemCountPerPage(5);
			$this->view->featuredowners = $featuredowners->setCurrentPageNumber($this->_getParam('store', 1));

			// Add count to title if configured
			if ($this->_getParam('titleCount', false) && $featuredowners->getTotalItemCount() > 0) {
				$this->_childCount = $featuredowners->getTotalItemCount();
			}

      if ($featuredowners->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}

    } else {
			if (!count($featuredowners)) {
				return $this->setNoRender();
			}
    }

  }

	public function getChildCount() {
    return $this->_childCount;
  }
}

?>