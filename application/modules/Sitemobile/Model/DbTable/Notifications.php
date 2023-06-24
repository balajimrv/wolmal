<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Notifications.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Notifications extends Activity_Model_DbTable_Notifications {

    protected $_name = 'activity_notifications';

    /**
     * Get notification paginator
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getNotificationsPaginator(User_Model_User $user) {
        $enabledNotificationTypes = array();
        foreach (Engine_Api::_()->getDbtable('NotificationTypes', 'sitemobile')->getNotificationTypes() as $type) {
            $enabledNotificationTypes[] = $type->type;
        }
        $select = $this->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('`type` IN(?)', $enabledNotificationTypes)
                ->order('date DESC')
        ;

        return Zend_Paginator::factory($select);
    }

    // Requests

    /**
     * Get all request-type notifications for a user
     *
     * @param User_Model_User $user
     * @return Engine_Db_Table_Rowset
     */
    public function getRequests(User_Model_User $user) {
        // Only get enabled types
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
        $typeTable = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile');
        $select = $this->select()
                ->from($this->info('name'))
                ->join($typeTable->info('name'), $typeTable->info('name') . '.type = ' . $this->info('name') . '.type', null)
                ->where('module IN(?)', $enabledModuleNames)
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->order('date DESC')
        ;

        return $this->fetchAll($select);
    }

    /**
     * Get a paginator for request-type notifications
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getRequestsPaginator(User_Model_User $user, $suggestion) {

        // Only get enabled types
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();

        $typeTable = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile');
        $select = $this->select()
                ->from($this->info('name'))
                ->join($typeTable->info('name'), $typeTable->info('name') . '.type = ' . $this->info('name') . '.type', null)
                ->where('module IN(?)', $enabledModuleNames)
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->order('date DESC')
        ;
        if ($suggestion == 'suggestion_true') {
            $select->where($this->info('name') . ".type = 'friend_request'")->orWhere($this->info('name') . ".type = 'friend_follow_request'");
        }


        return Zend_Paginator::factory($select);
    }

    /**
     * Does the user have notifications, returns the number or 0
     *
     * @param User_Model_User $user
     * @return int The number of notifications the user has
     */
    public function hasNotifications(User_Model_User $user) {

        // Only get enabled types
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
        $typeTable = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile');
        $select = $this->select()
                ->from($this->info('name'), array('object_type', 'type','object_id'))
                ->join($typeTable->info('name'), $typeTable->info('name') . '.type = ' . $this->info('name') . '.type', null)
                ->where('module IN(?)', $enabledModuleNames)
                ->where('user_id = ?', $user->getIdentity())
                ->where('`read` = ?', 0)
                ->where('`show` = ?', 0);

        $result = $this->fetchAll($select);

        $count = 0;
        foreach ($result as $values) {

            /* Check for display notifications of only enabled sitemobile modules, If Module is Suggestion then display only 
              suggestions of enabled modules.
             */
            $notification_object_type = explode("_", $values->object_type);

            if (!in_array($notification_object_type[0], $enabledModuleNames)) {
                continue;
            } else if ($notification_object_type[0] == 'suggestion') {
                
                $suggObj = Engine_Api::_()->getItem('suggestion', $values->object_id);
                $suggestionModule = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
                foreach ($suggestionModule as $sugModName) {
                    $sugModNameEnabled = $sugModName['pluginName'];
                    break;
                }
                if (!in_array($sugModNameEnabled, $enabledModuleNames)){
                    continue;
                } else {
                    $count++;
                }
            }/* End Checks */ 
            else {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get a paginator for request-type notifications
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function hasRequests(User_Model_User $user) {

        // Only get enabled types
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();

        $typeTable = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile');
        $select = $this->select()
                ->from($this->info('name'), array('object_type', 'type','object_id'))
                ->join($typeTable->info('name'), $typeTable->info('name') . '.type = ' . $this->info('name') . '.type', null)
                ->where('module IN(?)', $enabledModuleNames)
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->where($this->info('name') . '.show IN(?)',array(0,1))
                ->order('date DESC');

        $result = $this->fetchAll($select);
        $count = 0;
        foreach ($result as $values) {

            /* Check for display notifications of only enabled sitemobile modules, If Module is Suggestion then display only 
              suggestions of enabled modules.
             */
            $notification_object_type = explode("_", $values->object_type);

            if (!in_array($notification_object_type[0], $enabledModuleNames)) {
                continue;
            } else if ($notification_object_type[0] == 'suggestion') {
                
                $suggObj = Engine_Api::_()->getItem('suggestion', $values->object_id);
                $suggestionModule = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($suggObj->entity);
                foreach ($suggestionModule as $sugModName) {
                    $sugModNameEnabled = $sugModName['pluginName'];
                    break;
                }
                if (!in_array($sugModNameEnabled, $enabledModuleNames)){
                    continue;
                } else {
                    $count++;
                }
            }/* End Checks */ 
            else {
                $count++;
            }
        }
        return $count;
    }
    
    public function getShowRequest(User_Model_User $user){
        //UPDATE REQUEST COUNT CHANGE ON CLICK OF HEADER REQUEST BUTTON, UPDATE SHOW VALUE TO 1
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();

        $typeTable = Engine_Api::_()->getDbtable('notificationTypes', 'sitemobile');
        $select = $this->select()
                ->from($this->info('name'), 'notification_id')
                ->join($typeTable->info('name'), $typeTable->info('name') . '.type = ' . $this->info('name') . '.type', null)
                ->where('module IN(?)', $enabledModuleNames)
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->where($this->info('name') . '.show IN(?)',array(0,1))
                ->order('date DESC')
                ->query()
                ->fetchAll();

        return $select;
 
    }

}
