<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orders.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Orders extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_orders';
  protected $_rowClass = 'Sitestoreproduct_Model_Order';

  /**
   * Return list of placed orders
   *
   * @param $param = page id/ buyer id of the order
   * @param $flag
   * @return object
   */
  public function getOrdersPaginator($params = array()) {

    $paginator = Zend_Paginator::factory($this->getOrdersSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }

  public function getOrdersSelect($params) {

    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
    $orderTableName = $this->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($orderTableName)
            ->joinLeft($userTableName, "$orderTableName.buyer_id = $userTableName.user_id", array("$userTableName.user_id")) 
            ->group($orderTableName . '.order_id')
            ->order('creation_date DESC')
            ->order('order_id DESC');

    if(!empty($params['store_id'])){
      $select->where("$orderTableName.store_id =?", $params['store_id']);
    }
    
    if(!empty($params['order_id'])){
      $select->where("$orderTableName.order_id =?", $params['order_id']);
    }
    
    if(!empty($params['buyer_id'])){
      $select->where("$orderTableName.buyer_id =?", $params['buyer_id']);
    }

    if (isset($params['search'])) {

      if (!empty($params['order_id']))
        $select->where($orderTableName . '.order_id =?', $params['order_id']);
      
      if (!empty($params['username']))
        $select->where($userTableName . '.displayname  LIKE ?', '%' . trim($params['username']) . '%');

      if (!empty($params['billing_name']) || !empty($params['shipping_name'])) {
        $orderAddressTable = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
        $orderAddressTableName = $orderAddressTable->info('name');
      }

      if (!empty($params['billing_name'])) {
        $selectOrderAddress = $orderAddressTable->select()
                ->from($orderAddressTableName, array("order_id", "CONCAT(f_name,' ',l_name) as name"))
                ->where('type = 0')
                ->where("CONCAT(f_name,' ',l_name) LIKE ?", '%' . trim($params['billing_name']) . '%')
                ->query()
                ->fetchAll();

        $orderIdsString = 0;
        foreach ($selectOrderAddress as $key => $values) {
          $orderIdsString .= "," . $values['order_id'];
        }

        $select->where("$orderTableName.order_id IN ($orderIdsString)");
      }

      if (!empty($params['shipping_name'])) {
        $selectOrderAddress = $orderAddressTable->select()
                ->from($orderAddressTableName, array("order_id", "CONCAT(f_name,' ',l_name) as name"))
                ->where('type = 1')
                ->where("CONCAT(f_name,' ',l_name) LIKE ?", '%' . trim($params['shipping_name']) . '%')
                ->query()
                ->fetchAll();

        $orderIdsString = 0;
        foreach ($selectOrderAddress as $key => $values) {
          $orderIdsString .= "," . $values['order_id'];
        }

        $select->where("$orderTableName.order_id IN ($orderIdsString)");
      }

      if (!empty($params['creation_date_start']))
        $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($params['creation_date_start']));
      
      if (!empty($params['creation_date_end']))
        $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($params['creation_date_end']));
      
      $api = Engine_Api::_()->sitestoreproduct();
       if (!empty($params['order_min_amount']))
        $select->where("$orderTableName.grand_total >=?", $api->getPriceWithCurrency(trim($params['order_min_amount']), 1, 1));  
      
       if (!empty($params['order_max_amount']))
        $select->where("$orderTableName.grand_total <=?", $api->getPriceWithCurrency(trim($params['order_max_amount']), 1, 1));  
       
       if (!empty($params['commission_min_amount']))
        $select->where("$orderTableName.commission_value >=?", $api->getPriceWithCurrency(trim($params['commission_min_amount']), 1, 1));
      
       if (!empty($params['commission_max_amount']))
        $select->where("$orderTableName.commission_value <=?", $api->getPriceWithCurrency(trim($params['commission_max_amount']), 1, 1)); 

      if (!empty($params['delivery_time']))
        $select->where($orderTableName . '.delivery_time  = ?', trim($params['delivery_time']));
      
      if (!empty($params['downpayment'])) {
        if( $params['downpayment'] == 1 )
          $select->where("$orderTableName.is_downpayment = 1 OR $orderTableName.is_downpayment = 2");
        else if( $params['downpayment'] == 2 )
          $select->where($orderTableName . '.is_downpayment = ? ', 2);
        else if( $params['downpayment'] == 3 )
          $select->where($orderTableName . '.is_downpayment = ? ', 1);
        else if( $params['downpayment'] == 4 )
          $select->where($orderTableName . '.is_downpayment = ? ', 0);
      }
        


      if (!empty($params['order_status'])) {
        --$params['order_status'];
        $select->where($orderTableName . '.order_status = ? ', $params['order_status']);
      }
    }

    return $select;
     
  }

  /**
   * Return all orders detail for a parent order id
   *
   * @param $parent_id
   * @return array
   */
  public function getAllOrders($parent_id, $params = array()) {
    $order_table_name = $this->info('name');
    $order_product_table_name = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->info('name');
    
    if( !empty($params) && !empty($params['isDownPaymentEnable']) ) {
      $fetchColumnOrderTable = array("$order_table_name.order_id", "$order_table_name.store_id", "$order_table_name.downpayment_total", "$order_table_name.admin_tax", "$order_table_name.store_tax", "$order_table_name.shipping_price", "$order_table_name.coupon_detail");
      $fetchColumnOrderProductTable = array("$order_product_table_name.downpayment as price", "$order_product_table_name.quantity", "$order_product_table_name.product_title");
    } else {
      $fetchColumnOrderTable = array("$order_table_name.order_id", "$order_table_name.store_id", "$order_table_name.sub_total", "$order_table_name.admin_tax", "$order_table_name.store_tax", "$order_table_name.shipping_price", "$order_table_name.grand_total", "$order_table_name.coupon_detail");
      $fetchColumnOrderProductTable = array("$order_product_table_name.price", "$order_product_table_name.quantity", "$order_product_table_name.product_title");
    }

    $select = $this->select()
                    ->from($order_table_name, $fetchColumnOrderTable)
                    ->setIntegrityCheck(false)
                    ->join($order_product_table_name, "($order_product_table_name.order_id = $order_table_name.order_id)", $fetchColumnOrderProductTable)
                    ->where('parent_id =?', $parent_id);
    
    if( !empty($params) && !empty($params['store_id']) ) {
      $select->where("$order_table_name.store_id =?", $params['store_id']);
    }

    return $select->query()->fetchAll();
  }

  /**
   * Return all order id's for a parent order id
   *
   * @param $parent_id
   * @return array
   */
  public function getOrderIds($parent_id) {
    $select = $this->select()
                    ->from($this->info('name'), array('order_id', 'store_id'))
                    ->where('parent_id =?', $parent_id)->order('order_id')->query()->fetchAll();

    return $select;
  }
  
  /**
   * Return parent if for an order
   *
   * @param $order_id
   * @return int
   */
  public function getParentId($order_id) {
    return $this->select()
                ->from($this->info('name'), array('parent_id'))
                ->where('order_id =?', $order_id)
                ->query()
                ->fetchColumn();
  }

  /**
   * Return sum of sub-total, tax and shipping-price for a store
   *
   * @param $store_id
   * @return object
   */
  public function getTotalAmount($store_id) {
    $tablename=$this->info('name');
    $select = $this->select()
            ->from($tablename, array("SUM($tablename.sub_total) as sub_total", "SUM($tablename.store_tax) as store_tax", "SUM($tablename.shipping_price) as shipping_price", "SUM($tablename.commission_value) as commission_value", "COUNT($tablename.order_id) as order_count"))
            ->where('store_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE \'active\' AND order_status = 5', $store_id);
    
    if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
        $select->where('payment_split = ?', 0);
    }   
    return $this->fetchAll($select);
  }

  /**
   * Return sum of grand total for a parent id
   *
   * @param $parent_order_id
   * @return float
   */
  public function getGrandTotal($parent_order_id) {
    $select = $this->select()
            ->from($this->info('name'), array("SUM(grand_total) as grand_total"))
            ->where('parent_id =?', $parent_order_id);

    return $select->query()->fetchColumn();
  }

  /**
   * Return store earning over a particular time duration
   *
   * @param $store_id
   * @param $time_duration
   * @return float
   */
  public function getStoreEarning($store_id, $time_duration){

    $select = $this->select()
                   ->from($this->info('name'), array("SUM(grand_total) as grand_total"))
                   ->where("store_id =?", $store_id)
//                   ->where("payment_status LIKE 'active'")
//                   ->where("order_status <> 6");
                   ->where("order_status = 5");
     
    if ($time_duration == 'today')
        $select->where("DATE(creation_date) = DATE(NOW())");
     
    if ($time_duration == 'week')
      $select->where("YEARWEEK(creation_date) = YEARWEEK(CURRENT_DATE)");
     
    if ($time_duration == 'month')
      $select->where("YEAR(creation_date) = YEAR(NOW()) AND MONTH(creation_date) = MONTH(NOW())");

    return $select->query()->fetchColumn();
  }
  
  /**
   * Return latest order for a store
   *
   * @param array $params
     @return object
   */
  public function getLatestOrders($params){

    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');
    $orderTableName = $this->info('name');
     
    $select = $userTable->select()
              ->setIntegrityCheck(false)
              ->from($userTableName, array("displayname", "username"))
              ->joinRight($orderTableName, "$orderTableName.buyer_id = $userTableName.user_id", array("$orderTableName.grand_total", "$orderTableName.order_id", "$orderTableName.item_count", "$orderTableName.order_status", "$orderTableName.buyer_id", "$orderTableName.store_id", "$orderTableName.delivery_time", "DATE_FORMAT($orderTableName.creation_date, '%b %d %Y, %h:%i %p') as order_date"))
              ->where("$orderTableName.store_id =?", $params['store_id'])
              ->order("$orderTableName.creation_date DESC");
    
    if(!empty($params['limit']))
    {
      $select->limit($params['limit']);
    }

    return $userTable->fetchAll($select);
  }
  
  /**
   * Return report of a store for a particular time interval over products or orders
   *
   * @param array $values
   * @return object
   */
  public function getReports($values = array()) {

    if (!empty($values['owner_id']))
      $owner_id = $values['owner_id'];
    
    $orderTableName = $this->info('name');

    $productsTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productsTableName = $productsTable->info('name');

    $orderProductsTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
    $orderProductsTableName = $orderProductsTable->info('name');

    if (!empty($values['select_store'])) 
      $store = $values['select_store'];
    
    if( $values['time_summary'] == 'Daily' )
      $day = "%d";
    else
      $day = "";
    
    $select = $this->select()->setIntegrityCheck(false);
    
    if( !empty($values['type']) || (isset($values['report_depend']) && $values['report_depend'] == 'product') )
    {
      $select->from($orderTableName, array("COUNT($orderTableName.order_id) as order_count", "DATE_FORMAT($orderTableName.creation_date, '$day %M %Y') as creation_date", "$orderTableName.store_id"))
             ->join($orderProductsTableName, $orderProductsTableName . '.order_id = ' . $orderTableName . '.order_id', array("SUM($orderProductsTableName.quantity) AS quantity", "SUM($orderProductsTableName.price) AS price"))
             ->join($productsTableName, "($productsTableName.product_id  = $orderProductsTableName.product_id)", array("title", "product_code", "product_id"));
      
      $product = $values['select_product'];
      if( $product == 'specific_product' )
      {
        $product_ids = $values['product_ids'];
        $select->where("$productsTableName.product_id IN($product_ids)");
      }
      if( $store != 'all' )
      {
        if( $store == 'current_store' )
          $store_ids = $values['store_id'];
        elseif( $store == 'specific_store' )
          $store_ids = $values['store_ids'];

        $select->where("$productsTableName.store_id IN($store_ids)");
      }
      if( !empty($owner_id) )
      {
        $manageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $manageAdminsTableName = $manageAdminsTable->info('name');
    
        $select->join($manageAdminsTableName, "($manageAdminsTableName.store_id = $productsTableName.store_id)", array(''))
               ->where("$manageAdminsTableName.user_id =?", $owner_id);
      }
      $product_group_by = "$productsTableName.product_id";
      $order_group_by = '';
    }
    else if( (isset($values['report_depend']) && $values['report_depend'] == 'order' ) || empty($values['type']) )
    {
      $select->from($orderTableName, array("SUM($orderTableName.item_count) as quantity", "COUNT($orderTableName.order_id) as order_count", "DATE_FORMAT($orderTableName.creation_date, '$day %M %Y') as creation_date", "SUM($orderTableName.store_tax) as store_tax", "SUM($orderTableName.shipping_price) as shipping_price", "SUM($orderTableName.admin_tax) as admin_tax", "SUM($orderTableName.commission_value) as commission", "SUM($orderTableName.grand_total) as grand_total", "SUM($orderTableName.sub_total) as sub_total", "$orderTableName.store_id" ));
      if ($store == 'specific_store') 
      {
        $store_ids = $values['store_ids'];
        $select->where("$orderTableName.store_id IN($store_ids)");
      }
      else if( !empty($owner_id) )
      {
        $viewer_store_ids = Engine_Api::_()->getDbTable('stores', 'sitestore')->getStoreId($owner_id);
        foreach($viewer_store_ids as $store_id)
        {
          $temp_store_ids[] = $store_id['store_id'];
        }
        $viewer_store_ids = implode(",",$temp_store_ids);
        $select->where("$orderTableName.store_id IN($viewer_store_ids)");
      }
      $product_group_by = '';
      $order_group_by = "$orderTableName.store_id";
    }
    
    if( isset($values['order_status']) && $values['order_status'] != 'all' )
      $select->where("$orderTableName.order_status = ? ", $values['order_status']);

    if (!empty($values['time_summary'])) {
      if ($values['time_summary'] == 'Monthly') 
      {
        $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
        $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
      }
      else 
      {
        if (!empty($values['start_daily_time'])) 
        {
          $start = $values['start_daily_time'];
        }
        if (!empty($values['end_daily_time'])) 
        {
          $end = $values['end_daily_time'];
        }
        $startTime = date('Y-m-d', $start);
        $endTime = date('Y-m-d', $end);
      }

      switch ($values['time_summary']) {

        case 'Monthly':
          $select
                  ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m') >= ?", $startTime)
                  ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m') <= ?", $endTime)
                  ->group("$product_group_by$order_group_by")->group("YEAR($orderTableName.creation_date)")->group("MONTH   ($orderTableName.creation_date)");
          break;

        case 'Daily': 
          $select
                  ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                  ->where("DATE_FORMAT(" . $orderTableName . " .creation_date, '%Y-%m-%d') <= ?", $endTime)
                  ->group("$product_group_by$order_group_by")->group("YEAR($orderTableName.creation_date)")->group("MONTH($orderTableName.creation_date)")->group("DAY($orderTableName.creation_date)");
          break;
      }
    }
    
    if( isset($values['display']) && $values['display'] == 'date_wise' )
      $select->order("$orderTableName.creation_date");    
    else
      $select->order("$orderTableName.store_id");    
    return $this->fetchAll($select);
  }
  
  /**
   * Return top selling store
   *
   * @param $fetch_column
   * @param array $params
   * @return object
   */
  public function getTopSellingStore($fetch_column, $params = array())
  {
    $pageTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $pageTable->info('name');
    
    //MAKE TIMING STRING
    $interval = $params['interval'];
    $sqlTimeStr = '';
    $current_time = date("Y-m-d H:i:s");
    if ($interval == 'week') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
    } elseif ($interval == 'month') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
    }

    $storeReviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    
    $orderTableName = $this->info('name');
    $select = $pageTable->select()
            ->setIntegrityCheck(false);
    if( empty($storeReviewEnable) )
    {
      $select->from($pageTableName, array('store_url', 'store_id', "photo_id", "title", "category_id", "comment_count", "view_count", "like_count", "follow_count"));
    }
    else
    {
      $select->from($pageTableName, array('store_url', 'store_id', "photo_id", "title", "category_id", "comment_count", "view_count", "like_count", "follow_count", "review_count", "rating"));
    }
    
    //Start Network work
    $select = $pageTable->getNetworkBaseSql($select, array());
    //End Network work
            
    $select->joinInner($orderTableName, "$orderTableName.store_id = $pageTableName.store_id", array("SUM($fetch_column) as $fetch_column"));
    
    if ($interval != 'overall') {
      $select->where($orderTableName . "$sqlTimeStr  or " . $orderTableName . '.creation_date is null');
    }
    
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($pageTableName . '.category_id = ?', $params['category_id']);
    }

    if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
      $select->where($pageTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }
    
    if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
      $select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }
    
     $select->where($pageTableName . '.closed = ?', '0')
            ->where($pageTableName . '.declined = ?', '0')
            ->where($pageTableName . '.approved = ?', '1')
            ->where($pageTableName . '.draft = ?', '1');
    
    $select->order("$fetch_column DESC")
           ->group("store_id");

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }

    return $pageTable->fetchAll($select);
  }
  
  /**
   * Return top buyers
   *
   * @param array $params
   * @return object
   */
  public function getTopBuyers($params)
  {
    //MAKE TIMING STRING
    $sqlTimeStr = '';
    $current_time = date("Y-m-d H:i:s");
    if ($params['interval'] == 'week') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
    } elseif ($params['interval'] == 'month') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
    }
    
    if( $params['listing_based_on'] == 'price' )
    {
      $fetchColumn = "SUM(grand_total) as grand_total";
      $orderBy = "SUM(grand_total) DESC";
    }
    else if( $params['listing_based_on'] == 'item' )
    {
      $fetchColumn = "SUM(item_count) as item_count";
      $orderBy = "SUM(item_count) DESC";
    }
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
    
    $orderTableName = $this->info('name');
    
    $select = $userTable->select()
            ->setIntegrityCheck(false)
            ->from($userTableName, array('user_id', 'displayname', 'username', 'photo_id'))
            ->joinInner($orderTableName, "$orderTableName.buyer_id = $userTableName.user_id", array($fetchColumn))
            ->where('buyer_id != 0');
    
    if ($params['interval'] != 'overall') {
        $select->where($orderTableName . "$sqlTimeStr  or " . $orderTableName . '.creation_date is null');
    }
    
    $select->order($orderBy)
            ->order("$orderTableName.order_id DESC")
            ->group('buyer_id')
            ->limit($params['limit']);

    return $userTable->fetchAll($select);
  }
  
  /**
   * Return store overview : Selling of the store
   *
   * @param array $params
   * @return object
   */
  public function getStoreOverview($params)
  {
    $select = $this->select()
            ->from($this->info('name'), array("SUM(sub_total) as sub_total", "COUNT(order_id) as order_count", "SUM(commission_value) as commission", "SUM(store_tax) as store_tax", "SUM(item_count) as item_count", "SUM(admin_tax) as admin_tax"))
            ->where('store_id =?', $params['store_id'])
//            ->where("payment_status LIKE 'active'")
//            ->where("order_status <> 6");
            ->where("order_status = 5");

    return $select->query()->fetch();
  }
  
  /**
   * Return order according to status value
   *
   * @param array $params
   * @return object
   */
  public function getStatusOrders($params)
  {
    $select = $this->select()
            ->from($this->info('name'), array("COUNT(order_id)"));
    
    if( isset($params['store_id']) && !empty($params['store_id']) )
    {
      $select->where("store_id =?", $params['store_id']);
    }

    if( isset($params['order_status']) )
    {
      $select->where("order_status =?", $params['order_status']);
    }

    return $select->query()->fetchColumn();
  }

  /**
   * Return total sale for a year of a store
   *
   * @param array $params
   * @return float
   */
  public function getTotalSaleThisYear($params)
  {
    $select = $this->select()
            ->from($this->info('name'), array("SUM(grand_total) as grand_total"))
            ->where('store_id =?', $params['store_id'])
            ->where("YEAR(creation_date) = ?", date('Y'))
            ->where("payment_status = 'active'")
            ->where("order_status <> 6");
            
    return $select->query()->fetchColumn();
  }
  
  /**
   * Return order detail for which buyer is going to make payment
   *
   * @param $parent_id
   * @return object
   */
  public function getMakePaymentOrderDetail($parent_id)
  {
    $orderTableName = $this->info('name');
    
    $pageTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $pageTable->info('name');
    
      $select = $pageTable->select()
              ->setIntegrityCheck(false)
              ->from($pageTableName, array("store_url", "title", "store_id"))
              ->join($orderTableName, "$orderTableName.store_id = $pageTableName.store_id", array("$orderTableName.order_id", "$orderTableName.grand_total", "$orderTableName.store_id"))
              ->where("$orderTableName.parent_id =?", $parent_id)
              ->order("$orderTableName.order_id");

    return $pageTable->fetchAll($select);
  }
  
  /**
   * Return admin amount detail for order statistics
   *
   * @return object
   */
  public function getAdminAmountDetails()
  {
    $select = $this->select()
            ->from($this->info('name'), array("SUM(commission_value) as commission", "SUM(admin_tax) as admin_tax", "COUNT(order_id) as order_count"))
            ->where("payment_status = 'active'")
            ->where("order_status <> 6");
    
    return $this->fetchAll($select);
  }
  
  /**
   * Return viewer has purchased any downloadable product or not
   *
   * @param $viewer_id
   * @return int
   */
  public function isAnyDownloadableProduct($viewer_id)
  {
    $orderTableName = $this->info('name');
    $orderDownloadTableName = Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct')->info('name');
    
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'), array("$orderTableName.order_id"))
            ->join($orderDownloadTableName, "$orderTableName.order_id = $orderDownloadTableName.order_id", array(""))
            ->where("$orderTableName.buyer_id =? ", $viewer_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
    
    return $select;
  }
  
  /**
   * Return bill amount for a store
   *
   * @param $store_id
   * @return object
   */
  public function getStoreBillAmount($store_id) {
    $select = $this->select()
            ->from($this->info('name'), array('SUM(commission_value) as commission'))
            ->where("store_id =? AND storebill_id = 0 AND direct_payment = 1 AND non_payment_admin_reason != 1 AND order_status != 8 AND payment_status != 'not_paid'", $store_id);
    
    if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
        $select->where('payment_split = ?', 0);
    }        
    
    return $select->query()->fetchColumn();
  }
  
  public function getStoreBillPaginator($params = array()) {
      
    $paginator = Zend_Paginator::factory($this->getStoreBillSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getStoreBillSelect($params)
  {
    $orderTableName = $this->info('name');
    
    $select = $this->select()
            ->from($orderTableName, array("sum(grand_total) as grand_total", "sum(commission_value) as commission", "count(order_id) as order_count", "MONTHNAME(creation_date) as month", "MONTH(creation_date) as month_no", "YEAR(creation_date) as year"))
            ->where('store_id =?', $params['store_id'])
            ->where('direct_payment = 1')
            ->where('non_payment_admin_reason != 1')
            ->where('order_status != 8');
    
    $select->group("YEAR($orderTableName.creation_date)");
    $select->group("MONTH($orderTableName.creation_date)");
    return $select;
  }
  
  public function notPaidBillAmount($store_id)
  {
    $select = $this->select()
            ->from($this->info('name'), array("sum(commission_value) as commission"))
            ->where('store_id =?', $store_id)
            ->where('direct_payment = 1')
            ->where('non_payment_admin_reason = 1')
            ->where("payment_status != 'not_paid'")
            ->where('order_status = 8');

    return $select->query()->fetchColumn();
  }

    public function getStoreMonthlyBillPaginator($params = array()) {
      
    $paginator = Zend_Paginator::factory($this->getStoreMonthlyBillSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getStoreMonthlyBillSelect($params)
  {
    $orderTableName = $this->info('name');
    
    $select = $this->select()
            ->from($orderTableName, array("order_id", "item_count", "commission_value", "grand_total", "creation_date", "payment_status"))
            ->where('store_id =?', $params['store_id'])
            ->where('direct_payment = 1');
    
    if( isset($params['month']) && !empty($params['month']) ) {
      $select->where('MONTH(creation_date) = ?', $params['month']);
    }
    if( isset($params['year']) && !empty($params['year']) ) {
      $select->where('YEAR(creation_date) = ?', $params['year']);
    }

    $select->order('order_id DESC');
    return $select;
  }
  
  public function getStoreCommissionAmountDetailPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getStoreCommissionAmountDetail($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getStoreCommissionAmountDetail($params = array())
  {
    $orderTableName = $this->info('name');
    $storeTableName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');
    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
    
    $select = $this->select()->setIntegrityCheck(false);
    
    if( isset($params['tab']) && empty($params['tab']) ) {
      $select->from($orderTableName, array("SUM(grand_total) as order_total", "SUM(commission_value) as commission", "COUNT(order_id) as order_count"));
    } else if( isset($params['tab']) && !empty($params['tab']) ) {
      $select->from($orderTableName, array("order_id", "store_id", "commission_value", "grand_total", "non_payment_seller_reason", "non_payment_admin_reason", "non_payment_seller_message", "non_payment_admin_message"));
    }
    
    $select->join("$storeTableName", ("$storeTableName.store_id = $orderTableName.store_id"), array('store_id', 'title'))
    ->joinLeft($userTableName, "$storeTableName.owner_id = $userTableName.user_id", array("$userTableName.username"))
    ->where("$orderTableName.direct_payment = 1")
    ->where("$orderTableName.order_status != 8")
    ->where("$orderTableName.non_payment_admin_reason != 1");      
    
    if( isset($params['tab']) && empty($params['tab']) ) {
      $select->group("$storeTableName.store_id");
    }
    
    if( isset($params['tab']) && !empty($params['tab']) ) {
      $select->where("$orderTableName.non_payment_seller_reason != 0");
    }
    
    if( isset($params['order_id']) && !empty($params['order_id']) ) {
      $select->where("$storeTableName.order_id = ". trim($params['order_id']));
    }
    
    if( isset($params['username']) && !empty($params['username']) ) {
      $select->where($userTableName . '.username  LIKE ?', '%' . trim($params['username']) . '%');
    }
    
    if( isset($params['title']) && !empty($params['title']) ) {
      $select->where($storeTableName . '.title LIKE ?', '%' . trim($params['title']) . '%');
    }
    
    if( isset($params['from']) && !empty($params['from']) ) {
      $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($params['from']));
    }
    
    if( isset($params['to']) && !empty($params['to']) ) {
      $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($params['to']));
    }
    
     $api = Engine_Api::_()->sitestoreproduct();
    if( isset($params['commission_min_amount']) && !empty($params['commission_min_amount']) ) {
      $select->having("commission >= ". $api->getPriceWithCurrency(trim($params['commission_min_amount']), 1, 1)); 
    } 
    if( isset($params['commission_max_amount']) && !empty($params['commission_max_amount']) ) {
      $select->having("commission <= ". $api->getPriceWithCurrency(trim($params['commission_max_amount']), 1, 1));
    }
    
    if( isset($params['order_min_amount']) && !empty($params['order_min_amount']) ) {
      $select->having("order_total >= ". $api->getPriceWithCurrency(trim($params['order_min_amount']), 1, 1)); 
    }
    
    if( isset($params['order_max_amount']) && !empty($params['order_max_amount']) ) {
      $select->having("order_total <= ". $api->getPriceWithCurrency(trim($params['order_max_amount']), 1, 1)); 
    }


    if( isset($params['tab']) && empty($params['tab']) && isset($params['order']) ) {
      $select->order((!empty($params['order']) ? $params['order'] : 'store_id' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
    }
    
    if( isset($params['tab']) && !empty($params['tab']) ) {
      if( $params['order'] == 'store_id' ) {
        $select->order("$orderTableName.order_id DESC");
      }
    }
    
    return $select;
  }
}