<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TaxController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_TaxController extends Core_Controller_Action_Standard {

  // COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;
  }

  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
    $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->adminpage = $adminpage = $this->_getParam('adminpage', 1);

    $this->view->tab = $this->_getParam('tab', null);
    $this->view->admin_paginator = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getTaxesPaginator(array('store_id' => 0, 'page' => $adminpage));    
    $this->view->user_paginator = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getTaxesPaginator(array('store_id' => $store_id, 'page' => $page));
  }

  function multideleteTaxAction() {
    $values = $this->getRequest()->getPost();
    foreach ($values['tax_id'] as $key => $value) {
      if ($_POST['method'] == 0) {
        Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->delete(array('tax_id = ?' => $value));
        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('tax_id = ?' => $value));
      } else {
        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('taxrate_id = ?' => $value));
      }
    }
    $this->view->success = 1;
  }

  public function addTaxAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->form = $form = new Sitestoreproduct_Form_Tax_AddTax();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    if (empty($values['status']))
      unset($values['status']);

    //store_id 0 FOR SITE ADMIN
    $values['store_id'] = $store_id;

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      //CRATE TAX ROW
      $row = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->createRow();
      $row->setFromArray($values);
      $id = $row->save();

      $db->commit();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          // 'parentRedirect' => $this->view->url(array('action' => 'store', 'store_id' => $store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'manage-rate', 'tax_id' => $id), 'sitestore_store_dashboard', false),
          // 'parentRedirectTime' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Tax added successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function manageRateAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $tax_id = $this->_getParam('tax_id', null);
    if (!empty($tax_id)) {
      $taxObj = Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id);
      if (empty($taxObj))
        return $this->_forward('notfound', 'error', 'core');
    }else
      return $this->_forward('notfound', 'error', 'core');

    $this->view->pageNo = $this->_getParam('pageno', 1);
    $this->view->taxIdInvalid = false;
    $this->view->adminTax = 1;
    if (empty($taxObj)) {
      $this->view->taxIdInvalid = 1;
      return;
    }
    $this->view->adminTax = $taxObj->store_id; 

    $page = $this->_getParam('page', 1);
    $this->view->tax_id = $tax_id;
    $this->view->tax_title = $taxObj->title;
    $this->view->paginator = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->getTaxRatesPaginator(array('tax_id' => $tax_id, 'page' => $page));    
    
    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }

  // ENABLE AND DISABLE TAX ON MANAGE TAX PAGE
  public function taxEnableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $tax_id = $this->_getParam('id', null);
    $store_id = $this->_getParam('store_id', null);
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $tax = Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id);
      // CHANGING STATUS TO COMPLEMENT OF PRESENT STATUS VALUE
      $tax->status = !$tax->status;
      $tax->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->activeFlag = $tax->status;
  }

  // ENABLE AND DISABLE TAX RATE ON MANAGE TAX PAGE
  public function taxrateEnableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $taxrate_id = $this->_getParam('id', null);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $taxrate = Engine_Api::_()->getItem('sitestoreproduct_taxrate', $taxrate_id);
      // CHANGING STATUS TO COMPLEMENT OF PRESENT STATUS VALUE
      $taxrate->status = !$taxrate->status;
      $taxrate->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->activeFlag = $taxrate->status;
  }

  public function editTaxAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->form = $form = new Sitestoreproduct_Form_Tax_EditTax();

    $taxObj = Engine_Api::_()->getItem('sitestoreproduct_taxes', $this->_getParam('tax_id', false));

    $taxPopulateArray = $taxObj->toArray();
    $form->populate($taxPopulateArray);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      //UPDATING TAX ROW
      $taxObj->title = $values['title'];
      $taxObj->rate_dependency = $values['rate_dependency'];

      if (!empty($values['status'])) {
        $taxObj->status = 1;
      } else {
        $taxObj->status = 0;
      }

      $taxObj->save();
      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Tax edited successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteTaxAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->tax_id = $tax_id = $this->_getParam('tax_id', NULL);
    
    if( empty($tax_id) )
    {
      return $this->_forward('notfound', 'error', 'core');
    }

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id)->delete();
        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('tax_id = ?' => $tax_id));
        
       // WHEN DELETE TAX, THEN ALSO DELETE THAT TAX ENTRY FROM PRODUCT TAX
       $product_ids = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsByTax($tax_id);
       
       foreach( $product_ids as $product )
       {
         $user_tax = @unserialize($product->user_tax);
         foreach( $user_tax as $tax_key => $deleted_tax_id )
         {
           if( $deleted_tax_id == $tax_id )
           {
             unset($user_tax[$tax_key]);
             $new_user_tax = empty($user_tax) ? NULL : @serialize($user_tax);
             Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->update(array("user_tax" => $new_user_tax), array("product_id =?" => $product->product_id));
           }
         }
       }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Tax deleted successfully.'))
      ));
    }
  }

  public function addRateAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    $store_id = $this->_getParam('store_id', null);
    $getType = $this->_getParam('type', null);
    $this->view->tax_id = $tax_id = $this->_getParam('tax_id', null);
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');


    $this->view->flag_region = false;    

    $taxRatesTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
    $countryShowFlag = $taxRatesTable->checkAddedRatesLocations(array('tax_id' => $tax_id));
    $countryArray = $countryArrayTemp = array();
    if (empty($getType) || $getType == 1) {
      $countryArray[] = "";
      
      if (empty($countryShowFlag['all_country']))
        $countryArray['ALL'] = "ALL Countries";      

      $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getCountryAddTaxRate($params = array('tax_id' => $tax_id));

      if( !empty($shippingCountries) ){
        foreach ($shippingCountries as $keys => $shippingCountry) {
          $countryArrayTemp[$shippingCountry['country']] = Zend_Locale::getTranslation($shippingCountry['country'], 'country');
        }
        @asort($countryArrayTemp);
      }      
      $countryArray = array_merge($countryArray, $countryArrayTemp);
    }

    if ( !empty($countryArrayTemp) && (empty($countryShowFlag['all_country']) || empty($countryShowFlag['all_regions']))) {      
      $this->view->form = $form = new Sitestoreproduct_Form_Tax_AddRate(array('showAllCountries' => $countryArray));
    }

    $this->view->allRegionsAdded = $countryShowFlag;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $tempValues = $this->getRequest()->getPost();
    $shoErrorFlag = false;
    $form->handling_type->setValue($values['handling_type']);
    $form->all_regions->setValue($values['all_regions']);
    if ($values['country'] != 'ALL' && !array_key_exists("state", $values) && $values['all_regions'] == 'no' ) {
      $shoErrorFlag = true;
      $error = $this->view->translate('You have not select any location for apply this tax.');
      $error = Zend_Registry::get('Zend_Translate')->_($error);

      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
    }
   
      $form->country->setValue(array($values['country']));
      if(array_key_exists("state", $values) ){
        $flagTempState = '';
        foreach($values["state"] as $tempState) {
          $flagTempState .= "::" . $tempState . '::';
        }
          $this->view->getImplodeState = $flagTempState;
      }
      
      if (!empty($shoErrorFlag))
      return;
    
    if( $values['all_regions'] == 'yes' )
      $tempValues['state'] = array(0);
    
    if( $values['country'] == 'ALL' )
      $tempValues['state'] = array(0);

    foreach ($tempValues['state'] as $state) {
      $values['state'] = $state;
      //SETTING flag_state FOR SHOWING SELECTED SATATE VALUE IF VALDITOR FAILS
      if (!isset($values['state'])) {
        $this->view->flag_region = $values['state'];
      }

      if ($values['handling_type'] == 0)
        $form->tax_rate->setValidators(array());
      else
        $form->tax_price->setValidators(array());

      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      if ($values['country'] == 'ALL') {
        unset($values['state']);
      }

      //UNSET VALUE OF PRICE/RATE ACCORDING TO HANDLING FEE FOR UPDATE ACCORDINGLY
      if ($values['handling_type'] == 1)
        $values['tax_value'] = round($values['tax_rate'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
      else
        $values['tax_value'] = round($values['tax_price'], 2);

      $values['tax_id'] = $tax_id;
      $rateObj = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');

      $db = $rateObj->getAdapter();
      $db->beginTransaction();

      try {
        //CRATE TAX ROW
        $row = $rateObj->createRow();
        $row->setFromArray($values);
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rate added successfully.'))
    ));
  }

  public function editRateAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->flag_state = false;

    $rateObj = Engine_Api::_()->getItem('sitestoreproduct_taxrate', $this->_getParam('taxrate_id', false));
    $taxRatePopulateArray = $rateObj->toArray();

    $regionItem = Engine_Api::_()->getItem('sitestoreproduct_region', $taxRatePopulateArray['state']);

    if ($taxRatePopulateArray['country'] != 'ALL') {
      $regionCountry = Zend_Locale::getTranslation($taxRatePopulateArray['country'], 'country');
      $this->view->flagAllCountries = 0;
    } else {
      $regionCountry = 'All Countries';
      $this->view->flagAllCountries = 1;
    }

    if (empty($taxRatePopulateArray['state'])) {
      $region = 'All Regions';
    } else {
      $region = $regionItem->region;
    }
    
    $this->view->form = $form = new Sitestoreproduct_Form_Tax_EditRate();

    //POPULATE RATE/PRICE AFTER ROUND OFF TILL 2 FRACTION POINT
    if ($taxRatePopulateArray['handling_type'] == 0) {
      $taxRatePopulateArray['tax_price'] = round($taxRatePopulateArray['tax_value'], 2);
    } else {
      $taxRatePopulateArray['tax_rate'] = round($taxRatePopulateArray['tax_value'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
    }

    //SET flag_state FOR POPULATING STATE VALUE
    if (!empty($taxRatePopulateArray['state'])) {
      $this->view->flag_region = $taxRatePopulateArray['state'];
    }

    $form->populate($taxRatePopulateArray);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();

    //SETTING flag_state FOR SHOWING SELECTED SATATE VALUE IF VALDITOR FAILS
    if (!isset($values['state'])) {
      $this->view->flag_region = $values['state'];
    }

    if ($values['handling_type'] == 0)
      $form->tax_rate->setValidators(array());
    else
      $form->tax_price->setValidators(array());

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      //UPDATING TAX ROW
      $rateObj->handling_type = $values['handling_type'];


      if ($values['handling_type'] == 0) {
        $rateObj->tax_value = round($values['tax_price'], 2);
      } else {
        $rateObj->tax_value = round($values['tax_rate'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
      }

      $rateObj->status = $values['status'];

      $rateObj->save();
      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rate edited successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteRateAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $taxrate_id = $this->_getParam('taxrate_id', NULL);
    $this->view->taxrate_id = $taxrate_id;

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $region = Engine_Api::_()->getItem('sitestoreproduct_taxrate', $taxrate_id);
        $region->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rate deleteed successfully.'))
      ));
    }
  }

  public function viewRateAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    $this->view->taxrate = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->getTaxRatesById(array('tax_id' => $this->_getParam('tax_id', NULL), 'status' => 1));
    $this->view->title = $this->_getParam('title', NULL);
  }
  
  public function vatAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $this->view->vat_id = $vat_id = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getStoreVat($store_id, "tax_id");
    $this->view->form = $form = new Sitestoreproduct_Form_Tax_Vat();
    
    if( !empty($vat_id) ) {
      $vatDetail = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->getVatAttribs($vat_id);
      $vatTitle = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getStoreVat($store_id, array("title", "save_price_with_vat", "show_price_with_vat"), 1);
      $values = array('title' => $vatTitle, 'handling_type' => $vatDetail->handling_type, 'tax_price' => $vatDetail->tax_value, 'tax_rate' => $vatDetail->tax_value, 'save_price_with_vat' => $vatTitle->save_price_with_vat, 'show_price_with_vat' => $vatTitle->show_price_with_vat);
      $form->populate($values);
    } else {
      $form->title->setValue('VAT');
    }
  }
  
  public function saveVatDetailAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id');
    @parse_str($_POST['storeVatValues'], $storeVatValues);
    $vat_id = isset($_POST['vat_id']) ? $_POST['vat_id'] : 0;

    $storeVatValues['show_price_with_vat'] = empty($storeVatValues['show_price_with_vat'])? 0: $storeVatValues['show_price_with_vat'];
    $storeVatValues['save_price_with_vat'] = empty($storeVatValues['save_price_with_vat'])? 0: $storeVatValues['save_price_with_vat'];
    
    if (!empty($storeVatValues['handling_type'])){
      
      if($storeVatValues['tax_rate'] >= 0 && $storeVatValues['tax_rate'] <= 100)
      {
        $tax_value = round($storeVatValues['tax_rate'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
      }  else {
        $this->view->VATinvalidRateMessage = true;
        return;
      }
      
    }else{
      $tax_value = round($storeVatValues['tax_price'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      if( !empty($vat_id) ) {
        Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->update(array('title' => $storeVatValues['title'], 'save_price_with_vat' => $storeVatValues['save_price_with_vat'], 'show_price_with_vat' => $storeVatValues['show_price_with_vat']), array('tax_id =?' => $vat_id));
        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->update(array('handling_type' => $storeVatValues['handling_type'], 'tax_value' => $tax_value), array('tax_id =?' => $vat_id));
      } else {
        Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->insert(array(
            'title' => $storeVatValues['title'], 
            'store_id' => $store_id, 
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'is_vat' => 1,
            'save_price_with_vat' => $storeVatValues['save_price_with_vat'],
            'show_price_with_vat' => $storeVatValues['show_price_with_vat']
            ));
        $vat_id = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getAdapter()->lastInsertId();

        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->insert(array(
            'tax_id' => $vat_id,
            'handling_type' => $storeVatValues['handling_type'], 
            'tax_value' => $tax_value, 
            'status' => 1
            ));
      }
      $db->commit();
      $this->view->VATSuccessMessage = true;
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
}