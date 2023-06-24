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
class Sitestoreproduct_Widget_WriteSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //GET MODULE NAME
    $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    if ($module != 'sitestoreproduct') {
      return $this->setNoRender();
    }

    if ($this->_getParam('removeContent', false)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    //GET VIEWER ID
    $sitestoreproductWriteReview = Zend_Registry::isRegistered('sitestoreproductWriteReview') ?  Zend_Registry::get('sitestoreproductWriteReview') : null;
    if( empty($sitestoreproductWriteReview) ) {
      return $this->setNoRender();
    }
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->isOwner = 0;

    //DONT RENDER IF SUBJECT IS NOT SET
    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
      $this->view->subjectId = $subject->product_id;
      
      if ($subject->owner_id == $viewer_id) {
        $this->view->isOwner = 1;
      }
      
      $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');
      $this->view->aboutSubject = $tableOtherinfo->getColumnValue($this->view->subjectId, 'about');
    } elseif (Engine_Api::_()->core()->hasSubject('user')) {
      
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
      $this->view->subjectId = $subject->user_id;
      if ($subject->user_id == $viewer_id) {
        $this->view->isOwner = 1;
      }

      $user_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', null);
      $editor_id = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getColumnValue($user_id, 'editor_id');
      $this->view->editor = $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);
      $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);
      $this->view->aboutSubject = $editor->about;
    } else {
      return $this->setNoRender();
    }
    //SITEMOBILE CODE
    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if(empty($this->view->aboutSubject)){
        return $this->setNoRender();
      }
    }
    
    if(empty($this->view->aboutSubject))
        return $this->setNoRender();
  }

}