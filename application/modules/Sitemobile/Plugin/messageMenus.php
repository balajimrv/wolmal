<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: messageMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_messageMenus {

  public function onMenuInitialize_UserProfileMessage($row) {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
      return false;
    }

    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
      return false;
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($messageAuth == 'none') {
      return false;
    } else if ($messageAuth == 'friends') {
      // Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($subject);
      }
      else
        $friendship_status = $subject->membership()->getRow($viewer);

      if (!$friendship_status || $friendship_status->active == 0) {
        return false;
      }
    }

    return array(
        'label' => "Send Message",
        'icon' => 'application/modules/Messages/externals/images/send.png',
        'route' => 'messages_general',
        'params' => array(
            'action' => 'compose',
            'to' => $subject->getIdentity()
        ),
    );
  }

}