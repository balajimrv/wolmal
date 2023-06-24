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
class Sitestoreproduct_Widget_ShowSamePosterController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitestoreproduct_video = Engine_Api::_()->getItem('sitestoreproduct_video', $video_id);

    if (empty($sitestoreproduct_video)) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $subject = Engine_Api::_()->getItem('sitestoreproduct_product', $sitestoreproduct_video->product_id);

    //FETCH VIDEOS
    $params = array();
    $widgetType = 'sameposter';
    $params['product_id'] = $sitestoreproduct_video->product_id;
    $params['video_id'] = $sitestoreproduct_video->getIdentity();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['view_action'] = 1;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct')->widgetVideosData($params, '', $widgetType);
    $this->view->count_video = Count($paginator);
    $this->view->limit_sitestoreproduct_video = $this->_getParam('itemCount', 3);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}