<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PrintingTagController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Sitestoreproduct_PrintingTagController extends Seaocore_Controller_Action_Standard {

  public function manageAction() {
    
    // ONLY LOGGED IN USER 
    if (!$this->_helper->requireUser()->isValid())
      return;
    $allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);
    if(empty($allowPrintingTag))
      return $this->_forwardCustom('requireauth', 'error', 'core');
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
    $this->view->addNotice = $this->_getParam('notice', null);

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    $params = array();
    $params['store_id'] = $store_id;
    $params['page'] = $this->_getParam('page', 1);
    $params['limit'] = 8;

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('printingtags', 'sitestoreproduct')->getPrintingTagPaginator($params);
  }

  function multideletePrintingTagsAction() {

    $values = $this->getRequest()->getPost();
    foreach ($values['tag_id'] as $tag_id) {
      Engine_Api::_()->getDbtable('printingtags', 'sitestoreproduct')->delete(array('printingtag_id 	 = ?' => $tag_id));
//      Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->delete(array('printingtag_id 	 = ?' => $tag_id));
    }
    $this->view->success = 1;
  }

  function deletePrintingTagAction() {
    //ONLY LOGGED IN USER 
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id', null);
    $tag_id = $this->_getParam('tag_id');
    $this->view->printingtag_id = $tag_id;
    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS STORE ADMIN OR NOT
    if (empty($authValue))
      return $this->_forward('requireauth', 'error', 'core');
    else if ($authValue == 1)
      return $this->_forward('notfound', 'error', 'core');
    if ($this->getRequest()->isPost()) {
      $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);

      //IF TABLE OBJECT NOT EMPTY THEN DELETE ROW
      if (!empty($printingTagItem)) {
        $printingTagItem->delete();
//        Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->delete(array('printingtag_id 	 = ?' => $tag_id));
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Printing Tag deleted successfully.'))
      ));
    }
  }

  public function createAction() {

    // ONLY LOGGED IN USER 
    if (!$this->_helper->requireUser()->isValid())
      return;

    $allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);
    if(empty($allowPrintingTag))
      return $this->_forwardCustom('requireauth', 'error', 'core');
    
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS STORE ADMIN OR NOT
    if (empty($authValue))
      return $this->_forward('requireauth', 'error', 'core');
    else if ($authValue == 1)
      return $this->_forward('notfound', 'error', 'core');

    $this->view->form = $form = new Sitestoreproduct_Form_PrintingTag_AddTag();
    $values = $form->getValues();
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
  }

  // AJAX JSON REQUEST ACTION, WHICH RETURN THE RESULTS FOR "ADDING PRINTING TAG"
  public function savetagAction() {
    $tag_id = !empty($_POST['tag_id']) ? $_POST['tag_id'] : false;
    $form = new Sitestoreproduct_Form_PrintingTag_AddTag();
    $formTitleArray = array(
        'tag_name' => $this->view->translate('Tag Name'),
        'height' => $this->view->translate('Height'),
        'width' => $this->view->translate('Width')
    );
    $getErrors = $values = $tempValues = $address = array();
    @parse_str($_POST['tag_details'], $address);
    $tempValues = $address;

    if (in_array("title", $tempValues['details']))
      $values['title'] = 1;
    else
      $values['title'] = 0;
    if (in_array("category", $tempValues['details']))
      $values['category'] = 1;
    else
      $values['category'] = 0;
    if (in_array("price", $tempValues['details']))
      $values['price'] = 1;
    else
      $values['price'] = 0;
    if (in_array("qr", $tempValues['details']))
      $values['qr'] = 1;
    else
      $values['qr'] = 0;
    $values['tag_name'] = $tempValues['tag_name'];
    $values['height'] = $tempValues['height'];
    $values['width'] = $tempValues['width'];


    $errorMessage = null;
    $form->setDisableTranslator(true);
    $errorObj = $form->processAjax($address);
    $getErrors = Zend_Json::decode($errorObj);
    $this->view->errorFlag = '0';
    //TAKING ERROR MESSAGES IN $errorMessage STRING
    if (@is_array($getErrors)) {
      $errorMessageStr = '';
      foreach ($getErrors as $key => $errorArray) {
        $this->view->errorFlag = '1';

        foreach ($errorArray as $errorMsg) {
          $tempErrorTitle = !empty($formTitleArray[$key]) ? $formTitleArray[$key] : $key;
          $errorMsg = $this->view->translate($errorMsg);
          $errorMessageStr .= '<li>' . $tempErrorTitle . '<ul class="error"><li>' . $errorMsg . '</li></ul></li>';
        }
      }

      $this->view->errorMsgStr = $errorMessageStr;
      $this->view->successMsgStr = $this->view->translate('<ul class="form-notices"><li>Products successfully mapped with printing tag.</li></ul>');
      return;
    }
    $values['font_settings'] = $_POST['font_settings'];
    $values['store_id'] = $_POST['store_id'];
    if (empty($tag_id)) {
      // ADD NEW ROW IN TABLE ACCORDINGLY
      $printingtags = Engine_Api::_()->getDbtable('printingtags', 'sitestoreproduct');
      $db = $printingtags->getAdapter();
      $db->beginTransaction();
      try {
        // CREATE PRINTING TAG ROW
        $values['store_id'] = $_POST['store_id'];
        $values['status'] = 1;
        $values['coordinates'] = $_POST['coordinates'];
        $row = $printingtags->createRow();
        $row->setFromArray($values);
        $row->save();
        
        // MAPP WITH ALL PRODUCTS DEFAULT
//        $params['store_id'] = $_POST['store_id'];
//        $params['printingtag_id'] = $row->printingtag_id;
//        Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->mappWithAllProducts($params);
               
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

//      $this->_forward('print', 'printing-tag', 'sitestoreproduct', array(
//          'printingtag_id' => $row->printingtag_id,
//          'store_id' => $_POST['store_id'],
//          'checkAll' => 1,
//          'layout_disable' => false
//      ));
    } else {
      // EDIT TABLE ACCORDINGLY
      $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);
      $printingTagItem->tag_name = @trim($tempValues['tag_name']);
      $printingTagItem->height = @trim($tempValues['height']);
      $printingTagItem->width = @trim($tempValues['width']);
      if (in_array("title", $tempValues['details']))
        $printingTagItem->title = 1;
      else
        $printingTagItem->title = 0;
      if (in_array("category", $tempValues['details']))
        $printingTagItem->category = 1;
      else
        $printingTagItem->category = 0;
      if (in_array("price", $tempValues['details']))
        $printingTagItem->price = 1;
      else
        $printingTagItem->price = 0;
      if (in_array("qr", $tempValues['details']))
        $printingTagItem->qr = 1;
      else
        $printingTagItem->qr = 0;
      $printingTagItem->coordinates = $_POST['coordinates'];
      $printingTagItem->font_settings = $_POST['font_settings'];
      $printingTagItem->save();
      $this->view->successMsgStr = $this->view->translate("Printing tag has been changed successfully");
    }       
  }
  
  public function printAction() {
    $searchParams = $this->_getParam('searchParams', null);
    if(!empty($searchParams))
      $searchParams = Zend_Json::decode($searchParams);
    
    $tag_id = $this->_getParam('printingtag_id', null);
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $checked_product = $this->_getParam('checked_product', 0);
    $checkAll = $this->_getParam('checkAll', null);

    $product_id = $this->_getParam('product_id', null);
    $layout_disable = $this->_getParam('layout_disable', true);
    $this->view->currency_symbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;

    if (empty($currencySymbol)) {
      $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }

    $productCount = 0;


    if(empty($product_id)) {
      $selected_product = '';
      if (empty($checkAll)) {
        $selected_product = trim($checked_product, ",");
        $selected_product = str_replace("<", "", $selected_product);
        $selected_product = str_replace(">", "", $selected_product);
        $selected_product = trim($selected_product, "");
      }

      $allProductsOfStore = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
      $searchParams['store_id'] = $store_id;
      $searchParams['product_ids'] = $selected_product;
      $select = $allProductsOfStore->getSitestoreproductsSelect($searchParams);
      
      $this->view->paginator = $allProductsOfStore->fetchAll($select);      
    }else {
      $tempPaginatorArray[] = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
      $this->view->paginator = $tempPaginatorArray;
    }
    
    if(!empty($layout_disable))
      $this->_helper->layout->setLayout('default-simple');

    $this->view->printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);
  }

  public function printTagAction() {
    //ONLY LOGGED IN USER CAN ADD OVERVIEW
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    $allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);
    if(empty($allowPrintingTag))
      return $this->_forwardCustom('requireauth', 'error', 'core');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT ID AND OBJECT
    $this->view->product_id = $product_id = $this->_getParam('product_id');

    $this->view->store_id = $store_id = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->store_id;
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

    if (empty($sitestoreproduct))
      return $this->_forwardCustom('notfound', 'error', 'core');

    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.metakeyword', 1))) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "metakeyword")) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $this->view->sitestores_view_menu = 12;
    $this->view->printingTags = Engine_Api::_()->getDbtable('printingtags', 'sitestoreproduct')->getPrintingTagsByProduct($product_id, $store_id);
  }

  public function printingTagEnableAction() {
    $store_id = $this->_getParam('store_id', null);
    $tag_id = $this->_getParam('tag_id', null);

    $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);

    // CHANGING ACTIVE TO COMPLEMENT OF PRESENT ACTIVE VALUE
    $printingTagItem->status = !$printingTagItem->status;
    $printingTagItem->save();
    $this->view->activeFlag = $printingTagItem->status;
  }

  public function showProductsAction() {
//    $printing_tag_id = $this->_getParam('tag_id', null);
    $section_id = $this->_getParam('sectionId', null);
    $store_id = $store_id = $this->_getParam('store_id', null);
//    if (!empty($printing_tag_id)) {
//      $productIdObj = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->productIdByTagId($printing_tag_id, $store_id);
//    } elseif (!empty($section_id) && !empty($store_id)) {
//      $productIdObj = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsBySection($section_id, $store_id);
//    }
    if(!empty($section_id) && !empty($store_id)) {
      $productIdObj = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsBySection($section_id, $store_id);
      $this->view->productsObj = $productIdObj;
    }    
  }

//  public function printAction() {
//
//    $printingtag_id = $this->_getParam('printingtag_id', null);
//    $product_id = $this->_getParam('product_id', null);
//
//    $this->view->printingTagItem = $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $printingtag_id);
//    $this->view->sitestoreproduct = $sitestoreProductsObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
//
//    $this->view->currency_symbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
//    if (empty($currencySymbol)) {
//      $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
//    }
//  }

  public function editPrintingTagAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);
    if(empty($allowPrintingTag))
      return $this->_forwardCustom('requireauth', 'error', 'core');
    
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->tag_id = $tag_id = $this->_getParam('tag_id', null);

    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS STORE ADMIN OR NOT
    if (empty($authValue))
      return $this->_forward('requireauth', 'error', 'core');
    else if ($authValue == 1)
      return $this->_forward('notfound', 'error', 'core');

    $this->view->printingTagItem = $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);

    $printingTagItemArray = $printingTagItem->toArray();
    $details = $allowedDetails = array();

    // TO TAKE ROUND OFF FOR FLOAT VALUES
    $printingTagItemArray['width'] = @round($printingTagItemArray['width'], 2);
    $printingTagItemArray['height'] = @round($printingTagItemArray['height'], 2);

    $printingTagItemArray['details'] = array();
    if ($printingTagItemArray['title'] == "1")
      $printingTagItemArray['details'][] = 'title';
    if ($printingTagItemArray['category'] == "1")
      $printingTagItemArray['details'][] = 'category';
    if ($printingTagItemArray['price'] == "1")
      $printingTagItemArray['details'][] = 'price';
    if ($printingTagItemArray['qr'] == "1")
      $printingTagItemArray['details'][] = 'qr';

    $this->view->form = $form = new Sitestoreproduct_Form_PrintingTag_EditTag();

    $this->view->flag = 1;

    $form->populate($printingTagItemArray);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
  }

  public function previewTagAction() {
    $tag_id = $this->_getParam('tag_id', null);
//    $store_id = $this->_getParam('store_id', null);
    $this->view->printingTagItem = $printingTagItem = Engine_Api::_()->getItem('sitestoreproduct_printingtag', $tag_id);
  }

  public function deleteTagAction() {
    $tag_id = $this->_getParam('tag_id', null);
    $product_id = $this->_getParam('product_id', null);
    if ($this->getRequest()->isPost()) {
      if (!empty($tag_id))
//        Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->delete(array('printingtag_id 	 = ?' => $tag_id, 'product_id 	 = ?' => $product_id));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Printing Tag successfully removed from this product.'))
      ));
    }
  }

  public function fontFamilyAction() {
    $this->view->elementType = $this->_getParam('element_type', 'title');
  }

}