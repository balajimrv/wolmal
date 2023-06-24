<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _storePaymentGateway.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--START TAB WORK-->
<div class='tabs mbot10'>
  <ul class="navigation">
    <li id="account_information_tab_0">
      <a href="javascript:void(0)" onclick="changePaymentTab(0)">
        <?php echo $this->translate("Account Information") ?>
      </a>
    </li>		
    <li id="payment_method_tab_1">
      <a href="javascript:void(0)" onclick="changePaymentTab(1)">
        <?php echo $this->translate("Payment Methods") ?>
      </a>
    </li>    
  </ul>
</div>
<!--END TAB WORK-->

<!-- START PAYMENT INFORMATION WORK -->
<div id="store_payment_info" style="display: none;">
  <div id="store_payment_gateway_success_message_tab_0" style="display: none;">
    <ul class="form-notices">
      <li>
        <?php echo $this->translate("Changes Saved."); ?>
      </li>
    </ul>
  </div>
  <a href="javascript:void(0)" onclick="openPaymentInfo('paypal')">
    <?php echo $this->translate("Enter PayPal Info") ?>
  </a><br/>
  <div id="gateway_paypal_form" style="display: none;"> 
    <?php echo $this->form->render($this) ?>
  </div><br/>

  <a href="javascript:void(0)" onclick="openPaymentInfo('cheque')">
    <?php echo $this->translate("Enter By Cheque Info") ?>
  </a><br/>
  <div id="gateway_cheque_form" style="display: none;">
    <div id="show_bycheque_form_error" class="seaocore_txt_red" style="display:none;">
      <?php echo $this->translate("Please enter your cheque details.") ?><br/>
    </div>
    <?php echo $this->translate("Enter your cheque details:") ?>
    <p>
      <?php echo $this->translate('Enter your bank account details which buyers will fill in the cheques for making payments for their orders. This information will be shown when buyers choose "By Cheque" method in the "Payment Information" section during their checkout process.') ?>
    </p>
    <textarea id="store_bycheque_detail" rows="5" class="clr dblock mtop10"><?php if( !empty($this->bychequeDetail) ) :  echo trim($this->bychequeDetail); else: echo "Account Name: 
Account No.: 
Bank: 
Bank Branch Address:"; endif; ?></textarea>
  </div><br/>
  
  <div class='buttons' id="store_payment_info_submit" style="display:none;">
    <button type='button' name="save_gateway" onclick="saveStorePaymentInformation();"><?php echo $this->translate("Save") ?></button>
    <span id="store_payment_info_submit_spinner" style="display: none;">
      <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />
    </span>
  </div>
</div>
<!-- END PAYMENT INFORMATION WORK -->

<!-- START SELECT PAYMENT GATEWAY WORK -->
<div id="store_payment_gateway_select">
  <div id="store_payment_gateway_success_message_tab_1" style="display: none;">
    <ul class="form-notices">
      <li>
        <?php echo $this->translate("Changes Saved."); ?>
      </li>
    </ul>
  </div>
  <div id="store_payment_gateway_error" style="display: none;">
    <ul class="form-errors">
      <li>
        <ul class="error">
          <li>
            <?php echo $this->translate("Please enter PayPal info first, then try to enable PapPal gateway.") ?>
          </li>
        </ul>
      </li>
    </ul>
  </div>
  <!-- START WORK OF DEFAULT PAYMENT GATEWAY FOR DOWNPAYMENT AMOUNT -->
  <?php echo $this->translate("Please select the gateways for downpayment amount.") ?>
  <?php if( !empty($this->adminDefaultPaymentGateway) ) : ?>
    <div id="store_default_payment_gateway_error" style="display: none" class="seaocore_txt_red">
      <?php echo $this->translate("Please select at least one payment gateway.") ?>
    </div>
    <div id="default_payment_gateway">
      <?php foreach( $this->adminDefaultPaymentGateway as $gateway ) : ?>
        <?php if( $gateway == 'paypal' ) : ?>
          <input type="checkbox" id="default_payment_gateway_paypal" <?php if($this->storeDefaultPaypalEnable) : echo "checked"; endif; ?>><label for="default_payment_gateway_paypal"><?php echo $this->translate("PayPal") ?></label><br/>
        <?php elseif( $gateway == 'cheque' ) : ?>
          <input type="checkbox" id="default_payment_gateway_cheque" <?php if($this->storeDefaultBychequeEnable) : echo "checked"; endif; ?>><label for="default_payment_gateway_cheque"><?php echo $this->translate("By Cheque") ?></label><br/>
        <?php elseif( $gateway == 'cod' ) : ?>
          <input type="checkbox" id="default_payment_gateway_cod" <?php if($this->storeDefaultCodEnable) : echo "checked"; endif; ?>><label for="default_payment_gateway_cod"><?php echo $this->translate("Cash on Delivery") ?></label><br/>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <!-- END WORK OF DEFAULT PAYMENT GATEWAY FOR DOWNPAYMENT AMOUNT -->

  <!-- START WORK OF PAYMENT GATEWAY FOR REMAINING AMOUNT -->
  <?php echo $this->translate("Please select the gateways for remaining amount.") ?>
  <?php if( !empty($this->adminRemainingPaymentGateway) ) : ?>
    <div id="store_remaining_payment_gateway_error" style="display: none" class="seaocore_txt_red">
      <?php echo $this->translate("Please select at least one payment gateway.") ?>
    </div>
    <div id="remaining_payment_gateway">
      <?php foreach( $this->adminRemainingPaymentGateway as $gateway ) : ?>
        <?php if( $gateway == 'paypal' ) : ?>
          <input type="checkbox" id="remaining_payment_gateway_paypal" <?php if($this->storeRemainingPaypalEnable) : echo "checked"; endif; ?>><label for="remaining_payment_gateway_paypal" checked="checked"><?php echo $this->translate("PayPal") ?></label><br/>
        <?php elseif( $gateway == 'cheque' ) : ?>
          <input type="checkbox" id="remaining_payment_gateway_cheque" <?php if($this->storeRemainingBychequeEnable) : echo "checked"; endif; ?>><label for="remaining_payment_gateway_cheque"><?php echo $this->translate("By Cheque") ?></label><br/>
        <?php elseif( $gateway == 'cod' ) : ?>
          <input type="checkbox" id="remaining_payment_gateway_cod" <?php if($this->storeRemainingCodEnable) : echo "checked"; endif; ?>><label for="remaining_payment_gateway_cod"><?php echo $this->translate("Cash on Delivery") ?></label><br/>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <!-- END WORK OF PAYMENT GATEWAY FOR REMAINING AMOUNT -->

  <?php if( !empty($this->adminDefaultPaymentGateway) || !empty($this->adminRemainingPaymentGateway) ) : ?>
    <div class='buttons' id="store_gateway_submit">
      <button type='button' name="save_gateway" onclick="saveStoreGatewayInformation();"><?php echo $this->translate("Save") ?></button>
      <span id="store_downpayment_gateway_submit_spinner" style="display: none;">
        <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />
      </span>
    </div>
  <?php endif; ?>
</div>
<!-- END SELECT PAYMENT GATEWAY WORK -->


<script type="text/javascript">
en4.core.runonce.add(function() {
  changePaymentTab(0);
});

function changePaymentTab(value) {
  if( value == 0 ) {
    $("account_information_tab_0").addClass("active");
    $("payment_method_tab_1").removeClass("active");
    $("store_payment_info").style.display = 'block';
    $("store_payment_gateway_select").style.display = 'none';
  } else {
    $("payment_method_tab_1").addClass("active");
    $("account_information_tab_0").removeClass("active");
    $("store_payment_info").style.display = 'none';
    $("store_payment_gateway_select").style.display = 'block';
  }
}

function openPaymentInfo(gatewayName) {
  if( gatewayName == 'paypal' ) {
    $("gateway_paypal_form").toggle();
  } else if( gatewayName == 'cheque' ) {
    $("gateway_cheque_form").toggle();
  }
  
  if( $("gateway_paypal_form").style.display == 'block' || $("gateway_cheque_form").style.display == 'block' ) {
    $("store_payment_info_submit").style.display = 'block';
  } else {
    $("store_payment_info_submit").style.display = 'none';
  }
}

function saveStorePaymentInformation() {
  var paypalGatewayDetail = '';
  var bychequeGatewayDetail = '';
  var isPaypalChecked = false;
  var isByChequeChecked = false;
  var display_error;
  
  $("show_bycheque_form_error").style.display = 'none';
  $("show_paypal_form_massges").innerHTML = '';
  if( $("gateway_paypal_form").style.display == 'block' ) {
    isPaypalChecked = "true";
    paypalGatewayDetail = $("sitestoreproduct_payment_info").toQueryString();
  }
  
  if( $("gateway_cheque_form").style.display == 'block' ) {
    isByChequeChecked = "true";
    bychequeGatewayDetail = $("store_bycheque_detail").value;
    if( !bychequeGatewayDetail ) {
      $("show_bycheque_form_error").style.display = 'block';
      return;
    }
  }
  
  en4.core.request.send(new Request.JSON({
    url: en4.core.baseUrl + 'sitestoreproduct/product/set-store-gateway-info',
    method: 'POST',
    data: {
      format: 'json',
      isPaypalChecked : isPaypalChecked,
      paypalGatewayDetail: paypalGatewayDetail,
      bychequeGatewayDetail: bychequeGatewayDetail,
      isByChequeChecked : isByChequeChecked,
      isDownpayment : true,
      store_id: <?php echo $this->store_id ?>
    },
    onRequest: function(){
      $('store_payment_info_submit_spinner').style.display = 'block';
    },
    onSuccess: function(responseJSON) {
      $('store_payment_info_submit_spinner').style.display = 'none';
      // SHOW PAYPAL ERROR MESSAGE, IF ANY
      if( responseJSON.email_error || responseJSON.paypal_info_error || responseJSON.error_message ) {
        display_error = '<ul class="form-errors">';
        if (responseJSON.email_error) {
          display_error += '<li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';
        }
        if (responseJSON.paypal_info_error) {
          display_error += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li>';
        }
        if (responseJSON.error_message) {
          display_error += '<li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li>';
        }
        display_error += '</ul>';
        $("show_paypal_form_massges").innerHTML = display_error;
      }
      
      if( responseJSON.success_message ) {
        $("store_payment_gateway_success_message_tab_0").style.display = 'block';
      }
    }
  }));
}

function saveStoreGatewayInformation() {

  $("store_payment_gateway_success_message_tab_1").style.display = 'none';
  $("store_default_payment_gateway_error").style.display = 'none';
  $("store_remaining_payment_gateway_error").style.display = 'none';
  $("store_payment_gateway_error").style.display = 'none';

  var store_default_payment_gateway_error = 0;
  var store_remaining_payment_gateway_error = 0;
  var default_payment_gateway_paypal = 0;
  var default_payment_gateway_cheque = 0;
  var default_payment_gateway_cod = 0;
  var remaining_payment_gateway_paypal = 0;
  var remaining_payment_gateway_cheque = 0;
  var remaining_payment_gateway_cod = 0;
  var show_default_gateway_error = 1;
  var show_remaining_gateway_error = 1;

  if( $("default_payment_gateway_paypal") ) {
    if( $("default_payment_gateway_paypal").checked ) {
      default_payment_gateway_paypal = 1;
      show_default_gateway_error = 0;
    }
    else
      store_default_payment_gateway_error = 1;
  }

  if( $("default_payment_gateway_cheque") ) {
    if( $("default_payment_gateway_cheque").checked ) {
      default_payment_gateway_cheque = 1;
      show_default_gateway_error = 0;
    }
    else
      store_default_payment_gateway_error = 1;
  }

  if( $("default_payment_gateway_cod") ) {
    if( $("default_payment_gateway_cod").checked ) {
      show_default_gateway_error = 0;
      default_payment_gateway_cod = 1;
    }
    else
      store_default_payment_gateway_error = 1;
  }

  if( $("remaining_payment_gateway_paypal") ) {
    if( $("remaining_payment_gateway_paypal").checked ) {
      remaining_payment_gateway_paypal = 1;
      show_remaining_gateway_error = 0;
    }
    else
      store_remaining_payment_gateway_error = 1;
  }

  if( $("remaining_payment_gateway_cheque") ) {
    if( $("remaining_payment_gateway_cheque").checked ) {
      remaining_payment_gateway_cheque = 1;
      show_remaining_gateway_error = 0;
    }
    else
      store_remaining_payment_gateway_error = 1;
  }

  if( $("remaining_payment_gateway_cod") ) {
    if( $("remaining_payment_gateway_cod").checked ) {
      show_remaining_gateway_error = 0;
      remaining_payment_gateway_cod = 1;
    }
    else
      store_remaining_payment_gateway_error = 1;
  }

  if( (show_remaining_gateway_error && store_remaining_payment_gateway_error) && (store_default_payment_gateway_error && show_default_gateway_error) ) {
    $("store_default_payment_gateway_error").style.display = 'block';
    $("store_remaining_payment_gateway_error").style.display = 'block';
    return;
  } else if( store_default_payment_gateway_error && show_default_gateway_error ) {
    $("store_default_payment_gateway_error").style.display = 'block';
    return;
  } else if( show_remaining_gateway_error && store_remaining_payment_gateway_error ) {
    $("store_remaining_payment_gateway_error").style.display = 'block';
    return;
  }

  en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'sitestoreproduct/product/save-store-payment-info',
      method: 'POST',
      data: {
        format: 'json',
        store_id: '<?php echo $this->store_id ?>',
        default_payment_gateway_paypal : default_payment_gateway_paypal,
        default_payment_gateway_cheque : default_payment_gateway_cheque,
        default_payment_gateway_cod : default_payment_gateway_cod,
        remaining_payment_gateway_paypal : remaining_payment_gateway_paypal,
        remaining_payment_gateway_cheque : remaining_payment_gateway_cheque,
        remaining_payment_gateway_cod : remaining_payment_gateway_cod
      },
      onRequest: function(){
        $("store_downpayment_gateway_submit_spinner").style.display = 'block';
      },
      onSuccess: function(responseJSON) {
        $("store_downpayment_gateway_submit_spinner").style.display = 'none';
        if (responseJSON.changes_saved) {
          document.getElementById("store_payment_gateway_success_message_tab_1").style.display = 'block';
        }

        if (responseJSON.paypalDetailMissing) {
          $("store_payment_gateway_error").style.display = 'block';
        }
      }
    })
  );
}
</script>