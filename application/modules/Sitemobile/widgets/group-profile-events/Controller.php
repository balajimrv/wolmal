<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_GroupProfileEventsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
    // Don't render if event item not available
    if (!Engine_Api::_()->hasItemType('event')) {
      return $this->setNoRender();
    }

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $group = Engine_Api::_()->core()->getSubject('group');
    if (!$group->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    // Get paginator
    $this->view->paginator = $paginator = $group->getEventsPaginator();
    $this->view->canAdd = $canAdd = $group->authorization()->isAllowed(null, 'event') && Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
    $sitemobileGroupProfileEvent = Zend_Registry::isRegistered('sitemobileGroupProfileEvent') ?  Zend_Registry::get('sitemobileGroupProfileEvent') : null;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and cannot upload
    if (($paginator->getTotalItemCount() <= 0 && !$canAdd) || empty($sitemobileGroupProfileEvent)) {
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