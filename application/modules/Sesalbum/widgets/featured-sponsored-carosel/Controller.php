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
class Sesalbum_Widget_FeaturedSponsoredCaroselController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $value['limit'] = $this->_getParam('limit_data',5);
		$value['type'] = $this->_getParam('featured_sponsored_carosel');
		$value['align'] = $this->_getParam('aliganment_of_widget',1);
		$this->view->mouseover = $this->_getParam('mouseover','1');
		$value['show_criterias'] = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		$this->view->height = $this->_getParam('height','180');
		$this->view->width = $this->_getParam('width','180');
		$this->view->duration = $this->_getParam('duration','300');
		$value['info'] = $this->_getParam('info','recently_created');
		$this->view->title_truncation = $this->_getParam('title_truncation','45');
		foreach($value['show_criterias'] as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		switch($value['info']){
			case 'recently_updated':
				$value['info'] = 'modified_date';
				break;
			case 'most_viewed':
				$value['info'] = 'view_count';
				break;
			case 'most_liked':
				$value['info'] = 'like_count';
				break;
			case 'most_rated':
				$value['info'] = 'rating';
				break;
			case 'most_commented':
				$value['info'] = 'comment_count';
				break;
			case 'most_favourite':
				$value['info'] = 'favourite_count';
				break;
			case 'most_download':
				$value['info'] = 'download_count';
				break;
			default :
				$value['info'] = 'creation_date';
				break;
		}
		$value['customWhere'] = '';
		if($value['align'] == 1)
			$align = 'horizontal';
		else
			$align ='vertical';
		$this->view->align = $align;
		
		if($value['type'] == 1 || $value['type'] == 3){
		 $tableName = 'photos';
		 if($value['type'] == 1){
			 $this->view->type = 'is_featured';
			 $value['customWhere'] = "`is_featured` = 1";
			}else{
				 $this->view->type = 'is_sponsored';
				$value['customWhere'] = "`is_sponsored` = 1";
			}
		}else if($value['type'] == 2 || $value['type'] == 4){
			$tableName = 'albums';	
			if($value['type'] == 2)
			 $value['customWhere'] = "`is_featured` = 1";
			else
				$value['customWhere'] = "`is_sponsored` = 1";
		}else
			$this->setNoRender();
		$itemTable = Engine_Api::_()->getDbTable($tableName, 'sesalbum');
		$select = $itemTable->select()->from($itemTable->info('name'),array('*'))
							->where($itemTable->info('name').'.'.$value['customWhere'])
						  ->order($itemTable->info('name').'.'.$value['info'].' DESC');
			//store data in 
		$tempArray = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if($tableName == 'photos'){
			$albumTable= Engine_Api::_()->getItemTable('album')->info('name');
			$select->setIntegrityCheck(false)
							->joinLeft($albumTable, $albumTable . '.album_id = ' . $itemTable->info('name') . '.album_id', null)
							->where($itemTable->info('name').'.album_id != ?','0')
						  ->where($itemTable->info('name').'.album_id != ?','');
			if ($album_enable_check_privacy)
			{
				//fecth all
				$photos = $itemTable->fetchAll($select);
				//loop over all albums once
				foreach ($photos as $photo)
				{
					$album = $photo->getParent();
					if(!$album)
						continue;
					//check authorization album
					if ($album->authorization()->isAllowed($viewer, 'view'))
					{
						$tempArray[] = $photo;
					}
				}
				$paginator = Zend_Paginator::factory($tempArray);
			}else
				$paginator = Zend_Paginator::factory($select);
		}else if($tableName != 'photos'){
			if($album_enable_check_privacy){
					//fecth all
				$albums = $itemTable->fetchAll($select);
				//loop over all albums once
				foreach ($albums as $album)
				{
					//check authorization album
					if ($album->authorization()->isAllowed($viewer, 'view'))
					{
						$tempArray[] = $album;
					}
				}
				$paginator = Zend_Paginator::factory($tempArray);
			}else
				$paginator = Zend_Paginator::factory($select);
		}
		$this->view->order = $value['info'];
	  $this->view->paginator = $paginator ;
		$this->view->typeWidget = $value['type'];
		$this->view->typeSpecial = $value['info'];
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($value['limit']);
    $paginator->setCurrentPageNumber(1);
		 // Do not render if nothing to show
    if ($paginator->getTotalItemCount() == 0){
      return $this->setNoRender();
		}
	}
}
