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
class Sesalbum_Widget_welcomeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->height_slideshow  = $this->_getParam('height_slideshow','500');
		$limit_data_slide  = $this->_getParam('limit_data_slide','10');
		$this->view->is_fullwidth  = $this->_getParam('is_fullwidth','0');
		$slide_title  = $this->view->slide_title = $this->_getParam('slide_title','');
		$slide_descrition = $this->view->slide_descrition  = $this->_getParam('slide_descrition','');
		$enable_search = $this->view->enable_search  = $this->_getParam('enable_search','yes');
		$search_criteria = $this->view->search_criteria  = $this->_getParam('search_criteria','albums');
		$criteria_slide = $this->view->criteria_slide  = $this->_getParam('criteria_slide','featured');
		$slide_to_show = $this->view->slide_to_show  = $this->_getParam('slide_to_show','most_viewed');
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
		  return $this->setNoRender();
		$show_album_under =$this->view->show_album_under  = $this->_getParam('show_album_under','yes');
		$album_criteria  = $this->_getParam('album_criteria','recently_created');
		$criteria_slide_album = $this->_getParam('criteria_slide_album','featured');
		$limit_data_album  = $this->_getParam('limit_data_album','3');
		$title_truncation = $this->view->title_truncation  = $this->_getParam('title_truncation','45');
		$show_statistics = $this->view->show_statistics  = $this->_getParam('show_statistics','yes');
		
		switch($slide_to_show){
			case 'recently_created':
				$popularCol = 'creation_date';
				$type = 'creation';
			break;
			case 'most_viewed':
				$popularCol = 'view_count';
				$type = 'view';
			break;
			case 'most_liked':
				$popularCol = 'like_count';
				$type = 'like';
			break;
			case 'most_commented':
				$popularCol = 'comment_count';
				$type = 'comment';
			break;
			case 'most_download':
				$popularCol = 'download_count';
				$type = 'download';
			break;
			case 'most_rated':
				$popularCol = 'rating';
				$type = 'rating';
			break;
			case 'most_favourite':
				$popularCol = 'favourite';
				$type = 'favourite';
			break;
		}
		//for album
		switch($album_criteria){
			case 'recently_created':
				$popularColAlbum = 'creation_date';
			break;
			case 'most_viewed':
				$popularColAlbum = 'view_count';
			break;
			case 'most_liked':
				$popularColAlbum = 'like_count';
			break;
			case 'most_commented':
				$popularColAlbum = 'comment_count';
			break;
			case 'most_rated':
				$popularColAlbum = 'rating_count';
			break;
			case 'most_favourite':
				$popularColAlbum = 'favourite_count';
			break;
			case 'most_download':
				$popularColAlbum = 'download_count';
			break;
		}
		if(isset($criteria_slide)){
			if($criteria_slide == 'featured')
				$fixedDataPhoto = 'engine4_album_photos.is_featured = 1';
			else if($criteria_slide == 'sponsored')
				$fixedDataPhoto = 'engine4_album_photos.is_sponsored = 1';
			else if($criteria_slide == 'featuredSPSponsored')
				$fixedDataPhoto = 'engine4_album_photos.is_featured = 1 && engine4_album_photos.is_sponsored = 1';
			else if($criteria_slide == 'allincludedfeaturedsponsored')
				$fixedDataPhoto = '';
		  else if($criteria_slide == 'allexcludingfeaturedsponsored')
				$fixedDataPhoto = 'engine4_album_photos.is_featured != 1 && engine4_album_photos.is_sponsored != 1';
		}
		if(isset($criteria_slide_album)){
			if($criteria_slide_album == 'featured')
				$fixedDataAlbum = 'engine4_album_albums.is_featured = 1';
			else if($criteria_slide_album == 'sponsored')
				$fixedDataAlbum = 'engine4_album_albums.is_sponsored = 1';
			else if($criteria_slide_album == 'featuredSPSponsored')
				$fixedDataAlbum = 'engine4_album_albums.is_featured = 1 && engine4_album_albums.is_sponsored = 1';
			else if($criteria_slide_album == 'allincludedfeaturedsponsored')
				$fixedDataAlbum = '';
		  else if($criteria_slide_album == 'allexcludingfeaturedsponsored')
				$fixedDataAlbum = 'engine4_album_albums.is_featured != 1 && engine4_album_albums.is_sponsored != 1';
		}
		$value['popularCol'] = isset($popularCol) ? $popularCol : 'creation_date';
		$value['fixedDataPhoto'] = isset($fixedDataPhoto) ? $fixedDataPhoto : '';
		$albumFixedData = isset($fixedDataAlbum) ? $fixedDataAlbum : '';
		$this->view->stats  = Engine_Api::_()->sesalbum()->statsAlbumPhoto();
		$this->view->paginatorSlide = $paginatorSlide = Engine_Api::_()->getDbTable('photos', 'sesalbum')->tabWidgetPhotos($value);
		$paginatorSlide->setItemCountPerPage($limit_data_slide);
    $paginatorSlide->setCurrentPageNumber(1);
		if($show_album_under == 'yes'){
			$this->view->paginatorAlbums = $paginatorAlbums = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbumSelect(array('order'=>$popularColAlbum,'fixedDataAlbum'=>$fixedDataAlbum));
			$paginatorAlbums->setItemCountPerPage($limit_data_album);
			$paginatorAlbums->setCurrentPageNumber(1);
		}
	}
}