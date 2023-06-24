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
    protected $_cover = array();

    public function setMessagesRead(User_Model_User $user)
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

    public function setNotificationRead(User_Model_User $user, $notification_id)
    {
        return Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), array(
            '`notification_id` = ?' => $notification_id,
            '`user_id` = ?' => $user->getIdentity(),
            '`read` = ?' => 0
        ));
    }

    public function setNotificationsView(User_Model_User $user, $type = null)
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

    public function getUnreadMessageCount(User_Model_User $user)
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

    public function getProfileCover(User_Model_User $user)
    {
        $photo = $this->_initProfileCover($user);
		$this->profile_coverimg = Engine_Api::_()->getApi('settings', 'core')->getSetting('zephyr.profile_coverimg', false);
		
        if (null !== $photo)
        {
            return $photo->getPhotoUrl();
        }

        return !($this->profile_coverimg) ? $this->APPLICATION_PATH.'application/modules/Zephyrtheme/externals/images/profilecover.jpg' : $this->profile_coverimg;
    }

    public function hasProfileCover(User_Model_User $user)
    {
        return (bool) $this->_initProfileCover($user);
    }

    protected function _initProfileCover(User_Model_User $user)
    {
        $identity = $user->getIdentity();

        if (!isset($this->_cover[$identity]))
        {
            $albumTable = Engine_Api::_()->getDbtable('albums', 'zephyrtheme');
            $this->_cover[$identity] = $albumTable->getCoverPhoto($user);
        }

        return $this->_cover[$identity];
    }
}