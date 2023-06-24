<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_CommentsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        // Get subject
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
        } else if (($subject = $this->_getParam('subject'))) {
            list($type, $id) = explode('_', $subject);
            $subject = Engine_Api::_()->getItem($type, $id);
        } else if (($type = $this->_getParam('type')) &&
                ($id = $this->_getParam('id'))) {
            $subject = Engine_Api::_()->getItem($type, $id);
        }
        $sitemobileComments = Zend_Registry::isRegistered('sitemobileComments') ? Zend_Registry::get('sitemobileComments') : null;

        if (!($subject instanceof Core_Model_Item_Abstract) ||
                !$subject->getIdentity() ||
                (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')) ||
                empty($sitemobileComments)) {
            return $this->setNoRender();
        }

        // Perms
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');


        if (strpos($subject->getType(), "sitepage") !== false) {
            if ($subject->getType() == 'sitepage_page') {
                $pageSubject = $subject;
            } elseif ($subject->getType() == 'sitepagemusic_playlist') {
                $pageSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitepagenote_photo') {
                $pageSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitepageevent_photo') {
                $pageSubject = $subject->getEvent()->getParentPage();
            } else {
                $pageSubject = $subject->getParent();
            }
            $pageApi = Engine_Api::_()->sitepage();

            $this->view->canComment = $canComment = $pageApi->isManageAdmin($pageSubject, 'comment');
            $this->view->canDelete = $pageApi->isManageAdmin($pageSubject, 'edit');
        } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
            if ($subject->getType() == 'sitebusiness_business') {
                $businessSubject = $subject;
            } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
                $businessSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitebusinessnote_photo') {
                $businessSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitebusinessevent_photo') {
                $businessSubject = $subject->getParent()->getParent()->getParent();
            } else {
                $businessSubject = $subject->getParent();
            }
            $businessApi = Engine_Api::_()->sitebusiness();

            $this->view->canComment = $canComment = $businessApi->isManageAdmin($businessSubject, 'comment');
            $this->view->canDelete = $businessApi->isManageAdmin($businessSubject, 'edit');
        } else if (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $groupSubject = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $groupSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $groupSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $groupSubject = $subject->getEvent()->getParentPage();
            } else {
                $groupSubject = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();

            $this->view->canComment = $canComment = $groupApi->isManageAdmin($groupSubject, 'comment');
            $this->view->canDelete = $groupApi->isManageAdmin($groupSubject, 'edit');
        }

        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments
        // If has a page, display oldest to newest
        if (null !== ( $page = $this->_getParam('page'))) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->where('parent_comment_id =?', 0);
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
            $commentSelect->where('parent_comment_id =?', 0);
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }

        if ($viewer->getIdentity() && $canComment) {
            $this->view->form = $form = new Core_Form_Comment_Create();
            if (Engine_Api::_()->sitemobile()->isApp())
                $form->submit->setLabel('Post');
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }
        
        $this->view->params = $this->_getAllParams();
    }

}
