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
class Sitemobile_Widget_EventProfileMembersController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Get params
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->search = $search = $this->_getParam('search');
    $this->view->waiting = $waiting = $this->_getParam('waiting', false);

    // Prepare data
    $this->view->event = $event = $subject;

    $members = null;
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() && $event->isOwner($viewer)) {
      $this->view->waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
      if ($waiting) {
        $this->view->members = $members = $this->view->waitingMembers;
      }
    }

    if (!$members) {
      $select = $event->membership()->getMembersObjectSelect();
      if ($search) {
        $select->where('displayname LIKE ?', '%' . $search . '%');
      }
      $this->view->members = $members = Zend_Paginator::factory($select);
    }

    $paginator = $members;

    $sitemobileEventProfile = Zend_Registry::isRegistered('sitemobileEventProfile') ?  Zend_Registry::get('sitemobileEventProfile') : null;
    
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
    $paginator->setCurrentPageNumber($this->_getParam('page', $page));

    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0 && '' == $search) || empty($sitemobileEventProfile)) {
      return $this->setNoRender();
    }
    
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->sitemobile()->isApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalGuests = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalGuests) /($this->_getParam('itemCountPerPage', 12)));
    //END - SCROLLING WORK
    

    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}