<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _order_review.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

$settings = Engine_Api::_()->getApi('settings', 'core');
$isDownPaymentEnable = $settings->getSetting('sitestorereservation.downpayment', 0);
$directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
$isDownPaymentCouponEnable = $settings->getSetting('sitestorereservation.coupon', 0);
$isTermsConditionsEnable = $settings->getSetting('sitestore.terms.conditions', 0);
$isFixedTextEnable = $settings->getSetting('sitestore.fixed.text', 0);
$isVatAllow = $settings->getSetting('sitestoreproduct.vat', 0);
if (!empty($isFixedTextEnable)) {
    $fixedTextValue = $settings->getSetting('sitestore.checkout.fixed.text.value');
}

$productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
$cart_products_detail = @unserialize($this->cart_products_detail);
$stores_product = @unserialize($this->stores_products);
$checkout_process = @unserialize($this->checkout_process);
$checkout_store_name = @unserialize($this->checkout_store_name);
$store_product_types = @unserialize($this->store_product_types);
$address = @unserialize($this->address);
if (!empty($this->coupon_store_id))
    $coupon_store_id = unserialize($this->coupon_store_id);

$grand_total = $downpayment_grand_total = $remaining_downpayment_grand_total = $you_need_to_pay = 0;
if (!empty($directPayment) && !empty($isDownPaymentEnable)) :
    $product_ids = array();
    foreach ($cart_products_detail as $product_id => $productAttribs) :
        $product_ids[] = $product_id;
    endforeach;
    $product_ids = implode(",", $product_ids);
    $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
else:
    $cartProductPaymentType = true;
endif;
?>

<?php $isAllowCoupon = true; ?>
<?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType) && empty($isDownPaymentCouponEnable)) : ?>
    <?php $isAllowCoupon = false; ?>
<?php endif; ?>


<form name="order_place" id="order_place" method="post" action="<?php echo $this->url(); ?>" >

    <?php
    foreach ($stores_product as $store_id => $product_ids):
        $store_grand_total = $store_sub_total = $store_tax = $admin_tax = 0;
        ?>
        <div class="clr">
            <div class="clr cont-sep checkout-subheading b_medium">
                <b><?php echo $checkout_store_name[$store_id]; ?></b>
            </div>
            <div class="clr">
                <table class="store-order-review-table table-stroke widthfull">
                    <thead>
                        <tr class="product_detail_table_head">
                            <th><?php echo $this->translate("Product Name") ?></th>
                            <th class="txt_right"><?php echo $this->translate("Price") ?></th>
                            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                <th class="txt_right"><?php echo $this->translate("Downpayment") ?></th>
                            <?php endif; ?>
                            <th class="txt_center"><?php echo $this->translate("Quantity") ?></th>
                            <?php if (!empty($isVatAllow)): ?>
                                <th class="txt_right"><?php echo $this->translate("VAT") ?></th>
                            <?php else: ?>
                                <th class="txt_right"><?php echo $this->translate("Tax") ?></th>
                            <?php endif; ?>
                            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                <th class="txt_right"><?php echo $this->translate("Downpayment Total") ?></th>
                                <th class="txt_right"><?php echo $this->translate("Remaining Amount Total") ?></th>
                            <?php endif; ?>
                            <th class="txt_right"><?php echo $this->translate("Subtotal") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $order_product_count = 0;
                        $display_store_sub_total = 0;
                        foreach ($product_ids as $product_id):
                            // CALCULATE TAX
                            $store_tax_ids = array();
                            $temp_store_tax = array();
                            $total_product_tax = 0;

                            if (!empty($isVatAllow) && !empty($product_id)):
                                $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                                $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                            endif;

                            if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['vat_amount']) && !empty($cart_products_detail[$product_id]['vat_amount'])) :
                                $vatTitle = $cart_products_detail[$product_id]['vat_title'];
                                $cart_products_detail[$product_id]['product_tax_title'] = serialize($vatTitle);
                                if (isset($cart_products_detail[$product_id]['vat_creator'])) :
                                    if (empty($cart_products_detail[$product_id]['vat_creator'])) :
                                        if ($cart_products_detail[$product_id]['product_type'] != 'configurable' && $cart_products_detail[$product_id]['product_type'] != 'virtual') :
                                            $store_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($cart_products_detail[$product_id]['vat_amount'] * $cart_products_detail[$product_id]['quantity'], 2);
                                        endif;
                                    else:
                                        $admin_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($cart_products_detail[$product_id]['vat_amount'], 2);
                                    endif;
                                endif;
                            elseif (!empty($cart_products_detail[$product_id]['store_tax_id'])) :
                                $store_tax_ids = @implode(',', @unserialize($cart_products_detail[$product_id]['store_tax_id']));
                                $enable_tax = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getCheckoutTaxes($store_tax_ids, $address, $cart_products_detail[$product_id]['product_type']);

                                if (!empty($enable_tax)) :
                                    foreach ($enable_tax as $index => $tax):
                                        if (empty($cart_products_detail[$product_id]['sub_total'])) :
                                            continue;
                                        else:
                                            $product_tax = empty($tax['handling_type']) ? @round($tax['tax_value'], 2) : @round(($cart_products_detail[$product_id]['sub_total'] * @round($tax['tax_value'], $settings->getSetting('sitestoreproduct.rate.precision', 2)) / 100), 2);
                                            $temp_store_tax[$index]['title'] = $tax['title'];
                                            $temp_store_tax[$index]['amount'] = $product_tax;

                                            // ADMIN TAX
                                            if (empty($tax['store_id'])) :
                                                $tax_type = $this->translate('Site Administrators');
                                                $admin_tax += @round($product_tax, 2);
                                            else :
                                                $tax_type = $this->translate('Store Administrators');
                                                $store_tax += @round($product_tax, 2);
                                            endif;

                                            $temp_store_tax[$index]['handling_type'] = $tax['handling_type'];
                                            $temp_store_tax[$index]['tax_value'] = $tax['tax_value'];
                                            $temp_store_tax[$index]['type'] = $tax_type;
                                            $total_product_tax += @round($product_tax, 2);
                                        endif;
                                    endforeach;

                                    $tax_array = @serialize($temp_store_tax);
                                    $cart_products_detail[$product_id]['product_tax_title'] = $tax_array;
                                    $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax;
                                endif;
                            endif;
                            if (!empty($isVatAllow) && $cart_products_detail[$product_id]['product_type'] != 'configurable' && $cart_products_detail[$product_id]['product_type'] != 'virtual') :
                                $store_sub_total += @round($cart_products_detail[$product_id]['sub_total'], 2);
                                if (isset($cart_products_detail[$product_id]['display_sub_total'])):
                                    $display_store_sub_total += @round($cart_products_detail[$product_id]['display_sub_total'], 2);
                                endif;
                            elseif (empty($isVatAllow)):
                                $store_sub_total += @round($cart_products_detail[$product_id]['sub_total'], 2);
                            endif;
                            ?>
                            <?php $order_product_count += $cart_products_detail[$product_id]['quantity'] ?>
                            <?php $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title'); ?>
                            <?php
                            $productTitle = $productTable->getProductAttribute($title_column, array('product_id' => $product_id))->query()->fetchColumn();
                            if (empty($productTitle)):
                                $productTitle = $productTable->getProductAttribute('title', array('product_id' => $product_id))->query()->fetchColumn();
                            endif;
                            ?>
                            <?php if ($cart_products_detail[$product_id]['product_type'] == 'configurable' || $cart_products_detail[$product_id]['product_type'] == 'virtual') : ?>
                                <?php foreach ($cart_products_detail[$product_id]['config'] as $cartproduct_id): ?>
                                    <?php $configuration_price = 0;
                                    $total_price_config = 0; ?>

                                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                    <?php if (!empty($isVatAllow)): ?>
                                        <?php $configProductPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $cartproduct_id); ?>
                <?php endif; ?>
                                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                                    <tr>
                                        <td title="<?php echo $productTitle ?>">
                <?php echo Engine_Api::_()->sitestoreproduct()->truncation($productTitle, 40); ?>

                                            <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS STARTS-->
                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?>&nbsp;
                                                <?php
                                                $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                                                echo '<a class="smoothbox" href=" ' . $spec_url . ' " >' . $this->translate("more info..") . '</a>';
                                                ?>
                                            <?php endif; ?>
                                            <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS ENDS-->
                                            <?php
                                            echo '<br/>';
                                            if (!empty($this->viewer_id)) :
                                                $productOtherInfo = '';
                                                $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartproduct_id);
                                                if ($cart_products_detail[$product_id]['product_type'] == 'virtual' && !empty($cartProductObject->other_info)) :
                                                    $productOtherInfo = unserialize($cartProductObject->other_info);
                                                    if (!empty($productOtherInfo)) :
                                                        echo '<b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($productOtherInfo['starttime']) . '</br>';
                                                        echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($productOtherInfo['endtime']) . '</br>';
                                                    endif;
                                                endif;
//                    $temp_store_tax[$index]['handling_type'] = $tax['handling_type'];
//                    $temp_store_tax[$index]['tax_value'] = $tax['tax_value'];
//                    $temp_store_tax[$index]['type'] = $tax_type;
//                    $total_product_tax += @round($product_tax, 2);

                                                unset($cartProductObject->other_info);
                                                $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($cartProductObject);

                                                /* WORK FOR CONFIGURATION PRICE STARTS */
                                                $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                                                $valueRows = $values->getRowsMatching(array(
                                                    'item_id' => $cartProductObject->getIdentity(),
                                                ));
                                                $configuration_info = Engine_Api::_()->sitestoreproduct()->getConfigurationDetails($valueRows, $product_id);
                                                $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('price', 'price_increment'), $valueRows);
                                                if (!empty($configuration_price)):
                                                    $total_price_config = $cart_products_detail[$product_id]['price'] + $configuration_price;
                                                    $cart_products_detail[$product_id]['configuration_price'] += ($cartProductObject->quantity * $configuration_price);
                                                else:
                                                    $total_price_config = $cart_products_detail[$product_id]['price'];
                                                endif;
                                                /* END WORK FOR CONFIGURATION PRICE */

                                                $otherDetails = $this->storeFieldValueLoop($cartProductObject, $fieldStructure);
                                                if (!empty($otherDetails)) :
                                                    $configDetails = Zend_Json::decode($otherDetails);
                                                    if (!empty($configDetails)) :
                                                        foreach ($configDetails as $key => $makeFieldValue) :
                                                            echo "$key: <b>$makeFieldValue</b><br/>";
                                                        endforeach;
                                                    endif;
                                                endif;
                                                //$categoryAttributeDetails = Engine_Api::_()->sitestoreproduct()->makeCategoryFieldArray($cartProductObject);
//                   if (!empty($categoryAttributeDetails)) :
//                    foreach ($categoryAttributeDetails as $config_key => $makeFieldValue) :
//                      echo "$config_key: <b>$makeFieldValue</b><br/>";
//                    endforeach;
//                   endif;
//                  if(!empty($categoryAttributeDetails)):
//                    $otherDetails = Zend_Json::decode($otherDetails);
//                    $otherDetails = array_merge($otherDetails, $categoryAttributeDetails);
//                    $otherDetails = Zend_Json::encode($otherDetails);
//                  endif;

                                                $cart_products_detail[$product_id]['calendar_date'][] = $productOtherInfo;
                                                $cart_products_detail[$product_id]['config_name'][] = $otherDetails;
                                                $cart_products_detail[$product_id]['config_quantity'][] = $config_product_quantity = $cartProductObject->quantity;
                                                $cart_products_detail[$product_id]['config_info'][] = $configuration_info;
                                                if ($isVatAllow):
                                                    $cart_products_detail[$product_id]['config_price'][] = $configProductPricesArray['product_price_after_discount'];
                                                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount']));
                                                else:
                                                    $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                                                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config));
                                                endif;
                                            else:
                                                $tempCalendarDate = '';
                                                if ($cart_products_detail[$product_id]['product_type'] == 'virtual' && (!empty($cartproduct_id['starttime']) && !empty($cartproduct_id['endtime']))) :
                                                    $tempCalendarDate = array('starttime' => $cartproduct_id['starttime'], 'endtime' => $cartproduct_id['endtime']);
                                                    echo '<b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($cartproduct_id['starttime']) . '</br>';
                                                    echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($cartproduct_id['endtime']) . '</br>';
                                                endif;

                                                /* WORK FOR CONFIGURATION PRICE STARTS */
                                                $configuration_info = Engine_Api::_()->sitestoreproduct()->getConfigurationDetails($cartproduct_id, $product_id);
                                                $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('price', 'price_increment'), $cartproduct_id, 0, 1);
                                                if (!empty($configuration_price)):
                                                    $total_price_config = $cart_products_detail[$product_id]['price'] + $configuration_price;
                                                    $cart_products_detail[$product_id]['configuration_price'] += ($cartproduct_id['quantity'] * $configuration_price);
                                                endif;
                                                /* END WORK FOR CONFIGURATION PRICE */

                                                $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($cartproduct_id);
                                                if (!empty($makeFieldValueArray)) :
                                                    ?>
                                                    <div class="f_small seaocore_txt_light"
                                                             <?php foreach ($makeFieldValueArray as $key => $makeFieldValue) : ?>
                                                             <span><?php echo "$key: <b>$makeFieldValue</b><br/>"; ?></span>
                                                    <?php endforeach; ?>
                                                    </div>
                                                <?php
                                                endif;
                                                $cart_products_detail[$product_id]['calendar_date'][] = $tempCalendarDate;
                                                $cart_products_detail[$product_id]['config_name'][] = Zend_Json::encode($makeFieldValueArray);
                                                $cart_products_detail[$product_id]['config_quantity'][] = $config_product_quantity = $cartproduct_id['quantity'];
                                                $cart_products_detail[$product_id]['config_info'][] = $configuration_info;
//                  $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                                                if ($isVatAllow):
                                                    $cart_products_detail[$product_id]['config_price'][] = $configProductPricesArray['product_price_after_discount'];
                                                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount']));
                                                else:
                                                    $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                                                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config));
                                                endif;
                                            endif;
                                            ?>
                                        </td>
                                        <td class="txt_right">

                                            <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                            <?php if (!empty($isVatAllow)): ?>
                                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['display_product_price']); ?>
                                            <?php else: ?>
                                                <?php if (!empty($total_price_config)) : ?>
                                                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config); ?>
                                                <?php else: ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                                        <?php if ($cart_products_detail[$product_id]['product_type'] == 'virtual' && !empty($cart_products_detail[$product_id]['price_range_text'])) : ?>
                                            <?php echo $this->translate($cart_products_detail[$product_id]['price_range_text']) ?>
                <?php endif; ?>
                                        </td>
                                            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                            <td class="txt_right">
                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                                <?php if (!empty($isVatAllow)): ?>
                                                    <?php $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount'])); ?>               
                                                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) ?>
                                                <?php else: ?>
                                                    <?php if (!empty($total_price_config)) : ?>
                                                        <?php $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config)); ?>
                                                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) ?>
                                                    <?php else : ?>
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment']) ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                                            </td>
                <?php endif; ?>
                                        <td class="txt_center"><?php echo $config_product_quantity; ?></td>

                                        <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS-->
                                        <td class="txt_right" <?php if (!empty($vatTitle)) : echo "title = $vatTitle";
                endif; ?>>
                                            <?php if (!empty($isVatAllow) && !empty($configProductPricesArray)) : ?>
                                                <?php if (isset($productPricesArray['vatShowType'])) : ?>
                                                    <?php echo $productPricesArray['vatShowType']; ?>
                                                <?php else: ?>
                                                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['vat'] * $config_product_quantity); ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                    <?php echo empty($total_product_tax) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) : $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax), array('class' => 'sea_add_tooltip_link', 'rel' => $tax_array)); ?>
                <?php endif; ?>
                                        </td>
                                            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                            <td class="txt_right">

                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                                <?php if (!empty($isVatAllow)): ?>
                                                    <?php $downPaymentAmount = round(Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount'])) * $config_product_quantity, 2); ?>
                                                <?php else: ?>
                                                    <?php if (!empty($total_price_config)) : ?>
                                                        <?php $downPaymentAmount = round(Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config)) * $config_product_quantity, 2); ?>

                        <?php else : ?>
                                                        <?php $downPaymentAmount = @round($cart_products_detail[$product_id]['downpayment'] * $config_product_quantity, 2); ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                    <?php $downPaymentSubTotal += $downPaymentAmount; ?>
                                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount); ?>
                                            </td>
                                            <td class="txt_right">

                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                                <?php if (!empty($isVatAllow)): ?>
                                                    <?php $remainingDownPaymentAmount = round($configProductPricesArray['product_price_after_discount'] * $config_product_quantity - $downPaymentAmount, 2) ?>
                                                <?php else: ?>
                                                    <?php if (!empty($total_price_config)) : ?>
                                                        <?php $remainingDownPaymentAmount = round($total_price_config * $config_product_quantity - $downPaymentAmount, 2) ?>
                        <?php else: ?>
                                                        <?php $remainingDownPaymentAmount = round($cart_products_detail[$product_id]['price'] * $config_product_quantity - $downPaymentAmount, 2) ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                    <?php $remainingDownPaymentSubTotal += $remainingDownPaymentAmount; ?>
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentAmount); ?>
                                            </td>
                                            <?php endif; ?>

                                        <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                        <?php if (!empty($isVatAllow)): ?>
                                            <td class="txt_right">
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['display_product_price'] * $config_product_quantity); ?>
                                            <?php $display_store_sub_total += $configProductPricesArray['display_product_price'] * $config_product_quantity; ?>
                                            </td>
                                        <?php else: ?>
                                            <?php if (!empty($total_price_config)) : ?>
                                                <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config * $config_product_quantity); ?></td>
                    <?php else : ?>
                                                <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price'] * $config_product_quantity); ?></td>
                    <?php endif; ?>
                                    <?php endif; ?>
                                        <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                                    </tr>

                                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                                    <?php if (!empty($isVatAllow)): ?>
                                        <?php $store_sub_total += @round($configProductPricesArray['product_price_after_discount'] * $config_product_quantity, 2); ?>
                                        <?php if (isset($cart_products_detail[$product_id]['display_sub_total'])): ?>
                                            <?php // $display_store_sub_total += @round($configProductPricesArray['display_product_price']*$config_product_quantity, 2); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['vat_amount']) && !empty($cart_products_detail[$product_id]['vat_amount'])) : ?>
                                        <?php $store_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($configProductPricesArray['vat'] * $config_product_quantity, 2); ?>
                                    <?php endif; ?>
                                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                                        <?php endforeach; ?>
        <?php else: ?>
                                <tr>
                                    <td title="<?php echo $productTitle ?>">
                                        <?php echo Engine_Api::_()->sitestoreproduct()->truncation($productTitle, 40); ?>

                                        <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS STARTS-->
                                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?>&nbsp;
                                            <?php
                                            $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                                            echo '<a class="smoothbox" href=" ' . $spec_url . ' " >' . $this->translate("more info..") . '</a>';
                                            ?>
            <?php endif; ?>
                                        <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS ENDS-->


                                    </td>
                                    <td class="txt_right">           
                                        <!--WORK FOR SHOWING PRICE ACC. TO THE VAT SETTING-->
                                        <?php if (!empty($isVatAllow) && !empty($productPricesArray) && isset($productPricesArray['display_product_price'])): ?>
                                            <?php // $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id); ?>
                                            <?php // $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj); ?>

                                            <?php // if (!empty($productPricesArray) && isset($productPricesArray['display_product_price'])): ?>
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price']); ?>
                                            <?php // else: ?>
                                            <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
                                        <?php // endif;  ?>
                                    <?php else: ?>
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                        <td class="txt_right">
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment']); ?>
                                        </td>
                                        <?php endif; ?>
                                    <td class="txt_center"><?php echo $cart_products_detail[$product_id]['quantity']; ?></td>
                                    <td class="txt_right" <?php if (!empty($vatTitle)) : echo "title = $vatTitle";
                            endif; ?>>
                                        <?php if (!empty($isVatAllow)) : ?>
                                            <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) ?>

                                            <?php if (isset($productPricesArray['vatShowType'])) : ?>
                                                <?php echo $productPricesArray['vatShowType']; ?>
                                            <?php else: ?>
                                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['vat'] * $cart_products_detail[$product_id]['quantity']); ?>
                                        <?php endif; ?>

                                        <?php else: ?>
                                            <?php echo empty($total_product_tax) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) : $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax), array('class' => 'sea_add_tooltip_link', 'rel' => $tax_array)); ?>
                                        <?php endif; ?>
                                    </td>
                                        <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                        <td class="txt_right">
                                            <?php $downPaymentSubTotal += $cart_products_detail[$product_id]['downpayment_subtotal']; ?>
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment_subtotal']); ?>
                                        </td>
                                        <td class="txt_right">
                                            <?php $remainingDownPaymentAmount = round($cart_products_detail[$product_id]['sub_total'] - $cart_products_detail[$product_id]['downpayment_subtotal'], 2) ?>
                                            <?php $remainingDownPaymentSubTotal += $remainingDownPaymentAmount; ?>
                                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentAmount); ?>
                                        </td>
                                        <?php endif; ?>
                                    <td class="txt_right">
                                        <?php if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['display_sub_total'])): ?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['display_sub_total']); ?>
                                    <?php //$display_store_sub_total += ($cart_products_detail[$product_id]['display_sub_total']);  ?>
                                <?php else: ?>
                                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['sub_total']); ?>
                                <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (empty($isVatAllow) && !empty($cart_products_detail[$product_id]['configuration_price'])) : ?>
                                        <?php $store_sub_total += $cart_products_detail[$product_id]['configuration_price']; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                        <tr class="product_detail_table_head">
                            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                                <td colspan="8" class="bold txt_right">
        <?php echo $this->translate("Downpayment: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal)); ?>&nbsp&nbsp|&nbsp&nbsp
                                        <?php echo $this->translate("Remaining Amount: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentSubTotal)); ?>&nbsp&nbsp|&nbsp&nbsp
                                        <?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total)); ?>
                                </td>
                                    <?php else: ?>
                                <td colspan="5">
                                    <span class="fright bold">
        <?php if (!empty($isVatAllow)): ?>
                                            <b><?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($display_store_sub_total)); ?></b>
                                <?php else: ?>
                                            <b><?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total)); ?></b>
        <?php endif; ?>
                                    </span>
                                </td>
    <?php endif; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="clr m-cart-items-total checkout-items-total">
                <?php
                if (empty($store_product_types[$store_id])) :
                    $checkout_process['shipping_methods'][$store_id]['delivery_time'] = 0;
                    $checkout_process['shipping_methods'][$store_id]['price'] = 0;
                    $checkout_process['shipping_methods'][$store_id]['title'] = '';
                    $shipping_price = 0;
                else : $shipping_price = $checkout_process['shipping_methods'][$store_id]['price'];
                    ?>
                    <div class="ui-grid-a">
                        <div class="ui-block-a f_small"><?php echo $checkout_process['shipping_methods'][$store_id]['title']; ?></div>
                        <div class="ui-block-b f_small"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($shipping_price) . '<br/>'; ?></div>
                    </div>
    <?php endif; ?>
    <?php if (!empty($isVatAllow)): ?>
                    <div class="ui-grid-a">  
                        <div class="ui-block-a f_small">
        <?php echo $this->translate('Total Products (net):'); ?>
                        </div>
                        <div class="ui-block-b f_small"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total) . '<br/>'; ?> </div>
                    </div>
                        <?php endif; ?>
                <div class="ui-grid-a">  
                    <div class="ui-block-a f_small">
    <?php if (!empty($isVatAllow)): ?>
        <?php echo $this->translate('Total VAT'); ?>
    <?php else: ?>
                    <?php echo $this->translate('Tax'); ?>
                <?php endif; ?>
                    </div>
                    <div class="ui-block-b f_small"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_tax + $admin_tax) . '<br/>'; ?> </div>
                </div>

                <?php if (!empty($isAllowCoupon) && !empty($coupon_store_id) && in_array($store_id, $coupon_store_id)) : ?>
                    <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
                    <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                    <?php if (!empty($couponAmount) && !empty($couponCode)) : ?>
                        <?php $couponDetail = serialize(array('coupon_code' => $couponCode, 'coupon_amount' => $couponAmount)); ?>
                        <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                            <?php if ($downPaymentSubTotal > $couponAmount) : ?>
                                <?php $downPaymentSubTotal -= $couponAmount; ?>
                            <?php else: ?>
                                <?php $downPaymentSubTotal = 0; ?>
                            <?php endif; ?>
                        <?php endif; ?>
            <?php if ($store_sub_total > $couponAmount) : ?>
                <?php $store_sub_total -= $couponAmount; ?>
            <?php else: ?>
                <?php $store_sub_total = 0; ?>
                        <?php endif; ?> 

                        <div class="ui-grid-a">  
                            <div class="ui-block-a f_small"><?php echo $couponCode; ?></div>
                            <div class="ui-block-b f_small"><?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($couponAmount); ?> </div>
                        </div>
        <?php endif; ?>
    <?php endif; ?>

                <?php $store_total = @round($store_sub_total, 2) + @round($store_tax, 2) + @round($shipping_price, 2) + @round($admin_tax, 2); ?>

                <div class="ui-grid-a">
                    <div class="ui-block-a f_small"><b><?php echo $this->translate('Total'); ?></b></div>
                    <div class="ui-block-b f_small"><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total) . '<br/>'; ?></b></div>
                </div>
    <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
        <?php $you_need_to_pay += @round($downPaymentSubTotal, 2) + @round($shipping_price, 2) + @round($admin_tax, 2) + @round($store_tax, 2); ?>
                    <div class="ui-grid-a">  
                        <div class="ui-block-a f_small"><b><?php echo $this->translate('Payable Amount'); ?></b></div>
                        <div class="ui-block-b f_small">
                            <b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal + $store_tax + $admin_tax + $shipping_price) . '<br/>'; ?></b>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cont-sep t_l b_medium"></div>

            <div class="clr">
    <?php if (!empty($isTermsConditionsEnable)) : ?>
                    <section>
                            <?php $termsConditionsUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'terms-conditions-details', 'store_id' => $store_id), 'default', true); ?>
                        <div class="mbot5"><?php echo $this->translate("Terms and Conditions") ?><span class="seaocore_txt_red">*</span></div>
                        <input id="terms_conditions_agree_<?php echo $store_id ?>" type="checkbox" />
                        <label for="terms_conditions_agree_<?php echo $store_id ?>"> <?php echo $this->translate('I agree with the %1$sterms and conditions%2$s.', '<a href="javascript:void(0);" onclick="Smoothbox.open(\'' . $termsConditionsUrl . '\')">', '</a>') ?></label>
                        <span id="terms_conditions_error_<?php echo $store_id ?>" style="display: none" class="seaocore_txt_red">
        <?php echo $this->translate("Please agree with terms & conditions."); ?>
                        </span>
                    </section>
    <?php endif; ?>

                <a href="javascript:void(0)" onclick="orderNote(<?php echo $store_id; ?>)" class="f_small ui-link"><?php echo $this->translate("Write as note for your order from this store.") ?></a>
                <article id="note_<?php echo $store_id; ?>" style="display: none" class="mbot10" >
                    <textarea id="order_note_<?php echo $store_id; ?>" name="order_note[<?php echo $store_id; ?>]"></textarea>
                </article>
            </div>
        </div>

        <div class="cont-sep t_l b_medium"></div>
        <?php
        // COMMISSION
        $commission = Engine_Api::_()->sitestoreproduct()->getOrderCommission($store_id);
        $commission_type = $commission[0];
        $commission_rate = $commission[1];

        // IF COMMISSION VALUE IS FIX.
        if ($commission_type == 0) :
            $commission_value = $commission_rate;
        else :
            $commission_value = (@round($store_sub_total, 2) * $commission_rate) / 100;
        endif;

        // SAVE ALL FIELDS VALUE FOR A STORE
        $checkout_process[$store_id]['item_count'] = $order_product_count;
        $checkout_process['payment'][$store_id]['sub_total'] = @round($store_sub_total, 2);
        $checkout_process['payment'][$store_id]['store_tax'] = @round($store_tax, 2);
        $checkout_process['payment'][$store_id]['admin_tax'] = @round($admin_tax, 2);
        $checkout_process['payment'][$store_id]['commission_type'] = $commission_type;
        $checkout_process['payment'][$store_id]['commission_rate'] = @round($commission_rate, 2);
        $checkout_process['payment'][$store_id]['commission_value'] = @round($commission_value, 2);
        $checkout_process['payment'][$store_id]['grand_total'] = @round($store_total, 2);
        $grand_total += @round($store_total, 2);
    endforeach;
    $checkout_process['grand_total'] = @round($grand_total, 2);
    ?>
<?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
        <!--  <div class="fright m10 clr" style="margin-top:0;">
              <div class="invoice_ttlamt_box_wrap mbot10 o_hidden" style="width:300px;">
                <div class="invoice_ttlamt_box">
                  <div class="clr">  
                    <div class="invoice_order_info fleft"><?php echo $this->translate("Downpayment Grand Total:") ?></div>
                    <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downpayment_grand_total) ?><br> </div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate("Remaining Amount Grand Total:") ?></div>
                    <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remaining_downpayment_grand_total) ?></div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate("Amount Need to pay:") ?></div>
                    <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remaining_downpayment_grand_total) ?></div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft bold"><?php echo $this->translate("Grand Total:") ?></div>
                    <div class="fright bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total) ?></div>
                  </div>
                </div>
              </div>
            </div>-->

        <h3 class="clr fright pright10 mright10">
        <?php echo $this->translate("You need to pay: ") . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($you_need_to_pay); ?>
        </h3>
<?php endif; ?>

    <!--     <h3 class="clr fright pright10 mright10">
                <?php // echo $this->translate("Grand Total: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total));  ?>
      </h3>-->

    <div class="clr m-cart-items-total checkout-items-total"> 
        <div class="ui-grid-a">
            <div class="ui-block-a f_small">
<?php echo $this->htmlLink(array("action" => "cart", "route" => "sitestoreproduct_product_general"), $this->translate("Edit your cart"), array('class' => 'buttonlink sitestore_icon_edit_cart')); ?>           
            </div>
            <div class="ui-block-b">
                <b><?php echo $this->translate("Grand Total: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total)); ?> </b>
            </div>
        </div>
    </div>  
    <input type="hidden" id ="sitestoreproduct_address" name="sitestoreproduct_address" />
    <script type="text/javascript">
        sm4.core.runonce.add(function () {
            $.mobile.activePage.find('#sitestoreproduct_address').attr('value', sitestoreproduct_address);
        });
    </script>
    <div class="clr">

        <div id="checkout_place_order_error" class="r_text"></div>
        <div class='clr'>
            <span class="clr dblock mtop5">
                <input type="checkbox" id="isPrivateOrder" name="isPrivateOrder">
                <label for="isPrivateOrder"><?php echo $this->translate("Make my purchase private.") ?></label>
            </span>  
            <button type="button" data-theme="b" name="place_order" onclick="placeOrder()" class=""><?php echo $this->translate("Place Order") ?></button>
            <div id="loading_image_5" class="t_center clr"></div>
        </div>
    </div>
</form>

<script type="text/javascript">

    function orderNote(id)
    {
        $.mobile.activePage.find('#note_' + id).show();
    }

    function placeOrder()
    {
        var index = 0;
        var order_note = new Array(<?php echo count($stores_product) * 2; ?>);
        var agreeTermsConditions = true;
<?php foreach ($stores_product as $store_id => $value): ?>
            order_note[index++] = <?php echo $store_id ?>;
            order_note[index++] = $('order_note_' +<?php echo $store_id ?>).value;
    <?php if (!empty($isTermsConditionsEnable)) : ?>
                $("#terms_conditions_error_<?php echo $store_id ?>").css("display", "none");

                if (!$.mobile.activePage.find('#terms_conditions_agree_<?php echo $store_id ?>').is(':checked')) {
                    $("#terms_conditions_error_<?php echo $store_id ?>").css("display", "block");
                    agreeTermsConditions = false;
                }
    <?php endif; ?>
<?php endforeach; ?>

        if (!agreeTermsConditions)
            return;

        var isPrivateOrder = $.mobile.activePage.find('#isPrivateOrder').is(':checked');
        var placeOrderUrl;
<?php if (!empty($this->checkoutStoreId)) : ?>
            placeOrderUrl = "sitestoreproduct/index/place-order/store_id/<?php echo $this->checkoutStoreId ?>";
<?php else: ?>
            placeOrderUrl = 'sitestoreproduct/index/place-order';
<?php endif; ?>
        sm4.core.request.send({
            url: sm4.core.baseUrl + placeOrderUrl,
            method: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $.mobile.activePage.find('#loading_image_5').html('<img src=' + sm4.core.staticBaseUrl + 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif />');
            },
            data: {
                format: 'json',
                checkout_process: '<?php echo serialize($checkout_process); ?>',
                order_note: order_note.join('::@::'),
                cart_products_detail: '<?php echo @serialize($cart_products_detail); ?>',
                cart_products_exist: '<?php echo $this->paymentRequest; ?>',
                sitestoreproduct_address: $.mobile.activePage.find('#sitestoreproduct_address').val(),
                sitestoreproduct_downloadable_product: '<?php echo $this->sitestoreproduct_downloadable_product; ?>',
                cartProductPaymentType: '<?php echo $cartProductPaymentType ?>',
                isPrivateOrder: isPrivateOrder
            },
            success: function (responseJSON)
            {
                $.mobile.activePage.find('#loading_image_5').html('');
                if (responseJSON.return_sitestoreproduct_manage_cart)
                {
                    $.mobile.changePage('<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true) ?>');
                }
                if (responseJSON.checkout_place_order_error)
                {
                    $.mobile.activePage.find('#checkout_place_order_error').html(responseJSON.checkout_place_order_error);
                    return;
                }
                if (responseJSON.parent_id)
                {
                    if (responseJSON.gateway_id == 1 || responseJSON.gateway_id == 2 || $.inArray(responseJSON.gateway_id, otherPaymentGateways) !== -1)
                    {
<?php if (!empty($this->checkoutStoreId)) : ?>
    <?php $payment_url = $this->url(array('action' => 'payment', 'store_id' => $this->checkoutStoreId), 'sitestoreproduct_general', true) ?>
<?php else: ?>
    <?php $payment_url = $this->url(array('action' => 'payment'), 'sitestoreproduct_general', true) ?>
<?php endif; ?>
                        $.mobile.changePage('<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/order_id/' + responseJSON.parent_id);
                    }
                    else
                    {
                        $.mobile.changePage('<?php echo $this->url(array('action' => 'success'), 'sitestoreproduct_general', true) ?>/success_id/' + responseJSON.parent_id);
                    }
                }

            }
        }
        );
    }

</script>