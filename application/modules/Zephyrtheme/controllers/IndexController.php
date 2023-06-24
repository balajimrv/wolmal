<?php
class Zephyrtheme_IndexController extends Core_Controller_Action_Standard
{
    public function addCoverAction()
    {
        if (!Engine_Api::_()->user()->getViewer()->getIdentity())
        {
            $this->redirect('login');
            return false;
        }

        $this->view->cover_form = $cover_form = new Zephyrtheme_Form_Cover();
        if (!$this->getRequest()->isPost())
        {
            return false;
        }

        $values = $this->getRequest()->getPost();

        if( empty($values['Filename']) && !isset($_FILES['Filedata']))
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $first_cover = Engine_Api::_()->zephyrtheme()->hasProfileCover($viewer) ? false : true;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $photoTable = Engine_Api::_()->getDbtable('photos', 'zephyrtheme');
            $photo = $photoTable->createRow();
            $photo->setPhoto($_FILES['Filedata']);

            $albumTable = Engine_Api::_()->getDbtable('albums', 'zephyrtheme');
            $album = $albumTable->getCoverAlbum($viewer);
            $album->cover_id = $photo->photo_id;
            $album->save();

            $photo->album_id = $album->album_id;
            $photo->save();

            // Add action and attachments
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $api->addActivity($viewer, $viewer, 'profile_cover_update', null);
            $photo = Engine_Api::_()->getItem("zephyrtheme_photo", $photo->photo_id);

            if ($action instanceof Activity_Model_Action)
            {
                $api->attachActivity($action, $photo);
            }

            $db->commit();

            return $this->_helper->json(array('status' => true,
                'src' => $photo->getPhotoUrl(),
                'first_cover' => $first_cover,
                'saved' => true
            ));
        } catch (Engine_Exception $e) {
            $db->rollBack();

            return $this->_helper->json(array(
                'status' => false,
                'reload' => false,
                'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage())));
        }
    }

    public function deleteCoverAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $viewer = Engine_Api::_()->user()->getViewer();

        try {
            $albumTable = Engine_Api::_()->getDbtable('albums', 'zephyrtheme');
            $album = $albumTable->getCoverAlbum($viewer);

            if ($album->cover_id)
            {
                $photo = Engine_Api::_()->getItem('zephyrtheme_photo', $album->cover_id);
                $photo->delete();

                $album->cover_id = 0;
                $album->save();
                $db->commit();
            }

            $this->view->default_cover = Engine_Api::_()->zephyrtheme()->getProfileCover($viewer);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function messageAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('messages_conversation')
        ->getInboxPaginator($viewer);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Engine_Api::_()->zephyrtheme()->setMessagesRead($viewer);
    }

    public function friendRequestAction()
    {
        $page = $this->_getParam('page');
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->friendRequests = $requests = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->getFriendrequestPaginator($viewer);
		$this->view->appSalt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret');
        $requests->setCurrentPageNumber($page);
        Engine_Api::_()->zephyrtheme()->setNotificationsView($viewer, 'friend');
    }

    public function markreadAction()
    {
        $notification_id = $this->_getParam('notificationid', 0);
        $viewer = Engine_Api::_()->user()->getViewer();

        Engine_Api::_()->zephyrtheme()->setNotificationRead($viewer, $notification_id);

        if ($this->_helper->contextSwitch->getCurrentContext()  != 'json') {
            $this->_helper->viewRenderer->setNoRender();
        }
    }

    public function notificationsAction()
    {
        $page = $this->_getParam('page');
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->notifications = $notifications = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->getNotificationsPaginator($viewer);
        $notifications->setCurrentPageNumber($page);

        if ($notifications->getCurrentItemCount() <= 0 || $page > $notifications->getCurrentPageNumber()) {
            $this->_helper->viewRenderer->setNoRender(true);
            return;
        }

        Engine_Api::_()->zephyrtheme()->setNotificationsView($viewer);

        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     *   Used for new notifications polling
     */
    public function newNotificationsAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->hasNotifications($viewer);
    }

    /**
    *   Used for new friend requests polling
    */
    public function newFriendRequestsAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->requestCount = Engine_Api::_()->getDbtable('notifications', 'zephyrtheme')->hasNotifications($viewer, 'friend');
    }

    /**
    *   Used for new messages polling
    */
    public function newMessagesAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->messageCount = Engine_Api::_()->zephyrtheme()->getUnreadMessageCount($viewer);
    }

    public function deleteMessageAction()
    {
        $message_id = $this->getRequest()->getParam('id');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();
        try {
          $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
          foreach ($recipients as $r) {
            if ($viewer_id == $r->user_id) {
              $this->view->deleted_conversation_ids[] = $r->conversation_id;
              $r->inbox_deleted = true;
              $r->outbox_deleted = true;
              $r->save();
            }
          }

          $db->commit();
        } catch (Exception $e) {
          $db->rollback();
          throw $e;
        }
    }

    public function addSlideAction()
    {
        $this->view->form = $form = new Zephyrtheme_Form_Admin_SliderItemCreate();

        if (!$this->getRequest()->isPost()) return false;
        if (!$form->isValid($this->getRequest()->getPost())) return false;

        // Save
        $values = $form->getValues();
        $slider_items_table = Engine_Api::_()->getDbtable('sliderItems', 'zephyrtheme');

        $item = $slider_items_table->createRow();
        $item->image_path = $values['image_path'];
        $item->illustration_path = $values['illustration_path'];
		$item->video = $values['video'];
        $item->title = $values['title'];
        $item->subtitle = $values['subtitle'];
		$item->color = $values['color'];
        $item->text_align = $values['text_align'];
        $item->show_item = $values['show_item'];
        $item->colonetop = $values['colonetop'];
        $item->coltwotop = $values['coltwotop'];
        $item->save();

        $this->view->status = true;
        $this->view->form = null;
    }

    public function editSlideAction()
    {
        $id = $this->_getParam('id');

        if (null === $id) {
            throw new Core_Model_Exception('Invalid item id!');
        }

        $slider_items_table = Engine_Api::_()->getDbtable('sliderItems', 'zephyrtheme');
        $slider_item_select = $slider_items_table->select()
            ->where('id = ?', $id);

        $item = $slider_items_table->fetchRow($slider_item_select);

        if (!$item)
        {
            throw new Core_Model_Exception('missing slider item');
        }

        $this->view->form = $form = new Zephyrtheme_Form_Admin_SliderItemEdit();
        $slider_item_data = $item->toArray();

        // Check stuff
        if( !$this->getRequest()->isPost() ) {
            $form->populate($slider_item_data);
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Save
        $values = $form->getValues();

        $item->image_path = $values['image_path'];
        $item->illustration_path = $values['illustration_path'];
		$item->video = $values['video'];
        $item->title = $values['title'];
        $item->subtitle = $values['subtitle'];
		$item->color = $values['color'];
        $item->text_align = $values['text_align'];
        $item->show_item = $values['show_item'];
        $item->colonetop = $values['colonetop'];
        $item->coltwotop = $values['coltwotop'];
        $item->save();

        $this->view->new_img_path = $values['image_path'];
        $this->view->img_id = $item->id;
        $this->view->status = true;
        $this->view->form = null;
    }

    public function deleteSlideAction()
    {
        $slider_id = $this->_getParam('id');

        if (null === $slider_id) {
            throw new Core_Model_Exception('Invalid item id!');
        }

        $slider_items_table = Engine_Api::_()->getDbtable('sliderItems', 'zephyrtheme');

        // Make form
        $this->view->form = $form = new Core_Form_Confirm(array(
            'title' => 'Delete Slider Item?',
            'description' => 'Are you sure you want to delete this item?',
            'submitLabel' => 'Delete Item',
            'cancelHref' => 'javascript:parent.Smoothbox.close()',
        ));

        if (!$this->getRequest()->isPost()) return false;
        if (!$form->isValid($this->getRequest()->getPost())) return false;

        // Process
        $slider_items_table->delete(array(
            'id = ?' => $slider_id
        ));

        $this->view->form = null;
        $this->view->status = true;
        $this->view->slider_id = $slider_id;
    }
}