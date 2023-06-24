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
class Sitestoreoffer_Widget_ProfileSitestoreoffersController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    
    $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));

    //GET SUBJECT
    $this->view->sitestore = $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

    //GET STORE ID
    $store_id = $subject->store_id;

    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->setNoRender();
      }
    } else {
      $storeOwnerBase = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'coupon');
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
      //return $this->_forward('requireauth', 'error', 'core');
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
    
    $var = 1;
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestoreoffer_offer')->getSitestoreoffersPaginator($store_id, $var,'',$can_create_offer);
    
    if(!empty($paginator)) {
			$count = $paginator->getTotalItemCount();
    }
    //GETTING TAB ID FROM CONTENT TABLE
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $store_id, $layout);

    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $isajax = $this->_getParam('isajax', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $store_id);
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;
      $this->view->statistics = $this->_getParam('statistics');
      //MAKE PAGINATOR
      $currentStoreNumber = $this->_getParam('store', 1);
      $var = 1;
      $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestoreoffer_offer')->getSitestoreoffersPaginator($store_id, $var,'',$can_create_offer);
      $this->_childCount = $paginator->getTotalItemCount();
      $paginator->setItemCountPerPage(10)->setCurrentPageNumber($currentStoreNumber);
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
      $title_count = $this->_getParam('titleCount', false);
      $var = 1;
      $show_count = 1;
      $paginator = Engine_Api::_()->getItemTable('sitestoreoffer_offer')->getSitestoreoffersPaginator($subject->store_id, $var, $show_count,$can_create_offer);
      $this->_childCount = $paginator->getTotalItemCount();
    }

    // START: "SUGGEST TO FRIENDS" LINK WORK.
    $store_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    if( !empty($is_suggestion_enabled) ) {
      $this->view->is_moduleEnabled = $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
		  $isModuleInfo = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion');
      $isSupport = Engine_Api::_()->getApi('suggestion', 'sitestore')->isSupport();
      // HERE WE ARE DELETE THIS POLL SUGGESTION IF VIEWER HAVE.

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
	if ($sitestore->expiration_date <= date("Y-m-d H:i:s")) {
	  $store_flag = 1;
	}
      }

      if (!empty($viewer_id) && !empty($isSupport) && empty($sitestore->closed) && !empty($sitestore->approved) && empty($sitestore->declined) && !empty($sitestore->draft) && empty($store_flag) && !empty($is_suggestion_enabled) && ($isModuleInfo->version >= '4.1.8p2')) {
	$this->view->offerSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitestore', 'offer_sugg_link');
      }
    }
    // END: "SUGGEST TO FRIENDS" LINE WORK.
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
?>