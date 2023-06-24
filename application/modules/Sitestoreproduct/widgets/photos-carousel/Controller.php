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
class Sitestoreproduct_Widget_PhotosCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    $sitestoreproductPhotoCarousel = Zend_Registry::isRegistered('sitestoreproductPhotoCarousel') ?  Zend_Registry::get('sitestoreproductPhotoCarousel') : null;

    $this->view->isQuickView = $this->_getParam('isQuickView', null);
    $this->view->album = $album = $sitestoreproduct->getSingletonAlbum();
    $this->view->photo_paginator = $photo_paginator = $album->getCollectiblesPaginator();
    $this->view->total_images = $photo_paginator->getTotalItemCount();
    $minMum = $this->_getParam('minMum', 0);
    
    if (empty($this->view->total_images) || $this->view->total_images < $minMum || empty($sitestoreproductPhotoCarousel)) {
      return $this->setNoRender();
    }
    
    $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 3);
    $this->view->includeInWidget = $this->_getParam('includeInWidget', null);
    $photo_paginator->setItemCountPerPage(100);
    
    if ($this->view->includeInWidget) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
  }

}