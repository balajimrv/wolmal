<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Plugin_Menus {

  public function canViewOffers() {
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.offer.show.menu', 1)) {
      return false;
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0)) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();


    $table = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $rName = $table->info('name');
    $table_stores = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName_stores = $table_stores->info('name');
    $today = date("Y-m-d H:i:s");
    if (!empty($viewer_id)) {
      // Convert times
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $today = date("Y-m-d H:i:s");
      date_default_timezone_set($oldTz);
    }
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName_stores, array('photo_id', 'title as sitestore_title'))
            ->join($rName, $rName . '.store_id = ' . $rName_stores . '.store_id')
            ->where("($rName.end_settings = 1 AND $rName.end_time >= '$today' OR $rName.end_settings = 0) ");

    $select = $select
            ->where($rName_stores . '.closed = ?', '0')
            ->where($rName_stores . '.approved = ?', '1')
            ->where($rName_stores . '.search = ?', '1')
            ->where($rName_stores . '.declined = ?', '0')
            ->where($rName_stores . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($rName_stores . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

  //SITEMOBILE STORE ALBUM MENUS
  public function onMenuInitialize_SitestoreofferAdd($row) {

    $can_create_offer = $this->commonChecks();

    //CHECKS FOR ADD OFFER
    if (empty($can_create_offer)) {
      return false;
    }

    $sitestoreoffer = $this->getSitestoreOfferObject();

    if (empty($sitestoreoffer)) {
      return false;
    }

    $store_id = $sitestoreoffer->store_id;
    return array(
        'label' => 'Add an Offer',
        'route' => 'sitestoreoffer_general',
        'class' => 'ui-btn-action',
        'params' => array(
            'action' => 'create',
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestoreofferEdit($row) {

    $subject = Engine_Api::_()->core()->getSubject();

    $can_create_offer = $this->commonChecks();

    //CHECKS FOR EDIT OFFER
    if (empty($can_create_offer)) {
      return false;
    }

    $sitestoreoffer = $this->getSitestoreOfferObject();

    if (empty($sitestoreoffer)) {
      return false;
    }

    $store_id = $sitestoreoffer->store_id;

    return array(
        'label' => 'Edit Offer',
        'route' => 'sitestoreoffer_general',
        'class' => 'ui-btn-action',
        'params' => array(
            'action' => 'edit',
            'offer_id' => $subject->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestoreofferDelete($row) {

    $subject = Engine_Api::_()->core()->getSubject();

    $can_create_offer = $this->commonChecks();

    //CHECKS FOR DELETE OFFER
    if (empty($can_create_offer)) {
      return false;
    }

    $sitestoreoffer = $this->getSitestoreOfferObject();

    if (empty($sitestoreoffer)) {
      return false;
    }

    $store_id = $sitestoreoffer->store_id;
    return array(
        'label' => 'Delete Offer',
        'route' => 'sitestoreoffer_general',
        'class' => 'ui-btn-danger',
        'params' => array(
            'action' => 'delete',
            'offer_id' => $subject->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

//  public function onMenuInitialize_SitestoreofferPrint($row) {
//
//    $subject = Engine_Api::_()->core()->getSubject();
//
//    $sitestoreoffer = $this->getSitestoreOfferObject();
//
//    if (empty($sitestoreoffer)) {
//      return false;
//    }
//
//    $store_id = $sitestoreoffer->store_id;
//    return array(
//        'label' => 'Print Offer',
//        'route' => 'sitestoreoffer_general',
//        'target' => '_blank',
//        'params' => array(
//            'action' => 'print',
//            'offer_id' => $subject->getIdentity(),
//            'store_id' => $store_id
//        )
//    );
//  }

  public function onMenuInitialize_SitestoreofferShare($row) {

    $subject = Engine_Api::_()->core()->getSubject();
    return array(
        'label' => 'Share',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'id' => $subject->getIdentity(),
            'type' => 'sitestoreoffer_offer',
        ),
    );
  }

  public function onMenuInitialize_SitestoreofferReport($row) {

    $subject = Engine_Api::_()->core()->getSubject();

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        ),
    );
  }

  public function commonChecks() {

    //$viewer = Engine_Api::_()->user()->getViewer();

    $sitestoreoffer = $this->getSitestoreOfferObject();
    if (empty($sitestoreoffer)) {
      return false;
    }
    $store_id = $sitestoreoffer->store_id;
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    //START MANAGE-ADMIN CHECK
    $can_offer = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
    if (empty($isManageAdmin)) {
      $can_offer = 0;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    $can_create_offer = '';
    //OFFER CREATION AUTHENTICATION CHECK
    if ($can_edit == 1 && $can_offer == 1) {
      $can_create_offer = 1;
    }

    return $can_create_offer;
  }

  public function getSitestoreOfferObject() {
    $subject = Engine_Api::_()->core()->getSubject();
    $offer_id = $subject->getIdentity();
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

    if (empty($sitestoreoffer)) {
      return false;
    }

    return $sitestoreoffer;
  }

}

?>
