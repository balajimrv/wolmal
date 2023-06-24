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
class Sesalbum_Widget_FeaturedSponsoredController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  
		// default params
		$value['limit'] = $this->_getParam('limit_data',5);
		$value['tableName'] = $this->_getParam('tableName','photo');
		$value['criteria'] = $this->_getParam('criteria',5);
		$value['info'] = $this->_getParam('info','recently_created');
		$value['view_type'] = $this->_getParam('view_type',2);
		$value['height'] = $this->view->height = $this->_getParam('height','180');
		$value['width'] = $this->view->width = $this->_getParam('width','180');
		$value['show_criterias'] = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		$value['title_truncation'] = $this->view->title_truncation = $this->_getParam('title_truncation','45');
		
		foreach($value['show_criterias'] as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
			
		if($value['tableName'] == 'photo'){
		 $value['tableName'] = 'photos';
		 $this->view->typeWidget = 'photo';		
		 $result = Engine_Api::_()->getDbTable('photos', 'sesalbum')->featuredSponsored($value);
		}else if($value['tableName'] == 'album'){
			$value['tableName'] = 'albums';
			$this->view->typeWidget = 'album';
			$result = Engine_Api::_()->getDbTable('albums', 'sesalbum')->featuredSponsored($value);
		}else
			return $this->setNoRender();
			 
		if($value['criteria'] == 1){
			 $value['typeSpecial'] = 'is_featured';
		 }else if($value['criteria'] == 2){
			 $value['typeSpecial'] = 'is_sponsored';
		 }else if($value['criteria'] == 3){
				$value['typeSpecial'] = 'or'; 
		 }else if($value['criteria'] == 4){
			$value['typeSpecial'] = 'neither';
		 }else
		 	$value['typeSpecial'] = 'comment';
		switch($value['info']){
			case 'recently_created':
				$value['type'] = 'creation_date';
				$order['order']='creation_date';
				break;
			case 'most_viewed':
				$value['type']  = 'view';
				$order['order']='view_count';
				break;
			case 'most_liked':
				$value['type']  = 'like';
				$order['order']='like_count';
				break;
			case 'most_rated':
				$value['type']  = 'rating';
				$order['order']='rating';
				break;
			case 'most_commented':
				$value['type']  = 'comment';
				$order['order']='comment_count';
				break;
			case 'random':
					$value['type']  = 'random';
					$order['order']='creation_date';
			case 'most_favourite':
					$value['type'] = 'favourite';
					$order['order'] = 'favourite_count';
			break;
			case 'most_download':
					$value['type'] = 'download';
					$order['order'] = 'download_count';
			break;
		}
	  $this->view->paginator = $paginator = $result;
		$this->view->type = @$value['type'] ;
		$this->view->specialType = $value['typeSpecial'];
		$this->view->viewer  = Engine_Api::_()->user()->getViewer();
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($value['limit']);
    $paginator->setCurrentPageNumber(1);
		
		 // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0)
      return $this->setNoRender();
	}
}
