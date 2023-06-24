<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFriendshipSM.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_View_Helper_UserMutualFriendship extends Zend_View_Helper_Abstract {

  public function userMutualFriendship($user, $viewer = null) {
    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer || !$viewer->getIdentity() /* || $user->isSelf($viewer) */) {
      return '';
    }

    if ($user->isSelf($viewer)) {
      return $this->view->translate("SM_USER_SELF");
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    }
    else
      $row = $viewer->membership()->getRow($user);

    // Render
    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return '';
    }

    // check admin level setting if you can befriend people in your network
    else if ($eligible == 1) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
              ->from($networkMembershipName, 'user_id')
              ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
              ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
              ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
      ;

      $data = $select->query()->fetch();

      if (empty($data)) {
        return '';
      }
    }

    $userFriendIds = $user->membership()->getMembershipsOfIds();
    $viewerFriendIds = $viewer->membership()->getMembershipsOfIds();
    //put check for mobile app. If mobile app then we will only show mutual friends with friend suggestion only
    if(Engine_Api::_()->sitemobile()->isApp()) :
        if ($userFriendIds && $viewerFriendIds) {
          $mutual = array_intersect($userFriendIds, $viewerFriendIds);
          $countMutual = count($mutual);
          if ($mutual && $direction) {
           return $this->view->translate(array("%s mutual friend.", "%s mutual friends.", $countMutual), $this->view->locale()->toNumber($countMutual));
          }
          return;
        }
    endif;
    if ($userFriendIds && $viewerFriendIds) {
      $mutual = array_intersect($userFriendIds, $viewerFriendIds);
      $countMutual = count($mutual);
      if ($mutual) {

        if (!$direction) {
          // one-way mode
          return $this->view->translate(array("%s mutual follower.", "%s mutual followers.", $countMutual), $this->view->locale()->toNumber($countMutual));
        } else {
          // two-way mode
          return $this->view->translate(array("%s mutual friend.", "%s mutual friends.", $countMutual), $this->view->locale()->toNumber($countMutual));
        }
      } else {
        $countMembers = count($userFriendIds);
        if ($countMembers) {
          if (!$direction) {
            // one-way mode
            return $this->view->translate(array("%s follower.", "%s followers.", $countMembers), $this->view->locale()->toNumber($countMembers));
          } else {
            // two-way mode
            return $this->view->translate(array("%s friend.", "%s friends.", $countMembers), $this->view->locale()->toNumber($countMembers));
          }
        }
      }
    } else {
      $countMembers = count($userFriendIds);
      if ($countMembers) {
        if (!$direction) {
          // one-way mode
          return $this->view->translate(array("%s follower.", "%s followers.", $countMembers), $this->view->locale()->toNumber($countMembers));
        } else {
          // two-way mode
          return $this->view->translate(array("%s friend.", "%s friends.", $countMembers), $this->view->locale()->toNumber($countMembers));
        }
      }
    }

    return '';
  }

}