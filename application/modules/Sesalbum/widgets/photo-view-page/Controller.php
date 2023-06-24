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
class Sesalbum_Widget_photoViewPageController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if(isset($_POST['params'])){
			$params = json_decode($_POST['params'],true);
		}
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		if(Engine_Api::_()->core()->hasSubject('album_photo') && !$is_ajax)
	 	 $photo = Engine_Api::_()->core()->getSubject('album_photo');
		else if(isset($_POST['photo_id'])){
		 $photo = Engine_Api::_()->getItem('album_photo',$_POST['photo_id']);
		 Engine_Api::_()->core()->setSubject($photo); 
		 $photo = Engine_Api::_()->core()->getSubject();
		}else
			 return $this->setNoRender();
		$likeStatus = isset($_POST['criteria']) ? $_POST['criteria'] : $this->_getParam('criteria',array('like','tagged','slideshowPhoto','favourite'));
		$this->view->maxHeight = isset($_POST['maxHeight']) ? $_POST['maxHeight'] : $this->_getParam('maxHeight',900);
		$view_more_tagged = (int)$this->_getParam('view_more_tagged','10');
		if($view_more_tagged == 0) $view_more_tagged = 10;
		$view_more_like = $this->_getParam('view_more_like','10');
		if($view_more_like == 0) $view_more_like = 10;
		$view_more_favourite = $this->_getParam('view_more_favourite','10');
		if($view_more_favourite == 0) $view_more_favourite = 10;
		$view_more_tagged = isset($_POST['view_more_tagged']) ? $_POST['view_more_tagged'] : $view_more_tagged;
		$view_more_like = isset($_POST['view_more_like']) ? $_POST['view_more_like'] : $view_more_like;
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
		  return $this->setNoRender();
		$view_more_favourite = isset($_POST['view_more_favourite']) ? $_POST['view_more_favourite'] : $view_more_favourite;
		foreach($likeStatus as $value)
			$this->view->{"status_".$value} = ${"status_".$value} = true;
		$params = $this->view->params = array('likeStatus'=>$likeStatus,'view_more_tagged'=>$view_more_tagged,'view_more_like'=>$view_more_like);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->photo = $photo ;
		$this->view->album = $album = $photo->getAlbum();
	  if($viewer->getIdentity()>0){
			$this->view->canDownload = $canDownload = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'download');
			$this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
			$this->view->canComment = $canComment = $album->authorization()->isAllowed($viewer, 'comment');
			$this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
			$this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
			$this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
			$this->view->canCommentMemberLevelPermission = Engine_Api::_()->authorization()->getPermission($viewer, 'album', 'comment');
		}
    $this->view->nextPhoto = $photo->getNextPhoto();
    $this->view->previousPhoto = $photo->getPreviousPhoto();
		$this->view->photo_id = $photo->photo_id;
    // Get tags
    $tags = array();
    foreach ($photo->tags()->getTagMaps() as $tagmap) {
      $tags[] = array_merge($tagmap->toArray(), array(
          'id' => $tagmap->getIdentity(),
          'text' => $tagmap->getTitle(),
          'href' => $tagmap->getHref(),
          'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $this->view->tags = $tags;
		// rating code
		$this->view->allowShowRating = $allowShowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.show',1);
		$this->view->allowRating = $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.photo.rating',1);
		$this->view->getAllowRating = $allowRating;
		if($allowRating == 0){
			if($allowShowRating == 0)
				$showRating = false;
			else
				$showRating = true;	
		}else
				$showRating = true;
		$this->view->showRating = $showRating;
	if($showRating != 0){
		$this->view->canRate = $canRate = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'rating_photo');
		$this->view->allowRateAgain = $allowRateAgain  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.again',1);
		$this->view->allowRateOwn = $allowRateOwn =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.own',1);
		if($canRate == 0 || $allowRating == 0)
			$allowRating = false;
		else
			$allowRating = true;
		if($allowRateOwn == 0 && $photo->owner_id == $viewer->getIdentity())
			$allowMine = false;
		else
			$allowMine = true;
		$this->view->allowMine = $allowMine;
		$this->view->allowRating = $allowRating;
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer->getIdentity();
		$this->view->rating_type = $rating_type  = 'album_photo';		
		$this->view->rating_count = $rating_count = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->ratingCount($photo->getIdentity(),$rating_type);
		$this->view->rated = $rated = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->checkRated($photo->getIdentity(), $viewer->getIdentity(),$rating_type); 
		$rating_sum  = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->getSumRating($photo->getIdentity(),'album_photo');
		if($rating_count != 0){
			$this->view->total_rating_average = $rating_sum/$rating_count;
		}else
			$this->view->total_rating_average = 0;
		if(!$allowRateAgain && $rated)
			$rated = false;
		else
			$rated = true;
			
		$this->view->ratedAgain = $rated;
		// end rating code
	}
		
		if(isset($status_like) && $status_like){
			// Get like paginator
			$this->view->album_id = $paramData['id'] = $photo->getIdentity();
			$paramData['type'] = 'album_photo';				
			$this->view->paginator_like = $paginator_like = Engine_Api::_()->sesalbum()->likeItemCore($paramData);
			$this->view->data_show_like = $view_more_like;
			// Set item count per page and current page number
			$paginator_like->setItemCountPerPage($view_more_like);
			$paginator_like->setCurrentPageNumber(1);
		}
		if(isset($status_favourite) && $status_favourite){
			// Get like paginator
			$this->view->album_id = $paramData['id'] = $photo->getIdentity();
			$paramData['type'] = 'album_photo';				
			$this->view->paginator_favourite = $paginator_favourite = Engine_Api::_()->getDbTable('photos', 'sesalbum')->getFavourite(array('resource_id'=>$paramData['id']));
			$this->view->data_show_favourite = $view_more_like;
			// Set item count per page and current page number
			$paginator_favourite->setItemCountPerPage($view_more_like);
			$paginator_favourite->setCurrentPageNumber(1);
		}
		/*tagged people code*/
		if(isset($status_tagged) && $status_tagged){
			$this->view->photo_id = $param['id'] = $photo->getIdentity();
			$this->view->paginator_tagged = $paginator_tagged = Engine_Api::_()->sesalbum()->tagItemCore($param);		
			$this->view->data_show_tagged = $view_more_tagged ;
			// Set item count per page and current page number
			$paginator_tagged->setItemCountPerPage($view_more_tagged);
			$paginator_tagged->setCurrentPageNumber(1);
		}
		if($is_ajax){
			$this->getElement()->removeDecorator('Container');
		}else{
			$viewer = Engine_Api::_()->user()->getViewer();
			//check dependent module sesprofile install or not
			$sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
			$cookieData = isset($_COOKIE['sesalbum_lightbox_value']) ? $_COOKIE['sesalbum_lightbox_value'] : '';
			//check dependent module sesprofile install or not
			if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module) && !in_array($album->album_id,explode(',',$cookieData)) && $viewer->getIdentity() != $album->owner_id) {
				//member level check for lock videos
				$viewer = Engine_Api::_()->user()->getViewer();
				if ($viewer->getIdentity() == 0)
					$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
				else
					$level = $viewer;
				$album = $photo->getParent();
				if ($album->is_locked) {
					$this->view->locked = true;
				} else {
					$this->view->locked = false;
				}
				$this->view->password = $album->password;
			} else
				$this->view->password = true;			
			
			$getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
			if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.8') >= 0){
				$this->view->doctype('XHTML1_RDFA');
				$this->view->docActive = true;
			}
		}
	}
}