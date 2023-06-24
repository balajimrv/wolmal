<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: StatisticsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_StatisticsController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_periods = array(
      Zend_Date::DAY, //dd
      Zend_Date::WEEK, //ww
      Zend_Date::MONTH, //MM
      Zend_Date::YEAR, //y
  );
  protected $_allPeriods = array(
      Zend_Date::SECOND,
      Zend_Date::MINUTE,
      Zend_Date::HOUR,
      Zend_Date::DAY,
      Zend_Date::WEEK,
      Zend_Date::MONTH,
      Zend_Date::YEAR,
  );
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );
  
  //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;
  
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;
  }

  public function indexAction() {
        //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue =  Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
    $this->view->siteStoreUrl = Engine_Api::_()->sitestore()->getStoreUrl($store_id);
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

//    //IS USER IS PAGE ADMIN OR NOT
//    if(empty($authValue))
//       return $this->_forward('requireauth', 'error', 'core');
//    else if($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
    
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation("sitestoreproduct_main");
    
    $this->view->sitebusinesses_view_menu = $this->view->sitestores_view_menu = 57;
    
    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);

    $ordersTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $ordersTableName = $ordersTable->info('name');

    $date_select = $ordersTable->select()->from($ordersTableName, array('MIN(creation_date) as earliest_order_date'))
            ->where('store_id = ?', $store_id);

    $earliest_order_date = $ordersTable->fetchRow($date_select)->earliest_order_date;
    
    $this->view->sitebusiness = $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->prev_link = 1;
    $this->view->store_id = $store_id;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->earliest_order_date = $earliest_ad_date = strtotime($earliest_order_date);
    if (empty($earliest_order_date) || $earliest_order_date > $startObject) {
      $this->view->prev_link = 0;
    }
    
    if (empty($earliest_order_date) ) {
      $this->view->sitestoreproduct_no_order = 1;
    }

    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Statistics_Filter();
  }

  public function chartDataAction() {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    $store_id = $this->_getParam('store_id', null);

    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }
    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
      die('whoops');
      return;
    }

    // Validate start
    if ($start && !is_numeric($start)) {
      $start = strtotime($start);
    }
    if (!$start) {
      $start = time();
    }

    // Fixes issues with month view
    Zend_Date::setOptions(array(
        'extend_month' => true,
    ));

    // Make start fit to period?
    $startObject = new Zend_Date($start);

    $startObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $endObject->add($periodCount, $period);

    $end_tmstmp_obj = new Zend_Date(time());
    $end_tmstmp_obj->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $end_tmstmp = $end_tmstmp_obj->getTimestamp();
    if ($endObject->getTimestamp() < $end_tmstmp) {
      $end_tmstmp = $endObject->getTimestamp();
    }
    $end_tmstmp_object = new Zend_Date($end_tmstmp);
    $end_tmstmp_object->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    // Get data
    $statsTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $statsTableName = $statsTable->info('name');
    $statsSelect = $statsTable->select()
            ->from($statsTableName, array('SUM(commission_value) as commission', 'SUM(grand_total) as grand_total', 'SUM(sub_total) as sub_total', 'COUNT(creation_date) as transactions', 'SUM(store_tax) as tax', 'SUM(shipping_price) as shipping_price', 'creation_date'))
            ->where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
            ->where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
            ->group("DATE_FORMAT(creation_date, '%Y-%m-%d')")
            ->order('creation_date ASC')
            ->distinct(true);

    if (!empty($store_id)) {
      $statsSelect->where('store_id = ?', $store_id);
    }

    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;

    $data_gross_amount = array();
    $data_net_amount = array();
    $data_transactions = array();
    $data_commission = array();
    $data_tax = array();
    $data_shipping_price = array();
    $dataLabels = array();

    $cumulative_gross_amount = 0;
    $cumulative_net_amount = 0;
    $cumulative_transactions = 0;
    $cumulative_commission = 0;
    $cumulative_tax = 0; 
    $cumulative_shipping_price = 0;
     

    $previous_gross_amount = 0;
    $previous_net_amount = 0;
    $previous_transactions = 0;
    $previous_commission = 0;
    $previous_tax = 0;
    $previous_shipping_price = 0;

    do {
      $nextObject->add(1, $chunk);

      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data_gross_amount[$currentObjectTimestamp] = $cumulative_gross_amount;
      $data_net_amount[$currentObjectTimestamp] = $cumulative_net_amount;
      $data_transactions[$currentObjectTimestamp] = $cumulative_transactions;
      $data_commission[$currentObjectTimestamp] = $cumulative_commission;
      $data_tax[$currentObjectTimestamp] = $cumulative_tax;
      $data_shipping_price[$currentObjectTimestamp] = $cumulative_shipping_price;

      // Get everything that matches
      $currentPeriodCount_gross_amount = 0;
      $currentPeriodCount_net_amount = 0;
      $currentPeriodCount_transactions = 0;
      $currentPeriodCount_commission = 0;
      $currentPeriodCount_tax = 0;
      $currentPeriodCount_shipping_price = 0;

      foreach ($rawData as $rawDatum) {
        $rawDatumDate = strtotime($rawDatum->creation_date);
        if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {

          $currentPeriodCount_gross_amount += $rawDatum->grand_total;
          $currentPeriodCount_net_amount += $rawDatum->sub_total;
          $currentPeriodCount_transactions += $rawDatum->transactions;
          $currentPeriodCount_commission += $rawDatum->commission;
          $currentPeriodCount_tax += $rawDatum->tax;
          $currentPeriodCount_shipping_price += $rawDatum->shipping_price;
        }
      }

      // Now do stuff with it
      switch ($mode) {
        default:
        case 'normal':
          $data_gross_amount[$currentObjectTimestamp] = $currentPeriodCount_gross_amount;
          $data_net_amount[$currentObjectTimestamp] = $currentPeriodCount_net_amount;
          $data_transactions[$currentObjectTimestamp] = $currentPeriodCount_transactions;
          $data_commission[$currentObjectTimestamp] = $currentPeriodCount_commission;
          $data_tax[$currentObjectTimestamp] = $currentPeriodCount_tax;
          $data_shipping_price[$currentObjectTimestamp] = $currentPeriodCount_shipping_price;
          break;

        case 'cumulative':
          $cumulative_gross_amount += $currentPeriodCount_gross_amount;
          $cumulative_net_amount += $currentPeriodCount_net_amount;
          $cumulative_transactions += $currentPeriodCount_transactions;
          $cumulative_commission += $currentPeriodCount_commission;
          $cumulative_tax += $currentPeriodCount_tax;
          $cumulative_shipping_price += $currentPeriodCount_shipping_price;

          $data_gross_amount[$currentObjectTimestamp] = $cumulative_gross_amount;
          $data_net_amount[$currentObjectTimestamp] = $cumulative_net_amount;
          $data_transactions[$currentObjectTimestamp] = $cumulative_transactions;
          $data_commission[$currentObjectTimestamp] = $cumulative_commission;
          $data_tax[$currentObjectTimestamp] = $cumulative_tax;
          $data_shipping_price[$currentObjectTimestamp] = $cumulative_shipping_price;
          break;

        case 'delta':
          $data_gross_amount[$currentObjectTimestamp] = $currentPeriodCount_gross_amount - $previous_gross_amount;
          $data_net_amount[$currentObjectTimestamp] = $currentPeriodCount_net_amount - $previous_net_amount;
          $data_transactions[$currentObjectTimestamp] = $currentPeriodCount_transactions - $previous_transactions;
          $data_commission[$currentObjectTimestamp] = $currentPeriodCount_commission - $previous_commission;
          $data_tax[$currentObjectTimestamp] = $currentPeriodCount_tax - $previous_tax;
          $data_shipping_price[$currentObjectTimestamp] = $currentPeriodCount_shipping_price - $previous_shipping_price;

          $previous_gross_amount = $currentPeriodCount_gross_amount;
          $previous_net_amount = $currentPeriodCount_net_amount;
          $previous_transactions = $currentPeriodCount_transactions;
          $previous_commission = $currentPeriodCount_commission;
          $previous_tax = $currentPeriodCount_tax;
          $previous_shipping_price = $currentPeriodCount_shipping_price;
          break;
      }
      $currentObject->add(1, $chunk);
    } while ($nextObject->getTimestamp() < $end_tmstmp);

    $data_gross_amount_count = count($data_gross_amount);
    $data_net_amount_count = count($data_net_amount);
    $data_transactions_count = count($data_transactions);
    $data_commission_count = count($data_commission);
    $data_tax_count = count($data_tax);
    $data_shipping_price_count = count($data_shipping_price);
    $data = array();
    switch ($type) {
      case 'all':
        $merged_data_array = array_merge($data_gross_amount, $data_net_amount, $data_transactions, $data_commission, $data_tax, $data_shipping_price);
        $data_count_max = max($data_gross_amount_count, $data_net_amount_count, $data_transactions_count, $data_commission_count, $data_tax_count, $data_shipping_price_count);
        $data = $data_gross_amount;
        break;

      case 'grossamount':
        $merged_data_array = $data_gross_amount;
        $data_count_max = $data_gross_amount_count;
        $data = $data_gross_amount;
        break;

      case 'netamount':
        $data = $merged_data_array = $data_net_amount;
        $data_count_max = $data_net_amount_count;
        break;
      
      case 'transactions':
        $data = $merged_data_array = $data_transactions;
        $data_count_max = $data_transactions_count;
        break;

      case 'commission':
        $data = $merged_data_array = $data_commission;
        $data_count_max = $data_commission_count;
        break;
      
      case 'tax':
        $data = $merged_data_array = $data_tax;
        $data_count_max = $data_tax_count;
        break;
      
      case 'shippingprice':
        $data = $merged_data_array = $data_shipping_price;
        $data_count_max = $data_shipping_price_count;
        break;
    }

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ($data as $key => $value) {
      if ($key <= $end_tmstmp) {
        $labelDate->set($key);
        $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
      } else {
        $labelDate->set($end_tmstmp);
        $labelStrings[] = date('n/j/y', $end_tmstmp);
      }
    }

    // Let's expand them by 1.1 just for some nice spacing
    $maxVal = max($merged_data_array);
    $minVal = min($merged_data_array);

    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;

    if ($data_count_max > 10) {
      $xlabelsteps = ceil($data_count_max / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if ($data_count_max > 100) {
      $xsteps = ceil($data_count_max / 100);
    }
    $steps = null;
    if (empty($maxVal)) {
      $steps = 1;
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps($xlabelsteps);
    $x_axis_labels->set_labels($labelStrings);

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels($x_axis_labels);
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal, $steps);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");

    // Make title
    $translate = Zend_Registry::get('Zend_Translate');
    
    $typeTextArray = array(
        'all' => $translate->_('All'),
        'grossamount' => $translate->_('Grand Total'),
        'netamount' => $translate->_('Subtotal'),
        'transactions' => $translate->_('Total Transactions'),
        'commission' => $translate->_('Commission'),
        'tax' => $translate->_('Tax'),
        'shippingprice' => $translate->_('Shipping Price'),
    );
    
    $titleStr = $translate->_(strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $typeTextArray[$type]), '_')));
    $title = new OFC_Elements_Title($titleStr . ' - ' . $this->view->locale()->toDateTime($startObject) .$translate->_(' to '). $this->view->locale()->toDateTime($end_tmstmp_object));
    $title->set_style("{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}");

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graph.bgcolor', '#ffffff'));

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);


    $gross_amount_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphgrossamount.width', '3');
    $net_amount_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphnetamount.width', '3');
    $transactions_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtransactions.width', '3');
    $commission_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphcommission.width', '3');
    $tax_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtax.width', '3');
    $shipping_price_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphshippingprice.width', '3');

    $gross_amount_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphgrossamount.color', '#3299CC');
    $net_amount_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphnetamount.color', '#458B00');
    $transactions_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtransactions.color', '#394BAA');
    $commission_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphcommission.color', '#CD6839');
    $tax_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtax.color', '#f705ff');
    $shipping_price_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphshippingprice.color', '#00ffe1');

    $gross_amount_str = $translate->_('Grand Total');
    $net_amount_str = $translate->_('Subtotal');
    $transactions_str = $translate->_('Total Transactions');
    $commission_str = $translate->_('Commission');
    $tax_str = $translate->_('Tax');
    $shipping_price_str = $translate->_('Shipping Price');

    // Make data
    switch ($type) {

      case 'all':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_gross_amount));
        $graph1->set_key($gross_amount_str, '12');
        $graph1->set_width($gross_amount_width);
        $graph1->set_dot_size('20');
        $graph1->set_colour($gross_amount_color);
        $chart->add_element($graph1);

        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_net_amount));
        $graph2->set_key($net_amount_str, '12');
        $graph2->set_width($net_amount_width);
        $graph2->set_colour($net_amount_color);
        $chart->add_element($graph2);

        $graph3 = new OFC_Charts_Line();
        $graph3->set_values(array_values($data_transactions));
        $graph3->set_key($transactions_str, '12');
        $graph3->set_width($transactions_width);
        $graph3->set_colour($transactions_color);
        $chart->add_element($graph3);

        $graph4 = new OFC_Charts_Line();
        $graph4->set_values(array_values($data_commission));
        $graph4->set_key($commission_str, '12');
        $graph4->set_width($commission_width);
        $graph4->set_colour($commission_color);
        $chart->add_element($graph4);
      
        $graph5 = new OFC_Charts_Line();
        $graph5->set_values(array_values($data_tax));
        $graph5->set_key($tax_str, '12');
        $graph5->set_width($tax_width);
        $graph5->set_colour($tax_color);
        $chart->add_element($graph5);
      
        $graph6 = new OFC_Charts_Line();
        $graph6->set_values(array_values($data_shipping_price));
        $graph6->set_key($shipping_price_str, '12');
        $graph6->set_width($shipping_price_width);
        $graph6->set_colour($shipping_price_color);
        $chart->add_element($graph6);
        break;

      case 'grossamount':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_gross_amount));
        $graph1->set_key($gross_amount_str, '12');
        $graph1->set_width($gross_amount_width);
        $graph1->set_colour($gross_amount_color);
        $chart->add_element($graph1);
        break;

      case 'netamount':
        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_net_amount));
        $graph2->set_key($net_amount_str, '12');
        $graph2->set_width($net_amount_width);
        $graph2->set_colour($net_amount_color);
        $chart->add_element($graph2);
        break;

      case 'transactions':
        $graph3 = new OFC_Charts_Line();
        $graph3->set_values(array_values($data_transactions));
        $graph3->set_key($transactions_str, '12');
        $graph3->set_width($transactions_width);
        $graph3->set_colour($transactions_color);
        $chart->add_element($graph3);
        break;

      case 'commission':
        $graph4 = new OFC_Charts_Line();
        $graph4->set_values(array_values($data_commission));
        $graph4->set_key($commission_str, '12');
        $graph4->set_width($commission_width);
        $graph4->set_colour($commission_color);
        $chart->add_element($graph4);
        break;
      
      case 'tax':
        $graph5 = new OFC_Charts_Line();
        $graph5->set_values(array_values($data_tax));
        $graph5->set_key($tax_str, '12');
        $graph5->set_width($tax_width);
        $graph5->set_colour($tax_color);
        $chart->add_element($graph5);
        break;
      
      case 'shippingprice':
        $graph6 = new OFC_Charts_Line();
        $graph6->set_values(array_values($data_shipping_price));
        $graph6->set_key($shipping_price_str, '12');
        $graph6->set_width($shipping_price_width);
        $graph6->set_colour($shipping_price_color);
        $chart->add_element($graph6);
        break;
    }

    $chart->set_title($title);

    // Send
    $this->getResponse()->setBody($chart->toPrettyString());
  }

}