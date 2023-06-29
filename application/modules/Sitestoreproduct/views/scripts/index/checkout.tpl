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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
?>

<!-- IF VIEWER CART IS EMPTY -->
<?php if( !empty($this->sitestoreproduct_checkout_viewer_cart_empty) && !empty($this->store_id)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Your shopping cart does not have products from this store. Please %s for continue shopping.", $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'home'),$this->translate("click here"))); ?>
    </span>
  </div> 
<?php return; endif; ?> 

<?php if( !empty($this->sitestoreproduct_checkout_viewer_cart_empty) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Your shopping cart is empty. Please %s for continue shopping.", $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'home'),$this->translate("click here"))); ?>
    </span>
  </div> 
<?php return; endif; ?> 

<?php // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
if( !empty($this->sitestoreproduct_checkout_no_payment_gateway_enable) ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no payment gateway enable by the site administrator. So you can't process for checkout process. Please contact to site administrator."); ?>
    </span>
  </div> 
<?php return;
  endif; 
?>

<?php // IF NO PAYMENT GATEWAY ENABLE BY THE SELLER
if( !empty($this->sitestoreproduct_checkout_store_no_payment_gateway_enable) ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There is no payment gateway enabled by the seller of the products added into your cart. So, please contact respective sellers to complete your purchase."); ?>
    </span>
  </div> 
<?php return;
  endif; 
?> 

<?php // IF THERE IS NO COUNTRY AVAILABLE FOR SHIPPING
if( !empty($this->sitestoreproduct_checkout_no_region_enable) ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No Country is enable by site admin. So you can't complete checkout process.");?>
    </span>
  </div> 
<?php return;
  endif; 
?>


  <?php
    if($this->sitestoreproduct_checkout_flag == 1 || !empty($this->sitestoreproduct_logged_in_viewer)): ?>
    
<section class="sitestoreproduct_checkout_process_form">
	<ul> 
    <?php $checkout_process_no = 1; $is_shipping_method = 1;
    //IF VIEWER IS LOGGED-IN THEN DON'T SHOW LOGIN STEP.
    if( empty($this->sitestoreproduct_logged_in_viewer) ): $checkout_process_no = 0;
  	?>
  	<li>
      <div>
       	<div id="sitestoreproduct_checkout_process_1" class="sitestoreproduct_checkout_process_normal seaocore_txt_light bold mbot10" onclick="checkoutProcess(1)" id="checkout_link_1">
        	<span class="seq">1.</span>
          <?php echo $this->translate('Login'); ?>
          <span id="sitestoreproduct_checkout_edit_1" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate("Edit")?></a></span>
      	</div>
      </div>
      <div id="checkout_1">
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_login_member.tpl'; ?>
      </div>
    </li>
  <?php endif; ?>
    <li>
      <div>
          
<?php
    
    $showShippingAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.virtual.product.shipping', 1); 
?>
<?php if( (!empty($this->sitestoreproduct_virtual_product) && !empty($showShippingAddress)) || !empty($this->sitestoreproduct_other_product_type)) :
    $defaultAddressTitle = 'Billing / Shipping Address';
else:
    $defaultAddressTitle = 'Billing Address';
endif;
    ?>      
        <div id="sitestoreproduct_checkout_process_2" class="sitestoreproduct_checkout_process_normal seaocore_txt_light bold mbot10" onclick="checkoutProcess(2)" id="checkout_link_2">
        	<span class="seq"><?php echo (2 - $checkout_process_no) . '.' ?></span>
          <?php echo $this->translate($defaultAddressTitle); ?>
          <span id="sitestoreproduct_checkout_edit_2" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate("Edit")?></a></span>
         </div>
      </div>
      <div id="checkout_2">
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_billing_address.tpl';?>
      </div>
    </li>
  <?php if( !empty($this->sitestoreproduct_other_product_type) ) : $is_shipping_method = 0;?>
    <li>
      <div>
        <div id="sitestoreproduct_checkout_process_3" class="sitestoreproduct_checkout_process_normal seaocore_txt_light bold mbot10" onclick="checkoutProcess(3)" id="checkout_link_3" >
        	<span class="seq"><?php echo (3 - $checkout_process_no) . '.' ?></span>
	        <?php echo $this->translate('Shipping Methods'); ?>
          <span id="sitestoreproduct_checkout_edit_3" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate("Edit")?></a></span>
      	</div>    
      </div>  
      <div id="checkout_3"></div>
    </li>
  <?php endif; ?>
    <li>
      <div>
        <div id="sitestoreproduct_checkout_process_4" class="sitestoreproduct_checkout_process_normal seaocore_txt_light bold mbot10" onclick="checkoutProcess(4)" id="checkout_link_4">
        	<span class="seq"><?php echo (4 - $checkout_process_no - $is_shipping_method) . '.' ?></span>
        	<?php echo $this->translate('Payment Method'); ?>
      		<span id="sitestoreproduct_checkout_edit_4" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate("Edit")?></a></span>
        </div>
      </div>
      <div id="checkout_4">
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_payment_information.tpl'; ?>
      </div>
    </li>
    <li>
      <div> 
        <div id="sitestoreproduct_checkout_process_5" class="sitestoreproduct_checkout_process_normal seaocore_txt_light bold mbot10" onclick="checkoutProcess(5)" id="checkout_link_5" >
        	<span class="seq"><?php echo (5 - $checkout_process_no - $is_shipping_method) . '.' ?></span>
        	<?php echo $this->translate('Order Review'); ?>
      		<span id="sitestoreproduct_checkout_edit_5" style="display:none;" class="fright f_small"><a href="javascript:void(0)"><?php echo $this->translate("Edit")?></a></span>
        </div>
      </div>
      <div id="checkout_5"></div>
    </li> 
 

<script type="text/javascript"> 
var checkout_flag;     //ALLOW CHECKOUT PROCESS UPWARD.
var sitestoreproduct_address;
var checkout_process_billing_address;
var checkout_process_shipping_address;
var sitestoreproduct_checkout_process_shipping_method;
var sitestoreproduct_checkout_process_payment_information;
<?php if( empty($this->sitestoreproduct_logged_in_viewer) ) : ?>
  new Fx.Slide('checkout_2').hide();
<?php endif; ?>
<?php if( !empty($this->sitestoreproduct_other_product_type) ) : ?>
new Fx.Slide('checkout_3').hide();  // Remove Sliding on page load
//new Fx.Slide('checkout_3').toggle();  // Give sliding on page load
<?php endif; ?>
new Fx.Slide('checkout_4').hide();
new Fx.Slide('checkout_5').hide();

if( $('sitestoreproduct_checkout_process_address') && $('sitestoreproduct_checkout_process_payment') )
{
  var sitestoreproduct_checkout_process_address_toggle = new Fx.Slide('sitestoreproduct_checkout_process_address', {resetHeight : true}).hide();
  var sitestoreproduct_checkout_process_payment_toggle = new Fx.Slide('sitestoreproduct_checkout_process_payment', {resetHeight : true}).hide();
}
if( $('sitestoreproduct_checkout_process_shipping') )
{
  var sitestoreproduct_checkout_process_shipping_toggle = new Fx.Slide('sitestoreproduct_checkout_process_shipping', {resetHeight : true}).hide();
}
</script>
 </ul>
</section>
<?php endif;  
    if($this->sitestoreproduct_checkout_flag == 2) :
?>

<div style="display:none;" id="grand_total_ttl">
    
    <?php
        include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_order_review.tpl';
    ?>
</div>

<?php
endif;

if($this->sitestoreproduct_checkout_flag == 3) :
  if( !empty($this->sitestoreproduct_other_product_type) ) : 
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_shipping_method.tpl'; 
  endif;
elseif($this->sitestoreproduct_checkout_flag == 5) :
  include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/checkout/_order_review.tpl'; 
endif;
?>

<script type="text/javascript"> 
//Function will execute the checkout process step by step depending on process no  
function checkout(process_no, param, no_shipping_method_stores)
{
  if(typeof(param) === 'undefined') param = '';
  if(typeof(no_shipping_method_stores) === 'undefined') no_shipping_method_stores = '';
  checkout_flag = process_no; 

  var redirectUrl;
  <?php if( !empty($this->store_id) && empty($this->isPaymentToSiteEnable)) : ?>
      redirectUrl = en4.core.baseUrl + "sitestoreproduct/index/checkout/store_id/<?php echo $this->store_id ?>/placeOrder/"+checkout_flag;
  <?php else: ?>
    redirectUrl = en4.core.baseUrl + 'sitestoreproduct/index/checkout/placeOrder/'+checkout_flag;
  <?php endif; ?>
  // SENDING AJAX REQUEST
  en4.core.request.send(new Request.HTML({
    url : redirectUrl,
    method : 'POST',
    onRequest: function(){
      <?php if( empty($this->sitestoreproduct_logged_in_viewer) || $this->sitestoreproduct_checkout_flag == 2 ): ?>
        $('loading_image_'+(checkout_flag-1)).innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
      <?php endif; ?>
    },
    data : {
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
            no_shipping_method_stores : no_shipping_method_stores
           },
           onFailure: function (xhr) { //XMLHTTPREQUEST
      window.location = 'sitestoreproduct/product/cart';
        return;
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) 
    {
      // IF CHANGE IN VIEWER CART, THEN RETURN TO MANAGE CART
      if( responseHTML == 'return_to_cart' )
      {
        window.location = 'sitestoreproduct/product/cart';
        return;
      }
      
      // NEXT PROCESS IS SHIPPING ADDRESS, SHOW RESULT OF BILLING AND SHIPPING ADDRESS PROCESS
      if( (checkout_flag == 3) && $('sitestoreproduct_checkout_process_address') )
      {
        if( sitestoreproduct_checkout_process_address_toggle.open == false )
          sitestoreproduct_checkout_process_address_toggle.toggle();

        $('sitestoreproduct_checkout_process_billing_address').innerHTML = checkout_process_billing_address;
        if( $('sitestoreproduct_checkout_process_shipping_address') )
          $('sitestoreproduct_checkout_process_shipping_address').innerHTML = checkout_process_shipping_address;
      }

      // NEXT PROCESS IS PAYMENT INFORMATION, SHOW RESULT OF SHIPPING METHODS PROCESS
      if( (checkout_flag == 4) && $('sitestoreproduct_checkout_process_shipping') && param)
      {
        if( sitestoreproduct_checkout_process_shipping_toggle.open == false )
          sitestoreproduct_checkout_process_shipping_toggle.toggle();

        $('sitestoreproduct_checkout_process_shipping_method').innerHTML = sitestoreproduct_checkout_process_shipping_method;
      }

      // NEXT PROCESS IS ORDER REVIEW, SHOW RESULT OF PAYMENT INFORMATION PROCESS
      if( (checkout_flag == 5) && $('sitestoreproduct_checkout_process_payment') )
      {
        if( sitestoreproduct_checkout_process_payment_toggle.open == false )
          sitestoreproduct_checkout_process_payment_toggle.toggle();

        $('sitestoreproduct_checkout_process_payment_information').innerHTML = sitestoreproduct_checkout_process_payment_information;
      }
      
      if( checkout_flag == 4 )
      {
        <?php if( empty($this->other_product_type_exist) ) : ?>
          if($('sitestoreproduct_checkout_process_shipping') )
            $('sitestoreproduct_checkout_process_shipping').style.display = 'none';
        <?php endif; ?>
      }
      

      <?php if( empty($this->sitestoreproduct_logged_in_viewer) || $this->sitestoreproduct_checkout_flag == 2 ): ?>
        $('loading_image_'+(checkout_flag-1)).innerHTML = '';
        new Fx.Slide('checkout_'+(checkout_flag-1), {resetHeight : true}).toggle();
      <?php endif; ?>


      if( checkout_flag == 5 )
      {
        $('checkout_'+checkout_flag).innerHTML = responseHTML;
      }

      if( checkout_flag == 3 )
      {
        <?php if( empty($this->sitestoreproduct_other_product_type) ): ?>
          if( $('checkout_3') )
            $('checkout_'+checkout_flag).innerHTML = responseHTML;
          else
            checkout_flag = 4;
          
          $('sitestoreproduct_checkout_edit_'+(checkout_flag - 2)).style.display = 'block';
          $('sitestoreproduct_checkout_process_'+checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
      $('sitestoreproduct_checkout_process_'+checkout_flag).addClass('sitestoreproduct_checkout_process_current');
      
      $('sitestoreproduct_checkout_process_'+(checkout_flag - 2)).removeClass('sitestoreproduct_checkout_process_current');
      $('sitestoreproduct_checkout_process_'+(checkout_flag - 2)).addClass('sitestoreproduct_checkout_process_completed');
          new Fx.Slide('checkout_'+checkout_flag, {resetHeight : true}).toggle();
        <?php else: ?>
          $('sitestoreproduct_checkout_edit_'+(checkout_flag - 1)).style.display = 'block';
          $('sitestoreproduct_checkout_process_'+checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
      $('sitestoreproduct_checkout_process_'+checkout_flag).addClass('sitestoreproduct_checkout_process_current');
      
      $('sitestoreproduct_checkout_process_'+(checkout_flag - 1)).removeClass('sitestoreproduct_checkout_process_current');
      $('sitestoreproduct_checkout_process_'+(checkout_flag - 1)).addClass('sitestoreproduct_checkout_process_completed');
          $('checkout_'+checkout_flag).innerHTML = responseHTML;
          new Fx.Slide('checkout_'+checkout_flag, {resetHeight : true}).toggle();
        <?php endif; ?>
      }
      else
      {
        $('sitestoreproduct_checkout_edit_'+(checkout_flag - 1)).style.display = 'block';
        $('sitestoreproduct_checkout_process_'+checkout_flag).removeClass('sitestoreproduct_checkout_process_normal');
        $('sitestoreproduct_checkout_process_'+checkout_flag).addClass('sitestoreproduct_checkout_process_current');

        $('sitestoreproduct_checkout_process_'+(checkout_flag - 1)).removeClass('sitestoreproduct_checkout_process_current');
        $('sitestoreproduct_checkout_process_'+(checkout_flag - 1)).addClass('sitestoreproduct_checkout_process_completed');
      
        new Fx.Slide('checkout_'+process_no, {resetHeight : true}).toggle();
      }
    }
  }),{'force':true}
 );
}

// FOR UPWARD CHECKOUT PROCESS
function checkoutProcess(process_no)
{
  // IF PROCESS NO IS UPWARD FROM THE CURRENT PROCESS NO THEN ONLY SHOW THAT PROCESS CONTENT
  if( process_no < checkout_flag )
  {
    if( (checkout_flag == 4) )
    {
      if( $('checkout_3') )
      {
        new Fx.Slide('checkout_'+process_no).toggle();
      }
      else
      {
        <?php if( empty($this->sitestoreproduct_other_product_type) ): ?>
           new Fx.Slide('checkout_2', {resetHeight : true}).toggle();
        <?php else: ?>
          new Fx.Slide('checkout_3').toggle();     
        <?php endif; ?>
      }
    }
    else
    {
      new Fx.Slide('checkout_'+process_no, {resetHeight : true}).toggle();
    }
    
    for(index = process_no; index <= checkout_flag; index++)
    {
      if( $('sitestoreproduct_checkout_edit_'+index) )
        $('sitestoreproduct_checkout_edit_'+index).style.display = 'none';
    }

    new Fx.Slide('checkout_'+checkout_flag, {resetHeight : true}).toggle();

    if( $('sitestoreproduct_checkout_process_address') && $('sitestoreproduct_checkout_process_payment') )
    {
      switch(process_no)
      {
        case 1:
          if( sitestoreproduct_checkout_process_address_toggle.open == true )
            sitestoreproduct_checkout_process_address_toggle.toggle();
        case 2:
          if( $('sitestoreproduct_checkout_process_shipping') )
          {
            if( sitestoreproduct_checkout_process_shipping_toggle.open == true )
              sitestoreproduct_checkout_process_shipping_toggle.toggle();
          }
        case 3:
          if( sitestoreproduct_checkout_process_payment_toggle.open == true )
            sitestoreproduct_checkout_process_payment_toggle.toggle();
          break;
      }
    }
    checkout_flag = process_no;
  }
  else
  {
    return;
  }
}
<?php
//get FB total
 //if($grand_total > Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->earningGroupBy(array('month'=>date('Y-m')))){ 
 if($grand_total > Engine_Api::_()->sitestoreproduct()->getFBTotal()){ ?>
  sesJqueryObject('#ebpay_payment_method_message_10').show();
  sesJqueryObject('.ebpay_payment_btn_label').remove();
  sesJqueryObject('.ebpay_payment_btn').remove();
  sesJqueryObject('.ebpay_payment_btn').attr('disabled','disabled');
  
  sesJqueryObject(document).on('click','.noebcount',function(){
      alert('You Don\'t have enough FB to make payment.');
  })
  
      <?php
    }
?>
sesJqueryObject('#grand_total_ttl').remove();
</script>