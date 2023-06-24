<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _shipping_method.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="m10">
<?php 
if (!empty($this->stores_products)) :    
    $cart_products_detail = @unserialize(str_replace("::?::", "'", $this->cart_products_detail));
    $store_shipping_method = @unserialize($this->shipping_method);
    $stores_product = @unserialize($this->stores_products);
    $checkout_store_name = @unserialize($this->checkout_store_name);
    $store_product_types = @unserialize($this->store_product_types);
    $seller_no = 0;
  
    foreach ($stores_product as $key => $value):
      echo '<div class="mbot5 bold">' . str_replace("::@::", "'", $checkout_store_name[$key]) . '</div>';
      
      //IF VIEWER NOT SELECT ANY SHIPPING METHOD
      echo '<div> <span id="shipping_method_missing_' . $key . '" class="seaocore_txt_red f_small mbot5 dblock"> </sapn></div>';
  
      // IF THERE IS ONLY DOWNLOADABLE PRODUCTS
      if( empty($store_product_types[$key]) ) :
        echo '<div class="tip"> <span>'.$this->translate("You have added only downloadable products from this store, so shipping method is not required.").'</span> </div>';
        continue;
      endif;
  
      // IF THERE IS NO SHIPPING METHOD DEFINE BY SELLER THAN VIEWER CAN'T PURCHASE THEM
      if (count($store_shipping_method[$key]) == 0) :
        $no_shipping_method_stores[] = $key;
        echo '<div class="sitestoreproduct_alert_msg b_medium mbot10" id="no_shipping_method_message_'.$key.'">
                <p>'.
                $this->translate("There are no shipping methods available for this store yet. So, please remove products of this store from your cart to complete your purchase. To remove products, %1sclick here%2s", '<a href="javascript:void(0)" onClick="noShippingMethod('.$key.')">', '</a>').
                '</p>
              </div>';
        
        echo '<div class="sitestoreproduct_alert_msg b_medium mbot10" id="no_shipping_method_products_'.$key.'" style="display:none">
                <p id="product_remove_message_'.$key.'">
                  ';
        echo '<h4>' . $this->translate("Remove Products?") . '</h4>';
        echo '<input type="checkbox" id="no_shipping_method_'.$key.'" checked="checked" />';
        echo $this->translate("Yes, remove products of this store from my cart.");

        echo '  </p>
              </div>';
      else :
        ++$seller_no;
  
        if (count($store_shipping_method[$key]) == 1):
          $checked = "checked = checked style='display:none'";
          else:
          $checked = "";
        endif;
        
        foreach ($store_shipping_method[$key] as $index => $item) :
          echo '<input type="radio" name="shipping_method_' . $key . '" '.$checked.'  value="' . $index . '" onchange="shippingMethodMissing('.$key.')"><div class="o_hidden"><lable>' . $item['name'] . ' (' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item['charge']) . ')</lable><br>';
          echo '<span class="seaocore_txt_light">('. $this->translate('Delivered in ') .$item['delivery_time'].')</span></div>';
          echo '<input type="hidden" id="shipping_price_' . $key . '_' . $index . '" value="' . @round($item['charge'], 2) . '">';
          echo '<input type="hidden" id="shipping_currency_price_' . $key . '_' . $index . '" value="' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item['charge']) . '">';
          echo '<input type="hidden" id="shipping_method_name_' . $key . '_' . $index . '" value="' . $item['name'] . '">';
          echo '<input type="hidden" id="delivery_time_' . $key . '_' . $index . '" value="' . $item['delivery_time'] . '">';
          echo '<br class="clr" />';
        endforeach;
      endif;
 
    endforeach;
  endif;
  ?>
</div>
<div class='buttons'>
  <div class="m10 fleft"><a href="javascript:void(0)" onclick="checkoutProcess(2)"><?php echo $this->translate("&laquo; Back") ?></a></div> 
  <button type='button' name="continue" onclick="shippingMethod()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_3" class="fright mtop10 ptop10" style="display: inline-block;"></div>
</div>
<script type="text/javascript">
  
function noShippingMethod(store_id)
{
  $('no_shipping_method_message_'+store_id).style.display = 'none';
  $('no_shipping_method_products_'+store_id).style.display = 'block';
}
  
function shippingMethod()
{
  var shipping_method_missing = 0;
  var index = 0;
  var checkout_process_shipping_method = '';
  var shipping_method = new Array(<?php echo $seller_no; ?>);
<?php foreach ($stores_product as $key => $value) : 
        // IF THERE IS ONLY DOWNLOADABLE PRODUCTS
        if( empty($store_product_types[$key]) ) :
          continue;
        endif;
?>
    if( document.getElementById("no_shipping_method_<?php echo $key ?>") )
    {
      if( document.getElementById("no_shipping_method_<?php echo $key ?>").checked == false || document.getElementById("no_shipping_method_products_<?php echo $key ?>").style.display == 'none' )
      {
        shipping_method_missing = 1;
        $('shipping_method_missing_'+<?php echo $key ?>).innerHTML = '<?php echo $this->translate("Please choose a shipping method for %s store", $checkout_store_name[$key]) ?>';
      }
    }
    else
    {
      var method = $$('input[name=shipping_method_'+<?php echo $key; ?>+']:checked').get('value');
      if( method.length == 0 )
      {
        $('shipping_method_missing_'+<?php echo $key ?>).innerHTML = '<?php echo $this->translate("Please choose a shipping method for %s store", $checkout_store_name[$key]) ?>';
        shipping_method_missing = 1;
      }
    }

    if( $('shipping_price_'+<?php echo $key; ?>+'_'+method) )
    {
      if( shipping_method_missing == 0 )
      {
        var shipping_price = $('shipping_price_'+<?php echo $key; ?>+'_'+method).value;
        var shipping_method_name = $('shipping_method_name_'+<?php echo $key; ?>+'_'+method).value;
        var delivery_time = $('delivery_time_'+<?php echo $key; ?>+'_'+method).value;

        shipping_method[index] = new Array(4);
        shipping_method[index][0] = <?php echo $key; ?>;
        shipping_method[index][1] = shipping_method_name;
        shipping_method[index][2] = shipping_price;
        shipping_method[index++][3] = delivery_time;

        checkout_process_shipping_method = checkout_process_shipping_method + '<strong class="fleft">' +'<?php echo $checkout_store_name[$key] ?>'+ '</strong>';
        checkout_process_shipping_method += "<div class='clr mbot5 f_small'><span class='fleft'><b>" + shipping_method_name + "</b></span>";
        
        checkout_process_shipping_method += '<span class="fright">'+$('shipping_currency_price_'+<?php echo $key; ?>+'_'+method).value+ '</span>';
        
        checkout_process_shipping_method +='<span class="clr dblock">(<?php echo $this->translate("Delivered in ") ?> '+delivery_time+')</span>';
        
      }
    }
    checkout_process_shipping_method += '</div>';
    <?php endforeach; ?>

    sitestoreproduct_checkout_process_shipping_method = checkout_process_shipping_method;
    <?php if( !empty($no_shipping_method_stores) ) :  ?>
      var no_shipping_method_stores = '<?php echo @serialize($no_shipping_method_stores) ?>';
    <?php else: ?>
      var no_shipping_method_stores = '';
    <?php endif; ?>
    if( shipping_method_missing == 0 )
    {
      var store_shipping_method = shipping_method.toString();
      checkout(4, store_shipping_method, no_shipping_method_stores);
    }
  }
  
  function shippingMethodMissing(key)
  {
    $('shipping_method_missing_'+key).innerHTML = '';
  }
</script>