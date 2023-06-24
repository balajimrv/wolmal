<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {

      $sitestorereviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');
      $sitestorereviewSelect = $sitestorereviewTable->select()->where('owner_id = ?', $payload->getIdentity());

      foreach ($sitestorereviewTable->fetchAll($sitestorereviewSelect) as $sitestorereview) {
				Engine_Api::_()->sitestorereview()->deleteContent($sitestorereview->review_id);
      }
    }
  }

}
?>