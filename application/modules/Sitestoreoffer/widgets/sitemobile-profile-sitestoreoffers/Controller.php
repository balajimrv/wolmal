<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Widget_SitemobileProfileSitestoreoffersController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET SUBJECT
    $this->view->sitestore = $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

    //GET STORE ID
    $store_id = $subject->store_id;
    
    $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));
    
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->setNoRender();
      }
    } else {
      $storeOwnerBase = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($storeOwnerBase)) {
        return $this->setNoRender();
      }
    }
    // PACKAGE BASE PRIYACY END     
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $can_edit = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    }

    $can_offer = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      $can_offer = 0;
    }
    //END MANAGE-ADMIN CHECK

    $can_create_offer = '';
    //OFFER CREATION AUTHENTICATION CHECK
    if ($can_edit == 1 && $can_offer == 1) {
      $this->view->can_create_offer = $can_create_offer =  1;
    }

    //TOTAL OFFER
    $offerCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestoreoffer', 'offers');     
    if (empty($can_create_offer) && empty($offerCount) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }       

		$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestoreoffer_offer')->getSitestoreoffersPaginator($store_id, 1,'',$can_create_offer);
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('store', 1));
    $this->_childCount = $paginator->getTotalItemCount();
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}