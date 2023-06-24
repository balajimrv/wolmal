<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_ProfileSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->title_truncation = $this->_getParam('truncation', 35);
    $this->view->isListView = $isListView = $this->_getParam('isListView', null);
    if( !empty($isListView) )
      $this->view->viewType = 'listview';
    else
      $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->columnWidth = $this->_getParam('columnWidth', '165');
    $this->view->columnHeight = $this->_getParam('columnHeight', '325');
    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
    $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 10);


      if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
        $key = array_search('reviewCount', $this->view->statistics);
        if (!empty($key)) {
          unset($this->view->statistics[$key]);
        }
      }

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    //FETCH RESULTS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator(array(
        'type' => 'browse',
        'orderby' => 'product_id',
        'user_id' => $subject->getIdentity(),
        'is_widget' => 1
            ),null);
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //DONT RENDER IF RESULTS IS ZERO
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    //ADD PRODUCT COUNT
    if ($this->_getParam('titleCount', false)) {
      $this->_childCount = $this->view->totalResults = $paginator->getTotalItemCount();
    }
    $this->view->titleCount = $this->_getParam('titleCount', false);
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}