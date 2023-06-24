<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ReportController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_ReportController extends Core_Controller_Action_Standard {

  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestoreUrl = Engine_Api::_()->sitestore()->getStoreUrl($store_id);

//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

//    //IS USER IS PAGE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation("sitestoreproduct_main");

    $this->view->sitestores_view_menu = 61;

    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $orderTableName = $orderTable->info('name');

    // to calculate the oldest order's creation year
    $select = $orderTable->select();
    $select->from($orderTableName, array('order_id', 'MIN(creation_date) as min_year'))
            ->group('order_id')
            ->limit(1);
    $min_year = $orderTable->fetchRow($select);
    $date = explode(' ', $min_year['min_year']);
    $yr = explode('-', $date[0]);
    $current_yr = date('Y', time());
    $year_array = array();
    $this->view->no_ads = 0;
    if (empty($min_year)) {
      $this->view->no_ads = 1;
    }
    $year_array[$current_yr] = $current_yr;
    while ($current_yr != $yr[0]) {
      $current_yr--;
      $year_array[$current_yr] = $current_yr;
    }

    $this->view->reportform = $reportform = new Sitestoreproduct_Form_Report(array('storeId' => $store_id, 'storeName' => $sitestore->getTitle()));
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);

    // POPULATE FORM
    if (isset($_GET['generate_report'])) {
      $reportform->populate($_GET);

      // Get Form Values
      $values = $reportform->getValues();

      if (($values['select_store'] == 'specific_store') && empty($values['store_ids'])) {
        $reportform->addError('Must fill store name');
        return;
      }

      if (($values['select_product'] == 'specific_product') && empty($values['product_ids'])) {
        $reportform->addError('Must fill product name');
        return;
      }

      $start_cal_date = $values['start_cal'];
      $end_cal_date = $values['end_cal'];
      $start_tm = strtotime($start_cal_date);
      $end_tm = strtotime($end_cal_date);
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

      if (empty($values['format_report'])) {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'export-webpage', 'store_id' => $store_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'sitestoreproduct_report_general', true) . '?' . $url_values[1];
      } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'export-excel', 'store_id' => $store_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'sitestoreproduct_report_general', true) . '?' . $url_values[1];
      }
      // Session Object
      $session = new Zend_Session_Namespace('emptySellerReport');
      if (isset($session->empty_session) && !empty($session->empty_session)) {
        unset($session->empty_session);
      } else {
        header("Location: $url");
      }
    }
    $this->view->empty = $this->_getParam('empty', 0);
  }

  // IF REPORT FORMAT IS EXCEL
  public function exportExcelAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $_GET['store_id'];
    $owner_id = Engine_Api::_()->getItem('sitestore_store', $store_id)->owner_id;

    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $values = $_GET;
      
      if(empty($values['store_ids']) && !empty($values['store_id'])) {
        $values['select_store'] = 'current_store';
      }else if(!empty($values['store_ids'])){
        $values['select_store'] = 'specific_store';
      }
      
      $values = array_merge(array(
          'start_daily_time' => $this->_getParam('start_daily_time', time()),
          'end_daily_time' => $this->_getParam('end_daily_time', time()),
          'owner_id' => $owner_id,
              ), $values);

      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct')->getReports($values);
      $this->view->values = $values;
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index', 'store_id' => $store_id, 'empty' => '1'), 'sitestoreproduct_report_general', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
        // Session Object
        $session = new Zend_Session_Namespace('emptySellerReport');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }

    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }

  // IF REPORT FORMAT IS WEBPAGE
  public function exportWebpageAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

    $this->view->sitebusinesses_view_menu = $this->view->sitestores_view_menu = 61;
    $this->view->store_id = $store_id = $_GET['store_id'];
    $this->view->reportform = $reportform = new Sitestoreproduct_Form_Report(array('store_id' => $store_id));
    $reportform->populate($_GET);

    $owner_id = Engine_Api::_()->getItem('sitestore_store', $store_id)->owner_id;

    // Get Form Values
    $values = $reportform->getValues();

    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!empty($_GET)) {
      $values = $_GET;
      
      if(empty($values['store_ids']) && !empty($values['store_id'])) {
        $values['select_store'] = 'current_store';
      }else if(!empty($values['store_ids'])){
        $values['select_store'] = 'specific_store';
      }
      
      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'owner_id' => $owner_id,
              ), $values);
      $this->view->values = $values;
      $this->view->report_type = $values['report_depend'];
      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct')->getReports($values);

      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index', 'store_id' => $store_id, 'empty' => '1'), 'sitestoreproduct_report_general', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
        // Session Object
        $session = new Zend_Session_Namespace('emptySellerReport');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }

    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }

  // To display stores in the auto suggest at report form
  public function suggeststoresAction() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $text = $this->_getParam('search');
    $store_ids = $this->_getParam('store_ids', null);
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $select = $pageTable->select()->where('title LIKE ?', '%' . $text . '%');

    if (!empty($store_ids)) {
      $select->where("store_id NOT IN ($store_ids)");
    }
    if (!empty($viewer_id)) {
      $select->where("owner_id =?", $viewer_id);
    }

    $select->order('title ASC')->limit($limit);
    $pageObjects = $pageTable->fetchAll($select);

    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($pageObjects as $pages) {
        $data[] = $pages->title;
      }
    } else {
      foreach ($pageObjects as $pages) {
        $data[] = array(
            'id' => $pages->store_id,
            'label' => $pages->title,
            'photo' => $this->view->itemPhoto($pages, 'thumb.icon'),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  // To display products in the auto suggest at report form
  public function suggestproductsAction() {
    $owner_id = $this->_getParam('owner_id', '');
    $text = $this->_getParam('search', $this->_getParam('value'));
    $store_ids = $this->_getParam('store_ids', null);
    $store_id = $this->_getParam('store_id', null);
    $select_store = $this->_getParam('select_store', null);
    $product_ids = $this->_getParam('product_ids', null);
    $limit = $this->_getParam('limit', 40);
    $productCreateFlag = $this->_getParam('create', null);
    
    if( !empty($productCreateFlag) )
      $store_ids = $this->_getParam('store_id');
    
    $isSimpleProduct = $this->_getParam('is_simple');
    $isConfigurableProduct = $this->_getParam('is_configurable');
    $isVirtualProduct = $this->_getParam('is_virtual');
    $isDownloadableProduct = $this->_getParam('is_downloadable');

    $productTypes = array();
    if( $isSimpleProduct == 'true' )
      $productTypes[] = "'simple'";
    if( $isConfigurableProduct == 'true' )
      $productTypes[] = "'configurable'";
    if( $isVirtualProduct == 'true' )
      $productTypes[] = "'virtual'";
    if( $isDownloadableProduct == 'true' )
      $productTypes[] = "'downloadable'";
    
    $selectedProductTypes = @implode(',', $productTypes);
    
    
//    $selectedProductTypes = array('simple' => $isSimpleProduct, 'configurable' => $isConfigurableProduct, 'virtual' => $isVirtualProduct, 'downloadable' => $isDownloadableProduct);

    if (($select_store == 'specific_store') && empty($store_ids))
      $store_ids = $this->_getParam();

    if ($select_store == 'current_store')
      $store_ids = $store_id;

    $productObjects = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsByText($store_ids, $text, $limit, $product_ids, $owner_id, $productCreateFlag, $selectedProductTypes);
    $data = array();

    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($productObjects as $products) {
        $data[] = $products->title;
      }
    } else {
      foreach ($productObjects as $products) {
        $data[] = array(
            'id' => $products->product_id,
            'label' => $products->title,
            'price' => $products->price,
            'product_type' => $products->product_type,
            'photo' => $this->view->itemPhoto($products, 'thumb.icon'),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

}