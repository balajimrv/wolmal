<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_AlbumHomeErrorController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbumPaginator(array());
		$this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
		$this->view->itemType = $this->_getParam('itemType','album');
    if (count($paginator) > 0)
      return $this->setNoRender();
  }

}