<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminTaxController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminTaxController extends Core_Controller_Action_Admin {

  //TAX SETTINGS
  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_producttax');
    
    $showTipMessage = false;
    $this->view->type = $type = $this->_getParam('type', 0);
    $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
    $this->view->isVatAllow = $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
    
    if (!empty($isVatAllow)) {
      if (empty($type)) {
        $showTipMessage = true;
        $this->view->siteAdminTipMessage = true;
      }
    }

    $this->view->showTipMessage = $showTipMessage;
    if( empty($showTipMessage) ) {
      // SHOW ADMIN VAT FORM
      if( !empty($isVatAllow) && !empty($vatCreator) && empty($type) ) {
        $this->view->showVatForm = true;
        $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_Vat();
        $vat_id = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getStoreVat(0, "tax_id");
        if( !empty($vat_id) ) {
          $vatDetail = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->getVatAttribs($vat_id);
          $vatTitle = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getStoreVat(0, "title");
          $values = array('title' => $vatTitle, 'handling_type' => $vatDetail->handling_type, 'tax_price' => $vatDetail->tax_value, 'tax_rate' => $vatDetail->tax_value);
          $form->populate($values);
        }
        
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
          return;
        }

        $storeVatValues = $this->getRequest()->getPost();
        $form->populate($storeVatValues);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          if (!empty($storeVatValues['handling_type']))
            $tax_value = round($storeVatValues['tax_rate'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));
          else
            $tax_value = round($storeVatValues['tax_price'], Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2));

          if( !empty($vat_id) ) {
            Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->update(array('title' => $storeVatValues['title']), array('tax_id =?' => $vat_id));
            Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->update(array('handling_type' => $storeVatValues['handling_type'], 'tax_value' => $tax_value), array('tax_id =?' => $vat_id));
          } else {
            Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->insert(array(
                'title' => $storeVatValues['title'], 
                'store_id' => 0, 
                'creation_date' => new Zend_Db_Expr('NOW()'),
                'is_vat' => 1
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
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      } else {
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();

    $page = $this->_getParam('page', 1);
    
    $pageTable  = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $pageTable->info('name');

    $taxTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
    $taxTableName = $taxTable->info('name');

    $select = $taxTable->select()
            ->setIntegrityCheck(false)
            ->from($taxTableName)
            ->joinLeft($pageTableName, "$taxTableName.store_id = $pageTableName.store_id", array("$pageTableName.title as store_title"))
            ->group($taxTableName . '.tax_id');
    
    

    if ($type == 0) {
      $select->where($taxTableName . '.store_id = 0');
    } else {
      $select->where($taxTableName . '.store_id != 0');
    }
    
    if( !empty($isVatAllow) ) {
      $select->where($taxTableName . '.is_vat = 1');
    }

    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    // searching
    $this->view->tax_title = '';
    $this->view->store_title = '';
    $this->view->rate_dependecy = '';
    $this->view->status = '';


    if (isset($_POST['search'])) {

      if (!empty($_POST['tax_title'])) {
        $this->view->tax_title = $_POST['tax_title'];
        $select->where($taxTableName . '.title  LIKE ?', '%' . trim($_POST['tax_title']) . '%');
      }

      if (!empty($_POST['store_title'])) {
        $this->view->store_title = $_POST['store_title'];
        $select->where($pageTableName . '.title  LIKE ?', '%' . trim($_POST['store_title']) . '%');
      }

      if (!empty($_POST['rate_dependency'])) {
        $this->view->rate_dependency = $_POST['rate_dependency'];
        $_POST['rate_dependency']--;

        $select->where($taxTableName . '.rate_dependency = ? ', $_POST['rate_dependency']);
      }

      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;

        $select->where($taxTableName . '.status = ? ', $_POST['status']);
      }
    }

    if (empty($values['order'])) {
      $values['order'] = 'tax_id';
    }

    $values = array_merge(array(
        'order' => 'tax_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? ($values['order'] == "page_title" ? $pageTableName . '.title' : $values['order']) : 'tax_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
    }
    }
  }

//ACTION FOR MULTI DELETE WISHLIST
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->delete(array('tax_id = ?' => $value));
          Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('tax_id = ?' => $value));
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'type' => '0'));
  }

  public function addTaxAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_AddTax();

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
    $values['store_id'] = 0;

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

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_producttax');

    $tax_id = $this->_getParam('tax_id', null);
    $taxObj = Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id);
    $this->view->taxTitle = $taxObj->title;

    $this->view->taxIdInvalid = false;
    if (empty($taxObj)) {
      $this->view->taxIdInvalid = 1;
      return;
    }

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('taxrate_id = ?' => $value));
        }
      }
    }

    $page = $this->_getParam('page', 1);
    $this->view->tax_id = $tax_id;
    $this->view->type = $this->_getParam('type', 0);
    $this->view->paginator = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->getTaxRatesPaginator(array(
        'tax_id' => $tax_id,
        'orderby' => 'creation_date'
            ));
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  // ENABLE AND DISABLE TAX ON MANAGE TAX PAGE
  public function taxEnableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $tax_id = $this->_getParam('id', null);

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
    $this->_redirect('admin/sitestoreproduct/tax/index/type/' . $this->_getParam('type', 0));
  }

  // ENABLE AND DISABLE TAX RATE ON MANAGE TAX PAGE
  public function taxrateEnableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $taxrate_id = $this->_getParam('id', null);
    $tax_id = $this->_getParam('tax_id', null);

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
    $this->_redirect('admin/sitestoreproduct/tax/manage-rate/tax_id/' . $tax_id . '/type/' . $this->_getParam('type', 0));
  }

  public function editTaxAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_EditTax();

    $taxObj = Engine_Api::_()->getItem('sitestoreproduct_taxes', $this->_getParam('id', false));

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

    // IN SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');
    $tax_id = $this->_getParam('id', NULL);
    $this->view->tax_id = $tax_id;

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id)->delete();
        Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('tax_id = ?' => $tax_id));

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

    $this->_helper->layout->setLayout('admin-simple');
    
    $this->view->tax_id = $tax_id = $this->_getParam('tax_id', null);

    $this->view->flag_region = false;    

    $taxRatesTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
    $countryShowFlag = $taxRatesTable->checkAddedRatesLocations(array('tax_id' => $tax_id));
    $countryArray = $countryArrayTemp = array();
      $countryArray[] = "";
      
      if (empty($countryShowFlag['all_country']))
        $countryArray['ALL'] = "All Countries";      

      $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getCountryAddTaxRate($params = array('tax_id' => $tax_id));

      if( !empty($shippingCountries) ){
        foreach ($shippingCountries as $keys => $shippingCountry) {
          $countryArrayTemp[$shippingCountry['country']] = Zend_Locale::getTranslation($shippingCountry['country'], 'country');
        }
        @asort($countryArrayTemp);
      }      
      $countryArray = array_merge($countryArray, $countryArrayTemp);
  

    if ( !empty($countryArrayTemp) && (empty($countryShowFlag['all_country']) || empty($countryShowFlag['all_regions']))) {      
      $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_AddRate(array('showAllCountries' => $countryArray));
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
    {
      $tempValues['state'] = array(0);
    }
    
    if( $values['country'] == 'ALL' )
    {
      $tempValues['state'] = array(0);
    }

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

//      unset($values['tax_price']);
//      unset($values['tax_rate']);

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

    $this->_helper->layout->setLayout('admin-simple');

//    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_EditRate();

    $this->view->flag_state = false;

    $rateObj = Engine_Api::_()->getItem('sitestoreproduct_taxrate', $this->_getParam('id', false));
    $taxRatePopulateArray = $rateObj->toArray();

    $regionItem = Engine_Api::_()->getItem('sitestoreproduct_region', $taxRatePopulateArray['state']);
    
    if($taxRatePopulateArray['country'] != 'ALL'){
    $regionCountry = Zend_Locale::getTranslation($taxRatePopulateArray['country'], 'country');
     $this->view->flagAllCountries = 0;
    }
    else{
      $regionCountry = 'All Countries';
      $this->view->flagAllCountries = 1;
    }
    
    if(empty($taxRatePopulateArray['state'])){
      $region = 'All Regions';
    }else{
      $region = $regionItem->region;
    }
    
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Tax_EditRate(array('location' => $region, 'country' => $regionCountry));        

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

    // IN SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');
    $taxrate_id = $this->_getParam('id', NULL);
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rate deleted successfully.'))
      ));
    }
  }

}