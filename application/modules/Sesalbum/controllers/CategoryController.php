<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: CategoryController.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_CategoryController extends Core_Controller_Action_Standard
{
	//category browse function
	public function browseAction(){
		$this->_helper->content->setEnabled();
	}
	//function to get images as per given album id.
	public function albumDataAction(){
		$album_id = $this->_getParam('album_id', false);
		if($album_id){
			//default params
			if(isset($_POST['params']))
				$params = json_decode($_POST['params'],true);
		$this->view->photo_limit = $photo_limit = isset($params['photo_limit']) ? $params['photo_limit'] : $this->_getParam('photo_limit','8');
		$this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width','120');
		$this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height','80');
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '100');
		$this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] :$this->_getParam('description_truncation', '150');
	 $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('by','view','title','follow','followButton','featuredLabel','sponsoredLabel','description','albumPhoto','albumPhotos','photoThumbnail','albumCount'));
		foreach($show_criterias as $show_criteria)
			$this->view->{$show_criteria.'Active'} = $show_criteria;		
		foreach($show_criterias as $show_criteria)
			$this->view->{$show_criteria.'Active'} = $show_criteria;
			 $resultArray = array();
					$albumDatas =$resultArray['album_data'] =  Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbums(array('album_id'=>$album_id),true);
			 if(in_array('photoCounts',$show_criterias)){
				foreach($albumDatas as $albumData){
					$resultArray['photos']= Engine_Api::_()->getDbTable('photos', 'sesalbum')->getPhotoSelect(array('album_id'=>$albumData->album_id,'limit_data'=>$photo_limit,'paginator'=>false));
					break;
			  }
			 }
			$this->view->resultArray = $resultArray;
		}else{
				$this->_forward('requireauth', 'error', 'core');
		}
	}
	public function indexAction(){
		 $category_id = $this->_getParam('category_id', 0);
		 if($category_id){
				$category_id = Engine_Api::_()->getDbtable('categories', 'sesalbum')->getCategoryId($category_id); 
		 }else{
			 return;
		 }
		 $category = Engine_Api::_()->getItem('sesalbum_category', $category_id);
			if( $category )
			{
				Engine_Api::_()->core()->setSubject($category);
			}else
				$this->_forward('requireauth', 'error', 'core');		
		// item is a type of object chanel
		 // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if( $message_id ) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if( $conversation->hasRecipient(Engine_Api::_()->user()->getViewer()) ) {
        $message_view = true;
      }
    }
    $this->view->message_view = $message_view;
		// Render
   $this->_helper->content->setEnabled();		
	}
}