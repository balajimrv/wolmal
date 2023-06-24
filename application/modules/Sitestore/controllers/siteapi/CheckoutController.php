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
class Sitestore_CheckoutController extends Siteapi_Controller_Action_Standard {

    public $_error;
    public $_showshippingaddress;

    public function init() {

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

        $isBuyAllow = Engine_Api::_()->sitestoreproduct()->isBuyAllowed();
        if (empty($isBuyAllow)) {
            $this->respondWithValidationError('not_approved', $this->translate("Buying is not allowed"));
        }

        $checkout_store_id = $this->_getParam('store_id', null);

        $isPaymentToSiteEnable = $this->isPaymentToSIteEnable();
        $this->checkShowShipping();
    }

    public function addressAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $values = $this->getAllParams();

        $checkout_store_id = $this->_getParam('store_id', null);

        $checkoutHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.level.createhost', 0);
        $checkoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', 0);

        $getPaymentType = $this->getPaymentType($checkoutHost, 'sitestore');
        $getPaymentAuth = $this->getPaymentAuth($checkoutSettings);

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

        $loggedoutViewerCheckout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.loggedoutviewercheckout', 1);

        // IF THERE IS NO COUNTRY AVAILABLE FOR SHIPPING
        $region_enable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();
        if (empty($region_enable)) {
            $this->respondWithValidationError('not_approved', $this->translate("No region is enabled for delivery , please contact the admin of the store"));
        }

        // CHECK ENABLE PAYMENT GATEWAYS WHEN DOWNPAYMENT IS NOT ENABLED
        $isOnlyCodGatewayEnable = false;
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        $isPaymentToSiteEnable = $this->isPaymentToSIteEnable();

        if (!$directPayment) {
            if (empty($isDownPaymentEnable)) {
                // DIRECT PAYMENT TO SELLER ENABLED
                if (empty($isPaymentToSiteEnable)) {
                    $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($checkout_store_id, 'store_gateway');
                    if (!empty($storeEnabledgateway)) {
                        $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                        $storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);

                        foreach ($storeEnabledgateway as $gatewayName => $gatewayTableId) {
                            if ($gatewayName == 'paypal') {
                                $tempGatewayId = 0;
                            } else if ($gatewayName == 'cheque') {
                                $tempGatewayId = 1;
                            } else if ($gatewayName == 'cod') {
                                $tempGatewayId = 2;
                            } elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                                $tempGatewayId = $gatewayName;
                            }

                            if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                $finalStoreEnableGateway[] = $gatewayName;
                            }
                        }

                        $payment_gateway = $finalStoreEnableGateway;
                        if (count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway))
                            $isOnlyCodGatewayEnable = true;
                    }

                    // IF NO PAYMENT GATEWAY ENABLE
                    if (empty($storeEnabledgateway) || empty($finalStoreEnableGateway))
                        $no_payment_gateway_enable = true;

                    if (isset($storeEnabledgateway['cheque']) && !empty($storeEnabledgateway['cheque']))
                        $storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $checkout_store_id, "storegateway_id" => $storeEnabledgateway['cheque']));
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

                    if (!empty($admin_payment_gateway)) {
                        foreach ($admin_payment_gateway as $payment_gateway) {
                            if (empty($payment_gateway)) {
                                $by_cheque_enable = true;
                                $admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
                            } else if ($payment_gateway == 1) {
                                $cod_enable = true;
                            }
                        }
                    }

                    if (empty($enable_gateway) && !empty($admin_payment_gateway) && empty($by_cheque_enable) && !empty($cod_enable)) {
                        $isOnlyCodGatewayEnable = true;
                    }
                    // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
                    if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                        $no_payment_gateway_enable = true;
                    }

                    $payment_gateway = $enable_gateway;
                }
            } else {
                // DIRECT PAYMENT MODE
                if (empty($isPaymentToSiteEnable)) {
                    $storeEnabledgateway = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreEnabledGateway(array('store_id' => $checkout_store_id, 'gateway_type' => 1));
                    if (!empty($storeEnabledgateway)) {
                        $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                        foreach ($storeEnabledgateway as $enabledGatewayName) {
                            if ($enabledGatewayName == 'PayPal') {
                                $tempGatewayId = 0;
                                $gatewayName = 'paypal';
                            } else if ($enabledGatewayName == 'ByCheque') {
                                $tempGatewayId = 1;
                                $gatewayName = 'cheque';
                            } else if ($enabledGatewayName == 'COD') {
                                $tempGatewayId = 2;
                                $gatewayName = 'cod';
                            }
                            if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                $finalStoreEnableGateway[] = $gatewayName;
                            }
                        }
                        $this->view->payment_gateway = $finalStoreEnableGateway;
                        if (count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway))
                            $isOnlyCodGatewayEnable = true;
                    }

                    // IF NO PAYMENT GATEWAY ENABLE
                    if (empty($storeEnabledgateway) || empty($finalStoreEnableGateway)) {
                        $no_payment_gateway_enable = true;
                    }
                } else {
                    $payment_gateway = $enable_gateway = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.defaultpaymentgateway', serialize(array('paypal', 'cheque', 'cod'))));
                    if (count($enable_gateway) == 1 && in_array('cod', $enable_gateway))
                        $isOnlyCodGatewayEnable = true;
                    if (!count($enable_gateway))
                        $no_payment_gateway_enable = true;
                }
            }

            if ($no_payment_gateway_enable)
                $this->respondWithValidationError('not_approved', $this->translate("NO Payment Gateway enable  , please contact the store administrator"));
        }



        $showShippingAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.virtual.product.shipping', 1);

        $sitestoreproduct_checkout_viewer_cart = $this->sitestoreproduct_checkout_viewer_cart();

        if (empty($sitestoreproduct_checkout_viewer_cart))
            $this->respondWithValidationError('not_approved', $this->translate("No products in cart"));

        $formValues = array();
        $addressTable = Engine_Api::_()->getDbtable('Addresses', 'sitestoreproduct');
        $orderAddressTable = Engine_Api::_()->getDbTable('orderaddresses', 'sitestoreproduct');
        if ($viewer_id)
            $formValues = $addressTable->getAddress(array('owner_id' => $viewer_id));

        if ($this->getRequest()->isGet()) {
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->sitestoreproduct_Form_Addresses(array(
                'viewerId' => $viewer_id, 'store_id' => $values['store_id'], 'showShippingAddress' => $this->_showshippingaddress));

            $regionTable = Engine_Api::_()->getDbTable('Regions', 'sitestoreproduct');
            $regionTableName = $regionTable->info('name');

            if ($formValues) {
                $formValues = $formValues->toArray();
                $billingAddress = $shippingAddress = array();
                $billingAddress['f_name_billing'] = ($formValues[0]['f_name']) ? $formValues[0]['f_name'] : "";
                $billingAddress['l_name_billing'] = ($formValues[0]['l_name']) ? $formValues[0]['l_name'] : "";
                $billingAddress['email_billing'] = ($formValues[0]['email']) ? $formValues[0]['email'] : "";
                $billingAddress['phone_billing'] = ($formValues[0]['phone']) ? $formValues[0]['phone'] : "";
                $billingAddress['country_billing'] = ($formValues[0]['country']) ? $formValues[0]['country'] : "";
                if ($formValues[0]['country']) {
                    $select = $regionTable->select()
                            ->from($regionTableName)
                            ->where("country = ?", $formValues[0]['country']);
                    $result = $select->query()->fetchAll();

                    if ($result) {
                        $form['billingForm'][4]['multiOptions']['0'] = $this->translate('--- select ---');

                        foreach ($result as $row => $value)
                            $form['billingForm'][4]['multiOptions'][$value['region_id']] = $this->translate($value['region']);
                    }
                }
                $billingAddress['state_billing'] = ($formValues[0]['state']) ? $formValues[0]['state'] : "";
                $billingAddress['city_billing'] = ($formValues[0]['city']) ? $formValues[0]['city'] : "";
                $billingAddress['locality_billing'] = ($formValues[0]['locality']) ? $formValues[0]['locality'] : "";
                $billingAddress['address_billing'] = ($formValues[0]['address']) ? $formValues[0]['address'] : "";
                $billingAddress['zip_billing'] = ($formValues[0]['zip']) ? $formValues[0]['zip'] : "";

                if ($this->_showshippingaddress) {
                    $billingAddress['common'] = $formValues[0]['common'];
                    $shippingAddress['f_name_shipping'] = ($formValues[1]['f_name']) ? $formValues[1]['f_name'] : "";
                    $shippingAddress['l_name_shipping'] = ($formValues[1]['l_name']) ? $formValues[1]['l_name'] : "";
                    $shippingAddress['email_shipping'] = ($formValues[1]['email']) ? $formValues[1]['email'] : "";
                    $shippingAddress['phone_shipping'] = ($formValues[1]['phone']) ? $formValues[1]['phone'] : "";
                    $shippingAddress['country_shipping'] = ($formValues[1]['country']) ? $formValues[1]['country'] : "";
                    if ($formValues[1]['country']) {
                        $select = $regionTable->select()
                                ->from($regionTableName)
                                ->where("country = ?", $formValues[1]['country']);
                        $result = $select->query()->fetchAll();

                        if ($result) {
                            $form['shippingForm'][4]['multiOptions']['0'] = $this->translate('--- select ---');

                            foreach ($result as $row => $value)
                                $form['shippingForm'][4]['multiOptions'][$value['region_id']] = $this->translate($value['region']);
                        }
                    }
                    $shippingAddress['state_shipping'] = ($formValues[1]['state']) ? $formValues[1]['state'] : "";
                    $shippingAddress['city_shipping'] = ($formValues[1]['city']) ? $formValues[1]['city'] : "";
                    $shippingAddress['locality_shipping'] = ($formValues[1]['locality']) ? $formValues[1]['locality'] : "";
                    $shippingAddress['address_shipping'] = ($formValues[0]['address']) ? $formValues[0]['address'] : "";
                    $shippingAddress['zip_shipping'] = ($formValues[1]['zip']) ? $formValues[1]['zip'] : "";
                    $response['formValues']['shippingAddress'] = $shippingAddress;
                }
                $response['formValues']['billingAddress'] = $billingAddress;
            }

            $response['form'] = $form;

            $this->respondWithSuccess($response, false);
        }

        if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();

            if(!$viewer_id && (!isset($values['email_billing']) || empty($values['email_billing'])))
                $this->respondWithValidationError("parameter_missing" , "email missing");
            elseif(!$viewer_id && !filter_var($values['email_billing'], FILTER_VALIDATE_EMAIL))
                $this->respondWithValidationError("parameter_missing" , "email address invalid");

            if ($formValues) {
                $billingAddress = $formValues->getRowsMatching(array('type' => '0'));
                $shippingAddress = $formValues->getRowsMatching(array('type' => '1'));
            }

            $billingAddressArray = array();
            $billingAddressArray['f_name'] = $values['f_name_billing'];
            $billingAddressArray['l_name'] = $values['l_name_billing'];
            $billingAddressArray['common'] = ($values['common']) ? 1 : 0;
            $billingAddressArray['phone'] = $values['phone_billing'];
            $billingAddressArray['address'] = $values['address_billing'];
            $billingAddressArray['country'] = $values['country_billing'];
            $billingAddressArray['state'] = $values['state_billing'];
            $billingAddressArray['city'] = $values['city_billing'];
            $billingAddressArray['locality'] = $values['locality_billing'];
            $billingAddressArray['zip'] = $values['zip_billing'];

            $billingAddressValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestore')->billingAddressValidators();

            $values['validators'] = $billingAddressValidators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if ((!isset($values['common']) || empty($values['common'])) && $this->_showshippingaddress) {

                $shippingAddressArray = array();
                $shippingAddressArray['f_name'] = $values['f_name_shipping'];
                $shippingAddressArray['l_name'] = $values['l_name_shipping'];
                $shippingAddressArray['common'] = ($values['common']) ? 1 : 0;
                $shippingAddressArray['phone'] = $values['phone_shipping'];
                $shippingAddressArray['address'] = $values['address_shipping'];
                $shippingAddressArray['country'] = $values['country_shipping'];
                $shippingAddressArray['state'] = $values['state_shipping'];
                $shippingAddressArray['city'] = $values['city_shipping'];
                $shippingAddressArray['locality'] = $values['locality_shipping'];
                $shippingAddressArray['zip'] = $values['zip_shipping'];

                $shippingAddressValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestore')->shippingAddressValidators();

                $values['validators'] = $shippingAddressValidators;
                $validationMessage = $this->isValid($values);
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
            }

            if (!isset($values['common']))
                $values['common'] = 1;

            $db = $addressTable->getAdapter();
            $db->beginTransaction();
            try {

                if ($billingAddress) {
                    $billingAddress = $billingAddress[0];
                    // $addressTable->update($billingAddressArray, array('address_id' => $billingAddress->address_id));
                    $billingAddress->setFromArray($billingAddressArray);
                    
                    $billingAddress->common = $values['common'];
                    $billingAddress->save();
                } else {
                    $billingAddress = $addressTable->createRow();
                    $billingAddress->setFromArray($billingAddressArray);
                    $billingAddress->type = 0;
                    $billingAddress->common = $values['common'];
                    $billingAddress->owner_id = $viewer_id;
                    $billingAddress->save();
                }

                $neworderbillingAddress = $orderAddressTable->createRow();
                $neworderbillingAddress->setFromArray($billingAddressArray);
                if(isset($values['email_billing']))
                $neworderbillingAddress->email = $values['email_billing'];
                $neworderbillingAddress->type = 0;
                $neworderbillingAddress->save();

                if ($values['common'] && $this->_showshippingaddress) {
                    if ($shippingAddress) {
                        $shippingAddress = $shippingAddress[0];
                        // $addressTable->update($billingAddressArray, array('address_id' => $shippingAddress->address_id));
                        $shippingAddress->setFromArray($billingAddressArray);
                        $shippingAddress->common = 1;
                        $shippingAddress->save();
                    } else {
                        $shippingAddress = $addressTable->createRow();
                        $shippingAddress->setFromArray($billingAddressArray);
                        $shippingAddress->type = 1;
                        $shippingAddress->common = 1;
                        $shippingAddress->owner_id = $viewer_id;
                        $shippingAddress->save();
                    }

                    $newshippingaddress = $orderAddressTable->createRow();
                    $newshippingaddress->setFromArray($billingAddressArray);
                    $newshippingaddress->type = 1;
                    $newshippingaddress->save();
                } else if ($this->_showshippingaddress) {
                    if ($shippingAddress) {
                        $shippingAddress = $shippingAddress[0];
                        // $addressTable->update($shippingAddressArray, array('address_id' => $shippingAddress->address_id));
                        $shippingAddress->setFromArray($shippingAddressArray);
                        $shippingAddress->common = 0;
                        $shippingAddress->save();
                    } else {
                        $shippingAddress = $addressTable->createRow();
                        $shippingAddress->setFromArray($shippingAddressArray);
                        $shippingAddress->type = 1;
                        $shippingAddress->common = 0;
                        $shippingAddress->owner_id = $viewer_id;
                        $shippingAddress->save();
                    }

                    $newshippingaddress = $orderAddressTable->createRow();
                    $newshippingaddress->setFromArray($shippingAddressArray);
                    $newshippingaddress->type = 1;
                    $newshippingaddress->save();
                }
                $db->commit();
                $response = array();
                $response['billingAddress'] = $neworderbillingAddress->orderaddress_id;
                if ($this->_showshippingaddress)
                    $response['shippingAddress'] = $newshippingaddress->orderaddress_id;
                $this->respondWithSuccess($response, false);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
     * Shipping methods
     */

    public function shippingAction() {
        if (!$this->_showshippingaddress) {
            $response = array();
            $tempResponseArray = array(
                'type' => 'submit',
                'name' => 'submit',
                'description' => $this->translate("These products doesn't require a shipping method"),
                'label' => $this->translate('Continue'),
            );
            $response['form'][] = $tempResponseArray;
            $this->respondWithSuccess($response, false);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $response = array();
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

        if($viewer_id)
            $cart_obj = $this->getLoggedInUserCart($viewer_id);

        $shipping_method_obj = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct');
        $checkout_store_id = $this->_getParam('store_id', null);

        // if(!$checkout_store_id)
        // $this->respondWithValidationError('parameter_missing' , 'store_id missing');

        if($viewer_id)
            $cartData = $this->getCartProducts($cart_obj, $checkout_store_id);
        else
            $cartData = $this->sitestoreproduct_checkout_viewer_cart();

        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);

        $totalPrice = 0;
        $totalWeight = 0;
        $totalQuantity = 0;

        $addressTable = Engine_Api::_()->getDbTable('Addresses', 'sitestoreproduct');
        $productsTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

        $shippingAddress = $addressTable->getAddress(array('owner_id' => $viewer_id, 'type' => '1'));

        if ($shippingAddress)
            $shippingAddress = $shippingAddress->toArray()[0];

        $params = array();

        if ($cartData) {
            foreach ($cartData as $row => $value) {
                $product = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);

                if (!isset($params[$product->store_id])) {
                    $params[$product->store_id]['store_id'] = $product->store_id;
                    $params[$product->store_id]['total_weight'] = 0;
                    $params[$product->store_id]['total_quantity'] = 0;
                    $params[$product->store_id]['total_price'] = 0;
                    $params[$product->store_id]['shipping_region_id'] = $shippingAddress['state'];
                    $params[$product->store_id]['shipping_country'] = $shippingAddress['country'];
                }

                $params[$product->store_id]['total_weight'] += $product->weight;

                if (!empty($isVatAllow)) {
                    if ($product->product_type == 'configurable' || $product->product_type == 'virtual') {
                        if($viewer_id)
                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product, null, $value['cartproduct_id']);
                        else
                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                    } else {
                        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                    }
                    $params[$product->store_id]['total_price'] += $productPricesArray['product_price_after_discount'] * $value['quantity'];
                    $params[$product->store_id]['total_quantity'] += $value['quantity'];
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
                        }
                        else
                            $configuration_price = 0.00;

                        $productDiscountedPrice = $configuration_price + $productsTable->getProductDiscountedPrice($product->product_id);
                    } else {
                        $productDiscountedPrice = $productsTable->getProductDiscountedPrice($product->product_id);
                    }
                    $params[$product->store_id]['total_price'] += $productDiscountedPrice * $value['quantity'];
                    $params[$product->store_id]['total_quantity'] += $value['quantity'];
                }
            }
        } else
            $this->respondWithError('no_record');

        $store_ids = array_keys($params);
        $isshippingallowed = array();

        $shippingMethods = Engine_Api::_()->getDbTable('Shippingmethods', 'sitestoreproduct');
        $shippingMethodsData = array();

        foreach ($params as $row => $value) {
            if($this->checkShowShipping($row))
                $isshippingallowed[$value['store_id']] = 1;
            else
            {
                $isshippingallowed[$value['store_id']] = 0;
                continue;
            }
            $data = Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->getCheckoutShippingMethods($value);
            if (!count($data))
                $response[$value['store_id']]['error'] = $this->translate(" No Shipping Methods available , Please Contact Store Admin ");
            else
                $shippingMethodsData[$value['store_id']] = $data;
        }

        if ($this->getRequest()->isGet()) {

            if (!$shippingMethodsData)
                $this->respondWithValidationError('unauthorized', 'There are no shipping methods avalilable  , please contact store admin');

            foreach ($shippingMethodsData as $store_id => $value) {
                
                if(!$isshippingallowed[$store_id])
                    continue;

                $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
                $shippingInformation = $this->translate("Shipping Methods available :");
                $i = 1;
                $multiOptions = array();
                if (count($value)) {
                    foreach ($value as $row => $shippingMethod) {
                        $shippingInformation .= " \n" . $i++ . ") " . $this->translate($shippingMethod['name']) . " : " . $this->translate($shippingMethod['delivery_time'] . " , ".$response['currency']." ".$shippingMethod['charge']  );

                        $multiOptions[$shippingMethod['shippingmethod_id']] = $this->translate($shippingMethod['name']);
                        
                    }

                    $tempResponse = array(
                        'type' => 'radio',
                        'name' => 'shipping_method_' . $store_id,
                        'label' => $this->translate("For " . $store->getTitle()),
                        'multiOptions' => $multiOptions,
                    );
                    if(count($value)==1)
                        $tempResponse['value'] = $shippingMethod['shippingmethod_id'] ;                    
                    $tempResponse['shippingInformation'] = $shippingInformation;
                    $tempResponseArray[] = $tempResponse;
                }
            }

            if (isset($tempResponseArray) && !empty($tempResponseArray)) {
                $tempResponseArray[] = array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'label' => $this->translate('Continue'),
                );
                $response['form'] = $tempResponseArray;
            }
            $this->respondWithSuccess($response,false);
        }

        if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            foreach ($store_ids as $row => $value) {
                if(!$isshippingallowed[$value])
                    continue;
                $store = Engine_Api::_()->getItem('sitestore_store', $value);
                if (!isset($values['shipping_method_' . $value]) || empty($values['shipping_method_' . $value]))
                    $this->respondWithValidationError('parameter_missing', "Please Select A Shipping Method for Store : ".$store->getTitle());
            }

            $this->successResponseNoContent('no_content');

        }
    }

    /*
     * Payment method
     */

    public function paymentAction() {

        $checkout_store_id = $this->_getParam('store_id');

        $getPaymentType = $this->getPaymentType();
        $getPaymentAuth = $this->getPaymentAuth();

        if (($getPaymentType != $getPaymentAuth)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.type', 0);
            $paymentRequest = true;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        // CHECK ENABLE PAYMENT GATEWAYS WHEN DOWNPAYMENT IS NOT ENABLED
        $isOnlyCodGatewayEnable = false;
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        $isPaymentToSiteEnable = $this->isPaymentToSIteEnable();

        $gatewayArray = array();
        $form = array();
        $gatewayArray["2checkout"] = array('label' => $this->translate('2Checkout'), 'value' => 1);
        $gatewayArray["paypal"] = array('label' => $this->translate("Paypal"), 'value' => 2);
        $gatewayArray["cheque"] = array('label' => $this->translate("By Cheque"), 'value' => 3);
        $gatewayArray["cod"] = array('label' => $this->translate("Cash on delivery"), 'value' => 4);
        $gatewayArray["free"] = array('label' => $this->translate('Free order'), 'value' => 5);

        $multiOptions = array();

        if (empty($isPaymentToSiteEnable)) {
            $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($checkout_store_id, 'store_gateway');
            if(!empty($storeEnabledgateway)){
                $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');
                if ($paymentMethod == 'split') {
                    $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.split.gateway', array());
                } else if ($paymentMethod == 'escrow') {
                    $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.escrow.gateway', array());
                } else {
                    $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                }

                $storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);
                foreach ($storeEnabledgateway as $gatewayName => $gatewayTableId) {
                    if ($gatewayName == 'paypal') {
                        $tempGatewayId = 0;
                    } else if ($gatewayName == 'cheque') {
                        $tempGatewayId = 1;
                    } else if ($gatewayName == 'cod') {
                        $tempGatewayId = 2;
                    } elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                        $tempGatewayId = $gatewayName;
                    }
                    if ($tempGatewayId == 1 || $tempGatewayId == 2) {
                        if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                            $finalStoreEnableGateway[] = $gatewayName;
                        } else {
                            if ($tempGatewayId == 1) {
                                $tempGatewayId = "cheque";
                            } else {
                                $tempGatewayId = "cash";
                            }
                            if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                $finalStoreEnableGateway[] = $gatewayName;
                            }
                        }
                    } else {
                        if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                            $finalStoreEnableGateway[] = $gatewayName;
                        }
                    }
                }



                if (count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway))
                            $isOnlyCodGatewayEnable = true;

                foreach($finalStoreEnableGateway as $gatewayId => $payment_method)
                {
                    if($payment_method=='cod' && !$this->_showshippingaddress)
                        continue;

                    if($payment_method=='cod')
                        $multiOptions[4] = $this->translate("Cash on Delivery");
                    elseif($payment_method=='cheque')
                        $multiOptions[3] = $this->translate("By Cheque");
                    elseif($payment_method=='paypal')
                        $multiOptions[2] = $this->translate("PayPal");
                    elseif($payment_method=='2checkout')
                        $multiOptions[1] = $this->translate("2Checkout");
                    else
                    {
                        $paymentGateway = Engine_Api::_()->sitegateway()->getGatewayColumn(array('fetchRow' => true, 'plugin' => "Sitegateway_Plugin_Gateway_".$payment_method));
                        if($paymentGateway->title=='PayPalAdaptive')
                            $multiOptions[$paymentGateway->gateway_id] = $this->translate("Paypal");
                        elseif($paymentGateway->title=='Payumoney')
                            $multiOptions[$paymentGateway->gateway_id] = $this->translate("PayUmoney");
                        else
                            $multiOptions[$paymentGateway->gateway_id] = $this->translate($paymentGateway->title);
                    }

                }

            }
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

            if (!empty($admin_payment_gateway)) {
                foreach ($admin_payment_gateway as $payment_gateway) {
                    if (empty($payment_gateway)) {                        
                        $by_cheque_enable = true;
                        $multiOptions[3] = $this->translate('By Cheque');
                        $admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
                    } else if ($payment_gateway == 1) {
                        $cod_enable = true;
                        if($this->_showshippingaddress)
                            $multiOptions[4] = $this->translate('Cash on Delivery');
                    }
                }
            }

            if (empty($enable_gateway) && !empty($admin_payment_gateway) && empty($by_cheque_enable) && !empty($cod_enable)) {
                $isOnlyCodGatewayEnable = true;
            }
            // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
            if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                $no_payment_gateway_enable = true;
            }
            $payment_gateway = $enable_gateway;

            foreach ($payment_gateway as $row => $value) {
                if($value['title']=='PayPalAdaptive' || $value['title']=='MangoPay' || $value['title']=='Payumoney' || $value['title']=='Paynow')
                    continue;
                $multiOptions[$value['gateway_id']] = $this->translate($value['title']);
            }
        }

        if (empty($finalStoreEnableGateway) && empty($payment_gateway))
            $this->respondWithValidationError('validation_fail', 'no payment gateway enabled , please contact the store admin');

        ksort($multiOptions);

        if($this->getRequest()->isGet())
        {
            $options = array();
            $chequeForm[] = array(
                'type' => 'Text',
                'name' => 'cheque_number',
                'hasValidator' => true,
                'label' => $this->translate("Cheque No. / Ref. No."),
            );

            $chequeForm[] = array(
                'type' => 'Text',
                'name' => 'cheque_name',
                'hasValidator' => true,
                'label' => $this->translate("Account Holder Name"),
            );

            $chequeForm[] = array(
                'type' => 'Text',
                'name' => 'cheque_account_number',
                'hasValidator' => true,
                'label' => $this->translate('Account Number'),
            );

            $chequeForm[] = array(
                'type' => 'Text',
                'name' => 'cheque_routing_number',
                'hasValidator' => true,
                'label' => $this->translate("Bank Routing Number"), 
            );

            $options['3'] = $chequeForm;

            $form[] = array(
                'type' => 'radio',
                'label' => $this->translate('Select Payment Gateway'),
                'name' => 'payment_gateway',
                'multiOptions' => $multiOptions,
            );

            $form[] = array(
                'type' => 'Button',
                'name' => 'Continue',
                'label' => $this->translate("Continue"),
            );

            $response = array();
            $response['form'] = $form;
            if (isset($multiOptions['3']))
                $response['options'] = $options;

            $this->respondWithSuccess($response, false);

        }
        if($this->getRequest()->isPost())
        {
            $values = $this->_getAllParams();
            if(!isset($values['payment_gateway']) || empty($values['payment_gateway']))
                $this->respondWithValidationError("parameter_missing" , "Please select a payment method");

            if($values['payment_gateway']==3)
            {
                if(!isset($values['cheque_number']) || empty($values['cheque_number']))
                    $this->respondWithValidationError("parameter_missing" , "Please Enter 'Cheque No. / Ref. No.'");
                if(!isset($values['cheque_name']) || empty($values['cheque_name']))
                    $this->respondWithValidationError("parameter_missing" , "Please Enter 'Account Holder Name'");
                if(!isset($values['cheque_account_number']) || empty($values['cheque_account_number']))
                    $this->respondWithValidationError("parameter_missing" , "Please Enter 'Account Number'");
                if(!isset($values['cheque_routing_number']) || empty($values['cheque_routing_number']))
                    $this->respondWithValidationError("parameter_missing" , "Plese Enter 'Bank Routing Number'");
            }
            $this->successResponseNoContent("no_content");
        }
    }

    /*
     * Validating order
     */

    public function validatingOrderAction() {

        $params = $this->_getAllParams();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if($viewer_id)
            $cart_obj = $this->getLoggedInUserCart($viewer_id);

        $checkout_store_id = $params['store_id'];

        // Downpayment settings
        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        // DOwnpayment Settings end

        $isPaymentToSiteEnable = $this->isPaymentToSIteEnable();

        if (!$checkout_store_id && !$isPaymentToSiteEnable)
            $this->respondWithValidationError('parameter_missing', 'store_id missing');

        if($viewer_id)
            $cartProducts = $this->getCartProducts($cart_obj, $checkout_store_id);
        else
            $cartProducts = $this->sitestoreproduct_checkout_viewer_cart();

        if (!count($cartProducts) || empty($cartProducts))
            $this->respondWithError('no_record');

        $store_ids = array();
        $isshippingallowed = array();

        $shippingMethodarray = array();

        foreach ($cartProducts as $row => $value) {

            $product = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);

            if (!in_array($product->store_id, $store_ids))
            {
                $store_ids[] = $product->store_id;
                $isshippingallowed[$product->store_id] = $this->checkShowShipping($product->store_id) ;
            }

            if ($isshippingallowed[$product->store_id]) {
                $shippingMethodId = $this->_getParam('shipping_method_' . $product->store_id);

                if (!$shippingMethodId)
                    $this->respondWithValidationError('parameter_missing', "shipping_method_" . $product->store_id . " missing");

                $shippingMethod = Engine_Api::_()->getItem('sitestoreproduct_shippingmethod', $shippingMethodId);

                if (!$shippingMethod)
                    $this->respondWithValidationError('unauthorized', 'no such shipping method exists corresponding shipping_method_' . $product->store_id);

                $shippingMethodarray[$product->store_id] = $shippingMethod;
            }
        }

        $paymentGatewayId = $this->_getParam('payment_gateway');

        if (!$paymentGatewayId)
            $this->respondWithValidationError('parameter_missing', 'payment_gateway missing');

        $billingAddress_id = $this->_getParam('billingAddress_id');

        if (!$billingAddress_id)
            $this->respondWithValidationError('parameter_missing', 'billingAddress_id missing');

        $shippingAddress_id = $this->_getParam('shippingAddress_id');
        if (!$shippingAddress_id && $this->_showshippingaddress)
            $this->respondWithValidationError('parameter_missing', 'shippingAddress_id missing');

        $productsTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
        $couponTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
        $ordercouponsTable = Engine_Api::_()->getDbtable('ordercoupons', 'sitestoreoffer');
        $taxesTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
        $taxRatesTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
        $addressTable = Engine_Api::_()->getDbTable('addresses', 'sitestoreproduct');

        $orderAddressTable = Engine_Api::_()->getDbTable('orderaddresses', 'sitestoreproduct');

        $billingAddress = $orderAddressTable->select()
                        ->from($orderAddressTable->info('name'))
                        ->where('orderaddress_id = ?', $billingAddress_id)->query()->fetchAll();

        if ($this->_showshippingaddress) {
            $shippingAddress = $orderAddressTable->select()
                            ->from($orderAddressTable->info('name'))
                            ->where('orderaddress_id = ?', $shippingAddress_id)->query()->fetchAll();
        }

        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
        $total = 0;
        $totalTax = 0;

        $response = array();
        $response['totalProductQuantity'] = 0;
        $response['directPayment'] = $directPayment;
        $response['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $response['totalProductCount'] = count($cartProducts);
        $response['grandTotal'] = 0.00;

        foreach ($cartProducts as $row => $value) {
            $vatTax = 0;
            $admin_tax = 0;
            $admin_tax_array = array();
            $tempArray = array();
            $product = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);

            if (!isset($response['stores'][$product->store_id])) {
                $response['stores'][$product->store_id]['total'] = 0.00;
                $response['stores'][$product->store_id]['subTotal'] = 0.00;
                if($isVatAllow)
                    $response['stores'][$product->store_id]['totalVat'] = 0.00;
                else
                    $response['stores'][$product->store_id]['tax'] = 0.00;
                if ($isDownPaymentEnable) {
                    $response['stores'][$product->store_id]['downPaymentTotal'] = 0.00;
                    $response['stores'][$product->store_id]['remainingAmountTotal'] = 0.00;
                }
            }

            $tempArray['title'] = $product->title;
            $tempArray['product_id'] = $product->getIdentity();
            $tempArray['cartproduct_id'] = $value['cartproduct_id'];
            $tempArray['quantity'] = $value['quantity'];
            $tempArray['product_type'] = $product->product_type;
            $tempArray = array_merge($tempArray, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($product));

            if (isset($isVatAllow) && !empty($isVatAllow)) {
                if ($product->product_type == 'configurable' || $product->product_type == 'virtual') {
                    if($viewer_id)
                        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product, null, $value['cartproduct_id']);
                    else
                        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                } else {
                    $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product);
                }

                $productDiscountedPrice = $productPricesArray['display_product_price'];

                $vatTax = $productPricesArray['vat'] * $value['quantity'];
                $tempArray['unitVat'] = $productPricesArray['vat'];
                $tempArray['vat'] = $vatTax;
                $tempArray['show_msg'] = $productPricesArray['show_msg'];
                $tempArray['show_price_with_vat'] = $productPricesArray['show_price_with_vat'];
                $tempArray['save_price_with_vat'] = $productPricesArray['save_price_with_vat'];
                $productDiscountedPrice = (float)$productPricesArray['display_product_price'];
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
                    }
                    else
                        $configuration_price = 0.00;

                    $productDiscountedPrice = $configuration_price + $productsTable->getProductDiscountedPrice($product->product_id);
                } else {
                    $productDiscountedPrice = $productsTable->getProductDiscountedPrice($product->product_id);
                }

                // tax work starts 

                $userTaxData = unserialize($product->user_tax);

                $taxids = null;

                if (!empty($userTaxData)) {
                    foreach ($userTaxData as $row => $taxid)
                        $taxids[] = $taxid;

                    $taxids = implode(',', $taxids);
                }

                if (!empty($taxids)) {
                    $address = array();

                    if ($this->_showshippingaddress) {
                        $address['shipping_region_id'] = $shippingAddress[0]['state'];
                        $address['shipping_country'] = $shippingAddress[0]['country'];
                    } else {
                        $address['shipping_region_id'] = null;
                        $address['shipping_country'] = null;
                    }
                    $address['billing_region_id'] = $billingAddress[0]['state'];
                    $address['billing_country'] = $billingAddress[0]['country'];

                    $product_type = !$isshippingallowed[$product->store_id] ? "downloadable" : null;

                    $taxesTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');

                    $taxesData = $taxesTable->getCheckoutTaxes($taxids, $address, $product_type);

                    if(!empty($taxesData))
                    {
                        foreach ($taxesData as $row => $tax) {
                            $taxamount = (($tax['handling_type']) ? ( (float) ($productsTable->getProductDiscountedPrice($product->product_id) * $value['quantity']) * (float) $tax['tax_value'] ) / 100 : (float) $tax['tax_value']);
                            if($tax['store_id'])
                                $tempArray['store_tax'] += $taxamount;
                            else
                                $tempArray['admin_tax'] += $taxamount;
                            $admin_tax += $taxamount;
                            $admin_tax_array[] = array('title' => $this->translate($tax['title']), 'amount' => $taxamount, 'tax_value' => ($tax['handling_type']) ? $tax['tax_value'] . "%" : $tax['tax_value'], 'handling_type' => $tax['handling_type'], 'type' => $tax['store_id'] ? "Store administrator" : "Site administrator");
                        }
                    }
                }

                // user tax work ends 
            }

            // get products configuration
            if ($product->product_type == 'virtual' || $product->product_type == 'configurable') {
                $data = array();
                $cartProductFieldMeta = Engine_Api::_()->getDbTable('CartproductFieldMeta', 'sitestoreproduct');
                $cartProductFieldOptions = Engine_Api::_()->getDbTable('CartproductFieldOptions', 'sitestoreproduct');
                if ($viewer_id) {
                    $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value['cartproduct_id']);
                    $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                    if ($values->count()) {
                        $data['configuration'] = array();
                        foreach ($values as $fieldValue) {
                            $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($fieldValue->field_id));
                            $fieldValueLabel = $fieldValue->value ;
                            if(is_numeric($fieldValue->value))
                                $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($fieldValue->field_id, $fieldValue->value));
                            if (isset($data['configuration'][$fieldLabel]) && !empty($data['configuration'][$fieldLabel]))
                                $data['configuration'][$fieldLabel] .= " , " . $fieldValueLabel;
                            else
                                $data['configuration'][$fieldLabel] = $fieldValueLabel;
                        }
                    }
                } else {
                    if (isset($value['configFields']) && !empty($value['configFields'])) {
                        foreach ($value['configFields'] as $fieldname => $fieldvalue) {
                            if($fieldname=='combination_id')
                            {
                                continue;
                            }

                            $key = explode('_', $fieldname);
                            if(count($key)==2)
                            {
                                $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($key[1]));
                                $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($key[1], $fieldvalue));
                                $data['configuration'][$fieldLabel] = $fieldValueLabel;
                            }
                            elseif(count($key)==3)
                            {
                                if(!is_array($fieldvalue))
                                {
                                    $fieldLabel = $this->translate($cartProductFieldMeta->getFieldLabel($key[2]));
                                    $fieldValueLabel = $this->translate($cartProductFieldOptions->getOptionLabel($key[2], $fieldvalue));
                                    $data['configuration'][$fieldLabel] = $fieldValueLabel;
                                }
                                else
                                {
                                    foreach($fieldvalue as $subfieldrow => $subfieldvalue)
                                    {
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
                    unset($value['configFields']['combination_id']);
                    $tempArray['config_info'] = $value['configFields'];
                }

                $tempArray['configuration'] = $data['configuration'];

            }

            $response['totalProductQuantity'] += $value['quantity'];

            $tempArray['unitPrice'] = $productDiscountedPrice;
            $tempArray['price'] = $productDiscountedPrice * $value['quantity'];

            if (!empty($isDownPaymentEnable)) {
                $downPaymentPrice = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product->product_id, 'price' => $productDiscountedPrice));
                $tempArray['downPaymentPrice'] = number_format($downPaymentPrice * $value['quantity'], 2);
                $tempArray['remainingAmountTotal'] = $tempArray['price'] - $tempArray['downPaymentPrice'];
            }

            if ($isVatAllow)
            {
                $tempArray['vat'] = $vatTax;
                $response['stores'][$product->store_id]['totalVat'] += floatval($tempArray['vat']);
                if(($tempArray['show_price_with_vat']==true && $tempArray['save_price_with_vat']==true )  || ($tempArray['show_price_with_vat']==true && $tempArray['save_price_with_vat']==false ))
                {
                    $response['stores'][$product->store_id]['total'] += floatval($tempArray['vat']);
                    $response['stores'][$product->store_id]['subTotal'] += floatval($tempArray['price']);
                    $response['stores'][$product->store_id]['total'] += floatval($tempArray['price']);
                }
                else
                {
                    $response['stores'][$product->store_id]['subTotal'] += floatval($tempArray['price']) - floatval($tempArray['vat']);
                    $response['stores'][$product->store_id]['total'] += floatval($tempArray['price']);
                }
            }
            else {
                $tempArray['tax'] = $admin_tax;
                $tempArray['tax_detail'] = $admin_tax_array;
                $response['stores'][$product->store_id]['tax'] += $tempArray['tax'];
                $response['stores'][$product->store_id]['total_admin_tax'] += $tempArray['admin_tax'];
                $response['stores'][$product->store_id]['total_store_tax'] += $tempArray['store_tax'];
                $response['stores'][$product->store_id]['total'] += $tempArray['tax'];
                $response['stores'][$product->store_id]['subTotal'] += floatval($tempArray['price']);
                $response['stores'][$product->store_id]['total'] += floatval($tempArray['price']);
            }
            $subTotal[$product->store_id] += $tempArray['price'];

            $sitestore = Engine_Api::_()->getItem('sitestore_store', $product->store_id);
            $response['stores'][$product->store_id]['name'] = $sitestore->getTitle();
            $response['stores'][$product->store_id]['products'][] = $tempArray;

        }

        // COUPON CODE WORK HERE
        if ((isset($params['coupon_code']) && !empty($params['coupon_code'])) || (isset($params['coupon_code_' . $checkout_store_id]) && !empty($params['coupon_code_' . $checkout_store_id]))) {
            $coupon_code = ($params['coupon_code']) ? $params['coupon_code'] : $params['coupon_code_' . $checkout_store_id];
            $couponCodeArray = explode(',', $coupon_code);
            $couponTableName = $couponTable->info('name');

            foreach ($couponCodeArray as $row => $value) {
                $select = $couponTable->select()
                        ->from($couponTableName)
                        ->where("coupon_code = ?", $value);

                $coupon = $select->query()->fetchALL();

                $error = "";

                if (!$coupon)
                {
                    $response['couponerror'] = $this->translate("Please enter a different coupon code as ".$value." is either invalid or expired");
                    continue;
                }

                if(!isset($response['stores'][$coupon[0]['store_id']]['products']) && empty($response['stores'][$coupon[0]['store_id']]['products']))
                {
                    $response['couponerror'] = $this->translate("Please enter a different coupon code as ".$value." is either invalid or expired");
                }

                if ($coupon[0]['claim_count']>=0 && $coupon[0]['claim_count']-$coupon[0]['claimed'] <= 0 || (!$coupon[0]['status'])) {
                    $error = $this->translate("Please enter a different coupon code as ".$value." is either invalid or expired");
                }

                if(($coupon[0]['end_time']!="0000-00-00 00:00:00") && strtotime($coupon[0]['end_time'])<strtotime(date('Y-m-d H:i:s')))
                {
                    $error = $this->translate("Please enter a different coupon code as ".$value." is either invalid or expired");
                }

                if ($response['stores'][$coupon[0]['store_id']]['subTotal'] < $coupon[0]['minimum_purchase']) {
                    $error = $this->translate("Minimum cart amount to avail this coupon is " . $coupon[0]['minimum_purchase']);
                }

                // get total products quantity
                $coupontotalProductQuantity = 0;
                foreach ($response['stores'][$coupon[0]['store_id']]['products'] as $couponrow => $couponproduct)
                    $coupontotalProductQuantity += $couponproduct['quantity'];

                if ($coupontotalProductQuantity < $coupon[0]['min_product_quantity']) {
                    $error = $this->translate($value . ":- The products in the cart are less than " . $coupon[0]['min_product_quantity']);
                }

                if (!$directPayment && strlen($error))
                    $response['coupon_error'] = $error;
                elseif($error)
                    $response['stores'][$coupon[0]['store_id']]['coupon_error'] = $error;

                $tobeDiscountedAmount = 0.00;
                if($coupon[0]['product_ids'])
                {
                    $productIds = explode(",", $coupon[0]['product_ids']);
                    foreach($response['stores'][$coupon[0]['store_id']]['products'] as $subkey => $productData)
                    {
                        if(in_array($productData['product_id'], $productIds))
                        {
                            $tobeDiscountedAmount += $productData['price'];
                            if($productData['vat'])
                                $tobeDiscountedAmount -= $productData['vat'];
                        }
                    }
                }

                if(!$error)
                {
                    $discount_type = $coupon[0]['discount_type'];
                    $discount_rate = $discount_amount = $coupon[0]['discount_amount'];

                    if ($discount_type) {
                        $couponData = array('coupon_code' => $coupon[0]['coupon_code'], 'offer_id' => $coupon[0]['offer_id'], 'name' => $this->translate($coupon[0]['title']), 'discount_rate' => $discount_rate, 'discount_value' => $discount_amount, 'discount_type' => (int) $discount_type, 'value' => $discount_amount);
                        $response['stores'][$coupon[0]['store_id']]['total'] -= $discount_amount;
                    } else {
                        if(!$tobeDiscountedAmount)
                                $tobeDiscountedAmount = $response['stores'][$coupon[0]['store_id']]['subTotal'];

                        $discount_amount = floatval($tobeDiscountedAmount / 100) * floatval($discount_amount);

                        $couponData = array('coupon_code' => $coupon[0]['coupon_code'], 'offer_id' => $coupon[0]['offer_id'], 'name' => $this->translate($coupon[0]['title']), 'discount_rate' => $discount_rate, 'discount_value' => $discount_amount, 'discount_type' => '0', 'value' => $discount_amount);
                        $response['stores'][$coupon[0]['store_id']]['total'] -= $discount_amount;
                    }
                    
                    $response['stores'][$coupon[0]['store_id']]['coupon'] = $couponData;
                }
            }
        }

        foreach ($store_ids as $row => $value) {
            if ($isshippingallowed[$value]) {
                $response['stores'][$value]['shipping_method'] = $shippingMethodarray[$value]->getTitle();
                $response['stores'][$value]['shipping_method_price'] = $shippingMethodarray[$value]->handling_fee;
                $response['stores'][$value]['total'] += $response['stores'][$value]['shipping_method_price'];
            }

            $response['grandTotal'] += $response['stores'][$value]['total'];
            $form = array();

            $form[] = array(
                'name' => 'order_note_' . $value,
                'label' => $this->translate("Write a note for your order from this Store."),
                'type' => 'Text',
            );

            $response['stores'][$value]['form'] = $form;
        }

        if (isset($response['grandTotal']))
            $response['totalAmountFields'][$this->translate("Grand Total:")] = $response['grandTotal'];

        $form = array();

        $form[] = array(
            'name' => 'is_private_order',
            'label' => $this->translate(" Make my purchase private. "),
            'type' => 'Checkbox',
        );

        $form[] = array(
            'name' => 'Continue',
            'type' => 'Button',
            'label' => $this->translate("Place Order"),
        );

        $response['form'] = $form;


        $languagePluralStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores");
        $languagePluralStoreProducts = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.slugplural', "products");

        if ($this->getRequest()->isGet()) {
            $this->respondWithSuccess($response, false);
        }

        $orderIds = array();

        if ($this->getRequest()->isPost()) {

            $params = $this->_getAllParams();

            $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $orderProducts = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
            $orderAddress = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
            $orderDownload = Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct');
            $orderComments = Engine_Api::_()->getDbTable('orderComments', 'sitestoreproduct');
            $orderdownpayments = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct');

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            try {
                if ($paymentGatewayId == 3) {
                    if ((isset($params['cheque_number']) && !empty($params['cheque_number'])) && (isset($params['cheque_name']) && !empty($params['cheque_name'])) && (isset($params['cheque_account_number']) && !empty($params['cheque_account_number'])) && (isset($params['cheque_routing_number']) && !empty($params['cheque_routing_number'])))
                    {
                    }
                    else
                        $this->respondWithValidationError('not_approved', array("please cheque params , some of them might be missing"));

                    $cheque_number = $params['cheque_number'];
                    $cheque_name = $params['cheque_name'];
                    $cheque_account_number = $params['cheque_account_number'];
                    $cheque_routing_number = $params['cheque_routing_number'];
                }

                if (isset($cheque_number)) {
                    $orderChequeTable = Engine_Api::_()->getDbtable('Ordercheques', 'sitestoreproduct');
                    $neworderCheque = $orderChequeTable->createRow();
                    $neworderCheque->cheque_no = $cheque_number;
                    $neworderCheque->customer_signature = $cheque_name;
                    $neworderCheque->account_number = $cheque_account_number;
                    $neworderCheque->bank_routing_number = $cheque_routing_number;
                    $neworderCheque->save();
                    $neworderChequeId = $neworderCheque->ordercheque_id;
                }


                // save order table
                // GET IP ADDRESS
                $ipObj = new Engine_IP();
                $ipExpr = $ipObj->toString();

                if ($params['payment_gateway'] == 3) {
                    $order_status = 0;  // APPROVAL PENDING
                    $payment_status = 'initial';
                } else if ($params['payment_gateway'] == 5) {
                    $order_status = 2;  // PROCESSING
                    $payment_status = 'active';
                } else {
                    $order_status = 1;  // PAYMENT PENDING
                    $payment_status = 'initial';
                }

                // create order by store
                // parent order is required in case of direct payment to site admin , all orders get a parent id and the total of all the orders including the parentid is paid by the customer.

                $parent_order_id = 0;
                $parent_store_id = 0;

                $datacount =0;
                $productids = array();
                foreach ($store_ids as $row => $store_id) {
                    $storeinfo = $response['stores'][$store_id];

                    $newOrder = $orderTable->createRow();

                    if ($viewer_id)
                        $newOrder->buyer_id = $viewer_id;
                    else
                        $newOrder->buyer_id = 0;

                    $newOrder->store_id = $store_id;
                    $newOrder->order_status = $order_status;
                    $newOrder->item_count = 0;
                    $newOrder->ip_address = $ipExpr;
                    $newOrder->sub_total = $response['stores'][$store_id]['subTotal'];
                    $newOrder->payment_status = $payment_status;
                    $newOrder->gateway_id = $params['payment_gateway'];
                    $newOrder->grand_total = $response['stores'][$store_id]['total'];
                    $newOrder->order_note = ($params['order_note_' . $store_id]) ? $params['order_note_' . $store_id] : null;
                    $newOrder->direct_payment = $directPayment;
                    $newOrder->is_downpayment = $isDownPaymentEnable;
                    $newOrder->is_private_order = (int) $params['is_private_order'];

                    if (isset($neworderChequeId))
                        $newOrder->cheque_id = $neworderChequeId;

                    if ($isshippingallowed[$store_id]) {
                        $shippingMethod = $shippingMethodarray[$store_id];
                        if ($shippingMethod->handling_type) {
                            $handlingFee = ((float) $total / 100) * $shippingMethod->handling_fee;
                            $newOrder->shipping_price = $handling_fee;
                        } else
                            $newOrder->shipping_price = $shippingMethod->handling_fee;

                        $newOrder->shipping_title = $shippingMethod->title;
                        $newOrder->delivery_time = $shippingMethod->delivery_time;
                    }

                    $newOrder->is_private_order = (isset($params['is_private_order']) && !empty($params['is_private_order'])) ? 1 : 0;
                    
                    if($isVatAllow)
                        $newOrder->store_tax = (float)$response['stores'][$store_id]['totalVat'];
                    else
                    {

                        $newOrder->admin_tax = (float)$response['stores'][$store_id]['total_admin_tax'];
                        $newOrder->store_tax = (float)$response['stores'][$store_id]['total_store_tax'];
                    }
                    $newOrder->save();

                    if(!$parent_order_id)
                        $parent_order_id = $newOrder->getIdentity();

                    $newOrder->parent_id = $parent_order_id;
                    $newOrder->save();

                    if ($datacount == 0) {
                        $parent_order_id = $order_id = $newOrder->getIdentity();
                        $sql = "update engine4_sitestoreproduct_order_addresses set order_id='".$parent_order_id."' where orderaddress_id='".$billingAddress_id."' ";
                        $db->query($sql);

                        if ($isshippingallowed[$store_id]) {
                            $sql = "update engine4_sitestoreproduct_order_addresses set order_id='".$parent_order_id."' where orderaddress_id='".$shippingAddress_id."' ";
                            $db->query($sql);
                        }
                    } else {
                        $order_id = $newOrder->getIdentity();

                        // Start: set order address for next store
                        $newBillingOrderAddress = $orderAddressTable->createRow();
                        unset($billingAddress[0]['orderaddress_id']);
                        $newBillingOrderAddress->setFromArray($billingAddress[0]);
                        $newBillingOrderAddress->order_id = $order_id;
                        $newBillingOrderAddress->save();

                        if ($isshippingallowed[$store_id]) {
                            $newShippingAddress = $orderAddressTable->createRow();
                            unset($shippingAddress[0]['orderaddress_id']);
                            $newShippingAddress->setFromArray($shippingAddress[0]);
                            $newShippingAddress->order_id = $order_id;
                            $newShippingAddress->save();
                        }

                        // End: set order address for next store
                    }

                    // billinng address for multiple store remaining in address action
                    // billing and shipping address work
                    // billing and shipping address work

                    foreach ($storeinfo['products'] as $key => $orderproductdata) {

                        $productids[] = $orderproductdata['product_id'];

                        $product = Engine_Api::_()->getItem("sitestoreproduct_product", $orderproductdata['product_id']);
                        $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $orderproductdata['product_id']);
                        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $orderproductdata['product_id']);

                        $newOrderProduct = $orderProducts->createRow();
                        $newOrderProduct->order_id = $order_id;
                        $newOrderProduct->product_id = $orderproductdata['product_id'];
                        $newOrderProduct->product_title = serialize(array('title' => $sitestoreproduct->getTitle()));
                        $newOrderProduct->product_sku = $sitestoreproduct->getSlug();
                        $newOrderProduct->price = $orderproductdata['price'];
                        $newOrderProduct->quantity = $orderproductdata['quantity'];

                        if (isset($orderproductdata['downPaymentPrice']) && !empty($orderproductdata['downPaymentPrice']))
                            $newOrderProduct->downpayment = $orderproductdata['downPaymentPrice'];

                        // tax detail of product
                        if (isset($orderproductdata['tax_detail']) && !empty($orderproductdata['tax_detail'])) {
                            $newOrderProduct->tax_title = serialize($orderproductdata['tax_detail']);
                            $newOrderProduct->tax_amount = $orderproductdata['tax'];
                        }

                        if(isset($orderproductdata['vat']) && !empty($orderproductdata['vat']))
                        {
                            $newOrderProduct->tax_title = serialize('VAT');
                            $newOrderProduct->tax_amount = $orderproductdata['vat'];
                        }

                        if (isset($orderproductdata['configuration']) && !empty($orderproductdata['configuration']))
                            $newOrderProduct->configuration = Zend_Json::encode($orderproductdata['configuration']);

                        if (isset($orderproductdata['config_info']) && !empty($orderproductdata['config_info']))
                            $newOrderProduct->config_info = serialize($orderproductdata['config_info']);

                        $newOrderProduct->save();

                        // add files in order_downloads if the product is downloadable
                        if ($product->product_type == 'downloadable') {
                            $downloadsPaginator = Engine_Api::_()->getDbTable('Downloadablefiles', 'sitestoreproduct')->getDownloadableFilesPaginator(array('product_id' => $product->getIdentity(), 'type' => 'main'));

                            foreach ($downloadsPaginator as $downloadfile) {
                                $newOrderDownload = $orderDownload->createRow();
                                $newOrderDownload->order_id = $newOrder->order_id;
                                $newOrderDownload->store_id = $product->store_id;
                                $newOrderDownload->product_id = $product->getIdentity();
                                $newOrderDownload->downloadablefile_id = $downloadfile->getIdentity();
                                $newOrderDownload->max_downloads = $downloadfile->download_limit;
                                $newOrderDownload->downloads = 0;
                                $newOrderDownload->creation_date = date('Y-m-d H:i:s');
                                $newOrderDownload->save();
                            }
                        }
                    }
                    
                    if (isset($response['stores'][$store_id]['coupon']) && !empty($response['stores'][$store_id]['coupon'])) {
                        $newOrder->coupon_detail = serialize(array('coupon_code' => $response['stores'][$store_id]['coupon']['coupon_code'], 'coupon_amount' => $response['stores'][$store_id]['coupon']['discount_value']));
                        $newOrder->save();

                        // entry inside ordercoupons
                        $newordercoupon = $ordercouponsTable->createRow();
                        $newordercoupon->coupon_id = $response['stores'][$store_id]['coupon']['offer_id'];
                        $newordercoupon->buyer_id = $viewer_id;
                        $newordercoupon->store_id = $store_id;
                        $newordercoupon->creation_date = date('Y-m-d H:i:s');
                        $newordercoupon->save();

                        // update claims count on coupons table 
                        $sql = "update " . $couponTable->info('name') . " set claimed=claimed+1 where offer_id=".$response['stores'][$store_id]['coupon']['offer_id'];
                        $db->query($sql);
                    }
                    elseif(isset($response['coupon']) && !empty($response['coupon'])) {
                        $newOrder->coupon_detail = serialize(array('coupon_code' => $response['coupon']['coupon_code'], 'coupon_amount' => $response['coupon']['discount_value']));
                        $newOrder->save();

                        // entry inside ordercoupons
                        $newordercoupon = $ordercouponsTable->createRow();
                        $newordercoupon->coupon_id = $response['coupon']['offer_id'];
                        $newordercoupon->buyer_id = $viewer_id;
                        $newordercoupon->store_id = $store_id;
                        $newordercoupon->creation_date = date('Y-m-d H:i:s');
                        $newordercoupon->save();

                        // update claims count on coupons table 
                        $sql = "update " . $couponTable->info('name') . " set claimed=claimed+1 where offer_id=".$response['coupon']['offer_id'];
                        $db->query($sql);
                    }
                        
                    $orderIds[]['order_id'] = $newOrder->getIdentity();

                    // Commission Data

                    $commission = Engine_Api::_()->sitestoreproduct()->getOrderCommission($store_id);
                    $commission_type = $commission[0];
                    $commission_rate = $commission[1];
                    $newOrder->commission_type = $commission_type;
                    $newOrder->commission_rate = $commission_rate;
                    if($commission_type==0)
                        $newOrder->commission_value = $commission_rate;
                    else
                        $newOrder->commission_value = (@round($response['stores'][$store_id]['subTotal'], 2) * $commission_rate) / 100;

                    $newOrder->save();
                    
                    ++$datacount;

                }
                // remove store products or empty cart
                    if($viewer_id)
                    {
                        if ($checkout_store_id) {
                            foreach ($cartProducts as $row => $value) {
                                $product = $value['product_id'];
                                $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $product);
                                if($product && !$product->stock_unlimited)
                                {
                                    $product->in_stock -= $value['quantity'];
                                    $product->save();
                                }
                                $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $value['cartproduct_id']);
                                $cartProduct->delete();
                            }
                        } else
                        {
                            foreach ($cartProducts as $row => $value) {
                                $product = $value['product_id'];
                                $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $product);
                                if($product && !$product->stock_unlimited)
                                {
                                    $product->in_stock -= $value['quantity'];
                                    $product->save();
                                }
                            }
                            //Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cart_obj->getIdentity());
                            $sql = "delete from engine4_sitestoreproduct_cartproducts where cart_id=".$cart_obj->getIdentity();
                            $db->query($sql);

                            $sql = "delete from engine4_sitestoreproduct_carts where cart_id=".$cart_obj->getIdentity();
                            $db->query($sql);
                        }
                    }
                    // remove store products or empty cart ends
                $db->commit();
                
                Engine_Api::_()->getApi('Siteapi_Core', 'sitestoreproduct')->orderPlaceMailAndNotification($orderIds);
                if ($paymentGatewayId == 1 || $paymentGatewayId == 2 || $paymentGatewayId>4) {
                        // code for paypal url
                        $orderEncrypt = Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($parent_order_id);
                        $store_id = Engine_Api::_()->getItem("sitestoreproduct_order" , $parent_order_id)->store_id;
                        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
                        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                        $baseUrl = @trim($baseUrl, "/");
                        $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);

                        $url = $getHost . '/' . $baseUrl .'/'. $languagePluralStore. '/'.$languagePluralStoreProducts."/payment";

                        if($checkout_store_id)
                            $url .= "/store_id/".$checkout_store_id;

                        $url .= "/gateway_id/" . $paymentGatewayId . "/order_id/" . $orderEncrypt;
                        if($viewer_id)
                            $this->respondWithSuccess(array('payment_url' => $url), false);
                        else
                            $this->respondWithSuccess(array('payment_url' => $url,'productids' => $productids), false);
                }

                if($viewer_id)
                    $this->successResponseNoContent('no_content');
                else
                    $this->respondWithSuccess(array('productids'=>$productids),true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
     * Get states based on country
     */

    public function statesAction() {
        $country = $this->_getParam('country');

        if (!$country)
            $this->respondWithValidationError('parameter_missing', " parameter named 'country' missing ");

        $regionTable = Engine_Api::_()->getDbTable('Regions', 'sitestoreproduct');
        $regionTableName = $regionTable->info('name');

        $select = $regionTable->select()
                ->from($regionTableName)
                ->where("country = ?", $country);
        $result = $select->query()->fetchAll();        

        $response = array();
        if ($result) {
            $response['0'] = $this->translate('--- select ---');

            foreach ($result as $row => $value) {
                if (isset($value['region']) && !empty($value['region']))
                    $response[$value['region_id']] = $this->translate($value['region']);
            }
        }

        $this->respondWithSuccess($response, false);
    }

    /*
     * cart details 
     */

    private function getCartProducts($cart_obj, $checkout_store_id) {
        if (!$cart_obj)
            return;

        $cartProductTable = Engine_Api::_()->getDbTable('Cartproducts', 'sitestoreproduct');
        $cartProductTableName = $cartProductTable->info('name');
        $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
        $productTableName = $productTable->info('name');

        $select = $cartProductTable->select()
                ->from($cartProductTableName)
                ->joinInner($productTableName, "$productTableName . product_id = $cartProductTableName . product_id", array(''));
        if ($checkout_store_id)
            $select->where($productTableName . ".store_id = ?", $checkout_store_id);

        $select->where($cartProductTableName . ".cart_id = ?", $cart_obj->getIdentity())
                ->order($cartProductTableName . ".cartproduct_id asc");

        $result = $select->query()->fetchALL();

        return $result;
    }

    /*
     * get cart 
     */

    private function sitestoreproduct_checkout_viewer_cart() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $tempUserCart = $cart_obj = null;

        if (!$viewer_id)
            return $this->getLoggedOutUserCart();
        else
            $cart_obj = $this->getLoggedInUserCart($viewer_id);

        // GETCHECKOUTVIEWERCART  
        $sitestoreproduct_checkout_viewer_cart = $this->getcheckoutviewercart($viewer_id, $tempUserCart, $cart_obj);
        return $sitestoreproduct_checkout_viewer_cart;
    }

    // get the logged out user cart
    private function getLoggedOutUserCart() {
        if (!isset($_GET['productsData']) || empty($_GET['productsData']))
            $this->respondWithValidationError('parameter_missing', 'productsData missing');
        $productsArray = Zend_Json::decode(urldecode($_GET['productsData']));
        $cartProducts = array();
        $store_id = $this->_getParam('store_id', null);
        foreach ($productsArray as $row => $value) {
            $product = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);
            if($store_id==$product->store_id || !$store_id)
            {
                $cartProducts[] = $value;
            }
        }
        return $cartProducts;
    }

    private function getLoggedInUserCart($viewer_id) {
        $cart_obj = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->fetchRow(array('owner_id = ?' => $viewer_id));

        if (empty($cart_obj) || empty($cart_obj->cart_id)) {
            $this->respondWithValidationError('not_approved', 'Cart Empty');
        }
        return $cart_obj;
    }

    private function getcheckoutviewercart($viewer_id, $tempUserCart, $cart_obj) {

        $checkout_store_id = $this->_getParam('store_id');

        $isPaymentToSiteEnable = $this->isPaymentToSIteEnable();
        // IF DIRECT PAYMENT MODE IS ENABLED, THEN FETCH ONLY THAT STORE PRODUCTS
        // if (empty($isPaymentToSiteEnable))
        if(isset($checkout_store_id) && !empty($checkout_store_id))
            $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id, array('store_id' => $checkout_store_id));
        else
            $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id);

        return $sitestoreproduct_checkout_viewer_cart;
    }

    /*
     * Checks whether is payment t siet enabled
     */

    private function isPaymentToSIteEnable() {
        $isPaymentToSiteEnable = true;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }
        return $isPaymentToSiteEnable;
    }

    /*
     * Gets the payment type
     */

    private function getPaymentType($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    /*
     * Get payment authorization
     */

    public function getPaymentAuth($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

    /* check for show products */

    private function checkShowShipping($store_id = null) {
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $sitestoreproduct_checkout_viewer_cart = $this->sitestoreproduct_checkout_viewer_cart();

        if (empty($sitestoreproduct_checkout_viewer_cart))
            $this->respondWithValidationError('not_approved', $this->translate("No products in cart"));

        $can_show_shipping_address = 0;

        $isSellingAllowedProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($value['store_id']);
        $showShippingAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.virtual.product.shipping', 1);

        $totalcartArraycount = count($sitestoreproduct_checkout_viewer_cart);

        foreach ($sitestoreproduct_checkout_viewer_cart as $cart) {

            $product_id = $cart['product_id'];

            if(!isset($cart['product_id']) || empty($cart['product_id']))
                continue;

            $product = Engine_Api::_()->getItem("sitestoreproduct_product" , $cart['product_id'] );

            if($store_id && $product->store_id!=$store_id)
                continue;

            $otherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($product->getIdentity());

            if ($product->product_type == 'bundled') {
                $bundleProductInfo = @unserialize($otherinfo->product_info);
                if ($bundleProductInfo['enable_shipping'])
                    $can_show_shipping_address = 1;
            }
            elseif ($product->product_type == 'configurable' || $product->product_type == 'simple' || $product->product_type == 'grouped')
                $can_show_shipping_address = 1;
        }

        if ($showShippingAddress && $can_show_shipping_address)
            $can_show_shipping_address = 1;
        else
            $can_show_shipping_address = 0;

        if($store_id)
            return $can_show_shipping_address;
        else
            $this->_showshippingaddress = $can_show_shipping_address;
    }

    /*
    * Get configuration price for logged out user case
    */
    public function getConfigPrice($params)
    {
        if(!$params || !is_array($params))
            return 0.00;

        $combinationAttributesTable = Engine_Api::_()->getDbTable('CombinationAttributes', 'sitestoreproduct');
        $cartProductFieldsOptions = Engine_Api::_()->getDbtable('CartproductFieldOptions', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('CombinationAttributeMap', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');

        $configuration_price = 0.00;

        foreach($params as $row => $value)
        {
            if($row=='combination_id')
            {
                $select = $combinationAttributeMapsTable->select()
                                                        ->from($combinationAttributeMapsTableName)
                                                        ->setIntegrityCheck(false)
                                                        ->joinInner($combinationAttributesTableName, $combinationAttributeMapsTableName . ".attribute_id = " . $combinationAttributesTableName . ".attribute_id")
                                                        ->where("$combinationAttributeMapsTableName.combination_id = ?" , $value);

                $result = $select->query()->fetchALL();                

                if(!empty($result))
                {
                    foreach($result as $data => $values)
                    {
                        if($values['price_increment'])
                            $configuration_price += $values['price'];
                        else
                            $configuration_price -= $values['price'];
                    }
                }

                $key = explode('_', $row);

                if(count($key == 3))
                {
                    if(is_array($count))
                    {
                        foreach($value as $row => $multioption)
                        {
                            $select = $cartProductFieldsOptions->select()
                                                                ->where('field_id = ?' , $key[2])
                                                                ->where('option_id = ?' , $multioption);
                            $result = $select->query->fetchAll();

                            if($result)
                            {
                                if($result[0]['price_increment'])
                                    $configuration_price += $result[0]['price'];
                                else
                                    $configuration_price -= $result[0]['price'];
                            }
                        }
                    }
                    elseif(is_array($value))
                    {
                        $select = $cartProductFieldsOptions->select()
                                                                ->where('field_id = ?' , $key[2])
                                                                ->where('option_id = ?' , $multioption);
                        $result = $select->query->fetchAll();

                        if($result)
                        {
                            if($result[0]['price_increment'])
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

}
