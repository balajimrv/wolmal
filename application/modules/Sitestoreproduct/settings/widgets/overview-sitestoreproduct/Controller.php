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
class Sitestoreproduct_Widget_OverviewSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $this->view->showComments = $this->_getParam('showComments', 0);
    
    //GET EDITOR REVIEW ID
    $params = array();
    $params['resource_id'] = $sitestoreproduct->product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['type'] = 'editor';
    $showAfterEditorReview = $this->_getParam('showAfterEditorReview', 1);
    
    if ($showAfterEditorReview < 2) {
      $editor_review_id = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->canPostReview($params);

      //DONT RENDER IF NO REVIEW ID IS EXIST
      if (empty($editor_review_id) || empty($showAfterEditorReview)) {
        return $this->setNoRender();
      }
    }
    
    $params = $this->_getAllParams();
    $this->view->params = $params;
    if ($this->_getParam('loaded_by_ajax', false)) {
      $this->view->loaded_by_ajax = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->loaded_by_ajax = false;
        if (!$this->_getParam('onloadAdd', false))
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
        
        $this->view->showContent = true;
      }
    } else {
      $this->view->showContent = true;
    }

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1))) {
      return $this->setNoRender();
    }

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');
    $overViewColumn = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('overview');
    $overview = $tableOtherinfo->getColumnValue($sitestoreproduct->getIdentity(), $overViewColumn);
    if(!empty($overview)){
      $this->view->overview = $overview;
    }else{
      $this->view->overview = $tableOtherinfo->getColumnValue($sitestoreproduct->getIdentity(), 'overview');
    }
    if (empty($overview) && !$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
      return $this->setNoRender();
    }

    if (empty($overview) && !$sitestoreproduct->authorization()->isAllowed($viewer, 'overview')) {
      return $this->setNoRender();
    }
  }

}