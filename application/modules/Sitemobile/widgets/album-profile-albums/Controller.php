<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_AlbumProfileAlbumsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('album')
            ->getAlbumPaginator(array('owner' => $subject));
    
    $sitemobileProfileAlbum = Zend_Registry::isRegistered('sitemobileProfileAlbum') ?  Zend_Registry::get('sitemobileProfileAlbum') : null;
    if(empty($sitemobileProfileAlbum)) {
      return $this->setNoRender();
    }

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
    if (Engine_Api::_()->hasModuleBootstrap('sitealbum') && Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitealbum')) {
      return $this->setNoRender();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}