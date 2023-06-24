<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: checkout.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
// IF VIEWER CART IS EMPTY
if (!empty($this->sitestoreproduct_checkout_viewer_cart_empty)):
  ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Your shopping cart is empty. Please %s for continue shopping.", $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'home'), $this->translate("click here"))); ?>
    </span>
  </div> 


  <?php
// IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
elseif (!empty($this->sitestoreproduct_checkout_no_payment_gateway_enable)):
  ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no payment gateway enable by the site administrator. So you can't process for checkout process. Please contact to site administrator"); ?>
    </span>
  </div> 


  <?php
// IF THERE IS NO COUNTRY AVAILABLE FOR SHIPPING
elseif (!empty($this->sitestoreproduct_checkout_no_region_enable)):
  ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No Country is enable by site admin. So you can't complete checkout process."); ?>
    </span>
  </div> 
<?php // IF NO PAYMENT GATEWAY ENABLE BY THE SELLER
elseif( !empty($this->sitestoreproduct_checkout_store_no_payment_gateway_enable) ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no payment gateway enabled by the seller of the products added into your cart. So, please contact respective sellers to complete your purchase."); ?>
    </span>
  </div> 
<?php else: ?>

  <?php if ($this->sitestoreproduct_checkout_flag == 1 || !empty($this->sitestoreproduct_logged_in_viewer)): ?>

    <div class="m-cart widthfull store-checkout">
      <ul data-role="listview" data-inset="false" data-icon="false" >
        <?php
        $checkout_process_no = 1;
        //IF VIEWER IS LOGGED-IN THEN DON'T SHOW LOGIN STEP.
        if (empty($this->sitestoreproduct_logged_in_viewer)):
          ?>
          <li id="sitestoreproduct_checkout_process_1" class="ui-bar-c clr" onclick="checkoutProcess(1)" id="checkout_link_1" data-role="list-divider" role="heading">
            <span class="seq"><?php echo ($checkout_process_no++) . '.' ?></span>
            <?php echo $this->translate('Login'); ?>
            <span id="sitestoreproduct_checkout_edit_1" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate('Edit') ?></a></span>
          </li>
          <li>
            <div id="checkout_1">
              <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/checkout/_login_member.tpl'; ?>
            </div>
          </li>
        <?php endif; ?>
          
        <li id="sitestoreproduct_checkout_process_2" class="ui-bar-c clr" onclick="checkoutProcess(2)" id="checkout_link_2" data-role="list-divider" role="heading">
          <span class="seq"><?php echo ($checkout_process_no++) . '.' ?></span>
          <?php echo $this->translate('Billing / Shipping Address'); ?>
          <span id="sitestoreproduct_checkout_edit_2" style="display:none;" class="fright f_small">
            <a href="javascript:void(0)"><?php echo $this->translate('Edit') ?></a>
          </span>
        </li>
        <li>
          <div id="checkout_2">
            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/checkout/_billing_address.tpl'; ?>
          </div>
        </li>
        
        <?php if (!empty($this->sitestoreproduct_other_product_type)) : ?>
          <li id="sitestoreproduct_checkout_process_3" class="ui-bar-c clr" onclick="checkoutProcess(3)" id="checkout_link_3" data-role="list-divider" role="heading">
            <span class="seq"><?php echo ($checkout_process_no++) . '.' ?></span>
            <?php echo $this->translate('Shipping Methods'); ?>
            <span id="sitestoreproduct_checkout_edit_3" style="display:none;" class="fright f_small">
              <a href="javascript:void(0)"><?php echo $this->translate('Edit') ?></a>
            </span>
          </li>   
          <li>
            <div id="checkout_3"></div>
          </li>
        <?php endif; ?>
          
        <li id="sitestoreproduct_checkout_process_4" class="ui-bar-c clr" onclick="checkoutProcess(4)" id="checkout_link_4" data-role="list-divider" role="heading">
          <span class="seq"><?php echo ($checkout_process_no++) . '.' ?></span>
          <?php echo $this->translate('Payment Method'); ?>
          <span id="sitestoreproduct_checkout_edit_4" style="display:none;" class="fright f_small">
            <a href="javascript:void(0)"><?php echo $this->translate('Edit') ?></a>
          </span>
        </li>
        <li>
          <div id="checkout_4">
            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/checkout/_payment_information.tpl'; ?>
          </div>
          <div id="checkout_script_4" style="display: none;"></div>
        </li>

        
        <li id="sitestoreproduct_checkout_process_5" class="ui-bar-c clr" onclick="checkoutProcess(5)" id="checkout_link_5"  data-role="list-divider" role="heading">
          <span class="seq"><?php echo ($checkout_process_no++) . '.' ?></span>
          <?php echo $this->translate('Order Review'); ?>
          <span id="sitestoreproduct_checkout_edit_5" style="display:none;" class="fright f_small">
            <a href="javascript:void(0)"><?php echo $this->translate('Edit') ?></a>
          </span>
        </li>
        <li>
          <div id="checkout_5"></div>
        </li> 


        <script type="text/javascript">
              var checkout_flag;     //ALLOW CHECKOUT PROCESS UPWARD.
              var sitestoreproduct_address;
              var checkout_process_billing_address;
              var checkout_process_shipping_address;
              sm4.core.runonce.add(function() {
    <?php if (empty($this->sitestoreproduct_logged_in_viewer)) : ?>
                  $.mobile.activePage.find('#checkout_2').hide();
    <?php endif; ?>
    <?php if (!empty($this->sitestoreproduct_other_product_type)) : ?>
                  $.mobile.activePage.find('#checkout_3').hide();  // Remove Sliding on page load
                  //new Fx.Slide('checkout_3').toggle();  // Give sliding on page load
    <?php endif; ?>
                $.mobile.activePage.find('#checkout_4').hide();
                $.mobile.activePage.find('#checkout_5').hide();

              });
        </script>
      </ul>
    </div>
  <?php endif; ?>
  <?php
  if ($this->sitestoreproduct_checkout_flag == 3) :
    if (!empty($this->sitestoreproduct_other_product_type)) :
      include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/checkout/_shipping_method.tpl';
    endif;
  elseif ($this->sitestoreproduct_checkout_flag == 5) :
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/checkout/_order_review.tpl';
  endif;
  ?>
  <script type="text/javascript">
  //Function will execute the checkout process step by step depending on process no  
    function checkout(process_no, param, no_shipping_method_stores)
    {
      if (typeof(param) === 'undefined')
        param = '';
      if (typeof(no_shipping_method_stores) === 'undefined')
        no_shipping_method_stores = '';
      checkout_flag = process_no;
      // SENDING AJAX REQUEST
      var redirectUrl;
  <?php if( !empty($this->store_id) && empty($this->isPaymentToSiteEnable)) : ?>
      redirectUrl = sm4.core.baseUrl + "sitestoreproduct/index/checkout/store_id/<?php echo $this->store_id ?>/placeOrder/"+checkout_flag;
  <?php else: ?>
    redirectUrl = sm4.core.baseUrl + 'sitestoreproduct/index/checkout/placeOrder/'+checkout_flag;
  <?php endif; ?>
      sm4.core.request.send({
        url: redirectUrl,
        method: 'POST',
        dataType: 'html',
        beforeSend: function() {
  <?php if (empty($this->sitestoreproduct_logged_in_viewer) || $this->sitestoreproduct_checkout_flag == 2): ?>
            $.mobile.activePage.find('#loading_image_' + (checkout_flag - 1)).html('<img src=' + sm4.core.staticBaseUrl + 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif />');
  <?php endif; ?>
        },
        data: {
            checkout_process : '<?php echo $this->checkout_process; ?>',
            cart_products_detail : '<?php echo $this->cart_products_detail; ?>',
            checkout_store_name : '<?php echo $this->checkout_store_name; ?>',
            stores_products : '<?php echo $this->stores_products; ?>',
            store_product_types : '<?php echo $this->store_product_types; ?>',
            coupon_store_id : '<?php echo $this->coupon_store_id; ?>',
            other_product_type : '<?php echo $this->sitestoreproduct_other_product_type; ?>',
            sitestoreproduct_downloadable_product : '<?php echo $this->sitestoreproduct_downloadable_product; ?>',
            address : '<?php echo $this->address; ?>',
            param : param,
            no_shipping_method_stores : no_shipping_method_stores,
          formatType: 'html',
          format: 'html'
        },
        error: function(xhr) { //XMLHTTPREQUEST
          $.mobile.changePage(sm4.core.baseUrl + 'sitestoreproduct/product/cart');
          return;
        },
        success: function(responseHTML)
        {

          // IF CHANGE IN VIEWER CART, THEN RETURN TO MANAGE CART
          if (responseHTML == 'return_to_cart')
          {
            $.mobile.changePage(sm4.core.baseUrl + 'sitestoreproduct/product/cart');
            return;
          }


  <?php if (empty($this->sitestoreproduct_logged_in_viewer) || $this->sitestoreproduct_checkout_flag == 2): ?>
            $.mobile.activePage.find('#loading_image_' + (checkout_flag - 1)).html('');
            $.mobile.activePage.find('#checkout_' + (checkout_flag - 1)).hide();
  <?php endif; ?>

          // responseHTML = sm4.core.mobiPageHTML(responseHTML);
          if (checkout_flag == 5)
          {
            $.mobile.activePage.find('#checkout_' + checkout_flag).html(responseHTML);
            sm4.core.dloader.refreshPage();
          }
          if (checkout_flag == 4)
          {
            $.mobile.activePage.find('#checkout_script_4').html(responseHTML);
            sm4.core.dloader.refreshPage();
          }

          if (checkout_flag == 3)
          {
  <?php if (empty($this->sitestoreproduct_other_product_type)): ?>
              if ($.mobile.activePage.find('#checkout_3').length)
                $.mobile.activePage.find('#checkout_3').html(responseHTML);
              else
                checkout_flag = 4;

              if (checkout_flag == 4)
              {
                $.mobile.activePage.find('#checkout_script_4').html(responseHTML);
                sm4.core.dloader.refreshPage();
              }
              $.mobile.activePage.find('#sitestoreproduct_checkout_edit_' + (checkout_flag - 2)).css('display', 'block');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).addClass('sitestoreproduct_checkout_process_current');

              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 2)).removeClass('sitestoreproduct_checkout_process_current');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 2)).addClass('sitestoreproduct_checkout_process_completed');

              $.mobile.activePage.find('#checkout_' + checkout_flag).show();
  <?php else: ?>

              $.mobile.activePage.find('#sitestoreproduct_checkout_edit_' + (checkout_flag - 1)).css("display", 'block');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).addClass('sitestoreproduct_checkout_process_current');

              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 1)).removeClass('sitestoreproduct_checkout_process_current');
              $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 1)).addClass('sitestoreproduct_checkout_process_completed');
              $.mobile.activePage.find('#checkout_' + checkout_flag).html(responseHTML);
              sm4.core.dloader.refreshPage();
              $.mobile.activePage.find('#checkout_' + checkout_flag).show();
  <?php endif; ?>
          }
          else
          {
            $.mobile.activePage.find('#sitestoreproduct_checkout_edit_' + (checkout_flag - 1)).css("display", 'block');
            $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
            $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + checkout_flag).addClass('sitestoreproduct_checkout_process_current');

            $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 1)).removeClass('sitestoreproduct_checkout_process_current');
            $.mobile.activePage.find('#sitestoreproduct_checkout_process_' + (checkout_flag - 1)).addClass('sitestoreproduct_checkout_process_completed');

            $.mobile.activePage.find('#checkout_' + process_no).show();
          }

        }
      });
    }

  // FOR UPWARD CHECKOUT PROCESS
    function checkoutProcess(process_no)
    {
      // IF PROCESS NO IS UPWARD FROM THE CURRENT PROCESS NO THEN ONLY SHOW THAT PROCESS CONTENT
      if (process_no < checkout_flag)
      {
        if ((checkout_flag == 4))
        {
          if ($('checkout_3'))
          {
            $.mobile.activePage.find('#checkout_' + process_no).hide();
          }
          else
          {
  <?php if (empty($this->sitestoreproduct_other_product_type)): ?>
              $.mobile.activePage.find('#checkout_2').show();
              //   new Fx.Slide('checkout_2', {resetHeight: true}).toggle();
  <?php else: ?>
              $.mobile.activePage.find('#checkout_3').hide();
              // new Fx.Slide('checkout_3').toggle();
  <?php endif; ?>
          }
        }
        else
        {
          $.mobile.activePage.find('#checkout_' + process_no).show();
          // new Fx.Slide('checkout_' + process_no, {resetHeight: true}).toggle();
        }
        var index;
        for (index = process_no; index <= checkout_flag; index++)
        {
          if ($.mobile.activePage.find('#sitestoreproduct_checkout_edit_' + index).length)
            $.mobile.activePage.find('#sitestoreproduct_checkout_edit_' + index).css("display", 'none');
        }
        $.mobile.activePage.find('#checkout_' + checkout_flag).hide();
        //new Fx.Slide('checkout_' + checkout_flag, {resetHeight: true}).toggle();


        checkout_flag = process_no;
      }
      else
      {
        return;
      }
    }
  </script>
<?php endif; ?>