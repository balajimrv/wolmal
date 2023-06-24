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
class Sesalbum_Widget_peopleLikeAlbumController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	 if(Engine_Api::_()->core()->hasSubject('album'))
	 	 $album = Engine_Api::_()->core()->getSubject('album');
		else
		 return $this->setNoRender();   
		$this->view->album_id = $param['id'] = $album->album_id;
		$param['type'] = 'album';
    $this->view->paginator = $paginator = Engine_Api::_()->sesalbum()->likeItemCore($param);
		$this->view->data_show = $limit_data = $this->_getParam('view_more','10');
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber(1);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}