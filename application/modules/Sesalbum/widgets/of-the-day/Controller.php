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
class Sesalbum_Widget_OfTheDayController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  
		// default params of the widget
		$type = $this->_getParam('ofTheDayType','album');
		$this->view->height = $defaultHeight = $this->_getParam('height', '180');
		$this->view->width = $defaultWidth= $this->_getParam('width', '180');
		$this->view->limit_data = $limit = $this->_getParam('limit_data', '5');		
		$this->view->title_truncation = $title_truncation =$this->_getParam('title_truncation', '45');
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		
		$customWhere = '';
		if($type == 'albums'){
		 $value['tableName'] = 'album';
		 $value['fieldName'] = 'album_id';	 
		 $value['customResourceType'] = 'album';
		}else if($type == 'photos'){
			$value['tableName'] = 'photo';
			$value['fieldName'] = 'photo_id';
			$value['customResourceType'] = 'album_photo';
		}else
			return $this->setNoRender();
		if($type == 'albums')
			$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getOfTheDayResults($value);
		else
			$paginator = Engine_Api::_()->getDbTable('photos', 'sesalbum')->getOfTheDayResults($value);
	  $this->view->paginator = $paginator ;
		$this->view->typeWidget = $type;
		
    // Set item count per page and current page number

		 // Do not render if nothing to show
    if (empty($paginator)){
      return $this->setNoRender();}
		
	}
}
