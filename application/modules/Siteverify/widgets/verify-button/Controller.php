<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteverify_Widget_VerifyButtonController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK FOR USER LOGGED IN OR NOT
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    // WE GET RESOURCE ID,RESOURCE TYPE AND RESOURCE TITLE
    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->resource_id = $resource_id = $subject->getIdentity();
    $this->view->resource_title = $subject->getTitle();
    
    if($viewer_id == $resource_id) {
      return $this->setNoRender();
    }

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $authorizationApi = Engine_Api::_()->authorization();
    $siteverify_verifybutton = Zend_Registry::isRegistered('siteverify_verifybutton') ? Zend_Registry::get('siteverify_verifybutton') : null;

    $tempLevelId = ($subject && isset($subject->level_id) && !empty($subject->level_id))? $subject->level_id: 0;
    
    $this->view->allow_verify = $allowVerify = $authorizationApi->getPermission($tempLevelId, 'siteverify_verify', 'allow_verify');

    $this->view->verifyOptions = $verifyOption = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteverify_verify', $subject, 'auth_verify');

    if (isset($viewer->level_id) && (empty($siteverify_verifybutton) || empty($allowVerify) || !in_array($viewer->level_id, $verifyOption))) {
      return $this->setNoRender();
    }

    //TO CHECK CURRENT VIEWING USER HAS BEEN VERIFIED OR NOT
    $verifyTable = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $this->view->hasVerified = $hasVerified = $verifyTable->hasVerify($resource_id);

    if (!empty($hasVerified)) {
      $this->view->admin_approve = $hasVerified->admin_approve;
      $this->view->verify_id = $hasVerified->verify_id;
    }

    //TO COUNT NO OF USERS WHO VERIFIED CURRENT VIEWING USER
    $this->view->verify_count = $verifyTable->getVerifyCount($resource_id);

    //CHECK PERMISSIONS
    $this->view->verify_limit = $authorizationApi->getPermission($tempLevelId, 'siteverify_verify', 'verify_limit');
    $this->view->allow_unverify = $authorizationApi->getPermission($tempLevelId, 'siteverify_verify', 'allow_unverify');
    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.comment.verifypopup', 1);
  }

}