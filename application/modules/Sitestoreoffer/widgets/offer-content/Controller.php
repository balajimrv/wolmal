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
class Sitestoreoffer_Widget_OfferContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

     //GET OFFER ID AND OBJECT
    $this->view->offer_id = $offer_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('offer_id', $this->_getParam('offer_id', null));

    $this->view->share_offer = Zend_Controller_Front::getInstance()->getRequest()->getParam('share_offer', $this->_getParam('share_offer', null));

    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

     $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));

    
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    if (empty($sitestoreoffer)) {
      return $this->setNoRender();
    }

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    $getPackageofferView = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreoffer');

    //GET VIEWER INFO
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
    } 

    //IF THIS IS SENDING A MESSAGE ID, THE USER IS BEING DIRECTED FROM A CONVERSATION
    //CHECK IF MEMBER IS PART OF THE CONVERSATION
    $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;

    //SET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);

    //PACKAGE BASE PRIYACY START
//     if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
//       if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
//         return $this->setNoRender();
//       }
//     } else {
//       $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
//       if (empty($isStoreOwnerAllow)) {
//         return $this->setNoRender();
//       }
//     }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $can_offer = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      $can_offer = 0;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = $this->view->can_edit = 0;
    } else {
      $can_edit = $this->view->can_edit = 1;
    }

    $can_create_offer = '';
    //OFFER CREATION AUTHENTICATION CHECK
    if ($can_edit == 1 && $can_offer == 1) {
      $this->view->can_create_offer = $can_create_offer =  1;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }


//     if ($viewer_id != $sitestoreoffer->owner_id && $can_edit != 1 && ($sitestoreoffer->status != 1 || $sitestoreoffer->search != 1) || empty($getPackageofferView)) {
//       return $this->setNoRender();
//     }
    //END MANAGE-ADMIN CHECK
    
    //INCREMENT IN NUMBER OF VIEWS
      $owner = $sitestoreoffer->getOwner();
      if (!$owner->isSelf($viewer)) {
        $sitestoreoffer->view_count++;
      }
      $sitestoreoffer->save();
			
    //SET STORE-OFFER SUBJECT
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitestoreoffer);

    $this->view->offer = empty($getPackageofferView) ? null : $sitestoreoffer;
    
    //OFFER TABLE
    $offerTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');

    //TOTAL OFFER COUNT FOR THIS STORE
    $this->view->count_offer = $offerTable->getStoreOfferCount($sitestoreoffer->store_id);

    // START: "SUGGEST TO FRIENDS" LINK WORK.
    $store_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $this->view->is_moduleEnabled = $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
		$isModuleInfo = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitestore')->isSupport();
		
    // HERE WE ARE DELETE THIS POLL SUGGESTION IF VIEWER HAVE.

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
      if ($sitestore->expiration_date <= date("Y-m-d H:i:s")) {
        $store_flag = 1;
      }
    }

    if (!empty($viewer_id) && !empty($isSupport) && empty($sitestore->closed) && !empty($sitestore->approved) && empty($sitestore->declined) && !empty($sitestore->draft) && empty($store_flag) && !empty($is_suggestion_enabled) && ($isModuleInfo->version >= '4.1.7p2')) {
      $this->view->offerSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitestore', 'offer_sugg_link');
    }
    // END: "SUGGEST TO FRIENDS" LINE WORK.
  }

}
?>