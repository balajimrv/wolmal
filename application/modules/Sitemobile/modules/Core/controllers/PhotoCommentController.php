<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoCommentController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_PhotoCommentController extends Core_Controller_Action_Standard {

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract &&
                    (method_exists($item, 'comments') || method_exists($item, 'likes'))) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    Engine_Api::_()->core()->setSubject($item);
                }
                //$this->_helper->requireAuth()->setAuthParams($item, $viewer, 'comment');
            }
        }

        //$this->_helper->requireUser();
        $this->_helper->requireSubject();
        //$this->_helper->requireAuth();
    }

    public function listAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Perms
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $this->view->getLikeUsers = $this->_getParam('getLikeUsers', 1);
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments
        // If has a page, display oldest to newest
        if (null !== ( $page = $this->_getParam('page'))) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }

        // If not has a page, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }

        if ($viewer->getIdentity() && $canComment) {
            $this->view->form = $form = new Sitemobile_modules_Core_Form_PhotoComment_Create();
            $form->submit->setLabel('Post');
            $form->submit->setAttrib('class', 'ui-btn-default');
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }

        if (Engine_Api::_()->seaocore()->checkEnabledNestedComment($subject->getType())) {
            $this->renderScript('photo-comment/nestedcomment_list.tpl');
        } else {
            $this->renderScript('photo-comment/list.tpl');
        }
    }

    public function createAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subjectParent = $subject = Engine_Api::_()->core()->getSubject();

        $this->view->form = $form = new Sitemobile_modules_Core_Form_PhotoComment_Create();

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            ;
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');
        $body = $filter->filter($body);
        $postData = $this->getRequest()->getPost();
        $tagsArray = array();
        $tagString = '';

        if (isset($postData['toValues']) && $postData['toValues']) {
            $toValues = explode(",", $postData['toValues']);
            foreach ($toValues as $values) {
                $user = Engine_Api::_()->getItem('user', $values);
                $tagsArray['user_' . $values] = $user->getTitle();
                $tagString .= $user->getTitle() . ' ';
            }
            $body = $body . ' ' . $tagString;
        }


        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
             //For comment work owner or second user
            $baseOnContentOwner = Engine_Api::_()->seaocore()->baseOnContentOwner($viewer, $subjectParent);
            if ($baseOnContentOwner) {
                $comment = $subject->comments()->addComment($subjectParent, $body);
            } else {
                $comment = $subject->comments()->addComment($viewer, $body);
            }

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            // Activity
            $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                'owner' => $subjectOwner->getGuid(),
                'body' => $body
            ));
            //TRY ATTACHMENT GETTING STUFF
            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');

            $manifest = Zend_Registry::get('Engine_Manifest');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);
            }

            //TRY TO ATTACH IF NECESSARY
            if ($action && $attachment) {
                $activityApi->attachActivity($action, $attachment);
            }

            if ($attachment) {
                if (isset($comment->attachment_type))
                    $comment->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($comment->attachment_id))
                    $comment->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $comment->save();
            }

            if (!empty($tagsArray)) {

                if ($action) {
                    $actionParams = (array) $action->params;
                    $action->params = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $action->save();
                }
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_()->getItemByGuid($key);
                    if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                        $subject_title = $viewer->getTitle();
                        $page_title = $tag->getTitle();
                        foreach ($tag->getPageAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'page_title' => $page_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                        $subject_title = $viewer->getTitle();
                        $business_title = $tag->getTitle();
                        foreach ($tag->getBusinessAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'business_title' => $business_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getGroupAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'group_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getStoreAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'store_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }

                // if ($action) {
                $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                $comment->params = Zend_Json::encode(array('tags' => $tagsArray));
                //  }
                $comment->save();
            }
            //$activityApi->attachActivity($action, $subject);
            // Notifications
            // Add notification for owner (if user and not viewer)
            $this->view->subject = $subject->getGuid();
            $this->view->owner = $subjectOwner->getGuid();
            if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser->getIdentity();

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                    continue;

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = 'Comment added';
        $this->view->body = $this->view->action('list', 'photo-comment', 'core', array(
            'type' => $this->_getParam('type'),
            'id' => $this->_getParam('id'),
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function deleteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Comment id
        $comment_id = $this->_getParam('comment_id');
        if (!$comment_id) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
            return;
        }

        // Comment
        $comment = $subject->comments()->getComment($comment_id);
        if (!$comment) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
            return;
        }

        // Authorization
        if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
                ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
            return;
        }

        // Method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->comments()->removeComment($comment_id);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
    }

    public function likeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {

            $commentedItem->likes()->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType()
                ));
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
        $this->view->body = $this->view->action('list', 'photo-comment', 'core', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function unlikeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
            $commentedItem->likes()->removeLike($viewer);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
        $this->view->body = $this->view->action('list', 'photo-comment', 'core', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function getLikesAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $likes = $subject->likes()->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }

    public function getLikeUsersAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->page_current = $page = (int) $this->_getParam('page', 1);
        $likeSelect = $subject->likes()->getLikeSelect();
        $this->view->likes = $likes = Zend_Paginator::factory($likeSelect);
        $likes->setCurrentPageNumber($page);
        $likes->setItemCountPerPage(10);
    }

    public function getDislikeUsersAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->page_current = $page = (int) $this->_getParam('page', 1);
        $likeSelect = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDisLikeSelect($subject);
        $this->view->likes = $likes = Zend_Paginator::factory($likeSelect);
        $likes->setCurrentPageNumber($page);
        $likes->setItemCountPerPage(10);
    }
    
}