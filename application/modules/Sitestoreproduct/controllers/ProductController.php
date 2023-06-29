<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProductController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     onSocialEngineAddOns
 */
class Sitestoreproduct_ProductController extends Seaocore_Controller_Action_Standard {

    protected $_session;

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        // redirect to mobile actions
        if (!$this->getRequest()->isPost()) {
            $mobileSupportedAction = array(
                'highlighted',
                'featured',
                'sponsored',
            );

            if (!Engine_Api::_()->seaocore()->checkSitemobileMode('fullsite-mode') && in_array($this->getRequest()->getActionName(), $mobileSupportedAction)) {
                return $this->_helper->redirector->gotoRoute(
                                array_merge(
                                        $this->getRequest()->getParams(), array("action" => $this->getRequest()->getActionName() . "-mobile", "rewrite" => null)
                                ), 'default', true);
            }
        }
    }

    public function cartAction() {
        $order_id = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('order_id', null));
        $sitestoreproduct_manage_cart = Zend_Registry::isRegistered('sitestoreproduct_manage_cart') ? Zend_Registry::get('sitestoreproduct_manage_cart') : null;
        $reorder = $this->_getParam('reorder', null);

        $isBuyAllow = Engine_Api::_()->sitestoreproduct()->isBuyAllowed();
        if (empty($isBuyAllow) || empty($sitestoreproduct_manage_cart)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //REORDER THE ORDER PRODUCTS
        if (!empty($order_id) && !empty($reorder)) {
            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

            //IF VIEWER IS NOT LOGGED-IN
            if (empty($viewer_id)) {
                return;
            }

            $buyer_id = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id)->buyer_id;

            //IF VIEWER AND BUYER ARE NOT SAME
            if (($buyer_id != $viewer_id)) {
                return;
            }

            $params = array();
            $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
            $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                $params['fetchDownpaymentValue'] = 1;
            }
            $order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getReorderProducts($order_id, $params);

            $cart_table = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
            $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

            $viewerCartObj = $cart_table->fetchRow(array('owner_id = ?' => $viewer_id));
            if (!empty($viewerCartObj))
                $cart_id = $viewerCartObj->cart_id;

            if (!empty($order_products)) {
                if (empty($cart_id)) {
                    $cart_table->insert(array('owner_id' => $viewer_id, 'creation_date' => date('Y-m-d H:i:s')));
                    $cart_id = $cart_table->getAdapter()->lastInsertId();

                    foreach ($order_products as $product) {
                        if ($product['product_type'] == 'downloadable') {
                            $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);
                            if (!empty($isAnyFileExist))
                                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                        } else
                            $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                    }
                } else {
                    // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
                    if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                        $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                        $product_ids = implode(",", $productIds);
                        $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                    }

                    foreach ($order_products as $product) {
                        if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                            if (empty($order_products['downpayment_value']) && !empty($cartProductPaymentType)) {
                                return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 1), 'sitestoreproduct_product_general', true);
                            } else if (!empty($order_products['downpayment_value']) && empty($cartProductPaymentType)) {
                                return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 2), 'sitestoreproduct_product_general', true);
                            }
                        }
                        $quantity = $product['quantity'];
                        $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $product['product_id']));
                        if (!empty($cart_product_obj))
                            $cart_product_id = $cart_product_obj->product_id;
                        else
                            $cart_product_id = '';

                        if (empty($cart_product_id)) {
                            if ($product['product_type'] == 'downloadable') {
                                $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);
                                if (!empty($isAnyFileExist))
                                    $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                            } else
                                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $quantity));
                        } else {
                            if ($product['product_type'] == 'downloadable') {
                                $isAnyFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($product['product_id']);
                                if (!empty($isAnyFileExist))
                                    $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product['product_id'], 'quantity' => $product['quantity']));
                            }
                            else {
                                $cart_product_table->update(array(
                                    'quantity' => new Zend_Db_Expr("quantity + $quantity"),
                                        ), array(
                                    'product_id = ?' => $product['product_id'],
                                    'cart_id = ?' => $cart_id
                                ));
                            }
                        }
                    }
                }
            }
        }

        $this->_helper->content->setEnabled();
    }

    public function addtoCartAction() {
        // GET VIEWER ID
        $viewer_id = $tempViewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
        $product_id = $this->_getParam('product_id', null);
        $store_type = $this->_getParam('store_type', null);

        if (empty($product_id) || empty($store_type)) {
            $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
            return;
        }

        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        // IF PRODUCT IS NOT READY OR AVAILABLE FOR PURCAHSING
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            if (!empty($productObj->draft) || !empty($productObj->closed) || empty($productObj->search) || empty($productObj->approved) || $productObj->start_date > date('Y-m-d H:i:s') || ($productObj->end_date < date('Y-m-d H:i:s') && !empty($productObj->end_date_enable))) {
                $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
                return;
            }
        } else {
            if (!empty($productObj->draft) || empty($productObj->search) || empty($productObj->approved) || $productObj->start_date > date('Y-m-d H:i:s') || ($productObj->end_date < date('Y-m-d H:i:s') && !empty($productObj->end_date_enable))) {
                $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
                return;
            }
        }

        $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($productObj->store_id);
        if (empty($temp_allowed_selling) || empty($productObj->allow_purchase)) {
            $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
            return;
        }

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        if (empty($viewer_id)) {
            $tempUserCart = array();
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

            if (empty($session->sitestoreproduct_guest_user_cart))
                $session->sitestoreproduct_guest_user_cart = '';
            else
                $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

            if (!empty($directPayment) && !empty($isDownPaymentEnable) && !empty($tempUserCart)) {
                $productIds = array();
                foreach ($tempUserCart as $cart_product_id => $values) {
                    $productIds[] = $cart_product_id;
                }
                $product_ids = implode(",", $productIds);

                $selectedProductDownpaymentValue = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($productObj->product_id, 'downpayment_value');
                $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                if (empty($selectedProductDownpaymentValue) && !empty($cartProductPaymentType)) {
                    $this->view->addToCartError = $this->view->translate("You can't add this product in your cart right now as your cart contain products which have enabled downpayment and for this product downpayment is not enabled.");
                    return;
                } else if (!empty($selectedProductDownpaymentValue) && empty($cartProductPaymentType)) {
                    $this->view->addToCartError = $this->view->translate("You can't add this product in your cart right now as your cart contain products for which downpayment is not enabled and for this product downpayment is enabled.");
                    return;
                }
            }

            // PRODUCT IS ALREADY IN VIEWER CART OR NOT
            if (array_key_exists($product_id, $tempUserCart)) {
                $quantity = $tempUserCart[$product_id]['quantity'];
                $quantity++;
            } else {
                $quantity = $productObj->min_order_quantity;
            }

            $updatedQty = $quantity;
            if (empty($productObj->stock_unlimited) && empty($productObj->in_stock)) {
                $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
            } elseif (empty($productObj->stock_unlimited) && $productObj->in_stock < $updatedQty) {
                if ($productObj->in_stock == 1)
                    $this->view->addToCartError = $this->view->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
                else
                    $this->view->addToCartError = $this->view->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $productObj->in_stock, $productObj->in_stock);
            }
            else if (!empty($productObj->max_order_quantity) && $updatedQty > $productObj->max_order_quantity) {
                if ($productObj->max_order_quantity == 1)
                    $this->view->addToCartError = $this->view->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
                else
                    $this->view->addToCartError = $this->view->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $productObj->max_order_quantity, $productObj->max_order_quantity);
            }
            else {
                $tempUserCart[$product_id] = array('store_id' => $productObj->store_id, 'type' => $productObj->product_type, 'quantity' => $quantity);
                $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
                $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
            }
            return;
        } else {
            $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
            $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

            $cart_id = $cartTable->getCartId($viewer_id);

            if (empty($cart_id)) {
                $row = $cartTable->createRow();
                $row->setFromArray(array('owner_id' => $viewer_id));
                $cart_id = $row->save();

                $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $productObj->min_order_quantity));
                $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
            } else {
                // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
                if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                    $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                    $product_ids = implode(",", $productIds);

                    $selectedProductDownpaymentValue = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($productObj->product_id, 'downpayment_value');
                    $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                    if (empty($selectedProductDownpaymentValue) && !empty($cartProductPaymentType)) {
                        $this->view->addToCartError = $this->view->translate("You can't add to cart this product right now as your cart contain products which have enabled downpayment and for this product downpayment is not enabled.");
                        return;
                    } else if (!empty($selectedProductDownpaymentValue) && empty($cartProductPaymentType)) {
                        $this->view->addToCartError = $this->view->translate("You can't add to cart this product right now as your cart contain products for which downpayment is not enabled and for this product downpayment is enabled.");
                        return;
                    }
                }

                $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $product_id));
                // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART
                if (empty($cart_product_obj)) {
                    $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $productObj->min_order_quantity));
                    $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
                } else {
                    $product_qty = $cart_product_obj->quantity;
                    $updatedQty = $product_qty + 1;

                    if (empty($productObj->stock_unlimited) && empty($productObj->in_stock)) {
                        $this->view->addToCartError = $this->view->translate("This product is currently not available for purchase.");
                    } elseif (empty($productObj->stock_unlimited) && $productObj->in_stock < $updatedQty) {
                        if ($productObj->in_stock == 1)
                            $this->view->addToCartError = $this->view->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
                        else
                            $this->view->addToCartError = $this->view->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $productObj->in_stock, $productObj->in_stock);
                    }
                    else if (!empty($productObj->max_order_quantity) && $updatedQty > $productObj->max_order_quantity) {
                        if ($productObj->max_order_quantity == 1)
                            $this->view->addToCartError = $this->view->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
                        else
                            $this->view->addToCartError = $this->view->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $productObj->max_order_quantity, $productObj->max_order_quantity);
                    }
                    else {
                        $cart_product_table->update(array(
                            'quantity' => new Zend_Db_Expr('quantity + 1'),
                                ), array(
                            'product_id = ?' => $product_id,
                            'cart_id = ?' => $cart_id
                        ));
                        $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
                    }
                }
            }

            if (!empty($this->view->addToCartSuccess)) {
                $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
                if (!empty($session->sitestoreproductCartCouponDetail)) {
                    $session->sitestoreproductCartCouponDetail = null;
                }
            }
        }
    }

    public function deleteCartAction() {


        $cart_id = $this->_getParam('cart_id', null);
        $item_id = $this->_getParam('item_id', null);

        if (empty($cart_id) && empty($item_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        // CLEAR SHOPPING CART
        if (empty($item_id)) {
            $this->view->clear_shopping_cart = true;
        }

        // GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
            if (empty($session->sitestoreproduct_guest_user_cart)) {
                $this->view->sitestoreproduct_viewer_cart_empty = true;
                return;
            }
        } else {
            $cart_table_obj = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
            $cart_product_table_obj = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
            if (empty($cart_id)) {
                $cart_product_obj = $cart_product_table_obj->fetchRow(array('cartproduct_id =?' => $item_id));
                if (empty($cart_product_obj) || empty($cart_product_obj->cart_id)) {
                    $this->view->sitestoreproduct_viewer_cart_empty = true;
                    return;
                }
                $cart_id = $cart_product_obj->cart_id;
            }
            $cart_obj = $cart_table_obj->fetchRow(array('cart_id = ?' => $cart_id));

            if (empty($cart_obj)) {
                $this->view->sitestoreproduct_viewer_cart_empty = true;
                return;
            }
            if ($cart_obj->owner_id != $viewer_id && $viewer->level_id != 1) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        // Check post
        if ($this->getRequest()->isPost()) {
            if (empty($viewer_id)) {
                $tempUserCart = array();
                $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

                if (empty($item_id)) {
                    unset($tempUserCart);
                } else {
                    $is_array = $this->_getParam('is_array', 0);
                    $index_id = $this->_getParam('index_id', null);

                    if (!empty($is_array)) {
                        unset($tempUserCart[$item_id]['config'][$index_id]);
                        if (COUNT($tempUserCart[$item_id]['config']) == 0) {
                            unset($tempUserCart[$item_id]);
                        } else {
                            foreach ($tempUserCart[$item_id]['config'] as $index => $value) {
                                if ($index > $index_id) {
                                    $tempUserCart[$item_id]['config'][$index_id++] = $tempUserCart[$item_id]['config'][$index];
                                    unset($tempUserCart[$item_id]['config'][$index]);
                                }
                            }
                        }
                    } else {
                        unset($tempUserCart[$item_id]);
                    }
                }
                $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
            } else {
                //CLEAR SHOPPING CART
                if (empty($item_id)) {
                    Engine_Api::_()->getItem('sitestoreproduct_cart', $cart_id)->delete();
                } else {
                    $cartProductItem = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $item_id);
                    $cartId = $cartProductItem->cart_id;
                    $cartProductItem->delete();
                    Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cartId);
                }
            }

            $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
            if (!empty($session->sitestoreproductCartCouponDetail)) {
                $session->sitestoreproductCartCouponDetail = null;
            }
            $creditsession = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
            if (!empty($creditsession->sitestoreproductCartCreditDetail)) {
              $creditsession->sitestoreproductCartCreditDetail = null;
            } 

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRedirect' => $this->view->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true),
                'parentRedirectTime' => 10,
                'messages' => empty($item_id) ? array(Zend_Registry::get('Zend_Translate')->_('Shopping cart deleted successfully.')) : array(Zend_Registry::get('Zend_Translate')->_('Cart product successfully deleted.'))
            ));
        }
    }

    public function paymentToMeAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
// check Seller enabled payment method or not which is enabled by admin

        $AdminPaymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', "payumoney");


        $gateway_id = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->getSellerStoreGateway($store_id, $AdminPaymentMethod);

        if ($gateway_id) {
            $this->view->enablePaymentRequest = true;
        } else {
            $this->view->enablePaymentRequest = false;
        }
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $this->view->minimum_requested_amount = $minimum_requested_amount = @round(Engine_Api::_()->sitestoreproduct()->getTransferThreshold($store_id), 2);
        $total_store_amount = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getTotalAmount($store_id);
        $total_store_amount = $total_store_amount[0];
        if (empty($total_store_amount->sub_total) && empty($total_store_amount->order_count)) {
            $total_amount = 0;
        } else {
            $total_amount = $total_store_amount->sub_total + $total_store_amount->store_tax + $total_store_amount->shipping_price - $total_store_amount->commission_value;
        }
        $this->view->total_amount = @round($total_amount, 2);
        $this->view->order_count = $total_store_amount->order_count;
        $this->view->threshold_amount = Engine_Api::_()->sitestoreproduct()->getTransferThreshold($store_id);

        $remaining_amount_table = Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct');
        $remaining_amount_obj = $remaining_amount_table->fetchRow(array('store_id = ?' => $store_id));
        $paymentRequestTable = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct');
        $requested_amount = $paymentRequestTable->getRequestedAmount($store_id);

        if (empty($remaining_amount_obj->store_id)) {
            $remaining_amount_table->insert(array('store_id' => $store_id, 'remaining_amount' => 0));
            $remaining_amount = 0;
        } else {
            $remaining_amount = $remaining_amount_obj->remaining_amount;
        }

        $this->view->remaining_amount = @round($remaining_amount, 2);
        $this->view->requesting_amount = empty($requested_amount) ? 0 : @round($requested_amount, 2);

        $this->_helper->layout->disableLayout();
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['store_id'] = $store_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            $params['request_date'] = $_POST['request_date'];
            $params['response_date'] = $_POST['response_date'];
            $params['request_min_amount'] = $_POST['request_min_amount'];
            $params['request_max_amount'] = $_POST['request_max_amount'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['request_status'] = $_POST['request_status'];
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getStorePaymentRequestPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function paymentRequestAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $minimum_requested_amount = @round(Engine_Api::_()->sitestoreproduct()->getTransferThreshold($store_id), 2);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $remaining_amount = Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id))->remaining_amount;
        $total_store_amount = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getTotalAmount($store_id);
        $total_store_amount = $total_store_amount[0];
        $total_amount = empty($total_store_amount->sub_total) ? 0 : $total_store_amount->sub_total + $total_store_amount->store_tax + $total_store_amount->shipping_price - $total_store_amount->commission_value;
        $this->view->user_max_requested_amount = $user_requested_amount = @round(($remaining_amount + $total_amount), 2);
        $order_count = $this->_getParam('order_count');

        $gateway_id = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->getStoreGateway($store_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($gateway_id)) {
            if ($viewer_id == $sitestore->owner_id)
                $this->view->req_page_owner = true;
            $this->view->gateway_disable = 1;
        } else if ($minimum_requested_amount > $user_requested_amount) {
            $this->view->not_allowed_for_payment_request = 1;
            $this->view->minimun_requested_amount = $minimum_requested_amount;
            $this->view->gross_amount = $user_requested_amount;
        } else {
            $this->view->form = $form = new Sitestoreproduct_Form_Paymentrequest(array('requestedAmount' => $user_requested_amount, 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $user_requested_amount));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

            $form->total_amount->setLabel($this->view->translate('New Sales <br /> (%s)', $currencyName));
            $form->total_amount->getDecorator('Label')->setOption('escape', false);

            $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
            $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

            $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
            $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

            $form->amount->setLabel($this->view->translate('Amount to be Requested <br /> (%s)', $currencyName));
            $form->amount->getDecorator('Label')->setOption('escape', false);

            $form->removeElement('last_requested_amount');
            $this->view->user_requested_amount = $user_requested_amount;

            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'amount_to_be_requested' => @round($user_requested_amount, 2));
            $temp_values = $form->getValues();
            $values['amount'] = $temp_values['amount'];
            $values['message'] = $temp_values['message'];

            $form->populate($values);

            if ($values['amount'] < $minimum_requested_amount && $values['amount'] > 0) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($values['amount']), Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }

            if ($values['amount'] > $user_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting a amount for which you are not able. Please request for a amount equal or less than %s');
                $error = sprintf($error, Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($user_requested_amount));
                $form->addError($error);
                return;
            }

            $remaining_amount = @round($user_requested_amount - $values['amount'], 2);
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $payment_req_table = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct');
                $payment_req_table->insert(array(
                    'store_id' => $store_id,
                    'order_count' => $order_count,
                    'request_amount' => @round($values['amount'], 2),
                    'request_date' => date('Y-m-d H:i:s'),
                    'request_message' => $values['message'],
                    'remaining_amount' => $remaining_amount,
                    'request_status' => '0',
                ));

                $request_id = $payment_req_table->getAdapter()->lastInsertId();
                $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);

                //UPDATE PAYMENT REQUEST ID IN ORDER TABLE
                Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(
                        array('payment_request_id' => $request_id), array('store_id =? AND payment_request_id = 0 AND direct_payment = 0' => $store_id));

                //UPDATE REMAINING AMOUNT
                Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct')->update(
                        array('remaining_amount' => $remaining_amount), array('store_id =? ' => $store_id));

                $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

                if ($viewer_id != $sitestore->owner_id) {
                    // SEND MAIL TO STORE OWNER ABOUT PAYMENT REQUEST
                    $user = Engine_Api::_()->getItem('user', $sitestore->owner_id);
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'sitestoreproduct_payment_request', array(
                        'object_title' => $sitestore->getTitle(),
                        'object_name' => $store_name,
                        'sender_name' => $viewer->getTitle(),
                        'request_amount' => Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($values['amount']),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $sitestore->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me'), 'sitestore_store_dashboard', false),
                    ));
                }

                // SEND MAIL TO SITE ADMIN FOR THIS PAYMENT REQUEST
                $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);

                if (!empty($admin_email_id)) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'sitestoreproduct_payment_request_to_admin', array(
                        'object_title' => $sitestore->getTitle(),
                        'object_name' => $store_name,
                        'request_amount' => Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($values['amount']),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestoreproduct', 'controller' => 'payment'), 'admin_default', true),
                    ));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your payment request has been successfully sent.'))
            ));
        }
    }

    public function editPaymentRequestAction() {
        //ONLY LOGGED IN USER CAN EDIT
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj))
            return $this->_forward('notfound', 'error', 'core');

        $this->view->store_id = $store_id = $payment_req_obj->store_id;
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $gateway_id = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->getStoreGateway($store_id);
        $payment_req_table_obj = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct');

        if ($payment_req_obj->request_status == 1) {
            $this->view->sitestoreproduct_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->sitestoreproduct_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->sitestoreproduct_admin_responding_request = true;
                return;
            }
        }

        if (empty($gateway_id)) {
            if ($viewer_id == $sitestore->owner_id) {
                $this->view->req_page_owner = true;
            }
            $this->view->gateway_disable = 1;
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('store_id = ?' => $store_id))->remaining_amount;
        $total_store_amount = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getTotalAmount($store_id);
        $total_store_amount = $total_store_amount[0];
        $total_amount = empty($total_store_amount->sub_total) ? 0 : $total_store_amount->sub_total + $total_store_amount->store_tax + $total_store_amount->shipping_price - $total_store_amount->commission_value;
        $amount_to_be_requested = $remaining_amount + $total_amount + $payment_req_obj->request_amount;

        $this->view->form = $form = new Sitestoreproduct_Form_Paymentrequest(array('requestedAmount' => @round($payment_req_obj->request_amount, 2), 'totalAmount' => $total_amount, 'remainingAmount' => $remaining_amount, 'amounttobeRequested' => $amount_to_be_requested));
        $form->last_requested_amount->setValue(@round($payment_req_obj->request_amount, 2));
        $form->message->setValue($payment_req_obj->request_message);

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_amount->setLabel($this->view->translate('New Sales <br /> (%s)', $currencyName));
        $form->total_amount->getDecorator('Label')->setOption('escape', false);

        $form->remaining_amount->setLabel($this->view->translate('Remaining Amount <br /> (%s)', $currencyName));
        $form->remaining_amount->getDecorator('Label')->setOption('escape', false);

        $form->last_requested_amount->setLabel($this->view->translate('Last Requested Amount <br /> (%s)', $currencyName));
        $form->last_requested_amount->getDecorator('Label')->setOption('escape', false);

        $form->amount_to_be_requested->setLabel($this->view->translate('Balance Amount <br /> (%s)', $currencyName));
        $form->amount_to_be_requested->getDecorator('Label')->setOption('escape', false);

        $form->amount->setLabel($this->view->translate('New Amount to be Requested <br /> (%s)', $currencyName));
        $form->amount->getDecorator('Label')->setOption('escape', false);

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = array('total_amount' => @round($total_amount, 2), 'remaining_amount' => @round($remaining_amount, 2), 'last_requested_amount' => @round($payment_req_obj->request_amount, 2), 'amount_to_be_requested' => @round($amount_to_be_requested, 2));
        $temp_values = $form->getValues();
        $values['amount'] = $temp_values['amount'];
        $values['message'] = $temp_values['message'];

        $form->populate($values);
        $minimum_requested_amount = @round(Engine_Api::_()->sitestoreproduct()->getTransferThreshold($store_id), 2);

        if (@round($values['amount'], 2) != @round($payment_req_obj->request_amount, 2)) {
            $user_max_requested_amount = @round($payment_req_obj->request_amount, 2) + @round($remaining_amount, 2) + @round($total_amount, 2);

            if ($values['amount'] < $minimum_requested_amount) {
                $error = Zend_Registry::get('Zend_Translate')->_('You are requesting for a less amount (%s) than the minimun request payment amount (%s) set by site administrator. Please request for an amount equal or greater than (%s)');
                $error = sprintf($error, Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($values['amount']), Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($minimum_requested_amount), Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($minimum_requested_amount));
                $form->addError($error);
                return;
            }
            if ($values['amount'] > $user_max_requested_amount) {
                $form->addError('You are requesting a amount for which you are not able. Please request for a amount equal or less than in your shopping account.');
                return;
            }

            $remaining_amount = @round(($user_max_requested_amount - $values['amount']), 2);

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $payment_req_obj->request_amount = @round($values['amount'], 2);
                $payment_req_obj->request_message = $values['message'];
                $payment_req_obj->remaining_amount = $remaining_amount;
                $payment_req_obj->save();

                //UPDATE REMAINING AMOUNT
                $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('store_id =? ' => $store_id));

                // UPDATE ORDERS FOR WHICH PAYMENT REQUEST HAS BEEN SENT
                Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(
                        array('payment_request_id' => 1), array("store_id =? AND payment_request_id = 0 AND direct_payment = 0 AND payment_status LIKE 'active' AND order_status = 5" => $store_id)
                );

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            $payment_req_obj->request_message = $values['message'];
            $payment_req_obj->save();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request edited successfully.'))
        ));
    }

    public function deletePaymentRequestAction() {
        //ONLY LOGGED IN USER CAN EDIT
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $request_id = $this->_getParam('request_id', null);
        $payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);
        if (empty($request_id) || empty($payment_req_obj)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $store_id = $payment_req_obj->store_id;
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        if ($payment_req_obj->request_status == 1) {
            $this->view->sitestoreproduct_payment_request_deleted = true;
            return;
        } else if ($payment_req_obj->request_status == 2) {
            $this->view->sitestoreproduct_payment_request_completed = true;
            return;
        }

        if (!empty($payment_req_obj->payment_flag)) {
            $time_diff = abs(time() - strtotime($payment_req_obj->response_date));
            if ($time_diff > 3600) {
                $payment_req_obj->payment_flag = 0;
                $payment_req_obj->save();
            } else {
                $this->view->sitestoreproduct_admin_responding_request = true;
                return;
            }
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $remaining_amount_table_obj = Engine_Api::_()->getDbtable('remainingamounts', 'sitestoreproduct');
        $remaining_amount = $remaining_amount_table_obj->fetchRow(array('store_id = ?' => $store_id))->remaining_amount;
        $remaining_amount += $payment_req_obj->request_amount;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $payment_req_obj->request_status = 1;
            $payment_req_obj->save();

            //UPDATE REMAINING AMOUNT
            $remaining_amount_table_obj->update(array('remaining_amount' => $remaining_amount), array('store_id =? ' => $store_id));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment request deleted successfully.'))
        ));
    }

    public function setPaymentInfoAction() {
        $values = array();
        $values['username'] = $_POST['username'];
        $values['password'] = $_POST['password'];
        $values['signature'] = $_POST['signature'];
        $values['enabled'] = $_POST['enabled'];
        $store_id = $_POST['store_id'];
        $email = $_POST['email'];

        $form = new Sitestoreproduct_Form_Product_PayPal();

        $payment_info_error = false;

        if (!$form->isValid(array('email' => $email))) {
            $payment_info_error = true;
            $this->view->email_error = $this->view->translate('Please enter a valid email address.');
        }

        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $payment_info_error = true;
            $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }

        if (!empty($payment_info_error)) {
            return;
        }


        $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
        $gateway_id = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''))->gateway_id;

        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        //$testMode = !empty($values['test_mode']);
        unset($values['enabled']);
        //unset($values['test_mode']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // Process
        try {
            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (empty($gateway_id)) {
                $row = $sitestoreproduct_gateway_table->createRow();
                $row->store_id = $store_id;
                $row->user_id = $viewer_id;
                $row->email = $email;
                $row->title = 'Paypal';
                $row->description = '';
                $row->plugin = 'Payment_Plugin_Gateway_PayPal';
                $obj = $row->save();

                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                $gateway->email = $email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                // $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $values = null;
        }

        if (null !== $values) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }

    public function setStoreGatewayInfoAction() {
        if (!empty($_POST)) {
            $isPaypalChecked = $_POST['isPaypalChecked'];
            $isByChequeChecked = $_POST['isByChequeChecked'];
            $isCodChecked = isset($_POST['isCodChecked']) ? $_POST['isCodChecked'] : false;
            $isDownpayment = isset($_POST['isDownpayment']) ? $_POST['isDownpayment'] : false;
            $store_id = $_POST['store_id'];
            $storeChequeInfo = @trim($_POST['bychequeGatewayDetail']);
        }
        $storeGateway = array();
        if (!empty($store_id))
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

        if (empty($sitestore))
            return;

        $payment_info_error = false;
        $isPaypalAdaptiveChecked = false;
        $isPayumoneyChecked = false;
        if ($isPaypalChecked == "true") {
            $paypalDetails = array();
            @parse_str($_POST['paypalGatewayDetail'], $paypalDetails);

            $paypalEmail = $paypalDetails['email'];
            unset($paypalDetails['email']);

            if (!empty($paypalDetails)) {
                $form = new Sitestoreproduct_Form_Product_PayPal();

                if (!$form->isValid(array('email' => $paypalEmail))) {
                    $payment_info_error = true;
                    $this->view->email_error = $this->view->translate('Please enter a valid email address.');
                }

                if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                    $payment_info_error = true;
                    $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                }
            } else {
                $payment_info_error = true;
                $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
            }
        }

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $gatewayDatasValidation = array();
            $gatewayDetailsArray = array();
            foreach ($_POST['additionalGatewaysCheckedArray'] as $key => $additionalGatewaysCheckedArray) {
                if ($additionalGatewaysCheckedArray) {
                    $gatewayKey = ltrim($key, 'is');
                    $gatewayKeyFinal = substr($gatewayKey, 0, -7);
                    $gatewayKeyFinal = strtolower($gatewayKeyFinal);
                    $gatewayKeyFinalUC = ucfirst($gatewayKeyFinal);
                    if ($gatewayKeyFinal == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                        $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                        $sitestoreproduct_gateway_table_obj = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, 'plugin = \'Sitegateway_Plugin_Gateway_Stripe\''));
                        if (empty($sitestoreproduct_gateway_table_obj->config['stripe_user_id'])) {
                            $payment_info_error = true;
                            $this->view->stripe_info_error = $this->view->translate("Please click on 'Connect with Stripe' button before saving the changes.");
                        } else {
                            $gatewayDatasValidation['stripe']['storeGatewayId'] = $sitestoreproduct_gateway_table_obj->gateway_id;
                            $gatewayDatasValidation['stripe']['gatewayEnabled'] = true;
                        }
                    } elseif ($gatewayKeyFinal == 'paypaladaptive') {
                        $isPaypalAdaptiveChecked = true;
                        $paypalDetails = array();
                        @parse_str($_POST['additionalGatewayDetailArray']['paypaladaptiveGatewayDetail'], $paypalAdaptiveDetails);
                        $gatewayDetailsArray[$gatewayKeyFinal] = $paypalAdaptiveDetails;
                        $paypalEmail = $paypalAdaptiveDetails['email'];
                        unset($paypalAdaptiveDetails['email']);
                        $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
                        if (isset($adminAPGateway->config['test_mode'])) {
                            $gatewayDetailsArray[$gatewayKeyFinal]['test_mode'] = $adminAPGateway->config['test_mode'];
                        }
                        if (!empty($paypalAdaptiveDetails)) {
                            $form = new Sitestoreproduct_Form_PayPalAdaptive();

                            if (!$form->isValid(array('email' => $paypalEmail))) {
                                $payment_info_error = true;
                                $this->view->email_error = $this->view->translate('Please enter a valid email address.');
                            }

                            if (empty($paypalAdaptiveDetails['username']) || empty($paypalAdaptiveDetails['password']) || empty($paypalAdaptiveDetails['signature'])) {
                                $payment_info_error = true;
                                $this->view->paypaladaptive_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                            }
                        } else {
                            $payment_info_error = true;
                            $this->view->paypaladaptive_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                        }
                    } elseif ($gatewayKeyFinal == 'mangopay') {

                        $store_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                        $mangoPayDetails = array();
                        $mangoPayBankDetails = array();
                        @parse_str($_POST['additionalGatewayDetailArray']['mangopayGatewayDetail']['personalDetail'], $mangoPayDetails);
                        @parse_str($_POST['additionalGatewayDetailArray']['mangopayGatewayDetail']['accountDetail'], $mangoPayBankDetails);
                        $mangoPayform = new Sitestoreproduct_Form_MangoPay();
                        $this->view->mangopay_error = false;
                        $this->view->mangopay_bankDetail_error = false;

                        //FORM VALIDATION
                        if (!$mangoPayform->isValid($mangoPayDetails)) {
                            $payment_info_error = true;
                            $this->view->mangopay_info_error = $this->view->translate("Please complete all field - it is required.");
                        } else {
                            $result = $store_gateway_table->mangoPayConfigSettings($mangoPayDetails, $sitestore);
                            if ($result['error'] == 1) {
                                $this->view->mangopay_info_error = $result['error_message'];
                                $this->view->mangopay_error = true;
                            } else {
                                $bankDetails = $store_gateway_table->setMangoPayBankDetails($mangoPayBankDetails, $sitestore);
                                if ($bankDetails['error'] == 1) {
                                    $this->view->mangopay_info_error = $bankDetails['errorMessage'];
                                    $this->view->mangopay_bankDetail_error = true;
                                }
                                $storeGateway['mangopay'] = $result['gateway_id'];
                            }
                        }
                    } elseif ($gatewayKeyFinal == 'payumoney') {
                        $isPayumoneyChecked = true;
                        $payumoneyDetails = array();
                        @parse_str($_POST['additionalGatewayDetailArray']['payumoneyGatewayDetail'], $payumoneyPaymentDetails);
                        $gatewayDetailsArray[$gatewayKeyFinal] = $payumoneyPaymentDetails;
                        $MerchantKey = $payumoneyPaymentDetails['merchant_key'];
                        // unset($payumoneyPaymentDetails['merchant_key']);

                        if (!empty($payumoneyPaymentDetails)) {

                            $formClass = "Sitegateway_Form_Order_Payumoney";
                            $form = new $formClass();


                            if (empty($payumoneyPaymentDetails['merchant_key']) || empty($payumoneyPaymentDetails['salt'])) {
                                $payment_info_error = true;
                                $this->view->payumoney_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                            }
                        } else {
                            $payment_info_error = true;
                            $this->view->payumoney_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                        }
                    } else {

                        @parse_str($_POST['additionalGatewayDetailArray'][$gatewayKeyFinal . "GatewayDetail"], $gatewayDetailsArray[$gatewayKeyFinal]);

                        $showInfoError = false;
                        foreach ($gatewayDetailsArray[$gatewayKeyFinal] as $gatewayParam) {
                            if (empty($gatewayParam)) {
                                $showInfoError = true;
                                break;
                            }
                        }

                        $gateway_info_error = $gatewayKeyFinal . "_info_error";
                        if (!empty($gatewayDetailsArray[$gatewayKeyFinal])) {
                            $formClass = "Sitegateway_Form_Order_$gatewayKeyFinalUC";
                            $form = new $formClass();

                            if ($showInfoError) {
                                $payment_info_error = true;
                                $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                            }
                        } else {
                            $payment_info_error = true;
                            $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                        }
                    }
                }
            }
        }

        if ($isByChequeChecked == "true") {
            if (empty($storeChequeInfo)) {
                $payment_info_error = true;
                $this->view->cheque_info_error = $this->view->translate('Please enter your cheque details.');
            }
        }

        if (!empty($payment_info_error))
            return;

        // IF PAYPAL GATEWAY IS ENABLE, THEN INSERT PAYPAL ENTRY IN ENGINE4_SITESTOREPRODUCT_GATEWAY TABLE
        if (($isPaypalChecked == "true" ) && !empty($paypalDetails)) {
            $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');

            $plugin = "Payment_Plugin_Gateway_PayPal";
            $title = "PayPal";

            $sitestoreproduct_gateway_table_obj = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, "plugin = '{$plugin}'"));
            if (!empty($sitestoreproduct_gateway_table_obj))
                $gateway_id = $sitestoreproduct_gateway_table_obj->gateway_id;
            else
                $gateway_id = 0;

            $success_message = $error_message = false;

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $paypalEnabled = true;
            // Process
            try {
                //GET VIEWER ID
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                if (empty($gateway_id)) {
                    $row = $sitestoreproduct_gateway_table->createRow();
                    $row->store_id = $store_id;
                    $row->user_id = $viewer_id;
                    $row->email = $paypalEmail;
                    $row->title = $title;
                    $row->description = '';
                    $row->plugin = $plugin;
                    $obj = $row->save();

                    $gateway = $row;
                } else {
                    $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                    $gateway->email = $paypalEmail;
                    $gateway->save();
                }
                $db->commit();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            // Validate gateway config
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($paypalDetails);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $paypalEnabled = false;
                // $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }

            // Process
            $message = null;
            try {
                $values = $gateway->getPlugin()->processAdminGatewayForm($paypalDetails);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $values = null;
            }

            if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                $paypalDetails = null;
            }

            if (null !== $paypalDetails) {
                $gateway->setFromArray(array(
                    'enabled' => $paypalEnabled,
                    'config' => $paypalDetails,
                ));
                $gateway->save();
                $storePaypalId = $gateway->gateway_id;
            } else {
                if (!$error_message) {
                    $error_message = $message;
                }
            }

            $this->view->error_message = $error_message;
        }

        // IF PAYUMONEY GATEWAY IS ENABLE, THEN INSERT PAYUMONEY ENTRY IN ENGINE4_SITESTOREPRODUCT_GATEWAY TABLE
        if (($isPayumoneyChecked == "true" ) && !empty($payumoneyPaymentDetails)) {

            $Payment_gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
            $Payment_gateway_table_obj = $Payment_gateway_table->fetchRow(array('plugin = \'Sitegateway_Plugin_Gateway_Payumoney\''));
            $testmode = $Payment_gateway_table_obj->test_mode;

            $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');

            $plugin = "Sitegateway_Plugin_Gateway_Payumoney";
            $title = "payumoney";

            $sitestoreproduct_gateway_table_obj = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, "plugin = '{$plugin}'"));
            if (!empty($sitestoreproduct_gateway_table_obj))
                $gateway_id = $sitestoreproduct_gateway_table_obj->gateway_id;
            else
                $gateway_id = 0;

            $success_message = $error_message = false;

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $payumoneyEnabled = true;
            // Process
            try {
                //GET VIEWER ID
                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                if (empty($gateway_id)) {
                    $row = $sitestoreproduct_gateway_table->createRow();
                    $row->store_id = $store_id;
                    $row->user_id = $viewer_id;
                    $row->email = $viewer->email;
                    $row->title = $title;
                    $row->description = '';
                    $row->test_mode = $testmode;
                    $row->plugin = $plugin;
                    $obj = $row->save();

                    $gateway = $row;
                } else {
                    $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                    $gateway->test_mode = $testmode;
                    $gateway->email = $viewer->email;
                    $gateway->save();
                }
                $db->commit();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            // Validate gateway config
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($payumoneyPaymentDetails);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $payumoneyEnabled = false;
                // $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }

            // Process
            $message = null;
            try {
                $values = $gateway->getPlugin()->processAdminGatewayForm($payumoneyPaymentDetails);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $values = null;
            }

            if (empty($payumoneyPaymentDetails['salt']) || empty($payumoneyPaymentDetails['merchant_key'])) {
                $payumoneyPaymentDetails = null;
            }

            if (null !== $payumoneyPaymentDetails) {
                $gateway->setFromArray(array(
                    'enabled' => $payumoneyEnabled,
                    'config' => $payumoneyPaymentDetails,
                ));
                $gateway->save();
                $storePayumoneyId = $gateway->gateway_id;
            } else {
                if (!$error_message) {
                    $error_message = $message;
                }
            }

            $this->view->error_message = $error_message;
        }


        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && !empty($gatewayDetailsArray)) {

            foreach ($gatewayDetailsArray as $key => $gatewayDetails) {

                $gatewayKeyFinalUC = ucfirst($key);

                $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
                $sitestoreproduct_gateway_table_obj = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC"));

                if (!empty($sitestoreproduct_gateway_table_obj))
                    $gateway_id = $sitestoreproduct_gateway_table_obj->gateway_id;
                else
                    $gateway_id = 0;

                $error_message_additional_gateway = false;
                if ($key == "paypaladaptive") {
                    $email = $paypalEmail;
                    $gatewayKeyFinalUC = "PayPalAdaptive";
                } else {
                    $email = $viewer->email;
                }

                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                $gatewayDatasValidation[$key]['gatewayEnabled'] = true;
                // Process
                try {
                    //GET VIEWER ID
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $viewer_id = $viewer->getIdentity();
                    if (empty($gateway_id)) {
                        $row = $sitestoreproduct_gateway_table->createRow();
                        $row->store_id = $store_id;
                        $row->user_id = $viewer_id;
                        $row->email = $email;
                        $row->title = "$gatewayKeyFinalUC";
                        $row->description = '';
                        $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC";
                        $obj = $row->save();
                        $gateway = $row;
                    } else {
                        $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                        $gateway->email = $email;
                        $gateway->save();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                // Validate gateway config
                $gatewayObject = $gateway->getGateway();

                try {
                    $gatewayObject->setConfig($gatewayDetails);
                    $response = $gatewayObject->test();
                } catch (Exception $e) {

                    $gatewayDatasValidation[$key]['gatewayEnabled'] = false;
                    $error_message_additional_gateway = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
                }

                // Process
                $message_additional_gateway = null;
                try {
                    $values = $gateway->getPlugin()->processAdminGatewayForm($gatewayDetails);
                } catch (Exception $e) {
                    $message_additional_gateway = $e->getMessage();
                    $values = null;
                }

                $formValuesValidation = true;
                foreach ($gatewayDetails as $k => $gatewayParam) {
                    if ($k != 'test_mode' && empty($gatewayParam)) {
                        $formValuesValidation = false;
                        break;
                    }
                }

                if ($formValuesValidation) {
                    $gateway->setFromArray(array(
                        'enabled' => $gatewayDatasValidation[$key]['gatewayEnabled'],
                        'config' => $gatewayDetails,
                    ));
                    $gateway->save();
                    $gatewayDatasValidation[$key]['storeGatewayId'] = $gateway->gateway_id;
                } elseif (!$error_message_additional_gateway) {
                    $error_message_additional_gateway = $message_additional_gateway;
                }

                $error_message_gateway = "error_message_$key";
                $this->view->$error_message_gateway = $error_message_additional_gateway;
            }
        }

        // IF BYCHEQUE OR COD ENABLED, THEN SAVE THEIR ENTRY IN STOREGATEWAY TABLE
        if ($isByChequeChecked == "true" || $isCodChecked == "true") {
            $sitestoreproduct_store_gateway_table = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct');

            if (!empty($storeChequeInfo))
                $sitestoreproduct_store_gateway_table->update(array("details" => $storeChequeInfo, "title" => "ByCheque"), array('store_id =?' => $store_id));

            if (empty($isDownpayment)) {
                $storeByChequeDetail = $sitestoreproduct_store_gateway_table->fetchRow(array('store_id = ?' => $store_id, "title = 'ByCheque'", "gateway_type = 0"));
                if (!empty($storeByChequeDetail))
                    $storeByChequeId = $storeByChequeDetail->storegateway_id;
                else
                    $storeByChequeId = '';

                if ($isByChequeChecked == "true") {
                    if (!empty($storeByChequeId)) {
                        $storeByChequeDetail->enabled = 1;
                        $storeByChequeDetail->save();
                    } else {
                        $sitestoreproduct_store_gateway_table->insert(array(
                            'store_id' => $store_id,
                            'title' => 'ByCheque',
                            'details' => $storeChequeInfo,
                            'enabled' => 1
                        ));
                        $storeByChequeId = $sitestoreproduct_store_gateway_table->getAdapter()->lastInsertId();
                    }
                } else if (!empty($storeByChequeId)) {
                    $storeByChequeDetail->enabled = 0;
                    $storeByChequeDetail->save();
                }

                $storeCodDetail = $sitestoreproduct_store_gateway_table->fetchRow(array('store_id = ?' => $store_id, "title = 'COD'", "gateway_type = 0"));
                if (!empty($storeCodDetail))
                    $storeCodId = $storeCodDetail->storegateway_id;
                else
                    $storeCodId = '';

                if ($isCodChecked == "true") {
                    if (!empty($storeCodId)) {
                        $storeCodDetail->enabled = 1;
                        $storeCodDetail->save();
                    } else {
                        $sitestoreproduct_store_gateway_table->insert(array(
                            'store_id' => $store_id,
                            'title' => 'COD',
                            'enabled' => 1
                        ));
                        $storeCodId = $sitestoreproduct_store_gateway_table->getAdapter()->lastInsertId();
                    }
                } else if (!empty($storeCodId)) {
                    $storeByChequeDetail->enabled = 0;
                    $storeByChequeDetail->save();
                }
            }
        }

        if (empty($isDownpayment)) {
            // INSERT ALL ENABLED GATEWAY ENTRY IN STORE TABLE

            if ($isPaypalChecked == "true" && !empty($paypalEnabled)) {
                $storeGateway['paypal'] = $storePaypalId;
            }

            foreach ($_POST['additionalGatewaysCheckedArray'] as $key => $additionalGatewaysCheckedArray) {

                if ($additionalGatewaysCheckedArray && !empty($gatewayDatasValidation)) {

                    $gatewayKey = ltrim($key, 'is');
                    $gatewayKeyFinal = substr($gatewayKey, 0, -7);
                    $gatewayKeyFinal = strtolower($gatewayKeyFinal);

                    if ($gatewayDatasValidation[$gatewayKeyFinal]['gatewayEnabled']) {
                        $storeGateway[$gatewayKeyFinal] = $gatewayDatasValidation[$gatewayKeyFinal]['storeGatewayId'];
                    }
                }
            }
            
            if ($isByChequeChecked == "true") {
                $storeGateway['cheque'] = $storeByChequeId;
            }
            if ($isCodChecked == "true") {
                $storeGateway['cod'] = $storeCodId;
            }

            $sitestore->store_gateway = Zend_Json_Encoder::encode($storeGateway);
            $sitestore->save();
        }
        $this->view->success_message = $this->view->translate('Changes saved.');
    }

    public function paymentInfoAction() {

        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $isPasswordCorrect = false;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (!empty($viewer) && !empty($viewer_id) && (($viewer->level_id == 1) || empty($viewer->username)))
            $isPasswordCorrect = true;

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0) && isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
            $isPasswordCorrect = true;
            $this->view->showStripeConnectChecked = true;
            $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
            $session->unsetAll();
        }

        if (empty($isPasswordCorrect)) {
            if (!$this->getRequest()->isPost())
                return;

            if (empty($_POST['password'])) {
                echo 'payment_info_password_error';
                die;
            }

            $storeOwnerId = Engine_Api::_()->getItem('sitestore_store', $store_id)->owner_id;
            $storeOwnerObj = Engine_Api::_()->getItem('user', $storeOwnerId);

            // MAKING ENCODED PASSWORD STRING
            $passwordString = md5(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt') . $_POST['password'] . $storeOwnerObj->salt);
            if ($passwordString === $storeOwnerObj->password)
                $isPasswordCorrect = true;
        }

        if (!empty($isPasswordCorrect)) {
            $this->view->authenticationSuccess = true;
            $this->view->form = $form = new Sitestoreproduct_Form_Product_PayPal();

            $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

            $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', 'paypal');

            // IF DOWNPAYMENT IS NOT ENABLED
            if (empty($isDownPaymentEnable)) {
                $directPaymentEnable = false;
                $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
                if (empty($isAdminDrivenStore)) {
                    $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
                    $paymentmethod= Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');
                    if (($paymentmethod=='normal' && empty($isPaymentToSiteEnable))||$paymentmethod=='split'||$paymentmethod=='escrow') {
                        $directPaymentEnable = true;

                        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');
                        if ($paymentMethod == 'split') {
                            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.split.gateway', array());
                        } else if ($paymentMethod == 'escrow') {
                            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.escrow.gateway', array());
                        } else {
                            $this->view->enablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                        }

                        $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_gateway');
                        if (!empty($storeEnabledgateway))
                            $storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);
                    }
                }

                if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {

                    $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('pluginLike' => 'Sitegateway_Plugin_Gateway_'));
                    $otherGateways = array();
                    foreach ($getEnabledGateways as $getEnabledGateway) {

                        $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
                        $gatewayKey = strtolower($gatewyPlugin[1]);
                        $gatewayKeyUC = ucfirst($gatewyPlugin[1]);

                        $this->view->showStripeConnect = 0;
                        if ($gatewayKey == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                            $this->view->showStripeConnect = 1;
                            $this->view->stripeConnected = 0;
                            $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                            if (!empty($storeGatewayObj)) {

                                $gateway_id = $storeGatewayObj->gateway_id;

                                if (!empty($gateway_id)) {

                                    // Get gateway
                                    $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                                    if (is_array($gateway->config) && !empty($gateway->config['stripe_user_id'])) {
                                        $this->view->stripeConnected = 1;
                                    }
                                }
                            }

                            if ((!empty($storeEnabledgateway['stripe']) || empty($directPaymentEnable))) {
                                $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                                if (!empty($storeGatewayObj)) {

                                    $gateway_id = $storeGatewayObj->gateway_id;

                                    if (!empty($gateway_id)) {
                                        $this->view->stripeEnabled = true;
                                    }
                                }
                            }
                        } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive') {
                            $this->view->paypalAdaptiveForm = $paypalAdaptiveForm = new Sitestoreproduct_Form_PayPalAdaptive();
                            $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
                            if (!empty($storeGatewayObj)) {

                                if (!empty($storeGatewayObj->gateway_id)) {
                                    $this->view->paypaladaptiveEnabled = true;
                                    // Populate form
                                    $paypalAdaptiveForm->populate($storeGatewayObj->toArray());
                                    if (is_array($storeGatewayObj->config)) {
                                        $paypalAdaptiveForm->populate($storeGatewayObj->config);
                                    }
                                }
                            }
                        } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                            $this->view->mangopayForm = $mangopayForm = new Sitestoreproduct_Form_MangoPay();
                            $this->view->mangopayBankDetailForm = $mangopayBankDetailForm = new Sitestoreproduct_Form_MangoPayBankDetail();

                            $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                            if (!empty($storeGatewayObj)) {

                                $this->view->mangopayEnabled = true;
                                // Populate form
                                $mangopayForm->populate($storeGatewayObj->toArray());
                                $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                                $mode = 'live';
                                if ($adminAPGateway->config['test_mode']) {
                                    $mode = 'sandbox';
                                }
                                $config = isset($storeGatewayObj->config[$mode])?($storeGatewayObj->config[$mode]):null;
                                if (is_array($config)) {
                                    $birthday = $storeGatewayObj->config[$mode]['birthday'];
                                    $config['birthday'] = date('Y-m-d', $birthday);
                                    $mangopayForm->populate($config);
                                    $mangopayBankDetailForm->populate($config);
                                }
                            } else {
                                $viewer = Engine_Api::_()->user()->getViewer();

                                $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                                $select = $searchTable->select();
                                $select->where('item_id = ?', $viewer->getIdentity());
                                $otherUserRecords = $searchTable->fetchRow($select);
                                if ($otherUserRecords) {
                                    $formData['first_name'] = $otherUserRecords->first_name;
                                    $formData['last_name'] = $otherUserRecords->last_name;
                                    $formData['birthday'] = $otherUserRecords->birthdate;
                                }
                                $formData['email'] = $viewer->email;
                                $mangopayForm->populate($formData);
                            }
                        } else {
                            $formName = "form$gatewayKeyUC";
                            $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                            $this->view->$formName = $form = new $formClass();

                            $form->setName("sitestoreproduct_payment_info_$gatewayKey");
                            if ((!empty($storeEnabledgateway[$gatewayKey]) || empty($directPaymentEnable))) {
                                $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin = ?' => $getEnabledGateway->plugin));
                                if (!empty($storeGatewayObj)) {

                                    $gateway_id = $storeGatewayObj->gateway_id;

                                    if (!empty($gateway_id)) {
                                        $gatewyEnabled = $gatewayKey . 'Enabled';
                                        $this->view->$gatewyEnabled = true;

                                        // Get gateway
                                        $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                                        // Populate form
                                        $form->populate($gateway->toArray());

                                        if (is_array($gateway->config)) {
                                            $form->populate($gateway->config);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($storeEnabledgateway['paypal']) || empty($directPaymentEnable)) {
                    $storeGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
                    $gateway_id = $storeGatewayObj->gateway_id;

                    if (!empty($gateway_id)) {
                        $this->view->paypalEnable = true;
                        // Get gateway
                        $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);

                        // Populate form
                        $form->populate($gateway->toArray());
                        if (is_array($gateway->config)) {
                            $form->populate($gateway->config);
                        }
                    }
                }

                if (!empty($storeEnabledgateway['cheque'])) {
                    $this->view->bychequeEnable = true;
                    $this->view->bychequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, "title = 'ByCheque'"))->details;
                }

                if (!empty($storeEnabledgateway['cod'])) {
                    $this->view->codEnable = true;
                }
            } else {
                $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
                if (!empty($directPayment)) {
                    $store_gateway_table = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct');
                    $this->view->adminDefaultPaymentGateway = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.defaultpaymentgateway', serialize(array('cheque', 'cod'))));
                    $this->view->adminRemainingPaymentGateway = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.remainingpaymentgateway', serialize(array('cheque', 'cod'))));
                    $this->view->byChequeDetail = $store_gateway_table->getStoreChequeDetail(array('store_id' => $store_id));

                    $paypalGatewayId = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->isPayPalGatewayEnable($store_id);
                    if (!empty($paypalGatewayId)) {
                        // Get gateway
                        $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $paypalGatewayId);

                        // Populate form
                        $form->populate($gateway->toArray());
                        if (is_array($gateway->config)) {
                            $form->populate($gateway->config);
                        }
                    }

                    $storeDefaultPaymentGateway = $store_gateway_table->getStoreEnabledGateway(array('store_id' => $store_id, 'gateway_type' => 1));
                    foreach ($storeDefaultPaymentGateway as $gatewayName) {
                        if ($gatewayName == 'PayPal')
                            $this->view->storeDefaultPaypalEnable = true;
                        else if ($gatewayName == 'ByCheque')
                            $this->view->storeDefaultBychequeEnable = true;
                        else if ($gatewayName == 'COD')
                            $this->view->storeDefaultCodEnable = true;
                    }

                    $storeRemainingPaymentGateway = $store_gateway_table->getStoreEnabledGateway(array('store_id' => $store_id, 'gateway_type' => 2));
                    foreach ($storeRemainingPaymentGateway as $gatewayName) {
                        if ($gatewayName == 'PayPal')
                            $this->view->storeRemainingPaypalEnable = true;
                        else if ($gatewayName == 'ByCheque')
                            $this->view->storeRemainingBychequeEnable = true;
                        else if ($gatewayName == 'COD')
                            $this->view->storeRemainingCodEnable = true;
                    }
                }
            }
        }
        else {
            echo 'payment_info_password_error';
            die;
        }
    }

    public function saveStorePaymentInfoAction() {
        $store_id = $this->_getParam('store_id', null);
        $default_payment_gateway_paypal = $_POST['default_payment_gateway_paypal'];
        $default_payment_gateway_cheque = $_POST['default_payment_gateway_cheque'];
        $default_payment_gateway_cod = $_POST['default_payment_gateway_cod'];
        $remaining_payment_gateway_paypal = $_POST['remaining_payment_gateway_paypal'];
        $remaining_payment_gateway_cheque = $_POST['remaining_payment_gateway_cheque'];
        $remaining_payment_gateway_cod = $_POST['remaining_payment_gateway_cod'];
        $store_gateway_table = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct');

        $isPayPalInfoExist = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->isPayPalGatewayEnable($store_id);
        $isDefaultPayPalExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'PayPal', 'gateway_type' => 1));

        if (empty($isPayPalInfoExist) && (!empty($default_payment_gateway_paypal) || !empty($remaining_payment_gateway_paypal) )) {
            $default_payment_gateway_paypal = 0;
            $this->view->paypalDetailMissing = true;
        }

        if (!empty($isDefaultPayPalExist)) {
            $store_gateway_table->update(array('enabled' => $default_payment_gateway_paypal), array('store_id =?' => $store_id, 'title =?' => 'PayPal', 'gateway_type =?' => 1));
        } else if (!empty($default_payment_gateway_paypal) && !empty($isPayPalInfoExist)) {
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'PayPal',
                'enabled' => 1,
                'gateway_type' => 1
            ));
        }

        $isDefaultByChequeExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'ByCheque', 'gateway_type' => 1));
        if (!empty($isDefaultByChequeExist)) {
            $store_gateway_table->update(array('enabled' => $default_payment_gateway_cheque), array('store_id =?' => $store_id, 'title =?' => 'ByCheque', 'gateway_type =?' => 1));
        } else if (!empty($default_payment_gateway_cheque)) {
            $byChequeDetail = $store_gateway_table->getStoreChequeDetail(array('store_id' => $store_id));
            $byChequeDetail = empty($byChequeDetail) ? null : $byChequeDetail;
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'ByCheque',
                'details' => $byChequeDetail,
                'enabled' => 1,
                'gateway_type' => 1
            ));
        }

        $isDefaultCodExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'COD', 'gateway_type' => 1));
        if (!empty($isDefaultCodExist)) {
            $store_gateway_table->update(array('enabled' => $default_payment_gateway_cod), array('store_id =?' => $store_id, 'title =?' => 'COD', 'gateway_type =?' => 1));
        } else if (!empty($default_payment_gateway_cod)) {
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'COD',
                'enabled' => 1,
                'gateway_type' => 1
            ));
        }

        $isPayPalExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'PayPal', 'gateway_type' => 2));
        if (!empty($isPayPalExist)) {
            $store_gateway_table->update(array('enabled' => $remaining_payment_gateway_paypal), array('store_id =?' => $store_id, 'title =?' => 'PayPal', 'gateway_type =?' => 2));
        } else if (!empty($remaining_payment_gateway_paypal) && !empty($isPayPalInfoExist)) {
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'PayPal',
                'enabled' => 1,
                'gateway_type' => 2
            ));
        }

        $isBychequeExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'ByCheque', 'gateway_type' => 2));
        if (!empty($isBychequeExist)) {
            $store_gateway_table->update(array('enabled' => $remaining_payment_gateway_cheque), array('store_id =?' => $store_id, 'title =?' => 'ByCheque', 'gateway_type =?' => 2));
        } else if (!empty($remaining_payment_gateway_cheque)) {
            $byChequeDetail = $store_gateway_table->getStoreChequeDetail(array('store_id' => $store_id));
            $byChequeDetail = empty($byChequeDetail) ? null : $byChequeDetail;
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'ByCheque',
                'details' => $byChequeDetail,
                'enabled' => 1,
                'gateway_type' => 2
            ));
        }

        $isCodExist = $store_gateway_table->isGatewayEnable(array('store_id' => $store_id, 'title' => 'COD', 'gateway_type' => 2));
        if (!empty($isCodExist)) {
            $store_gateway_table->update(array('enabled' => $remaining_payment_gateway_cod), array('store_id =?' => $store_id, 'title =?' => 'COD', 'gateway_type =?' => 2));
        } else if (!empty($remaining_payment_gateway_cod)) {
            $store_gateway_table->insert(array(
                'store_id' => $store_id,
                'title' => 'COD',
                'enabled' => 1,
                'gateway_type' => 2
            ));
        }
        $this->view->changes_saved = true;
    }

    public function setPaymentInfoAdditionalGatewayAction() {

        $values = array();
        @parse_str($_POST['gatewayCredentials'], $values);
        $gatewayCredentials = $values;
        $values['enabled'] = $_POST['enabled'];
        $store_id = $_POST['store_id'];
        $gatewayName = $_POST['gatewayName'];
        $gatewayNameUC = ucfirst($gatewayName);

        $payment_info_error = false;

        $showInfoError = false;
        foreach ($gatewayCredentials as $gatewayParam) {
            if (empty($gatewayParam)) {
                $showInfoError = true;
                break;
            }
        }

        if ($showInfoError) {
            $payment_info_error = true;
            $gateway_info_error = $gatewayName . "_info_error";
            $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }

        $Payment_gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $Payment_gateway_table_obj = $Payment_gateway_table->fetchRow(array('plugin = \'Sitegateway_Plugin_Gateway_Payumoney\''));
        $testmode = $Payment_gateway_table_obj->test_mode;

        $sitestoreproduct_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct');
        $gateway_id = $sitestoreproduct_gateway_table->fetchRow(array('store_id = ?' => $store_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayNameUC"))->gateway_id;

        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        unset($values['enabled']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // Process
        try {
            //GET VIEWER ID
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            if (empty($gateway_id)) {
                $row = $sitestoreproduct_gateway_table->createRow();
                $row->store_id = $store_id;
                $row->user_id = $viewer_id;
                $row->email = $viewer->email;
                $row->title = "$gatewayNameUC";
                $row->description = '';
                if ($gatewayNameUC == 'Payumoney')
                    $row->test_mode = $testmode;
                $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayNameUC";
                $row->save();

                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("sitestoreproduct_gateway", $gateway_id);
                $gateway->test_mode = $testmode;
                $gateway->email = $viewer->email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (!$showInfoError) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }

    public function viewPaymentRequestAction() {
        // ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
            return;

        $this->view->request_id = $request_id = $this->_getParam('request_id');
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $this->view->payment_req_obj = Engine_Api::_()->getItem('sitestoreproduct_paymentrequest', $request_id);
    }

    public function getCartProductsAction() {
        //GET VIEWER ID
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->isOtherModule = $this->_getParam('isOtherModule', null);
        $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $otherinfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
        $this->view->isVatAllow = $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
        $this->view->isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();

        $sitestoreproduct_manage_cart = Zend_Registry::isRegistered('sitestoreproduct_manage_cart') ? Zend_Registry::get('sitestoreproduct_manage_cart') : null;
        if (empty($sitestoreproduct_manage_cart))
            return;

        $product_price = $getCart = $product_down_payment_amount = $priceRangeBasis = array();
        $cartProductCounts = 0;
        if (empty($viewer_id)) {
            $tempUserCart = $product_ids = $product_quantity = $product_attribute = $viewerCartConfig = array();

            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
            if (empty($tempUserCart))
                return;

            foreach ($tempUserCart as $product_id => $values) {
                if (!empty($isVatAllow)) {
                    $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                    $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                }
                if (isset($values['config']) && is_array($values['config'])) {
                    $field_id = 0;
                    $viewerCartConfig[$product_id] = $values['config'];
                    foreach ($values['config'] as $quantity) {
                        $cartProductCounts += $quantity['quantity'];
                        $product_quantity[$product_id]['config'][$field_id++] = $quantity['quantity'];
                    }
                } else {
                    $cartProductCounts += $values['quantity'];
                    $product_quantity[$product_id] = $values['quantity'];
                }

                $product_ids[] = $product_id;
                $product_price[$product_id] = $productTable->getProductDiscountedPrice($product_id);
                $product_down_payment_amount[$product_id] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $product_price[$product_id]));
                if (!empty($isVatAllow) && !empty($productPricesArray)) {
                    $product_price[$product_id] = $productPricesArray['display_product_price'];
                    $product_down_payment_amount[$product_id] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $productPricesArray['product_price_after_discount']));
                }
                if ($values['type'] == 'virtual') {
                    $priceRangeBasis[$product_id] = Engine_Api::_()->sitestoreproduct()->getVirtualProductPriceBasis($product_id);
                }
            }

            $product_ids = implode(",", $product_ids);
            $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
            $product_attribute = $productTable->getProductAttribute(array("store_id", "price", "title", "photo_id", "product_id", "product_type"), "product_id IN ($product_ids)", true);
            $product_attribute = $productTable->fetchAll($product_attribute);
            $getCart = $product_attribute;
            $this->view->product_quantity = $product_quantity;
            $this->view->viewerCartConfig = $viewerCartConfig;
        } else {
            $getCartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer_id);
            if (!empty($getCartId)) {
                $getCart = $productTable->getCart($getCartId, false);
                $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($getCartId);

                foreach ($getCart as $cart_product) {
                    if (!empty($isVatAllow)) {
                        $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $cart_product->product_id);
                        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                    }
                    $product_price[$cart_product->product_id] = $productTable->getProductDiscountedPrice($cart_product->product_id);
                    $product_down_payment_amount[$cart_product->product_id] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $cart_product->product_id, 'price' => $product_price[$cart_product->product_id]));
                    if (!empty($isVatAllow) && !empty($productPricesArray)) {
                        $product_price[$cart_product->product_id] = $productPricesArray['display_product_price'];
                        $product_down_payment_amount[$cart_product->product_id] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $cart_product->product_id, 'price' => $productPricesArray['product_price_after_discount']));
                    }
                    if ($cart_product->product_type == 'virtual') {
                        $priceRangeBasis[$cart_product->product_id] = Engine_Api::_()->sitestoreproduct()->getVirtualProductPriceBasis($cart_product->product_id);
                    }
                }
            }
        }

        if (empty($cartProductCounts))
            $getProductCountStr = $this->view->translate('0 items');
        else
            $getProductCountStr = $this->view->translate(array('%s item', '%s items', $cartProductCounts), $this->view->locale()->toNumber($cartProductCounts));

        $this->view->cartProductCounts = $cartProductCounts;
        $this->view->getCartProducts = $getCart;
        $this->view->getProductCountStr = $getProductCountStr;
        $this->view->productPrice = $product_price;
        $this->view->product_down_payment_amount = $product_down_payment_amount;
        $this->view->priceRangeBasis = $priceRangeBasis;
    }

    public function deleteCartProductAction() {
        // GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $cartProductId = $this->_getParam('cartProductId', null);
        if (empty($cartProductId)) {
            return;
        }
        if (empty($viewer_id)) {
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
            if (empty($session->sitestoreproduct_guest_user_cart)) {
                return;
            }

            $tempUserCart = array();
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
            $index_id = $this->_getParam('index_id', null);
            $is_array = $this->_getParam('is_array', null);

            if (!empty($is_array)) {
                unset($tempUserCart[$cartProductId]['config'][$index_id]);
                if (COUNT($tempUserCart[$cartProductId]['config']) == 0) {
                    unset($tempUserCart[$cartProductId]);
                } else {
                    foreach ($tempUserCart[$cartProductId]['config'] as $index => $value) {
                        if ($index > $index_id) {
                            $tempUserCart[$cartProductId]['config'][$index_id++] = $tempUserCart[$cartProductId]['config'][$index];
                            unset($tempUserCart[$cartProductId]['config'][$index]);
                        }
                    }
                }
            } else {
                unset($tempUserCart[$cartProductId]);
            }
            $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
        } else {
            $cartProductItem = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId);
            $cartId = $cartProductItem->cart_id;
            $cartProductItem->delete();
            Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cartId);
        }

        $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
        if (!empty($session->sitestoreproductCartCouponDetail)) {
            $session->sitestoreproductCartCouponDetail = null;
        }

        $this->view->flag = true;
    }

    public function transactionAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //STORE ID 
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        $this->_helper->layout->disableLayout();
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->transaction_state = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->getTransactionState(true, $store_id);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $params['store_id'] = $store_id;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            $params['date'] = $_POST['date'];
            $params['response_min_amount'] = $_POST['response_min_amount'];
            $params['response_max_amount'] = $_POST['response_max_amount'];
            $params['state'] = $_POST['state'];
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('paymentrequests', 'sitestoreproduct')->getAllAdminTransactionsPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function storeTransactionAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $isDirectPaymentEanble = false;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
            if (empty($isPaymentToSiteEnable)) {
                $isDirectPaymentEanble = true;
            }
        }

        if (empty($isDirectPaymentEanble)) {
            return;
        }

        //STORE ID
        $this->view->tab = $tab = $this->_getParam('tab', 0);
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);

        $commission = Engine_Api::_()->sitestoreproduct()->getOrderCommission($store_id);

        if (empty($commission[1])) {
            $this->view->commissionFreePackage = true;
        }

        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $this->_helper->layout->disableLayout();
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $params['store_id'] = $store_id;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            if ($_POST['starttime'] == 'From') {
                $params['from'] = '';
            } else {
                $params['from'] = $_POST['starttime'];
            }

            if ($_POST['endtime'] == 'To') {
                $params['to'] = '';
            } else {
                $params['to'] = $_POST['endtime'];
            }
        }

        // ORDER RELATED TRANSACTIONS
        if (empty($tab)) {
            // FETCH STORE ENABLE GATEWAY
            if (!empty($isDirectPaymentEanble)) {
                $this->view->enablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_gateway');
                if (!empty($storeEnabledgateway))
                    $this->view->storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);
            }

            if (isset($_POST['search'])) {
                $params['username'] = $_POST['username'];
                $params['order_min_amount'] = $_POST['order_min_amount'];
                $params['order_max_amount'] = $_POST['order_max_amount'];
                $params['gateway'] = $_POST['gateway'];
            }

            $this->view->paginator = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->getOrderTransactionsPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        } else {
            if (!isset($_POST['search'])) {
                $session = new Zend_Session_Namespace('Sitestoreproduct_Store_Bill_Payment_Detail');
                if (!empty($session->sitestoreproductStoreBillPaymentDetail)) {
                    $this->view->isPayment = true;
                    $paymentDetail = $session->sitestoreproductStoreBillPaymentDetail;
                    if (isset($paymentDetail['errorMessage']) && !empty($paymentDetail['errorMessage'])) {
                        $this->view->errorMessage = $paymentDetail['errorMessage'];
                    }
                    $this->view->state = $paymentDetail['state'];
                    $session->unsetAll();
                }
            }

            if (isset($_POST['search'])) {
                $params['username'] = $_POST['username'];
                $params['bill_min_amount'] = $_POST['bill_min_amount'];
                $params['bill_max_amount'] = $_POST['bill_max_amount'];
                $params['payment'] = $_POST['payment'];
            }

            //MAKE PAGINATOR
            $this->view->paginator = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->getStoreBillPaginator($params);
            $this->view->total_item = $this->view->paginator->getTotalItemCount();
        }
    }

    public function viewOrderTransactionDetailAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //STORE ID 
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $this->view->transaction_id = $this->_getParam('transaction_id');
        $this->view->order_id = $this->_getParam('order_id');
        $this->view->payment_gateway = $this->_getParam('payment_gateway');
        $this->view->grand_total = $this->_getParam('grand_total');
        $this->view->payment_type = $this->_getParam('payment_type');
        $this->view->payment_state = $this->_getParam('payment_state');
        $this->view->date = $this->_getParam('date');
        $this->view->gateway_transaction_id = $this->_getParam('gateway_transaction_id');
    }

    public function viewTransactionDetailAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //STORE ID 
        $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $this->view->transaction_id = $this->_getParam('transaction_id');
        $this->view->request_id = $this->_getParam('request_id');
        $this->view->payment_gateway = $this->_getParam('payment_gateway');
        $this->view->payment_type = $this->_getParam('payment_type');
        $this->view->payment_state = $this->_getParam('payment_state');
        $this->view->response_amount = $this->_getParam('response_amount');
        $this->view->response_date = $this->_getParam('response_date');
        $this->view->gateway_transaction_id = $this->_getParam('gateway_transaction_id');
        $this->view->gateway_order_id = $this->_getParam('gateway_order_id'); 
    }

    public function changeOrderStatusAction() {
        
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $status = $this->_getParam('status');
        $order_id = $this->_getParam('order_id');
        $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $buyer_id = $order_obj->buyer_id;
        $notify_buyer = $this->_getParam('notify_buyer');
        $notify_seller = $this->_getParam('notify_seller');

        $order_obj->order_status = $status;
        $order_obj->save();
        $temp_status = $this->view->getOrderStatus($status, true);
        $this->view->order_status_no = $status;
        $this->view->status = $temp_status['title'];
        $this->view->status_class = $temp_status['class'];
        $userObj = Engine_Api::_()->getItem('user',$order_obj->buyer_id);
        
        
        
        $discountValue = $order_obj->discount_value;
        if(!$discountValue){
          $cartProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
          $cartProducts = $cartProductTable->select() 
                                               ->from($cartProductTable->info('name'), array('product_id','quantity'))
                                               ->where('order_id =?', $order_id)
                                               ->query()->fetchAll();
          $finalValue = 0;
          foreach($cartProducts as $cartProduct) {
              $infoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
              $discountValue = $infoTable->select()
                               ->from($infoTable->info('name'), 'discount_value')
                               ->where('product_id =?',$cartProduct['product_id'])
                               ->query()->fetchColumn();
              $finalValue += $cartProduct['quantity']*$discountValue;
              
          }
          //if($type == 1) {
              $userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($order_obj->buyer_id);
              $userpointsCount = $userpoints['userpoints_count'];
              if($userpointsCount > $finalValue) {
                $discountValue = $finalValue;
              }
              else
               $discountValue = 0;
        }
        //}
      
      
        
        //ses
        if($status == 5){
          $totalPrice = 0;
          $subTotle = $order_obj->sub_total;
          $dbObject = Engine_Db_Table::getDefaultAdapter();
          $dbObject->query("INSERT INTO engine4_sesbasic_pointcounts SET user_id = ".$order_obj->buyer_id.", points = '".$subTotle."', date= '".date('m-Y')."' ON DUPLICATE KEY UPDATE points = points + '".$subTotle."'");
          $modValue = 0;
          $creationData = date('Y-m-d H:i:s');
          $result = $dbObject->query("SELECT * FROM engine4_sesbasic_pointcounts WHERE user_id = ".$order_obj->buyer_id ." AND date = '".date('m-Y',strtotime($order_obj->creation_date))."'")->fetchAll();
          if($result){
            $res = $result[0];
            $modValueLates = $res['points'] % 1000;
            $totalPriceOrders = $res['points'] - $modValueLates;  
          }
          $dbObject->query('DELETE FROM engine4_sesbasic_bridges where order_id ='.$order_id);
          $viewer_id = $order_obj->buyer_id;
          $userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($order_obj->buyer_id);
        	//$discountValue = $order_obj->discount_value;
        	$remainingPoints = $userpoints['userpoints_count']-$discountValue;
        	$spentPoints = $userpoints['userpoints_totalspent']+$discountValue;
        	Engine_Api::_()->getDbtable('points', 'activitypoints')->update(array('userpoints_count' => $remainingPoints, 'userpoints_totalspent' => $spentPoints), array('userpoints_user_id =?' => $order_obj->buyer_id));
          $level_id = $userObj->level_id;
          $divided_amount = 1000;
          $total_BB = 0;
          if($totalPriceOrders >= $divided_amount){
            $totalPrice  = $totalPriceOrders;
            $total_point_basedontotalamout_paid = $totalPrice / $divided_amount;
            $total_BB = floor($total_point_basedontotalamout_paid);
          }
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
          //START BUSINESS BRIDGES WORK
          if($total_BB) {
              $bridgesTable->insert(array(
                'buyer_user_id' => $viewer_id,
                'buyer_bb' => $total_BB,
                'order_id' =>$order_id,
                'type' => 'business',
                'creation_date' => $order_obj->creation_date,
              ));
              $parentId = $db->lastInsertId();
          }
          //END BUSINESS BRIDGES WORK
          //if($level_id != 4){
                //CB Point work
                $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
                $firstParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $viewer_id)->query()->fetchColumn();
                if($firstParentId) {
                     $objUserIdParent = Engine_Api::_()->getItem('user',$firstParentId);
                     if($objUserIdParent && $objUserIdParent->level_id != 4){
                        $total_CB = ($total_BB / 100) * 50; // 50% of BB Point is equal to CB
                        $bridgesTable->insert(array(
                        'buyer_user_id' => $firstParentId,
                        'user_id' => $viewer_id,
                        'buyer_cb' => $total_CB,
                        'order_id' =>$order_id,
                        'parent_id' => $parentId,
                        'type' => 'collection',
                        'creation_date' => $order_obj->creation_date,
                      ));
                     }
                    //if($level_id == 10 || $level_id == 11 || $level_id == 12 || $level_id == 13 || $level_id == 14){
                      $parentId = $db->lastInsertId();
                      $secondParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $firstParentId)->query()->fetchColumn();
                    if($secondParentId && !empty($total_CB)) {
                        $objUserIdParent = Engine_Api::_()->getItem('user',$secondParentId);
                        if($objUserIdParent && $objUserIdParent->level_id == 10 || $objUserIdParent->level_id == 11 || $objUserIdParent->level_id == 12 || $objUserIdParent->level_id == 13 || $objUserIdParent->level_id == 14){
                          $total_DB = ($total_CB / 100) * 10; // 10% of CB Point is equal to DB
                          $bridgesTable->insert(array(
                            'buyer_user_id' => $secondParentId,
                            'user_id' => $firstParentId,
                            'order_id' =>$order_id,
                            'buyer_db' => $total_DB,
                            'parent_id' => $parentId,
                            'type' => 'direct',
                            'creation_date' => $order_obj->creation_date,
                          ));
                        }
                    }
                    //}
                }  
              //}
              
              //update the points 
              if($totalPrice >= $divided_amount){
                $dbObject->query("UPDATE engine4_sesbasic_pointcounts SET points = ".$modValueLates."  WHERE user_id = ".$order_obj->buyer_id ." AND date = '".date('m-Y',strtotime($order_obj->creation_date))."'");                  
              }              
        }else{
          //again give AB 
          $viewer_id = $order_obj->buyer_id;
          $userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($order_obj->buyer_id);        	
          //$discountValue = $order_obj->discount_value;
      	  $remainingPoints = $userpoints['userpoints_count']+$discountValue;
      	  $spentPoints = $userpoints['userpoints_totalspent']-$discountValue;
    	  Engine_Api::_()->getDbtable('points', 'activitypoints')->update(array('userpoints_count' => $remainingPoints, 'userpoints_totalspent' => $spentPoints), array('userpoints_user_id =?' => $order_obj->buyer_id));          
          //remove from bb table
          $dbObject = Engine_Db_Table::getDefaultAdapter();
          $dbObject->query('DELETE FROM engine4_sesbasic_bridges where order_id ='.$order_id);
          $dbObject->query('DELETE FROM engine4_sescustomize_fbvalues where order_id ='.$order_id);
        }
               
        // SEND MAIL AND NOTIFICATIONS
        if ($notify_buyer == 'true') {
            if (empty($buyer_id)) {
                $billing_email_id = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingEmailId($order_id);
                $order_no = '#' . $order_id;
            } else {
                $billing_email_id = $user = Engine_Api::_()->getItem('user', $buyer_id);
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $order_obj->store_id);
                $order_no = $this->view->htmlLink($this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true), '#' . $order_id);
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $order_obj, 'sitestoreproduct_order_status_change', array('order_id' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));
                $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true) . '">#' . $order_id . '</a>';
            }

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_buyer_for_order_status_change', array(
                'order_id' => '#' . $order_obj->order_id,
                'order_no' => $order_no,
                'status_title' => $this->view->getOrderStatus($status),
                'order_invoice' => $this->view->orderInvoice($order_obj),
            ));
        }

        if (!empty($notify_seller)) {
            $order_no = $this->view->htmlLink($this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true), '#' . $order_id);
            $getPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($order_obj->store_id);

            if (!empty($getPageAdmins)) {
                foreach ($getPageAdmins as $pageAdmin) {
                    if (!empty($pageAdmin->sitestoreproduct_notification)) {
                        continue;
                    }

                    $user = Engine_Api::_()->getItem('user', $pageAdmin->user_id);
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $order_obj, 'sitestoreproduct_order_status_admin_change', array('order_id' => $order_no));

                    $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true) . '">#' . $order_id . '</a>';
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'sitestoreproduct_order_status_change_to_seller', array(
                        'order_id' => '#' . $order_id,
                        'order_no' => $order_no,
                        'status_title' => $this->view->getOrderStatus($status),
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true), manag
                    ));
                }
            }
        }
    
    }

    // SHOW MANAGE STORE OF MY ORDERS TO BUYER.
    public function myOrderAction() {

        // ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->layout->disableLayout();
        }

        //GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $this->view->call_same_action = $this->_getParam('call_same_action', 0);
        $this->view->page_user = 1;

        $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        if (!empty($this->view->isDownPaymentEnable)) {
            $remainingAmountGateways = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.remainingpaymentgateway', serialize(array('paypal', 'cheque', 'cod'))));
            if (!empty($remainingAmountGateways) && count($remainingAmountGateways) == 1 && in_array('cod', $remainingAmountGateways)) {
                $this->view->onlyCodGatewayEnable = true;
            }
        }

        $params = array();
        $params['buyer_id'] = $viewer_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 8;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            $params['billing_name'] = isset($_POST['billing_name']) ? $_POST['billing_name'] : '';
            $params['order_id'] = isset($_POST['order_id']) ? $_POST['order_id'] : '';
            $params['shipping_name'] = isset($_POST['shipping_name']) ? $_POST['shipping_name'] : '';
            $params['creation_date'] = isset($_POST['creation_date']) ? $_POST['creation_date'] : '';
            $params['order_min_amount'] = isset($_POST['order_min_amount']) ? $_POST['order_min_amount'] : '';
            $params['order_max_amount'] = isset($_POST['order_max_amount']) ? $_POST['order_max_amount'] : '';
            $params['delivery_time'] = isset($_POST['delivery_time']) ? $_POST['delivery_time'] : '';
            $params['order_status'] = isset($_POST['order_status']) ? $_POST['order_status'] : '';
            $params['downpayment'] = isset($_POST['downpayment']) ? $_POST['downpayment'] : '';
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getOrdersPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount($this->view->paginator);
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled();
        }
    }

    public function quickViewAction() {

        $this->_helper->layout->setLayout('default-simple');
        $this->view->product_id = $product_id = $this->_getParam('product_id', null);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //SET SITESTOREPRODUCT SUBJECT
        Engine_Api::_()->core()->setSubject($sitestoreproduct);
    }

    //ACTION FOR SHOWING THE HOME STORE
    public function manageAction() {


        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $mobileId = $this->_getParam('store_id', null);
            $mobileSubject = Engine_Api::_()->getItem('sitestore_store', $mobileId);
            if (!empty($mobileSubject) && !Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($mobileSubject);
            }

            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;
            if ($coreversion < '4.1.0') {
                $this->_helper->content->render();
            } else {
                $this->_helper->content
                        ->setNoRender()
                        ->setEnabled();
            }
        }


        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

//      $temp_featured = $this->_getParam('temp_featured');
//      $temp_sponsored = $this->_getParam('temp_sponsored');
        $temp_in_stock = $this->_getParam('temp_in_stock');
//      $temp_newlabel = $this->_getParam('temp_newlabel');
//      $temp_status = $this->_getParam('temp_status');  
//SET NO RENDER IF NO SUBJECT

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $this->view->checked_product = $this->_getParam('checked_product', 0);
        $temp_is_subject = $this->_getParam('temp_is_subject', 1);
        $this->view->responseFlag = $this->_getParam('responseFlag', 0);
        $this->view->printingTagsObj = Engine_Api::_()->getDbTable('printingtags', 'sitestoreproduct')->getPrintingTags($store_id);
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (!empty($viewer->level_id))
            $this->view->viewer_level_id = $viewer->level_id;

        //GET SUBJECT AND STORE ID AND STORE OWNER ID
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->is_subject = 1;
            $this->view->storeSubject = $storeSubject = Engine_Api::_()->getItem('sitestore_store', $store_id);
        } else {
            $this->view->is_subject = empty($temp_is_subject) ? 0 : 1;
            $this->view->storeSubject = $storeSubject = Engine_Api::_()->core()->getSubject('sitestore_store');
        }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'view');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
        //GET LAYOUT
        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
        $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);
        $this->view->can_edit = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'edit');

        //GET SETTINGS    
        $this->view->temp_layouts_views = $this->_getParam('temp_layouts_views', null);
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1"));
        if (!empty($this->view->temp_layouts_views))
            $ShowViewArray = @explode(",", $this->view->temp_layouts_views);
        $this->view->temp_layouts_views = @implode(",", $ShowViewArray);

        $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
        $this->view->temp_statistics = $this->_getParam('statistics', null);
        $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
        if (!empty($this->view->temp_statistics))
            $this->view->statistics = @explode(",", $this->view->statistics);
        $this->view->temp_statistics = @implode(",", $this->view->statistics);

        $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
        $defaultOrder = $this->_getParam('layouts_order', 2);
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        $this->view->title_truncation = $this->_getParam('truncation', 50);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->postedby = $this->_getParam('postedby', 1);

        $this->view->search = $search = $this->_getParam('search');
        $this->view->checkbox = $checkbox = $this->_getParam('checkbox');
        $this->view->selectbox = $selectbox = $this->_getParam('selectbox');

        $this->view->list_view = 0;
        $this->view->grid_view = 0;
        $this->view->defaultView = -1;
        if (in_array("1", $ShowViewArray)) {
            $this->view->list_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 1)
                $this->view->defaultView = 0;
        }
        if (in_array("2", $ShowViewArray)) {
            $this->view->grid_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 2)
                $this->view->defaultView = 1;
        }

        $temViewType = $this->_getParam('temViewType', false);
        if (empty($temViewType))
            $temViewType = $this->view->defaultView;
        $this->view->temViewType = $this->view->defaultView = $temViewType;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $customFieldValues = array();
        $values = array();

        if (!empty($checkbox) && $checkbox == 1) {
            $values['owner_id'] = $viewer_id;
        }

        $this->view->params = $params = $request->getParams();
        $this->view->downpayment = isset($params['downpayment']) ? $params['downpayment'] : '';
//    if(!empty($temp_featured))
//      $params['featured'] = --$temp_featured;
//    if(!empty($temp_sponsored))
//      $params['sponsored'] = --$temp_sponsored;
        if (!empty($temp_in_stock))
            $params['temp_stock'] = --$temp_in_stock;
//    if(!empty($temp_newlabel))
//      $params['newlabel'] = --$temp_newlabel;
//    if(!empty($temp_status))
//      $params['status'] = --$temp_status;
//    
        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;
        $this->view->category_id = $params['category_id'];
        $this->view->subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $params['subsubcategory_id'];

        //SHOW CATEGORY NAME
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->category_id);
            $this->view->categoryName = $this->view->categoryObject->category_name;

            if ($this->view->subcategory_id) {
                $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subcategory_id);
                $this->view->categoryName = $this->view->categoryObject->category_name;

                if ($this->view->subsubcategory_id) {
                    $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subsubcategory_id);
                    $this->view->categoryName = $this->view->categoryObject->category_name;
                }
            }
        }

        if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
            $tag_id = $params['tag_id'];
        }

        $page = 1;
        if (isset($params['page']) && !empty($params['page'])) {
            $page = $params['page'];
        }

        //GET VALUE BY POST TO GET DESIRED PRODUCTS
        if (!empty($params)) {
            $values = array_merge($values, $params);
        }

        //FORM GENERATION
        $form = new Sitestoreproduct_Form_Search(array('type' => 'sitestoreproduct_product'));

        if (!empty($params)) {
            $form->populate($params);
        }

        $this->view->formValues = $form->getValues();

        $values = @array_merge($values, $form->getValues());

        // By pass the location work because location reflect on product at "Store Dashboard: Manage Products"
        $values['location'] = '';
        $values['locationmiles'] = '';

        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $values['orderby'] = $this->_getParam('orderby', 'product_id');
        }
        if (!empty($selectbox) && $selectbox == 'featured') {
            $values['featured'] = 1;
            $values['orderby'] = 'creation_date';
        }
        if (!empty($search)) {
            $values['search'] = $search;
        }
        if (!empty($selectbox)) {
            if ($selectbox == 'selling_price_count') {
                $values['selling_price_count'] = 'selling_price_count';
            } else if ($selectbox == 'selling_item_count') {
                $values['selling_item_count'] = 'selling_item_count';
            } else {
                $values['orderby'] = $selectbox;
            }
        } else {
            $values['orderby'] = 'creation_date';
        }

        $values['page'] = $page;

        //GET LISITNG FPR PUBLIC STORE SET VALUE
        $values['type'] = 'manage';

        if (@$values['show'] == 2) {

            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();

            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }

            $values['users'] = $ids;
        }

        $this->view->assign($values);

        //CORE API
        $this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
            @$values['show'] = 3;
        }

        $values['limit'] = $itemCount = $this->_getParam('itemCount', 100);
        $this->view->bottomLine = $this->_getParam('bottomLine', 1);
        $values['viewType'] = $this->view->viewType = $this->_getParam('viewType', 0);
        $values['showClosed'] = $this->_getParam('showClosed', 1);
        $values['is_widget'] = 1;

        // GET PRODUCTS
        $values['store_id'] = $store_id;
        $values['notifyemails'] = true;
        $values['is_owner'] = true;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);


//    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        $this->view->current_count = $this->view->totalResults = $paginator->getTotalItemCount();

        $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);

        $this->view->flageSponsored = 0;

        //SEND FORM VALUES TO TPL
        $this->view->formValues = $values;

        $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    }

    public function storeDashboardAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        //STORE ID 
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
    }

    public function downloadProductsAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;
        $this->_helper->layout->disableLayout();

        //GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $params = array();
        $params['buyer_id'] = $viewer_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        $this->view->paginator = $downloadableFiles = Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct')->getOrderDownloadsPaginator($params);
    }

    public function downloadAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->_helper->layout->disableLayout();

        $productId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('product_id', NULL));
        $downloadableFileId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('downloadablefile_id', NULL));
        $orderDownloadId = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('download_id', NULL));

        $orderDownloadItem = Engine_Api::_()->getItem('sitestoreproduct_orderdownload', $orderDownloadId);
        $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $downloadableFileId);
        $orderItem = Engine_Api::_()->getItem('sitestoreproduct_order', $orderDownloadItem->order_id);

        if (empty($orderDownloadItem) ||
                empty($downloadablefileItem) ||
                empty($orderItem) ||
                ($viewer_id != $orderItem->buyer_id) ||
                ( (!empty($orderDownloadItem->max_downloads) && $orderDownloadItem->downloads >= $orderDownloadItem->max_downloads) )
        )
            return $this->_forward('notfound', 'error', 'core');

        // Get path
        $path = $relPath = (string) APPLICATION_PATH . '/public/sitestoreproduct_product/file_' . $productId . '/main';

        $downloadablefile_name = $downloadablefileItem->filename;
        $path = $path . '/' . $downloadablefile_name;

        if (@file_exists($path) && @is_file($path)) {
            if (!empty($orderDownloadItem->max_downloads)) {
//        Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct')->update(array(
//            'downloads' => new Zend_Db_Expr("downloads + 1")
//            ), array(
//                'orderdownload_id = ?' => $orderDownloadId
//              ));
                $orderDownloadItem->downloads += 1;
                $orderDownloadItem->save();
            }

            // Kill zend's ob
            $isGZIPEnabled = false;
            if (ob_get_level()) {
                $isGZIPEnabled = true;
                @ob_end_clean();
            }

            header("Content-Disposition: attachment; filename=" . @urlencode(@basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            if (empty($isGZIPEnabled)) {
                header("Content-Length: " . @filesize($path), true);
                @flush();
            }

            $fp = @fopen($path, "r");
            while (!feof($fp)) {
                echo @fread($fp, 65536);
                if (empty($isGZIPEnabled))
                    @flush();
            }
            @fclose($fp);
        }
        exit();
    }

    public function downloadSampleAction() {
        // Get path
//    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $path = $relPath = (string) APPLICATION_PATH . '/public/sitestoreproduct_product/file_' . $this->_getParam('product_id', null) . '/sample';
//    $path = $relPath = (string) $base_url . '/public/sitestoreproduct_product/file_' . $this->_getParam('product_id', null) . '/sample';

        $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $this->_getParam('downloadablefile_id', null));

        if (empty($downloadablefileItem) || $downloadablefileItem->type != 'sample')
            return $this->_forward('notfound', 'error', 'core');

        $downloadablefile_name = $downloadablefileItem->filename;

        $path = $path . '/' . $downloadablefile_name;

        $this->view->filePath = $path;
        if (true) {
            $this->view->filePath = $path;
            // Kill zend's ob
            $isGZIPEnabled = false;
            if (ob_get_level()) {
                $isGZIPEnabled = true;
                @ob_end_clean();
            }

            header("Content-Disposition: attachment; filename=" . @urlencode(@basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            if (empty($isGZIPEnabled)) {
                header("Content-Length: " . @filesize($path), true);
                @flush();
            }

            $fp = @fopen($path, "r");
            while (!feof($fp)) {
                echo @fread($fp, 65536);
                if (empty($isGZIPEnabled))
                    @flush();
            }
            @fclose($fp);
        }

        exit();
    }

    //ACTION FOR NOTIFY TO SELLER
    public function notifyToSellerAction() {

        $saveValues = array();
        $saveValues['product_id'] = $product_id = $this->_getParam('product_id', null);
        $saveValues['buyer_email'] = $sender_email = @trim($this->_getParam('buyer_email'));
        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $product->store_id);
        $storeOwnerObject = Engine_Api::_()->getItem('user', $sitestore->owner_id);

        $row = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->createRow();
        $row->setFromArray($saveValues);
        $row->save();

        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';
        $product_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $product->getHref() . '">' . $product->getTitle() . '</a>';
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($storeOwnerObject, 'sitestoreproduct_notify_to_seller', array(
            'object_name' => $store_name,
            'item_title' => $product->getTitle(),
            'item_name' => $product_name,
        ));
        $this->view->successMessage = $this->view->translate("Thank you for showing your interest in this product. You will be notified by an email, when this product becomes available.");
    }

    // AJAX JSON REQUEST ACTION, WHICH RETURN THE RESULTS FOR "ADD SHIPPING METHODS"
    public function saveshippmentAction() {

        // USE FOR EDIT FORM.
        $method_id = !empty($_POST['method_id']) ? $_POST['method_id'] : false;
        $form = new Sitestoreproduct_Form_Shipping_AddMethod();
        $formTitleArray = array(
            'title' => $this->view->translate('Title'),
            'delivery_time' => $this->view->translate('Delivery Time'),
            'price' => $this->view->translate('Price'),
            'allow_weight_from' => $this->view->translate('Weight Range From'),
            'ship_start_limit' => $this->view->translate('Method Dependency From')
        );
        $getErrors = $values = $address = array();
        @parse_str($_POST['shipping_method'], $address);
        $values = $address;

        if ($values['all_regions'] == 'yes') {
            $values['state'] = array(0);
            unset($values['all_regions']);
        }

        $errorMessage = null;
        $form->setDisableTranslator(true);
        $errorObj = $form->processAjax($address);
        $getErrors = Zend_Json::decode($errorObj);

        if (!@is_array($getErrors))
            $getErrors = array();

        $values['allow_weight_from'] = @trim($values['allow_weight_from']);
        $values['allow_weight_to'] = @trim($values['allow_weight_to']);
        $values['ship_start_limit'] = @trim($values['ship_start_limit']);
        $values['ship_end_limit'] = @trim($values['ship_end_limit']);
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.shipping.extra.content', 1)){
        if ($values['dependency'] != 1) {
            if ($values['allow_weight_from'] == '') {
                $getErrors['allow_weight_from'] = array('Please enter starting weight limit - it is required.');
            } else {
                if (!@preg_match('/^(\d+|\d*\.\d+)$/', $values['allow_weight_from'])) {
                    $getErrors['allow_weight_from'] = array('Please enter a valid weight.');
                }
            }

            if (trim($values['allow_weight_to']) != '') {
                if (!@preg_match('/^(?:\d+|\d*\.\d+)$/', $values['allow_weight_to'])) {
                    $getErrors['allow_weight_to'] = array('Please enter a valid weight.');
                }
            }
        }

        if ($values['ship_start_limit'] == '') {
            $getErrors['ship_start_limit'] = array('Please enter starting ship limit - it is required.');
        } else {
            if (!@preg_match('/^(\d+|\d*\.\d+)$/', $values['ship_start_limit'])) {
                $getErrors['ship_start_limit'] = array('Please enter a valid ship start limit.');
            }
        }

        if (trim($values['ship_end_limit']) != '') {
            if ($values['dependency'] != 2) {
                if (!@preg_match('/^(?:\d+|\d*\.\d+)$/', $values['ship_end_limit'])) {
                    $getErrors['ship_end_limit'] = array('Please enter a valid ship end limit.');
                }
            } else {
                if (!@preg_match('/^(\d+|\d*)$/', $values['ship_end_limit'])) {
                    $getErrors['ship_end_limit'] = array('Please enter a valid ship end limit.');
                }
            }
        }

        if ($values['dependency'] != 1) {
            if ($values['allow_weight_to'] != '' && $values['allow_weight_from'] > $values['allow_weight_to']) {
                $getErrors['allow_weight_to'] = array('Please enter weight range logically.');
            }
        }

        if ($values['ship_end_limit'] != '' && $values['ship_start_limit'] > $values['ship_end_limit']) {
            $getErrors['ship_end_limit'] = array('Please enter cost/quantity correct range value.');
        }
    }
        $isMinimumShippingCost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0);
        if ($isMinimumShippingCost && $values['handling_type'] == 0 && $values['price'] < Engine_Api::_()->sitestore()->getStoreMinShippingCost($_POST['store_id']) && !empty($values['status'])) {
            $getErrors['price'] = array('Entered Shipping Fee is less than the minimum shipping cost. Please enter shipping fee greator than store\'s minimum shipping cost');
        }

        $this->view->errorFlag = '0';
        //TAKING ERROR MESSAGES IN $errorMessage STRING
        if (@is_array($getErrors) && !empty($getErrors)) {
            $errorMessageStr = '';
            foreach ($getErrors as $key => $errorArray) {
                $this->view->errorFlag = '1';

                foreach ($errorArray as $errorMsg) {
                    $tempErrorTitle = !empty($formTitleArray[$key]) ? $formTitleArray[$key] : $key;
                    if ($key == 'ship_start_limit')
                        $errorMsg = $this->view->translate('Please enter starting range of Method Dependency - it is required.');

                    $errorMsg = $this->view->translate($errorMsg);
                    $errorMessageStr .= '<li>' . $tempErrorTitle . '<ul class="error"><li>' . $errorMsg . '</li></ul></li>';
                }
            }

            $this->view->errorMsgStr = $errorMessageStr;
            $this->view->successMsgStr = $this->view->translate("Shipping method has been create successfully");
            return;
        }

        //UNSET VALUE OF PRICE/RATE ACCORDING TO HANDLING TYPE
        if ($values['dependency'] == 1 && $values['handling_type'] == 2) {
            $values['ship_type'] = 1;
            $values['handling_type'] = 0;
            unset($values['rate']);
            $values['handling_fee'] = @round($values['price'], 2);
        } else {
            if ($values['handling_type'] == 1) {
                unset($values['price']);
                $values['handling_fee'] = @round($values['rate'], 2);
            } else {
                unset($values['rate']);
                $values['handling_fee'] = @round($values['price'], 2);
            }
        }

        $values['store_id'] = $_POST['store_id'];

        if ($values['country'] != 'ALL')
            $values['region'] = $values['state'];

        $values['title'] = trim($this->view->string()->stripTags($values['title']));
        $values['delivery_time'] = trim($values['delivery_time']);
        unset($values['state']);

        if (empty($method_id)) {
            // ADD NEW ROW IN TABLE ACCORDINGLY
            $shippingMethodObj = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct');
            $db = $shippingMethodObj->getAdapter();
            $db->beginTransaction();
            try {
                // CREATE SHIPPING METHOD ROW
                if (!array_key_exists("region", $values))
                    $values['region'] = array(0);
                $tempRegionIds = $values['region'];
                $values['store_id'] = $_POST['store_id'];
                foreach ($tempRegionIds as $regionId) {
                    $values['region'] = $regionId;
                    $row = $shippingMethodObj->createRow();
                    $row->setFromArray($values);
                    $row->save();
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else {
            // EDIT TABLE ACCORDINGLY
            $shippingMethodItem = Engine_Api::_()->getItem('sitestoreproduct_shippingmethod', $method_id);
            $shippingMethodItem->title = @trim($values['title']);
            $shippingMethodItem->delivery_time = $values['delivery_time'];
            if ($values['dependency'] != 1) {
                $shippingMethodItem->allow_weight_from = $values['allow_weight_from'];
                $shippingMethodItem->allow_weight_to = $values['allow_weight_to'];
            }
            $shippingMethodItem->dependency = $values['dependency'];
            $shippingMethodItem->ship_start_limit = $values['ship_start_limit'];
            $shippingMethodItem->ship_end_limit = $values['ship_end_limit'];
            if ($values['dependency'] == 2)
                $shippingMethodItem->ship_type = $values['ship_type'];

            if ($values['dependency'] == 1 && $values['handling_type'] == 2) {
                $shippingMethodItem->ship_type = 1;
                $shippingMethodItem->handling_type = 0;
                $shippingMethodItem->handling_fee = @round($values['price'], 2);
            } else {
                if ($values['dependency'] == 1)
                    $shippingMethodItem->ship_type = 0;
                $shippingMethodItem->handling_type = $values['handling_type'];
                //UPDATE VALUE OF PRICE/RATE ACCORDING TO HANDLING FEE
                if ($values['handling_type'] == 0)
                    $shippingMethodItem->handling_fee = @round($values['price'], 2);
                else
                    $shippingMethodItem->handling_fee = @round($values['rate'], 2);
            }

            if (!empty($values['status'])) {
                $shippingMethodItem->status = 1;
            } else {
                $shippingMethodItem->status = 0;
            }

            $shippingMethodItem->save();
        }
    }

    //ACTION FOR MAKE THE SITESTOREVIDEO FEATURED/UNFEATURED
    public function highlightedAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIDEO ID AND OBJECT
        $tab_selected_id = $this->_getParam('tab', null);
        $product_id = $this->view->product_id = $this->_getParam('product_id');
        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $this->view->highlighted = $productObj->highlighted;

        $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
        $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
        if (empty($tab_selected_id)) {
            $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
        }

        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
        //CHECK CAN MAKE FEATURED OR NOT(ONLY STORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
        if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            try {
                if ($productObj->highlighted == 0) {
                    $productObj->highlighted = 1;
                } else {
                    $productObj->highlighted = 0;
                }

                $productObj->save();
                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        if ($productObj->highlighted) {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made highlighted.'));
        } else {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-highlighted.'));
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $tempRedirectUrl,
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => $suc_msg
        ));
    }

    public function featuredAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIDEO ID AND OBJECT
        $tab_selected_id = $this->_getParam('tab');
        $product_id = $this->view->product_id = $this->_getParam('product_id');
        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->featured = $productObj->featured;

        //GET STORE OBJECT
        $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
        $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', false);
        if (empty($tab_selected_id)) {
            $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
        }

        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
        //CHECK CAN MAKE FEATURED OR NOT(ONLY SITESTORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
        if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            try {
                if ($productObj->featured == 0) {
                    $productObj->featured = 1;
                } else {
                    $productObj->featured = 0;
                }

                $productObj->save();
                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        if ($productObj->featured) {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made featured.'));
        } else {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-featured.'));
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $tempRedirectUrl,
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => $suc_msg
        ));
    }

    public function sponsoredAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIDEO ID AND OBJECT
        $tab_selected_id = $this->_getParam('tab');
        $product_id = $this->view->product_id = $this->_getParam('product_id');
        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $this->view->sponsored = $productObj->sponsored;

        //GET STORE OBJECT
        $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
        $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
        if (empty($tab_selected_id)) {
            $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
        }

        $this->view->canEdit = 0;
        if (!empty($isManageAdmin)) {
            $this->view->canEdit = 1;
        }
        //END MANAGE-ADMIN CHECK
        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {//NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        if (!$this->getRequest()->isPost())
            return;

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            try {
                if ($productObj->sponsored == 0) {
                    $productObj->sponsored = 1;
                } else {
                    $productObj->sponsored = 0;
                }

                $productObj->save();
                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        if ($productObj->sponsored) {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made sponsored.'));
        } else {
            $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-sponsored.'));
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $tempRedirectUrl,
            'parentRedirectTime' => '2',
            'format' => 'smoothbox',
            'messages' => $suc_msg
        ));
    }

    public function changeProductStatusAction() {
        $product_id = $this->_getParam('product_id', null);
        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        if (empty($productObj) || empty($product_id)) {
            $this->view->error = true;
            return;
        }
        $productObj->search = 1;
        $productObj->save();
        $this->view->success = true;
        return;
    }

    public function enableProductAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->product_id = $product_id = $this->_getParam('product_id', null);
        $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);


        if (!$this->getRequest()->isPost())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();
        try {
            $productObj->search = 1;
            $productObj->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 5,
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Product successfully enabled.'))
        ));
    }

    public function bundleProductAttributesAction() {
        $this->view->product_id = $product_id = $this->_getParam('product_id', null);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $otherInfoObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($product_id);
        $this->view->sitestores_view_menu = 15;

        if (empty($product_id) || empty($sitestoreproduct) || empty($otherInfoObj) || $sitestoreproduct->product_type != 'bundled')
            return;

        $mappedIds = Zend_Json_Decoder::decode($otherInfoObj->mapped_ids);
        $bundle_product_info = @unserialize($otherInfoObj->product_info);
        if (isset($bundle_product_info['bundle_product_attribute']))
            $this->view->bundle_product_attributes = $bundle_product_attributes = $bundle_product_info['bundle_product_attribute'];

        $bundleConfigProductsForm = array();

        foreach ($mappedIds as $mappedProductId) {
            $productType = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductAttribute('product_type', array('product_id' => $mappedProductId))->query()->fetchColumn();
            if ($productType == 'configurable' || $productType == 'virtual') {
                $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($mappedProductId);

                $form = new Sitestoreproduct_Form_Custom_Standard(array(
                    'item' => 'sitestoreproduct_cartproduct',
                    'topLevelId' => 1,
                    'topLevelValue' => $option_id,
                ));

                if (!empty($bundle_product_attributes[$mappedProductId]))
                    $form->populate($bundle_product_attributes[$mappedProductId]);

                $form->removeElement('submit_addtocart');
                $form->setAttrib('id', 'bundle_product_config_' . $mappedProductId);

                $bundleConfigProductsForm[$mappedProductId] = $form;
            }
        }
        $this->view->bundleConfigProductsForm = $bundleConfigProductsForm;
    }

    public function saveBundleProductAttributeAction() {
        $product_id = $this->_getParam('product_id', null);
        $bundleProductConfigurations = $this->_getParam('bundleProductConfigurations', null);

        if (empty($product_id) || empty($bundleProductConfigurations))
            return;

        @parse_str($_POST['bundleProductConfigurations'], $tempBundleProductAttribute);
        $tempIndex = 0;
        $bundleProductAttribute = array();

        foreach ($tempBundleProductAttribute as $key => $productAttribute) {
            if ($key == 'product_id_' . $tempIndex) {
                $tempIndex++;
                $tempProductId = $productAttribute;
                $bundleProductAttribute[$productAttribute] = array();
            } else {
                $bundleProductAttribute[$tempProductId] = array_merge($bundleProductAttribute[$tempProductId], array($key => $productAttribute));
            }
        }

        $tempBundleProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($product_id, "product_info");
        $bundleProductInfo = @unserialize($tempBundleProductInfo);
        $tempNewBundleProductInfo = @array_merge($bundleProductInfo, array('bundle_product_attribute' => $bundleProductAttribute));
        $newBundleProductInfo = @serialize($tempNewBundleProductInfo);

        Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->update(array('product_info' => $newBundleProductInfo), array('product_id =?' => $product_id));
    }

    public function tipsOnBuyingAction() {
        $isSitereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
        if (!empty($isSitereviewEnabled)) {
            $listingTypesTable = Engine_Api::_()->getDbtable('listingtypes', 'sitereview');
            $this->view->isBlogListingTypeExists = $listingTypesTable->fetchRow(array('visible = 1 AND title_plural = ?' => 'Blogs'));
        }
    }

    public function yourBillAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->disableLayout();
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $storeRemainingBillObj = Engine_Api::_()->getDbtable('remainingbills', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id));
        if (!empty($storeRemainingBillObj))
            $tempRemainingBillAmount = $storeRemainingBillObj->remaining_bill;
        else
            $tempRemainingBillAmount = 0;

        $paymentFailedBillAmount = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->paymentFailedBillAmount($store_id);

        // IF SEELER HAS MAKE PAYMENT AND HIS AMOUNT IS NOT SUBMMITED, THEN ADD IN REMAINING AMOUNT
        if (!empty($paymentFailedBillAmount)) {
            $remainingBillAmount = $tempRemainingBillAmount + $paymentFailedBillAmount;
            Engine_Api::_()->getDbtable('remainingbills', 'sitestoreproduct')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('store_id = ?' => $store_id));
            Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->update(array("status" => "not_paid"), array('store_id =?' => $store_id, "status != 'active'", "status != 'not_paid'"));
        } else {
            $remainingBillAmount = $tempRemainingBillAmount;
        }

        // SUBTRACT NON-PAYMENT ORDERS AMOUNT FROM STORE BILL
        $notPaidBillAmount = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->notPaidBillAmount($store_id);
        if (!empty($notPaidBillAmount) && ($remainingBillAmount >= $notPaidBillAmount)) {
            $remainingBillAmount -= round($notPaidBillAmount, 2);
            Engine_Api::_()->getDbtable('remainingbills', 'sitestoreproduct')->update(array('remaining_bill' => round($remainingBillAmount, 2)), array('store_id = ?' => $store_id));
            Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('payment_status' => 'not_paid'), array('store_id = ?' => $store_id, 'direct_payment = 1', 'non_payment_admin_reason = 1', 'order_status = 8', "payment_status != 'not_paid'"));
        }

        $this->view->paidBillAmount = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct')->totalPaidBillAmount($store_id);
        $this->view->newBillAmount = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getStoreBillAmount($store_id);
        $this->view->remainingBillAmount = round($remainingBillAmount, 2);
        $this->view->totalBillAmount = round(($this->view->remainingBillAmount + $this->view->newBillAmount), 2);

        $params = array();
        $params['store_id'] = $store_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            $params['bill_date'] = $_POST['bill_date'];
            $params['bill_min_amount'] = $_POST['bill_min_amount'];
            $params['bill_max_amount'] = $_POST['bill_max_amount'];
            $params['status'] = $_POST['status'];
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getStoreBillPaginator($params);
    }

    public function monthlyBillDetailAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->disableLayout();
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $this->view->search = $this->_getParam('search', 0);

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $params = array();
        $this->view->month = $params['month'] = $this->_getParam('month');
        $this->view->year = $params['year'] = $this->_getParam('year');

        $this->view->monthName = date("F", mktime(0, 0, 0, $params['month']));

        $params['store_id'] = $store_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getStoreMonthlyBillPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function billPaymentAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);

//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $where = "plugin = 'Payment_Plugin_Gateway_PayPal'";
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $where = "plugin = 'Payment_Plugin_Gateway_PayPal' OR plugin = 'Sitegateway_Plugin_Gateway_Stripe' OR plugin = 'Sitegateway_Plugin_Gateway_Payumoney'";
        }

        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $isPaypalEnabled = $gateway_table->select()
                ->from($gateway_table->info('name'), array('gateway_id'))
                ->where($where)
                ->where('enabled = 1')
                ->query()
                ->fetchColumn();

        if (empty($isPaypalEnabled)) {
            $this->view->noAdminGateway = true;
            return;
        }

        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

        $remainingBillTable = Engine_Api::_()->getDbtable('remainingbills', 'sitestoreproduct');
        $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');

        $remainingBillAmount = $remainingBillTable->fetchRow(array('store_id = ?' => $store_id))->remaining_bill;
        $newBillAmount = $orderTable->getStoreBillAmount($store_id);

        $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);

        $this->view->form = $form = new Sitestoreproduct_Form_BillPayment(array('totalBillAmount' => $totalBillAmount));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $form->total_bill_amount->setLabel($this->view->translate('Total Bill Amount <br /> (%s)', $currencyName));
        $form->total_bill_amount->getDecorator('Label')->setOption('escape', false);
        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        $form->bill_amount_pay->setLabel($this->view->translate('Amount to Pay <br /> (%s)', $currencyName));
        $form->bill_amount_pay->getDecorator('Label')->setOption('escape', false);
        $form->bill_amount_pay->setValue($totalBillAmount);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));
        $form->total_bill_amount->setValue($totalBillAmount);

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (round($values['bill_amount_pay'], 2) > $totalBillAmount) {
            $error = Zend_Registry::get('Zend_Translate')->_("You can't pay commission more than your total bill amount. Please enter an amount equal to or less than your total bill amount.");
            $form->addError($error);
            return;
        }

        $newRemainingBillAmount = round($totalBillAmount - $values['bill_amount_pay'], 2);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $storeBillTable = Engine_Api::_()->getDbtable('storebills', 'sitestoreproduct');
            $storeBillTable->insert(array(
                'store_id' => $store_id,
                'amount' => round($values['bill_amount_pay'], 2),
                'remaining_amount' => round($newRemainingBillAmount, 2),
                'message' => $values['message'],
                'creation_date' => new Zend_Db_Expr('NOW()'),
                'status' => 'initial',
            ));

            $storeBillId = $storeBillTable->getAdapter()->lastInsertId();

            // MANAGE REMAINING BILL AMOUNT
            $isStoreRemainingBillExist = $remainingBillTable->isStoreRemainingBillExist($store_id);
            if (empty($isStoreRemainingBillExist)) {
                $remainingBillTable->insert(array(
                    'store_id' => $store_id,
                    'remaining_bill' => $newRemainingBillAmount,
                ));
            } else {
                $remainingBillTable->update(array('remaining_bill' => $newRemainingBillAmount), array('store_id =? ' => $store_id));
            }

            //UPDATE STORE BILL ID IN ORDER TABLE
            $orderTable->update(array('storebill_id' => $storeBillId), array('store_id =? AND storebill_id = 0 AND direct_payment = 1' => $store_id));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '10',
            'parentRedirect' => $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'bill-process', 'store_id' => $store_id, 'bill_id' => $storeBillId), '', true),
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You will be redirected to make payment for your bill.'))
        ));
    }

//  public function editStoreBillAction()
//  {
//    //ONLY LOGGED IN USER CAN MANAGE
//    if (!$this->_helper->requireUser()->isValid())
//      return;
//
//    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $this->view->bill_id = $bill_id = $this->_getParam('bill_id', null);
//
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
//    
//    $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
//    $isPaypalEnabled = $gateway_table->select()
//                                     ->from($gateway_table->info('name'), array('gateway_id'))
//                                     ->where("plugin = 'Payment_Plugin_Gateway_PayPal'")
//                                     ->where('enabled = 1')
//                                     ->query()
//                                     ->fetchColumn();
//    
//    if( empty($isPaypalEnabled) ) {
//      $this->view->noAdminGateway = true;
//      return;
//    }
//
//    $storeBillObj = Engine_Api::_()->getItem('sitestoreproduct_storebill', $bill_id);
//    $remainingBillTable = Engine_Api::_()->getDbtable('remainingbills', 'sitestoreproduct');
//    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
//    
//    $remainingBillAmount = $remainingBillTable->fetchRow(array('store_id = ?' => $store_id))->remaining_bill;
//    $newBillAmount = $orderTable->getStoreBillAmount($store_id);
//    $totalBillAmount = round(($remainingBillAmount + $newBillAmount), 2);
//
//    $this->view->form = $form = new Sitestoreproduct_Form_BillPayment(array('totalBillAmount' => $totalBillAmount));
//
//    $localeObject = Zend_Registry::get('Locale');
//    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
//    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
//
//    $form->total_bill_amount->setLabel($this->view->translate('Total Bill Amount <br /> (%s)', $currencyName));
//    $form->total_bill_amount->getDecorator('Label')->setOption('escape', false);
//    $form->total_bill_amount->setAttribs(array('disabled' => 'disabled'));  
//    $form->total_bill_amount->setValue($totalBillAmount);
//
//    $form->bill_amount_pay->setLabel($this->view->translate('Amount to Pay <br /> (%s)', $currencyName));
//    $form->bill_amount_pay->getDecorator('Label')->setOption('escape', false);
//    $form->bill_amount_pay->setValue(round($storeBillObj->amount, 2));
//    
//    $form->message->setValue($storeBillObj->message);
//
//    if (!$this->getRequest()->isPost()) {
//      return;
//    }
//    
//    if( !$form->isValid($this->getRequest()->getPost()) ) {
//      return;
//    }
//    
//    $values = $form->getValues();
//    if( round($values['bill_amount_pay'], 2) > $totalBillAmount ) {
//      $error = Zend_Registry::get('Zend_Translate')->_("You can't pay more than your total bill amount. Please enter a amount equal or less than your total bill amount.");
//      $form->addError($error);
//      return;
//    }
//
//    $newRemainingBillAmount = round($totalBillAmount - $values['bill_amount_pay'], 2);
//    
//    $storeBillObj->amount = round($values['bill_amount_pay'], 2);
//    $storeBillObj->remaining_amount = round($newRemainingBillAmount, 2);
//    $storeBillObj->message = $values['message'];
//    $storeBillObj->save();
//
//    // MANAGE REMAINING BILL AMOUNT
//    $remainingBillTable->update(array('remaining_bill' => $newRemainingBillAmount), array('store_id =? ' => $store_id));
//
//    //UPDATE STORE BILL ID IN ORDER TABLE
//    $orderTable->update(array('storebill_id' => $bill_id), array('store_id =? AND storebill_id = 0 AND direct_payment = 1' => $store_id));
//
//    $this->_forward('success', 'utility', 'core', array(
//        'smoothboxClose' => true,
//        'parentRedirect' => $this->view->url(array('action' => 'store', 'store_id' => $store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'your-bill'), 'sitestore_store_dashboard', true),
//        'parentRefresh' => 10,
//        'format' => 'smoothbox',
//        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'))
//    ));
//  }

    public function billProcessAction() {
        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $store_id = $this->_getParam('store_id', null);
        $bill_id = $this->_getParam('bill_id', null);

//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->isValid())
            return;

        $this->_session = new Zend_Session_Namespace('Store_Bill_Payment_Sitestoreproduct');
        if (!empty($this->_session)) {
            $this->_session->unsetAll();
        }
        $this->_session->store_id = $store_id;
        $this->_session->bill_id = $bill_id;

        return $this->_forwardCustom('process', 'store-bill-payment', 'sitestoreproduct', array('store_id' => $store_id, 'bill_id' => $bill_id));
    }

    public function billDetailsAction() {
        $bill_id = $this->_getParam('bill_id', null);
        $store_id = $this->_getParam('store_id', null);
        $this->view->storeBillObj = Engine_Api::_()->getItem('sitestoreproduct_storebill', $bill_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $this->view->userObj = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $this->view->transaction = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->fetchRow(array('parent_order_id = ?' => $bill_id, ' 	sender_type =?' => 2, 'gateway_id = ?' => $this->view->storeBillObj->gateway_id));
    }

//    public function checkProductAvailabilityAction()
//  {
//    $product_id = $this->_getParam('product_id');
//    $product_quantity = $this->_getParam('product_quantity');
//    $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
//    
//    if( empty($product_quantity) )
//    {
//      $this->view->productAvailabilityMessage = $this->view->translate("Please enter a valid counting no.");
//      return;
//    }
//    
//    if (@preg_match('/^-?(?:\d+|\d*\.\d+)$/', $product_quantity)) 
//    {
//      if ( @is_float((float) $product_quantity) && 
//           @is_int((int) @floor($product_quantity)) && 
//           (int) @floor($product_quantity) > 0 )
//      {
////        if ($productObj->min_order_quantity > $product_quantity)
////          $this->view->productAvailabilityMessage = $this->view->translate("You must purchase at-least %s quantities of this product in a single order.", $productObj->min_order_quantity);
////        else if (!empty($productObj->max_order_quantity) && $product_quantity > $productObj->max_order_quantity)
////          $this->view->productAvailabilityMessage = $this->view->translate("You can purchase maximum %s quantities of this product in a single order.", $productObj->max_order_quantity);
////        else if (empty($productObj->stock_unlimited) && $productObj->in_stock < $product_quantity)
////          $this->view->productAvailabilityMessage = $this->view->translate("%s quantities are available for purchasing.", $productObj->max_order_quantity);
////        else
//        {
//          if( $product_quantity == 1 )
//            $this->view->productAvailabilityMessage = $this->view->translate("You can purchase 1 quantity of this product.");
//          else
//            $this->view->productAvailabilityMessage = $this->view->translate("You can purchase %s quantities of this product.", $product_quantity);
//          $this->view->canPurchaseProduct = true;
//        }
//          
//      }
//      else
//        $this->view->productAvailabilityMessage = $this->view->translate("Please enter a valid counting no.");
//    }
//    else
//      $this->view->productAvailabilityMessage = $this->view->translate("Please enter a valid counting no.");
//  }
//  
//  public function addProductInCartAction() {
//    // GET VIEWER ID
//    $viewer_id = $tempViewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
//    $product_id = $this->_getParam('product_id', null);
//    $quantity = $this->_getParam('product_quantity');
//
//    $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
//
//
//    if (empty($viewer_id)) {
//      $tempUserCart = array();
//      $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
//
//      if (empty($session->sitestoreproduct_guest_user_cart))
//        $session->sitestoreproduct_guest_user_cart = '';
//      else
//        $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
//
//      // PRODUCT IS ALREADY IN VIEWER CART OR NOT
//      if (array_key_exists($product_id, $tempUserCart)) 
//        $quantity += $tempUserCart[$product_id]['quantity'];
//
//        $tempUserCart[$product_id] = array('store_id' => $productObj->store_id, 'type' => $productObj->product_type, 'quantity' => $quantity);
//        $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
//        $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
//        
//      return;
//    } else {
//      $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
//      $cart_product_table = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
//
//      $cart_id = $cartTable->getCartId($viewer_id);
//
//      if (empty($cart_id)) {
//        $row = $cartTable->createRow();
//        $row->setFromArray(array('owner_id' => $viewer_id));
//        $cart_id = $row->save();
//
//        $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $quantity));
//        $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
//      } else {
//        $cart_product_obj = $cart_product_table->fetchRow(array('cart_id = ?' => $cart_id, 'product_id =?' => $product_id));
//
//        // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART
//        if (empty($cart_product_obj)) {
//          $cart_product_table->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $quantity));
//          $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
//        } else {
//            $cart_product_table->update(array(
//                'quantity' => new Zend_Db_Expr("quantity + $quantity"),
//                    ), array(
//                'product_id = ?' => $product_id,
//                'cart_id = ?' => $cart_id
//            ));
//            $this->view->addToCartSuccess = $this->view->translate("This Product has been successfully added to your cart.");
//
//        }
//      }
//    }
//  }
    public function multiFunctionalityAction() {
        $store_id = $this->_getParam('store_id', null);
        $checked_product = $this->_getParam('checked_product', null);
        $checkAll = $this->_getParam('checkAll', null);
        $tempAction = $this->_getParam('tempValue', null);
//    $searchParams = $this->_getParam('searchParams', null);
        $productCount = 0;

        if (!empty($checkAll)) {
            $allProductsOfStore = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
            $select = $allProductsOfStore->getSitestoreproductsSelect(array('store_id' => $store_id));
            $allProductsOfStoreObj = $allProductsOfStore->fetchAll($select);
            foreach ($allProductsOfStoreObj as $products) {
                if (!empty($products['product_id'])) {
                    $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $products['product_id']);
                    if ($tempAction == "delete") {
                        $productObj->delete();
                    } else {
                        $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
                        $db->beginTransaction();
                        try {
                            if ($tempAction == "sponsored") {
                                $productObj->sponsored = 1;
                            }
                            if ($tempAction == "unSponsored") {
                                $productObj->sponsored = 0;
                            }
                            if ($tempAction == "featured") {
                                $productObj->featured = 1;
                            }
                            if ($tempAction == "unFeatured") {
                                $productObj->featured = 0;
                            }
                            if ($tempAction == "highlighted") {
                                $productObj->highlighted = 1;
                            }
                            if ($tempAction == "unHighlighted") {
                                $productObj->highlighted = 0;
                            }
                            if ($tempAction == "enable") {
                                $productObj->search = 1;
                            }
                            if ($tempAction == "disable") {
                                $productObj->search = 0;
                            }
                            $productObj->save();
                            $db->commit();
                            $this->view->status = true;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw $e;
                        }
                    }
                }
            }
        } else {
            $selected_product = trim($checked_product, ",");
            $selected_product = str_replace("<", "", $selected_product);
            $selected_product = str_replace(">", "", $selected_product);
            $selected_product = trim($selected_product, "");
            $exploded_products = explode(",", $selected_product);
            foreach ($exploded_products as $product_id) {
                if (!empty($product_id)) {
                    $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                    if ($tempAction == "delete") {
                        $productObj->delete();
                    } else {
                        $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
                        $db->beginTransaction();
                        try {
                            if ($tempAction == "sponsored") {
                                $productObj->sponsored = 1;
                            }
                            if ($tempAction == "unSponsored") {
                                $productObj->sponsored = 0;
                            }
                            if ($tempAction == "featured") {
                                $productObj->featured = 1;
                            }
                            if ($tempAction == "unFeatured") {
                                $productObj->featured = 0;
                            }
                            if ($tempAction == "highlighted") {
                                $productObj->highlighted = 1;
                            }
                            if ($tempAction == "unHighlighted") {
                                $productObj->highlighted = 0;
                            }
                            if ($tempAction == "enable") {
                                $productObj->search = 1;
                            }
                            if ($tempAction == "disable") {
                                $productObj->search = 0;
                            }

                            $productObj->save();
                            $db->commit();
                        } catch (Exception $e) {
                            $db->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }
        $this->view->status = true;
    }

    public function uploadAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $photo = $_FILES['image'];

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }

        $randomValue = @rand();
        $name = basename($_FILES['image']['name']);
        $name = $randomValue . $name;

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/temporary';
        $photoName = $this->view->baseUrl() . '/public/temporary/' . $name;

        @chmod($path, 0777);

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(1500, 1500)
                ->write($path . '/' . $name)
                ->destroy();
        ?>
        <input type="checkbox" style="display: block" checked="checked" id="temp_image_file_path-<?php echo APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/temporary/' . $name; ?>" name="temp_image_file_path[]" id="" value="<?php echo APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/temporary/' . $name; ?>" onClick ="getId(this);"/>

        <div id="temp_image_file_path-<?php echo APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/temporary/' . $name . "image"; ?>"class='photoThumb'>
        <?php
        echo '<img  width="80" height="70" src="' . $photoName . '" />';
        ?>
        </div>
            <?php
        }

        //FUNCTION TO SET THE MINIMUM SHIPPING COST
        public function setMinimumShippingCostAction() {

            //CHECK USER VALIDATION
            if (!$this->_helper->requireUser()->isValid())
                return;

            $this->view->store_id = $store_id = $this->_getParam('store_id', null);

            if (empty($store_id))
                return;

            if ($this->getRequest()->isPost()) {

                if (isset($_POST['store_id']) && isset($_POST['minimum_shipping_cost'])) {
                    if (is_numeric($_POST['minimum_shipping_cost']) && ($_POST['minimum_shipping_cost'] >= 0)) {
                        $this->view->showMessage = false;
                        $params = array('store_id' => $_POST['store_id'], 'minimum_shipping_cost' => $_POST['minimum_shipping_cost']);

                        Engine_Api::_()->getDbtable('stores', 'sitestore')->setMinShippingCost($store_id, $_POST['minimum_shipping_cost']);
                        Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->toggleStoreShippingMethods($params);

                        $this->_forward('success', 'utility', 'core', array(
                            'smoothboxClose' => 10,
                            'parentRefresh' => 10,
                            'messages' => array(Zend_Registry::get('Zend_Translate')->_('changes saved successfully.'))
                        ));
                    } else {
                        $this->view->showMessage = true;
                    }
                }
            }
        }

        public function getProductSellingPriceAction() {

            $store_id = $this->_getParam('store_id', null);
            $price = $this->_getParam('price', null);
            $special_vat = $this->_getParam('special_vat', null);
            $discount_value = $this->_getParam('discount_value', null);
            $discount_type = $this->_getParam('discount_type', null);
            $is_discount = $this->_getParam('is_discount', false);

            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0))
                return;

            $productTaxProduct = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
            $productTaxRateProduct = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
            $values = array();
            $productPrice = $price;
            $tempDiscountedAmount = $discount_value;
            $appliedVATPrice = 0;
            $discountAllowedOnProduct = $is_discount;
            $save_price_with_vat = $show_price_with_vat = $isConfigProduct = false;
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

            // FINDOUT THE APPLIED VAT ON PRODUCT.
            $storeVatDetail = $productTaxProduct->fetchRow(array('store_id = ?' => $store_id, 'is_vat = ?' => 1));
            if (!empty($storeVatDetail)) {
                $vatTitle = $storeVatDetail->title;
                $vatId = $storeVatDetail->tax_id;

                //IF PRODUCT HAS SPECIAL VAT, PRIORITY IS GIVEN TO SPECIAL VAT      
                if (!empty($special_vat)) {
                    $tempVATvalues = $special_vat;
                    $storeVatRateDetail = true;
                    $isFixed = 1;
                } else {
                    $storeVatRateDetail = $productTaxRateProduct->fetchRow(array('tax_id = ?' => $vatId));
                    if (!empty($storeVatRateDetail)) {
                        $tempVATvalues = $storeVatRateDetail->tax_value;
                        $isFixed = $storeVatRateDetail->handling_type;
                    }
                }

                if (empty($isFixed))
                    $appliedVATPrice = @round($tempVATvalues, 2);
                else
                    $appliedVATPrice = @round($tempVATvalues * ($productPrice - $tempDiscountedAmount) / 100, 2);

                $save_price_with_vat = $storeVatDetail->save_price_with_vat;
                $show_price_with_vat = $storeVatDetail->show_price_with_vat;
            }

            /*
             * $save_price_with_vat = 0: Admin assume that VAT and DISCOUNT also added with product price.
             * $save_price_with_vat = 1: VAT and Product Price both are differently set by Admin.
             * 
             * $show_price_with_vat = 0: Show Product Price with VAT in Frontend.
             * $show_price_with_vat = 1: Show Product Price without VAT.
             * 
             */

            if (empty($show_price_with_vat)) {
                //Show Product Price with VAT in Frontend.
                if (empty($save_price_with_vat)) {
                    //Admin assume that VAT and DISCOUNT also added with product price.
                    $tempProductPrice = !empty($isFixed) ? ($productPrice * 100) / (100 + $tempVATvalues) : ($productPrice - $tempVATvalues);

                    $tempProductPrice = ($tempProductPrice > 0) ? $tempProductPrice : 0;

                    // FIND OUT THE DISCOUNT ON THE PRODUCT AFTER REMOVING THE VAT.
                    $tempDiscountedAmount = 0;
                    if (!empty($discountAllowedOnProduct)) {
                        if (empty($discount_type)) {
                            $tempDiscountedAmount = $discount_value;
                        } else {
                            $tempDiscountedAmount = ($tempProductPrice * $discount_value) / 100;
                        }
                    }

                    if ($tempDiscountedAmount > $tempProductPrice)
                        $tempProductPrice = 0;

                    $tempProductPrice = ($tempProductPrice < 0) ? 0 : $tempProductPrice;

                    $values['product_price'] = @round($tempProductPrice, 2);
                    $values['product_price_after_discount'] = empty($tempProductPrice) ? 0 : ($tempProductPrice - $tempDiscountedAmount);
                    $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];

                    $values['discount'] = empty($tempProductPrice) ? 0 : @round($tempDiscountedAmount, 2);

                    if (!empty($storeVatRateDetail)) {
                        if (empty($isFixed)) {
                            $tempAppliedVATPrice = @round($tempVATvalues, 2);
                        } else {
                            $tempAppliedVATPrice = @round(($tempVATvalues * $values['product_price_after_discount']) / 100, 2);
                        }
                    }

                    $values['vat'] = empty($tempAppliedVATPrice) ? 0 : $tempAppliedVATPrice;
                    $tempDisplayProductPrice = $values['product_price_after_discount'] + $values['vat'];
                    $values['display_product_price'] = empty($tempDisplayProductPrice) ? 0 : @round(($values['product_price_after_discount'] + $values['vat']), 2);
                } else {
                    //VAT and Product Price both are differently set by Admin.
                    $values['product_price'] = @round($productPrice, 2);
                    $values['product_price_after_discount'] = @round(($productPrice - $tempDiscountedAmount), 2);
                    $values['display_product_price'] = @round(($values['product_price_after_discount'] + $appliedVATPrice), 2);
                }
            } else {
                //Show Product Price without VAT.
                if (empty($save_price_with_vat)) {
                    //Admin assume that VAT and DISCOUNT also added with product price.
                    $tempProductPrice = !empty($isFixed) ? ($productPrice * 100) / (100 + $tempVATvalues) : ($productPrice - $tempVATvalues);
                    $tempProductPrice = ($tempProductPrice > 0) ? $tempProductPrice : 0;
                    // FIND OUT THE DISCOUNT ON THE PRODUCT AFTER REMOVING THE VAT.
                    $tempDiscountedAmount = 0;
                    if (!empty($discountAllowedOnProduct)) {
                        if (empty($discount_type)) {
                            $tempDiscountedAmount = $discount_value;
                        } else {
                            $tempDiscountedAmount = ($tempProductPrice * $discount_value) / 100;
                        }
                    }

                    if ($tempDiscountedAmount > $tempProductPrice)
                        $tempProductPrice = 0;

                    $tempProductPrice = ($tempProductPrice < 0) ? 0 : $tempProductPrice;

                    $values['product_price'] = empty($tempProductPrice) ? 0 : @round($tempProductPrice, 2);
                    $values['product_price_after_discount'] = empty($tempProductPrice) ? 0 : ($tempProductPrice - $tempDiscountedAmount);
                    $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];
                    $values['display_product_price'] = empty($tempProductPrice) ? 0 : @round($values['product_price_after_discount'], 2);
                }else {
                    //VAT and Product Price both are differently set by Admin.
                    $values['product_price'] = @round($productPrice, 2);
                    $values['product_price_after_discount'] = @round(($productPrice - $tempDiscountedAmount), 2);
                    $values['display_product_price'] = @round($values['product_price_after_discount'], 2);
                }
            }

            $this->view->value = $values['display_product_price'];
        }

        //FUNCTION FOR SHOWING THE PRODUCT SPECIFICATIONS
        public function showProductSpecificationsAction() {

            $product_id = $this->_getParam('product_id', null);

            if (empty($product_id))
                $this->view->showMessage = true;
            else {
                $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                if (empty($sitestoreproduct->profile_type)) {
                    $this->view->showMessage = true;
                    return;
                }

                $this->view->showContent = true;
                $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
                $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestoreproduct);
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                $this->view->otherDetails = $view->fieldValueLoop($sitestoreproduct, $this->view->fieldStructure);
                if (empty($this->view->otherDetails)) {
                    $this->view->showMessage = true;
                    return;
                }
            }
        }

        public function highlightedMobileAction() {

            //CHECK USER VALIDATION
            if (!$this->_helper->requireUser()->isValid())
                return;

            //GET VIDEO ID AND OBJECT
            $tab_selected_id = $this->_getParam('tab', null);
            $product_id = $this->view->product_id = $this->_getParam('product_id');
            $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

            $this->view->highlighted = $productObj->highlighted;

            $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);
            $this->view->store_id = $store_id = $productObj->store_id;

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
            $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
            if (empty($tab_selected_id)) {
                $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
            }

            $this->view->canEdit = 0;
            if (!empty($isManageAdmin)) {
                $this->view->canEdit = 1;
            }
            //END MANAGE-ADMIN CHECK

            if (!$this->getRequest()->isPost())
                return;

            //GET VIEWER INFORMATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
            //CHECK CAN MAKE FEATURED OR NOT(ONLY STORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
            if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
                $this->view->permission = true;
                $this->view->success = false;
                $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
                $db->beginTransaction();
                try {
                    if ($productObj->highlighted == 0) {
                        $productObj->highlighted = 1;
                    } else {
                        $productObj->highlighted = 0;
                    }

                    $productObj->save();
                    $db->commit();
                    $this->view->success = true;
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->view->permission = false;
            }

            if ($productObj->highlighted) {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made highlighted.'));
            } else {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-highlighted.'));
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'product', 'action' => 'manage', 'store_id' => $store_id), 'sitestoreproduct_extended', false);
        }

        public function featuredMobileAction() {

            //CHECK USER VALIDATION
            if (!$this->_helper->requireUser()->isValid())
                return;

            //GET VIDEO ID AND OBJECT
            $tab_selected_id = $this->_getParam('tab');
            $product_id = $this->view->product_id = $this->_getParam('product_id');
            $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $this->view->featured = $productObj->featured;

            //GET STORE OBJECT
            $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);
            $this->view->store_id = $store_id = $productObj->store_id;

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
            $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', false);
            if (empty($tab_selected_id)) {
                $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
            }

            $this->view->canEdit = 0;
            if (!empty($isManageAdmin)) {
                $this->view->canEdit = 1;
            }
            //END MANAGE-ADMIN CHECK

            if (!$this->getRequest()->isPost())
                return;

            //GET VIEWER INFORMATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
            //CHECK CAN MAKE FEATURED OR NOT(ONLY SITESTORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
            if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
                $this->view->permission = true;
                $this->view->success = false;
                $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
                $db->beginTransaction();
                try {
                    if ($productObj->featured == 0) {
                        $productObj->featured = 1;
                    } else {
                        $productObj->featured = 0;
                    }

                    $productObj->save();
                    $db->commit();
                    $this->view->success = true;
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->view->permission = false;
            }

            if ($productObj->featured) {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made featured.'));
            } else {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-featured.'));
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'product', 'action' => 'manage', 'store_id' => $store_id), 'sitestoreproduct_extended', false);
        }

        public function sponsoredMobileAction() {

            //CHECK USER VALIDATION
            if (!$this->_helper->requireUser()->isValid())
                return;

            //GET VIDEO ID AND OBJECT
            $tab_selected_id = $this->_getParam('tab');
            $product_id = $this->view->product_id = $this->_getParam('product_id');
            $productObj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

            $this->view->sponsored = $productObj->sponsored;

            //GET STORE OBJECT
            $this->view->store = $store = Engine_Api::_()->getItem('sitestore_store', $productObj->store_id);
            $this->view->store_id = $store_id = $productObj->store_id;

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
            $tempRedirectUrl = $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($productObj->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
            if (empty($tab_selected_id)) {
                $tempRedirectUrl = $this->view->url(array(), 'default', false) . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores") . '/dashboard/store/' . $productObj->store_id . '/product/62/manage';
            }

            $this->view->canEdit = 0;
            if (!empty($isManageAdmin)) {
                $this->view->canEdit = 1;
            }
            //END MANAGE-ADMIN CHECK

            if (!$this->getRequest()->isPost())
                return;

            //GET VIEWER INFORMATION
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            if ($viewer_id == $productObj->owner_id || !empty($this->view->canEdit)) {
                $this->view->permission = true;
                $this->view->success = false;
                $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
                $db->beginTransaction();
                try {
                    if ($productObj->sponsored == 0) {
                        $productObj->sponsored = 1;
                    } else {
                        $productObj->sponsored = 0;
                    }

                    $productObj->save();
                    $db->commit();
                    $this->view->success = true;
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->view->permission = false;
            }

            if ($productObj->sponsored) {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made sponsored.'));
            } else {
                $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Product successfully made un-sponsored.'));
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'product', 'action' => 'manage', 'store_id' => $store_id), 'sitestoreproduct_extended', false);
        }

    }
    
