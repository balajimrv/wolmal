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
class Sitestoreproduct_Widget_FullWidthListInformationProfileController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->storeInfo = $storeInfo = $this->_getParam('storeInfo', 1);
    $this->view->showDescription = $this->_getParam('showDescription', 1);
    $this->view->showProductRating = $this->_getParam('showProductRating', 1);    
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }
    
    $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
    $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
    
    $this->view->currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($this->view->currencySymbol))
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    //DONT RENDER IF NOT AUTHORIZED
    $this->view->sitestoreproduct_like = true;
    $this->view->isQuickView = $this->_getParam('is_quick_view', null);

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');    
    $this->view->storeObj = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
    
    $this->view->actionLinks = $this->_getParam('actionLinks', 1);

    //IF FACEBOOK PLUGIN IS THERE THEN WE WILL SHOW DEFAULT FACEBOOK LIKE BUTTON.
    $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
    $default_like = 1;
    $this->view->success_showFBLikeButton = 0;
    if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {
      $this->view->success_showFBLikeButton = Engine_Api::_()->facebookse()->showFBLikeButton('sitestoreproduct');
      $default_like = 2;
    }
    $this->view->like_button = $this->_getParam('like_button', $default_like);

    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    if (!empty($sitestoreproduct->category_id)) {
      $this->view->category_name = $this->view->tableCategory->getCategory($sitestoreproduct->category_id)->category_name;

      if (!empty($sitestoreproduct->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitestoreproduct->subcategory_id)->category_name;

        if (!empty($sitestoreproduct->subsubcategory_id)) {
          $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($sitestoreproduct->subsubcategory_id)->category_name;
        }
      }
    }
    
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

    //GET PRODUCT TAGS
    $this->view->sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
    $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, 'edit');

    //POPULATE FORM
    $this->view->otherInfo = $row = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
    
    $this->view->productQuantityBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantitybox', 0);
    
    
    $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
//    $allowCombinationsQuantity = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.check.combination.quantity', 0);
    
    $categoryCombinations = Engine_Api::_()->sitestoreproduct()->getCombinationOptions($sitestoreproduct->product_id, 1);
    $Combinations = Engine_Api::_()->sitestoreproduct()->getCombinationOptions($sitestoreproduct->product_id);
    $this->view->out_of_stock = 0;
    
    if(($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual') && !empty($allowCombinations) && !empty($Combinations) && empty($categoryCombinations)){
      $this->view->out_of_stock = 1;
      $this->view->notAllowSelling = true;
    }
//    $this->view->show_items_left = 0;
//    if(($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual') && !empty($allowCombinations) && !empty($allowCombinationsQuantity)){
//      $this->view->show_items_left = 1;
//    }
    
    if( $sitestoreproduct->product_type == 'virtual' ) {
      $this->view->productInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
      //$this->view->productQuantityBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.quantitybox', 0);
      
      $this->view->productQuantityBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantitybox', 0);
      
      if( !empty($row->product_info) ) {
        $virtualProductOptions = unserialize($row->product_info);
        if( !empty($virtualProductOptions) ) {
          if( !empty($virtualProductOptions['virtual_product_date_selector']) )
            $this->view->dateTimeSelector = $dateTimeSelector = $virtualProductOptions['virtual_product_date_selector'];

          if( !empty($virtualProductOptions['virtual_product_price_range']) && $virtualProductOptions['virtual_product_price_range'] != 'fixed' ) {
            $this->view->productPriceOptions = array('priceRange' => Engine_Api::_()->sitestoreproduct()->getProductPriceRangeText($virtualProductOptions['virtual_product_price_range']));
            $this->view->priceRangeText = $virtualProductOptions['virtual_product_price_range'];
          }
        }
      }
    } else {
      $this->view->productInventory = true;
    }
    
    $this->view->out_of_stock_action = $row->out_of_stock_action;

    //POPULATE FORM
    $this->view->email = $row->email;
    $this->view->phone = $row->phone;
    $this->view->website = $row->website;

    $this->view->create_review = ($sitestoreproduct->owner_id == $viewer->getIdentity()) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.allowownerreview', 0) : 1;
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      $this->view->create_review = 0;
    }
    //GET NAVIGATION
    $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_gutter");

    //ADD TO SHOPPING CART WORK
    $this->view->doNotRenderForm = false;
    $this->view->option_id = $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
    if ($option_id && ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual')) {
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
        $closed = empty($sitestoreproduct->closed);
      } 
      else {
        $closed = 1;
      }      

      if((!empty($sitestoreproduct->stock_unlimited) || $sitestoreproduct->in_stock >= $sitestoreproduct->min_order_quantity) && $closed && empty($sitestoreproduct->draft) && !empty($sitestoreproduct->search) && !empty($sitestoreproduct->approved) && ($sitestoreproduct->start_date < date('Y-m-d H:i:s')) && (empty($sitestoreproduct->end_date_enable) || $sitestoreproduct->end_date > date('Y-m-d H:i:s') ) ) {
        
        $isAllowSelling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id);
        
        if( empty($isAllowSelling) )
          $this->view->notAllowSelling = true;
        else {
        $this->view->form = $form = new Sitestoreproduct_Form_Custom_Standard(array(
            'item' => 'sitestoreproduct_cartproduct',
            'topLevelId' => 1,
            'topLevelValue' => $option_id,
            'productId' => $sitestoreproduct->product_id,
            'hideSelect' => 1,
        ));     
        if( $sitestoreproduct->product_type == 'virtual' && !empty($dateTimeSelector) ) {
          
          $form->addElement('hidden', 'is_calendar_allow', array('value' => 1, 'order' => 9999999));

          $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
          $starttime->setLabel("From");
          $starttime->setAllowEmpty(false);
          $starttime->setValue(date("Y-m-d H:i:s"));
          //$starttime->setValue(date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') , date('d'), date('Y'))));
          $form->addElement($starttime);

          $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
          $endtime->setLabel("To");
          $endtime->setAllowEmpty(false);
          $endtime->setValue(date("Y-m-d H:i:s"));
          $form->addElement($endtime);
        }
        
//        $this->view->showQuantityBox = $productQuantityBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.quantitybox', 0);
//        if( $sitestoreproduct->product_type == 'virtual' && !empty($productQuantityBox) )
//          $formElementType = 'Text';
//        else
//          $formElementType = 'Hidden';
       $productQuantityBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantitybox', 0);
       if(!empty($productQuantityBox))
          $formElementType = 'Text';
       else
          $formElementType = 'Hidden';
       
        $form->addElement($formElementType, 'quantity', array(
            'label' => 'Qty:',
            'required' => true,
            'allowEmpty' => false,
            'value' => $sitestoreproduct->min_order_quantity,
        ));
        $form->getElement('quantity')->setAttrib('style','width:50px;');

        $form->submit_addtocart->setLabel("Add to cart");
        $form->setAttrib('id', 'add_to_cart');
        $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'siteform', 'action' => 'make-order', 'product_id' => $sitestoreproduct->product_id, 'option_id' => $option_id), 'sitestoreproduct_extended', true));
        
        //ADD COMPARE AND WISHLIST LINKS        
        $form->addElement('Hidden', 'compare_wishlist', array(
            'order' => 99999999,
            'decorators' => array('ViewHelper', array('ViewScript', array(
                        'viewScript' => '_compareWishlist.tpl',
                        'sitestoreproduct' => $sitestoreproduct,
                        'identity' => $this->view->identity,
                        'create_review' => $this->view->create_review,
                        'isQuickView' => $this->view->isQuickView,
                        'prependText' => '     ',
                        //'class' => 'form element'
                    ))),
        ));        
        
        $form->addDisplayGroup(array(
            'submit_addtocart',
            'compare_wishlist'
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
      }}
      else
      {
        $this->view->doNotRenderForm = true;
      }
    }

    //VIEW GROUP PRODUCTS
    if ($sitestoreproduct->product_type == 'grouped') {
      
      $params = array();
      $params['product_type'] = 'grouped';
      $params['product_id'] = $sitestoreproduct->product_id;

      $this->view->groupedProducts = $groupedProducts = $productTable->getCombinedProducts($params);

      if (Zend_Controller_Front::getInstance()->getRequest()->isPost()) {

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        foreach ($groupedProducts as $productdetail) 
        {
          $quantity = (isset($_POST['quantity']) && !empty($_POST['quantity'])) ? $_POST['quantity'] : $productdetail->min_order_quantity;
          if (empty($viewer_id)) 
          {
            $tempUserCart = array();
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
            if (empty($session->sitestoreproduct_guest_user_cart)) 
            {
              $session->sitestoreproduct_guest_user_cart = '';
              $tempUserCart[$productdetail->product_id] = array('store_id' => $sitestoreproduct->store_id, 'type' => $sitestoreproduct->product_type, 'quantity' => $quantity);
            }
            else 
            {
              $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
              
              // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
              if( !empty($directPayment) && !empty($isDownPaymentEnable) && !empty($tempUserCart) ) {
                $product_ids = array();
                foreach ($tempUserCart as $product_id => $values) {
                  $product_ids[] = $product_id;
                }
                $product_ids = implode(",", $product_ids);
                $this->checkProductsPaymentType($product_ids, $row->downpayment_value);
              }
            
              if (@array_key_exists($productdetail->product_id, $tempUserCart) )
                $tempUserCart[$productdetail->product_id]['quantity'] += $quantity;
              else
                $tempUserCart[$productdetail->product_id] = array('store_id' => $sitestoreproduct->store_id, 'type' => $sitestoreproduct->product_type, 'quantity' => $quantity);
            }
            $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
          }
          else 
          {
            $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
            $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
            $cart_id = $cartTable->getCartId($viewer_id);

            if (empty($cart_id)) {
              $cartTable->insert(array('owner_id' => $viewer_id));
              $cart_id = $cartTable->getAdapter()->lastInsertId();
              $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $productdetail->product_id, 'quantity' => $quantity));
            }
            else 
            {
              // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
              if( !empty($directPayment) && !empty($isDownPaymentEnable) ) {
                $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                $product_ids = implode(",", $productIds);
                $this->checkProductsPaymentType($product_ids, $row->downpayment_value);
              }
            
              $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $productdetail->product_id));

              // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART
              if (empty($cart_product_obj)) 
                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $productdetail->product_id, 'quantity' => $quantity));
              else
                $cart_product_table->update(array('quantity' =>  new Zend_Db_Expr("quantity + $quantity")),
                                            array('product_id = ?' => $productdetail->product_id, 'cart_id = ?' => $cart_id));
            }
          }
        }
        $hostType = "http://";
        if ((!empty($_SERVER["HTTPS"])) && (@$_SERVER["HTTPS"] == "on")) {
          $hostType = "https://";
        }
        
        $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
        if (!empty($session->sitestoreproductCartCouponDetail)) {
            $session->sitestoreproductCartCouponDetail = null;
        }

        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->gotoUrl($hostType . $_SERVER['HTTP_HOST'] .  $this->view->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true));
      }
    }

    //VIEW BUNDLED DOWNLOADABLE AND SIMPLE PRODUCTS
    if ($sitestoreproduct->product_type == 'bundled' || $sitestoreproduct->product_type == 'downloadable' || $sitestoreproduct->product_type == 'simple') {

      $productId = $sitestoreproduct->product_id;
      if (Zend_Controller_Front::getInstance()->getRequest()->isPost()) 
      {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $hostType = "http://";
        if ((!empty($_SERVER["HTTPS"])) && (@$_SERVER["HTTPS"] == "on")) {
          $hostType = "https://";
        }
        
        $quantity = (isset($_POST['quantity']) && !empty($_POST['quantity'])) ? $_POST['quantity'] : $sitestoreproduct->min_order_quantity;
        
        if (empty($viewer_id)) 
        {
          $tempUserCart = array();
          $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

          if (empty($session->sitestoreproduct_guest_user_cart)) 
          {
            $session->sitestoreproduct_guest_user_cart = '';
            $tempUserCart[$productId] = array('store_id' => $sitestoreproduct->store_id, 'type' => $sitestoreproduct->product_type, 'quantity' => $quantity);
          } 
          else 
          {
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
            
            // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
            if( !empty($directPayment) && !empty($isDownPaymentEnable) && !empty($tempUserCart) ) {
              $product_ids = array();
              foreach ($tempUserCart as $product_id => $values) {
                $product_ids[] = $product_id;
              }
              $product_ids = implode(",", $product_ids);
              $this->checkProductsPaymentType($product_ids, $row->downpayment_value);
            }
            
            if (@array_key_exists($productId, $tempUserCart) )
              $tempUserCart[$productId]['quantity'] += $quantity;
            else
              $tempUserCart[$productId] = array('store_id' => $sitestoreproduct->store_id, 'type' => $sitestoreproduct->product_type, 'quantity' => $quantity);
          }

          $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
        }
        else 
        {
          $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
          $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
          $cart_id = $cartTable->getCartId($viewer_id);

          if (empty($cart_id)) 
          {
            $cartTable->insert(array('owner_id' => $viewer_id));
            $cart_id = $cartTable->getAdapter()->lastInsertId();
            $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $productId, 'quantity' => $quantity));
          } 
          else 
          {
            // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
            if( !empty($directPayment) && !empty($isDownPaymentEnable) ) {
              $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
              $product_ids = implode(",", $productIds);
              $this->checkProductsPaymentType($product_ids, $row->downpayment_value);
            }
        
            $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $productId));

            // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART
            if (empty($cart_product_obj))
              $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $productId, 'quantity' => $quantity));
            else 
              $cart_product_table->update(array('quantity' =>  new Zend_Db_Expr("quantity + $quantity")),
                                          array('product_id = ?' => $productId, 'cart_id = ?' => $cart_id ));
          }
        }
        $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
        if (!empty($session->sitestoreproductCartCouponDetail)) {
            $session->sitestoreproductCartCouponDetail = null;
        }
        $redirector->gotoUrl($hostType . $_SERVER['HTTP_HOST'] .  $this->view->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true));
      }

      if ($sitestoreproduct->product_type == 'bundled') {
        $params = array();
        $params['product_id'] = $productId;
        $this->view->bundledProducts = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getCombinedProducts($params);
        $tempBundleProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, "product_info");
        $bundleProductInfo = @unserialize($tempBundleProductInfo);
        if( !empty($bundleProductInfo) && !empty($bundleProductInfo['bundle_product_attribute']) )
          $this->view->bundle_product_attributes = $bundleProductInfo['bundle_product_attribute'];
      }

      if ($sitestoreproduct->product_type == 'downloadable') {

        //PAGINATOR FOR SAMPLE FILES
        $this->view->downloadableProducts = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->getSampleFiles(array('product_id' => $productId));

        $this->view->isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->IsAnyMainFileExist($productId);
      }
    }
    
    if (Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
      $album = $sitestoreproduct->getSingletonAlbum();
      $this->view->photo_paginator = $photo_paginator = $album->getCollectiblesPaginator();
      $this->view->total_images = $photo_paginator->getTotalItemCount();
    }
    
    
    //WORK FOR SHOWING PAYMENT METHODS STARTS
    $showPaymentMethods = $this->_getParam('showPaymentMethods', 1);
    if ( !empty($showPaymentMethods) ) {
      $isPaymentToSiteEnable = true;
      $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
      if ( empty($isAdminDrivenStore) ) {
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      }

      // DIRECT PAYMENT TO SELLER ENABLED
      if ( empty($isPaymentToSiteEnable) ) {
        $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($sitestoreproduct->store_id, 'store_gateway');
        if ( !empty($storeEnabledgateway) ) {
          $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
          $storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);

          foreach ( $storeEnabledgateway as $gatewayName => $gatewayTableId ) {
            if ( $gatewayName == 'paypal' ) {
              $tempGatewayId = 0;
            } else if ( $gatewayName == 'cheque' ) {
              $tempGatewayId = 1;
            } else if ( $gatewayName == 'cod' ) {
              $tempGatewayId = 2;
            }
            if ( in_array($tempGatewayId, $siteAdminEnablePaymentGateway) ) {
              $finalStoreEnableGateway[] = $gatewayName;
            }
          }
          $this->view->payment_gateway = $gateways = $finalStoreEnableGateway;
          if ( count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway) )
            $isOnlyCodGatewayEnable = true;
        }

        // IF NO PAYMENT GATEWAY ENABLE
        if ( empty($storeEnabledgateway) || empty($finalStoreEnableGateway) )
          $no_payment_gateway_enable = true;

        if ( isset($storeEnabledgateway['cheque']) && !empty($storeEnabledgateway['cheque']) )
          $this->view->storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $checkout_store_id, "storegateway_id" => $storeEnabledgateway['cheque']));
      }
      else {
        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $enable_gateway = $gateway_table->select()
                ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                ->where('enabled = 1')
                ->query()
                ->fetchAll();

        try {
            $admin_payment_gateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1))));
        } catch (Exception $ex) {
            $admin_payment_gateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1)));
        }

        if ( !empty($admin_payment_gateway) ) {
          foreach ( $admin_payment_gateway as $payment_gateway ) {
            if ( empty($payment_gateway) ) {
              $this->view->by_cheque_enable = true;
              $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
            } else if ( $payment_gateway == 1 ) {
              $this->view->cod_enable = true;
            }
          }
        }

        if ( empty($enable_gateway) && !empty($admin_payment_gateway) && empty($this->view->by_cheque_enable) && !empty($this->view->cod_enable) ) {
          $isOnlyCodGatewayEnable = true;
        }
        // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
        if ( empty($enable_gateway) && empty($admin_payment_gateway) ) {
          $no_payment_gateway_enable = true;
        }

        $this->view->payment_gateway = $gateways = $enable_gateway;
      }

      if ( !empty($gateways) ) {

        $payWithString = '';
        foreach ( $gateways as $gateway ) {

          if ( is_array($gateway) && isset($gateway['title']) ) {
            $gatewayTitle = $gateway['title'];
            $payWithString .= $this->view->translate($gatewayTitle) . ', ';
            continue;
          }

          if ( $gateway == '2checkout' ) {
            $payWithString .= $this->view->translate('2Checkout') . ', '; //$this->view->translate('2Checkout, ');
          }

          if ( $gateway == 'paypal' ) {
            $payWithString .= $this->view->translate('Paypal') . ', '; //$this->view->translate('Paypal, ');
          }

          if ( $gateway == 'check' ) {
            $payWithString .= $this->view->translate('Cheque') . ', '; //$this->view->translate('Cheque, ');
          }

          if ( $gateway == 'cod' ) {
            $payWithString .= $this->view->translate('Cash On Delivery') . ', '; //$this->view->translate('Cash On Delivery, ');
          }
        }
        $this->view->payWithString = trim($payWithString, " .','");
      }
    }
    //WORK FOR SHOWING PAYMENT METHODS ENDS
  }
  
  protected function checkProductsPaymentType($product_ids, $downpayment_value) {
    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    $hostType = "http://";
    if ((!empty($_SERVER["HTTPS"])) && (@$_SERVER["HTTPS"] == "on")) {
      $hostType = "https://";
    }
        
    $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
    if( empty($downpayment_value) && !empty($cartProductPaymentType) ) {
      $redirector->gotoUrl($hostType . $_SERVER['HTTP_HOST'] .  $this->view->url(array('action' => 'cart', 'cartproduct' => 1), 'sitestoreproduct_product_general', true));
    } else if( !empty($downpayment_value) && empty($cartProductPaymentType) ) {
      $redirector->gotoUrl($hostType . $_SERVER['HTTP_HOST'] .  $this->view->url(array('action' => 'cart', 'cartproduct' => 2), 'sitestoreproduct_product_general', true));
    }
  }
}