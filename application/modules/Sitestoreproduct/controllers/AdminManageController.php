<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE PRODUCTS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_productmanage');
    
    //MAKE FORM
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Manage_Filter();
    
    $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
    $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

    //GET PAGE NUMBER
    $page = $this->_getParam('page', 1);
    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    //GET CATEGORY TABLE
    $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');
    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    //GET PRODUCT TABLE
    $tableProduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productTableName = $tableProduct->info('name');
    
    $otherinfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
    $otherinfoTableName = $otherinfoTable->info('name');
    
    $getStoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $getStoreTable->info('name');
    

    //MAKE QUERY
    $select = $tableProduct->select()
            ->setIntegrityCheck(false)
            ->from($productTableName)
            ->joinLeft($tableUserName, "$productTableName.owner_id = $tableUserName.user_id", 'username')
            
            ->group("$productTableName.product_id");
    $select->joinLeft($pageTableName, "$productTableName.store_id = $pageTableName.store_id", array("title as store"));
    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    // searching
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->store = '';
    $this->view->price_min = '';
    $this->view->price_max = '';
    $this->view->in_stock_min = '';
    $this->view->in_stock_max = '';
    $this->view->sponsored = '';
    $this->view->newlabel = '';
    $this->view->approved = '';
    $this->view->downpayment = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->productbrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
    $this->view->product_type = '';
    $this->view->product_code = '';

    if (isset($_POST['search'])) {

      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUserName . '.username  LIKE ?', '%' . trim($_POST['owner']) . '%');
      }

      if (!empty($_POST['review_status'])) {
        $this->view->review_status = $review_status = $_POST['review_status'];
        $_POST['review_status']--;

        if ($review_status == 'rating_editor') {
          $select->where($productTableName . '.rating_editor > ? ', 0);
        } elseif ($review_status == 'rating_users') {
          $select->where($productTableName . '.rating_users > ? ', 0);
        } elseif ($review_status == 'rating_avg') {
          $select->where($productTableName . '.rating_avg > ? ', 0);
        } elseif ($review_status == 'both') {
          $select->where($productTableName . '.rating_editor > ? ', 0);
          $select->where($productTableName . '.rating_users > ? ', 0);
        }
      }
      
      if (!empty($_POST['product_type'])) {
        $this->view->product_type = $product_type = $_POST['product_type'];
        $select->where($productTableName . '.product_type LIKE ? ', '%' . $product_type .'%');
      }
      
      if (!empty($_POST['product_code'])) {
        $this->view->product_code = $product_code = $_POST['product_code'];
        $select->where($productTableName . '.product_code LIKE ? ', '%' . $product_code .'%');
      }

      if (!empty($_POST['title'])) {
        $this->view->title = $_POST['title'];
        $select->where($productTableName . '.title LIKE ?', '%' . trim($_POST['title']) . '%');
      }
      
      if (!empty($_POST['store'])) {
        $this->view->store = $_POST['store'];
        $select->where($pageTableName . '.title LIKE ?', '%' . trim($_POST['store']) . '%');
      }
      
      if ($_POST['price_min'] != '') {
        $this->view->price_min = $_POST['price_min'];
        $select->where($productTableName . '.price  >=?', trim($_POST['price_min']));
      }
      
      if ($_POST['price_max'] != '') {
        $this->view->price_max = $_POST['price_max'];
        $select->where($productTableName . '.price  <=?', trim($_POST['price_max']));
      }
      
      if ($_POST['in_stock_min'] != '') {
        $this->view->in_stock_min = $_POST['in_stock_min'];
        $select->where($productTableName . '.in_stock >=?', trim($_POST['in_stock_min']));
      }
      
      if ($_POST['in_stock_max'] != '') {
        $this->view->in_stock_max = $_POST['in_stock_max'];
        $select->where($productTableName . '.in_stock <=?', trim($_POST['in_stock_max']));
      }

      if (!empty($_POST['sponsored'])) {
        $this->view->sponsored = $_POST['sponsored'];
        $_POST['sponsored']--;

        $select->where($productTableName . '.sponsored = ? ', $_POST['sponsored']);
      }

      if (!empty($_POST['approved'])) {
        $this->view->approved = $_POST['approved'];
        $_POST['approved']--;
        $select->where($productTableName . '.approved = ? ', $_POST['approved']);
      }
      
      if (!empty($_POST['downpayment'])) {
        $this->view->downpayment = $_POST['downpayment'];
        $tempDownpayment = --$_POST['downpayment'];
        $select->join($otherinfoTableName, "$productTableName.product_id = $otherinfoTableName.product_id", 'downpayment_value');
        if( empty($tempDownpayment) ) {
          $select->where($otherinfoTableName . '.downpayment_value = ? ', 0);
        } else{
          $select->where($otherinfoTableName . '.downpayment_value != ? ', 0);
        }
      }

      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($productTableName . '.featured = ? ', $_POST['featured']);
      }

      if (!empty($_POST['newlabel'])) {
        $this->view->newlabel = $_POST['newlabel'];
        $_POST['newlabel']--;
        $select->where($productTableName . '.newlabel = ? ', $_POST['newlabel']);
      }

      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;
        $select->where($productTableName . '.closed = ? ', $_POST['status']);
      }

      if (!empty($_POST['productbrowse'])) {
        $this->view->productbrowse = $_POST['productbrowse'];
        $_POST['productbrowse']--;
        if ($_POST['productbrowse'] == 0) {
          $select->order($productTableName . '.view_count DESC');
        } else {
          $select->order($productTableName . '.product_id DESC');
        }
      }

      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($productTableName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $this->view->subcategory_id = $_POST['subcategory_id'];
        $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;

        $select->where($productTableName . '.category_id = ? ', $_POST['category_id'])
                ->where($productTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $this->view->subcategory_id = $_POST['subcategory_id'];
        $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
        $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
        $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;

        $select->where($productTableName . '.category_id = ? ', $_POST['category_id'])
                ->where($productTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($productTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
      }
    }

    $values = array_merge(array(
        'order' => 'product_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'product_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
  }

  //ACTION FOR VIEWING SITESTOREPRODUCT DETAILS
  public function detailAction() {

    //GET THE SITESTOREPRODUCT ITEM
    $this->view->sitestoreproductDetail = Engine_Api::_()->getItem('sitestoreproduct_product', (int) $this->_getParam('id'));
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
  }

  //ACTION FOR MULTI-DELETE PRODUCTS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('sitestoreproduct_product', (int) $value)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function manageOrdersAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_manage_manage');
    
    $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
    $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
    
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();

    $page = $this->_getParam('page', 1);

    $getStoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $pageTableName = $getStoreTable->info('name');
   
    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');

    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $orderTableName = $orderTable->info('name');


    $select = $orderTable->select()
            ->setIntegrityCheck(false)
            ->from($orderTableName)
            ->joinLeft($userTableName, "$orderTableName.buyer_id = $userTableName.user_id", array("$userTableName.username", "$userTableName.user_id"))
            ->joinLeft($pageTableName, "$orderTableName.store_id = $pageTableName.store_id", array("title"))
            ->group($orderTableName . '.order_id');

    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    
    $values = array_merge(array('order' => 'order_id','order_direction' => 'DESC'), $values);
    
    if (!empty($_POST['username'])) {
      $username = $_POST['username'];
    } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
      $username = $_GET['username'];
    } else {
      $username = '';
    }

    if (!empty($_POST['title'])) {
      $title = $_POST['title'];
    } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
      $title = $_GET['title'];
    } else {
      $title = '';
    }
    
    if (!empty($_POST['creation_date_start'])) {
      $creation_date_start = $_POST['creation_date_start'];
    } elseif (!empty($_GET['creation_date_start']) && !isset($_POST['post_search'])) {
      $creation_date_start = $_GET['creation_date_start'];
    } else {
      $creation_date_start = '';
    }
    
    if (!empty($_POST['creation_date_end'])) {
      $creation_date_end = $_POST['creation_date_end'];
    } elseif (!empty($_GET['creation_date_end']) && !isset($_POST['post_search'])) {
      $creation_date_end = $_GET['creation_date_end'];
    } else {
      $creation_date_end = '';
    }
    
    if (!empty($_POST['billing_name'])) {
      $billing_name = $_POST['billing_name'];
    } elseif (!empty($_GET['billing_name']) && !isset($_POST['post_search'])) {
      $billing_name = $_GET['billing_name'];
    } else {
      $billing_name = '';
    }
    
    if (!empty($_POST['shiping_name'])) {
      $shiping_name = $_POST['shiping_name'];
    } elseif (!empty($_GET['shiping_name']) && !isset($_POST['post_search'])) {
      $shiping_name = $_GET['shiping_name'];
    } else {
      $shiping_name = '';
    }
    
    if (isset($_POST['order_min_amount']) && $_POST['order_min_amount'] != '') {
      $order_min_amount = $_POST['order_min_amount'];
    } elseif (!empty($_GET['order_min_amount']) && !isset($_POST['post_search'])) {
      $order_min_amount = $_GET['order_min_amount'];
    } else {
      $order_min_amount = '';
    }
    
    if (isset($_POST['order_max_amount']) && $_POST['order_max_amount'] != '') {
      $order_max_amount = $_POST['order_max_amount'];
    } elseif (!empty($_GET['order_max_amount']) && !isset($_POST['post_search'])) {
      $order_max_amount = $_GET['order_max_amount'];
    } else {
      $order_max_amount = '';
    }
    
    if (isset($_POST['commission_min_amount']) && $_POST['commission_min_amount'] != '') {
      $commission_min_amount = $_POST['commission_min_amount'];
    } elseif (!empty($_GET['commission_min_amount']) && !isset($_POST['post_search'])) {
      $commission_min_amount = $_GET['commission_min_amount'];
    } else {
      $commission_min_amount = '';
    }
    
    if (isset($_POST['commission_max_amount']) && $_POST['commission_max_amount'] != '') {
      $commission_max_amount = $_POST['commission_max_amount'];
    } elseif (!empty($_GET['commission_max_amount']) && !isset($_POST['post_search'])) {
      $commission_max_amount = $_GET['commission_max_amount'];
    } else {
      $commission_max_amount = '';
    }
    
    if (isset($_POST['delivery_time']) && $_POST['delivery_time'] != '') {
      $delivery_time = $_POST['delivery_time'];
    } elseif (!empty($_GET['delivery_time']) && !isset($_POST['post_search'])) {
      $delivery_time = $_GET['delivery_time'];
    } else {
      $delivery_time = '';
    }
    
    if (!empty($_POST['order_status'])) {
      $order_status = $_POST['order_status'];
    } elseif (!empty($_GET['order_status']) && !isset($_POST['post_search'])) {
      $order_status = $_GET['order_status'];
    } else {
      $order_status = '';
    }
    
    if (!empty($_POST['display_only_site_payment_orders'])) {
      $display_only_site_payment_orders = $_POST['display_only_site_payment_orders'];
    } elseif (!empty($_GET['display_only_site_payment_orders']) && !isset($_POST['post_search'])) {
      $display_only_site_payment_orders = $_GET['display_only_site_payment_orders'];
    } else {
      $display_only_site_payment_orders = '';
    }
    
    if (!empty($_POST['payment_gateway'])) {
      $payment_gateway = $_POST['payment_gateway'];
    } elseif (!empty($_GET['payment_gateway']) && !isset($_POST['post_search'])) {
      $payment_gateway = $_GET['payment_gateway'];
    } else {
      $payment_gateway = '';
    }
    
    if (!empty($_POST['downpayment'])) {
      $downpayment = $_POST['downpayment'];
    } elseif (!empty($_GET['downpayment']) && !isset($_POST['post_search'])) {
      $downpayment = $_GET['downpayment'];
    } else {
      $downpayment = '';
    }
    
    if (!empty($_POST['cheque_no'])) {
      $cheque_no = $_POST['cheque_no'];
    } elseif (!empty($_GET['cheque_no']) && !isset($_POST['cheque_no'])) {
      $cheque_no = $_GET['cheque_no'];
    } else {
      $cheque_no = '';
    }
    
    // searching
    $this->view->username = $values['username'] = $username;
    $this->view->title = $values['title'] = $title;
    $this->view->creation_date_start = $values['creation_date_start'] = $creation_date_start;
    $this->view->creation_date_end = $values['creation_date_end'] = $creation_date_end;
    $this->view->billing_name = $values['billing_name'] = $billing_name;
    $this->view->shiping_name = $values['shiping_name'] = $shiping_name;
    $this->view->order_min_amount = $values['order_min_amount'] = $order_min_amount;
    $this->view->order_max_amount = $values['order_max_amount'] = $order_max_amount;
    $this->view->commission_min_amount = $values['commission_min_amount'] = $commission_min_amount;
    $this->view->commission_max_amount = $values['commission_max_amount'] = $commission_max_amount;
    $this->view->delivery_time = $values['delivery_time'] = $delivery_time;
    $this->view->order_status = $values['order_status'] = $order_status;
    $this->view->payment_gateway = $values['payment_gateway'] = $payment_gateway;
    $this->view->downpayment = $values['downpayment'] = $downpayment;
    $this->view->cheque_no = $values['cheque_no'] = $cheque_no;


      if (!empty($username)) {
        $select->where($userTableName . '.username  LIKE ?', '%' . trim($username) . '%');
      }

      if (!empty($title)) {
        $select->where($pageTableName . '.title  LIKE ?', '%' . trim($title) . '%');
      }

      if ((!empty($billing_name) || !empty($shipping_name))) {
        $orderAddressTable = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
        $orderAddressTableName = $orderAddressTable->info('name');
      }

      if (!empty($billing_name)) {
        $selectOrderAddress = $orderAddressTable->select()
                ->from($orderAddressTableName, array("order_id", "CONCAT(f_name,' ',l_name) as name"))
                ->where('type = 0')
                ->where("CONCAT(f_name,' ',l_name) LIKE ?", '%' . trim($billing_name) . '%')
                ->query()
                ->fetchAll();

        $orderIdsString = 0;
        foreach ($selectOrderAddress as $key => $values) {
          $orderIdsString .= "," . $values['order_id'];
        }

        $select->where("$orderTableName.order_id IN ($orderIdsString)");
      }

      if (!empty($shipping_name)) {
        $selectOrderAddress = $orderAddressTable->select()
                ->from($orderAddressTableName, array("order_id", "CONCAT(f_name,' ',l_name) as name"))
                ->where('type = 1')
                ->where("CONCAT(f_name,' ',l_name) LIKE ?", '%' . trim($shipping_name) . '%')
                ->query()
                ->fetchAll();

        $orderIdsString = 0;
        foreach ($selectOrderAddress as $key => $values) {
          $orderIdsString .= "," . $values['order_id'];
        }

        $select->where("$orderTableName.order_id IN ($orderIdsString)");
      }

      if (!empty($creation_date_start)) {
        $select->where("CAST($orderTableName.creation_date AS DATE) >=?", trim($creation_date_start));
      }
      
       if (!empty($creation_date_end)) {
        $select->where("CAST($orderTableName.creation_date AS DATE) <=?", trim($creation_date_end));
      }

      if ($order_min_amount != '') {
        $select->where("$orderTableName.grand_total >=?", trim($order_min_amount));
      }

      if ($order_max_amount != '') {
        $select->where("$orderTableName.grand_total <=?", trim($order_max_amount));
      }
      
      if ($commission_min_amount != '') {
        $select->where("$orderTableName.commission_value >=?", trim($commission_min_amount));
      }

      if ($commission_max_amount != '') {
        $select->where("$orderTableName.commission_value <=?", trim($commission_max_amount));
      }
      
      if ($delivery_time != '') {
        $select->where($orderTableName . '.delivery_time  = ?', trim($delivery_time));
      }

      if (!empty($order_status)) {
        $order_status--;

        $select->where($orderTableName . '.order_status = ? ', $order_status);
      }
      
      if( !empty($display_only_site_payment_orders) ) {
        $select->where($orderTableName . '.direct_payment = ? ', 0);
      }
      
      if (!empty($payment_gateway)) {
        $select->where($orderTableName . '.gateway_id = ? ', $payment_gateway);
      }
      
      if (!empty($downpayment)) {
        if( $downpayment == 1 )
          $select->where("$orderTableName.is_downpayment = 1 OR $orderTableName.is_downpayment = 2");
        else if( $downpayment == 2 )
          $select->where($orderTableName . '.is_downpayment = ? ', 2);
        else if( $downpayment == 3 )
          $select->where($orderTableName . '.is_downpayment = ? ', 1);
        else if( $downpayment == 4 )
          $select->where($orderTableName . '.is_downpayment = ? ', 0);
      }
      
      if (!empty($cheque_no)) {
        $chequeTableName = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->info('name');
        $select->joinLeft($chequeTableName, "$orderTableName.cheque_id = $chequeTableName.ordercheque_id", array(""));
        $select->where($chequeTableName . '.cheque_no LIKE ? ', '%'.$cheque_no.'%');
      }

    $this->view->order_approve_count = $orderTable->select()->from($orderTableName, array("COUNT(order_id) as order_id"))->where("gateway_id = 3 AND order_status = 0 AND direct_payment = 0")->query()->fetchColumn();

       //ASSIGN VALUES TO THE TPL
    $this->view->formValues = array_filter($values); 
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'order_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
  }

  public function viewStoreDetailsAction() {

    $store_id = $this->_getParam('store_id', null);
    
    if( empty($store_id) )
      return $this->_forward('notfound', 'error', 'core');
    
    $tablePage = Engine_Api::_()->getDbtable('stores', 'sitestore');    
    $tablePageName = $tablePage->info('name');

    $select = $tablePage->select()
            ->from($tablePageName)
            ->where($tablePageName . '.store_id = ?', $store_id);

    $this->view->sitestoreDetail = $detail = $tablePage->fetchRow($select);
    
    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');

    $this->view->total_sale_this_year = $orderTable->getTotalSaleThisYear(array('store_id' => $store_id));
    $this->view->store_overview = $orderTable->getStoreOverview(array('store_id' => $store_id));
    
    $this->view->approval_pending_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 0));
    $this->view->payment_pending_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 1));
    $this->view->processing_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 2));
    $this->view->on_hold_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 3));
    $this->view->fraud_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 4));
    $this->view->complete_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 5));
    $this->view->cancel_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 6));
  }
  
  public function paymentApproveAction() {

    $viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
    $order_id = $this->_getParam('order_id', null);
    $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
    $this->view->paymentPending = $paymentPending = $this->_getParam("payment_pending", null);
    
    if( empty($order_id) || empty($order_obj) || (empty($order_obj->cheque_id) && empty($paymentPending)) )
    {
      return $this->_forward('notfound', 'error', 'core');
    }
    
    if( empty($paymentPending) )
    {
      $cheque_detail = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($order_obj->cheque_id);
      $this->view->form = $form = new Sitestoreproduct_Form_Admin_Payment_PaymentApprove($cheque_detail);
    }
    
    $order_ids = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getMakePaymentOrderDetail($order_obj->parent_id);

    $index = 1;
    $payment_approve_message = '';
    $tempCount = @COUNT($order_ids);
    foreach ($order_ids as $order_id) {
      if ($index != 1) {
        if ($tempCount == $index) {
          $payment_approve_message .= $this->view->translate(" SITESTOREPRODUCT_CHECKOUT_AND ");
        } else {
          $payment_approve_message .= ', ';
        }
      }
      $tempViewUrl = $this->view->url(array('action' => 'store', 'store_id' => $order_id['store_id'], 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id['order_id'] ), 'sitestore_store_dashboard', false);

      $payment_approve_message .= '<a href="'.$tempViewUrl.'" target="_blank">#' . $order_id['order_id'] . '</a>';
      $index++;
    }

    $this->view->payment_approve_message = $payment_approve_message;
    
    // CHECK POST
//    if (!$this->getRequest()->isPost()) {
//      if( empty($paymentPending) )
//      {
//        $form->populate($cheque_detail);
//        return;
//      }
//    }

if ($this->getRequest()->isPost()) {
    if( empty($paymentPending) )
    {
      $form->populate($cheque_detail);
      $grand_total = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getGrandTotal($order_obj->parent_id);
    }
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try 
    {
      if( empty($paymentPending) )
      {
        $gateway_transaction_id = $_POST['transaction_no'];
        $type = 'cheque';
      }
      else
      {
        $gateway_transaction_id = '';
        $type = 'payment';
      }
      $gateway_transaction_id = empty($paymentPending) ? $_POST['transaction_no'] : 0;
      
      $transactionData = array(
                  'user_id' => $order_obj->buyer_id,
                  'gateway_id' => $order_obj->gateway_id,
                  'date' => new Zend_Db_Expr('NOW()'),
                  'payment_order_id' => 0,
                  'parent_order_id' => $order_obj->parent_id,
                  'gateway_transaction_id' => $gateway_transaction_id,
                  'type' => $type,
                  'state' => 'okay',
                  'amount' => @round($grand_total, 2),
                  'currency' => $currencyCode,
                  'cheque_id' => $order_obj->cheque_id
              );
      
      Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->insert($transactionData);
      
        if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $transactionParams = array_merge($transactionData, array('resource_type' => 'sitestoreproduct_order'));
            Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
        }          
      
      
      
      // UPDATE PAYMENT STATUS
      Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array("payment_status" => "active"),array("parent_id =?" => $order_obj->parent_id));

      $order_ids = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getOrderIds($order_obj->parent_id);
      
      // UPDATE ORDER STATUS
      $orderProductTable = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct');
      foreach($order_ids as $order_id)
      {
        $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $order_id['order_id'], 'all_downloadable_products' => true));
        $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $order_id['order_id']));
        if( empty($anyOtherProducts) || !empty($bundleProductShipping) )
          Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 5), array('order_id = ?' => $order_id['order_id']));
        else
          Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 2), array('order_id = ?' => $order_id['order_id']));
      }

      Engine_Api::_()->sitestoreproduct()->orderPlaceMailAndNotification($order_ids, true);

      $db->commit();
      
      /*//eb cb calculation
      $viewer_id = $order_obj->buyer_id;
      $divided_amount = 1000;
      $sub_total = $order_obj->sub_total;
      $total_point_basedontotalamout_paid = $sub_total / $divided_amount;
      $total_BB = floor($total_point_basedontotalamout_paid);
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
      //START BUSINESS BRIDGES WORK
      if($total_BB) {
          $bridgesTable->insert(array(
            'buyer_user_id' => $viewer_id,
            'buyer_bb' => $total_BB,
            'order_id' =>$order_obj->order_id,
            'type' => 'business',
            'creation_date' => new Zend_Db_Expr('NOW()'),
          ));
          $parentId = $db->lastInsertId();
      }
      
      //END BUSINESS BRIDGES WORK
      
      //CB Point work
      $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
      $firstParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $viewer_id)->query()->fetchColumn();
        if($firstParentId) {
            $total_CB = ($total_BB / 100) * 50; // 50% of BB Point is equal to CB
            $bridgesTable->insert(array(
            'buyer_user_id' => $firstParentId,
            'user_id' => $viewer_id,
            'buyer_cb' => $total_CB,
            'order_id' =>$order_obj->order_id,
            'parent_id' => $parentId,
            'type' => 'collection',
            'creation_date' => new Zend_Db_Expr('NOW()'),
          ));
          $parentId = $db->lastInsertId();
          $secondParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $firstParentId)->query()->fetchColumn();
          if($secondParentId && $total_CB) {
              $total_DB = ($total_CB / 100) * 10; // 10% of CB Point is equal to DB
              $bridgesTable->insert(array(
                'buyer_user_id' => $secondParentId,
                'user_id' => $firstParentId,
                'order_id' =>$order_obj->order_id,
                'buyer_db' => $total_DB,
                'parent_id' => $parentId,
                'type' => 'direct',
                'creation_date' => new Zend_Db_Expr('NOW()'),
              ));
          }
        }  */
    
      
    }
    catch (Exception $e) 
    {
      $db->rollBack();
      throw $e;
    }
    
     $this->_forward('success', 'utility', 'core', array(
         'smoothboxClose' => true,
         'parentRefreshTime' => '100',
         'parentRedirect' => $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'manage', 'action' => 'manage-orders'), "admin_default", true),
         'format' => 'smoothbox',
         'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment approved successfully.'))
    ));
}
  }
  
//  public function orderCancelAction()
//  {
//    if( $this->getRequest()->isPost())
//    {
//      $order_id = $this->_getParam('order_id');
//      $page_id = $this->_getParam('page_id', $this->_getParam('business_id', null));
//      Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 6), array('order_id =?' => $order_id));
//      
//      $this->_forward('success', 'utility', 'core', array(
//        'smoothboxClose' => true,
//        'parentRefresh' => true,
//        'parentRedirect' => $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'manage', 'action' => 'manage-orders'), "admin_default", true),
//        'parentRedirectTime' => 10,
//        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Order canceled successfully.'))
//    ));
//    }
//  }
  
  public function sendInvoiceAction() {

    // GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    $order_id = $this->_getParam('order_id', null);
    $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);

    if (empty($order_id) || empty($order) || $viewer->level_id != 1) {
      return $this->_forward('notfound', 'error', 'core');
    }

    // FORM GENERATION
    $this->view->form = $form = new Sitestoreproduct_Form_SendInvoice();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // GET FORM VALUES
    $values = $form->getValues();
    
   //CHECK VALID EMAIL ID FORMAT
    $validator = new Zend_Validate_EmailAddress();
    $validator->getHostnameValidator()->setValidateTld(false);

    if (!$validator->isValid($values['email_id'])) {
      $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid email address.'));
      return;
    }

    Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['email_id'], 'sitestoreproduct_order_invoice', array(
        'message' => '<div>"' . $values['message'] . '"</div>',
        'order_id' => '#' . $order_id,
        'order_invoice' => $this->view->orderInvoice($order),
    ));

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => false,
        'messages' => array('Invoice sent successfully.')
    ));
  }
  
  public function commissionAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_commission');
    
    $this->view->tab = $tab = $this->_getParam('tab', 0);
    
    //FORM GENERATION
//    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Manage_Filter();
    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
 
    $values = array_merge(array('order' => 'store_id','order_direction' => 'DESC'), $values);

    if (!empty($_POST['username'])) {
      $values['username'] = $_POST['username'];
    } elseif (!empty($_GET['username']) && !isset($_POST['post_search'])) {
      $values['username'] = $_GET['username'];
    } else {
      $values['username'] = '';
    }

    if (!empty($_POST['title'])) {
      $values['title'] = $_POST['title'];
    } elseif (!empty($_GET['title']) && !isset($_POST['post_search'])) {
      $values['title'] = $_GET['title'];
    } else {
      $values['title'] = '';
    }
    
    if (!empty($_POST['starttime']) && !empty($_POST['starttime']['date'])) {
      $values['from'] = $_POST['starttime']['date'];
    } elseif (!empty($_GET['starttime']) && !empty($_GET['starttime']['date']) && !isset($_POST['post_search'])) {
      $values['from'] = $_GET['starttime']['date'];
    } else {
      $values['from'] = '';
    }
    
    if (!empty($_POST['endtime']) && !empty($_POST['endtime']['date'])) {
      $values['to'] = $_POST['endtime']['date'];
    } elseif (!empty($_GET['endtime']) && !empty($_GET['endtime']['date']) && !isset($_POST['post_search'])) {
      $values['to'] = $_GET['endtime']['date'];
    } else {
      $values['to'] = '';
    }
    
    if (!empty($_POST['commission_min_amount']) && is_numeric($_POST['commission_min_amount'])) {
      $values['commission_min_amount'] = $_POST['commission_min_amount'];
    } elseif (!empty($_GET['commission_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_min_amount'])) {
      $values['commission_min_amount'] = $_GET['commission_min_amount'];
    } else {
      $values['commission_min_amount'] = '';
    }
    
    if (!empty($_POST['commission_max_amount']) && is_numeric($_POST['commission_max_amount'])) {
      $values['commission_max_amount'] = $_POST['commission_max_amount'];
    } elseif (!empty($_GET['commission_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['commission_max_amount'])) {
      $values['commission_max_amount'] = $_GET['commission_max_amount'];
    } else {
      $values['commission_max_amount'] = '';
    }
    
    if (!empty($_POST['order_min_amount']) && is_numeric($_POST['order_min_amount'])) {
      $values['order_min_amount'] = $_POST['order_min_amount'];
    } elseif (!empty($_GET['order_min_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['order_min_amount'])) {
      $values['order_min_amount'] = $_GET['order_min_amount'];
    } else {
      $values['order_min_amount'] = '';
    }
    
    if (!empty($_POST['order_max_amount']) && is_numeric($_POST['order_max_amount'])) {
      $values['order_max_amount'] = $_POST['order_max_amount'];
    } elseif (!empty($_GET['order_max_amount']) && !isset($_POST['post_search']) && is_numeric($_GET['order_max_amount'])) {
      $values['order_max_amount'] = $_GET['order_max_amount'];
    } else {
      $values['order_max_amount'] = '';
    }
    
    if (!empty($_POST['order_id'])) {
      $values['order_id'] = $_POST['order_id'];
    } elseif (!empty($_GET['order_id']) && !isset($_POST['post_search'])) {
      $values['order_id'] = $_GET['order_id'];
    } else {
      $values['order_id'] = '';
    }
    
    $values['tab'] = $tab;
    $values['page'] = $this->_getParam('page', 1);
    $values['limit'] = 20;

    $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getStoreCommissionAmountDetailPaginator($values);
    if( empty($tab) ) {
      $tempStorePaidCommission = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->getPaidCommissionDetail();
      $storePaidCommission = array();
     
        foreach ($tempStorePaidCommission as $amount) {
          $storePaidCommission[$amount['store_id']]['paid_commission'] = $amount['paid_commission'];
  //        $storePaidCommission[$amount['store_id']]['last_paid_date'] = $amount['last_paid_date'];
          
            if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {  
                $storePaidCommission[$amount['store_id']]['paid_commission'] = $amount['paid_commission'] + Engine_Api::_()->sitegateway()->getSplitNEscrowGatewayCommission(array('resource_type' => 'sitestoreproduct_order', 'resource_id' => $amount['store_id'], 'resource_key' => 'store_id'));
            }          
          
        }
    
      $this->view->storePaidCommission = $storePaidCommission;
    }
    // searching
    $this->view->title = $values['title'];
    $this->view->username = $values['username'];
    $this->view->order_min_amount = $values['order_min_amount'];
    $this->view->order_max_amount = $values['order_max_amount'];
    $this->view->commission_min_amount = $values['commission_min_amount'];
    $this->view->commission_max_amount = $values['commission_max_amount'];
    $this->view->starttime = $values['from'];
    $this->view->endtime = $values['to'];
    $this->view->order_id = $values['order_id'];
    
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
    $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
  }
  
  public function reversalCommissionAction()
  {
    $order_id = $this->_getParam('order_id', null);
    $this->view->order = $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
    $this->view->storeOwner = $storeOwner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
    $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    
    if (!$this->getRequest()->isPost()) {
      return;
    }
    
    $reversal_commission_action = $_POST['reversal_commission_action'];
    if( $reversal_commission_action == 1 ) {
      $order->order_status = 8;
      if( empty($order->storebill_id) ) {
        $order->payment_status = 'not_paid';
      }
      $actionName = 'approved';
    } else if( $reversal_commission_action == 2 ) {
      $actionName = 'declined';
    } else {
      $actionName = 'put on hold';
    }
    
    $order->non_payment_admin_reason = $reversal_commission_action;
    $order->non_payment_admin_message = $_POST['non_payment_admin_message'];
    $order->save();
    
    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
    $orderUrl = $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false);    $order_no = '<a href="' . $orderUrl . '">#' . $order->order_id . '</a>';
    $store_name = '<a href="'.$newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref().'">'. $sitestore->getTitle().'</a>';
    
    //SEND EMAIL
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($storeOwner, 'sitestoreproduct_store_commission_reversal_action', array(
                'order_id' => '#' . $order->order_id,
                'order_no' => $order_no,
                'object_title' => $sitestore->getTitle(),
                'object_name' => $store_name,
                'action' => $actionName,
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false),
            ));

    $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your action has been submitted and email successfully sent to the store owner.'))
      ));
  }
 
}