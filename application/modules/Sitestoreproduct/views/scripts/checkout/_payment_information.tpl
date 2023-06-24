<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _payment_information.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $isNotAllowedOnlinePayment = !empty($this->isNotAllowedOnlinePayment)? $this->isNotAllowedOnlinePayment: null;
$temp_online_gateway = false;
$otherPaymentGateways = array();
?>

<?php $base_url = $this->layout()->staticBaseUrl; ?>
<div class="m10">
  <?php if( !empty($this->totalOrderPriceFree) ) : ?>
    <div class="sitestoreproduct_payment_method">
      <input type="radio" id="free_order" name="payment_method" value="5" checked="checked" style="display: none;" />
      <label for="free_order">
        <span><?php echo $this->translate("Free Order") ?></span>
      </label>
      <input type="hidden" id="payment_gateway_name_5" value="<?php echo $this->translate('Free Order') ?>" />
    </div>
  <?php else: ?>
  <?php foreach ($this->payment_gateway as $payment_method) :
    if( count($this->payment_gateway) == 1 && empty($this->by_cheque_enable) && empty($this->cod_enable) ):
      $selected = "checked = checked style='display:none'";
    else:
      $selected = "";
    endif;
    

    if( ((isset($payment_method['plugin']) && $payment_method['plugin'] === 'Payment_Plugin_Gateway_2Checkout') || ($payment_method == '2checkout'))):
            $temp_online_gateway = true;
      
      if(empty($isNotAllowedOnlinePayment)){
      $payment_method['title'] = $this->translate("2Checkout");
      $twoCheckoutEnable = true;
    echo '<div class="sitestoreproduct_payment_method"><input type="radio" id="2checkout" name="payment_method" value="1" '.$selected.' onchange="paymentMethod(this.value)"><label for="2checkout" class="mbot5"><img src="'.$base_url.'application/modules/Sitestoreproduct/externals/images/2-checkout.png" title="'.$payment_method['title'].'" /></label>';
      echo '<input type="hidden" id="payment_gateway_name_1" value="'.$payment_method['title'].'" >
      <span id="payment_method_message_1" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be redirected to %s to make payment for your order.", $payment_method['title']) . '</span></div>';      
      }
      endif;
  
    if( ((isset($payment_method['plugin']) && $payment_method['plugin'] === 'Payment_Plugin_Gateway_PayPal') || ($payment_method == 'paypal'))):
            $temp_online_gateway = true;
      
      if(empty($isNotAllowedOnlinePayment)){
        $paypalEnable = true;
      echo '<div class="sitestoreproduct_payment_method"><input id="paypal" type="radio" name="payment_method" value="2" '.$selected.' onchange="paymentMethod(this.value)"><label for="paypal" class="mbot5"><img src="'.$base_url.'application/modules/Sitestoreproduct/externals/images/paypal.png" title="PayPal" /></label>';
      echo '<input type="hidden" id="payment_gateway_name_2" value="PayPal" >
       <span id="payment_method_message_2" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be redirected to PayPal to make payment for your order.") . '</span></div>';
      }
      endif;
      
    if(Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
        if ((isset($payment_method['plugin']) && strstr($payment_method['plugin'], 'Sitegateway_Plugin_Gateway_')) || Engine_Api::_()->sitegateway()->isValidGateway($payment_method)) {
            if(is_array($payment_method)) {
                $pluginName = $payment_method['plugin'];
            }
            else {
                $pluginName = 'Sitegateway_Plugin_Gateway_'.$payment_method;
            }
            
            Engine_Api::_()->sitegateway()->getGatewayColumn(array('pluginLike' => 'Sitegateway_Plugin_Gateway_', 'columnName' => 'gateway_id', 'gateway_id' => $gateway_id));

            $temp_online_gateway = true;
            $paymentGateway = Engine_Api::_()->sitegateway()->getGatewayColumn(array('fetchRow' => true, 'plugin' => $pluginName));
            $otherPaymentGateways[] = $paymentGatewayId = $paymentGateway->gateway_id;
            $paymentGatewayTitle = strtolower($paymentGateway->title);
            $paymentGatewayTitleUC = ucfirst($paymentGateway->title);
          
            if (empty($isNotAllowedOnlinePayment)) {
                $otherGatewayEnabled = true;
                $gatewayTtl = $paymentGatewayTitleUC;
                if(strtolower($paymentGatewayTitleUC)=='Payumoney'){
                    $gatewayTtl = "PayUmoney";
              }elseif(strtolower($paymentGatewayTitleUC)=="paypaladaptive"){
                  $gatewayTtl = "PayPal";
              }
                echo '<div class="sitestoreproduct_payment_method"><input id="'.$paymentGatewayTitle.'" type="radio" name="payment_method" value="'.$paymentGatewayId.'" ' . $selected . ' onchange="paymentMethod(this.value)"><label for="'.$paymentGatewayTitle.'" class="mbot5"><img src="' . $base_url . 'application/modules/Sitegateway/externals/images/'.$paymentGatewayTitle.'.png" title="'.$gatewayTtl.'" /></label>';
                echo '<input type="hidden" id="payment_gateway_name_'.$paymentGatewayId.'" value="'.$paymentGatewayTitleUC.'" ><span id="payment_method_message_'.$paymentGatewayId.'" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be able to securely make payment for your order using %s.", $gatewayTtl) . '</span></div>';
                ?>
                <div class="sitestoreproduct_payment_method">
                  <input id="ebpay" class="ebpay_payment_btn" type="radio" name="payment_method" value="5" onchange="paymentMethod(this.value)">
                  <label for="ebpay" class="mbot5 ebpay_payment_btn_label">
                    <img src="<?php echo $base_url.'application/modules/Sitestoreproduct/externals/images/paybyeb.png'; ?>" title="Pay By EB" style="margin-left:2px; margin-bottom:4px;" />
                    <!--<img src="/testing/application/modules/Sitegateway/externals/images/payumoney.png" title="Payumoney">-->
                  </label>
                  <input type="hidden" id="payment_gateway_name_5" value="<?php echo $this->translate('Free Order') ?>">
                  <span id="ebpay_payment_method_message_10" class="mbot5 pleft10" style="display:none;">&nbsp;&nbsp;<a href="javascript:;" class="noebcount"><img src="<?php echo $base_url.'application/modules/Sitestoreproduct/externals/images/paybyeb.png'; ?>" title="Pay By EB" style="margin-left:2px; margin-bottom:4px;" /></a></span>
                  <div><span class="mbot5 pleft10" style="margin-left:8px;">Minimum Order Rs.2000</span></div>
               </div>
               <?php
              }
        }                
    }      
    
    if( $payment_method == 'cheque' ) :
      $byChequeEnable = true;
    endif;
    
    if( $payment_method == 'cod' && (!empty($this->sitestoreproduct_virtual_product) || !empty($this->sitestoreproduct_other_product_type)) ) :
      $codEnable = true;
    endif;
  endforeach;

  
  if( !empty($this->cod_enable) || !empty($codEnable) ) :
    if( empty($twoCheckoutEnable) && empty($paypalEnable) && empty($otherGatewayEnabled) && (empty($this->by_cheque_enable) && empty($byChequeEnable)) ) :
      $showSelected = "checked = checked style='display:none'";
    else:
      $showSelected = "";
    endif;
    echo '<div class="sitestoreproduct_payment_method"><input type="radio" id="cod" name="payment_method" value="4" '.$showSelected.' onchange="paymentMethod(this.value);" /><label for="cod"><img src="'.$base_url.'application/modules/Sitestoreproduct/externals/images/cod.png" title="'.$this->translate('Cash on Delivery').'" /></label>';
    echo '<input type="hidden" id="payment_gateway_name_4" value="'.$this->translate('Cash on Delivery').'" ></div>';
    $temp_payment_gateway_flag = true;
    endif;

  if( !empty($this->by_cheque_enable) || !empty($byChequeEnable) ):
    if( empty($twoCheckoutEnable) && empty($paypalEnable) && empty($otherGatewayEnabled) && (empty($this->cod_enable) && empty($codEnable)) ) :
      $showSelected = "checked = checked style='display:none'";
    else:
      $showSelected = "";
    endif;
    echo '<div class="sitestoreproduct_payment_method"><input type="radio" id="bycheque" name="payment_method" value="3" '.$showSelected.' onchange="paymentMethod(this.value)" /><label for="bycheque"><img src="'.$base_url.'application/modules/Sitestoreproduct/externals/images/check.png" title="'.$this->translate('By Cheque').'" /></label>';
    echo '<input type="hidden" id="payment_gateway_name_3" value="'.$this->translate('By Cheque').'" ></div>';
    $temp_payment_gateway_flag = true;
  endif;
  endif;?>
  
  <div id="cheque" class="pleft10">
    <?php if( !empty($this->admin_cheque_detail) || ( !empty($this->store_id) && empty($this->isPaymentToSiteEnable) && !empty($this->storeChequeDetail) ) ) : ?>
      <div class="sitestoreproduct_payment_l fleft"><b><?php echo $this->translate("Send Cheque to: ") ?></b></div>
      <?php if( !empty($this->admin_cheque_detail) ) : ?>
        <div class="fleft"><pre><?php echo $this->admin_cheque_detail ?></pre></div><br/><br/>
      <?php elseif( !empty($this->store_id) && empty($this->isPaymentToSiteEnable) && !empty($this->storeChequeDetail) ): ?>
        <div class="fleft"><pre><?php echo $this->storeChequeDetail ?></pre></div><br/><br/>
      <?php endif; ?>
    <?php endif; ?>

    <div class="mbot10 mleft10">
      <?php echo $this->translate("Please enter the information for your payment.") ?>
    </div>
    <div class="mbot10">
    	<div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Cheque No. / Ref. No.") ?></div>
      <input type="text" id="cheque_no" onkeyup="chequeInfo(this.id)">
      <span id="cheque_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
  	<div class="mbot10">
    	<div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Account Holder Name") ?></div>
      <input type="text" id="signature" onkeyup="chequeInfo(this.id)">
    	<span id="signature_missing" class="seaocore_txt_red f_small"></span>
    </div>
    <div class="mbot10">
    	<div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Account Number") ?></div>
      <input type="text" id="account_no" onkeyup="chequeInfo(this.id)">
    	<span id="account_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
    <div class="mbot10">
    	<div class="sitestoreproduct_payment_l fleft"><?php echo $this->translate("Bank Routing Number") ?></div>
      <input type="text" id="routing_no" onkeyup="chequeInfo(this.id)">
    	<span id="routing_no_missing" class="seaocore_txt_red f_small"></span>
    </div>
  </div>
  <?php if(count($this->payment_gateway) == 1 && !empty ($temp_online_gateway) && $isNotAllowedOnlinePayment && empty($temp_payment_gateway_flag)):
          echo '<div class ="sitestoreproduct_alert_msg b_medium mbot10"><span><b>'.$this->translate('NO PAYMENT METHODS AVAILABLE.').'</b></span></div>'; $hideContinue = true;  
        endif;
  
        if(!empty ($temp_online_gateway) && !empty($isNotAllowedOnlinePayment)):
          $temp_cart_url = $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
            echo '<div class ="seaocore_txt_red">'.$this->translate('Your purchasable amount exceeds the Threshold Amount, Please <a href="%s">click here</a> to enable online transaction option, by managing your ordered amount.', $temp_cart_url).'</div>';  
        endif; ?>
  <div> <span id="payment_method_missing" class="seaocore_txt_red f_small"></span> </div>
</div>

<div class='buttons'>
  <div class="m10 fleft">
    <a href="javascript:void(0)" onclick="checkoutProcess(3)">
      <?php echo $this->translate("&laquo; Back") ?>
    </a>
  </div>
  <?php if(empty ($hideContinue)):?>
  <button type='button' name="continue" onclick="paymentInformation()" class="m10 fright">
    <?php echo $this->translate("Continue") ?>
  </button>
  <?php endif;?>
 	<div id="loading_image_4" class="fright mtop10 ptop10" style="display: inline-block;"></div>
</div>

<script>
var otherPaymentGateways = '<?php echo (!empty($otherPaymentGateways) ? json_encode($otherPaymentGateways) : '[]'); ?>'; 
if( $('payment_method_message_1') )
  new Fx.Slide('payment_method_message_1').hide();
if( $('payment_method_message_2') )
  new Fx.Slide('payment_method_message_2').hide();
  
var index;
for(index = 0; index < otherPaymentGateways.length; index++) {
  if ($('payment_method_message_'+otherPaymentGateways[index]))
    new Fx.Slide('payment_method_message_'+otherPaymentGateways[index]).hide();
} 
  
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

    var index;
    for(index = 0; index < otherPaymentGateways.length; index++) {
        if (payment_method_value == otherPaymentGateways[index])
          new Fx.Slide('payment_method_message_'+otherPaymentGateways[index], {mode: 'vertical', resetHeight: true}).slideIn().toggle();
        else if (document.getElementById('payment_method_message_'+otherPaymentGateways[index]))
          new Fx.Slide('payment_method_message_'+otherPaymentGateways[index]).slideOut().toggle();  
    }
  
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
    $('payment_method_missing').innerHTML = '<?php echo $this->translate("Please choose a payment method.") ?>';
    return;
  }
  var checkout_process_payment_gateway = '<?php echo $this->translate("Gateway: ") ?>' + $('payment_gateway_name_'+payment_method).value + '<br />';
  if( payment_method == 3 )
  {
    var cheque_info_missing = 0;
    if($('cheque_no').value == "")
    {
      $('cheque_no_missing').innerHTML = '<?php echo $this->translate("Enter Cheque No. / Ref. No.") ?>';
      cheque_info_missing = 1;
    }
    if($('signature').value == "")
    {
      $('signature_missing').innerHTML = '<?php echo $this->translate("Enter Account Holder Name") ?>';
      cheque_info_missing = 1;
    }
    if($('account_no').value == "")
    {
      $('account_no_missing').innerHTML = '<?php echo $this->translate("Enter Account Number") ?>';
      cheque_info_missing = 1;
    }
    if($('routing_no').value == "")
    {
      $('routing_no_missing').innerHTML = '<?php echo $this->translate("Enter Bank Routing Number") ?>';
      cheque_info_missing = 1;
    }
    
    if( cheque_info_missing == 1 )
    {
      return;
    }
    else
    {
      checkout_process_payment_gateway += '<?php echo $this->translate("Cheque No: ")?>'+ $('cheque_no').value + '<br />' + '<?php echo $this->translate("Account Holder: ") ?>'+ $('signature').value + '<br />' + '<?php echo $this->translate("Account No: ") ?>'+ $('account_no').value + '<br />' + '<?php echo $this->translate("Routing No: ") ?>'+ $('routing_no').value;
      payment_method += ','+$('cheque_no').value+','+$('signature').value+','+$('account_no').value+','+$('routing_no').value;
    }
  }
  sitestoreproduct_checkout_process_payment_information = checkout_process_payment_gateway;

  checkout(5, String(payment_method));
}

</script>