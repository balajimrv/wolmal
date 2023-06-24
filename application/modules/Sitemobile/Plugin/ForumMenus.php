<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ForumMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitemobile_Plugin_ForumMenus {

  public function onMenuInitialize_ForumTopicWatch() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
      $isWatching = $topicWatchesTable
              ->select()
              ->from($topicWatchesTable->info('name'), 'watch')
              ->where('resource_id = ?', $forum->getIdentity())
              ->where('topic_id = ?', $subject->getIdentity())
              ->where('user_id = ?', $viewer->getIdentity())
              ->limit(1)
              ->query()
              ->fetchColumn(0)
      ;
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    } else {
      return false;
    }

    if (!$isWatching) {
      return array(
          'label' => 'Watch Topic',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'watch',
              'watch' => 1,
              'topic_id' => $subject->getIdentity()
          )
      );
    } else {
      return array(
          'label' => 'Stop Watching Topic',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'watch',
              'watch' => 0,
              'topic_id' => $subject->getIdentity()
          )
      );
    }
  }

  public function onMenuInitialize_ForumTopicRename() {

    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    $canEdit = false;
    if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
      $canEdit = true;
    }
    if (!$canEdit)
      return false;

    return array(
        'label' => 'Rename',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'forum',
            'controller' => 'topic',
            'action' => 'rename',
            'reset' => false,
            'topic_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_ForumTopicMove() {

    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    $canEdit = false;
    if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
      $canEdit = true;
    }
    if (!$canEdit)
      return false;

    return array(
        'label' => 'Move',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'forum',
            'controller' => 'topic',
            'action' => 'move',
            'reset' => false,
            'topic_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_ForumTopicDelete() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    $canDelete = false;
    if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.delete')) {
      $canDelete = true;
    }

    if (!$canDelete)
      return false;

    return array(
        'label' => 'Delete Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-danger',
        'params' => array(
            'module' => 'forum',
            'controller' => 'topic',
            'action' => 'delete',
            'reset' => false,
            'topic_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_ForumTopicOpen() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    $canEdit = false;
    if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
      $canEdit = true;
    }
    if (!$canEdit)
      return false;

    if (!$subject->closed) {
      return array(
          'label' => 'Close',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'close',
              'reset' => false,
              'topic_id' => $subject->getIdentity(),
              'closed' => 1
          )
      );
    } else {
      return array(
          'label' => 'Open',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'close',
              'reset' => false,
              'topic_id' => $subject->getIdentity(),
              'closed' => 0
          )
      );
    }
  }

  public function onMenuInitialize_ForumTopicSticky() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $forum = $subject->getParent();
    $canEdit = false;
    if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
      $canEdit = true;
    }
    if (!$canEdit)
      return false;

    if (!$subject->sticky) {
      return array(
          'label' => 'Make Sticky',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'sticky',
              'topic_id' => $subject->getIdentity(),
              'reset' => false,
              'sticky' => 1
          )
      );
    } else {
      return array(
          'label' => 'Remove Sticky',
          'route' => 'default',
          'class' => 'smoothbox ui-btn-default ui-btn-action',
          'params' => array(
              'module' => 'forum',
              'controller' => 'topic',
              'action' => 'sticky',
              'topic_id' => $subject->getIdentity(),
              'reset' => false,
              'sticky' => 0
          )
      );
    }
  }

  public function onMenuInitialize_ForumReport($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $forum = Engine_Api::_()->core()->getSubject('forum');
    $forum = $subject->getParent();
    if ($viewer->getIdentity() && $forum->user_id != $viewer->getIdentity()) {
      return false;
    }
    if (empty($forum)) {
      return false;
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $forum->getGuid(),
        )
    );
  }

}