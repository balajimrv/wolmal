<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $isAllowVAT = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0); ?>
<?php if (!empty($this->cart_update_suyccessfully)): ?>
    <ul class="form-notices">
        <li>
            <?php echo $this->translate("Cart Updated Successfully."); ?>
        </li>
    </ul>
<?php endif; ?>

<?php
$tempCartInfoForCoupon = array();
$continue_shopping_url = $this->url(array("action" => "home"), "sitestoreproduct_general", true);

//IF USER CART IS EMPTY
if (!empty($this->sitestoreproduct_viewer_cart_empty)):
    ?>
    <div class="m-cart-head">
        <h3><?php echo $this->translate("Shopping Cart is Empty") ?></h3>
    </div>
    <div class="tip">
        <span>
            <?php echo $this->translate('You have no items in your shopping cart. %s to continue shopping.', '<a href="' . $continue_shopping_url . '">' . $this->translate('Click here') . '</a>'); ?>
        </span>
    </div>
    <?php
    return;
endif;
?>
<?php $isAllowCoupon = true; ?>
<?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType) && empty($this->isDownPaymentCouponEnable)) : ?>
    <?php $isAllowCoupon = false; ?>
<?php endif; ?>
<?php if (!empty($this->isProductAddedInCart) && !empty($this->productAddErrorMessage)) : ?>
    <span class="seaocore_txt_red">
        <?php echo $this->productAddErrorMessage; ?>
    </span>
<?php endif; ?>


<?php if (!empty($this->store_product_id)) : ?>
    <?php $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct'); ?>
    <form id='viewer_cart' method="post" action="<?php echo $this->url(array('action' => 'cart'), "sitestoreproduct_product_general", true) ?>">
        <div class='buttons widthfull mbot10 mtop10 clr'>
            <div class="m-cart-head"><h3><?php echo $this->translate("Shopping Cart") ?></h3></div>  
            <?php if (!empty($this->isPaymentToSiteEnable)) : ?>
                <button type='button' onclick="proceedToCheckout()" id="update_shopping_cart" class="fright"><?php echo $this->translate("Proceed to Checkout") ?></button>
            <?php endif; ?>

        </div>

        <?php
        $grand_total = 0;
        $downpayment_grand_total = 0;
        $final_store_total = $final_downpayment_store_total = $store_config_price = $store_config_price_total = array();
        ?>

        <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
            <?php $colspanValue = 9; ?>
            <?php $tempDivClassName = 'downpayment_enab' ?>
        <?php else: ?>
            <?php $colspanValue = 7; ?>
            <?php $tempDivClassName = '' ?>
        <?php endif; ?>


        <div id="manage_order_tab" class="clr">
            <div class="m-cart widthfull">
                <ul data-role="listview" data-inset="false" data-icon="false" >
                    <?php foreach ($this->manage_cart_store_name as $store_id => $name): ?>
                        <?php $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id); ?>
                        <li data-role="list-divider" role="heading"  class="clr">
                            <b><?php echo $this->htmlLink($storeObj->getHref(), $storeObj->title, array('title' => $storeObj->title, 'class' => 'seaocore_link_inherit')); ?></b>
                        </li>
                        <li class="clr o_hidden">
                            <?php
                            $store_total = $temp_product_id = $downPaymentSubTotal = $temp_store_payment_amount = $storeOnlineThresholdMessage = $store_vat_total = $store_grand_total = $display_store_total = $store_products_total_net = 0;
                            $errorArray = $this->error;

                            foreach ($this->store_product_id[$store_id] as $product_id):
                                $product_config_price = array();
                                foreach ($this->product_obj as $product_detail):
                                    $configuration_price = 0;
                                    if ($product_id == $product_detail->product_id) :
                                        if ($temp_product_id != $product_id) :
                                            $temp_product_id = $product_id;
                                            $index = 0;
                                        else:
                                            ++$index;
                                        endif;
                                        if (empty($this->viewer_id)):
                                            if (is_array($this->store_product_quantity[$product_id]) && !isset($this->store_product_quantity[$product_id][$index])):
                                                continue;
                                            endif;
                                        endif;
//                          $discounted_price = $this->productDiscountedPrice[$product_id];
                                        $productPricesArray = array();
                                        $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                                        if ($product_obj->product_type == 'configurable' || $product_obj->product_type == 'virtual'):
                                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]);
                                            if (empty($this->viewer_id)):
                                                $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $this->viewerCartConfig[$product_id][$index]);
                                            endif;
                                        else:
                                            $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                                        endif;

                                        if (!empty($productPricesArray)):
                                            $discounted_price = $productPricesArray['product_price_after_discount'];
                                        else:
                                            $discounted_price = $this->productDiscountedPrice[$product_id][$index];
                                        endif;

                                        // CALCULATE QUANTITY
                                        if (empty($this->viewer_id)):
                                            if (is_array($this->store_product_quantity[$product_id])) :
                                                $quantity = $this->store_product_quantity[$product_id][$index];
                                            else :
                                                $quantity = $this->store_product_quantity[$product_id];
                                            endif;
                                        else:
                                            $quantity = $this->store_product_quantity[$this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]]['quantity'];
                                        endif;

                                        /* WORK FOR CONFIGURATION PRICE 
                                          if (!empty($this->viewer_id) && ($product_detail->product_type == 'configurable' || $product_detail->product_type == 'virtual')) :
                                          $cartProductId = $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index];
                                          $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId);
                                          $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                                          $valueRows = $values->getRowsMatching(array(
                                          'item_id' => $cartProductObject->getIdentity(),
                                          ));
                                          $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice(array('price', 'price_increment'), $valueRows);

                                          elseif (empty($this->viewer_id) && ($product_detail->product_type == 'configurable' || $product_detail->product_type == 'virtual')):

                                          $fieldArray = $this->viewerCartConfig[$product_id][$index];
                                          $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice(array('price', 'price_increment'), $fieldArray, 0, 1);
                                          endif;

                                          if(!empty($configuration_price) && empty($productPricesArray)):
                                          $discounted_price  += $configuration_price;
                                          $store_config_price[$store_id] += $configuration_price;
                                          $product_config_price[$product_id] = $configuration_price;
                                          $store_config_price_total[$store_id] += @round($configuration_price * $quantity);
                                          endif;
                                          END CONFIGURATION PRICE */

                                        if (!empty($productPricesArray)):
                                            $product_vat_total = ($quantity * $productPricesArray['vat']);
                                            $product_sub_total = ($quantity * $discounted_price); //PRODUCT NET PRICE TOTAL
                                            $display_product_sub_total = ($quantity * $productPricesArray['display_product_price']); //PRODUCT TOTAL THAT HAS TO BE SHOWN
                                            $display_store_total +=$display_product_sub_total;
                                            $store_products_total_net += $product_sub_total;
                                            $store_total += $product_sub_total;
                                            $store_vat_total += $product_vat_total;
                                            $store_grand_total = $store_products_total_net + $store_vat_total;
                                        else:
                                            $product_sub_total = ($quantity * $discounted_price);
                                            $store_total += $product_sub_total;
                                            $store_grand_total = $store_total;
                                        endif;
//                            $product_sub_total = ($quantity * $discounted_price);
//                            $store_total += $product_sub_total;

                                        if (isset($tempCartInfoForCoupon[$store_id]['product_ids'][$product_id]['sub_total']) && !empty($tempCartInfoForCoupon[$store_id]['product_ids'][$product_id]['sub_total'])):
                                            $tempCartInfoForCoupon[$store_id]['product_ids'][$product_id]['sub_total'] += $product_sub_total;
                                        else:
                                            $tempCartInfoForCoupon[$store_id]['product_ids'][$product_id] = array('sub_total' => $product_sub_total);
                                        endif;

                                        if (isset($tempCartInfoForCoupon[$store_id]['qty']) && !empty($tempCartInfoForCoupon[$store_id]['qty'])):
                                            $tempCartInfoForCoupon[$store_id]['qty'] += $quantity;
                                        else:
                                            $tempCartInfoForCoupon[$store_id]['qty'] = $quantity;
                                        endif;
                                        $tempCartInfoForCoupon[$store_id]['sub_total'] = $store_total;
                                        ?>

                                        <!--Error Message of Cart Product-->
                                        <div>
                                            <div colspan="<?php echo $colspanValue; ?>">
                                                <span class="seaocore_txt_red">
                                                    <?php
                                                    if (empty($this->viewer_id)):
                                                        echo empty($errorArray[$product_id]['error']) ? '' : $errorArray[$product_id]['error'];
                                                    else:
                                                        echo empty($errorArray[$this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]]['error']) ? '' : $errorArray[$this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]]['error'];
                                                    endif;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="m-cart-items clr b_medium o_hidden">
                                            <!--Product Profile Image And Configuration Detail, If Any-->
                                            <div class="product-photo fleft t_center">
                                                <?php echo $this->htmlLink($product_detail->getHref(), $this->itemPhoto($product_detail, 'thumb.icon')); ?>

                                            </div>
                                            <div class="o_hidden product-details">
                                            
                                            <div class="o_hidden">
                                                <!--Product Title-->
                                                <div class="fleft"><?php echo $this->htmlLink($product_detail->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($product_detail->getTitle(), 40), array('title' => $product_detail->getTitle())) ?>
                                                    
                                                                                                           <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?> 
                 <div class="txt_right">&nbsp;
                               <?php
                               $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                               echo '<a class="smoothbox" href=" ' . $spec_url . ' " >' . $this->translate("more info..") . '</a>';
                               ?>
                 </div>
<?php endif; ?>   
                                                    
                                                <?php
                                                if (!empty($this->viewer_id) && ($product_detail->product_type == 'configurable' || $product_detail->product_type == 'virtual')) :
                                                    $cartProductId = $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index];
                                                    $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId);
                                                    echo '<br/>';
                                                    if ($product_detail->product_type == 'virtual' && !empty($this->product_other_info[$cartProductId]['product_other_info'])) :
                                                        echo '<span class="f_small"><b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($this->product_other_info[$cartProductId]['product_other_info']['starttime']) . '</span><br />';
                                                        echo '<span class="f_small"><b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($this->product_other_info[$cartProductId]['product_other_info']['endtime']) . '</span>';
                                                    endif;
                                                    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($cartProductObject);
                                                    $otherDetails = $this->fieldValueLoopStore($cartProductObject, $fieldStructure);
                                                    //$categoryAttributeDetails = Engine_Api::_()->sitestoreproduct()->makeCategoryFieldArray($cartProductObject); 
                                                    ?>
                                                    <div class="f_small seaocore_txt_light"> 
                                                        <?php //if (!empty($categoryAttributeDetails)) :
                                                        //foreach ($categoryAttributeDetails as $config_key => $makeFieldValue) : 
                                                        ?>
                                                       <!--<span><?php //echo "$config_key:" . '     ' . "<b>$makeFieldValue</b>"; ?></span>-->
                                                        <?php
                                                        //endforeach;
                                                        //endif; 
                                                        echo htmlspecialchars_decode($otherDetails);
                                                        ?>
                                                    </div>
                                                <?php
                                                elseif (empty($this->viewer_id) && ($product_detail->product_type == 'configurable' || $product_detail->product_type == 'virtual')):

                                                    $fieldArray = $this->viewerCartConfig[$product_id][$index];
                                                    if ($product_detail->product_type == 'virtual' && (!empty($fieldArray['starttime']) && !empty($fieldArray['endtime']))) :

                                                        echo '<br /><span class="f_small"><b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($fieldArray['starttime']) . '</span><br />';
                                                        echo '<span class="f_small"><b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($fieldArray['endtime']) . '</span>';

                                                    endif;
                                                    ?> 
                                                    <br/>
                                                    <div class="f_small seaocore_txt_light"><?php
                                                        $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($fieldArray);
                                                        if (!empty($makeFieldValueArray)) :
                                                            foreach ($makeFieldValueArray as $config_key => $makeFieldValue) :
                                                                ?>
                                                                <span> <?php echo "$config_key: <b>$makeFieldValue</b>"; ?> </span>
                            <?php endforeach;
                        endif;
                        ?> </div>
                                                <?php endif;
                                                ?>
                                                </div>    
                                                <!-- Cart Product Delete Work -->
                                                <span class="fright">
                                                    <?php
                                                    if (empty($this->viewer_id)):
                                                        if (is_array($this->store_product_quantity[$product_id])) :
                                                            $delete_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'delete-cart', 'item_id' => $product_id, 'index_id' => $index, 'is_array' => 1), 'default', true);
                                                        else:
                                                            $delete_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'delete-cart', 'item_id' => $product_id), 'default', true);
                                                        endif;
                                                    else:
                                                        $delete_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'delete-cart', 'item_id' => $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]), 'default', true);
                                                    endif;
                                                    ?>
                              <a  href="<?php echo $delete_url ?>" class="ui-link smoothbox">
                              <span class="ui-icon ui-icon-remove" title="<?php echo $this->translate("Remove from Cart") ?>">
                              </span>
                              </a>  
                                                </span>
                                            </div>
                                            
                                            <div class="product-details-price clr o_hidden f_normal">
                                                <!--Product Unit Price-->
                                                <div class="f_small fleft first t_light">
                                                    <?php if (!empty($productPricesArray)): ?>
                                                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price']); ?>
                        <?php else: ?>
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($discounted_price); ?>
                        <?php endif; ?>

                                                <?php if (!empty($this->productPriceRangeText[$product_detail->product_id])) : ?>
                                                        <?php echo $this->translate($this->productPriceRangeText[$product_detail->product_id]); ?>
                                                    <?php endif; ?>
                                                </div>

                                                <!--Product Unit Downpayment Price-->
                                                <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                                <div class="f_small fleft first t_light">
                                                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->downPaymentPrice[$product_id][$index]); ?>
                                                </div>
                                                <?php endif; ?>

                                                <!-- Cart Product Quantity Update Work -->
                                                <div class="fleft">
                                                    <?php
                                                    if (empty($this->viewer_id)):
                                                        if (is_array($this->store_product_quantity[$product_id])) :
                                                            echo '<input type="text" name="quantity_product[' . $product_id . '][' . $index . ']" value="' . $quantity . '" style="width:50px;">';
                                                        else:
                                                            echo '<input type="text" name="quantity_product[' . $product_id . ']" value="' . $quantity . '" style="width:50px;">';
                                                        endif;
                                                    else:
                                                        echo '<input type="text" name="quantity_product[' . $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index] . ']" value="' . $quantity . '" style="width:50px;">';
                                                    endif;
                                                    ?>
                                                </div>                                          
                                                
                                            <!-- Cart Product Downpayment total and Remaining amount Work -->
                                            <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                                <div class="f_small fleft first t_light">
                                                <?php $productDownpaymentTotal = round(($this->downPaymentPrice[$product_id][$index] * $quantity), 2); ?>
                                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productDownpaymentTotal); ?>
                        <?php $downPaymentSubTotal += $productDownpaymentTotal ?>
                                                </div>
                                                <div class="f_small fleft first t_light">
                                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($product_sub_total - $productDownpaymentTotal)); ?>
                                                <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($product_sub_total - $productDownpaymentTotal) - ($product_config_price[$product_id] * $quantity)); ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Online Threshold Amount Checks -->
                                            <?php if (!empty($this->storeOnlineThresholdAmount[$store_id])) : ?>
                                                <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                            <?php if ($this->storeOnlineThresholdAmount[$store_id] < $productDownpaymentTotal) : ?>
                                <?php $storeOnlineThresholdMessage = 1; ?>
                                                        <?php endif; ?>
                                                    <?php elseif ($this->storeOnlineThresholdAmount[$store_id] < $product_sub_total): ?>
                                                        <?php $storeOnlineThresholdMessage = 1; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        </div>  
                                        <?php
                                    endif;
                                endforeach;
                            endforeach;

                            $grand_total += $store_grand_total;
                            $downpayment_grand_total += $downPaymentSubTotal;
//                          $final_store_total[$store_id] = $store_total;
                            $final_store_total[$store_id] = $store_grand_total;
                            $final_downpayment_store_total[$store_id] = $downPaymentSubTotal;
                            $final_store_total_net[$store_id] = $store_products_total_net;
                            $final_store_total_vat[$store_id] = $store_vat_total;
                            ?>
                            <div class="m-cart-items-sub-total clr">
                                <div class="clr sm-widget-block m-cart-items-total o_hidden">    
                                
                                <div class="fleft mbot10"><?php echo $this->translate("Subtotal"); ?>&nbsp;&nbsp;</div>
                                        <div class="fright">
                                    <?php echo $this->translate('&nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total)); ?>
                                        </div>
                                
                                  <?php if (!empty($isAllowCoupon) && !empty($this->couponDetail) && isset($this->couponDetail[$store_id]) && !empty($this->couponDetail[$store_id])) : ?>
            <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
                                <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                                <?php if (!empty($couponAmount) && !empty($couponCode)) : ?>
                                    <div class='clr'>
                                        <div class="fleft mbot10"><?php echo $couponCode; ?></div>
                                        <div class="fright">
                                    <?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($couponAmount); ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)): ?>
                                        <?php if ($downPaymentSubTotal > $couponAmount): ?>
                                            <?php $downPaymentSubTotal -= $couponAmount; ?>
                                        <?php else: ?>
                                            <?php $downPaymentSubTotal = 0; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>

                <?php if ($store_grand_total > $couponAmount): ?>
                                        <?php $store_grand_total -= $couponAmount; ?>
                                    <?php else: ?>
                    <?php $store_grand_total = 0; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
        <?php endif; ?>


        <?php if (!empty($isAllowVAT)): ?>
                                <div class="clr">
                                    <div class="fleft">
            <?php echo $this->translate("Store Products Total (net):"); ?>&nbsp;&nbsp;
                                    </div>
                                    <div class="fright">
            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_products_total_net); ?>
                                    </div>
                                </div>

                                <div class="clr">
                                    <div class="fleft">
            <?php echo $this->translate("VAT tax total"); ?>&nbsp;&nbsp;
                                    </div>
                                    <div class="fright">
            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_vat_total); ?>
                                    </div>
                                </div>
        <?php endif; ?>
<!--WORK FOR SHOWING THE MINIMUM SHIPPING COST-->
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0)): ?>
                  <div class="clr">
                    <div class="fleft">
                      <strong><?php echo $this->translate('Minimum shipping cost'); ?>&nbsp;&nbsp;</strong>
                    </div>
                    <div class="fright">
                      <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(Engine_Api::_()->sitestore()->getStoreMinShippingCost($store_id));?></strong>
                    </div>
                  </div>
                <?php endif; ?>
                            <div class="clr">
                                <div class="fleft">
                                    <strong><?php echo $this->translate('Grand Total'); ?>&nbsp;&nbsp;</strong>
                                </div>
                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0)): ?>
                                    <div class="fright">
                                        <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_grand_total + Engine_Api::_()->sitestore()->getStoreMinShippingCost($store_id))); ?></strong>
                                    </div>
                            <?php else: ?>
                                    <div class="fright">
                                        <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_grand_total)); ?></strong>
                                    </div>
        <?php endif; ?>
                            </div>
                            </div>
                            </div>

                              


        <?php if (!empty($isAllowCoupon)) : ?>
                                <div class="sm-widget-block">
                                    <div class="mbot5 t_center">
                                        <label><?php echo $this->translate("Enter your coupon code if you have one."); ?></label>
                                    </div>
                                    <div class="mbot5">
                                        <input type = 'text' id='coupon_code_value_<?php echo $store_id ?>' name='coupon_code' value = "" />
                                    </div>
                                    <span id='coupon_error_msg_<?php echo $store_id ?>' class="seaocore_txt_red"></span>
                                    <div class='buttons clr mtop5'>
                                        <button type='button' onclick="applyCouponcode(<?php echo $store_id ?>);" id="update_shopping_cart">
            <?php echo $this->translate("Apply Coupon") ?>
                                        </button>
                                        <div id="apply_coupon_spinner_<?php echo $store_id ?>" style="display: inline-block;"></div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            <!--                <div>
                                            <a data-role="button" data-theme="b" href="<?php //echo $continue_shopping_url ?>" ><?php //echo $this->translate("Continue Shopping") ?></a>
                                            </div>-->
        <?php if (empty($this->isPaymentToSiteEnable)) : ?>
                                <button data-theme="b"   type='button' onclick="proceedToCheckout(<?php echo $store_id ?>)" id="update_shopping_cart" class="mleft10 checkout_btn">
            <?php echo $this->translate("Proceed to Checkout") ?>
                                </button>
            <?php endif; ?>
    <?php endforeach; ?>
                    </li>
                </ul>

            </div>
        </div>
                <?php if (!empty($this->isPaymentToSiteEnable)) : ?>
            <div class="clr">
                <div class="clr sm-widget-block m-cart-items-total">
                            <?php foreach ($this->manage_cart_store_name as $store_id => $store_name) : ?>  

            <?php if (!empty($this->couponDetail) && isset($this->couponDetail[$store_id]) && !empty($this->couponDetail[$store_id])) : ?>
                                    <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                                    <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
            <?php endif; ?>
                        <div class="ui-grid-a">
                            <div class="ui-block-a">
            <?php echo $this->translate("Subtotal of %s store", $store_name); ?>
                            </div>
                            <div class="ui-block-b">
            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($final_store_total[$store_id]); ?>
                            </div>
                        </div>
        <?php endforeach; ?>
                    <div class="ui-grid-a">
                        <div class="ui-block-a">
                            <b><?php echo $this->translate('Grand Total'); ?></b>
                        </div>
                        <div class="ui-block-b">
                            <b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total); ?></b>
                        </div>
                    </div>  
                </div>
                <div>
                    <button data-theme="b"  type='button' onclick="proceedToCheckout(0)" id="update_shopping_cart">
        <?php echo $this->translate("Proceed to Checkout") ?>
                    </button>
                </div>
            </div>
                    <?php endif; ?>
        <div class='m-cart-bottons t_center'>
            <div>
                <a data-role="button" data-theme="b" href="<?php echo $continue_shopping_url ?>" ><?php echo $this->translate("Continue Shopping") ?></a>
            </div>
            <div>
                <a  data-role="button" data-theme="b" href="<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'delete-cart', 'cart_id' => $this->cart_id), 'default', true) ?>" class="smoothbox ui-btn-danger"> 
    <?php echo $this->translate("Empty Cart") ?></a>
            </div>
            <div>    
                <button data-theme="b"  type='submit' name="update_shopping_cart">
        <?php echo $this->translate("Update Cart") ?>
                </button>
            </div>
        </div>
    </form>

<?php else: ?>
    <div class="m-cart-head">
    <?php echo $this->translate("Shopping Cart is Empty") ?>
    </div>
    <div class="tip">
        <span>
    <?php echo $this->translate('You have no items in your shopping cart. %s to continue shopping.', '<a href="' . $continue_shopping_url . '">' . $this->translate('Click here') . '</a>'); ?>
        </span>
    </div>
    <?php
    return;
endif;
?>

<script type="text/javascript">
            function proceedToCheckout(store_id)
            {
                var form = $.mobile.activePage.find('#viewer_cart');
                form.attr('action', '<?php echo $this->url(array("action" => "checkout"), "sitestoreproduct_general", true); ?>/store_id/' + store_id);
                form.submit();
            }

<?php if (!empty($this->isPaymentToSiteEnable)) : ?>
                function proceedToCheckout() {
                    window.location = '<?php echo $this->url(array("action" => "checkout"), "sitestoreproduct_general", true); ?>';
                }
<?php endif; ?>

            function applyCouponcode(store_id)
            {
                if ($("#coupon_code_value_" + store_id).value == '') {
                    $('#coupon_error_msg_' + store_id).innerHTML = '<?php echo $this->translate("Please Enter a coupon code."); ?>';
                    return;
                }
                $.ajax({
                    url: "<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index', 'action' => 'apply-coupon'), 'default', true); ?>",
                    dataType: 'json',
                    data: {
                        format: 'json',
                        coupon_code: $("#coupon_code_value_" + store_id).val(),
                        cart_info: '<?php echo json_encode($tempCartInfoForCoupon); ?>',
                        store_id: store_id
                    },
                    error: function() {
                    },
                    success: function(response, textStatus, xhr) {
                        $("#apply_coupon_spinner_" + store_id).val();
                        if (response.coupon_error_msg) {
                            alert(response.coupon_error_msg);
                            $('#coupon_error_msg_' + store_id).val(response.coupon_error_msg);
                        } else if (response.cart_coupon_applied) {
                            $.mobile.changePage($.mobile.navigate.history.getActive().url, {
                                reloadPage: true,
                                showLoadMsg: true
                            });

                        }

                    }
                });


            }

</script>