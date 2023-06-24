<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {
      //DELETE OFFERS
      $sitestoreofferTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
      $sitestoreofferSelect = $sitestoreofferTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach ($sitestoreofferTable->fetchAll($sitestoreofferSelect) as $sitestoreoffer) {
        Engine_Api::_()->sitestoreoffer()->deleteContent($sitestoreoffer->offer_id);
      }
    }
  }

}
?>