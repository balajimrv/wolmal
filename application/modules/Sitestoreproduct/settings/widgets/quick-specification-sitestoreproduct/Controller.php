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
class Sitestoreproduct_Widget_QuickSpecificationSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') && !Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      return $this->setNoRender();
    }

    $this->view->review = $review = '';
    if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();
    } elseif (Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      $this->view->review = $review = Engine_Api::_()->core()->getSubject();
      $this->view->sitestoreproduct = $sitestoreproduct = $review->getParent();
    }

    //LISITNG SHOULD BE MAPPED WITH PROFILE
    if (empty($this->view->sitestoreproduct->profile_type)) {
      return $this->setNoRender();
    }

    $itemCount = $this->_getParam('itemCount', 5);

    //GET QUICK INFO DETAILS
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestoreproduct/View/Helper', 'Sitestoreproduct_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestoreproduct);

    $this->view->show_fields = $this->view->fieldValueLoopQuickInfoSitestoreproduct($sitestoreproduct, $this->view->fieldStructure, $itemCount);
    if (empty($this->view->show_fields)) {
      return $this->setNoRender();
    }

    //GET WIDGET SETTINGS
    $this->view->show_specificationlink = $this->_getParam('show_specificationlink', 1);
    
    //GET WIDGET SETTINGS
    $this->view->show_specificationtext = $this->view->translate($this->_getParam('show_specificationtext', 'Full Specifications'));
    if(empty($this->view->show_specificationtext)) {
      $this->view->show_specificationtext = 'Full Specifications';
    }

    //FETCH CONTENT DETAILS
    if (!empty($review)) {
      $this->view->tab_id = Engine_Api::_()->sitestoreproduct()->getTabId('sitestoreproduct.specification-sitestoreproduct');
    } else {
      $this->view->contentDetails = Engine_Api::_()->sitestoreproduct()->getWidgetInfo('sitestoreproduct.specification-sitestoreproduct', $this->view->identity);

      if (empty($this->view->contentDetails)) {
        $this->view->contentDetails->content_id = 0;
      }
    }
  }

}