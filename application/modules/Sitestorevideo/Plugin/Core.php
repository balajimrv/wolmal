<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {

      //VIDEO TABLE
      $sitestorevideoTable = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
      $sitestorevideoSelect = $sitestorevideoTable->select()->where('owner_id = ?', $payload->getIdentity());

      //RATING TABLE
      $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestorevideo');

      $ratingTable->delete(array('user_id = ?' => $payload->getIdentity()));

      foreach ($sitestorevideoTable->fetchAll($sitestorevideoSelect) as $sitestorevideo) {
				$ratingTable->delete(array('video_id = ?' => $sitestorevideo->video_id));
        $sitestorevideo->delete();
      }
    }
  }

}
?>