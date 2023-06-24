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
class Sitestoreproduct_Widget_ManageCartController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!$viewer_id){
      $creditsession = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
            if (!empty($creditsession->sitestoreproductCartCreditDetail)) {
              $creditsession->sitestoreproductCartCreditDetail = null;
            }    }
    $creditAllow=false;
    $isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
    if($isSitecreditEnabled){
     $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
     if($isAdminDrivenStore){
      if(!empty($viewer_id)){
        $modulekey= array('store' => 'Stores','event' => 'Events');
        try { $creditmodule = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('credit.modules', Zend_Json_Encoder    ::encode($modulekey)));
      } catch (Exception $ex) {
        $creditmodule = Engine_Api::_()->getApi('settings', 'core')->getSetting('credit.modules', Zend_Json_Encoder::encode($modulekey));
      }
      $creditModuleAllow=in_array("store", $creditmodule);
      if(!empty($creditModuleAllow)){

        $param['user_id']=$viewer_id;
        $param['basedon']= 1;
        $param['count']= 1;
        $credits = Engine_Api::_()->getDbtable('credits','sitecredit')->Credits($param);
        $totalCredits= $credits->credit;
        
        $creditCheckout = Engine_Api::_()->getApi('settings', 'core')->getSetting('credit.checkout', 0);
        if(!empty($creditCheckout)){
         if($totalCredits>$creditCheckout)
          $creditAllow=true;
      } else {
        $creditAllow=true;
      }
    }
  }
}
}
$this->view->creditAllow=$creditAllow;

$isPaymentToSiteEnable = true;
$isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
if (empty($isAdminDrivenStore)) {
  $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
}
$isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
$this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable;
$this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

$this->view->isDownPaymentCouponEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.coupon', 0);

    // GET VIEWER ID

$paramValues = Zend_Controller_Front::getInstance()->getRequest()->getParams();

if (isset($paramValues['cartproduct']) && !empty($paramValues['cartproduct']))
  $this->view->isProductAddedInCart = $isProductAddedInCart = $paramValues['cartproduct'];

if (!empty($isProductAddedInCart)) {
  if ($isProductAddedInCart == 1) {
    $this->view->productAddErrorMessage = $this->view->translate("You can't add this product in your cart right now as your cart contain products which have enabled downpayment and for this product downpayment is not enabled. So please complete checkout process for your cart products or remove these products from your cart to add this product in your cart.");
  } else if ($isProductAddedInCart == 2) {
    $this->view->productAddErrorMessage = $this->view->translate("You can't add this product in your cart right now as your cart contain products for which downpayment is not enabled and for this product downpayment is enabled. So please complete checkout process for your cart products or remove these products from your cart to add this product in your cart.");
  }
}
/* Coupon COde work */
$coupon_session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
if (!empty($coupon_session->sitestoreproductCartCouponDetail)) {
  $this->view->couponDetailisPaymentToSiteEnable = unserialize($coupon_session->sitestoreproductCartCouponDetail);
}

/*credit code work */
$credit_session = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
if (!empty($credit_session->sitestoreproductCartCreditDetail)) {
  $this->view->creditDetail = unserialize($credit_session->sitestoreproductCartCreditDetail);
}
    // GET VIEWER CART OBJECT
if (empty($viewer_id)) {
  $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

  if (empty($session->sitestoreproduct_guest_user_cart)) {
    $this->view->sitestoreproduct_viewer_cart_empty = true;
    return;
  }

  $tempUserCart = array();
  $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

  if (empty($tempUserCart)) {
    $this->view->sitestoreproduct_viewer_cart_empty = true;
    return;
  }
} else {
  $cart_table = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
  $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
  $cart_obj = $cart_table->fetchRow(array('owner_id =?' => $viewer_id));

  if (empty($cart_obj) || empty($cart_obj->cart_id)) {
    $this->view->sitestoreproduct_viewer_cart_empty = true;
    return;
  }

  $cart_id = $cart_obj->cart_id;
}

$productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
$storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
$errorArray = $product_price = array();
$flagOnLoad = 1;

    //FORM POST
if (isset($_POST['update_shopping_cart'])) {
  $quantity_product = $_POST['quantity_product'];
  $flagOnLoad = $temp_product_id = 0;

      // FOR LOGGED-IN VIEWER, PRODUCT ID IS CARTPRODUCT_ID
      // FOR CONFIGURABLE PRODUCT, QUANTITY IS AN ARRAY
  foreach ($quantity_product as $product_id => $quantity) {
    $cartproduct_id = $product_id;
    $can_update_quantity = $is_quantity_update = $can_update_config_product_quantity = false;
    $delete_product = false;
    if (is_array($quantity)) {
      $temp_quantity = 0;
      foreach ($quantity as $config_product_index => $config_product_quantity) {
        if (@preg_match('/^-?(?:\d+|\d*\.\d+)$/', $config_product_quantity)) {
          $config_product_quantity = (float) $config_product_quantity;
          if (@is_float($config_product_quantity)) {
            $config_product_quantity = floor($config_product_quantity);
            $config_product_quantity = (int) $config_product_quantity;
          }
          if (@is_int($config_product_quantity)) {
            if (empty($config_product_quantity)) {
                  // DELETE THE PRODUCT FROM THE CART
              $delete_product = true;
              if (empty($viewer_id)) {
                unset($tempUserCart[$product_id]['config'][$config_product_index]);

                    // CHANGE THE INDEX FROM SESSION ARRAY
                if (COUNT($tempUserCart[$product_id]['config']) == 0) {
                  unset($tempUserCart[$product_id]);
                } else {
                  foreach ($tempUserCart[$product_id]['config'] as $index => $value) {
                    if ($index > $config_product_index) {
                      $tempUserCart[$product_id]['config'][$config_product_index++] = $tempUserCart[$product_id]['config'][$config_product_index];
                      unset($tempUserCart[$product_id]['config'][$config_product_index]);
                    }
                  }
                }
                $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
              }
              
              $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
              if (!empty($session->sitestoreproductCartCouponDetail)) {
                $session->sitestoreproductCartCouponDetail = null;
              }
              $creditsession = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
            if (!empty($creditsession->sitestoreproductCartCreditDetail)) {
              $creditsession->sitestoreproductCartCreditDetail = null;
            } 
            } else if ($config_product_quantity > 0) {
              $is_quantity_update = true;
              $can_update_config_product_quantity[$config_product_index] = true;
              $temp_quantity += $config_product_quantity;
            }
          }
        }
      }
    } else {
      if (@preg_match('/^-?(?:\d+|\d*\.\d+)$/', $quantity)) {
        $quantity = (float) $quantity;
        if (@is_float($quantity)) {
          $quantity = floor($quantity);
          $quantity = (int) $quantity;
        }
        if (@is_int($quantity)) {
          if ($quantity == 0) {
                // DELETE THE PRODUCT FROM THE CART
            $delete_product = true;
            if (empty($viewer_id)) {
              unset($tempUserCart[$product_id]);
              $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
            } else {
              $cartProductItem = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $product_id);
              $cartId = $cartProductItem->cart_id;
              $cartProductItem->delete();
              Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cartId);
            }
            $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
            if (!empty($session->sitestoreproductCartCouponDetail)) {
              $session->sitestoreproductCartCouponDetail = null;
            }
            $creditsession = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
            if (!empty($creditsession->sitestoreproductCartCreditDetail)) {
              $creditsession->sitestoreproductCartCreditDetail = null;
            } 
            $this->view->cart_update_suyccessfully = true;
          } else if ($quantity > 0) {
            $is_quantity_update = true;
            $temp_quantity = $quantity;
            if (!empty($viewer_id)) {
              $product_id = $cart_product_table->select()->from($cart_product_table->info('name'), 'product_id')->where('cartproduct_id =?', $product_id);
            }
          }
        }
      }
    }

        // IF QUANTITY ENTERED BY VIEWER IS A VALID COUNTING NO, THEN CHECK FURTHER CONDITIONS AND UPDATE THE QUANTITY
    if (!empty($is_quantity_update)) {
      if (empty($delete_product)) {
        $productObj = $productTable->getProductAttribute(array("stock_unlimited", "in_stock", "max_order_quantity", "min_order_quantity", "closed", "draft", "approved", "start_date", "end_date", "end_date_enable", "search", "store_id", "allow_purchase", "product_type"), array('product_id' => $product_id));
        $productObj = $productTable->fetchRow($productObj);
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
          $closed = !empty($productObj->closed);
        } else {
          $closed = 0;
        }

        $isSellingAllowedProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($productObj->store_id);
        if (empty($isSellingAllowedProducts) || empty($productObj->allow_purchase)) {
          $errorArray[$cartproduct_id]['error'] = $this->view->translate("It is a non purchasable product. Please delete this product to continue shopping.");
        } else if (empty($productObj->stock_unlimited) && empty($productObj->in_stock)) {
          $errorArray[$cartproduct_id]['error'] = $this->view->translate("This product is currently not available for purchase.");
        } elseif (empty($productObj->stock_unlimited) && $temp_quantity > $productObj->in_stock) {
          if ($productObj->in_stock == 1)
            $errorArray[$cartproduct_id]['error'] = $this->view->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
          else
            $errorArray[$cartproduct_id]['error'] = $this->view->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $productObj->in_stock, $productObj->in_stock);
        }
        else if (!empty($productObj->max_order_quantity) && $temp_quantity > $productObj->max_order_quantity) {
          if ($productObj->max_order_quantity == 1)
            $errorArray[$cartproduct_id]['error'] = $this->view->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
          else
            $errorArray[$cartproduct_id]['error'] = $this->view->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $productObj->max_order_quantity, $productObj->max_order_quantity);
        }
        else if (!empty($productObj->min_order_quantity) && $temp_quantity < $productObj->min_order_quantity) {
          $errorArray[$cartproduct_id]['error'] = $this->view->translate("To order this product, you must add at-least %s quantities of it to your cart.", $productObj->min_order_quantity);
        } else if ($closed || !empty($productObj->draft) || empty($productObj->search) || empty($productObj->approved) || $productObj->start_date > date('Y-m-d H:i:s') || ($productObj->end_date < date('Y-m-d H:i:s') && !empty($productObj->end_date_enable))) {
          $errorArray[$cartproduct_id]['error'] = $this->view->translate("This product is currently not available for purchase.");
        } elseif ($productObj->product_type == 'configurable' || $productObj->product_type == 'virtual'){
          if(!empty($viewer_id)){
           $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartproduct_id);
           $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
           $valueRows = $values->getRowsMatching(array(
             'item_id' => $cartProductObject->getIdentity(),
             ));
           $error = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('quantity', 'quantity_unlimited'), $valueRows, $temp_quantity);
           if(!empty($error))
            $errorArray[$cartproduct_id]['error'] = $error;
          else
            $can_update_quantity = true;
        }
        else{
          
          if (is_array($quantity)) {
            foreach ($quantity as $key => $config_product_quantity) {
              $tempUserCart[$product_id]['config'][$key]['quantity'] = $config_product_quantity;
            }
          } else {
            $tempUserCart[$product_id]['quantity'] = $quantity;
          }
          
          foreach ($tempUserCart as $product_id => $values) {
           if (isset($values['config']) && !empty($values['config']))
             $viewerCartConfigArray[$product_id] = $values['config'];
         }
         
         $error = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('quantity', 'quantity_unlimited'), $viewerCartConfigArray[$product_id], 1);
         
         if(!empty($error))
          $errorArray[$cartproduct_id]['error'] = $error;
        else
          $can_update_quantity = true;
      }
      
    } else {
      $can_update_quantity = true;
    }
    
    if (!empty($can_update_quantity)) {
              // UPDATE PRODUCT QUANTITY
      if (empty($viewer_id)) {
        if (is_array($quantity)) {
          foreach ($quantity as $key => $config_product_quantity) {
            if (!empty($can_update_config_product_quantity[$key])) {
              $tempUserCart[$product_id]['config'][$key]['quantity'] = $config_product_quantity;
            }
          }
        } else {
          $tempUserCart[$product_id]['quantity'] = $quantity;
        }
        $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
      } else {
        $cart_product_table->update(array('quantity' => $quantity), array('cartproduct_id = ?' => $cartproduct_id));
      }
      $this->view->cart_update_suyccessfully = true;
    }
  }
}
}
}

$viewerCartConfig = $product_other_info = $downPaymentPrice = $store_product_id = $store_cartproduct_id = $productIds = array();

if (empty($viewer_id)) {
  foreach ($tempUserCart as $product_id => $values) {
    if (isset($values['config']) && !empty($values['config'])) {
      $index = $temp_quantity = 0;
      $viewerCartConfig[$product_id] = $values['config'];
      foreach ($values['config'] as $item) {
        $productIds[] = $product_id;
        $store_product_id[$values['store_id']][] = $product_id;
        $store_product_quantity[$product_id][$index++] = $item['quantity'];
        $temp_quantity += $item['quantity'];
      }
    } else {
      $productIds[] = $product_id;
      $store_product_id[$values['store_id']][] = $product_id;
      $temp_quantity = $store_product_quantity[$product_id] = $values['quantity'];
    }
    $product_quantity[$product_id] = $temp_quantity;
  }
  if (!empty($productIds))
    $product_ids = implode(",", $productIds);
  $cart_id = true;
  $this->view->viewerCartConfig = $viewerCartConfig;
} else {
  $cart_products = $cart_product_table->getCart($cart_id);
  $tempProductId = 0;
  foreach ($cart_products as $product) {

    if ($tempProductId != $product['product_id']) {
      $tempProductId = $product['product_id'];
      $product_quantity[$product['product_id']] = $product['quantity'];
    } else {
      $product_quantity[$product['product_id']] += $product['quantity'];
    }

    if ($product['product_type'] == 'virtual' && !empty($product['other_info'])) {
      $product_other_info[$product['cartproduct_id']]['product_other_info'] = unserialize($product['other_info']);
    }

    $productIds[] = $product['product_id'];
    $store_product_id[$product['store_id']][] = $product['product_id'];
    $store_cartproduct_id[$product['product_id']]['cartproduct_id'][] = $product['cartproduct_id'];
    $store_product_quantity[$product['cartproduct_id']]['quantity'] = $product['quantity'];
  }
  if (!empty($productIds))
    $product_ids = implode(",", $productIds);
}

if (empty($product_ids)) {
  $this->view->sitestoreproduct_checkout_viewer_cart_empty = true;
  return;
}

$this->view->product_obj = $product_obj = $productTable->getLoggedOutViewerCartDetail($product_ids, true);

    // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
if (empty($isPaymentToSiteEnable) && !empty($isDownPaymentEnable)) {
  $this->view->cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
} elseif (!empty($isDownPaymentEnable)) {
  $this->view->cartProductPaymentType = true;
}

$temp_store_id = 0;
$productPriceRangeText = $productDiscountedPrice = $sellingAllowedProducts = $storeOnlineThresholdAmount = array();

foreach ($product_obj as $index => $product_detail) {
  $cartProduct_ids = empty($viewer_id) ? $product_detail->product_id : $store_cartproduct_id[$product_detail->product_id]['cartproduct_id'];
  $quantity = $product_quantity[$product_detail->product_id];

  if ($temp_store_id != $product_detail->store_id) {
    $temp_store_id = $product_detail->store_id;
    $manage_cart_store_name[$product_detail->store_id] = $storeTable->getStoreName($product_detail->store_id);
    $sellingAllowedProducts[$product_detail->store_id] = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($product_detail->store_id);
    $storeOnlineThresholdAmount[$product_detail->store_id] = Engine_Api::_()->sitestoreproduct()->getOnlinePaymentThreshold($product_detail->store_id);
  }
  $temp_product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_detail->product_id);
  if (!empty($viewer_id)) {
    foreach ($cartProduct_ids as $index => $cartProduct_id) {
      
      if (!empty($isVatAllow)) {
        if ($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual') {
          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($temp_product_obj, null, $cartProduct_id);
        } else {
          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($temp_product_obj);
        }
        $productDiscountedPrice[$product_detail->product_id][$index] = $productPricesArray['product_price_after_discount'];
      } else {
        if ($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual') {
          $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProduct_id);
          $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
          $valueRows = $values->getRowsMatching(array(
            'item_id' => $cartProductObject->getIdentity(),
            ));
          
          $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($temp_product_obj->product_id, array('price', 'price_increment'), $valueRows);
          $productDiscountedPrice[$product_detail->product_id][$index] = $configuration_price + $productTable->getProductDiscountedPrice($product_detail->product_id);
        } else {
          $productDiscountedPrice[$product_detail->product_id][$index] = $productTable->getProductDiscountedPrice($product_detail->product_id);
        }
      }
      $downPaymentPrice[$product_detail->product_id][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_detail->product_id, 'price' => $productDiscountedPrice[$product_detail->product_id][$index]));


      if (!empty($flagOnLoad)) {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
          $closed = !empty($product_detail->closed);
        } else {
          $closed = 0;
        }
        if (empty($sellingAllowedProducts[$product_detail->store_id]) || empty($product_detail->allow_purchase)) {
          $errorArray[$cartProduct_id]['error'] = $this->view->translate("It is a non purchasable product. Please delete this product to continue shopping.");
        } elseif (empty($product_detail->stock_unlimited) && empty($product_detail->in_stock)) {
          $errorArray[$cartProduct_id]['error'] = $this->view->translate("This product is currently not available for purchase.");
        } elseif (empty($product_detail->stock_unlimited) && $quantity > $product_detail->in_stock) {
          if ($productObj->in_stock == 1)
            $errorArray[$cartProduct_id]['error'] = $this->view->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
          else
            $errorArray[$cartProduct_id]['error'] = $this->view->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $product_detail->in_stock, $product_detail->in_stock);
        } else if (!empty($product_detail->max_order_quantity) && $quantity > $product_detail->max_order_quantity) {
          if ($product_detail->max_order_quantity == 1)
            $errorArray[$cartProduct_id]['error'] = $this->view->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
          else
            $errorArray[$cartProduct_id]['error'] = $this->view->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $product_detail->max_order_quantity, $product_detail->max_order_quantity);
        } else if (!empty($product_detail->min_order_quantity) && $quantity < $product_detail->min_order_quantity) {
          $errorArray[$cartProduct_id]['error'] = $this->view->translate("To order this product, you must add at-least %s quantities of it to your cart.", $product_detail->min_order_quantity);
        } else if ($closed || !empty($product_detail->draft) || empty($product_detail->search) || empty($product_detail->approved) || $product_detail->start_date > date('Y-m-d H:i:s') || ($product_detail->end_date < date('Y-m-d H:i:s') && !empty($product_detail->end_date_enable))) {
          $errorArray[$cartProduct_id]['error'] = $this->view->translate("This product is currently not available for purchase.");
        }
        elseif($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual'){
          $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProduct_id);
          $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
          $valueRows = $values->getRowsMatching(array(
            'item_id' => $cartProductObject->getIdentity(),
            ));
          $cartProductTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
          $config_quantity = $cartProductTable->select()->from($cartProductTable->info('name'), 'quantity')
          ->where('cartproduct_id =?', $cartProductObject->getIdentity())
          ->query()->fetchColumn();
          $error = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($temp_product_obj->product_id, array('quantity', 'quantity_unlimited'), $valueRows, $config_quantity);
          if(!empty($error))
            $errorArray[$cartProduct_id]['error'] = $error;
        }
      }
    }
  } else {
    if (isset($viewerCartConfig[$cartProduct_ids]) && !empty($viewerCartConfig[$cartProduct_ids])) {
      foreach ($viewerCartConfig[$cartProduct_ids] as $index => $cartProduct_id) {
        if (!empty($isVatAllow)) {
          if ($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual') {
            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($temp_product_obj, null, $cartProduct_id);
          } else {
            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($temp_product_obj);
          }
          $productDiscountedPrice[$product_detail->product_id][$index] = $productPricesArray['product_price_after_discount'];
        } else {
          if ($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual') {
            $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($temp_product_obj->product_id, array('price', 'price_increment'), $cartProduct_id, 0, 1);

            $productDiscountedPrice[$product_detail->product_id][$index] = $configuration_price + $productTable->getProductDiscountedPrice($product_detail->product_id);
          } else {
            $productDiscountedPrice[$product_detail->product_id][$index] = $productTable->getProductDiscountedPrice($product_detail->product_id);
          }
        }
        $downPaymentPrice[$product_detail->product_id][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_detail->product_id, 'price' => $productDiscountedPrice[$product_detail->product_id][$index]));
      }
    } else {
      if (!empty($isVatAllow)) {
        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($temp_product_obj);
        $productDiscountedPrice[$product_detail->product_id][0] = $productPricesArray['product_price_after_discount'];
      } else {
        $productDiscountedPrice[$product_detail->product_id][0] = $productTable->getProductDiscountedPrice($product_detail->product_id);
      }
      $downPaymentPrice[$product_detail->product_id][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_detail->product_id, 'price' => $productDiscountedPrice[$product_detail->product_id][0]));
    }

    if (!empty($flagOnLoad)) {

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
        $closed = !empty($product_detail->closed);
      } else {
        $closed = 0;
      }

      if (empty($sellingAllowedProducts[$product_detail->store_id]) || empty($product_detail->allow_purchase)) {
        $errorArray[$cartProduct_ids]['error'] = $this->view->translate("It is a non purchasable product. Please delete this product to continue shopping.");
      } elseif (empty($product_detail->stock_unlimited) && empty($product_detail->in_stock)) {
        $errorArray[$cartProduct_ids]['error'] = $this->view->translate("This product is currently not available for purchase.");
      } elseif (empty($product_detail->stock_unlimited) && $quantity > $product_detail->in_stock) {
        if ($productObj->in_stock == 1)
          $errorArray[$cartProduct_ids]['error'] = $this->view->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
        else
          $errorArray[$cartProduct_ids]['error'] = $this->view->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $product_detail->in_stock, $product_detail->in_stock);
      } else if (!empty($product_detail->max_order_quantity) && $quantity > $product_detail->max_order_quantity) {
        if ($product_detail->max_order_quantity == 1)
          $errorArray[$cartProduct_ids]['error'] = $this->view->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
        else
          $errorArray[$cartProduct_ids]['error'] = $this->view->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $product_detail->max_order_quantity, $product_detail->max_order_quantity);
      } else if (!empty($product_detail->min_order_quantity) && $quantity < $product_detail->min_order_quantity) {
        $errorArray[$cartProduct_ids]['error'] = $this->view->translate("To order this product, you must add at-least %s quantities of it to your cart.", $product_detail->min_order_quantity);
      } else if ($closed || !empty($product_detail->draft) || empty($product_detail->search) || empty($product_detail->approved) || $product_detail->start_date > date('Y-m-d H:i:s') || ($product_detail->end_date < date('Y-m-d H:i:s') && !empty($product_detail->end_date_enable))) {
        $errorArray[$cartProduct_ids]['error'] = $this->view->translate("This product is currently not available for purchase.");
      } elseif($temp_product_obj->product_type == 'configurable' || $temp_product_obj->product_type == 'virtual'){
        $error = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($temp_product_obj->product_id, array('quantity', 'quantity_unlimited'), $cartProduct_id, $cartProduct_id['quantity']);
        if(!empty($error))
          $errorArray[$cartProduct_ids]['error'] = $error;
      }
    }
  }
  if ($product_detail->product_type == 'virtual') {
    $virtualProductOptions = unserialize($product_detail->product_info);
    if (!empty($virtualProductOptions['virtual_product_price_range']))
      $productPriceRangeText[$product_detail->product_id] = Engine_Api::_()->sitestoreproduct()->getProductPriceRangeText($virtualProductOptions['virtual_product_price_range']);
  }
}

$this->view->manage_cart_store_name = $manage_cart_store_name;
$this->view->error = $errorArray;
$this->view->cart_id = $cart_id;
$this->view->store_product_id = $store_product_id;
$this->view->store_product_quantity = $store_product_quantity;
$this->view->productPriceRangeText = $productPriceRangeText;
$this->view->product_other_info = $product_other_info;
$this->view->downPaymentPrice = $downPaymentPrice;
$this->view->productDiscountedPrice = $productDiscountedPrice;
$this->view->storeOnlineThresholdAmount = $storeOnlineThresholdAmount;

if (!empty($viewer_id))
  $this->view->store_cartproduct_id = $store_cartproduct_id;

$this->view->currency_symbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
if (empty($currencySymbol)) {
  $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
}

$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
}

}