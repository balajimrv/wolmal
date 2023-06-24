<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    Zephyrtheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

class Zephyrtheme_Api_Core extends Core_Api_Abstract
{
  public function getUnreadMessageCount(Core_Model_Item_Abstract $user)
  {
	$recipients_table = Engine_Api::_()->getDbtable('recipients', 'messages');
	$recipients_select = $recipients_table->select()->where('user_id = ?', $user->getIdentity());
	$recipients = $recipients_table->fetchAll($recipients_select);
	
	$messages_table = Engine_Api::_()->getDbtable('messages', 'messages');
	$messages_no = 0;
	
	foreach ($recipients as $recipient)
	{
		if (null === $recipient->inbox_message_id || !is_numeric($recipient->inbox_message_id))
			continue;
	
		$messages_select = $messages_table->select()
			->where('`message_id` = ?', $recipient->inbox_message_id)
			->where('`read` = ?', 0);
	
		$new_messages = $messages_table->fetchAll($messages_select)->toArray();
	
		if (!empty($new_messages))
			$messages_no++;
	}
	
	return $messages_no;
  }
  public function setMessagesRead(Core_Model_Item_Abstract $user)
  {
    $recipients_table = Engine_Api::_()->getDbtable('recipients', 'messages');
    $recipients_select = $recipients_table->select()->where('user_id = ?', $user->getIdentity());
    $recipients = $recipients_table->fetchAll($recipients_select);

    foreach ($recipients as $recipient)
    {
        Engine_Api::_()->getDbtable('messages', 'messages')->update(array('read' => 1), array(
            '`message_id` = ?' => $recipient->inbox_message_id
        ));
    }
  }

    public function setNotificationRead(Core_Model_Item_Abstract $user, $notification_id)
    {
        return Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), array(
            '`notification_id` = ?' => $notification_id,
            '`user_id` = ?' => $user->getIdentity(),
            '`read` = ?' => 0
        ));
    }

    public function setNotificationsView(Core_Model_Item_Abstract $user, $type = null)
    {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`read` = ?' => 0,
            '`view_notification` = ?' => 0
        );

        if ('friend' == $type)
        {
            $where['`mitigated` = ?'] = 0;
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('view_notification' => 1), $where);
    }
}