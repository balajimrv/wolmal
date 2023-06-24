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
class Sesalbum_Widget_BrowseCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
			$limit = $this->_getParam('limit_data',5);	
			$this->view->icon = $this->_getParam('icon','0');
			$this->view->allign = $this->_getParam('allign','1');
			
			$this->view->type = $type = $this->_getParam('type','album');
			$this->view->show_category_has_count = $show_category_has_count = $this->_getParam('show_category_has_count','1');
			$this->view->show_count = $show_count = $this->_getParam('show_count','1');
			if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
      return $this->setNoRender();
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('categories', 'sesalbum')->getCategoryForWidget(array('paginator'=>true,'column_name'=>'*','show_count'=>$show_count,'type'=>$type,'show_category_has_count'=>$show_category_has_count));
			$this->view->storage = Engine_Api::_()->storage();
			// Set item count per page and current page number
			$paginator->setItemCountPerPage($limit);
			$paginator->setCurrentPageNumber(1);
			// Do not render if nothing to show
			if ($paginator->getTotalItemCount() <= 0)
				return $this->setNoRender();			
	}
}