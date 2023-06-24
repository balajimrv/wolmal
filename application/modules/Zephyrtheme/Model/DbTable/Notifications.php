<?php
class Zephyrtheme_Model_DbTable_Notifications extends Engine_Db_Table {

  protected $_rowClass = 'Activity_Model_Notification';
  protected $_serializedColumns = array('params');

  public function getFriendrequestPaginator(User_Model_User $user) {
    $enabledNotificationTypes = array();
    foreach (Engine_Api::_()->getDbtable('NotificationTypes', 'activity')->getNotificationTypes() as $type) {
      $enabledNotificationTypes[] = $type->type;
    }

    $select = Engine_Api::_()->getDbtable('notifications', 'activity')->select()
            ->where('user_id = ?', $user->getIdentity())
            ->where('`type` IN(?)', $enabledNotificationTypes)
            ->where('type = ?', 'friend_request')
            ->where('mitigated = ?', 0)
            ->order('date DESC')
    ;

    return Zend_Paginator::factory($select);
  }

    public function hasNotifications(User_Model_User $user, $friend = null)
    {
        $table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $select = new Zend_Db_Select($table->getAdapter());
        $select
                ->from($table->info('name'), 'COUNT(notification_id) AS notification_count')
                ->where('`user_id` = ?', $user->getIdentity())
                ->where('`read` = ?', 0)
                ->where('`view_notification` = ?', 0);
        if ($friend == 'friend') {
          $select->where('type = ?', 'friend_request');
          $select->where('mitigated 	 = ?', 0);
        } else {
          $select->where('type != ?', 'message_new');
          $select->where('type != ?', 'friend_request');
        }

        $data = $table->getAdapter()->fetchRow($select);
        return (int) @$data['notification_count'];
    }

    public function getNotificationsPaginator(User_Model_User $user)
    {
        $enabledNotificationTypes = array();

        foreach (Engine_Api::_()->getDbtable('NotificationTypes', 'activity')->getNotificationTypes() as $type)
            $enabledNotificationTypes[] = $type->type;

        $select = Engine_Api::_()->getDbtable('notifications', 'activity')
            ->select()
            ->where('user_id = ?', $user->getIdentity())
            ->where('`type` IN(?)', $enabledNotificationTypes)
            ->where('type != ?', 'message_new')
            ->where('type != ?', 'friend_request')
            ->order('date DESC');
        return Zend_Paginator::factory($select);
    }
	

}