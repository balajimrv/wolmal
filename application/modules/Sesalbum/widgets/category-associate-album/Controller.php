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
class Sesalbum_Widget_CategoryAssociateAlbumController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Default option for tabbed widget
		if(isset($_POST['params']))
			$params = json_decode($_POST['params'],true);
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
		$this->view->category_limit = $category_limit =  isset($params['category_limit']) ? $params['category_limit'] : $this->_getParam('category_limit','10');
		$this->view->photo_limit = $photo_limit = isset($params['photo_limit']) ? $params['photo_limit'] : $this->_getParam('photo_limit','8');
		$this->view->album_limit = $album_limit = isset($params['album_limit']) ? $params['album_limit'] : $this->_getParam('album_limit','8');
		$this->view->count_album = $count_album = isset($params['count_album']) ? $params['count_album'] : $this->_getParam('count_album','0');
		$this->view->popularity_album = $popularity_album = isset($params['popularity_album']) ? $params['popularity_album'] : $this->_getParam('popularity_album','most_liked');
		$this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width','120');
		$this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height','80');
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type','1');
		$this->view->seemore_text = $seemore_text = isset($params['seemore_text']) ? $params['seemore_text'] : $this->_getParam('seemore_text','+ See all [category_name]');
		$this->view->allignment_seeall = $allignment_seeall = isset($params['allignment_seeall']) ? $params['allignment_seeall'] : $this->_getParam('allignment_seeall','left');
 	  $criteriaData =  isset($params['criteria']) ? $params['criteria'] : $this->_getParam('criteria','alphabetical');
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '100');
		$this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] :$this->_getParam('description_truncation', '150');
	 $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('by','view','title','follow','followButton','featuredLabel','sponsoredLabel','description','albumPhoto','albumPhotos','photoThumbnail','albumCount','favourite'));
		foreach($show_criterias as $show_criteria)
			$this->view->{$show_criteria.'Active'} = $show_criteria;		
		$params  = array('height'=>$height,'width' => $width,'category_limit'=>$category_limit,'photo_limit'=>$photo_limit,'count_album'=>$count_album,'seemore_text'=>$seemore_text,'allignment_seeall'=>$allignment_seeall,'pagging'=>$loadOptionData,'show_criterias'=>$show_criterias,'title_truncation' =>$title_truncation,'description_truncation'=>$description_truncation,'album_limit'=>$album_limit,'criteria'=>$criteriaData,'popularity_album'=>$popularity_album,'view_type'=>$view_type);		
		$this->view->widgetName = 'category-associate-album';		
		$this->view->paginatorCategory = $paginatorCategory = Engine_Api::_()->getDbTable('categories', 'sesalbum')->getCategory(array('hasAlbum'=>true,'criteria'=>$criteriaData,'albumDesc'=>'desc','paginator'=>'true'));	
		$paginatorCategory->setItemCountPerPage($category_limit);	
		$paginatorCategory->setCurrentPageNumber($page);
		$resultArray = array();
		if($view_type == 0)
			$album_limit = 5;
		if( $paginatorCategory->getTotalItemCount() > 0 ) {
			foreach($paginatorCategory as $key=>$valuePaginator){
				$albumDatas =$resultArray['album_data'][$valuePaginator->category_id] =  Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbums(array('category_id'=>$valuePaginator->category_id,'limit_data'=>$album_limit,'popularity_album'=>$popularity_album),true);
			 if(in_array('photoCounts',$show_criterias) && $view_type == 1){
				foreach($albumDatas as $albumData){
					$resultArray['photos'][$valuePaginator->category_id] = Engine_Api::_()->getDbTable('photos', 'sesalbum')->getPhotoSelect(array('album_id'=>$albumData->album_id,'limit_data'=>$photo_limit,'paginator'=>false));
					break;
			  }
			 }
			}
		}
		$this->view->resultArray = $resultArray;
		$this->view->page = $page ;
		$this->view->params = $params;
		if($is_ajax){
			$this->getElement()->removeDecorator('Container');
		}else{
			$this->view->can_create = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
			// Do not render if nothing to show
			if( $paginatorCategory->getTotalItemCount() <= 0 ) {
			}
		}    
  }
}