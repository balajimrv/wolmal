<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Widget_ReviewContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
//GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $this->view->level_id = $viewer->level_id;
    } else {
      $this->view->level_id = 0;
    }

    //GET REVIEW MODEL
    $this->view->sitestorereview = $sitestorereview = Engine_Api::_()->getItem('sitestorereview_review', Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id'));
    if (empty($sitestorereview)) {
      return $this->setNoRender();
    }

    $this->view->store_id = $store_id = $sitestorereview->store_id;

    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $this->view->sitestore_slug = $sitestore->getSlug();
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //START MANAGE-ADMIN CHECK
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
    //END MANAGE-ADMIN CHECK
    //GET OWNER INFORMATION
    $this->view->owner = $owner = $sitestorereview->getOwner();

    //INCREMENT IN NUMBER OF VIEWS
    if (!$owner->isSelf($viewer)) {
      $sitestorereview->view_count++;
      $sitestorereview->save();
    }

    //REPORT CODE
    $this->view->review_report = $review_report = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.report', 1);
    if (!empty($viewer_id) && $review_report == 1) {
      $report = $this->view->report = $sitestorereview;
    }

    // Start: "Suggest to Friends" link work.
    $store_flag = 0;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitestore')->isSupport();
    if (!empty($is_suggestion_enabled)) {
      // Here we are delete this review suggestion if viewer have.
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($viewer_id, 'store_review', Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id'), 'store_review_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitestorereview', Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id'), 1,null);
        if (!empty($modContentObj)) {
          $contentCreatePopup = @COUNT($modContentObj);
        }
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
        if ($sitestore->expiration_date <= date("Y-m-d H:i:s")) {
          $store_flag = 1;
        }
      }
      if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitestore->closed) && !empty($sitestore->approved) && empty($sitestore->declined) && !empty($sitestore->draft) && empty($store_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
        $this->view->reviewSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitestore', 'review_sugg_link');
      }
      // End: "Suggest to Friends" link work.
    }
  }

}
?>