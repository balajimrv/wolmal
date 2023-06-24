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
  <?php
  foreach ($this->payment_gateway as $payment_method) :
    if (count($this->payment_gateway) == 1 && empty($this->by_cheque_enable)):
      $selected = "checked = 'checked' style='display:none'";
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
    
    if( (($payment_method['plugin'] === 'Payment_Plugin_Gateway_PayPal') || ($payment_method == 'paypal'))):
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
                echo '<div class="sitestoreproduct_payment_method"><input id="'.$paymentGatewayTitle.'" type="radio" name="payment_method" value="'.$paymentGatewayId.'" ' . $selected . ' onchange="paymentMethod(this.value)"><label for="'.$paymentGatewayTitle.'" class="mbot5"><img src="' . $base_url . 'application/modules/Sitegateway/externals/images/'.$paymentGatewayTitle.'.png" title="'.$paymentGatewayTitleUC.'" /></label>';
                echo '<input type="hidden" id="payment_gateway_name_'.$paymentGatewayId.'" value="'.$paymentGatewayTitleUC.'" ><span id="payment_method_message_'.$paymentGatewayId.'" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be able to securely make payment for your order using %s.", $paymentGatewayTitleUC) . '</span></div>';
              }
        }                
    }

    if ($payment_method['plugin'] === 'Payment_Plugin_Gateway_PayPal'):
      ?>
      <div class="sitestoreproduct_payment_method">
        <input id="paypal" type="radio" name="payment_method" value="2" <?php echo $selected ?> onchange="paymentMethod(this.value)"><label for="paypal" class="mbot5"><img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/paypal.png" title="<?php echo $payment_method['title'] ?>" /></label>
        <input type="hidden" id="payment_gateway_name_2" value="<?php echo $payment_method['title'] ?>" >
        <span id="payment_method_message_2" class="mbot5 pleft10">&nbsp;&nbsp;
          <?php echo $this->translate("You will be redirected to %s to make payment for your order.", $payment_method['title']) ?></span></div>
      <?php
    endif;
    
     if( $payment_method == 'cheque' ) :
      $byChequeEnable = true;
    endif;
    
    if( $payment_method == 'cod' ) :
      $codEnable = true;
    endif;
  endforeach;
if( !empty($this->cod_enable) || !empty($codEnable) ) :
    $selected = (empty($this->payment_gateway) && empty($this->by_cheque_enable)) ? "checked = 'checked' style='display:none'" : ""; ?>
     <div class="sitestoreproduct_payment_method"><input type="radio" id="cod" name="payment_method" value="4" <?php echo $selected ?> onchange="paymentMethod(this.value);" />
       <label for="cod"><img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/cod.png" title="<?php echo $this->translate('Cash on Delivery') ?>" /></label>
    <input type="hidden" id="payment_gateway_name_4" value="<?php echo $this->translate('Cash on Delivery') ?>" ></div>
    <?php endif;
  if (!empty($this->by_cheque_enable) || !empty($byChequeEnable)):
    $selected = empty($this->payment_gateway) ? "checked = 'checked' style='display:none'" : "";
    ?>
    <div class="sitestoreproduct_payment_method">
      <input type="radio" id="bycheque" name="payment_method" value="3" <?php echo $selected ?> onchange="paymentMethod(this.value)" />
      <label for="bycheque"><img src="<?php echo $base_url ?>application/modules/Sitestoreproduct/externals/images/check.png" title="<?php echo $this->translate('By Cheque') ?>" /></label>
      <input type="hidden" id="payment_gateway_name_3" value="<?php echo $this->translate('By Cheque') ?>" ></div>
  <?php endif; ?>
<?php endif; ?>
  <div id="cheque" class="pleft10">
    <?php if (!empty($this->admin_cheque_detail)) : ?>
      <div class="o_hidden t_l">
        <div class="clr cont-sep checkout-subheading b_medium">
          <b><?php echo $this->translate("Send Cheque to: ") ?></b>
        </div>
        <div class="clr checkout-subcont"><pre style="margin:0;"><?php echo $this->admin_cheque_detail ?></pre></div>
      </div>
    <?php endif; ?>
    
    <div class="clr cont-sep checkout-subheading b_medium">
      <b><?php echo $this->translate("Please enter the information for your payment.") ?></b>
    </div>
    <div class="checkout-subcont">
      <div class="clr">
        <div class="f_small"><?php echo $this->translate("Cheque No. / Ref. No.") ?></div>
        <input type="text" id="cheque_no" onkeyup="chequeInfo(this.id)">
        <span id="cheque_no_missing" class="r_text f_small"></span>
      </div>
      <div class="clr">
        <div class="f_small"><?php echo $this->translate("Account Holder Name") ?></div>
        <input type="text" id="signature" onkeyup="chequeInfo(this.id)">
        <span id="signature_missing" class="r_text f_small"></span>
      </div>
      <div class="clr">
        <div class="f_small"><?php echo $this->translate("Account Number") ?></div>
        <input type="text" id="account_no" onkeyup="chequeInfo(this.id)">
        <span id="account_no_missing" class="r_text f_small"></span>
      </div>
      <div class="clr">
        <div class="f_small"><?php echo $this->translate("Bank Routing Number") ?></div>
        <input type="text" id="routing_no" onkeyup="chequeInfo(this.id)">
        <span id="routing_no_missing" class="r_text f_small"></span>
      </div>
    </div>
  </div>
  <div> <span id="payment_method_missing" class="r_text f_small"></span> </div>
</div>

<div class='buttons'>
  <button type='button' data-theme="b" name="continue" onclick="paymentInformation()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
 	<div id="loading_image_4" class="t_center clr"></div>
</div>

<script type="text/javascript">
var otherPaymentGateways = '<?php echo (!empty($otherPaymentGateways) ? json_encode($otherPaymentGateways) : '[]'); ?>'; 
    sm4.core.runonce.add(function() {
      if ($.mobile.activePage.find('#payment_method_message_1').length)
        $.mobile.activePage.find('#payment_method_message_1').hide();
      if ($.mobile.activePage.find('#payment_method_message_2').length)
        $.mobile.activePage.find('#payment_method_message_2').hide();
      if ($.mobile.activePage.find('#cheque').length)
        $.mobile.activePage.find('#cheque').hide();
      if ($.mobile.activePage.find('input[name=payment_method]:checked').length)
        paymentMethod($.mobile.activePage.find('input[name=payment_method]:checked').attr('value'));
    });
//If user fill the required field of cheque method then this function will remove the error message
    function chequeInfo(id)
    {
      $.mobile.activePage.find('#'+id + '_missing').html('');
    }

    function paymentMethod(payment_method_value)
    {
      $('payment_method_missing').html('');
      if ($.mobile.activePage.find('#payment_method_message_1').length)
        $.mobile.activePage.find('#payment_method_message_1').hide();
      if ($.mobile.activePage.find('#payment_method_message_2').length)
        $.mobile.activePage.find('#payment_method_message_2').hide();
      $.mobile.activePage.find('#cheque').hide();
      
      if (payment_method_value == 1)
        $.mobile.activePage.find('#payment_method_message_1').show();
      else if (payment_method_value == 2)
        $.mobile.activePage.find('#payment_method_message_2').show();
      else if (payment_method_value == 3)
        $.mobile.activePage.find('#cheque').show();

    }

    function paymentInformation()
    {
      //If viewer not select any payment method then show error message.
      if ($.mobile.activePage.find('input[name=payment_method]:checked').length == 0)
      {
        $('payment_method_missing').html('<?php echo $this->translate("Please choose a payment method.") ?>');
        return;
      }
      var payment_method = $.mobile.activePage.find('input[name=payment_method]:checked').attr('value');
      if (payment_method == 3)
      {
        var cheque_info_missing = 0;
        if ($.mobile.activePage.find('#cheque_no').val() == "")
        {
          $.mobile.activePage.find('#cheque_no_missing').html('<?php echo $this->translate("Enter Cheque No. / Ref. No.") ?>');
          cheque_info_missing = 1;
        }
        if ($.mobile.activePage.find('#signature').val() == "")
        {
          $.mobile.activePage.find('#signature_missing').html('<?php echo $this->translate("Enter Account Holder Name") ?>');
          cheque_info_missing = 1;
        }
        if ($.mobile.activePage.find('#account_no').val() == "")
        {
          $.mobile.activePage.find('#account_no_missing').html('<?php echo $this->translate("Enter Account Number") ?>');
          cheque_info_missing = 1;
        }
        if ($.mobile.activePage.find('#routing_no').val() == "")
        {
          $.mobile.activePage.find('#routing_no_missing').html('<?php echo $this->translate("Enter Bank Routing Number") ?>');
          cheque_info_missing = 1;
        }

        if (cheque_info_missing == 1)
        {
          return;
        }else{
          payment_method += ','+$.mobile.activePage.find('#cheque_no').val()+','+$.mobile.activePage.find('#signature').val()+','+$.mobile.activePage.find('#account_no').val()+','+$.mobile.activePage.find('#routing_no').val();
        }

      }

      checkout(5, payment_method);
    }

</script>