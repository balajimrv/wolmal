<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_CartController extends Siteapi_Controller_Action_Standard {

    public function init() {
        
    }

    /*
     * view cart
     */

    public function viewAction() {

        try {

            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            // Validate request methods
            $this->validateRequestMethod();

            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            $params = $this->_getAllParams();

            $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
            $storeTable = Engine_Api::_()->getDbTable('stores', 'sitestore');
            $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
            $userCartId = $cartTable->getCartId($viewer_id);

            $isPaymentToSiteEnable = true;

            $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

            $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

            $isDownPaymentCouponEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.coupon', 0);

            // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
            if (empty($isPaymentToSiteEnable) && !empty($isDownPaymentEnable)) {
                $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($userCartId);
                $product_ids = implode(",", $productIds);
                $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
            } elseif (!empty($isDownPaymentEnable)) {
                $cartProductPaymentType = true;
            }

            $canApplyCoupon = 1;
            if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
                $canApplyCoupon = 0;

                if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType) && empty($isDownPaymentCouponEnable))
                    $canApplyCoupon = 0;
            }

            // cart products
            if ($viewer_id) {
                if (!$userCartId)
                    $this->successResponseNoContent("no_content");
                $cartProductIds = $cartProductsTable->getCartProductIds($userCartId);
                $cartProductIdsString = implode(',', $cartProductIds);
            }

            $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);

            $productsTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
            $productsTableName = $productsTable->info('name');

            // get distinct store ids
            if ($viewer_id) {
                $cartProductsselect = $cartProductsTable->select()
                        ->from($cartProductsTable->info('name'), array('product_id'))
                        ->where('cart_id = ?', $userCartId);

                $cartProducts = $cartProductsselect->query()->fetchAll();
                if (empty($cartProducts))
                    $this->successResponseNoContent("no_content");

                $select = $productsTable->select()
                        ->from($productsTableName, array('store_id'))
                        ->distinct()
                        ->where("$productsTableName.product_id in (?)", $cartProductsselect->query()->fetchAll());

                $store_ids = $select->query()->fetchAll();
            }
            else {
                $productsData = $params['productsData'];

                if (!isset($productsData) || empty($productsData))
                    $this->respondWithValidationError('parameter_missing', 'productsData missing');

                $productsArray = $cartProducts = Zend_Json::decode(urldecode($productsData));

                if (empty($productsArray))
                    $this->successResponseNoContent("no_content");

                $productsids = array();

                foreach ($productsArray as $row => $value) {
                    $productidsstring .= ",'" . $value['product_id'] . "'";
                    $productsids[] = $value['product_id'];
                    if ($value['configFields']) {
                        foreach ($value['configFields'] as $subrow => $subvalue)
                            if (is_string($subvalue) && strpos($subvalue, ',') !== false)
                                $productsArray[$row]['configFields'][$subrow] = explode(',', $subvalue);
                    }
                }

                $select = $productsTable->select()
                        ->from($productsTableName, array('store_id'))
                        ->distinct()
                        ->where("$productsTableName.product_id in (?)", $productsids);

                $store_ids = $select->query()->fetchAll();
            }

            $response = array();
            $response['stores'] = array();
            $response['directPayment'] = $directPayment;
            $response['totalProductsQuantity'] = 0;
            $response['totalProductsCount'] = 0;
            $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

            if (!$directPayment) {
                $response['grandTotal'] = 0.00;
                if ($canApplyCoupon)
                    $response['canApplyCoupon'] = $canApplyCoupon;
            }

            $productsCount = array();
            foreach ($store_ids as $key => $value) {
                $store = Engine_Api::_()->getItem('sitestore_store', $value['store_id']);
                $response['stores'][$value['store_id']]['name'] = $store->getTitle();
                $response['stores'][$value['store_id']]['link'] = "http://" . $_SERVER['HTTP_HOST'] . $store->getHref();
                $response['stores'][$value['store_id']]['totalProductsCount'] = floatval(0);
                $response['stores'][$value['store_id']]['totalProductsQuantity'] = 0;

                $response['stores'][$value['store_id']]['subTotal'] = floatval(0);
                $response['stores'][$value['store_id']]['total'] = floatval(0);

                if ($isVatAllow)
                    $response['stores'][$value['store_id']]['totalVat'] = floatval(0);

                if ($directPayment && $canApplyCoupon)
                    $response['stores'][$value['store_id']]['canApplyCoupon'] = $canApplyCoupon;

                $temp_store_id = $value['store_id'];
                $manage_cart_store_name['stores'][$value['store_id']] = $storeTable->getStoreName($value['store_id']);
                $sellingAllowedProducts['stores'][$value['store_id']] = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($value['store_id']);
                $storeOnlineThresholdAmount['stores'][$value['store_id']] = Engine_Api::_()->sitestoreproduct()->getOnlinePaymentThreshold($value['store_id']);
            }

            // Cartproducts api response
            if ($viewer_id)
                $cartProducts = $cartProductsTable->getCart($userCartId);

            foreach ($cartProducts as $row => $value) {
                $vatTax = 0;
                $admin_tax = 0;
                $admin_tax_array = array();
                $product = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);

                $data = array();
                $data['title'] = $product->title;

                if ($viewer_id)
                    $data['cartproduct_id'] = $value['cartproduct_id'];

                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product));
                $data['quantity'] = $value['quantity'];
                $data['product_type'] = $product->product_type;
                $data['product_id'] = $product->getIdentity();

                if (!empty($isVatAllow)) {
                    if ($product->product_type == 'configurable' || $product->product_type == 'virtual') {
                        if ($viewer_id)
                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product, null, $value['cartproduct_id']);
                        else
                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                    } else {
                        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                    }
                    $vatTax = $productPricesArray['vat'] * $value['quantity'];
                    $data['unitVat'] = $productPricesArray['vat'];
                    $data['vat'] = $vatTax;
                    $data['show_msg'] = $productPricesArray['show_msg'];
                    $data['show_price_with_vat'] = $productPricesArray['show_price_with_vat'];
                    $data['save_price_with_vat'] = $productPricesArray['save_price_with_vat'];
                    $productDiscountedPrice = $productPricesArray["display_product_price"];
                } else {
                    if ($product->product_type == 'configurable' || $product->product_type == 'virtual') {
                        if ($viewer_id) {
                            $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value['cartproduct_id']);
                            $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                            $valueRows = $values->getRowsMatching(array(
                                'item_id' => $cartProductObject->getIdentity(),
                            ));

                            $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product->product_id, array('price', 'price_increment'), $valueRows);
                        } elseif (isset($value['configFields']) && !empty($value['configFields'])) {
                            $configuration_price = $this->getConfigPrice($value['configFields']);
                        } else
                            $configuration_price = 0.00;

                        $productDiscountedPrice = $configuration_price + $productsTable->getProductDiscountedPrice($product->product_id);
                    } else {
                        $productDiscountedPrice = $productsTable->getProductDiscountedPrice($product->product_id);
                    }
                }

                $data['price'] = $productDiscountedPrice * $value['quantity'];

                $data['unitPrice'] = $productDiscountedPrice;

                if ($isVatAllow) {
                    $response['stores'][$product->store_id]['totalVat'] += floatval($data['vat']);
                    if (($data['show_price_with_vat'] == true && $data['save_price_with_vat'] == true ) || ($data['show_price_with_vat'] == true && $data['save_price_with_vat'] == false )) {
                        $response['stores'][$product->store_id]['total'] += floatval($data['vat']);
                        $response['stores'][$product->store_id]['subTotal'] += floatval($data['price']);
                        $response['stores'][$product->store_id]['total'] += floatval($data['price']);
                    } else {
                        $response['stores'][$product->store_id]['subTotal'] += floatval($data['price']) - floatval($data['vat']);
                        $response['stores'][$product->store_id]['total'] += floatval($data['price']);
                    }
                } else {
                    $response['stores'][$product->store_id]['subTotal'] += floatval($data['price']);
                    $response['stores'][$product->store_id]['total'] += floatval($data['price']);
                }

                if (!$directPayment) {

                    if (isset($response['totalAmountFields']) && !empty($response['totalAmountFields'])) {
                        if ($isVatAllow)
                            $response['totalVatFields'] = array_merge($response['totalVatFields'], $this->_getTotalVatFields($response['stores'][$product->store_id]));
                        $response['totalAmountFields'] = array_merge($response['totalAmountFields'], $this->_getTotalAmountFields($response['stores'][$product->store_id]));
                    }
                    else {
                        if ($isVatAllow)
                            $response['totalVatFields'] = $this->_getTotalVatFields($response['stores'][$product->store_id]);
                        $response['totalAmountFields'] = $this->_getTotalAmountFields($response['stores'][$product->store_id]);
                    }
                } else {
                    if ($isVatAllow) {
                        $totalVatFields = $this->_getTotalVatFields($response['stores'][$product->store_id], 1);
                        if (isset($totalVatFields) && !empty($totalVatFields))
                            $response['stores'][$product->store_id]['totalVatFields'] = $totalVatFields;
                    }
                    $totalAmountFields = $this->_getTotalAmountFields($response['stores'][$product->store_id], 1);
                    if (isset($totalAmountFields) && !empty($totalAmountFields))
                        $response['stores'][$product->store_id]['totalAmountFields'] = $totalAmountFields;
                }

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                    $closed = !empty($product->closed);
                } else {
                    $closed = 0;
                }

                $quantity = $value['quantity'];

                $error = "";

                if (empty($sellingAllowedProducts['stores'][$product->store_id]) || empty($product->allow_purchase)) {
                    $error = $this->translate("It is a non purchasable product.");
                } elseif (empty($product->stock_unlimited) && empty($product->in_stock)) {
                    $error = $this->translate("Not available for purchase.");
                } elseif (empty($product->stock_unlimited) && $quantity > $product->in_stock) {
                    $error = $this->translate("Only " . $product->in_stock . " quantities of this product are available in stock.");
                } elseif (!empty($product->max_order_quantity) && $quantity > $product->max_order_quantity) {
                    $error = $this->translate("You can purchase maximum " . $product->max_order_quantity . " quantities of this product in a single order.");
                } elseif (!empty($product->min_order_quantity) && $quantity < $product->min_order_quantity)
                    $error = $this->translate("To order this product, you must add at-least " . $product->min_order_quantity . " quantities of it to your cart.");
                elseif ($closed || !empty($product->draft) || empty($product->search) || empty($product->approved) || $product->start_date > date('Y-m-d H:i:s') || ($product->end_date < date('Y-m-d H:i:s') && !empty($product->end_date_enable))) {
                    $error = $this->translate("Not available for purchase.");
                } elseif ($product->product_type == 'configurable' || $product->product_type == 'virtual') {
                    $error = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product->product_id, array('quantity', 'quantity_unlimited'), $value['cartproduct_id'], $value['quantity']);
                }

                // check payment errors
                if ($directPayment) {
                    if (empty($isPaymentToSiteEnable)) {
                        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                        $enable_gateway = $gateway_table->select()
                                ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                                ->where('enabled = 1')
                                ->query()
                                ->fetchAll();

                        if (empty($enable_gateway)) {
                            $error = $this->translate("No Admin payment gateway available .");
                        }
                    } else {
                        $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($product->store_id, 'store_gateway');
                        if (empty($storeEnabledgateway))
                            $error = $this->translate("No Store payment gateway available");
                    }
                }
                else {
                    if (empty($isPaymentToSiteEnable)) {
                        $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($product->store_id, 'store_gateway');
                        if (empty($storeEnabledgateway))
                            $error = $this->translate("No Store payment gateway available");
                    }
                    else {
                        $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                        $enable_gateway = $gateway_table->select()
                                ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                                ->where('enabled = 1')
                                ->query()
                                ->fetchAll();

                        if (empty($enable_gateway))
                            $error = $this->translate("No Admin payment gateway available");
                    }
                }

                if ($error)
                    $data['error'] = $this->translate($error);

                // get products configuration
                if ($product->product_type == 'virtual' || $product->product_type == 'configurable') {
                    $cartProductFieldMeta = Engine_Api::_()->getDbTable('CartproductFieldMeta', 'sitestoreproduct');
                    $cartProductFieldOptions = Engine_Api::_()->getDbTable('CartproductFieldOptions', 'sitestoreproduct');
                    if ($viewer_id) {
                        $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value['cartproduct_id']);
                        $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                        if ($values->count()) {
                            $data['configuration'] = array();
                            foreach ($values as $fieldValue) {
                                $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($fieldValue->field_id));
                                $fieldValueLabel = $fieldValue->value;
                                if (is_numeric($fieldValue->value))
                                    $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($fieldValue->field_id, $fieldValue->value));
                                if (isset($data['configuration'][$fieldLabel]) && !empty($data['configuration'][$fieldLabel]))
                                    $data['configuration'][$fieldLabel] .= " , " . $fieldValueLabel;
                                else
                                    $data['configuration'][$fieldLabel] = $fieldValueLabel;
                            }
                        }
                    } else {
                        if (isset($value['configFields']) && !empty($value['configFields'])) {
                            $data['configFields'] = $value['configFields'];
                            foreach ($value['configFields'] as $fieldname => $fieldvalue) {
                                if ($fieldname == 'combination_id') {
                                    continue;
                                }

                                $key = explode('_', $fieldname);
                                if (count($key) == 2) {
                                    $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($key[1]));
                                    $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($key[1], $fieldvalue));
                                    $data['configuration'][$fieldLabel] = $fieldValueLabel;
                                } elseif (count($key) == 3) {
                                    if (!is_array($fieldvalue)) {
                                        $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($key[2]));
                                        $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($key[2], $fieldvalue));
                                        $data['configuration'][$fieldLabel] = $fieldValueLabel;
                                    } else {
                                        foreach ($fieldvalue as $subfieldrow => $subfieldvalue) {
                                            $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($key[2]));
                                            $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($key[2], $subfieldvalue));
                                            if (isset($data['configuration'][$fieldLabel]) && !empty($data['configuration'][$fieldLabel]))
                                                $data['configuration'][$fieldLabel] .= " , " . $fieldValueLabel;
                                            else
                                                $data['configuration'][$fieldLabel] = $fieldValueLabel;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                ++$response['stores'][$product->store_id]['totalProductsCount'];
                ++$response['totalProductsCount'];

                $response['stores'][$product->store_id]['totalProductsQuantity'] += $value['quantity'];
                $response['totalProductsQuantity'] += $value["quantity"];

                $response['stores'][$product->store_id]['products'][] = $data;
            }

            if ($directPayment) {
                foreach ($params as $key => $value) {
                    if (strpos($key, "coupon_code_") !== false) {
                        // coupon_code_5 eg: will never have a comma separated value
                        $keyexplode = explode("_", $key);
                        $couponArray[$keyexplode[2]] = $value;
                    }
                }
            }

            if (isset($couponArray) && !empty($couponArray)) {
                $couponTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
                $couponTableName = $couponTable->info('name');

                foreach ($couponArray as $row => $value) {

                    $select = $couponTable->select()
                            ->from($couponTableName)
                            ->where("coupon_code = ?", $value);

                    $coupon = $select->query()->fetchALL();

                    if ($coupon[0]['store_id'] == $row) {
                        if (!isset($params['coupon_code']) || empty($params['coupon_code']))
                            $params['coupon_code'] = $value;
                        else
                            $params['coupon_code'] .= "," . $value;
                    }
                    else {
                        $response['stores'][$row]['couponerror'] = $this->translate("Please enter a different coupon code as " . $value . " is either invalid or expired");
                    }
                }
            }

            // COUPON CODE WORK HERE
            if (isset($params['coupon_code']) && !empty($params['coupon_code'])) {
                $coupon_code = $params['coupon_code'];
                $couponCodeArray = explode(',', $coupon_code);
                $couponTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
                $couponTableName = $couponTable->info('name');
                foreach ($couponCodeArray as $row => $value) {
                    $select = $couponTable->select()
                            ->from($couponTableName)
                            ->where("coupon_code = ?", $value);

                    $coupon = $select->query()->fetchALL();

                    $error = "";

                    if (!$coupon) {
                        $response['couponerror'] = $this->translate("Please enter a different coupon code as " . $value . " is either invalid or expired");
                        continue;
                    }



                    if (!isset($response['stores'][$coupon[0]['store_id']]) || empty($response['stores'][$coupon[0]['store_id']])) {
                        $response['couponerror'] = $this->translate("Their is no product of the store which has this offer");
                        continue;
                    } else {
                        if (($coupon[0]['claim_count'] >= 0 && $coupon[0]['claim_count'] - $coupon[0]['claimed'] <= 0 ) || (!$coupon[0]['status'])) {
                            $error = $this->translate("Please enter a different coupon code as " . $value . " is either invalid or expired");
                        }

                        if (($coupon[0]['end_time'] != "0000-00-00 00:00:00") && strtotime($coupon[0]['end_time']) < strtotime(date('Y-m-d H:i:s'))) {
                            $error = $this->translate("Please enter a different coupon code as " . $value . " is either invalid or expired");
                        }

                        if ($response['stores'][$coupon[0]['store_id']]['subTotal'] < $coupon[0]['minimum_purchase']) {
                            $error = $this->translate("Cart total Amount should be atleast " . $coupon[0]['minimum_purchase']);
                        }

                        if ($response['stores'][$coupon[0]['store_id']]['totalProductsQuantity'] < $coupon[0]['min_product_quantity']) {
                            $error = $this->translate("Cart total product count should be atleast " . $coupon[0]['min_product_quantity']);
                        }

                        $tobeDiscountedAmount = 0.00;
                        if ($coupon[0]['product_ids']) {
                            $productIds = explode(",", $coupon[0]['product_ids']);
                            foreach ($response['stores'][$coupon[0]['store_id']]['products'] as $subkey => $productData) {
                                if (in_array($productData['product_id'], $productIds)) {
                                    $tobeDiscountedAmount += $productData['price'];
                                    if ($productData['vat'])
                                        $tobeDiscountedAmount -= $productData['vat'];
                                }
                            }
                        }

                        if (strlen($error) == 0) {
                            $discount_type = $coupon[0]['discount_type'];
                            $discount_amount = $coupon[0]['discount_amount'];
                            $couponData = array();

                            $amount = $response['stores'][$coupon[0]['store_id']]['total'];

                            if ($discount_type) {
                                $couponData = array('name' => $this->translate($coupon[0]['title']), 'coupon_code' => $coupon[0]['coupon_code'], 'value' => $discount_amount);
                            } else {
                                if (!$tobeDiscountedAmount)
                                    $tobeDiscountedAmount = $response['stores'][$coupon[0]['store_id']]['subTotal'];
                                $discount_amount = floatval($tobeDiscountedAmount / 100) * floatval($discount_amount);
                                $couponData = array('name' => $this->translate($coupon[0]['title']), 'coupon_code' => $coupon[0]['coupon_code'], 'value' => $discount_amount);
                            }

                            if (isset($response['stores'][$coupon[0]['store_id']]['total']) && !empty($response['stores'][$coupon[0]['store_id']]['total']) && isset($discount_amount))
                                $response['stores'][$coupon[0]['store_id']]['total'] = $amount - $discount_amount;
                            else
                                $error = $this->translate("No Product Of This Store Present");
                        }
                    }

                    if ($error) {
                        if (!$directPayment)
                            $response['couponerror'] = $error;
                        else
                            $response['stores'][$coupon[0]['store_id']]['couponerror'] = $error;
                    }
                    else {
                        if (!empty($coupon) && !empty($coupon[0]['store_id']) && isset($couponData) && !empty($couponData)) {
                            $response['stores'][$coupon[0]['store_id']]['coupon'] = $couponData;
                            if (!$directPayment)
                                $response['coupon'] = $couponData;
                        }
                    }
                }
            }

            if (!$directPayment) {
                $response['grandTotal'] = 0.00;
                foreach ($store_ids as $row => $store_id)
                    $response['grandTotal'] += $response['stores'][$store_id['store_id']]['total'];
            }

            $this->respondwithsuccess($response, false);
        } catch (Exception $e) {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     *  Merges the cart of login user with that of un loggined user
     */

    public function mergeAction() {
        $this->validateRequestMethod("POST");
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondwitherror('unauthorized');


        $params = $this->_getAllParams();

        if (!isset($params['products']))
            $this->respondWithValidationError('parameter_missing', 'products missing');

        $productsArray = Zend_Json::decode(urldecode($params['products']));

        $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
        $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
        $cartProductsTableName = $cartProductsTable->info('name');
        $userCartId = $cart_id = $cartTable->getCartId($viewer_id);

        if (!$userCartId) {
            $row = $cartTable->createRow();
            $row->setFromArray(array('owner_id' => $viewer_id));
            $cart = $row->save();
            $userCartId = $cart_id = $cart->getIdentity();
        }

        foreach ($productsArray as $row => $product) {

            $productObj = Engine_Api::_()->getItem("sitestoreproduct_product", $product['product_id']);

            if (!$productObj)
                continue;

            $product_id = $productObj->getIdentity();

            $cart_product_values = $cartProductsTable->getConfigurationId($productObj->getIdentity(), $cart_id);
            $cart_product_obj = null;
            if (!empty($cart_product_values) && $productObj->product_type == 'configurable') {
                if (!isset($product['config']) || empty($product['config']) || !isset($product['config']['combination_id']) || empty($product['config']['combination_id']))
                    continue;

                $configData = $product['config'];

                unset($configData['combination_id']);

                foreach ($cart_product_values as $row => $value) {
                    $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value);
                    $fieldValues = Engine_Api::_()->fields()->getFieldsValues($cartProduct);
                    $cartProductFieldValue = $fieldValues->getRowsMatching(array(
                        'item_id' => $cartProduct->getIdentity(),
                    ));
                    $fieldvalueArray = array();
                    foreach ($cartProductFieldValue as $fieldIndex => $fieldValue) {
                        $fieldValue = $fieldValue->toArray();
                        if ($fieldValue['category_attribute'])
                            $fieldvalueArray['select_' . $fieldValue['field_id']] = $fieldValue['value'];
                        else
                            $fieldvalueArray[$productObj->store_id . '_' . $productObj->getIdentity() . '_' . $fieldValue['field_id']] = $fieldValue['value'];
                    }

                    $array_diff_assoc = Engine_Api::_()->sitestoreproduct()->multidimensional_array_diff($fieldvalueArray, $configData);

                    if ($array_diff_assoc) {
                        $cart_product_obj = $cartProduct;
                        break;
                    }
                }
            } else if (!empty($cart_product_values))
                $cart_product_obj = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cart_product_values[0]);

            // (array('cart_id = ?' => $cart_id, 'product_id =?' => $product_id));
            // IF PRODUCT IS NOT IN VIEWER CART, THEN ADD IT TO CART 
            if (empty($cart_product_obj)) {
                $lastInsertId = $cartProductsTable->insert(array('cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $product['quantity']));

                if ($productObj->product_type == 'configurable') {
                    $cartProductFieldValue = Engine_Api::_()->getDbtable('CartProductFieldValues', 'sitestoreproduct');
                    foreach ($configData as $row => $value) {
                        $key = explode('_', $row);

                        if (count($key) == 2) {
                            if ($key[0] != "select")
                                continue;

                            $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[1], 'value' => $value, 'category_attribute' => 1));
                        }
                        elseif (count($key) == 3) {
                            $cartProductFieldValue->insert(array('item_id' => $lastInsertId, 'field_id' => $key[2], 'value' => $value, 'category_attribute' => 0));
                        }
                    }
                }
            } else {
                $db = $cartProductsTable->getAdapter();
                $sql = "update " . $cartProductsTable->info('name') . " set quantity=quantity+" . $product['quantity'] . " where cartproduct_id=?";
                $query = new Zend_Db_Statement_Mysqli($db, $sql);
                $query->execute(array($cart_product_obj->getIdentity()));
            }
        }

        $this->successResponseNoContent('no_content');
    }

    /*
     * Remove products of a particular store id form cart
     */

    public function removeStoreProductsAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $params = $this->_getAllParams();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!isset($params['store_id']) || empty($params['store_id']))
            $this->respondWithValidationError('parameter_missing', 'store_id missing');

        $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
        $cartProductTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
        $cartProductsItemTable = Engine_Api::_()->getItemTable('sitestoreproduct_cartproduct');
        $userCartId = $cartTable->getCartId($viewer_id);

        $storeproducts = $cartProductTable->getStoreCartProducts($userCartId, $params['store_id']);
        $storeproductIds = array();
        foreach ($storeproducts as $store => $product) {
            $cartProductObj = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $product['cartproduct_id']);
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $cartProductObj->product_id);
            if ($sitestoreproduct->product_type == 'downloadable') {
                $downloadable_product_exist = true;
                continue;
            } else {
                $storeproductIds[] = $cartProductObj->product_id;
            }
        }

        if (!$userCartId)
            $this->respondWithValidationError('validation_fail', 'no cart for this user present');
        $db = $cartProductsItemTable->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($storeproductIds as $store_id) {
                $cartProductTable->delete(array("product_id =?" => $store_id, "cart_id =?" => $userCartId));
                // COMMIT
                $db->commit();
            }
            $this->successResponseNoContent("no_content");
        } catch (Exception $e) {
            $this->respondWithValidationError('validation_fail', $e->getMessage());
        }
    }

    /*
     * Update quantity
     */

    public function updateQuantityAction() {
        // update quantity only check product is_stock as of now
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Validate request methods
        $this->validateRequestMethod("POST");

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($viewer_id) {
            $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
            $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
            $cartProductsTableName = $cartProductsTable->info('name');
            $userCartId = $cartTable->getCartId($viewer_id);

            // cart products
            if (!$userCartId)
                $this->respondwitherror('no_record');

            $cartIds = $this->_getParam('cartproduct_id');
            $quantities = $this->_getParam('quantity');
            $cartIdsArray = explode(",", $cartIds);
            $quantitiesArray = explode(",", $quantities);

            foreach ($cartIdsArray as $key => $value) {
                $updateQuantity[$value] = $quantitiesArray[$key];
            }

            if (!isset($updateQuantity) || empty($updateQuantity) || !is_array($updateQuantity))
                $this->respondWithValidationError('parameter_missing', "quantity missing");
        }
        else {
            $productsData = $this->_getParam('productsData');
            if (!isset($productsData) || empty($productsData))
                $this->respondWithValidationError('parameter_missing', 'productsData missing');
            $productsArray = $cartProducts = Zend_Json::decode(urldecode($productsData));

            $updateQuantity = array();

            foreach ($productsArray['stores'] as $row => $value) {
                if (is_numeric($row)) {
                    foreach ($value['products'] as $product_key => $product) {
                        if (isset($product['error']))
                            unset($productsArray['stores'][$row]['products'][$product_key]['error']);

                        if (isset($product['couponerror']))
                            unset($productsArray['stores'][$row]['products'][$product_key]['couponerror']);

                        $updateQuantity[$row . "_" . $product_key] = array('product_id' => $product['product_id'], 'quantity' => $product['quantity']);
                    }
                }
            }
        }

        $productQuantityArray = array();

        $errorMessage = array();

        foreach ($updateQuantity as $cartproduct_id => $quantity) {

            if ($viewer_id) {
                if (!($quantity >= 0))
                    $this->respondWithValidationError('parameter_missing', "quantity missing");

                if (!$cartproduct_id)
                    $this->respondWithValidationError('parameter_missing', "cartproduct_id missing");

                $cartproduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartproduct_id);

                if ($cartproduct->cart_id != $userCartId)
                    $this->respondWithValidationError('not_allowed', $this->translate("This product does not belong to your Cart"));
                $product = Engine_Api::_()->getItem('sitestoreproduct_product', $cartproduct->product_id);
            }
            else {
                $product = Engine_Api::_()->getItem('sitestoreproduct_product', $quantity['product_id']);
                if (isset($productQuantityArray[$quantity['product_id']]))
                    $productQuantityArray[$quantity['product_id']] += $quantity['quantity'];
                else
                    $productQuantityArray[$quantity['product_id']] = $quantity['quantity'];

                $quantity = $productQuantityArray[$quantity['product_id']];
            }

            $store_id = $product->store_id;

            $error = "";

            if (empty($product->allow_purchase))
                $error = $this->translate("It is a non purchasable product. Please delete this product to continue shopping.");
            elseif (empty($product->stock_unlimited) && empty($product->in_stock)) {
                $error = $this->translate("This product is currently not available for purchase.");
            } elseif (empty($product->stock_unlimited) && $quantity > $product->in_stock) {
                if ($product->in_stock == 1)
                    $error = $this->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.");
                else
                    $error = $this->translate("Only " . $product->in_stock . " quantities of this product are available .");
            } else if (!empty($product->max_order_quantity) && $quantity > $product->max_order_quantity) {
                if ($product->max_order_quantity == 1)
                    $error = $this->translate("You can purchase maximum 1 quantity of this product in a single order. So, please enter the quantity as 1.");
                else
                    $error = $this->translate("You can purchase maximum %s quantities of this product in a single order. So, please enter the quantity as less than or equal to %s.", $product->max_order_quantity, $product->max_order_quantity);
            }
            //else if (!empty($product->min_order_quantity) && $quantity < $product->min_order_quantity) {
            // $error = $this->translate("To order this product, you must add at-least %s quantities of it to your cart.", $product->min_order_quantity);
            else if ($closed || !empty($product->draft) || empty($product->search) || empty($product->approved) || $product->start_date > date('Y-m-d H:i:s') || ($product->end_date < date('Y-m-d H:i:s') && !empty($product->end_date_enable))) {
                $error = $this->translate("This product is currently not available for purchase.");
            }

            if ($error) {
                if ($viewer_id)
                    $errorMessage[$cartproduct_id] = $error;
                else {
                    $data = explode('_', $cartproduct_id);
                    $productsArray['stores'][$data[0]]['products'][$data[1]]['error'] = $error;
                }

                continue;
            }

            if ($viewer_id) {
                $db = $cartProductsTable->getAdapter();
                $db->beginTransaction();
                try {
                    if ($quantity) {
                        $cartproduct->quantity = $quantity;
                        $cartproduct->save();
                    } else
                        $cartproduct->delete();

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                }
            } else {
                $productsArray['stores'][$store_id]['products'][$cartproduct_id]['vat'] = $productsArray['stores'][$store_id]['products'][$cartproduct_id]['unitVat'] * $productsArray['stores'][$store_id]['products'][$cartproduct_id]['quantity'];
                $productsArray['stores'][$store_id]['products'][$cartproduct_id]['price'] = $productsArray['stores'][$store_id]['products'][$cartproduct_id]['unitPrice'] * $productsArray['stores'][$store_id]['products'][$cartproduct_id]['quantity'];
            }
        }

        if ($viewer_id) {
            if (!empty($errorMessage))
                $this->respondWithValidationError('validation_fail', $errorMessage);
            else
                $this->successResponseNoContent('no_content');
        }
        else {
            $this->respondwithsuccess($productsArray, false);
        }
    }

    /*
     * deletes a product from the cart
     */

    public function deleteProductAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Validate request methods
        $this->validateRequestMethod("DELETE");

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondwitherror('unauthorized');

        $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
        $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
        $cartProductsTableName = $cartProductsTable->info('name');
        $userCartId = $cartTable->getCartId($viewer_id);
        // cart products
        if (!$userCartId)
            $this->respondwitherror('no_record');

        $cartproduct_id = $this->_getParam('cartproduct_id');
        if (!$cartproduct_id)
            $this->respondWithValidationError('parameter_missing', "cartproduct_id missing");

        $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartproduct_id);

        if (!$cartProduct)
            $this->respondWithError('no_record');

        if ($cartProduct->cart_id != $userCartId)
            $this->respondWithValidationError('unauthorized', $this->translate("This product does not belong to your Cart"));

        $db = $cartProductsTable->getAdapter();
        $db->beginTransaction();
        try {
            $cartProduct->delete();
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * empty the cart
     */

    public function emptyAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Validate request methods
        $this->validateRequestMethod("POST");

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondwitherror('unauthorized');

        $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
        $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
        $cartProductsTableName = $cartProductsTable->info('name');
        $userCartId = $cartTable->getCartId($viewer_id);
        // cart products
        if (!$userCartId)
            $this->respondwitherror('no_record');

        $cart = Engine_Api::_()->getItem('sitestoreproduct_cart', $userCartId);

        $db = $cartProductsTable->getAdapter();
        $db->beginTransaction();
        try {
            $cart->_delete();
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     *   is coupon valid
     */

    public function applyCouponAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Validate request methods
        $this->validateRequestMethod("POST");

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondwitherror('unauthorized');

        $cartTable = Engine_Api::_()->getDbTable('carts', 'sitestoreproduct');
        $cartProductsTable = Engine_Api::_()->getDbTable('cartproducts', 'sitestoreproduct');
        $cartProductsTableName = $cartProductsTable->info('name');
        $userCartId = $cartTable->getCartId($viewer_id);
        $offersTable = Engine_Api::_()->getDbTable('offers', 'sitestoreoffer');

        $coupon_code = $this->_getParam('coupon_code');

        if (!$coupon_code)
            $this->respondWithValidationError('parameter_missing', 'coupon_code missing');

        $coupon = $offersTable->getCouponInfo(array('coupon_code' => $coupon_code, 'fetchRow' => 'fetchRow'), array('*'));
        $this->successResponseNoContent('no_content');
    }

    /*
     * Cart product gutter menu
     */

    private function gutterMenu($product) {
        if (!$product)
            return;

        $menu = array();

        $menu[] = array(
            'title' => $this->translate('Delete Product'),
            'name' => 'delete',
            'url' => 'sitestore/cart/deleteProduct/' . $product->getIdentity(),
        );


        return $menu;
    }

    private function _getTotalAmountFields($params, $paymentType) {

        if (isset($params['name']) && !empty($params['name']))
            $store_name = $params['name'];

        // if (isset($params['remainingAmountSubtotal']) && !empty($params['remainingAmountSubtotal']))
        //     $amountFields[$this->translate("Remaining Amount Subtotal " . $store_name)] = $params['remainingAmountSubtotal'];
        // if (isset($params['downPaymentPrice']) && !empty($params['downPaymentPrice']))
        // $amountFields[$this->translate("Downpayment Subtotal of ", $store_name)] = $params['downPaymentPrice'];

        if (isset($params['subTotal']) && !empty($params['subTotal']))
            $amountFields[$this->translate("Subtotal of " . $store_name)] = $params['subTotal'];

        // if(isset($params['totalVat']) && !empty($params['totalVat']))
        // $amountFields[$this->translate("VAT of " . $store_name)] = $params['totalVat'];

        if (isset($paymentType) && !empty($paymentType)) {
            // if (isset($params['grandTotal']) && !empty($params['grandTotal']))
            //     $amountFields[$this->translate("Grand Total")] = $params['grandTotal'];
            // if (isset($params['subTotal']) && !empty($params['subTotal']))
            //     $amountFields[$this->translate("Grand Total")] = $params['subTotal'];
        }
        if (isset($amountFields) && !empty($amountFields))
            return $amountFields;
    }

    public function _getTotalVatFields($params, $paymentType) {
        if (isset($params['name']) && !empty($params['name']))
            $store_name = $params['name'];
        if (isset($params['totalVat']))
            $amountFields[$this->translate("VAT of " . $store_name)] = $params['totalVat'];
        return $amountFields;
    }

    /*
     * Get configuration price for logged out user case
     */

    public function getConfigPrice($params) {
        if (!$params || !is_array($params))
            return 0.00;

        $combinationAttributesTable = Engine_Api::_()->getDbTable('CombinationAttributes', 'sitestoreproduct');
        $cartProductFieldsOptions = Engine_Api::_()->getDbtable('CartproductFieldOptions', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('CombinationAttributeMap', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');

        $configuration_price = 0.00;

        foreach ($params as $row => $value) {
            if ($row == 'combination_id') {
                $select = $combinationAttributeMapsTable->select()
                        ->from($combinationAttributeMapsTableName)
                        ->setIntegrityCheck(false)
                        ->joinInner($combinationAttributesTableName, $combinationAttributeMapsTableName . ".attribute_id = " . $combinationAttributesTableName . ".attribute_id")
                        ->where("$combinationAttributeMapsTableName.combination_id = ?", $value);

                $result = $select->query()->fetchALL();

                if (!empty($result)) {
                    foreach ($result as $data => $values) {
                        if ($values['price_increment'])
                            $configuration_price += $values['price'];
                        else
                            $configuration_price -= $values['price'];
                    }
                }

                $key = explode('_', $row);

                if (count($key == 3)) {
                    if (is_array($count)) {
                        foreach ($value as $row => $multioption) {
                            $select = $cartProductFieldsOptions->select()
                                    ->where('field_id = ?', $key[2])
                                    ->where('option_id = ?', $multioption);
                            $result = $select->query->fetchAll();

                            if ($result) {
                                if ($result[0]['price_increment'])
                                    $configuration_price += $result[0]['price'];
                                else
                                    $configuration_price -= $result[0]['price'];
                            }
                        }
                    }
                    elseif (is_array($value)) {
                        $select = $cartProductFieldsOptions->select()
                                ->where('field_id = ?', $key[2])
                                ->where('option_id = ?', $multioption);
                        $result = $select->query->fetchAll();

                        if ($result) {
                            if ($result[0]['price_increment'])
                                $configuration_price += $result[0]['price'];
                            else
                                $configuration_price -= $result[0]['price'];
                        }
                    }
                }
            }
        }
        return $configuration_price;
    }

    public function cartcountAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Validate request methods
        $this->validateRequestMethod("GET");

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $cartId = Engine_Api::_()->getDbtable("carts", "sitestoreproduct")->getCartId($viewer_id);
        if ($cartId)
            $updates['count'] = Engine_Api::_()->getDbtable("carts", "sitestoreproduct")->getProductCounts($cartId);
        else
            $updates['count'] = 0;

        $updates['count'] = $updates['count'] ? $updates['count'] : 0;

        $this->respondwithsuccess($updates);
    }

}
