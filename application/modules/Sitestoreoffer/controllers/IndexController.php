<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http:setOfferPackages//www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_IndexController extends Seaocore_Controller_Action_Standard {

  protected $_session;

  //ACTION FOR MANAGING OFFERS
  public function indexAction() {
    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIAGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    $this->view->viewer = Engine_Api::_()->user()->getViewer();

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $can_edit = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $can_offer = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      $can_offer = 0;
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //END MANAGE-ADMIN CHECK
    //OFFER CREATION AUTHENTICATION CHECK
    if ($can_edit == 1 && $can_offer == 1) {
      $this->view->can_create_offer = 1;
    } else if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //SEND TAB ID TO TPL
    $this->view->tab_selected_id = $this->_getParam('tab', null);

    //MAKE PAGINATOR
    $currentStoreNumber = $this->_getParam('store', 1);
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestoreoffer_offer')->getSitestoreoffersPaginator($store_id);
    if (!empty($paginator)) {
      $paginator->setItemCountPerPage(50)->setCurrentPageNumber($currentStoreNumber);
    }
    $this->view->count = count($paginator);
  }

  //ACTION FOR CREATE OFFER
  public function createAction() {
    
    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $store_id = $this->_getParam('store_id');
    $max_creation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.max.limit', 0);

    $sitestoreoffersTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $couponCount = $sitestoreoffersTable->getStoreOfferCount($store_id);
    $session = new Zend_Session_Namespace();
    if (isset($session->image_path)) {
      unset($session->image_path);
      // IF ANY IMAGE IS CREATE, IT WILL REMOVE THERE
      if (isset($session->photoName_Temp)) {
        unset($session->photoName_Temp);
      }
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID AND STORE OBJECT
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    //$this->view->enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->offer_store = $offer_store = $this->_getParam('store_offer');
    $this->view->store_offer = $offer_store = $this->_getParam('store_offer');

    if (!empty($max_creation_limit) && !empty($couponCount) && ($couponCount >= $max_creation_limit)) {
      $this->view->coutErrorMessage = true;
      return;
    }

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $sitestoreoffer_getInfo = Zend_Registry::isRegistered('sitestoreoffer_getInfo') ? Zend_Registry::get('sitestoreoffer_getInfo') : null;
    if (empty($sitestoreoffer_getInfo)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $sitestoreModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    $getPackageOffer = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreoffer');
    $store_offer = Engine_Api::_()->getItemtable('sitestoreoffer_offer')->getOfferList();
    $this->view->tab_selected_id = $this->_getParam('tab');

    //FORM GENERATION
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->view->form = $form = new Sitestoreoffer_Form_Create();
    } else {
      $this->view->form = $form = new Sitestoreoffer_Form_SitemobileCreate();
    }

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $form->addElement("dummy", "dummy", array('label' => 'Coupon Picture', 'description' => 'Sorry, the 
browser you are using does not support Photo uploading. We recommend you to create an Coupon from your mobile / tablet without uploading a main photo for it. You can later upload the coupon picture from your Desktop.', 'order' => 4, 'style' => 'display:none;'));

      if (isset($form->photo))
        $form->photo->setAttrib('accept', "image/*");
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      if (empty($store_offer)) {
        return;
      }
      $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.set.type', 0);
      if (empty($isModType)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreoffer.offer.type', convert_uuencode($sitestoreModHostName));
      }

      $db = $sitestoreoffersTable->getAdapter();
      $db->beginTransaction();

      //GET POSTED VALUES FROM CREATE FORM
      $values = empty($getPackageOffer) ? null : $form->getValues();
      if (empty($values)) {
        return;
      }
      
//      if (empty($this->view->enable_url)) {
//        $values['url'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer_manifestUrl', 'siteoffer-offer');
//      }
      $values['discount_amount'] = !empty($values['discount_type']) ? $values['price'] : $values['rate'];
      
      if ($values['claim_count'] == 0) {
        $values['claim_count'] = -1;
      }
     
      if ($values['end_settings'] == 0) {
        $values['end_time'] = '0000-00-00 00:00:00';
      }

      $values['approved'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.approve', 1);

      try {

        //CREATE OFFER
        $sitestoreofferRow = $sitestoreoffersTable->createRow();
        $sitestoreofferRow->setFromArray($values);
        $sitestoreofferRow->discount_amount = $values['discount_amount'];
        $sitestoreofferRow->claim_count = $values['claim_count'];
        $sitestoreofferRow->claim_user_count = $values['claim_user_count'];
        $sitestoreofferRow->store_id = $store_id;
        $sitestoreofferRow->owner_id = $viewer->getIdentity();
        $sitestoreofferRow->save();
        $db->commit();

        //ADD PHOTO
        if (!empty($values['photo'])) {
          $sitestoreofferRow->setPhoto($form->photo);
        }
        $activityFeedType = null;
        if (Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
          $activityFeedType = 'sitestoreoffer_admin_new';
        else
          $activityFeedType = 'sitestoreoffer_new';
        if ($activityFeedType) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestore, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
        }

        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitestoreofferRow);
        }


        //COMMENT PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitestoreofferRow, $role, 'comment', ($i <= $commentMax));
        }

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

          $offer_array = array();
          $offer_array['type'] = 'sitestoreoffer_new';
          $offer_array['object'] = $sitestoreofferRow;

          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($offer_array);
        }

        //STORE OFFER CREATE NOTIFICATION AND EMAIL WORK
        $sitestoreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')->version;
        if (!empty($action)) {
          Engine_Api::_()->sitestore()->sendNotificationEmail($sitestoreofferRow, $action, 'sitestoreoffer_create', 'SITESTOREOFFER_CREATENOTIFICATION_EMAIL');

          $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $store_id);
          if (!empty($isStoreAdmins)) {
            //NOTIFICATION FOR ALL FOLLWERS.
            Engine_Api::_()->sitestore()->sendNotificationToFollowers($sitestoreofferRow, $action, 'sitestoreoffer_create');
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      if (!empty($offer_store)) {
        return $this->_gotoRouteCustom(array('action' => 'index'));
      } else {
        return $this->_redirectCustom($sitestoreofferRow->getHref(), array('prependBase' => false));
      }
    }
  }

  //ACTION FOR EDIT OFFER
  public function editAction() {

    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->offer_store = $offer_store = $this->_getParam('offer_store');
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET OFFER OBJECT
    $this->view->offer_id = $this->_getParam('offer_id');
    $this->view->sitestoreoffer = $sitestoreoffers = Engine_Api::_()->getItem('sitestoreoffer_offer', $this->_getParam('offer_id'));

    //GET STORE OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffers->store_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    $this->view->enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //FORM GENERATION
    $this->view->form = $form = new Sitestoreoffer_Form_Edit();

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $form->addElement("dummy", "dummy", array('label' => 'Coupon Picture', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to edit an Coupon from your mobile / tablet without uploading a main photo for it. You can later upload the coupon picture from your Desktop.', 'order' => 4, 'style' => 'display:none;'));

      if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (isset($form->photo)) {
          $form->removeElement('photo');
        }
      } else {
        if (isset($form->photo)) {
          $form->photo->setAttrib('accept', "image/*");
        }
      }
    }

    $date = (string) date('Y-m-d');
    $sitestoreoffers->end_time = $sitestoreoffers->end_time;
    
      $tempMappedIds = $sitestoreoffers->product_ids;
      if (!empty($tempMappedIds)) {
        $productMappedIds = $tempMappedIds = explode(',', $tempMappedIds);//Zend_Json_Decoder::decode($tempMappedIds);
        $productsArray = $tempProductsArray = array();

        foreach ($tempMappedIds as $tempIdsKey => $product_id) {
          $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
          if( empty($productTitle) ) {
            unset($productMappedIds[$tempIdsKey]);
            continue;
          }
          $tempProductsArray['title'] = $productTitle;
          $tempProductsArray['id'] = $product_id;
          $productsArray[] = $tempProductsArray;
        }
        
        $this->view->tempMappedIdsStr = @implode(",", $productMappedIds);
        $this->view->productArray = $productsArray;
      }

    if ($sitestoreoffers->claim_count == 0) {
      $form->getElement('claim_count')
              ->setIgnore(true)
              ->setAttrib('disable', true)
              ->clearValidators()
              ->setRequired(false)
              ->setAllowEmpty(true)
      ;
    }

    //SHOW PRE-FIELD FORM
    if ($sitestoreoffers->claim_count == -1) {
      $sitestoreoffers->claim_count = '';
    }
    
    if($sitestoreoffers->minimum_purchase == 0)
      $sitestoreoffers->minimum_purchase = 0;
    elseif (empty($sitestoreoffers->minimum_purchase))
      $sitestoreoffers->minimum_purchase = '';
    
    if($sitestoreoffers->claim_user_count == 0)
      $sitestoreoffers->claim_user_count = 0;
    elseif (empty($sitestoreoffers->claim_user_count))
      $sitestoreoffers->claim_user_count = '';

    if($sitestoreoffers->min_product_quantity == 0)
      $sitestoreoffers->min_product_quantity = 0;
    elseif (empty($sitestoreoffers->min_product_quantity))
      $sitestoreoffers->min_product_quantity = '';

    if ($sitestoreoffers->end_settings == 0) {
      $date = (string) date('Y-m-d');
      $sitestoreoffers->end_time = $date . ' 00:00:00';
    }
    $form->populate($sitestoreoffers->toArray());
    $offerarray = $sitestoreoffers->toArray();
    $form->price->setValue($offerarray['discount_amount']);
    $form->rate->setValue($offerarray['discount_amount']);
//    $tempMappedIds = explode(',', $offerarray['product_ids']);
//        foreach ($tempMappedIds as $product_id) {
//          $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
//          if( empty($productTitle) )
//            continue;
//          $form->product_ids->setValue($productTitle);
//        }
    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($sitestoreoffers->toArray());
      return;
    }

    if($this->getRequest()->isPost())
    {
      if(!empty($_POST['discount_type']))
           $form->rate->setValidators(array());
     else
           $form->price->setValidators(array());
    }
    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
   
    //GET FORM VALUES
    $values = $form->getValues();
//    if (empty($this->view->enable_url)) {
//      $values['url'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer_manifestUrl', 'siteoffer-ofefr');
//    }

    $values['discount_amount'] = !empty($values['discount_type']) ? $values['price'] : $values['rate'];
    if (isset($values['claim_count']) && $values['claim_count'] == 0) {
      $values['claim_count'] = -1;
    }
      
    if ($values['end_settings'] == 0) {
      $values['end_time'] = '0000-00-00 00:00:00';
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestoreoffers->discount_amount = $values['discount_amount'];
      $sitestoreoffers->claim_count = $values['claim_count'];
      $sitestoreoffers->claim_user_count = $values['claim_user_count'];
      $sitestoreoffers->setFromArray($values);
      $sitestoreoffers->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if (!empty($offer_store)) {
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format' => 'smoothbox',
          'messages' => Zend_Registry::get('Zend_Translate')->_('Your coupon has been edit successfully.')
      ));
    } else {
      return $this->_redirectCustom($sitestoreoffers->getHref(), array('prependBase' => false));
    }
  }

  //ACTION FOR DELETE OFFER
  public function deleteAction() {

    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->offer_store = $offer_store = $this->_getParam('offer_store');
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    //GET OFFER ID AND OFFER OBJECT
    $this->view->offer_id = $offer_id = $this->_getParam('offer_id');
    $sitestoreoffers = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

    //GET STORE OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffers->store_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        Engine_Api::_()->sitestoreoffer()->deleteContent($sitestoreoffers->offer_id);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      if (!empty($offer_store)) {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 300,
            'parentRefresh' => 300,
            'messages' => array('Your coupon has been deleted successfully.')
        ));
      } else {
        return $this->_gotoRouteCustom(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestoreoffers->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
      }
    }
  }

  public function stickyAction() {

    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id');
    $offer_id = $this->_getParam('offer_id');
    $offer_store = $this->_getParam('offer_store');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
    $this->view->sticky = $sitestoreoffer->sticky;
    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //POST DATA
    if ($this->getRequest()->isPost()) {

      Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->makeSticky($offer_id, $store_id);
      if (!empty($offer_store)) {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('')
        ));
      } else {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $this->_getParam('tab')), 'sitestore_entry_view', true),
            'parentRedirectTime' => '2',
            'messages' => array('')
        ));
      }
    }
  }

  public function browseAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR PRINTING THE OFFER
  public function printAction() {

    $this->_helper->layout->setLayout('default-simple');

    //GET OFFER ID AND OFFER OBJECT
    $offer_id = $this->_getParam('offer_id', null);
    $this->view->sitestore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id', null));
    $this->view->sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
    if (empty($offer_id))
      return $this->_forwardCustom('notfound', 'error', 'core');
  }

  //ACTION FOR PRINTING THE OFFER
  public function previewAction() {

    $session = new Zend_Session_Namespace();
    $this->view->discount_amount = $this->_getParam('discount_amount');
    $this->view->minimum_purchase = $this->_getParam('minimum_purchase');
    if (isset($session->image_path)) {
      $this->view->image_path = $session->image_path;
    } else {
      $this->view->image_path = 'application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png';
    }
  }

  public function uploadAction() {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $file = $_FILES["photo"]["tmp_name"];
    $file1 = $_FILES["photo"]["name"];

    $name = basename($file1);

    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/sitestoreoffer_offer';

    $storage = Engine_Api::_()->storage();

    //RESIZE IMAGE (MAIN)
    $image = Engine_Image::factory();
    $image->open($file);
    //IMAGE WIDTH
    $dstW = $image->width;
    // IMAGE HIGHT
    $dstH = $image->height;

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 140, 160)
            ->write($path . '/' . $name)
            ->destroy();

    $photoName = $this->view->baseUrl() . '/public/sitestoreoffer_offer/' . $name;

    $session = new Zend_Session_Namespace();
    $session->image_path = $photoName;
    $session->photoName_Temp = $path . '/' . $name;

    @chmod($this->_session->photoName_Temp, 0777);
  }

  //ACTION FOR VIEW OFFER
  public function viewAction() {

    //IF SITESTOREOFFER SUBJECT IS NOT THEN RETURN
//     if (!$this->_helper->requireSubject('sitestoreoffer_offer')->isValid())
//       return;
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (!Zend_Registry::isRegistered('sitemobileNavigationName')) {
        Zend_Registry::set('sitemobileNavigationName', 'setNoRender');
      }
    }

    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $this->getRequest()->getParam('offer_id'));
    if ($sitestoreoffer) {
      Engine_Api::_()->core()->setSubject($sitestoreoffer);
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET OFFER ITEM
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $this->getRequest()->getParam('offer_id'));

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);


    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    $can_offer = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      $can_offer = 0;
    }

    $can_create_offer = '';
    //OFFER CREATION AUTHENTICATION CHECK
    if ($can_edit == 1 && $can_offer == 1) {
      $this->view->can_create_offer = $can_create_offer = 1;
    }

    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE OFFER OR NOT
//     if ($viewer_id != $sitestoreoffer->owner_id && $can_edit != 1 && ($sitestoreoffer->search != 1 || $sitestoreoffer->status != 1)) {
//       return $this->_forwardCustom('requireauth', 'error', 'core');
//     }
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR SEND CLAIM OFFER MAIL
//  public function getofferAction() {
//
//    $param = $this->_getParam('param');
//    $request_url = $this->_getParam('request_url');
//    $return_url = $this->_getParam('return_url');
//    $front = Zend_Controller_Front::getInstance();
//    $base_url = $front->getBaseUrl();
//
//    // CHECK USER VALIDATION
//    if (!$this->_helper->requireUser()->isValid()) {
//      $host = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
//      if ($base_url == '') {
//        $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/login';
//      } else {
//        $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/' . $request_url . '/login';
//      }
//      if (empty($param)) {
//        return $this->_helper->redirector->gotoUrl($URL_Home, array('prependBase' => false));
//      } else {
//        return $this->_helper->redirector->gotoUrl($URL_Home . '?return_url=' . urlencode($return_url), array('prependBase' => false));
//      }
//    }
//
//    $offer_id = $this->_getParam('id');
//    //GET OFFER OBJECT
//    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
//    $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);
//
//    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestoreObject, 'view');
//    if (empty($isManageAdmin)) {
//      $this->view->private_message = 1;
//    } else {
//      //GET LOGGED IN USER INFORMATION
//      $viewer = Engine_Api::_()->user()->getViewer();
//
//      $claim_value = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer')->getClaimValue($viewer->getIdentity(), $offer_id, $sitestoreoffer->store_id);
//
//      $this->view->offer_id = $offer_id;
//
//      if (!empty($claim_value)) {
//        $this->renderScript('index/resendoffer.tpl');
//      } else {
//
//        //GET THE TAB ID OF OFFER ON STORE PROFILE
//        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
//        $offer_tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestoreoffer->store_id, $layout);
//
//        //STORE URL
//        $store_url = Engine_Api::_()->sitestore()->getStoreUrl($sitestoreObject->store_id);
//
//        $data['storehome_offer'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble('', 'sitestoreoffer_home', true), $this->view->translate('View More Coupons'), array('style' => 'color:#3b5998;text-decoration:none;margin-left:10px;', 'target' => '_blank'));
//        if ($sitestoreObject->photo_id) {
//          $data['store_photo_path'] = $sitestoreObject->getPhotoUrl('thumb.icon');
//        } else {
//          $data['store_photo_path'] = 'application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png';
//        }
//
//        $data['share_offer'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title), 'share_offer' => 1), 'sitestoreoffer_view', true), $this->view->translate('Share Coupon'), array('style' => 'text-decoration:none;font-weight:bold;color:#fff;font-size:11px;', 'target' => '_blank'));
//
//        $data['like_store'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true), $this->view->translate('Like') . ' ' . $sitestoreObject->getTitle(), array('style' => 'color:#3b5998;text-decoration:none;margin-right:10px;margin-left:10px;', 'target' => '_blank'));
//
//        $this->view->store_title = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true), $sitestoreObject->getTitle(), array('target' => '_blank'));
//
//        $data['store_title'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true), $sitestoreObject->getTitle(), array('target' => '_blank', 'style' => 'color:#3b5998;text-decoration:none;'));
//
//        if ($sitestoreoffer->photo_id) {
//          $data['offer_photo_path'] = $sitestoreoffer->getPhotoUrl('thumb.icon');
//        } else {
//          $data['offer_photo_path'] = 'application/modules/Sitestoreoffer/externals/images/offer_thumb.png';
//        }
//
//        $data['site_title'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);
//
//        $data['offer_title'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
//                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title)), 'sitestoreoffer_view', true), $sitestoreoffer->title, array('style' => 'color:#3b5998;text-decoration:none;'));
//
//        $data['coupon_code'] = $sitestoreoffer->coupon_code;
//        $data['offer_url'] = $sitestoreoffer->url;
//        $data['offer_description'] = $sitestoreoffer->description;
//        $data['offer_time'] = gmdate('M d, Y', strtotime($sitestoreoffer->end_time));
//        $data['offer_time_setting'] = $sitestoreoffer->end_settings;
//        $data['claim_owner_name'] = Engine_Api::_()->user()->getViewer()->username;
//        $data['enable_mailtemplate'] = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
//
//        // INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
//        $template_header = "";
//        $template_footer = "";
//        $string = '';
//        $string = $this->view->offermail($data);
//
//        $sitestoreofferClaimTable = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer');
//
//        $db = Engine_Db_Table::getDefaultAdapter();
//        $db->beginTransaction();
//        try {
//
//          //CREATE CLAIM FOR OFFER
//          $sitestoreofferRow = $sitestoreofferClaimTable->createRow();
//          $sitestoreofferRow->owner_id = $viewer->getIdentity();
//          $sitestoreofferRow->store_id = $sitestoreoffer->store_id;
//          $sitestoreofferRow->offer_id = $sitestoreoffer->offer_id;
//          $sitestoreofferRow->claim_value = 1;
//          $sitestoreofferRow->save();
//
//          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestoreoffer, 'sitestoreoffer_home');
//
//          if ($action != null) {
//            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitestoreoffer);
//          }
//
//          $db->commit();
//        } catch (Exception $e) {
//          $db->rollBack();
//          throw $e;
//        }
//
//        $subject = $this->view->translate('Your ') . $data['site_title'] . $this->view->translate(' coupon from ') . $sitestoreObject->title;
//
//        // SEND MAIL CLAIM OFFER
//        $email = Engine_Api::_()->user()->getViewer()->email;
//        $email_admin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
//        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'offer_claim', array(
//            'subject' => $subject,
//            'template_header' => $template_header,
//            'message' => $string,
//            'template_footer' => $template_footer,
//            'email' => $email_admin,
//            'queue' => false));
//
//        $today = date("Y-m-d H:i:s");
//
//        if ($sitestoreoffer->claim_count > 0) {
//          $sitestoreoffer->claim_count--;
//        }
//        $sitestoreoffer->claimed++;
//        $sitestoreoffer->save();
//        $claim_count = $sitestoreoffer->claim_count;
//        $offer_id = $sitestoreoffer->offer_id;
//
//        if (($claim_count == 0) && $sitestoreoffer->end_settings == 1 && $sitestoreoffer->end_time < $today) {
//          $sitestoreofferClaimTable->deleteClaimOffers($offer_id);
//        }
//      }
//    }
//  }
  //ACTION FOR RESEND OFFER CLAIM MAIL
  public function resendofferAction() {
    $offer_id = $this->_getParam('id', null);

    if(!empty($offer_id))
     $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

    if(!empty($sitestoreoffer)){
      $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestoreObject, 'view');
    }
    
    if (empty($sitestoreoffer) || empty($isManageAdmin))
      $this->view->private_message = true;
    else 
      $this->view->offer_id = $offer_id;
  }

  public function sendofferAction() {

    $this->_helper->layout->setLayout('default-simple');
    $this->view->offer_id = $offer_id = $this->_getParam('id');

    $data = array();

    //GET OFFER OBJECT
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

    //GET THE TAB ID OF OFFER FOR STORE PROFILE
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $offer_tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestoreoffer->store_id, $layout);

    $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', $sitestoreoffer->store_id);

    //STORE URL
    $store_url = Engine_Api::_()->sitestore()->getStoreUrl($sitestoreObject->store_id);

    $data['storehome_offer'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'sitestoreoffer_home', true), $this->view->translate('View More Coupons'), array('style' => 'color:#3b5998;text-decoration:none;margin-left:10px; ', 'target' => '_blank'));

    $data['share_offer'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title), 'share_offer' => 1), 'sitestoreoffer_view', true), $this->view->translate('Share Coupon'), array('style' => 'text-decoration:none;font-weight:bold;color:#fff;font-size:11px;', 'target' => '_blank'));

    $data['like_store'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true), $this->view->translate('Like') . ' ' . $sitestoreObject->getTitle(), array('style' => 'color:#3b5998;text-decoration:none;margin-right:10px;margin-left:10px;', 'target' => '_blank'));

    if ($sitestoreObject->photo_id) {
      $data['store_photo_path'] = $sitestoreObject->getPhotoUrl('thumb.icon');
    } else {
      $data['store_photo_path'] = 'application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png';
    }

    $data['store_title'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true), $sitestoreObject->getTitle(), array('style' => 'color:#3b5998;text-decoration:none;'));

    if ($sitestoreoffer->photo_id) {
      $data['offer_photo_path'] = $sitestoreoffer->getPhotoUrl('thumb.icon');
    } else {
      $data['offer_photo_path'] = 'application/modules/Sitestoreoffer/externals/images/offer_thumb.png';
    }

    $data['site_title'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);

    $data['offer_title'] = $this->view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title)), 'sitestoreoffer_view', true), $sitestoreoffer->title, array('style' => 'color:#3b5998;text-decoration:none;'));
    $data['coupon_code'] = $sitestoreoffer->coupon_code;
    $data['offer_url'] = $sitestoreoffer->url;
    $data['offer_description'] = $sitestoreoffer->description;
    $data['offer_time'] = gmdate('M d, Y', strtotime($sitestoreoffer->end_time));
    $data['offer_time_setting'] = $sitestoreoffer->end_settings;
    $data['claim_owner_name'] = Engine_Api::_()->user()->getViewer()->username;
    $data['enable_mailtemplate'] = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    //INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
    $template_header = "";
    $template_footer = "";
    $string = '';
    $string = $this->view->offermail($data);

    $this->view->store_title = $sitestoreObject->title;

    $subject = $this->view->translate('Your ') . $data['site_title'] . $this->view->translate(' coupon from ') . $sitestoreObject->title;

    // SEND MAIL CLAIM OFFER
    $email = Engine_Api::_()->user()->getViewer()->email;
    $email_admin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'offer_claim', array(
        'subject' => $subject,
        'template_header' => $template_header,
        'message' => $string,
        'template_footer' => $template_footer,
        'email' => $email_admin,
        'queue' => false));
  }

  // ACTION FOR FEATURED NOTES CAROUSEL AFTER CLICK ON BUTTON 
  public function hotOffersCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];
    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    $params = array();
    $params['category_id'] = $_GET['category_id'];
    $hotOffer = 1;
    $params['offertype'] = 'hotoffer';

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $params['start_index'] = $startindex;

    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $params['limit'] = $limit * 2;
    $this->view->hotOffers = $this->view->hotOffers = $hotOffers = Engine_Api::_()->getDbTable('offers', 'sitestoreoffer')->getOffers($hotOffer, $params);

    //Pass the total number of result in tpl file
    $this->view->count = count($hotOffers);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

  public function homeAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR ADDING OFFER OF THE DAY
  public function addOfferOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitestoreoffer_Form_ItemOfDayday();
    $offer_id = $this->_getParam('offer_id');
    // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $offer_id)->where('resource_type = ?', 'sitestoreoffer_offer');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $offer_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitestoreoffer_offer';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Coupon of the Day has been added successfully.'))
      ));
    }
  }

  public function couponUrlValidationAction() {
    $coupon_code = $this->_getParam('coupon_code');
    $offer_id = $this->_getParam('offer_id', 0);

    $staticBaseUrl = Zend_Registry::get('Zend_View')->layout()->staticBaseUrl;

    if (empty($coupon_code)) {
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/cross.png"/>' . $this->view->translate('Coupon Code is not available.') . '</span>'));
      exit();
    }

    $isCouponExist = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getCouponInfo(array('coupon_code' => $coupon_code, 'fetchColumn' => 1), array('offer_id', 'store_id', 'product_ids', 'minimum_purchase', 'discount_type', 'discount_amount', 'start_time', 'end_time', 'end_settings', 'status', 'min_product_quantity', 'approved', 'claim_count', 'claimed', 'claim_user_count'));

    if (!empty($isCouponExist)) {
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/cross.png"/>' . $this->view->translate('Coupon Code is not available.') . '</span>'));
      exit();
    } else {
      $success_message = Zend_Registry::get('Zend_Translate')->_("Coupon Code is Available.");
      echo Zend_Json::encode(array('success' => 1, 'success_msg' => '<span style="color:green;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/tick.png"/>' . $success_message . '</span>'));
      exit();
    }
  }

  public function suggestproductsAction() {
    $owner_id = $this->_getParam('owner_id', '');
    $text = $this->_getParam('search', $this->_getParam('value'));
    $store_ids = $this->_getParam('store_ids', null);
    $store_id = $this->_getParam('store_id', null);
    $product_ids = $this->_getParam('product_ids', null);
    $limit = $this->_getParam('limit', 40);
    $productObjects = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsByText($store_ids, $text, $limit, $product_ids, $owner_id);
    $data = array();
    foreach ($productObjects as $products) {
      $data[] = array(
          'id' => $products->product_id,
          'label' => $products->title,
          'price' => $products->price,
          'product_type' => $products->product_type,
          'photo' => $this->view->itemPhoto($products, 'thumb.icon'),
      );
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  public function enableDisableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id', null);
    $offer_id = $this->_getParam('offer_id', null);
    $tab_id = $this->_getParam('tab', null);
    $this->view->status = $this->_getParam('status', null);

    if (empty($store_id) || empty($offer_id))
      return $this->_forwardCustom('requireauth', 'error', 'core');

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->changeStatus($offer_id, $store_id);
      if (empty($tab_id)) {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('')
        ));
      } else {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $this->_getParam('tab')), 'sitestore_entry_view', true),
            'parentRedirectTime' => '2',
            'messages' => array('')
        ));
      }
    }
  }

  public function applyCouponAction() {
    
    $coupon_code = $this->_getparam('coupon_code');
    $cart_store_id = $this->_getparam('store_id');
    $cart_info = json_decode($this->_getparam('cart_info'), true);
    if (empty($coupon_code) || empty($cart_info)) {
      $this->view->coupon_error_msg = $this->view->translate("Please enter correct coupon code.");
      return;
    }

    $offer_detail = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getCouponInfo(array('coupon_code' => $coupon_code, 'fetchRow' => 1), array('offer_id', 'store_id', 'product_ids', 'minimum_purchase', 'discount_type', 'discount_amount', 'start_time', 'end_time', 'end_settings', 'status', 'min_product_quantity', 'approved', 'claim_count', 'claimed', 'claim_user_count'));
    $isCouponExist = @COUNT($offer_detail);
    $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
      if (!empty($session->sitestoreproductCartCouponDetail)) {
        $coupon_details = unserialize($session->sitestoreproductCartCouponDetail);
        if($coupon_details[$offer_detail->store_id]['coupon_name'] == $coupon_code)
        {
          $this->view->coupon_error_msg = $this->view->translate("You have already applied this coupon.");
          return;
        }
      }
    if (empty($isCouponExist) || empty($offer_detail->approved) || empty($offer_detail->status) || (date("Y-m-d H:i:s") < $offer_detail->start_time)||($offer_detail->end_settings == 1 && $offer_detail->end_time < date("Y-m-d H:i:s")) || ( ($offer_detail->claim_count != -1) && ($offer_detail->claimed >= $offer_detail->claim_count) ) || (!empty($cart_store_id) && $offer_detail->store_id != $cart_store_id)) {
      $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or expired", $coupon_code);
      return;
    }
    if(!empty($offer_detail->claim_user_count)){
    $uses_count = Engine_Api::_()->getDbtable('ordercoupons', 'sitestoreoffer')->getOrderCouponCount($offer_detail->offer_id, $offer_detail->store_id);
    
    if($uses_count >= $offer_detail->claim_user_count){
      $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or expired", $coupon_code);
      return;
    }
    } 
    $cart_info = $cart_info[$offer_detail->store_id];
    if (empty($cart_info)) {
      $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as %s is either invalid or expired", $coupon_code);
      return;
    }

    $discount_amount = $total_product_price = $directPayment = 0;
    $siteadminBasePayment = true;
    $store_product_ids = array_keys($cart_info['product_ids']); // Have Product Id's, which are in cart for respective store.
    $store_product_ids_price = $cart_info['product_ids']; // Have Product Id's with price, which are in cart for respective store.
    $product_count = $cart_info['qty']; // Total cart quantity for respective store.
    $store_products_sub_total = $cart_info['sub_total']; // Total cart sub-total for respective store.
    $product_store_ids = array();

    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if (empty($isAdminDrivenStore)) {
      $siteadminBasePayment = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if (empty($siteadminBasePayment))
        $directPayment = 1;
    }
    $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

    // IF COUPON APPLY ONLY ON THE SELECTED PRODUCTS.    
    if (!empty($offer_detail->product_ids)) {
      $mappedProductIds = explode(',', $offer_detail->product_ids);
      foreach ($store_product_ids as $product_id) {
        if (!in_array($product_id, $mappedProductIds))
          unset($store_product_ids_price[$product_id]);
      }

      if (empty($store_product_ids_price)) {
        $this->view->coupon_error_msg = $this->view->translate("Please enter a different coupon code as" .' '. $coupon_code . ' '. "is either invalid or expired.");
        return;
      }
    }

    foreach ($store_product_ids_price as $product_id => $product_info) {
      if ((!empty($siteadminBasePayment) || !empty($directPayment)) && empty($isDownPaymentEnable))
        $total_product_price += $product_info['sub_total'];

      elseif ((!empty($siteadminBasePayment) || !empty($directPayment)) && !empty($isDownPaymentEnable))
        $total_product_price += Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $product_info['sub_total']));
    }
    
    if (!empty($offer_detail->minimum_purchase) && $store_products_sub_total < $offer_detail->minimum_purchase) {
      $this->view->coupon_error_msg = $this->view->translate("Cart total amount should be atleast %s", Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($offer_detail->minimum_purchase));
      return;
    }

    if (!empty($offer_detail->min_product_quantity) && $product_count < $offer_detail->min_product_quantity) {
      $this->view->coupon_error_msg = $this->view->translate("Cart total product count should be atleast %s", $offer_detail->min_product_quantity);
      return;
    }

       $credit_session = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
        if (!empty($credit_session->sitestoreproductCartCreditDetail)) {
            $credit_session->sitestoreproductCartCreditDetail = null;
        } 
    // FIXED TYPE
    $discount_amount = $total_product_price;
    if (!empty($offer_detail->discount_type)) {
      if ($discount_amount > $offer_detail->discount_amount)
        $discount_amount = $offer_detail->discount_amount;
    }else { // IN THE CASE OF PERCENT
      $coupon_amount = ($discount_amount * $offer_detail->discount_amount) / 100;
      if ($discount_amount > $coupon_amount)
        $discount_amount = $coupon_amount;
    }

    $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
    if (!empty($directPayment)) {
      $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
      if (!empty($session->sitestoreproductCartCouponDetail)) {
        $coupon_details = $session->sitestoreproductCartCouponDetail;
        $coupon_details = unserialize($coupon_details);
        if (array_key_exists($offer_detail->store_id, $coupon_details))
          unset($coupon_details[$offer_detail->store_id]);

//          $coupon_details_array[$offer_detail->store_id] = array('coupon_name' => $coupon_code, 'coupon_amount' => $discount_amount, 'store_id' => $offer_detail->store_id);
//          $session->sitestoreproductCartCouponDetail = serialize($coupon_details_array);
      }
    }
    else {
      $session->sitestoreproductCartCouponDetail = array();
//        $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
//        if (!empty($session->sitestoreproductCartCouponDetail))
//          $session->sitestoreproductCartCouponDetail = null;
    }

//      if (!empty($session->sitestoreproductCartCouponDetail))
//        $session->sitestoreproductCartCouponDetail = null;
    $discount_amount = @round($discount_amount, 2);
    $coupon_details_array[$offer_detail->store_id] = array('coupon_name' => $coupon_code, 'coupon_amount' => $discount_amount, 'store_id' => $offer_detail->store_id);
    $session->sitestoreproductCartCouponDetail = serialize($coupon_details_array);
    $this->view->cart_coupon_applied = true;
  }

}

?>