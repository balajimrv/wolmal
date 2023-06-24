<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: PostController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_PostController extends Core_Controller_Action_Standard {

  public function init() {
    if (Engine_Api::_()->core()->hasSubject())
      return;

    if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
            null !== ($post = Engine_Api::_()->getItem('event_post', $post_id))) {
      Engine_Api::_()->core()->setSubject($post);
    }

    $this->_helper->requireUser->addActionRequires(array(
        'edit',
        'delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
        'edit' => 'event_post',
        'delete' => 'event_post',
    ));
  }

  public function editAction() {
    $post = Engine_Api::_()->core()->getSubject('event_post');
    $event = $post->getParent('event');
    $viewer = Engine_Api::_()->user()->getViewer();
    $topic_id = (int) $this->_getParam('topic_id');
    $topic = Engine_Api::_()->getItem('event_topic', $topic_id);
    if (!$event->isOwner($viewer) && !$post->isOwner($viewer)) {
      if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) {
        return;
      }
    }

    $this->view->form = $form = new Event_Form_Post_Edit();
    $this->view->clear_cache = true;

    $form->removeElement('body');

    // Description
    $form->addElement('Textarea', 'body', array(
       // 'label' => 'Edit Post',
        'order' => 1,
        'required' => true,
        'allowEmpty' => false,
    ));
 
    $form->buttons->setOrder(999);
		$form->cancel->setAttrib("onclick", $topic->getHref());
    if (!$this->getRequest()->isPost()) {
      $form->populate($post->toArray());
      $form->body->setValue(html_entity_decode($post->body));
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->body = htmlspecialchars($post->body, ENT_NOQUOTES, 'UTF-8');
      $post->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                //'closeSmoothbox' => true,
                 'redirect' => $topic->getHref(),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The changes to your post have been saved.')),
            ));
  }

  public function deleteAction() {
    $post = Engine_Api::_()->core()->getSubject('event_post');
    $event = $post->getParent('event');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event->isOwner($viewer) && !$post->isOwner($viewer)) {
      if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) {
        return;
      }
    }

    $this->view->form = $form = new Event_Form_Post_Delete();
    $this->view->clear_cache = true;
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      $topic_id = $post->topic_id;
      $post->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    $topic = Engine_Api::_()->getItem('event_topic', $topic_id);
    $href = ( null === $topic ? $event->getHref() : $topic->getHref() );
    return $this->_forward('success', 'utility', 'core', array(
                'closeSmoothbox' => true,
                'parentRedirect' => $href,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
            ));
  }

}