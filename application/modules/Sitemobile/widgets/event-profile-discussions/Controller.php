<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_EventProfileDiscussionsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    if (Engine_Api::_()->sitemobile()->isApp()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Get paginator
    $table = Engine_Api::_()->getItemTable('event_topic');
    $select = $table->select()
            ->where('event_id = ?', $subject->getIdentity())
            ->order('sticky DESC')
            ->order('modified_date DESC');
    ;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    $sitemobileEventProfileDiscussion = Zend_Registry::isRegistered('sitemobileEventProfileDiscussion') ?  Zend_Registry::get('sitemobileEventProfileDiscussion') : null;

    // Do not render if nothing to show and not viewer
    if (($paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity()) || empty($sitemobileEventProfileDiscussion)) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}