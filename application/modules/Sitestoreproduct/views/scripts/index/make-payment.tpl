<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: make-payment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<?php $base_url = $this->layout()->staticBaseUrl; ?>

<?php if( !empty($this->notMakePayment) || !empty($this->productDeleted) || !empty($this->outOfStock) || !empty($this->noPaymentGatewayEnable) || !empty($this->noStorePaymentGatewayEnable) ) : ?>
  <div class="tip">
    <span>
      <?php if( !empty($this->notMakePayment) ) : ?>
        <?php echo $this->translate("You have not permission to make payment for this order.") ?>
      <?php elseif( !empty($this->productDeleted) ) : ?>
        <?php echo $this->translate("Product has been deleted by seller that's why you will not be able to make payment for this order") ?>
      <?php elseif( !empty($this->outOfStock) ) : ?>
        <?php echo $this->translate("The products you have purchased in this order is now out of stock. So you can't make payment for this order.") ?>
      <?php elseif( !empty($this->noPaymentGatewayEnable) ) : ?>
        <?php echo $this->translate("There is no payment gateway enable by the site administrator. So you can't make payment. Please contact to site administrator"); ?>
      <?php elseif( !empty($this->noStorePaymentGatewayEnable) ) : ?>
        <?php echo $this->translate("There is no payment gateway enabled by the seller of the products. So you can't make payment. Please contact to respective sellers."); ?>
      <?php endif; ?>
    </span>
  </div>
<?php return; endif; ?>

 <?php $twoCheckoutGatewayChecked = $paypalGatewayChecked = $bychequeGatewayChecked = $codGatewayChecked = '';
if( empty($this->paypalEnable) && empty($this->by_cheque_enable) && empty($this->cod_enable) ):
  $twoCheckoutGatewayChecked = "checked = checked style='display:none'";
elseif( empty($this->twoCheckoutEnable) && empty($this->by_cheque_enable) && empty($this->cod_enable) ):
  $paypalGatewayChecked = "checked = checked style='display:none'";
elseif( empty($this->twoCheckoutEnable) && empty($this->paypalEnable) && empty($this->cod_enable) ):
  $bychequeGatewayChecked = "checked = checked style='display:none'";
elseif( empty($this->twoCheckoutEnable) && empty($this->by_cheque_enable) && empty($this->paypalEnable) ):
  $codGatewayChecked = "checked = checked style='display:none'";
endif; ?>

<div class="global_form_popup sitestore_makepayment_popup">
	<form id="make_payment_form" method="post">
  <h3><?php echo $this->translate("Enter Payment Information") ?></h3>
  <p class="mtop10 mbot10">
    <?php if( !empty($this->makePayment) ) : ?>
      <?php echo $this->translate("Enter payment information to make payment for this order below:") ?>
    <?php elseif( !empty($this->remainingAmountPayment) ) : ?>
      <?php echo $this->translate("Enter payment information to complete remaining amount payment for this order below. You need to pay %s for your order.", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->amount_need_to_pay)); ?><br/>
    <?php endif; ?>
  </p>
  <div class="m10">
    <?php if( !empty($this->twoCheckoutEnable) ) : ?>
      <div class="sitestoreproduct_payment_method">
        <input type="radio" id="2checkout" name="payment_method" value="1" <?php echo $twoCheckoutGatewayChecked ?> onchange="paymentMethod(this.value)" />
        <label for="2checkout" class="mbot5">
          <img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/2-checkout.png" title="2Checkout" />
        </label>
        <input type="hidden" id="payment_gateway_name_1" value="2Checkout" >
        <span id="payment_method_message_1" class="mbot5 pleft10">
          &nbsp;&nbsp;<?php echo $this->translate("You will be redirected to 2Checkout to make payment for your order.") ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if( !empty($this->paypalEnable) ) : ?>
      <div class="sitestoreproduct_payment_method">
        <input type="radio" id="paypal" name="payment_method" value="2" <?php echo $paypalGatewayChecked ?> onchange="paymentMethod(this.value)" />
        <label for="paypal" class="mbot5">
          <img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/paypal.png" title="PayPal" />
        </label>
        <input type="hidden" id="payment_gateway_name_2" value="PayPal" >
        <span id="payment_method_message_2" class="mbot5 pleft10">
          &nbsp;&nbsp;<?php echo $this->translate("You will be redirected to PayPal to make payment for your order.") ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if( !empty($this->cod_enable) ) : ?>
      <div class="sitestoreproduct_payment_method">
        <input type="radio" id="cod" name="payment_method" value="4" <?php echo $codGatewayChecked ?> onchange="paymentMethod(this.value)" />
        <label for="cod" class="mbot5">
          <img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/cod.png" title="<?php echo $this->translate('Cash on Delivery') ?>" />
        </label>
        <input type="hidden" id="payment_gateway_name_4" value="<?php echo $this->translate('Cash on Delivery') ?>" >
      </div>
    <?php endif; ?>

    <?php if( !empty($this->by_cheque_enable) ) : ?>
      <div class="sitestoreproduct_payment_method">
        <input type="radio" id="bycheque" name="payment_method" value="3" <?php echo $bychequeGatewayChecked ?> onchange="paymentMethod(this.value)" />
        <label for="bycheque" class="mbot5">
          <img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/check.png" title="<?php echo $this->translate('By Cheque') ?>" />
        </label>
        <input type="hidden" id="payment_gateway_name_3" value="<?php echo $this->translate('By Cheque') ?>" >
      </div>
    <?php endif; ?>

    <div id="cheque" class="pleft10">
      <div class="mbot10">
        <div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Cheque No") ?></div>
        <div class="o_hidden">
          <input type="text" id="cheque_no" name="cheque_no" onkeyup="chequeInfo(this.id)" />
          <span id="cheque_no_missing" class="seaocore_txt_red f_small dblock"></span>
        </div>    
      </div>
      <div class="mbot10">
        <div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Account Holder Name") ?></div>
        <div class="o_hidden">
          <input type="text" id="signature" name="signature" onkeyup="chequeInfo(this.id)" />
          <span id="signature_missing" class="seaocore_txt_red f_small dblock"></span>
        </div>  
      </div>
      <div class="mbot10">
        <div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Account Number") ?></div>
        <div class="o_hidden">
          <input type="text" id="account_no" name="account_no" onkeyup="chequeInfo(this.id)" />
          <span id="account_no_missing" class="seaocore_txt_red f_small dblock"></span>
        </div>
      </div>
      <div class="mbot10">
        <div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Bank Routing Number") ?></div>
        <div class="o_hidden">
          <input type="text" id="routing_no" name="routing_no" onkeyup="chequeInfo(this.id)" />
          <span id="routing_no_missing" class="seaocore_txt_red f_small dblock"></span>
        </div>
      </div>
    </div>
    <div> <span id="payment_method_missing" class="seaocore_txt_red f_small"></span> </div>
  </div>
  
  <p>
    <?php if( !empty($this->makePayment) ) : ?>
      <button type='submit'><?php echo $this->translate("Make Payment") ?></button>
    <?php elseif( !empty($this->remainingAmountPayment) ) : ?>
      <button type='submit'><?php echo $this->translate("Pay Remaining Amount") ?></button>
    <?php endif; ?>
    <?php echo $this->translate(" or ") ?> 
    <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
    <?php echo $this->translate("cancel") ?></a>
  </p>
  </form>
</div>

<script type="text/javascript">
$("make_payment_form").addEvent('submit', function(e) {
  e.stop();
  paymentInformation();
});
  
if( $('payment_method_message_1') )
  new Fx.Slide('payment_method_message_1').hide();
if( $('payment_method_message_2') )
  new Fx.Slide('payment_method_message_2').hide();
if( $('cheque') )
  new Fx.Slide('cheque').hide();

if( $$('input[name=payment_method]:checked').get('value') )
  paymentMethod($$('input[name=payment_method]:checked').get('value'));
  
//If user fill the required field of cheque method then this function will remove the error message
function chequeInfo(id)
{
  $(id+'_missing').innerHTML = '';
}

function paymentMethod(payment_method_value)
{
  $('payment_method_missing').innerHTML = '';

  if( payment_method_value == 1 )
    new Fx.Slide('payment_method_message_1', {mode : 'vertical', resetHeight : true}).slideIn().toggle();
  else if( document.getElementById('payment_method_message_1') )
    new Fx.Slide('payment_method_message_1').slideOut().toggle();
  
  if( payment_method_value == 2 )
    new Fx.Slide('payment_method_message_2', {mode : 'vertical', resetHeight : true}).slideIn().toggle();
  else if( document.getElementById('payment_method_message_2') )
    new Fx.Slide('payment_method_message_2').slideOut().toggle();
  
  if( payment_method_value == 3 )
    new Fx.Slide('cheque',{resetHeight : true}).toggle();
  else
    new Fx.Slide('cheque').slideOut().toggle();
}

function paymentInformation()
{
  var payment_method = $$('input[name=payment_method]:checked').get('value');

  //If viewer not select any payment method then show error message.
  if( payment_method.length == 0 )
  {
    $('payment_method_missing').innerHTML = '<?php echo $this->translate("Must choose a payment information method") ?>';
    return;
  }

  if( payment_method == 3 )
  {
    var cheque_info_missing = 0;
    if($('cheque_no').value == "")
    {
      $('cheque_no_missing').innerHTML = '<?php echo $this->translate("Fill cheque no") ?>';
      cheque_info_missing = 1;
    }
    if($('signature').value == "")
    {
      $('signature_missing').innerHTML = '<?php echo $this->translate("Enter account holder name") ?>';
      cheque_info_missing = 1;
    }
    if($('account_no').value == "")
    {
      $('account_no_missing').innerHTML = '<?php echo $this->translate("Enter your account no") ?>';
      cheque_info_missing = 1;
    }
    if($('routing_no').value == "")
    {
      $('routing_no_missing').innerHTML = '<?php echo $this->translate("Enter bank routing no") ?>';
      cheque_info_missing = 1;
    }
    
    if( cheque_info_missing == 1 )
      return;
  }

  $('make_payment_form').submit();
}
</script>

