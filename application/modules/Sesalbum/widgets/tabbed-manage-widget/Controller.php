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
class Sesalbum_Widget_TabbedManageWidgetController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Default option for tabbed widget
		if(isset($_POST['params']))
			$params = json_decode($_POST['params'],true);
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type');
		$this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
		if(!$is_ajax){
			if(count($defaultOptionsArray) == 0)
					return $this->setNoRender();
			$defaultOptions = $arrayOptions = array();
			foreach($defaultOptionsArray as $key=>$defaultValue){
				if( $this->_getParam($defaultValue.'_order'))
					$order = $this->_getParam($defaultValue.'_order').'||'.$defaultValue;
				else
					$order = (999+$key).'||'.$defaultValue;
				if( $this->_getParam($defaultValue.'_label'))
						$valueLabel = $this->_getParam($defaultValue.'_label');
				else{
					if($defaultValue == 'ownalbum')
						$valueLabel ='Albums';
					else if($defaultValue == 'likeAlbum')
						$valueLabel = 'Liked Albums';
					else if($defaultValue == 'likePhoto')
						$valueLabel = 'Liked Photos';
					else if($defaultValue == 'ratedAlbums')
						$valueLabel = 'Rated Albums';
					else if($defaultValue == 'favouriteAlbums')
						$valueLabel = 'Favourite Albums';
					else if($defaultValue == 'favouritePhotos')
						$valueLabel = 'Favourite Photos';
					else if($defaultValue == 'featuredAlbums')
						$valueLabel = 'Featured Albums';
					else if($defaultValue == 'featuredPhotos')
						$valueLabel = 'Featured Photos';
					else if($defaultValue == 'SponsoredPhotos')
						$valueLabel = 'Sponsored Photos';
					else if($defaultValue == 'SponsoredAlbums')
						$valueLabel = 'Sponsored Albums';
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
		}
		if(isset($_GET['openTab']) || $is_ajax){
		 $this->view->defaultOpenTab = $defaultOpenTab = (isset($_GET['openTab']) ? str_replace('_','SP',$_GET['openTab']) : ($this->_getParam('openTab') != NULL ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : '' )));
		}
		$defaultOptions =isset($params['defaultOptions']) ? $params['defaultOptions'] : $defaultOptions;
		$this->view->height = $defaultHeight =isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
		$this->view->height_masonry = $defaultHeightMasonry =isset($params['height_masonry']) ? $params['height_masonry'] : $this->_getParam('height_masonry', '250');
		$this->view->width = $defaultWidth= isset($params['width']) ? $params['width'] :$this->_getParam('width', '140px');
		$this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] :$this->_getParam('limit_data', '10');
 	  $this->view->limit = ($page-1)*$limit_data;
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '45');
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
		$params  = array('height'=>$defaultHeight,'width' => $defaultWidth,'limit_data' => $limit_data,'openTab'=>$defaultOpenTab,'pagging'=>$loadOptionData,'show_criterias'=>$show_criterias,'view_type'=>$view_type,'title_truncation' =>$title_truncation,'insideOutside' =>$insideOutside,'fixHover'=>$fixHover,'defaultOptions'=>$defaultOptions,'height_masonry'=>$defaultHeightMasonry);		
		$this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
		//initialize type variable type
		$type = '';
		$getItem = false;
		switch($defaultOpenTab){
			case 'ownalbum':
				$type = 'album';
				$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums(array('userId' =>Engine_Api::_()->user()->getViewer()->getIdentity()));
			break;
			case 'likeAlbum':
				$type = 'album';
				$paginator = Engine_Api::_()->sesalbum()->likeItemCore(array('type'=>'album','poster_id' =>Engine_Api::_()->user()->getViewer()->getIdentity()));
				$getItem = true;
			break;
			case 'likePhoto':
				$type = 'photo';
				$paginator = Engine_Api::_()->sesalbum()->likeItemCore(array('type'=>'album_photo','poster_id' =>Engine_Api::_()->user()->getViewer()->getIdentity()));
				$getItem = true;
			break;
			case 'ratedAlbums':
				$type = 'album';
				$paginator = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->getRatingItems(array('user_id' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'type'=>'album'));
				$getItem = true;
			break;
			case 'ratedPhotos':
				$type = 'photo';
				$paginator = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->getRatingItems(array('user_id' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'type'=>'album_photo'));
				$getItem = true;
			break;
			case 'favouriteAlbums':
				$type = 'album';
				$paginator = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getFavourites(array('resource_type'=>'album'));
				$getItem = true;
			break;
			case 'favouritePhotos':
				$type = 'photo';
				$paginator = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getFavourites(array('resource_type'=>'album_photo'));
				$getItem = true;
			break;
			case 'featuredAlbums':
					$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums(array('userId' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'is_featured'=>true));
				$type = 'album';
			break;
			case 'featuredPhotos':
			$paginator = Engine_Api::_()->getDbTable('photos', 'sesalbum')->profilePhotos(array('userId' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'is_featured'=>true));
				$type = 'photo';
			break;
			case 'sponsoredPhotos':
				$paginator = Engine_Api::_()->getDbTable('photos', 'sesalbum')->profilePhotos(array('userId' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'is_sponsored'=>true));
				$type = 'photo';
			break;
			case 'sponsoredAlbums':
				$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums(array('userId' =>Engine_Api::_()->user()->getViewer()->getIdentity(),'is_sponsored'=>true));
				$type = 'album';
			break;
			default:
				return $this->setNoRender();
				break;
		}
		if($defaultOpenTab == 'ownalbum'){
			$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
			$this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
			if($viewer->getIdentity() != 0){
				$this->view->canEdit = Engine_Api::_()->authorization()->getPermission($viewer, 'album', 'edit');			
				$this->view->canDelete = Engine_Api::_()->authorization()->getPermission($viewer, 'album', 'delete');
			}
		}
		$this->view->itemOrigTitle = isset($defaultOptions[$defaultOpenTab]) ? $defaultOptions[$defaultOpenTab] : 'items';
		$this->view->albumPhotoOption = $type;
		$this->view->params = $params;
		$this->view->getItem = $getItem ;
    $this->view->paginator = $paginator ;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
		$this->view->page = $page ;
    $paginator->setCurrentPageNumber($page);
		if($is_ajax)
			$this->getElement()->removeDecorator('Container');
  }
}