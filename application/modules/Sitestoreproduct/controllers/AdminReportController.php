<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReportController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminReportController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_report');
    
    $this->view->reportType = $reportType = $this->_getParam('type', 0);

    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $orderTableName = $orderTable->info('name');

    // to calculate the oldest order's creation year
    $select = $orderTable->select()->from($orderTableName, array('order_id', 'MIN(creation_date) as min_year'))->group('order_id')->limit(1);
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

    $this->view->reportform = $reportform = new Sitestoreproduct_Form_Admin_Report(array('reportType' => $reportType));
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);

		// POPULATE FORM
    if (isset($_GET['generate_report']) ) {
      $reportform->populate($_GET);

			// Get Form Values
			$values = $reportform->getValues();
      $report_form_error = false;
      
      if( ($values['select_store'] == 'specific_store') && empty($values['store_ids']) )
      {
        $reportform->addError('Must fill store name');
        $report_form_error = true;
      }
      
      if( !empty($reportType) )
      {
        if( ($values['select_product'] == 'specific_product') && empty($values['product_ids']) )
        {
          $reportform->addError('Must fill product name');
          $report_form_error = true;
        }
      }
      
      if( !empty($report_form_error) )
        return;
      
			$start_cal_date = $values['start_cal'];
			$end_cal_date = $values['end_cal'];
			$start_tm = strtotime($start_cal_date);
			$end_tm = strtotime($end_cal_date);
			$url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

			if(empty($values['format_report'])) {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'export-webpage', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm, 'type' => $reportType), 'admin_default', true) . '?' . $url_values[1];
			}
			else {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'export-excel', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm, 'type' => $reportType), 'admin_default', true) . '?' . $url_values[1];
			}
			// Session Object
			$session = new Zend_Session_Namespace('empty_adminredirect');
			if(isset($session->empty_session) && !empty($session->empty_session)) {
				unset($session->empty_session);
       } else {
				header("Location: $url");
			}
    }
    $this->view->empty = $this->_getParam('empty', 0);
  }
  
  // in case of admin's report format is excel file, the form action is redirected to this action
  public function exportExcelAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->post = $post = 0;
    $this->view->reportType = $reportType = $this->_getParam('type', 0);
		$start_daily_time = $this->_getParam('start_daily_time', time());
		$end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $this->view->post = $post = 1;
      $values = $_GET;
      $values = array_merge(array(
									'start_daily_time' => $start_daily_time,
									'end_daily_time' => $end_daily_time,
                  'admin_report' => '1',
                  'type' => $reportType,
              ), $values);
      
      $this->view->values = $values;
      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct')->getReports($values);

      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'index', 'type' => $reportType, 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
				$session->empty_session = 1;
        header("Location: $url");
      }
    }
    
    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }

  // in case of admin's report format is webpage, the form action is redirected to this action
  public function exportWebpageAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_report');

    $this->view->reportType = $reportType = $this->_getParam('type', 0);
    $this->view->post = $post = 0;
		$start_daily_time = $this->_getParam('start_daily_time', time());
		$end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET;
      $values = array_merge(array(
									'start_daily_time' => $start_daily_time,
									'end_daily_time' => $end_daily_time,
                  'admin_report' => '1',
                  'type' => $reportType,
              ), $values);
      $this->view->values = $values;
      
      $this->view->rawdata = $rawdata = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct')->getReports($values);
        
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'index', 'type' => $reportType, 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
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
    $text = $this->_getParam('search');
    $store_ids = $this->_getParam('store_ids', null);
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $select = $pageTable->select()
            ->where('title LIKE ?', '%' . $text . '%');
    if( !empty($store_ids) )
    {
      $select->where("store_id NOT IN ($store_ids)");
    }      
      
            $select->order('title ASC')
            ->limit($limit);
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
    $text = $this->_getParam('search', $this->_getParam('value'));
    $store_ids = $this->_getParam('store_ids', null);
    $select_store = $this->_getParam('select_store', null);
    
    if( ($select_store == 'specific_store') && empty($store_ids) )
    {
      return;
    }
    
    $product_ids = $this->_getParam('product_ids', null);
    $limit = $this->_getParam('limit', 40);
    
    $productObjects = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductsByText($store_ids, $text, $limit, $product_ids);
    $data = array();

    $mode = $this->_getParam('struct');
    if ($mode == 'text') 
    {
      foreach ($productObjects as $products) 
      {
        $data[] = $products->title;
      }
    } 
    else 
    {
      foreach ($productObjects as $products) 
      {
        $data[] = array(
                'id' => $products->product_id,
                'label' => $products->title,
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