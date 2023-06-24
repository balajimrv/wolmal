<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-cart-products.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestoreproduct/View/Helper', 'Sitestoreproduct_View_Helper'); ?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>
<?php if(COUNT($this->getCartProducts)): ?>
<?php if( !empty($this->isOtherModule) && $this->isOtherModule == 1 ) : ?>
<div class="seocore_pulldown_item_list">
  <div id="sitemenu_cart_main_right_content_area">
    <div id="sitemenu_cart_scroll_main_right_content" class="sitemenu_scroll_content">
<?php endif; ?>
<ul id="sitestoreproduct_cart_menu" class="notifications_menu sitestoreproduct_cart_mini">
<?php
$cartTotal = 0;
foreach ($this->getCartProducts as $getProduct) :
  $configuration_price = 0; $total_price_config = 0;
  if( empty($this->viewer_id) ):
    $item_id = $getProduct->product_id;
    // IF CONFIGURABLE PRODUUCT
    if( isset($this->product_quantity[$item_id]['config']) && is_array($this->product_quantity[$item_id]['config']) ) :       
      $pageObj = Engine_Api::_()->getItem('sitestore_store', $getProduct->store_id);
      foreach( $this->product_quantity[$item_id]['config'] as $index => $item ) : 
        $quantity = $item;
        $total_price_config = 0;
?>
  <li class="sitestoreproduct_cart_item" id="sitestoreproduct_notification_<?php echo $item_id.'_'.$index; ?>">
    <div>
      <div class="sitestoreproduct_product_img">   
	      <?php echo $this->htmlLink($getProduct->getHref(), $this->itemPhoto($getProduct, 'thumb.normal')); ?>
      </div>  
      <div class="sitestoreproduct_product_info">
        <div class="sitestoreproduct_product_title">
          <?php if( empty($this->isOtherModule) ) : ?>
          <a href="javascript:void(0)" class="seaocore_remove"  onclick="sitestoreproductHandler.removeProduct(<?php echo $item_id . ','.$index.', 1' ?>)" title="<?php echo $this->translate('Remove from cart')?>"></a>
          <?php else: ?>
          <a href="javascript:void(0)" class="seaocore_remove"  onclick="removeCartProduct(<?php echo $item_id . ','.$index.', 1,'.$this->isOtherModule ?>)" title="<?php echo $this->translate('Remove from cart')?>"></a>
          <?php endif; ?>
          <?php echo $this->htmlLink($getProduct->getHref(), $getProduct->getTitle()); ?>  
        </div>
        <?php if( isset($this->viewerCartConfig[$getProduct->product_id]) && !empty($this->viewerCartConfig[$getProduct->product_id]) ) : ?>
          <div class="sitestoreproduct_product_stats sitestoreproduct_product_cong">
            <?php
              $configuration = $this->viewerCartConfig[$getProduct->product_id][$index];
              $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($getProduct->product_id, array('price', 'price_increment'), $configuration, 0, 1);
              if(!empty($configuration_price)):
                $total_price_config = $this->productPrice[$getProduct->product_id] + $configuration_price;
              endif;
              $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($configuration);
              foreach($makeFieldValueArray as $key => $makeFieldValue) :
                echo "<b>$key</b>  $makeFieldValue<br/>";
              endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="sitestoreproduct_product_stats">
          <?php echo $this->translate('Store: <b>%s</b>', $this->htmlLink($pageObj->getHref(), $pageObj->getTitle())); ?>
        </div>
        <div class="sitestoreproduct_product_stats">
          <?php echo $this->translate('Qty: <b>%s</b>', $quantity); ?>
        </div>
        <div class="sitestoreproduct_product_stats">
          <?php echo $this->translate('Price:'); ?>
          <b class="sitestoreproduct_price_sale">
            <?php if(!empty($this->isVatAllow)):?>
              <?php $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $getProduct->product_id); ?>
              <?php $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj); ?>
              <?php if(($getProduct->product_type == 'configurable' || $getProduct->product_type == 'virtual' ) && !empty($item_id)) :  ?>
              <?php $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $configuration); ?>
              <?php endif; ?>
            <?php endif;?>
            <?php if(!empty($this->isVatAllow) && !empty($productPricesArray) && !empty($productPricesArray['show_msg'])):?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price'])."*"; ?>
            <?php elseif(!empty($total_price_config)):?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config) ?>
            <?php else:?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->productPrice[$getProduct->product_id]) ?>
            <?php endif;?>
            <?php if( !empty($this->priceRangeBasis[$getProduct->product_id]) ) : ?>
              <?php echo $this->priceRangeBasis[$getProduct->product_id]; ?>
            <?php endif; ?>
          </b>
        </div>
        <?php $downPaymentSubTotal = 0; ?>
        <?php if( !empty($this->isSitestorereservationModuleExist) && !empty($this->product_down_payment_amount[$getProduct->product_id]) ) : ?>
          <?php if( $quantity > 1 ) : ?>
            <?php $downPaymentSubTotal = $this->product_down_payment_amount[$getProduct->product_id] * $quantity ?>
          <?php endif; ?>
          <div class="sitestoreproduct_product_stats">
            <?php echo $this->translate('Downpayment: ') . '<b class="sitestoreproduct_price_sale">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->product_down_payment_amount[$getProduct->product_id]) . '</b>';?>
          </div>
          <?php if( !empty($downPaymentSubTotal) ) : ?>
            <div class="sitestoreproduct_product_stats">
              <?php echo $this->translate('Downpayment Subtotal: ') . '<b>' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal) . '</b>';?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div class="sitestoreproduct_product_stats">
          
          <?php if(!empty($this->isVatAllow) && !empty($productPricesArray) && !empty($productPricesArray['show_msg'])):?>
            <?php echo $this->translate('Subtotal: <b>%s</b>', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($quantity * $productPricesArray['display_product_price']))); ?>
          <?php elseif(!empty($total_price_config)) :?>
          <?php echo $this->translate('Subtotal: <b>%s</b>', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($quantity * $total_price_config))); ?>
          <?php else : ?>
          <?php echo $this->translate('Subtotal: <b>%s</b>', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($quantity * $this->productPrice[$getProduct->product_id]))); ?>
          <?php endif;?>
        </div>
        <div class="sitestoreproduct_product_stats">
          <?php if( !empty($this->isVatAllow) && !empty($productPricesArray)):?>
            <?php $cartTotal += $quantity * $productPricesArray['display_product_price']; ?>
          <?php elseif(!empty($total_price_config)) : ?>
          <?php $cartTotal += $quantity * $total_price_config;?>
          <?php else : ?>
          <?php $cartTotal += $quantity * $this->productPrice[$getProduct->product_id];?>
          <?php endif; ?>
        </div>  
      </div>
    </div>
  </li> 
<?php   endforeach;
      continue; 
    endif;
    $quantity = $this->product_quantity[$item_id];
  else:
    $item_id = $getProduct->cartproduct_id;
    $quantity = $getProduct->quantity;
  endif;
  $pageObj = Engine_Api::_()->getItem('sitestore_store', $getProduct->store_id);
  ?>
    <li class="sitestoreproduct_cart_item" id="sitestoreproduct_notification_<?php echo $item_id; ?>">
      <div>
        <div class="sitestoreproduct_product_img">
          <?php echo $this->htmlLink($getProduct->getHref(), $this->itemPhoto($getProduct, 'thumb.normal')); ?>
        </div>  
        <div class="sitestoreproduct_product_info">
          <div class="sitestoreproduct_product_title">
            <?php if( empty($this->isOtherModule) ) : ?>
            <a class="seaocore_remove" href="javascript:void(0)" onclick="sitestoreproductHandler.removeProduct(<?php echo $item_id ?>)" title="<?php echo $this->translate('Remove from cart')?>"></a>
            <?php else: ?>
            <a class="seaocore_remove" href="javascript:void(0)" onclick="removeCartProduct(<?php echo $item_id . ', 0, 0,'.$this->isOtherModule ?>)" title="<?php echo $this->translate('Remove from cart')?>"></a>
            <?php endif; ?>
            <?php echo $this->htmlLink($getProduct->getHref(), $getProduct->getTitle()); ?>
          </div>
          <?php if($getProduct->product_type == 'configurable' || $getProduct->product_type == 'virtual' ) : ?>
            <div class="sitestoreproduct_product_stats sitestoreproduct_product_cong">
              <?php
                $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $item_id); 
                
                $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                
                // WORK FOR CONFIGURATION PRICE 
                
                $valueRows = $values->getRowsMatching(array(
                    'item_id' => $cartProductObject->getIdentity(),
                ));
                $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($getProduct->product_id, array('price', 'price_increment'), $valueRows);
                
                if(!empty($configuration_price)):
                     $total_price_config = $this->productPrice[$getProduct->product_id] + $configuration_price;
                endif;
                $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($cartProductObject);
                $otherDetails = $this->fieldValueLoopStore($cartProductObject, $fieldStructure);   
                //$categoryAttributeDetails = Engine_Api::_()->sitestoreproduct()->makeCategoryFieldArray($cartProductObject);
                echo htmlspecialchars_decode($otherDetails);
//                if (!empty($categoryAttributeDetails)) :
//                foreach ($categoryAttributeDetails as $config_key => $makeFieldValue) :
//                  echo "$config_key: <b>  $makeFieldValue</b><br/>";
//                endforeach;
//              endif;?>
            </div>
          <?php endif; ?>
          <div class="sitestoreproduct_product_stats">
            <?php echo $this->translate('Store: <b>%s</b>', $this->htmlLink($pageObj->getHref(), $pageObj->getTitle())); ?>
          </div>
          <div class="sitestoreproduct_product_stats">
            <?php echo $this->translate('Qty: <b>%s</b>', $quantity); ?>
          </div>
          <div class="sitestoreproduct_product_stats">
            <?php echo $this->translate('Price:'); ?>
            <b class="sitestoreproduct_price_sale">
              <?php if(!empty($this->isVatAllow)): ?>
              <?php $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $getProduct->product_id); ?>
              <?php $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj); ?>
              <?php if(($getProduct->product_type == 'configurable' || $getProduct->product_type == 'virtual' ) && !empty($item_id)) :  ?>
                <?php $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $item_id); ?>
              <?php endif;?>
              <?php endif;?>
              <?php if( !empty($this->isVatAllow) && !empty($productPricesArray) && !empty($productPricesArray['show_msg'])):?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price'])."*"; ?>
              <?php elseif(!empty($total_price_config)):?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config) ?>
              <?php else:?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->productPrice[$getProduct->product_id]) ?>
              <?php endif;?>
              <?php if( !empty($this->priceRangeBasis[$getProduct->product_id]) ) : ?>
                <?php echo $this->priceRangeBasis[$getProduct->product_id]; ?>
              <?php endif; ?>
            </b>
          </div>
          <?php $downPaymentSubTotal = 0; ?>
          <?php if( !empty($this->isSitestorereservationModuleExist) && !empty($this->product_down_payment_amount[$getProduct->product_id]) ) : ?>
            <?php if( $quantity > 1 ) : ?>
              <?php $downPaymentSubTotal = $this->product_down_payment_amount[$getProduct->product_id] * $quantity ?>
            <?php endif; ?>
            <div class="sitestoreproduct_product_stats">
              <?php echo $this->translate('Downpayment: ') . '<b class="sitestoreproduct_price_sale">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->product_down_payment_amount[$getProduct->product_id]) . '</b>';?>
            </div>
            <?php if( !empty($downPaymentSubTotal) ) : ?>
              <div class="sitestoreproduct_product_stats">
                <?php echo $this->translate('Downpayment Subtotal: ') . '<b>' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal) . '</b>';?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <div class="sitestoreproduct_product_stats">
            
            <?php if( !empty($this->isVatAllow) && !empty($productPricesArray)):?>
                <?php echo $this->translate("Subtotal: <b>%s</b>", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price'] * $quantity));?>
            <?php elseif(!empty($total_price_config)) : ?>
            <?php echo $this->translate('Subtotal: <b>%s</b>', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($quantity * $total_price_config)));?>
            <?php else: ?>
            <?php echo $this->translate('Subtotal: <b>%s</b>', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($quantity * $this->productPrice[$getProduct->product_id])));?>
            <?php endif; ?>
          </div>
          <div class="sitestoreproduct_product_stats">
            <?php if( !empty($this->isVatAllow) && !empty($productPricesArray)):?>
            <?php $cartTotal += $quantity * $productPricesArray['display_product_price']; ?>
            <?php elseif(!empty($total_price_config)) : ?>
            <?php $cartTotal += $quantity * $total_price_config; ?>
            <?php else : ?>
            <?php $cartTotal += $quantity * $this->productPrice[$getProduct->product_id]; ?>
            <?php endif;?>
          </div>
        </div>
      </div>  
    </li>
<?php endforeach; ?>
</ul>
<?php if( !empty($this->isOtherModule) && $this->isOtherModule == 1 ) : ?>
  	</div>
  </div>
</div>
<?php endif; ?>
<?php $cartTotal = $this->translate('Total: %s', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cartTotal));?>
<?php if(!empty($this->isOtherModule)): ?>
  <div id="cart_pulldown_options" class="seocore-pulldown-footer">
    <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true) ?>"><?php echo $this->translate("View Cart") ?></a>
    <strong>
      <span id="sitestoreproduct_update_total" class="fright">
        <?php echo $cartTotal; ?>
      </span>
    </strong>
  </div>
<?php endif; ?>
<?php elseif( !empty($this->isOtherModule) ): ?>
  <?php if( $this->isOtherModule == 1 ) : ?>
    <div class="seocore_pulldown_item_list">
      <div class="sitemenu_pulldown_nocontent_msg">
        <?php echo $this->translate("Your shopping cart is empty."); ?>
      </div>
    </div>
  <?php else: ?>
    <div class="m10 txt_center">
      <?php echo $this->translate("Your shopping cart is empty."); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
   var sitestoreproduct_product_in_cart = '<?php echo $this->getProductCountStr; ?>';
   var sitestoreproduct_update_total = '<?php echo empty($cartTotal) ? false : $cartTotal; ?>';
<?php if( !empty($this->isOtherModule)): ?>
  <?php if( $this->isOtherModule == 1 ) : ?>
    en4.core.runonce.add(function(){
      new SEAOMooVerticalScroll('sitemenu_cart_main_right_content_area', 'sitemenu_cart_scroll_main_right_content', {});
    });
  <?php endif; ?>
  showCartProductCount(<?php echo $this->cartProductCounts ?>);
<?php endif; ?>
</script>