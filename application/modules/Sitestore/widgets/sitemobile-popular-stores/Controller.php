<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitestore_Widget_SitemobilePopularStoresController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    //SITEMOBILE CODE
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->isajax = $this->_getParam('isajax', false);
    if ($this->view->isajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->viewmore = $this->_getParam('viewmore', false);
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }
    
    $params = array();
    //Content display widget setting parameter.
    $param['content_display'] = $this->view->contentDisplayArray = $this->_getParam('content_display', array("date","owner","ratings","likeCount","reviewCount","viewCount"));
    $param['columnHeight']  = $this->view->columnHeight = $this->_getParam('columnHeight', 230);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['popularity'] = $popularity = $this->view->popularity = $this->_getParam('popularity', 'Recently Posted');    
    $params['layouts_views'] = $this->view->layouts_views = $this->_getParam('layouts_views', array("1","2"));   
    
    $limit = $this->_getParam('itemCount',5);
    if($limit){
      $params['limit']= $limit;
    }
    
    $params['page'] = $this->_getParam('page', 1);
    $this->view->isajax = $params['isajax'] = $this->_getParam('isajax', 0);
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
    $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 25);  
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    //location work
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = Engine_Api::_()->sitestore()->enableLocation();
    }
    if ($this->view->detactLocation) {
      $params['locationmiles'] = $this->_getParam('locationmiles', 1000); //in miles
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }
    //end
    $params['paginator'] = true;
    $this->view->sitestores = $paginator = Engine_Api::_()->sitestore()->getLising($popularity,$params);  
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($limit); 
    $this->view->paginator = $paginator->setCurrentPageNumber($params['page']);
    $params['totalpages'] = $this->view->totalCount;
    //SEND ALL PARAMS
    $this->view->params = $params;
     //location work
    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);
    //end
    
    if ( !(count($this->view->sitestores) > 0)){
      return $this->setNoRender();
    }
  }
}

?>
