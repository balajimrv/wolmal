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
class Sitestoreproduct_Widget_ProductDocumentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.enable', null)){
      return $this->setNoRender();
    }
    $params = $this->_getAllParams();
    $this->view->params = $params;
    if ($this->_getParam('loaded_by_ajax', false)) {
      $this->view->loaded_by_ajax = true;
    }
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $this->view->canEdit = $sitestoreproduct->authorization()->isAllowed($viewer, 'edit');    
    $tempArray = array();
    $this->view->product_id = $tempArray['product_id'] = $sitestoreproduct->product_id;
    $tempArray['status'] = 1;
    $documentTable = Engine_Api::_()->getDbtable('documents', 'sitestoreproduct');
    $documentsByProductQuery = $documentTable->getwidgetProductDocumentSelect($tempArray);
    $this->view->documentsByProduct = $documentTable->fetchAll($documentsByProductQuery);

    if (!count($documentTable)){
      return $this->setNoRender();
    }
    
    $orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
    $orderProductTableName = $orderProductTable->info('name');
    $orderTableName = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
    $select = $orderProductTable->select()
            ->setIntegrityCheck(false)
            ->from($orderProductTable)
            ->joinLeft($orderTableName, "$orderProductTableName.order_id = $orderTableName.order_id", null)
            ->where("$orderProductTableName.product_id = ?", $sitestoreproduct->product_id)
            ->where("$orderTableName.buyer_id =?", $viewer_id)
            ->where("$orderTableName.order_status = ?", 5)
            ->group("$orderProductTableName.order_id")
            ->limit(1);

    $this->view->temp_result = $temp_result = $orderProductTable->fetchRow($select);
    if (!empty($this->view->loaded_by_ajax)) {
      $this->getElement()->removeDecorator('Title');
    }
  }
  
    //RETURN THE COUNT OF THE product
  public function getChildCount() {
    return Zend_Registry::isRegistered('productCountFlag') ? Zend_Registry::get('productCountFlag') : null;
  }

}