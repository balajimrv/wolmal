<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-info.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->authenticationSuccess)) : ?> 
    <div id="payment_info_detail" class="p5 sitestoreproduct_dashboard_payment">
        <?php //if( empty($this->passwordError) ) : ?>
        <div class="mbot10">
            <?php echo $this->translate("Enter Account Password:") ?>
        </div>
        <div class="mbot10">
            <input type="password" id="payment_info_password" size="25" />
            <div class="clr seaocore_txt_light f_small mtop5">
                <?php echo $this->translate("Please enter the password for your account on this site to proceed.") ?>
            </div>
            <div id="payment_info_empty_password" class="mtop5 seaocore_txt_red" style="display: none">
                <?php echo $this->translate("Please enter the password.") ?>
            </div>
            <div id="payment_info_password_error" class="mtop5 seaocore_txt_red" style="display: none">
                <?php echo $this->translate("The passwords you entered did not match.") ?>
            </div>
        </div>
        <div class='buttons'>
            <button type='button' id="button" onclick="paymentInfoPassword();"><?php echo $this->translate("Submit") ?></button>
            <div id="payment_info_spinner" style="display: inline-block;"></div>
        </div>
        <?php //endif; ?>
    </div>

    <script type="text/javascript">
        function paymentInfoPassword()
        {
            document.getElementById("payment_info_empty_password").style.display = 'none';
            document.getElementById("payment_info_password_error").style.display = 'none';

            if (document.getElementById("payment_info_password").value == '')
            {
                document.getElementById("payment_info_empty_password").style.display = 'block';
                return;
            }

            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/payment-info',
                method: 'POST',
                onRequest: function () {
                    document.getElementById("payment_info_spinner").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
                },
                data:
                        {
                            password: document.getElementById('payment_info_password').value,
                            store_id: '<?php echo $this->store_id ?>'
                        },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    document.getElementById("payment_info_spinner").innerHTML = '';
                    if (responseHTML == 'payment_info_password_error')
                        document.getElementById("payment_info_password_error").style.display = 'block';
                    else
                        document.getElementById("payment_info_detail").innerHTML = responseHTML;
                }
            }));
        }
    </script>
<?php else: ?>
    <?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

    <?php if (!empty($this->isDownPaymentEnable) && !empty($this->directPayment)) : ?>
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_storePaymentGateway.tpl'; ?>
    <?php else: ?>
        <?php if (empty($this->enablePaymentGateway)) : ?>
            <div> 
                <?php if ($this->paymentMethod == 'paypal'): ?>  
                    <?php echo $this->form->render($this) ?>

                    <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway')): ?>
                        <?php if ($this->showStripeConnect): ?>
                            <?php echo $this->partial('_stripeConnectButton.tpl', 'sitegateway', array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => false, 'resource_type' => 'sitestore_store', 'resource_id' => $this->store_id)); ?>
                        <?php endif; ?>              
                    <?php endif; ?>   

                <?php elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($this->paymentMethod)): ?>

                    <?php if ($this->paymentMethod == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                        <?php echo $this->partial('_stripeConnectButton.tpl', 'sitegateway', array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => false, 'resource_type' => 'sitestore_store', 'resource_id' => $this->store_id)); ?>
                    <?php else: ?>
                        <?php $formName = "form" . ucfirst($this->paymentMethod); ?>
                        <?php echo $this->$formName->render($this) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (count($this->enablePaymentGateway) > 1) : ?>
                <h3><?php echo $this->translate('Payment Methods') ?></h3>
                <p><?php echo $this->translate("Below, you can choose the payment methods that you want to be available to buyers during their checkout process to purchase products from this store."); ?></p>
            <?php else: ?>
                <h3><?php echo $this->translate('Payment Method') ?></h3>
            <?php endif; ?>
            <br/>
            <div id="store_payment_gateway" class="mbot10 sitestore_seller_payment_options">
                <div id="store_payment_gateway_success_message"></div>
                <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>

                    <?php if (empty($paymentGateway)) : ?>
                        <?php $isPaypalEnable = true; ?>
                        <input type="checkbox" id="sitestore_gateway_paypal" onchange="selectStorePaymentGateway(this.id, this.checked);" <?php
                        if (!empty($this->paypalEnable)) : echo "checked";
                        endif;
                        ?>>
                        <label for="sitestore_gateway_paypal"><?php echo $this->translate("PayPal") ?></label><br/>
                        <div id="sitestore_gateway_paypal_form" class="sitestoreproduct_dashboard_payment_method b_medium" <?php
                             if (empty($this->paypalEnable)) : echo 'style="display:none"';
                             endif;
                             ?>> 
                        <?php echo $this->form->render($this) ?><br/>
                        </div>
                    <?php endif; ?>

                    <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                        <?php
                        $gatewayName = strtolower($paymentGateway);
                        $gatewayNameUC = ucfirst($paymentGateway);
                        $gatewayVariableName = "is" . $gatewayNameUC . "Enable";
                        $formName = "form$gatewayNameUC";
                        $gatewyEnabled = $gatewayName . 'Enabled';
                        ?>
                        <?php $gatewayVariableName = true; ?>
                        <input type="checkbox" id="sitestore_gateway_<?php echo $gatewayName; ?>" onchange="selectStorePaymentGateway(this.id, this.checked);" <?php
                        if (!empty($this->$gatewyEnabled)) : echo "checked";
                        endif;
                        $checkedStripeConnect = false;
                        if ($paymentGateway == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0) && !empty($this->showStripeConnectChecked)) {
                            echo "checked";
                            $checkedStripeConnect = true;
                        }
                        ?>>
                        <label for="sitestore_gateway_<?php echo $gatewayName; ?>"><?php echo $this->translate($gatewayNameUC) ?></label><br/>
                        <div id="sitestore_gateway_<?php echo $gatewayName; ?>_form" class="sitestoreproduct_dashboard_payment_method b_medium" <?php
                                 if (empty($this->stripeEnabled) || empty($this->mangopayEnable) || empty($this->paypalAdaptiveEnable)) : echo 'style="display:none"';
                                 endif;
                                 ?> > 
                                 <?php if ($paymentGateway == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                                     <?php echo $this->partial('_stripeConnectButton.tpl', 'sitegateway', array('stripeConnected' => $this->stripeConnected, 'show_stripe_form_massges' => true, 'resource_type' => 'sitestore_store', 'resource_id' => $this->store_id)); ?>
                                 <?php elseif ($paymentGateway == 'mangopay'): ?>
                                     <?php echo $this->mangopayForm->render($this); ?>
                                     <?php echo $this->mangopayBankDetailForm->render($this); ?>
                            <?php elseif ($paymentGateway == 'paypaladaptive'): ?>
                                <?php echo $this->paypalAdaptiveForm->render($this); ?>
                        <?php else: ?>
                            <?php echo $this->$formName->render($this) ?><br/>
                        <?php endif; ?>   
                        </div>
                    <?php endif; ?>          

                    <?php if ($paymentGateway == 1 || $paymentGateway == 'cheque') : ?>
                               <?php $isByChequeEnable = true; ?>
                        <input type="checkbox" id="sitestore_gateway_by_cheque" onchange="selectStorePaymentGateway(this.id, this.checked);" <?php
                    if (!empty($this->bychequeEnable)) : echo 'checked';
                    endif;
                               ?>>
                        <label for="sitestore_gateway_by_cheque"><?php echo $this->translate("By Cheque") ?></label><br/>
                        <div id="sitestore_gateway_by_cheque_form" class="sitestoreproduct_dashboard_payment_method b_medium" <?php
                                if (empty($this->bychequeEnable)) : echo 'style="display:none"';
                                endif;
                                ?>>
                            <div id="show_bycheque_error"></div>
                            <p>
                                <?php echo $this->translate('Enter your bank account details which buyers will fill in the cheques for making payments for their orders. This information will be shown when buyers choose "By Cheque" method in the "Payment Information" section during their checkout process.') ?>
                            </p>
                            <textarea id="store_bycheque_detail" rows="5" class="clr dblock mtop10"><?php
                                if (!empty($this->bychequeDetail)) : echo trim($this->bychequeDetail);
                                else: echo "Account Name: 
Account No.: 
Bank: 
Bank Branch Address:";
                                endif;
                                ?></textarea>
                        </div>
                    <?php endif; ?>

                    <?php if ($paymentGateway == 2 || $paymentGateway == 'cash') : ?>
                        <?php $isCodEnable = true; ?>
                        <input type="checkbox" id="sitestore_gateway_cod" onchange="checkEnableGateway();" <?php
                        if ($this->codEnable) : echo 'checked';
                        endif;
                        ?>>
                        <label for="sitestore_gateway_cod"><?php echo $this->translate("Cash on Delivery") ?></label><br/>
                <?php endif; ?>

            <?php endforeach; ?>
            </div>

            <div class='buttons' id="store_gateway_submit" style="display: none;">
                <button type='button' name="save_gateway" onclick="saveStoreGateway();"><?php echo $this->translate("Save") ?></button>
                <span id="store_gateway_submit_spinner"></span>
            </div>
        <?php endif; ?>

        <script type="text/javascript">
        <?php if (!empty($this->enablePaymentGateway)) : ?>
                function selectStorePaymentGateway(elementId, elementValue)
                {
                    if (elementValue)
                        $(elementId + "_form").style.display = 'block';
                    else
                        $(elementId + "_form").style.display = 'none';

                    checkEnableGateway();
                }

                function checkEnableGateway()
                {
                    var showSubmitButton = false;
            <?php if (!empty($isPaypalEnable)) : ?>
                        if ($("sitestore_gateway_paypal") && $("sitestore_gateway_paypal").checked)
                            showSubmitButton = true;
            <?php endif; ?>

            <?php foreach ($this->enablePaymentGateway as $paymentGateway): ?>
                <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                    <?php
                    $gatewayName = strtolower($paymentGateway);
                    $gatewayNameUC = ucfirst($paymentGateway);
                    $gatewayVariableName = "is" . $gatewayNameUC . "Enable";
                    ?>
                    <?php if (!empty($gatewayVariableName)) : ?>
                                if ($("sitestore_gateway_<?php echo $gatewayName; ?>") && $("sitestore_gateway_<?php echo $gatewayName; ?>").checked)
                                    showSubmitButton = true;
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!empty($isByChequeEnable)) : ?>
                        if ($("sitestore_gateway_by_cheque") && $("sitestore_gateway_by_cheque").checked)
                            showSubmitButton = true;
            <?php endif; ?>
            <?php if (!empty($isCodEnable)) : ?>
                        if ($("sitestore_gateway_cod") && $("sitestore_gateway_cod").checked)
                            showSubmitButton = true;
            <?php endif; ?>

                    if (showSubmitButton)
                        $("store_gateway_submit").style.display = 'block';
                    else
                        $("store_gateway_submit").style.display = 'none';
                }

                function saveStoreGateway()
                {
                    var TempStr = '';
                    var display_error = true;
                    var paypalGatewayDetail = '';
                    var bychequeGatewayDetail = '';
                    var codGatewayDetail = '';
                    var isPaypalChecked = false;
                    var isByChequeChecked = false;
                    var isCodChecked = false;

                    var additionalGateways = new Array();
                    var additionalGatewaysCheckedArray = {};
                    var additionalGatewayDetailArray = {};
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                    <?php
                    $gatewayName = strtolower($paymentGateway);
                    $gatewayNameUC = ucfirst($paymentGateway);
                    $gatewayVariableName = "is" . $gatewayNameUC . "Enable";
                    ?>
                            var gatewayName = '<?php echo $gatewayName ?>';
                            var gatewayNameUC = '<?php echo $gatewayNameUC ?>';

                            window['<?php echo $gatewayName ?>' + 'TempStr'] = '';
                            window['<?php echo $gatewayName ?>' + 'GatewayDetail'] = '';
                            window['is' + '<?php echo $gatewayNameUC ?>' + 'Checked'] = false;

                    <?php if (!empty($gatewayVariableName)) : ?>
                                if ($("sitestore_gateway_<?php echo $gatewayName; ?>") && $("sitestore_gateway_<?php echo $gatewayName; ?>").checked) {
                                    if ($("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>")) {

                                        window['<?php echo $gatewayName ?>' + 'GatewayDetail'] = $("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>").toQueryString();
                                        formId = "sitestoreproduct_payment_info_<?php echo $gatewayName; ?>";
                                        if (formId != 'sitestoreproduct_payment_info_mangopay') {
                                            additionalGatewayDetailArray['<?php echo $gatewayName ?>' + 'GatewayDetail'] = $("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>").toQueryString();
                                        } else if (formId == 'sitestoreproduct_payment_info_mangopay') {
                                            additionalGatewayDetailArray['<?php echo $gatewayName ?>' + 'GatewayDetail'] = {
                                                personalDetail: $("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>").toQueryString(),
                                                accountDetail: $("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>_bank_detail").toQueryString(),
                                            }
                                        }

                                    }

                                    window['is' + '<?php echo $gatewayNameUC; ?>' + 'Checked'] = $("sitestore_gateway_<?php echo $gatewayName; ?>").checked;
                                    additionalGatewaysCheckedArray['is' + '<?php echo $gatewayNameUC; ?>' + 'Checked'] = $("sitestore_gateway_<?php echo $gatewayName; ?>").checked;
                                }
                    <?php endif; ?>

                            if (($("sitestore_gateway_<?php echo $gatewayName; ?>") && !$("sitestore_gateway_<?php echo $gatewayName; ?>").checked)) {
                                additionalGateways.push("1");
                            }
                            else {
                                additionalGateways.push("0");
                            }

                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!empty($isPaypalEnable)) : ?>
                        if ($("sitestore_gateway_paypal") && $("sitestore_gateway_paypal").checked) {
                            paypalGatewayDetail = $("sitestoreproduct_payment_info").toQueryString();
                            isPaypalChecked = $("sitestore_gateway_paypal").checked;
                        }
            <?php endif; ?>

            <?php if (!empty($isByChequeEnable)) : ?>
                        if ($("sitestore_gateway_by_cheque") && $("sitestore_gateway_by_cheque").checked) {
                            bychequeGatewayDetail = $("store_bycheque_detail").value;
                            isByChequeChecked = $("sitestore_gateway_by_cheque").checked;
                        }
            <?php endif; ?>

            <?php if (!empty($isCodEnable)) : ?>
                        if ($("sitestore_gateway_cod") && $("sitestore_gateway_cod").checked) {
                            isCodChecked = $("sitestore_gateway_cod").checked;
                        }
            <?php endif; ?>

                    if ((additionalGateways.contains(0)) && ($("sitestore_gateway_paypal") && !$("sitestore_gateway_paypal").checked) && ($("sitestore_gateway_by_cheque") && !$("sitestore_gateway_by_cheque").checked) && ($("sitestore_gateway_cod") && !$("sitestore_gateway_cod").checked))
                        return;


                    en4.core.request.send(new Request.JSON({
                        url: en4.core.baseUrl + 'sitestoreproduct/product/set-store-gateway-info',
                        method: 'POST',
                        data: {
                            format: 'json',
                            isPaypalChecked: isPaypalChecked,
                            isByChequeChecked: isByChequeChecked,
                            isCodChecked: isCodChecked,
                            paypalGatewayDetail: paypalGatewayDetail,
                            additionalGatewaysCheckedArray: additionalGatewaysCheckedArray,
                            additionalGatewayDetailArray: additionalGatewayDetailArray,
                            bychequeGatewayDetail: bychequeGatewayDetail,
                            store_id: <?php echo $this->store_id ?>
                        },
                        onRequest: function () {
                            $('store_gateway_submit_spinner').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
            <?php if (!empty($isPaypalEnable)) : ?>
                                document.getElementById('show_paypal_form_massges').innerHTML = '';
            <?php endif; ?>
                            if (document.getElementById('show_mangopay_bank_detail_form_massges')) {
                                document.getElementById('show_mangopay_bank_detail_form_massges').innerHTML = '';
                            }
                            
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                    <?php
                    $gatewayName = strtolower($paymentGateway);
                    $gatewayVariableName = "is" . $gatewayNameUC . "Enable";
                    ?>
                    <?php if (!empty($gatewayVariableName)) : ?>
                                        if (document.getElementById('show_<?php echo $gatewayName; ?>_form_massges')) {
                                            document.getElementById('show_<?php echo $gatewayName; ?>_form_massges').innerHTML = '';
                                        }
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!empty($isByChequeEnable)) : ?>
                                document.getElementById("show_bycheque_error").innerHTML = '';
            <?php endif; ?>
                            document.getElementById("store_payment_gateway_success_message").innerHTML = '';
                        },
                        onSuccess: function (responseJSON) {
                            document.getElementById('store_gateway_submit_spinner').innerHTML = '';

                            if (responseJSON.success_message) {
                                document.getElementById("store_payment_gateway_success_message").innerHTML = '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                            }

                            // SHOW PAYPAL ERROR MESSAGE, IF ANY
                            if (responseJSON.email_error) {
                                TempStr = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';

                                if (responseJSON.paypal_info_error) {
                                    display_error = false;
                                    if (responseJSON.paypal_info_error)
                                        TempStr += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li>';
                                }
                                TempStr += '</ul>';
                            }

                            if (display_error == true)
                            {
                                if (responseJSON.paypal_info_error) {
                                    TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                                }
                            }

                            if (responseJSON.error_message) {
                                TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                            }

                            if (TempStr)
                                document.getElementById('show_paypal_form_massges').innerHTML = TempStr;

            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
                <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                    <?php $gatewayName = strtolower($paymentGateway); ?>
                                    gatewayName = '<?php echo $gatewayName; ?>';
                                    divId = 'show_<?php echo $gatewayName; ?>_form_massges';
                                    if (gatewayName == 'mangopay') {
                                        if (responseJSON.mangopay_bankDetail_error) {
                                            divId = 'show_mangopay_bank_detail_form_massges';
                                        }
                                    }
                                    if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                                        window['<?php echo $gatewayName ?>' + 'TempStr'] += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                                    }

                                    if (responseJSON.error_message_<?php echo $gatewayName ?>) {
                                        window['<?php echo $gatewayName ?>' + 'TempStr'] += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message_<?php echo $gatewayName ?> + '</li></ul></li></ul>';
                                    }

                                    if (window['<?php echo $gatewayName ?>' + 'TempStr'] && document.getElementById('show_<?php echo $gatewayName; ?>_form_massges')) {
                                        document.getElementById(divId).innerHTML = window['<?php echo $gatewayName ?>' + 'TempStr'];
                                    }

                <?php endif; ?>
            <?php endforeach; ?>

                            // SHOW BYCHEQUE ERROR MESSAGE, IF ANY
                            if (responseJSON.cheque_info_error)
                                document.getElementById("show_bycheque_error").innerHTML = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.cheque_info_error + '</li></ul></li></ul>';
                        }

                    })
                            );
                }
        <?php endif; ?>

            en4.core.runonce.add(function () {
        <?php if (!empty($this->enablePaymentGateway)) : ?>
                    checkEnableGateway();
        <?php else: ?>

            <?php $paymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.paymentmethod', 'paypal'); ?>
            <?php if ($paymentGateway != 'paypal' && Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
                <?php $gatewayName = strtolower($paymentGateway); ?>

                        if (document.getElementById('sitestoreproduct_payment_info_<?php echo $gatewayName ?>')) {
                            document.getElementById('sitestoreproduct_payment_info_<?php echo $gatewayName ?>').addEvent('submit', function (e) {
                                e.stop();
                                var TempStr = '';
                                var enabled = 0;
                                var display_error = true;
                                if (document.getElementById('enabled-1').checked) {
                                    enabled = 1;
                                }
                                document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = '';
                                document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" /></center>';
                                en4.core.request.send(new Request.JSON({
                                    url: en4.core.baseUrl + 'sitestoreproduct/product/set-payment-info-additional-gateway',
                                    method: 'POST',
                                    data: {
                                        format: 'json',
                                        gatewayName: '<?php echo $gatewayName ?>',
                                        gatewayCredentials: $("sitestoreproduct_payment_info_<?php echo $gatewayName; ?>").toQueryString(),
                                        enabled: enabled,
                                        store_id: <?php echo $this->store_id ?>
                                    },
                                    onSuccess: function (responseJSON) {

                                        document.getElementById('spiner-image').innerHTML = '';

                                        if (responseJSON.success_message) {
                                            TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                                        }

                                        if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                                            display_error = false;
                                            TempStr += '<li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                                        }
                                        else
                                        {
                                            TempStr += '</ul>'
                                        }


                                        if (display_error == true)
                                        {
                                            if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                                                TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                                            }
                                        }

                                        if (responseJSON.error_message) {
                                            TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                                        }

                                        document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = TempStr;
                                    }

                                })
                                        );
                            });
                        }
            <?php endif; ?>

                    document.getElementById('sitestoreproduct_payment_info').addEvent('submit', function (e) {
                        e.stop();
                        var TempStr = '';
                        var enabled = 0;
                        var display_error = true;
                        if (document.getElementById('enabled-1').checked) {
                            enabled = 1;
                        }
                        document.getElementById('show_paypal_form_massges').innerHTML = '';
                        document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
                        en4.core.request.send(new Request.JSON({
                            url: en4.core.baseUrl + 'sitestoreproduct/product/set-payment-info',
                            method: 'POST',
                            data: {
                                format: 'json',
                                username: document.getElementById('username').value,
                                password: document.getElementById('password').value,
                                signature: document.getElementById('signature').value,
                                email: document.getElementById('email').value,
                                enabled: enabled,
                                store_id: <?php echo $this->store_id ?>
                            },
                            onSuccess: function (responseJSON) {
                                document.getElementById('spiner-image').innerHTML = '';
                                if (responseJSON.success_message) {
                                    TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                                }

                                if (responseJSON.email_error) {
                                    TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';

                                    if (responseJSON.paypal_info_error) {
                                        display_error = false;
                                        TempStr += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                                    }
                                    else
                                    {
                                        TempStr += '</ul>'
                                    }
                                }

                                if (display_error == true)
                                {
                                    if (responseJSON.paypal_info_error) {
                                        TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                                    }
                                }

                                if (responseJSON.error_message) {
                                    TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                                }

                                document.getElementById('show_paypal_form_massges').innerHTML = TempStr;
                            }

                        })
                                );
                    });
        <?php endif; ?>
            });
            function changeMangoPayAccountType(accountType, onload) {
                $$('.optionalBankDetails').getParent().getParent().hide();
                if (!onload) {
                    Object.each($$('.optionalBankDetails'), function (obj, index) {
                        obj.value = "";
                    });
                }
                switch (accountType) {

                    case 'IBAN' :
                        $('iban-wrapper').show();
                        $('bic-wrapper').show();
                        break;
                    case 'GB' :
                        $('sort_code-wrapper').show();
                        $('account_number-wrapper').show();
                        break;
                    case 'US' :
                        $('deposit_account_type-wrapper').show();
                        $('aba-wrapper').show();
                        $('us_account_number-wrapper').show();
                        break;
                    case 'CA' :
                        $('branch_code-wrapper').show();
                        $('bank_name-wrapper').show();
                        $('institution_number-wrapper').show();
                        $('ca_account_number-wrapper').show();
                        break;
                    case 'OTHER' :
                        $('other_bic-wrapper').show();
                        $('other_account_number-wrapper').show();
                        break;
                }
            }
            function initateForm() {
                if ($('account_type')) {
                    changeMangoPayAccountType($('account_type').value, true);
                }
                else {
                    setTimeout(initateForm, 500);
                }
            }
            window.addEvent('domready', function () {
                initateForm();
            });
        </script>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {
        if (<?php echo ($checkedStripeConnect) ? 1 : 0; ?>) {
            selectStorePaymentGateway('sitestore_gateway_stripe', true);
        }
        if (<?php echo ($this->mangopayEnabled) ? 1 : 0; ?>) {
            selectStorePaymentGateway('sitestore_gateway_mangopay', true);
        }
        if (<?php echo ($this->paypaladaptiveEnabled) ? 1 : 0; ?>) {
            selectStorePaymentGateway('sitestore_gateway_paypaladaptive', true);
        }
    });
</script>   