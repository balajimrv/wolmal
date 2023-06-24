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
class Sesalbum_Widget_AlbumCategoryController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->height = $this->_getParam('height','160px');
		$this->view->width = $this->_getParam('width','260px');
		$this->view->allign_content = $this->_getParam('allign_content','center');
		$params['criteria'] =  $this->_getParam('criteria','');
		$show_criterias =  $this->_getParam('show_criteria',array('title','countAlbums','icon'));
		if(in_array('countAlbums',$show_criterias) || $params['criteria'] == 'most_album')
			$params['countAlbums'] = true;
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
		  return $this->setNoRender();
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
			// Get albums category
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('categories', 'sesalbum')->getCategory($params);		
		if(count($paginator) == 0)
			return;
		
  }
}