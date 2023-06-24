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
class Sesalbum_Widget_TabbedWidgetController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Default option for tabbed widget
		if(isset($_POST['params']))
			$params = json_decode($_POST['params'],true);
		if(isset($_POST['searchParams']) && $_POST['searchParams'])
			parse_str($_POST['searchParams'], $searchArray);
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type');
		$this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
		if(!$is_ajax){
			//order tabs
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
					if($defaultValue == 'recentlySPcreated')
						$valueLabel ='Recently Created';
					else if($defaultValue == 'mostSPviewed')
						$valueLabel = 'Most Viewed';
					else if($defaultValue == 'mostSPliked')
						$valueLabel = 'Most Liked';
					else if($defaultValue == 'mostSPcommented')
						$valueLabel = 'Most Commented';
					else if($defaultValue == 'mostSPrated')
						$valueLabel = 'Most Rated';
					else if($defaultValue == 'mostSPfavourite')
						$valueLabel = 'Most Favourite';
					else if($defaultValue == 'mostSPdownloaded')
						$valueLabel = 'Most Downloaded';
					else if($defaultValue == 'featured')
						$valueLabel = 'Featured';
					else if($defaultValue == 'sponsored')
						$valueLabel = 'Sponsored';
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
			$this->view->tab_option = $this->_getParam('tab_option','filter');
		}
		//default params
		if(isset($_GET['openTab']) || $is_ajax){
		 $this->view->defaultOpenTab = $defaultOpenTab = !empty($searchArray['sort']) ? $searchArray['sort'] : (!empty($_GET['openTab']) ? $_GET['openTab'] : ($this->_getParam('openTab',false) ? $this->_getParam('openTab') : (!empty($params['openTab']) ? $params['openTab'] : '' )));
		}
		$defaultOptions =isset($params['defaultOptions']) ? $params['defaultOptions'] : $defaultOptions;
		$this->view->height = $defaultHeight =isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
		$this->view->width = $defaultWidth= isset($params['width']) ? $params['width'] :$this->_getParam('width', '140px');
		$this->view->hide_row = $hide_row= isset($params['hide_row']) ? $params['hide_row'] :$this->_getParam('hide_row', '1');
		$this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] :$this->_getParam('limit_data', '9');
		$this->view->show_limited_data = $show_limited_data = isset($params['show_limited_data']) ? $params['show_limited_data'] :$this->_getParam('show_limited_data', 'no');
 	  $this->view->limit = ($page-1)*$limit_data;
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '45');
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
		$value['category_id'] = isset($searchArray['category_id']) ? $searchArray['category_id'] :  (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ?  $params['category_id'] : '')) ;
		$value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] :  (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ?  $params['subcat_id'] : '')) ;
		$value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] :  (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ?  $params['subsubcat_id'] : '')) ;
		$value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] :  (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ?  $params['sort'] : '')) ;
		$value['search'] = isset($searchArray['search']) ? $searchArray['search'] :  (isset($_GET['search']) ? $_GET['search'] : (isset($params['search']) ?  $params['search'] : '')) ;
		$value['location'] = isset($searchArray['location']) ? $searchArray['location'] :  (isset($_GET['location']) ? $_GET['location'] : (isset($params['location']) ?  $params['location'] : ''));
		$value['lat'] = isset($searchArray['lat']) ? $searchArray['lat'] :  (isset($searchArray['lat']) ? $_GET['lat'] : (isset($params['lat']) ?  $params['lat'] : ''));
		$value['lng'] = isset($searchArray['lng']) ? $searchArray['lng'] :  (isset($searchArray['lng']) ? $_GET['lng'] : (isset($params['lng']) ?  $params['lng'] : ''));
		$value['miles'] = isset($searchArray['miles']) ? $searchArray['miles'] :  (isset($_GET['miles']) ? $_GET['miles'] : (isset($params['miles']) ?  $params['miles'] : ''));		
		$value['show'] = isset($searchArray['show']) ? $searchArray['show'] :  (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ?  $params['show'] : ''));
	  $this->view->albumPhotoOption =  $albumPhotoOption = isset($params['albumPhotoOption']) ? $params['albumPhotoOption'] : $this->_getParam('photo_album', 'photo');
		$this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', '80');
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
		$params = $this->view->params = array('height'=>$defaultHeight,'width' => $defaultWidth,'limit_data' => $limit_data,'albumPhotoOption' =>$albumPhotoOption,'openTab'=>$defaultOpenTab,'pagging'=>$loadOptionData,'show_criterias'=>$show_criterias,'view_type'=>$view_type,'title_truncation' =>$title_truncation,'insideOutside' =>$insideOutside,'fixHover'=>$fixHover,'defaultOptions'=>$defaultOptions,'sort'=>$value['sort'],'search'=>$value['search'],'category_id'=>$value['category_id'],'subcat_id'=>$value['subcat_id'],'subsubcat_id'=>$value['subsubcat_id'],'lat'=>$value['lat'],'lng'=>$value['lng'],'location'=>$value['location'],'miles'=>$value['miles'],'show_limited_data'=>$show_limited_data,'show'=>$value['show'],'description_truncation'=>$description_truncation);
		$this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
		// initialize type variable type
		$type = '';
		if(!$is_ajax){
			$defaultOpenTab = (isset($_GET['sort']) && $_GET['sort'] ? $_GET['sort'] : $defaultOpenTab);	
		}
		switch($defaultOpenTab){
			case 'recentlySPcreated':
				$popularCol = 'creation_date';
				$type = 'creation';
			break;
			case 'mostSPviewed':
				$popularCol = 'view_count';
				$type = 'view';
			break;
			case 'mostSPliked':
				$popularCol = 'like_count';
				$type = 'like';
			break;
			case 'mostSPcommented':
				$popularCol = 'comment_count';
				$type = 'comment';
			break;
			case 'mostSPrated':
				$popularCol = 'rating';
				$type = 'rating';
			break;
			case 'featured':
				$popularCol = 'is_featured';
				$type = 'is_featured';
				$fixedData = 'is_featured';
			break;
			case 'sponsored':
				$popularCol = 'is_sponsored';
				$type = 'is_sponsored';
				$fixedData = 'is_sponsored';
			break;
			case 'mostSPfavourite':
				$popularCol = 'favourite_count';
				$type = 'favourite';
			break;
			case 'mostSPdownloaded':
				$popularCol = 'download_count';
				$type = 'download';
			break;
			default:
				return $this->setNoRender();
			break;
		}
		$this->view->type = $type;
		$this->view->itemOrigTitle = isset($defaultOptions[$defaultOpenTab]) ? $defaultOptions[$defaultOpenTab] : 'items';
		$value['popularCol'] = isset($popularCol) ? $popularCol : 'creation_date';
		$value['fixedData'] = 	isset($fixedData) ? $fixedData : ''; 
		//fetch data

		if($albumPhotoOption == 'photo'){
		$paginator = Engine_Api::_()->getDbTable('photos', 'sesalbum')->tabWidgetPhotos($value);
		}else{
			$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->tabWidgetAlbums($value);
		}
    $this->view->paginator = $paginator ;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage',$limit_data));
		$this->view->page = $page ;
    $paginator->setCurrentPageNumber($page);
		$this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
		if($is_ajax)
			$this->getElement()->removeDecorator('Container');
		else{
			// Do not render if nothing to show
			if( $paginator->getTotalItemCount() <= 0 ) {
				$nameFunction = 'count'.ucfirst($albumPhotoOption).'s';
				$checkAlbumCount = Engine_Api::_()->getDbTable($albumPhotoOption.'s', 'sesalbum')->$nameFunction();
				if( $checkAlbumCount <= 0 ) {
					return $this->setNoRender();
				}
			}
		}
  }
}