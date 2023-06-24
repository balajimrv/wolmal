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
class Sesalbum_Widget_AlbumViewPageController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {	
		//option params
		if(isset($_POST['params']))
			$params = json_decode($_POST['params'],true);
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$this->view->is_related = $is_related = isset($_POST['is_related']) ? true : false;
		
		$this->view->height_cover = $this->_getParam('height_cover','300');
		$this->view->is_fullwidth = $this->_getParam('is_fullwidth','0');
		
	if(!isset($_POST['is_related'])){
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load'); 
		$this->view->height = $defaultHeight =isset($params['height']) ? $params['height'] : $this->_getParam('height', '340px');
		$this->view->width = $defaultWidth= isset($params['width']) ? $params['width'] :$this->_getParam('width', '140px');
		$this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] :$this->_getParam('limit_data', '20');		
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '45');
		
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
		$params = $this->view->params = array('height'=>$defaultHeight,'limit_data' => $limit_data,'pagging'=>$loadOptionData,'show_criterias'=>$show_criterias,'view_type'=>$view_type,'title_truncation' =>$title_truncation,'width'=>$defaultWidth,'insideOutside' =>$insideOutside,'fixHover'=>$fixHover);
	}
	if(Engine_Api::_()->core()->hasSubject()){
			$album = Engine_Api::_()->core()->getSubject();
		}else
			$album =  Engine_Api::_()->getItem('album', $_POST['album_id']);		
		 $this->view->album = $album;
		 $this->view->album_id = $param['id'] = $album->album_id; 
	if(!isset($_POST['is_related'])){
		 $photoTable = Engine_Api::_()->getItemTable('album_photo');
		 // Do other stuff
			$this->view->mine = $mine  = true;
			$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if($viewer->getIdentity() > 0){
			$this->view->canEdit = $viewPermission = $album->authorization()->isAllowed($viewer, 'edit');
			$this->view->canComment =  $album->authorization()->isAllowed($viewer, 'comment');
			$this->view->canCreateMemberLevelPermission = Engine_Api::_()->authorization()->getPermission($viewer, 'album', 'create');
			$this->view->canEditMemberLevelPermission   = Engine_Api::_()->authorization()->getPermission($viewer,'album', 'edit');
			$this->view->canDeleteMemberLevelPermission  = Engine_Api::_()->authorization()->getPermission($viewer,'album', 'delete');
		}
    if(!$is_ajax){
			$viewer = Engine_Api::_()->user()->getViewer();
			//check dependent module sesprofile install or not
			$sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
			//check dependent module sesprofile install or not
			if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module)) {
				//member level check for lock albums
				$viewer = Engine_Api::_()->user()->getViewer();
				if ($viewer->getIdentity() == 0)
					$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
				else
					$level = $viewer;
        $cookieData = isset($_COOKIE['sesalbum_lightbox_value']) ? $_COOKIE['sesalbum_lightbox_value'] : '';
        
				if ($album->is_locked && !in_array($album->album_id,explode(',',$cookieData)) && $viewer->getIdentity() != $album->owner_id) {
					$this->view->locked = true;
				} else {
					$this->view->locked = false;
				}
				$this->view->password = $album->password;
			} else
				$this->view->password = true;
			// Load fields view helpers
			$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');			
			// Values
			$this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($album);
			// Prepare data
			if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
				$album->getTable()->update(array(
						'view_count' => new Zend_Db_Expr('view_count + 1'),
								), array(
						'album_id = ?' => $album->getIdentity(),
				));
				$this->view->mine = $mine = false;
			}
		}else{
			if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
				$this->view->mine = $mine = false;
			}
		}
		$this->view->albumTags = $album->tags()->getTagMaps();
		if(!$is_ajax){
			$this->view->canDownload = $canDownload = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'download');
			$this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type');
			$defaultOptions = $arrayOptions = array();
			foreach($defaultOptionsArray as $key=>$defaultValue){
				if( $this->_getParam($defaultValue.'_order'))
					$order = $this->_getParam($defaultValue.'_order').'||'.$defaultValue;
				else
					$order = (999+$key).'||'.$defaultValue;
				if( $this->_getParam($defaultValue.'_label'))
						$valueLabel = $this->_getParam($defaultValue.'_label');
				else{
					if($defaultValue == 'RecentAlbum')
						$valueLabel ='[USER_NAME]\'s Recent Albums';
					else if($defaultValue == 'Like')
						$valueLabel = 'People Who Like This';
					else if($defaultValue == 'TaggedUser')
						$valueLabel = 'People Who Are Tagged In This Album';
					else if($defaultValue == 'Fav')
						$valueLabel = 'People Who Added This As Favourite';
				}
				$arrayOptions[$order] = $valueLabel;
			}
			ksort($arrayOptions);
			$counter = 0;
			foreach($arrayOptions as $key => $valueOption){
				$key = explode('||',$key);
			if($counter == 0)
				$this->view->defaultOpenTab = $defaultOpenTab = $key[1];
				$defaultOptions[$key[1]]=$valueOption;
				$counter++;
			}
			$this->view->defaultOptions = $defaultOptions;
			if(array_key_exists('RecentAlbum',$defaultOptions)){
			/*Get recent Album of user*/
				$this->view->paginatorRecentAlbum = $paginatorRecentAlbum = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums(array('userId'=>$album->owner_id,'photo_id'=>false,'widget'=>true,'notInclude'=>$album->album_id));
				$this->view->data_showRecentAlbum = $limit_dataRecentAlbum = $this->_getParam('RecentAlbum_limitdata','10');
				$paginatorRecentAlbum->setItemCountPerPage($limit_dataRecentAlbum);
				$paginatorRecentAlbum->setCurrentPageNumber(1);
			}
			if(array_key_exists('Like',$defaultOptions)){
			/*User like Album*/
				$param['type'] = 'album';
				$this->view->paginatorLike = $paginatorLike = Engine_Api::_()->sesalbum()->likeItemCore($param);
				$this->view->data_showLike = $limit_dataLike = $this->_getParam('Like_limitdata','10');
				$paginatorLike->setItemCountPerPage($limit_dataLike);
				$paginatorLike->setCurrentPageNumber(1);
			}
			if(array_key_exists('TaggedUser',$defaultOptions)){
			/**User tag in Album*/
				$this->view->paginatorTaggedUser = $paginatorTaggedUser = Engine_Api::_()->sesalbum()->tagItemCore(array('id'=>$param['id'],'album'=>true));
				$this->view->data_showTagged = $limit_dataTagged = $this->_getParam('TaggedUser_limitdata','10');
				// Set item count per page and current page number
				$paginatorTaggedUser->setItemCountPerPage($limit_dataTagged);
				$paginatorTaggedUser->setCurrentPageNumber(1);
			}
			if(array_key_exists('Fav',$defaultOptions)){
			/**User favourite This Album*/
				$this->view->paginatorFav = $paginatorFav = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getFavourite(array('resource_id'=>$param['id']));
				$this->view->data_showFav = $limit_dataFav = $this->_getParam('Fav_limitdata','10');
				// Set item count per page and current page number
				$paginatorFav->setItemCountPerPage($limit_dataFav);
				$paginatorFav->setCurrentPageNumber(1);
			}
		}
		$this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
    ));
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber($page);
		$this->view->page = $page + 1;
	}
	if(!$is_ajax || isset($_POST['is_related'])){
		if(isset($_POST['paramsRelated'])){
			$paramsRelated = json_decode($_POST['paramsRelated'],true);
		}
		//related albums data
		$pageRelated = isset($_POST['pageRelated']) ? $_POST['pageRelated'] : 1;
		$this->view->loadOptionDataRelated = $loadOptionDataRelated = isset($paramsRelated['paggingRelated']) ? $paramsRelated['paggingRelated'] : $this->_getParam('paggingRelated', 'auto_load'); 
		$this->view->heightRelated = $defaultHeightRelated =isset($paramsRelated['heightRelated']) ? $paramsRelated['heightRelated'] : $this->_getParam('heightRelated', '340px');
		$this->view->widthRelated = $widthRelatedRelated = isset($paramsRelated['widthRelated']) ? $paramsRelated['widthRelated'] :$this->_getParam('widthRelated', '31.6%');
		$this->view->limit_dataRelated = $limit_dataRelated = isset($paramsRelated['limit_dataRelated']) ? $paramsRelated['limit_dataRelated'] :$this->_getParam('limit_dataRelated', '20');		
		$this->view->title_truncationRelated = $title_truncationRelated = isset($paramsRelated['title_truncationRelated']) ? $paramsRelated['title_truncationRelated'] :$this->_getParam('title_truncationRelated', '45');
		$show_criteriasRelated = isset($paramsRelated['show_criteriasRelated']) ? $paramsRelated['show_criteriasRelated'] : $this->_getParam('show_criteriasRelated',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHoverRelated = $fixHoverRelated = isset($paramsRelated['fixHoverRelated']) ? $paramsRelated['fixHoverRelated'] :$this->_getParam('fixHoverRelated', 'fix');
		$this->view->insideOutsideRelated =  $insideOutsideRelated = isset($paramsRelated['insideOutsideRelated']) ? $paramsRelated['insideOutsideRelated'] : $this->_getParam('insideOutsideRelated', 'inside');
		$paramsRelated = $this->view->paramsRelated = array('heightRelated'=>$defaultHeightRelated,'limit_dataRelated' => $limit_dataRelated,'paggingRelated'=>$loadOptionDataRelated,'show_criteriasRelated'=>$show_criteriasRelated,'title_truncationRelated' =>$title_truncationRelated,'widthRelated'=>$widthRelatedRelated,'insideOutsideRelated' =>$insideOutsideRelated,'fixHoverRelated'=>$fixHoverRelated);
		foreach($show_criteriasRelated as $show_criteriaRelated)
			$this->view->{$show_criteriaRelated.'Related'} = $show_criteriaRelated;
	 // end code related
	  $pageRelated = isset($_POST['pageRelated']) ? $_POST['pageRelated'] : 1 ;
		$this->view->relatedAlbumsPaginator = $relatedAlbumsPaginator = Engine_Api::_()->getDbTable('relatedalbums', 'sesalbum')->getitem(array('album_id' =>$album->album_id));
		$relatedAlbumsPaginator->setItemCountPerPage($limit_dataRelated);
		$relatedAlbumsPaginator->setCurrentPageNumber($pageRelated);
		$this->view->pageRelated = $pageRelated+1;
	}
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$is_ajax){
			// rating code
			$this->view->allowShowRating = $allowShowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
			$this->view->allowRating = $allowRating= Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
			$this->view->getAllowRating = $allowRating;
			if($allowRating == 0){
				if($allowShowRating == 0)
					$showRating = false;
				else
					$showRating = true;	
			}else
					$showRating = true;
			$this->view->showRating = $showRating;
			if($showRating){
				$this->view->canRate = $canRate = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'rating_album');
				$this->view->allowRateAgain = $allowRateAgain  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.again',1);
				$this->view->allowRateOwn = $allowRateOwn =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.own',1);
				
				if($canRate == 0 || $allowRating == 0)
					$allowRating = false;
				else
					$allowRating = true;
				if($allowRateOwn == 0 && $mine)
					$allowMine = false;
				else
					$allowMine = true;
				$this->view->allowMine = $allowMine;
				$this->view->allowRating = $allowRating;
				$this->view->viewer_id = $viewer->getIdentity();
				$this->view->rating_type = $rating_type  = 'album';
				$this->view->rating_count = $rating_count = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->ratingCount($album->getIdentity(),$rating_type);
				$this->view->rated = $rated = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->checkRated($album->getIdentity(), $viewer->getIdentity(),$rating_type); 
				$rating_sum  = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->getSumRating($album->getIdentity(),'album');
				if($rating_count != 0){
					$this->view->total_rating_average = $rating_sum/$rating_count;
				}else{
					$this->view->total_rating_average = 0;
				}
				if(!$allowRateAgain && $rated){
					$rated = false;
				}else{
					$rated = true;
				}
				$this->view->ratedAgain = $rated;
			}
		}
		if($is_ajax || $is_related){
			$this->getElement()->removeDecorator('Container');
		}else if(!$is_ajax){
		  $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
			if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.8') >= 0){
				$this->view->doctype('XHTML1_RDFA');
				$this->view->docActive = true;
			}
		}
  }
}