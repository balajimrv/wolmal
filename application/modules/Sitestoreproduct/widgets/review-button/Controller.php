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
class Sitestoreproduct_Widget_ReviewButtonController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $product_guid = $this->_getParam('product_guid', null);
    $this->view->isProductProfile = $this->_getParam('isProductProfile', null);
    $this->view->isQuickView = $this->_getParam('isQuickView', null);
    $identity = $this->_getParam('identity', 0);
    $this->view->product_profile_page = $this->_getParam('product_profile_page', 0);
    if (empty($product_guid) && !Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }
    
    if(empty($product_guid) && Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
      $product_guid = $sitestoreproduct->getGuid();
      $this->view->product_profile_page = 1;
      $identity = Engine_Api::_()->sitestoreproduct()->existWidget('sitestoreproduct_reviews', 0);
    }
    else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItemByGuid($product_guid);

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      return $this->setNoRender();
    }

    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $create_review = ($sitestoreproduct->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.allowownerreview', 0) : 1;
    if (empty($create_review)) {
      return $this->setNoRender();
    }

    $create_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_create");

    if ($create_allow) {
      //GET REVIEW TABLE
      $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
      if ($viewer_id) {
        $params = array();
        $params['resource_id'] = $sitestoreproduct->product_id;
        $params['resource_type'] = $sitestoreproduct->getType();
        $params['viewer_id'] = $viewer_id;
        $params['type'] = 'user';
        $this->view->review_id =  $hasPosted = $reviewTable->canPostReview($params);
      } else {
        $this->view->review_id = $hasPosted = 0;
      }
      $this->view->createAllow = empty($hasPosted) ? 1 : 2;
    }

    if ($this->view->product_profile_page) {
      $this->view->contentDetails = Engine_Api::_()->sitestoreproduct()->getWidgetInfo('sitestoreproduct.user-sitestoreproduct', $identity);
    } 
    $this->view->tab = Engine_Api::_()->sitestoreproduct()->getTabId('sitestoreproduct.user-sitestoreproduct'); 
  }

}