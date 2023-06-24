<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NotificationTypes.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_NotificationTypes extends Activity_Model_DbTable_NotificationTypes {

  protected $_name = 'activity_notificationtypes';

  /**
   * All notification types
   *
   * @var Engine_Db_Table_Rowset
   */
  protected $_notificationTypes;

  /**
   * Gets all action type meta info
   *
   * @param string|null $type
   * @return Engine_Db_Rowset
   */
  public function getNotificationTypes() {
    if (null === $this->_notificationTypes) {
      // Only get enabled types
      //$this->_notificationTypes = $this->fetchAll();
      $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();

      $select = $this->select()
              ->where('module IN(?)', $enabledModuleNames)
      ;

      // Exclude disabled friend types
      $excludedTypes = array();
      $friend_verfication = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification', true);
      $friend_direction = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', true);
      if ($friend_direction) {
        $excludedTypes = array_merge($excludedTypes, array('friend_follow', 'friend_follow_accepted', 'friend_follow_request'));
      } else {
        $excludedTypes = array_merge($excludedTypes, array('friend_accepted', 'friend_request'));
      }
      if (!$friend_verfication) {
        $excludedTypes = array_merge($excludedTypes, array('friend_follow_request', 'friend_request'));
      }
      if (!empty($excludedTypes)) {
        $excludedTypes = array_unique($excludedTypes);
        $select->where('type NOT IN(?)', $excludedTypes);
      }

      // Gotta catch em' all
      $this->_notificationTypes = $this->fetchAll($select);
    }

    return $this->_notificationTypes;
  }

  
  public function getDefaultPushNotifications() {

    $select = $this->select()
            ->from($this->info('name'), 'type')
            ->where('`enable_push` = ?', 1);

    // Exclude disabled friend types
    $excludedTypes = array();
    $friend_verfication = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification', true);
    $friend_direction = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', true);
    if ($friend_direction) {
      $excludedTypes = array_merge($excludedTypes, array('friend_follow', 'friend_follow_accepted', 'friend_follow_request'));
    } else {
      $excludedTypes = array_merge($excludedTypes, array('friend_accepted', 'friend_request'));
    }
    if (!$friend_verfication) {
      $excludedTypes = array_merge($excludedTypes, array('friend_follow_request', 'friend_request'));
    }

    if (!empty($excludedTypes)) {
      $excludedTypes = array_unique($excludedTypes);
      $select->where('type NOT IN(?)', $excludedTypes);
    }

    $types = $select
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN)
    ;

    return $types;
  }

  public function setDefaultPushNotifications($values) {
    if (!is_array($values)) {
      
      throw new Activity_Model_Exception('setDefaultPushNotifications requires an array of notifications');
    }

    $types = $this->select()
            ->from($this->info('name'), 'type')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN)
    ;

    $defaults = array();
    foreach ($types as $value) {
      if (in_array($value, $values)) {
        $defaults[] = $value;
      }
    }

    if (!empty($defaults)) {

      $this->update(
              array('enable_push' => '1',), array('`type` IN(?)' => $defaults));

      $this->update(
              array('enable_push' => '0',), array('`type` NOT IN(?)' => $defaults));
    } else {
      $this->update(array('enable_push' => '0'), array('`enable_push`' => '1'));
    }
  }

}