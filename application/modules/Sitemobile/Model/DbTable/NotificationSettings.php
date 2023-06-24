<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NotificationSettings.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_NotificationSettings extends Activity_Model_DbTable_NotificationSettings {

  protected $_name = 'activity_notificationsettings';

  /**
   * Gets all enabled notification types for a user
   *
   * @param User_Model_User $user
   * @return array An array of enabled types
   */
  public function getEnabledNotifications(User_Model_User $user) {
    $types = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile')->getNotificationTypes();

    $select = $this->select()
            ->where('user_id = ?', $user->getIdentity());
    $rowset = $this->fetchAll($select);

    $enabledTypes = array();
    foreach ($types as $type) {
      $row = $rowset->getRowMatching('type', $type->type);
      if (null === $row || $row->email == true) {
        $enabledTypes[] = $type->type;
      }
    }

    return $enabledTypes;
  }

  /**
   * Set enabled notification types for a user
   *
   * @param User_Model_User $user
   * @param array $types
   * @return Activity_Api_Notifications
   */
  public function setEnabledNotifications(User_Model_User $user, array $enabledTypes) {
    $types = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile')->getNotificationTypes();

    $select = $this->select()
            ->where('user_id = ?', $user->getIdentity());
    $rowset = $this->fetchAll($select);

    foreach ($types as $type) {
      $row = $rowset->getRowMatching('type', $type->type);
      $value = in_array($type->type, $enabledTypes);
      if ($value && null !== $row) {
        $row->delete();
      } else if (!$value && null === $row) {
        $row = $this->createRow();
        $row->user_id = $user->getIdentity();
        $row->type = $type->type;
        $row->email = (bool) $value;
        $row->save();
      }
    }

    return $this;
  }

}