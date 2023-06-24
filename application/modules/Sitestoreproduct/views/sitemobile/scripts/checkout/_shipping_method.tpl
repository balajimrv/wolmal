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

<div>
  <?php
  if (!empty($this->stores_products)) :
    $cart_products_detail = @unserialize(str_replace("::?::", "'", $this->cart_products_detail));
    $store_shipping_method = @unserialize($this->shipping_method);
    $stores_product = @unserialize($this->stores_products);
    $checkout_store_name = @unserialize($this->checkout_store_name);
    $store_product_types = @unserialize($this->store_product_types);
    $seller_no = 0;

    foreach ($stores_product as $key => $value):
      ?>
    <div class="clr t_l cont-sep checkout-subheading b_medium"><b><?php echo $checkout_store_name[$key] ?></b></div>

      <!--      //IF VIEWER NOT SELECT ANY SHIPPING METHOD-->
      <div> <span id="shipping_method_missing_<?php echo $key ?>" class="r_text f_small mbot5 dblock"> </sapn></div>
      <?php
      // IF THERE IS ONLY DOWNLOADABLE PRODUCTS
      if (empty($store_product_types[$key])) :
        ?>
        <div class="tip"> 
          <span><?php echo $this->translate("You have added only downloadable products from this store, so shipping method is not required.") ?></span> 
        </div>
        <?php
        continue;
      endif;

      // IF THERE IS NO SHIPPING METHOD DEFINE BY SELLER THAN VIEWER CAN'T PURCHASE THEM
      if (count($store_shipping_method[$key]) == 0) :
        $no_shipping_method_stores[] = $key;
        ?>
        <div class="clr o_hidden" id="no_shipping_method_message_<?php echo $key ?>">
          <p>
            <?php echo $this->translate("There are no shipping methods available for this store yet. So, please remove products of this store from your cart to complete your purchase. To remove products, %s", '<a href="javascript:void(0)" onClick="noShippingMethod(' . $key . ')">click here</a>') ?>
          </p>
        </div>'

        <div class="clr o_hidden" id="no_shipping_method_products_<?php echo $key ?>" style="display:none">
          <p id="product_remove_message_<?php echo $key ?>">
          <h4><?php echo $this->translate("Remove Products?") ?></h4>
          <input type="checkbox" id="no_shipping_method_<?php echo $key ?>" checked="checked" />
          <?php echo $this->translate("Yes, remove products of this store from my cart."); ?>
          </p>
        </div>
        <?php
      else :
        ++$seller_no;

        if (count($store_shipping_method[$key]) == 1):
          $checked = 'checked="checked" style="display:none"';
        else:
          $checked = "";
        endif;

        foreach ($store_shipping_method[$key] as $index => $item) :
          ?>
        <input type="radio" name="shipping_method_<?php echo $key ?>" <?php echo $checked ?>  value="<?php echo $index ?>" onchange="shippingMethodMissing(<?php echo $key ?>)" data-role="none" class="fleft">
          <div class="o_hidden">
            <lable><?php echo $item['name'] ?> (<?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item['charge']) ?>)</lable><br>
            <span class="t_light f_small">(<?php echo $this->translate('Delivered in ') . $item['delivery_time'] ?>)</span>
          </div> 
          <input type="hidden" id="shipping_price_<?php echo $key . '_' . $index ?>" value="<?php echo @round($item['charge'], 2) ?>">
          <input type="hidden" id="shipping_method_name_<?php echo $key . '_' . $index ?>" value="<?php echo $item['name'] ?>">
          <input type="hidden" id="delivery_time_<?php echo $key . '_' . $index ?>" value="<?php echo $item['delivery_time'] ?>">
          <br class="clr" />
          <?php
        endforeach;
      endif;

    endforeach;
  endif;
  ?>
</div>
<div class='buttons'> 
  <button type='button' data-theme="b" name="continue" onclick="shippingMethod()" class="m10 fright">
    <?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_3" class="t_center clr"></div>
</div>
<script type="text/javascript">

    function noShippingMethod(store_id)
    {
      $('no_shipping_method_message_' + store_id).style.display = 'none';
      $('no_shipping_method_products_' + store_id).style.display = 'block';
    }

    function shippingMethod()
    {
      var shipping_method_missing = 0;
      var index = 0;
      var checkout_process_shipping_method = '';
      var shipping_method = new Array(<?php echo $seller_no; ?>);
<?php
foreach ($stores_product as $key => $value) :
  // IF THERE IS ONLY DOWNLOADABLE PRODUCTS
  if (empty($store_product_types[$key])) :
    continue;
  endif;
  ?> 
        if ($.mobile.activePage.find("#no_shipping_method_<?php echo $key ?>").length)
        { 
          if ($.mobile.activePage.find("#no_shipping_method_<?php echo $key ?>").attr('checked') == false || $.mobile.activePage.find("#no_shipping_method_products_<?php echo $key ?>").css("display")== 'none')
          {
            shipping_method_missing = 1;
            $.mobile.activePage.find('#shipping_method_missing_' +<?php echo $key ?>).html('<?php echo $this->translate("Please choose a shipping method for %s store", $checkout_store_name[$key]) ?>');
          }
        }
        else
        {

          var method = $.mobile.activePage.find('input[name=shipping_method_<?php echo $key; ?>]:checked').val();

          if (method.length == 0)
          {
            $.mobile.activePage.find('#shipping_method_missing_' +<?php echo $key ?>).html('<?php echo $this->translate("Please choose a shipping method for %s store", $checkout_store_name[$key]) ?>');
            shipping_method_missing = 1;
          }
        }

        if ($.mobile.activePage.find('#shipping_price_' +<?php echo $key; ?> + '_' + method).length)
        { 
          if (shipping_method_missing == 0)
          {
            var shipping_price = $.mobile.activePage.find('#shipping_price_' +<?php echo $key; ?> + '_' + method).val();
            var shipping_method_name = $.mobile.activePage.find('#shipping_method_name_' +<?php echo $key; ?> + '_' + method).val();
            var delivery_time = $.mobile.activePage.find('#delivery_time_' +<?php echo $key; ?> + '_' + method).val();
            shipping_method[index] = new Array(4);
            shipping_method[index][0] = <?php echo $key; ?>;
            shipping_method[index][1] = shipping_method_name;
            shipping_method[index][2] = shipping_price;
            shipping_method[index++][3] = delivery_time;
          }
        }
<?php endforeach; ?>
var no_shipping_method_stores = '';
<?php if (!empty($no_shipping_method_stores)) : ?>
        no_shipping_method_stores = '<?php echo @serialize($no_shipping_method_stores) ?>';
<?php endif; ?>
      if (shipping_method_missing == 0)
      {
        var store_shipping_method = shipping_method.toString();
        checkout(4, store_shipping_method, no_shipping_method_stores);
      }
    }

    function shippingMethodMissing(key)
    {
      $('shipping_method_missing_' + key).html( '');
    }
</script>