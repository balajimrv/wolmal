<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Plugin_Menus {

  public function canViewReviews() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.review.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');
    $rName = $table->info('name');
    $table_stores = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName_stores = $table_stores->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_stores, array('photo_id', 'title as sitestore_title'))
                    ->join($rName, $rName . '.store_id = ' . $rName_stores . '.store_id');
                    
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


  //SITEMOBILE STORE REVIEW MENUS
  public function onMenuInitialize_SitestorereviewEdit($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $sitestorereview = $this->getSitestoreReviewObject();
    if (empty($sitestorereview)) {
      return false;
    }
    //STORE ID
    $store_id = $sitestorereview->store_id;

    $owner_id = $sitestorereview->owner_id;

    //CHECKS FOR EDIT
    if ($viewer_id != $owner_id) {
      return false;
    }

    return array(
        'label' => 'Edit Review',
        'route' => 'sitestorereview_edit',
        'class' => 'ui-btn-action',
        'params' => array(
            'review_id' => $sitestorereview->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorereviewDelete($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $sitestorereview = $this->getSitestoreReviewObject();
    if (empty($sitestorereview)) {
      return false;
    }
    //STORE ID
    $store_id = $sitestorereview->store_id;

    $owner_id = $sitestorereview->owner_id;
    $viewer->level_id;

    //CHECK FOR DELETE
    if ($viewer_id != $owner_id && $viewer->level_id != 1) {
      return false;
    }

    return array(
        'label' => 'Delete Review',
        'route' => 'sitestorereview_delete',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'review_id' => $sitestorereview->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorereviewReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $review_report = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.report', 1);
    $viewer_id = $viewer->getIdentity();

    //CHECK FOR REPORT
    if ($review_report != 1 || empty($viewer_id)) {
      return false;
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        // 'format' => 'smoothbox'
        )
    );
  }

  public function getSitestoreReviewObject() {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET REVIEW ID
    $review_id = $subject->getIdentity();
    //GET REVIEW ITEM
    $sitestorereview = Engine_Api::_()->getItem('sitestorereview_review', $review_id);
    //ASK
    if (empty($sitestorereview)) {
      return false;
    }
    return $sitestorereview;
  }
}
?>