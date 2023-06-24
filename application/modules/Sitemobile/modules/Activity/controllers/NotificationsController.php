<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: NotificationsController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_NotificationsController extends Core_Controller_Action_Standard {

    public function init() {
        $this->_helper->requireUser();
    }

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
        $this->view->notifications = $notifications = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getNotificationsPaginator($viewer);
        $this->view->requests = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getRequestsPaginator($viewer, 'null');
        $notifications->setCurrentPageNumber($this->_getParam('page'));
        $this->view->showrequest = $this->_getParam('showrequest');
        $this->view->isajax = $this->_getParam('isajax', 0);
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->autoScrollNotificationEnable = $coreSettingsApi->getSetting('sitemobile.scroll.autoload', 1);
        //sitemobile code
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->requestsCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);
        }
        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->view->hasunread = false;

        if (empty($this->view->showrequest)) {
            $this->view->sitemapPageHeaderTitle = $this->view->translate('Recent Updates');
            //UPDATE NOTIFICATION COUNT CHANGE ON CLICK OF HEADER NOTIFICATION BUTTON, UPDATE SHOW VALUE TO 1    
            $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
              $where = array(
                '`user_id` = ?' => $viewer->getIdentity(),
                '`show` = ?' => 0
              );
          
        $notificationsTable->update(array('show' => 1), $where);
        } else {
            $this->view->sitemapPageHeaderTitle = $this->view->translate('My Requests');
            //UPDATE NOTIFICATION COUNT CHANGE ON CLICK OF HEADER NOTIFICATION BUTTON, UPDATE SHOW VALUE TO 1 
        $showRequestArray = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getShowRequest($viewer);
                 
         $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
         foreach($showRequestArray as $showRequest){
         
          $where = array(
            '`user_id` = ?' => $viewer->getIdentity(),
            '`notification_id` = ?' => $showRequest['notification_id'],
          );
          
         $notificationsTable->update(array('show' => 2), $where);
         }
        }
        Zend_Registry::set('sitemapPageHeaderTitle', $this->view->sitemapPageHeaderTitle);
        // Now mark them all as read
        $ids = array();
        foreach ($notifications as $notification) {
            $ids[] = $notification->notification_id;
        }
        //Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer, $ids);
        // Render
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
    }

    public function hideAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer);
    }

    public function markreadAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $action_id = $request->getParam('actionid', 0);

        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $notificationsTable->getAdapter();
        $db->beginTransaction();

        try {
            $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
            if ($notification) {
                $notification->read = 1;
                $notification->save();
            }
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->_helper->contextSwitch->getCurrentContext() != 'json') {
            $this->_helper->viewRenderer->setNoRender();
        }
    }

    public function updateCountRequestAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->requestCount = 0;
        if ($viewer->getIdentity()) {
            $this->view->requestCount = $requestCount = (int) Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);
        }
    }
     public function updateCountAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->notificationCount = 0;
        if ($viewer->getIdentity()) {
            $this->view->notificationCount = $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasNotifications($viewer);
        }
    }
    
      public function messageCountUpdateAction(){
           $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->messageCount = 0;
        if ($viewer->getIdentity()) {
            $this->view->messageCount = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
        }
    }

    public function updateAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $this->view->notificationCount = $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasNotifications($viewer);
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->notificationOnly = $request->getParam('notificationOnly', false);

        // @todo locale()->tonumber
        // array('%s update', '%s updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount));
        $this->view->text = $this->view->translate(array('%s Update', '%s Updates', $notificationCount), $notificationCount);
    }

    public function pulldownAction() {
        $page = $this->_getParam('page');
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->notifications = $notifications = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getNotificationsPaginator($viewer);
        $this->view->enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
        $notifications->setCurrentPageNumber($page);
        $this->view->isajax = $this->_getParam('isajax', 0);
        $this->view->autoScrollNotificationEnable = $coreSettingsApi->getSetting('sitemobile.scroll.autoload', 1);
//     if ($notifications->getCurrentItemCount() <= 0 || $page > $notifications->getCurrentPageNumber()) {
//       $this->_helper->viewRenderer->setNoRender(true);
//       return;
//     }

        //UPDATE NOTIFICATION COUNT CHANGE ON CLICK OF HEADER NOTIFICATION BUTTON, UPDATE SHOW VALUE TO 1    
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
          $where = array(
            '`user_id` = ?' => $viewer->getIdentity(),
            '`show` = ?' => 0
          );
          
        $notificationsTable->update(array('show' => 1), $where);
        
        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function pulldownRequestAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->requests = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getRequestsPaginator($viewer, 'null');
        //Sitemobile code
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->requestsCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);
        }
        $this->view->enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
       
         //UPDATE NOTIFICATION COUNT CHANGE ON CLICK OF HEADER NOTIFICATION BUTTON, UPDATE SHOW VALUE TO 1 
        $showRequestArray = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->getShowRequest($viewer);
                 
         $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
         foreach($showRequestArray as $showRequest){
         
          $where = array(
            '`user_id` = ?' => $viewer->getIdentity(),
            '`notification_id` = ?' => $showRequest['notification_id'],
          );
          
         $notificationsTable->update(array('show' => 2), $where);
         }

        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $this->_helper->viewRenderer->setNoRender(true);
    }

}