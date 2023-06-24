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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
<?php $isAllowVAT = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$precision = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2);
$defaultParams['precision'] = $precision;
?>

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
<div class="sitestoreproduct_managecart mbot10">
  <?php echo $this->translate("Shopping Cart is Empty") ?>
</div>
<div class="tip">
  <span>
    <?php echo $this->translate('You have no items in your shopping cart. %s to continue shopping.', '<a href="' . $continue_shopping_url . '">' . $this->translate('Click here') . '</a>'); ?>
  </span>
</div>
<?php return;
endif;
?>
<?php $isAllowCoupon = true; ?>
<?php $isAllowCredit = true; ?>
<?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType) && empty($this->isDownPaymentCouponEnable)) : ?>
  <?php $isAllowCoupon = false; ?>
  <?php $isAllowCredit = false; ?>
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
      <div class="sitestoreproduct_managecart fleft mtop10"><?php echo $this->translate("Shopping Cart") ?></div>
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
    <div id="manage_order_tab">
      <div class="sitestoreproduct_data_table <?php echo $tempDivClassName ?>">
        <table class="clr fleft mtop10">
          <thead>
            <tr class="sitestoreproduct_manage_cart_store_head ">
              <th>
                <div></div>
                <div class="sitestoreprod_mcart_product"><?php echo $this->translate("Product") ?></div>
                <div class="sitestoreprod_mcart_unitprice"><?php echo $this->translate("Unit Price") ?></div>
                <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                  <div class="sitestoreprod_mcart_unitdownpay"><?php echo $this->translate("Unit Downpayment") ?></div>
                <?php endif; ?>
                <div class="sitestoreprod_mcart_qty"><?php echo $this->translate("Qty") ?></div>
                <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                  <div class="sitestoreprod_mcart_downpayment"><?php echo $this->translate("Downpayment") ?></div>
                  <div class="sitestoreprod_mcart_remaining"><?php echo $this->translate("Remaining Amount") ?></div>
                <?php endif; ?>
                <?php if(!empty($isAllowVAT)): ?>
                  <div class="sitestoreprod_mcart_vat"><?php echo $this->translate("VAT") ?></div>
                <?php endif;?>
                <div class="sitestoreprod_mcart_subtotal"><?php echo $this->translate("Subtotal") ?></div>
                <div style="border-right-width:1px !important;"></div>
              </th> 
            </tr>
          </thead>
          <tr>
            <td colspan="<?php echo $colspanValue; ?>">
              <div>
                <div class="sitestoreproduct_manage_cart_store mtop5 mbot5">
                  <?php foreach ($this->manage_cart_store_name as $store_id => $name): ?>
                    <?php $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id); ?>

                    <!--Product Store Name-->
                    <div class="clr">
                      <div  colspan="<?php echo $colspanValue; ?>" class="sitestoreproduct_manage_cart_store_name">
                        <?php echo $this->htmlLink($storeObj->getHref(), $storeObj->getTitle(), array('title' => $storeObj->getTitle(), 'class' => 'seaocore_link_inherit')); ?>
                      </div>
                    </div>

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
                        if($product_obj->product_type == 'configurable' || $product_obj->product_type == 'virtual'):
                          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $this->store_cartproduct_id[$product_id]['cartproduct_id'][$index]);
                        if(empty($this->viewer_id)):
                          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $this->viewerCartConfig[$product_id][$index]);
                        endif;
                        else:
                          $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                        endif;
                        
                        if(!empty($productPricesArray)):
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
                          
                          if(!empty($productPricesArray)):
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
                            
                            if(isset($tempCartInfoForCoupon[$store_id]['qty']) && !empty($tempCartInfoForCoupon[$store_id]['qty'])):
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

                            <div class="sitestoreproduct_table_row">
                              <!--Product Profile Image And Configuration Detail, If Any-->
                              <div>
                                <?php echo $this->htmlLink($product_detail->getHref(), $this->itemPhoto($product_detail, 'thumb.icon')); ?>
                                
                              </div>

                              <!--Product Title-->
                              <div class="sitestoreprod_mcart_product"><?php echo $this->htmlLink($product_detail->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($product_detail->getTitle(), 40), array('title' => $product_detail->getTitle())) ?>
                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?> <div class="txt_right">&nbsp;
                                  <?php
                                  $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                                  echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $spec_url . '\');">' . $this->translate("more info..") . '</a>';
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
                                //$categoryAttributeDetails = Engine_Api::_()->sitestoreproduct()->makeCategoryFieldArray($cartProductObject); ?>
                                <div class="f_small seaocore_txt_light"> 
                                <?php //if (!empty($categoryAttributeDetails)) :
                                  //foreach ($categoryAttributeDetails as $config_key => $makeFieldValue) : ?>
                                  <!--<span><?php //echo "$config_key:" . '     ' . "<b>$makeFieldValue</b>"; ?></span>-->
                                 <?php //endforeach;
                                //endif; 
                                 echo htmlspecialchars_decode($otherDetails);
                                 ?>
                               </div>
                             <?php  elseif (empty($this->viewer_id) && ($product_detail->product_type == 'configurable' || $product_detail->product_type == 'virtual')):

                             $fieldArray = $this->viewerCartConfig[$product_id][$index];
                             if ($product_detail->product_type == 'virtual' && (!empty($fieldArray['starttime']) && !empty($fieldArray['endtime']))) :

                              echo '<br /><span class="f_small"><b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($fieldArray['starttime']) . '</span><br />';
                            echo '<span class="f_small"><b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($fieldArray['endtime']) . '</span>';

                            endif; ?> 
                            <br/>
                            <div class="f_small seaocore_txt_light"><?php $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($fieldArray);
                              if (!empty($makeFieldValueArray)) :
                                foreach ($makeFieldValueArray as $config_key => $makeFieldValue) : ?>
                              <span> <?php echo "$config_key: <b>$makeFieldValue</b>"; ?> </span>
                            <?php endforeach;
                            endif; ?> </div>
                          <?php endif;
                          ?>
                        </div>

                        <!--Product Unit Price-->
                        <div class="sitestoreprod_mcart_unitprice">
                          <?php if(!empty($productPricesArray)): ?>
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price']); ?>
                          <?php else:?>
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($discounted_price); ?>
                          <?php endif;?>
                          
                          <?php if (!empty($this->productPriceRangeText[$product_detail->product_id])) : ?>
                            <?php echo $this->translate($this->productPriceRangeText[$product_detail->product_id]); ?>
                          <?php endif; ?>
                        </div>

                        <!--Product Unit Downpayment Price-->
                        <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                          <div class="sitestoreprod_mcart_unitdownpay">
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->downPaymentPrice[$product_id][$index]); ?>
                          </div>
                        <?php endif; ?>

                        <!-- Cart Product Quantity Update Work -->
                        <div class="sitestoreprod_mcart_qty">
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
                            <div class="sitestoreprod_mcart_downpayment">
                              <?php $productDownpaymentTotal = round(($this->downPaymentPrice[$product_id][$index] * $quantity), 2); ?>
                              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productDownpaymentTotal); ?>
                              <?php $downPaymentSubTotal += $productDownpaymentTotal ?>
                            </div>
                            <div class="sitestoreprod_mcart_remaining">
                              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($product_sub_total - $productDownpaymentTotal)); ?>
                              <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($product_sub_total - $productDownpaymentTotal) - ($product_config_price[$product_id] * $quantity)); ?>
                            </div>
                          <?php endif; ?>
                          
                          <!--VAT Charges work-->
                          <?php if(!empty($isAllowVAT)): ?>
                            <div class="sitestoreprod_mcart_vat">
                              <?php
                              if (!empty($productPricesArray)) :
                                if(isset($productPricesArray['vatShowType'])):
                                  echo $productPricesArray['vatShowType'];
                                else :
                                  $productVat = $productPricesArray['vat']* $quantity;
                                if (empty($productVat)):
                                  $productVat = (float) $productVat;
                                $productVatStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($productVat, $currency, $defaultParams);
                                echo $productVatStr;
                                else:
                                  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['vat'] * $quantity);
                                endif;
                                endif;
                                endif;
                                ?>
                              </div>
                            <?php endif;?>

                            <!-- Cart Product SubTotal Work -->
                            <div class="sitestoreprod_mcart_subtotal">
                              <?php if(!empty($productPricesArray)): ?>
                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($display_product_sub_total); ?>
                              <?php else: ?>
                                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product_sub_total); ?>
                              <?php endif;?>
                            </div>

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

                            <!-- Cart Product Delete Work -->
                            <div class="txt_right">
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
                                echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $delete_url . '\');"><img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/cross.png" title=" ' . $this->translate("Remove from Cart") . '"></a>';
                                ?>
                              </div>
                            </div>  
                            <?php
                            endif;
                            endforeach;
                            endforeach;
//                    $grand_total += $store_total;
                            $grand_total += $store_grand_total;
                            $downpayment_grand_total += $downPaymentSubTotal;
//                    $final_store_total[$store_id] = $store_total;
                            $final_store_total[$store_id] = $store_grand_total;
                            $final_downpayment_store_total[$store_id] = $downPaymentSubTotal;
                            $final_store_total_net[$store_id] = $store_products_total_net;
                            $final_store_total_vat[$store_id] = $store_vat_total;
                            ?>

                            <!--Show Online Threshold Amount Message-->
                            <?php if (!empty($storeOnlineThresholdMessage)) : ?>
                              <div>
                                <div colspan="<?php echo $colspanValue; ?>">
                                  <div class="seaocore_txt_red">
                                    <?php echo $this->translate("Exceeding your product subtotal amount by %s, will disable the online transaction option for this store.", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->storeOnlineThresholdAmount[$store_id])); ?>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>

                            <!--Start Work of Store Product Amount Summary for Direct Payment-->
                            <?php if (empty($this->isPaymentToSiteEnable)) : ?>
                              <div class="sitestoreproduct_manage_cart_total_box mbot10 o_hidden <?php echo empty($isAllowCoupon) ? 'fright' : ''; ?>">
                                <div class="sitestoreproduct_manage_cart_store_price fright">
                                  <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>

                                    <div class="clr">
                                      <div class="fleft">
                                        <?php echo $this->translate("Downpayment Subtotal"); ?>&nbsp;&nbsp;
                                      </div>
                                      <div class="fright">
                                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal); ?>
                                      </div>
                                    </div>
                                    <div class="clr">
                                      <div class="fleft">
                                        <?php echo $this->translate("Remaining Amount Subtotal"); ?>&nbsp;&nbsp;
                                      </div>
                                      <div class="fright">
                                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_total - $downPaymentSubTotal)); ?>
                                        <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_total - $downPaymentSubTotal) - ($store_config_price[$store_id] * $quantity)); ?>
                                      </div>
                                    </div>
                                  <?php endif; ?>
                                  
                                  <?php // if (!empty($isAllowVAT) && !empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                  <!--<div class="fleft mbot10"><?php // echo $this->translate("Configuration Total"); ?>&nbsp;&nbsp;</div>-->
                                  <!--<div class="fright"><?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(array_sum($store_config_price_total)); ?></div>-->
                                  <?php // endif; ?>

                                  <div class="clr">
                                    <div class="fleft mbot10"><?php echo $this->translate("Subtotal"); ?>&nbsp;&nbsp;</div>
                                    <?php if(!empty($isAllowVAT) ):?>
                                      <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                        <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total); ?></div>
                                      <?php else:?>
                                        <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($display_store_total); ?></div>
                                      <?php endif;?>
                                    <?php else:?>
                                      <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total); ?></div>
                                    <?php endif;?>
                                    
                                  </div>
                                  <?php if (!empty($isAllowCoupon) && !empty($this->couponDetail) && isset($this->couponDetail[$store_id]) && !empty($this->couponDetail[$store_id])) : ?>
                                    <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
                                    <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                                    <?php if( !empty($couponAmount) && !empty($couponCode) ) : ?>
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

                                  <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                    <div class="clr">
                                      <div class="fleft">
                                        <strong><?php echo $this->translate('Downpayment Grand Total'); ?>&nbsp;&nbsp;</strong>
                                      </div>
                                      <div class="fright">
                                        <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal); ?></strong>
                                      </div><br/>
                                    </div>
                                  <?php endif; ?>
                                  
                                  <?php if(!empty($isAllowVAT)): ?>
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
                                        <?php
                                        if(empty($store_vat_total)):
                                          $store_vat_total = (float) $store_vat_total;
                                        $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($store_vat_total, $currency, $defaultParams);
                                        echo $priceStr;
                                        else:
                                          echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_vat_total);
                                        endif; ?>
                                      </div>
                                    </div>
                                  <?php endif;?>

                                  <!--WORK FOR SHOWING THE MINIMUM SHIPPING COST-->
                                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0)): ?>
                                    <div class="clr">
                                      <div class="fleft">
                                        <strong><?php echo $this->translate('Minimum shipping cost'); ?>&nbsp;&nbsp;</strong>
                                      </div>
                                      <div class="fright">
                                        <strong><?php echo $this->locale()->toCurrency(Engine_Api::_()->sitestore()->getStoreMinShippingCost($store_id), Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?></strong>
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
                                    <?php else:?>
                                      <div class="fright">
                                        <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_grand_total)); ?></strong>
                                      </div>
                                    <?php endif;?>
                                  </div>
                                  <div class='buttons fright clr mtop10'>
                                    <button type='button' onclick="window.location.assign('<?php echo $this->url(array("action" => "checkout", "store_id" => $store_id), "sitestoreproduct_general", true); ?>')" id="update_shopping_cart" class="mleft10 checkout_btn">
                                      <?php echo $this->translate("Proceed to Checkout") ?>
                                    </button>
                                  </div>
                                </div>

                                <!-- Apply Coupon Code -->
                                <?php if (!empty($isAllowCoupon)) : ?>
                                  <div class="fleft">
                                    <div class="mbot5">
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
                              </div>
                            <?php endif; ?>

                            

                            <!--End Work of Store Product Amount Summary for Direct Payment-->

                            <!--Show Store Subtotal and downpayment subototal for Site Payment-->
                      <?php if (!empty($this->isPaymentToSiteEnable)) : ?>
                              <div><div colspan="<?php echo $colspanValue; ?>"></div></div>
                              <div class="sitestoreproduct_manage_cart_sub_total">
                                <div colspan="<?php echo $colspanValue; ?>" class="sitestoreproduct_manage_cart_store_name txt_right">
                                  <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                                    <?php echo $this->translate('Downpayment Subtotal &nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal)); ?>&nbsp&nbsp|&nbsp&nbsp
                                    <?php // echo $this->translate('Remaining Amount Subtotal &nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_total - $downPaymentSubTotal) - ($store_config_price[$store_id] * $quantity))); ?><?php echo $this->translate('Remaining Amount Subtotal &nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($store_total - $downPaymentSubTotal))); ?>&nbsp&nbsp|&nbsp&nbsp
                                  <?php endif; ?>
                                  <?php if(!empty($isAllowVAT)): ?>
                                    <?php
                                    if(empty($store_vat_total)):
                                      $store_vat_total = (float) $store_vat_total;
                                    $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($store_vat_total, $currency, $defaultParams);
                                    echo $this->translate('Vat tax total &nbsp; %s', $priceStr);
                                    else:
                                      echo $this->translate('Vat tax total &nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_vat_total));
                                    endif; ?>&nbsp&nbsp|&nbsp&nbsp
                                  <?php endif;?>
                                  <?php echo $this->translate('Subtotal &nbsp; %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total)); ?>
                                </div>
                              </div>
                            <?php endif; ?> 
                          <?php endforeach; ?>
                        </div>
                      </div>   
                    </td>
                  </tr>
                  <tr>
                    <td colspan="<?php echo $colspanValue; ?>" class="sitestoreproduct_manage_cart_buttons_wrap">
                      <div class="sitestoreproduct_manage_cart_buttons clr">
                        <div class="fleft"><?php echo '<a href="' . $continue_shopping_url . '" >' . $this->translate("Continue Shopping") . '</a>'; ?></div>
                        <div class="fright">
                          <div class="fleft mright5">
                            <?php $clear_cart_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'delete-cart', 'cart_id' => $this->cart_id), 'default', true); ?>
                            <?php echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $clear_cart_url . '\');"> ' . $this->translate("Empty Cart") . '</a>'; ?>
                          </div>
                          <div class="fleft">
                            <button type='submit' name="update_shopping_cart"><?php echo $this->translate("Update Cart") ?></button>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <!--Start Work of Store Total Summary when Site Payment is Enabled-->
            <?php if (!empty($this->isPaymentToSiteEnable)) : ?>
              <div>
                <div class="sitestoreproduct_manage_cart_total_box mbot10 o_hidden <?php echo empty($isAllowCoupon) ? 'fright' : ''; ?>">
                  <div class="sitestoreproduct_manage_cart_store_price fright">
                    <?php $couponAmount = $couponCode = ''; ?>
                    <?php $creditAmount = $creditCode = ''; ?>
                    <?php foreach ($this->manage_cart_store_name as $store_id => $store_name) : ?>
                      <?php if( !empty($this->couponDetail) && isset($this->couponDetail[$store_id]) && !empty($this->couponDetail[$store_id]) ) : ?>
                        <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                        <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
                      <?php endif; ?>
                      <?php if( !empty($this->creditDetail) && isset($this->creditDetail) && !empty($this->creditDetail) ) : ?>
                        <?php $creditCode = $this->creditDetail['credit_points']; ?>
                        <?php $creditAmount = $this->creditDetail['credit_amount']; ?>
                      <?php endif; ?>
                      <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                        <div class="clr">
                          <div class="fleft">
                            <?php echo $this->translate("Downpayment Subtotal of %s store", $store_name); ?>&nbsp;&nbsp;
                          </div>
                          <div class="fright">
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($final_downpayment_store_total[$store_id]); ?>
                          </div>
                        </div>
                        <div class="clr">
                          <div class="fleft">
                            <?php echo $this->translate("Remaining Amount Subtotal of %s store", $store_name); ?>&nbsp;&nbsp;
                          </div>
                          <div class="fright">
                            <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total[$store_id] - $final_downpayment_store_total[$store_id]) - ($store_config_price[$store_id] * $quantity)); ?>
                            <?php if(!empty($isAllowVAT)):?>
                              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total_net[$store_id] - $final_downpayment_store_total[$store_id])); ?>
                            <?php else:?>
                              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total[$store_id] - $final_downpayment_store_total[$store_id])); ?>
                            <?php endif;?>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if(!empty($isAllowVAT)):?>
                        <div class="clr">
                          <div class="fleft">
                            <?php echo $this->translate("Net Amount Subtotal of %s store", $store_name); ?>&nbsp;&nbsp;
                          </div>
                          <div class="fright">
                            <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total[$store_id] - $final_downpayment_store_total[$store_id]) - ($store_config_price[$store_id] * $quantity)); ?>
                            
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total_net[$store_id])); ?>
                          </div>
                        </div>
                      <?php endif;?>
                      <?php if(!empty($isAllowVAT)):?>
                        <div class="clr">
                          <div class="fleft">
                            <?php echo $this->translate("VAT Amount Subtotal of %s store", $store_name); ?>&nbsp;&nbsp;
                          </div>
                          <div class="fright">
                            <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total[$store_id] - $final_downpayment_store_total[$store_id]) - ($store_config_price[$store_id] * $quantity)); ?>
                            
                            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($final_store_total_vat[$store_id])); ?>
                          </div>
                        </div>
                      <?php endif;?>
                      <div class="clr">
                        <div class="fleft mbot10">
                          <?php echo $this->translate("Subtotal of %s store", $store_name); ?>&nbsp;&nbsp;
                        </div>
                        <div class="fright">
                          <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($final_store_total[$store_id]); ?>
                        </div>
                      </div>
                    <?php endforeach; ?>

                    <?php if (!empty($isAllowCoupon) && !empty($couponCode) && !empty($couponAmount)) : ?>
                      <div class='clr'>
                        <div class="fleft mbot10"><?php echo $couponCode; ?>&nbsp;&nbsp; </div>
                        <div class="fright"><?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($couponAmount); ?></div>
                      </div>
                      <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)): ?>
                        <?php if ($downpayment_grand_total > $couponAmount) : ?>
                          <?php $downpayment_grand_total -= $couponAmount; ?>
                        <?php else: ?>
                          <?php $downpayment_grand_total = 0; ?>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php if ($grand_total > $couponAmount) : ?>
                        <?php $grand_total -= $couponAmount; ?>
                      <?php else: ?>
                        <?php $grand_total = 0; ?>
                      <?php endif; ?>
                    <?php endif; ?>


                    <?php if (!empty($isAllowCredit) && !empty($this->creditAllow) && !empty($creditCode) && !empty($creditAmount)) : ?>
                      <div class='clr'>
                        <div class="fleft mbot10">Credit Points <?php echo $creditCode; ?>&nbsp;&nbsp; </div>
                        <div class="fright"><?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($creditAmount); ?></div>
                      </div>
                      <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)): ?>
                        <?php if ($downpayment_grand_total > $creditAmount) : ?>
                          <?php $downpayment_grand_total -= $creditAmount; ?>
                        <?php else: ?>
                          <?php $downpayment_grand_total = 0; ?>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php if ($grand_total > $creditAmount) : ?>
                        <?php $grand_total -= $creditAmount; ?>
                      <?php else: ?>
                        <?php $grand_total = 0; ?>
                      <?php endif; ?>
                    <?php endif; ?>


                    <?php if (!empty($this->isDownPaymentEnable) && !empty($this->cartProductPaymentType)) : ?>
                      <div class="clr">
                        <div class="fleft"><strong><?php echo $this->translate('Downpayment Grand Total'); ?>&nbsp;&nbsp;</strong> </div>
                        <div class="fright">
                          <strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downpayment_grand_total); ?> </strong>
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
                          <strong><?php echo $this->locale()->toCurrency(Engine_Api::_()->sitestore()->getStoreMinShippingCost($store_id), Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?></strong>
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="clr">
                      <div class="fleft"><strong><?php echo $this->translate('Grand Total'); ?>&nbsp;&nbsp;</strong> </div>
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.minimum.shipping.cost', 0)): ?>
                        <div class="fright"><strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total + Engine_Api::_()->sitestore()->getStoreMinShippingCost($store_id)); ?> </strong></div>
                      <?php else:?>
                        <div class="fright"><strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total); ?> </strong></div>
                      <?php endif;?>
                    </div>
                    <div class='buttons fright clr mtop10'>
                      <button type='button' onclick="proceedToCheckout()" id="update_shopping_cart">
                        <?php echo $this->translate("Proceed to Checkout") ?>
                      </button>
                    </div>
                  </div>
                  <!-- Apply Coupon Code -->
                  <?php if (!empty($isAllowCoupon)) : ?>
                    <div class="fleft">
                      <div class="mbot5">
                        <label><?php echo $this->translate("Enter your coupon code if you have one."); ?></label>
                      </div>
                      <div class="mbot5">
                        <input type = 'text' id='coupon_code_value_0' name='coupon_code' value = "" />
                      </div>
                      <span id='coupon_error_msg_0' class="seaocore_txt_red"></span>
                      <div class='buttons clr mtop5'>
                        <button type='button' onclick="applyCouponcode(0);" id="update_shopping_cart">
                          <?php echo $this->translate("Apply Coupon") ?>
                        </button>
                        <div id="apply_coupon_spinner_0" style="display: inline-block;"></div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="sitestoreproduct_manage_cart_total_box">
                  <?php if (!empty($isAllowCredit) && !empty($this->creditAllow) ) : ?>
                    <div class="credits_box">
                      <div class="mbot5">
                        <label><?php echo $this->translate("Enter credits if you want to use."); ?></label>
                      </div>
                      <div class="mbot5">
                      <input type='text' id='credit_code_value_0' name='credit_code' onkeypress="return isNumberKey(event)" value = "" onchange="return validateCreditCode()" />
                      </div>
                      <div class='buttons clr mtop5'>
                        <button type='button' onclick="applyCreditcode(0);" id="update_shopping_cart">
                          <?php echo $this->translate("Use Credits") ?>
                        </button>
                        <?php if(!empty($this->creditDetail)): ?>
                        <button type='button' onclick="cancelCreditcode(0);" id="update_shopping_cart_cancel">
                          <?php echo $this->translate("Remove Credits") ?>
                        </button>
                      <?php endif; ?>
                        <div id="apply_credit_spinner_0" style="display: inline-block;"></div>
                      </div>
                    </div>
                    <div class="credits_msg"><span id='credit_error_msg_0' class="seaocore_txt_red"></span></div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          </form>

        <?php else: ?>
          <div class="sitestoreproduct_managecart mbot10">
            <?php echo $this->translate("Shopping Cart is Empty") ?>
          </div>
          <div class="tip">
            <span>
              <?php echo $this->translate('You have no items in your shopping cart. %s to continue shopping.', '<a href="' . $continue_shopping_url . '">' . $this->translate('Click here') . '</a>'); ?>
            </span>
          </div>
          <?php return;
          endif;
          ?>
          <?php //echo '<pre>'; print_r($tempCartInfoForCoupon); die; ?>
          <script type="text/javascript">
            <?php if (!empty($this->isPaymentToSiteEnable)) : ?>
            function proceedToCheckout() {
              var isActivityBridge = "<?php echo Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->checkActivityBridges(1);?>";
              if(isActivityBridge == 1) 
                window.location = '<?php echo $this->url(array("action" => "checkout"), "sitestoreproduct_general", true); ?>';
              else
              alert("You do not have proper activity bridges for completing this process.");
              
            }
          <?php endif; ?>

          function applyCouponcode(store_id)
          {
            if( document.getElementById("coupon_code_value_"+store_id).value == '' ) {
              document.getElementById('coupon_error_msg_'+store_id).innerHTML = '<?php echo $this->translate("Please Enter a coupon code."); ?>';
              return;
            }
            
            en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index', 'action' => 'apply-coupon'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                coupon_code: document.getElementById("coupon_code_value_" + store_id).value,
                cart_info: '<?php echo json_encode($tempCartInfoForCoupon); ?>',
                store_id: store_id
              },
              onRequest: function(){
                document.getElementById('coupon_error_msg_'+store_id).innerHTML = '';
                document.getElementById("apply_coupon_spinner_"+store_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                document.getElementById("apply_coupon_spinner_"+store_id).innerHTML = '';
                if (responseJSON.coupon_error_msg) {
                  document.getElementById('coupon_error_msg_'+store_id).innerHTML = responseJSON.coupon_error_msg;
                } else if (responseJSON.cart_coupon_applied) {
                  window.location = '<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>';
                }
              }
            }));
          }
          function applyCreditcode(store_id)
          {
            if( document.getElementById("credit_code_value_"+store_id).value == '' ) {
              document.getElementById('credit_error_msg_'+store_id).innerHTML = '<?php echo $this->translate("Please Enter valid credits."); ?>';
              return;
            }
            if(!validateCreditCode())
            {
              return;
            }
            en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'sitecredit', 'controller' => 'index', 'action' => 'apply-credit-store'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                credit_code: document.getElementById("credit_code_value_" + store_id).value,
                cart_info: '<?php echo json_encode($tempCartInfoForCoupon); ?>',
                store_id: store_id
              },
              onRequest: function(){
                document.getElementById('credit_error_msg_'+store_id).innerHTML = '';
                document.getElementById("apply_credit_spinner_"+store_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                document.getElementById("apply_credit_spinner_"+store_id).innerHTML = '';
                if (responseJSON.credit_error_msg) {
                  document.getElementById('credit_error_msg_'+store_id).innerHTML = responseJSON.credit_error_msg;
                } else if (responseJSON.cart_credit_applied) {
                  window.location = '<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>';
                }
              }
            }));
          }
          function cancelCreditcode(store_id)
          {
           
            en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'sitecredit', 'controller' => 'index', 'action' => 'cancel-credit-store'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                credit_code: document.getElementById("credit_code_value_" + store_id).value,
                store_id: store_id
              },
              onRequest: function(){
                document.getElementById('credit_error_msg_'+store_id).innerHTML = '';
                document.getElementById("apply_credit_spinner_"+store_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                document.getElementById("apply_credit_spinner_"+store_id).innerHTML = '';
                window.location = '<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>';
              }
            }));
          }
         function validateCreditCode()
          {
              if($('credit_code_value_0').value < 1)
            {
              $('credit_error_msg_0').innerHTML="Please enter valid credit points"; 
              return false;
            } else {
              $('credit_error_msg_0').innerHTML=""; 
              return true;
            }
          }

          function isNumberKey(evt) { 
            var charCode = (evt.charCode) ? evt.which : event.keyCode

            if (charCode > 31 && (charCode < 48 || charCode > 57) || charCode == 46) 
              return false; 
             
         return true; 
      } 
        </script>