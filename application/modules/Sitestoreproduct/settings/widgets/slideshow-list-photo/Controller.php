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
class Sitestoreproduct_Widget_SlideshowListPhotoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }
    
    $this->view->showButtonSlide = $showButtonSlide = $this->_getParam('showButtonSlide', 0);
    $this->view->mouseEnterEvent = $this->_getParam('mouseEnterEvent', 0);
    $this->view->thumbPosition = $thumbPosition = $this->_getParam('thumbPosition', 'bottom');
    $this->view->autoPlay = $this->_getParam('autoPlay', 0);    

    //GET SLIDESHOW WIDTH AND HEIGHT
    $this->view->slideshow_width = $slideshow_width = $this->_getParam('slideshow_width', 600);
    $this->view->slideshow_height = $slideshow_height = $this->_getParam('slideshow_height', 400);
    $this->view->showCaption = $this->_getParam('showCaption', 1);
    $this->view->captionTruncation = $this->_getParam('captionTruncation', 200);

    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $product_id = $sitestoreproduct->getIdentity();

    // GET THE VIDEO TYPE WHICH WE ARE USING CURRENTLY SOCIALENGINE VIDEO OR SEPARATE VIDEO
    $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video');

    // GET THE VALUE OF VIDEO WHICH IS THE MAIN VIDEO IN THE PRODUCT
    $this->view->params = $params = $sitestoreproduct->main_video;

    if (!empty($params)) {
      $this->view->video = $video = Engine_Api::_()->sitestoreproduct()->GetProductVideo($params, $type_video);
    }

    // GET THE EMBEDED CODE OF VIDEO
    if (!empty($video)) {
      $this->view->videoEmbedded = $video->getRichContent(true);
    }

    $this->view->show_slideshow_object = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->GetProductPhoto($product_id, array('show_slidishow' => 1, 'limit' => $this->_getParam('slidesLimit', 20), 'order' => 'order ASC'));

    if (!empty($video)) {
      $main_video_result = array();
      $main_video_result[0] = $video->toArray();
      $this->view->show_slideshow_object = array_merge($main_video_result, $this->view->show_slideshow_object);

      if( $video->type == 3 && $video->status == 1 ) {
				if( !empty($video->file_id) ) {
					$storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
					if( $storage_file ) {
						$this->view->video_location = $storage_file->map();
					}
				}
			}      
    }

    //RESULTS COUNT
    $this->view->num_of_slideshow = $num_of_slideshow = Count($this->view->show_slideshow_object);
    if ($this->view->num_of_slideshow <= 0) {
      return $this->setNoRender();
    }
    
    if ($showButtonSlide) { 
      if ($thumbPosition == 'bottom') {
        $this->view->slidesPerRow = floor($slideshow_width / 50);
        $this->view->totalRow = ceil($num_of_slideshow / $this->view->slidesPerRow);
      } else {
        $this->view->slidesPerColumn = floor($slideshow_height / 50);
        $this->view->totalColumn = ceil($num_of_slideshow / $this->view->slidesPerColumn);
      }
    }    
  }

}
