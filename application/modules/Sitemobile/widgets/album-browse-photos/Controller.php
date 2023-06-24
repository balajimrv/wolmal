<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_AlbumBrowsePhotosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $sitealbum = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
    if (!$sitealbum) {
      return $this->setNoRender();
    }
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      $this->getElement()->removeDecorator('Container');
    } else {
     // $this->getElement()->removeDecorator('Title');
      $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
    }

    $param = array();
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);

    $this->view->is_ajax = $is_ajax = $this->_getParam('ajax', '');
    $this->view->columnHeight = $param['columnHeight'] = $this->_getParam('columnHeight', 205);
    $this->view->showViewMore = $param['showViewMore'] = $this->_getParam('showViewMore', 1);
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    if (empty($is_ajax)) {
//      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitealbum', 'type' => 'photos', 'enabled' => 1));
//      $count_tabs = count($tabs);
//      if (!empty($count_tabs)) {
//        return $this->setNoRender();
//      }
//      $activeTabName = $tabs[0]['name'];
    } else {
      $this->view->is_ajax_load = true;
    }

//    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
//    if (empty($getLightBox)) { echo "sdfsdaf";die;
//      return;
//    }

    $this->view->photoTitleTruncation = $param['photoTitleTruncation'] = $this->_getParam('photoTitleTruncation', 22);
    //$this->view->marginPhoto = $param['margin_photo'] = $this->_getParam('margin_photo', 2);
    // $this->view->photoHeight = $param['photoHeight'] = $this->_getParam('photoHeight', 200);
    //$this->view->photoWidth = $param['photoWidth'] = $this->_getParam('photoWidth', 200);
    $this->view->itemCount = $itemCount = $param['itemCount'] = $this->_getParam('itemCount', 10);
    $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("ownerName"));
//    $sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->enabled;
//    if($sitealbumModule) {
    $this->view->filterTypes = $defaultfilter_types = array("likedPhotos" => "Most Liked", "recentPhotos" => "Recent", "viewedPhotos" => "Most Viewed", 'commentedPhotos' => 'Most Commented', 'featuredPhotos' => 'Featured');
    $defaultFilterType = 'recentPhotos';
//    }
//    else {
//      $this->view->filterTypes = $defaultfilter_types = array("recent_photos" => "Recent", "viewed_photos" => "Most Viewed", 'commented_photos' => 'Most Commented');
//      $defaultFilterType = 'recent_photos';
//    }
    $this->view->filterTabs = $param['filter_types'] = $this->_getParam('filter_types', $defaultfilter_types);
    $this->view->param = $param;
    //$this->view->showLightBox = Engine_Api::_()->getApi('album', 'sitemobile')->showLightBoxPhoto();
//    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.viewertype', 0)) {echo "yyyyyyyyyy";die;
//      return $this->setNoRender();
//    }
    $paramTabName = $this->_getParam('tabName', $defaultFilterType);
    if (!empty($paramTabName))
      $activeTabName = $paramTabName;
    $widgetParams = $this->_getAllParams();

    //$activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitealbum', 'type' => 'photos', 'enabled' => 1, 'name' => $activeTabName));

    $this->view->activeTab = $activeTabName;
    $values = array();
    switch ($activeTabName) {
      case 'recentPhotos':
        $values['orderby'] = 'creation_date';
        break;
      case 'likedPhotos':
        $values['orderby'] = 'like_count';
        break;
      case 'viewedPhotos':
        $values['orderby'] = 'view_count';
        break;
      case 'commentedPhotos':
        $values['orderby'] = 'comment_count';
        break;
      case 'featuredPhotos':
        $values['orderby'] = 'featured';
        break;
      case 'ratingPhotos':
        $values['orderby'] = 'rating';
        break;
      case 'randomPhotos':
        $values['orderby'] = 'random';
        break;
    }

    if (!Engine_Api::_()->sitemobile()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->version, '4.8.5')) {
      $this->view->paginator = $paginator = Engine_Api::_()->sitealbum()->photoBySettings($values);
    } else {
      $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('photos','sitealbum')->photoBySettings($values);
    }

    if (($paginator->getTotalItemCount() <= 0)) {
      return $this->setNoRender();
    }
    //$this->view->activTab_limit = $activTab->limit;
    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->page = $this->_getParam('page', 1);
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->totalPhotos = $this->view->count;
    $this->view->totalPages = ceil(($this->view->totalPhotos) / $itemCount);
    $this->view->params = $params = array();
//    if ($this->view->showLightBox && !empty($values['orderby'])) {
//      $this->view->params = $params = array('type' => $values['orderby'], 'count' => $paginator->getTotalItemCount(), 'title' => $activTab->getTitle() . " Photos");
//    }
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
  }

}