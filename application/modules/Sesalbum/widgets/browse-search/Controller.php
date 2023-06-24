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
class Sesalbum_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $filterOptions = (array)$this->_getParam('search_type', array('recentlySPcreated' => 'Recently Created','mostSPviewed' => 'Most Viewed','mostSPliked' => 'Most Liked', 'mostSPcommented' => 'Most Commented','featured' => 'Featured','sponsored' => 'Sponsored','mostSPrated'=>'Most Rated','mostSPfavourite'=>'Most Favourite'));
    $this->view->view_type = $this-> _getParam('view_type', 'horizontal');
		$this->view->search_for = $search_for = $this-> _getParam('search_for', 'album');
		$default_search_type = $this-> _getParam('default_search_type', 'mostSPliked');
		
	 $searchForm = $this->view->searchForm = new Sesalbum_Form_Search(array('searchTitle' => $this->_getParam('search_title', 'yes'),'browseBy' => $this->_getParam('browse_by', 'yes'),'categoriesSearch' => $this->_getParam('categories', 'yes'),'locationSearch' => $this->_getParam('location', 'yes'),'kilometerMiles' => $this->_getParam('kilometer_miles', 'yes'),'searchFor'=>$search_for,'FriendsSearch'=>$this->_getParam('friend_show', 'yes'),'defaultSearchtype'=>$default_search_type));
	 if(isset($_GET['tag_name'])){
		 $searchForm->getElement('search')->setValue($_GET['tag_name']);
	 }
   if($this->_getParam('search_type') !== null && $this->_getParam('browse_by', 'yes') == 'yes'){
		$arrayOptions = $filterOptions;
		$filterOptions = array();
		foreach ($arrayOptions as $filterOption) {
      $value = str_replace(array('SP',''), array(' ',' '), $filterOption);
      $filterOptions[$filterOption] = ucwords($value);
    }
		$filterOptions = array(''=>'')+$filterOptions;
		 $searchForm->sort->setMultiOptions($filterOptions);
		 $searchForm->sort->setValue($default_search_type);
	 }
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $searchForm
            ->setMethod('get')
            ->populate($request->getParams());
  }
}
