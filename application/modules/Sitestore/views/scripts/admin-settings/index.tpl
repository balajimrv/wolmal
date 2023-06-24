<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isStoreActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.navi.auth', null);
$coreSettings = Engine_Api::_()->getApi('settings', 'core');

if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Stores / Marketplace Plugin.</span></div>', @ucfirst($modName));
    }
endif;
?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
    if (document.getElementById('sitestore_map_city')) {
        window.addEvent('domready', function () {
            new google.maps.places.Autocomplete(document.getElementById('sitestore_map_city'));
        });
    }
</script>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php
if (!empty($isStoreActivate)):
    include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl';
endif;
?>
<?php
$moduleName = 'sitevideointegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('sitevideointegration')): ?>
        <div id="dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Videos System with <a href="https://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin">"Stores / Marketplace - Ecommerce Plugin"</a>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-videos-product-kit">"Advanced Videos - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
        <?php if (Engine_Api::_()->hasModuleBootstrap('sitevideo') && !Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore'))): ?>
            <div id="dismiss_modules">
                <div class="seaocore-notice">
                    <div class="seaocore-notice-icon">
                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                    </div>
                    <div style="float:right;">
                        <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                    </div>
                    <div class="seaocore-notice-text ">
                        <?php echo 'You have installed <a href="https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension" target="_blank">Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension</a> installed on your website. If you want to display videos using the Advanced Videos Plugin on your website so that all videos can be place all together then please <a  target="_blank" href="admin/sitevideointegration/modules">click here</a> to integrate it.'; ?>
                    </div>	
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php
$moduleName = 'documentintegration';
if (!isset($_COOKIE[$moduleName . '_dismiss'])):
    ?>
    <?php if (!Engine_Api::_()->hasModuleBootstrap('documentintegration')): ?>
        <div id="dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'To set up a robust Documents System with <a href="https://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin">"Stores / Marketplace - Ecommerce Plugin"</a>, you can purchase our awesome <a  target="_blank" href="https://www.socialengineaddons.com/socialengine-documents-product-kit">"Documents Sharing - Product Kit"</a>.'; ?>
                </div>	
            </div>
        </div>
    <?php else: ?>
        <?php if (Engine_Api::_()->hasModuleBootstrap('document') && !Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore'))): ?>
            <div id="dismiss_modules">
                <div class="seaocore-notice">
                    <div class="seaocore-notice-icon">
                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                    </div>
                    <div style="float:right;">
                        <button onclick="dismissintegration('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                    </div>
                    <div class="seaocore-notice-text ">
                        <?php echo 'You have installed <a href="https://www.socialengineaddons.com/documentextensions/socialengine-documents-sharing-pages-businesses-groups-listings-events-stores-extension" target="_blank">Documents Sharing - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension</a> installed on your website. If you want to display documents using the Documents Plugin on your website so that all documents can be place all together then please <a  target="_blank" href="admin/documentsintegration/modules">click here</a> to integrate it.'; ?>
                    </div>	
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
    function dismissintegration(modName) {
        var d = new Date();
        // Expire after 1 Year.
        d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
        $('dismissintegration_modules').style.display = 'none';
    }

</script>
<?php if (count($this->navigationStoreGlobal)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigationStoreGlobal)->render()
        ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    function dismiss1(modName) {
        document.cookie = modName + "_dismiss_store" + "=" + 1;
        $('dismiss_modules_store').style.display = 'none';
    }
</script>

<?php if (!empty($isStoreActivate) && !$coreSettings->getSetting('seaocore.google.map.key')): ?>
    <?php
    $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
    echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP);
    ?>
<?php endif; ?>

<?php if (!$this->hasLanguageDirectoryPermissions): ?>
    <div class="seaocore_tip">
        <span>
    <?php echo 'Please access your website\'s web directory via FTP and set recursive 777 permission (chmod -R 777) to the "application/languages/" directory for changing the text of phrases: "store" and "stores".'; ?>
        </span>
    </div>
<?php endif; ?>

<div class='clear sitestore_settings_form'>
    <div class='settings'>
<?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">
    var display_msg = 0;
    paypalAdaptivePayment = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.paypaladaptivepaymentmethod', 'split'); ?>';
    window.addEvent('domready', function () {
        showlocationKM('<?php echo $coreSettings->getSetting('sitestore.proximitysearch', 1); ?>');
        showclaim('<?php echo $coreSettings->getSetting('sitestore.claimlink', 1); ?>');
        showcategoryblock('<?php echo $coreSettings->getSetting('sitestore.category.edit', 0); ?>');
        showlocationOption('<?php echo $coreSettings->getSetting('sitestore.locationfield', 1); ?>');
        showpackageOption('<?php echo $coreSettings->getSetting('sitestore.package.enable', 1); ?>');
        showDefaultNetwork('<?php echo $coreSettings->getSetting('sitestore.network', 0) ?>');
        showMapOptions('<?php echo $coreSettings->getSetting('sitestore.location', 1) ?>');
        showCheckoutFixedText('<?php echo $coreSettings->getSetting('sitestore.fixed.text', 0) ?>');
        showPaymentForOrders();
//    showPaymentForOrdersGateway();
        display_msg = 1;
        if(paypalAdaptivePayment=='split' && $('sitestore_allowed_payment_escrow_gateway-paypaladaptive')){
            escrowEle = $("sitestore_allowed_payment_escrow_gateway-paypaladaptive");
            escrowEle.disabled = true;
            escrowEle.checked = false;
            escrowEle.getNext().innerHTML = "PayPalAdaptive <span> (To enable this payment gateway, change the Payment Method in Global Settings of <a href='admin/sitegateway/settings'>‘Advanced Payment Gateways / Stripe Connect /  PayPal Adaptive Plugin’</a>.)</span> ";
        }else if(paypalAdaptivePayment=='escrow' && $('sitestore_allowed_payment_split_gateway-paypaladaptive')){
            splitEle = $('sitestore_allowed_payment_split_gateway-paypaladaptive');
            splitEle.disabled = true;
            splitEle.checked = false;
            splitEle.getNext().innerHTML = "PayPalAdaptive <span> (To enable this payment gateway, change the Payment Method in Global Settings of <a href='admin/sitegateway/settings'>‘Advanced Payment Gateways / Stripe Connect /  PayPal Adaptive Plugin’</a>.)</span> ";
        }
    });
    function showCheckoutFixedText(showText) {
        if ($("sitestore_checkout_fixed_text_value-wrapper")) {
            if (showText == 1)
                $("sitestore_checkout_fixed_text_value-wrapper").style.display = 'block';
            else
                $("sitestore_checkout_fixed_text_value-wrapper").style.display = 'none';
        }
    }

    // START FUNCTIONS TO MANAGE PAYMENT FLOW FOR ORDERS AND PAYMNET GATEWAYS
    function showPaymentForOrders() {
        if ($("sitestore_payment_for_orders-wrapper")) {
            if ($("is_sitestore_admin_driven-0").checked) {
                console.log($("sitestore_payment_method-wrapper"));
                if ($("sitestore_payment_method-wrapper")) {
                    $("sitestore_payment_method-wrapper").show();
                    showPaymentMethod();
                } else {
                    $("sitestore_payment_for_orders-wrapper").style.display = 'block';
                    showPaymentForOrdersGateway();
                }
            }
            else {
                if ($("sitestore_payment_method-wrapper")) {
                    $("sitestore_payment_method-wrapper").hide();
                    $('sitestore_allowed_payment_gateway-wrapper').hide();
                    $('sitestore_allowed_payment_escrow_gateway-wrapper').hide();
                    $('sitestore_allowed_payment_split_gateway-wrapper').hide();
                  //  $('sitestore_payout_method-wrapper').hide();
                } else {
                    if ($("sitestore_allowed_payment_gateway-wrapper")) {
                        if ($("sitestore_payment_for_orders-0").checked && $("is_sitestore_admin_driven-0").checked)
                            $("sitestore_allowed_payment_gateway-wrapper").style.display = 'block';
                        else
                            $("sitestore_allowed_payment_gateway-wrapper").style.display = 'none';
                    }
                }
                $("sitestore_payment_for_orders-wrapper").style.display = 'none';
                if ($("sitestore_admin_gateway-wrapper")) {
                    if (($("sitestore_payment_for_orders-1").checked && $("is_sitestore_admin_driven-0").checked) || $("is_sitestore_admin_driven-1").checked)
                        $("sitestore_admin_gateway-wrapper").style.display = 'block';
                    else
                        $("sitestore_admin_gateway-wrapper").style.display = 'none';
                }
                showAdminChequeInformation();
                billPaymentSettings();
            }


        }
    }

    function showPaymentForOrdersGateway() {
        if ($("sitestore_allowed_payment_gateway-wrapper")) {
            if ($("sitestore_payment_for_orders-0").checked && $("is_sitestore_admin_driven-0").checked)
                $("sitestore_allowed_payment_gateway-wrapper").style.display = 'block';
            else
                $("sitestore_allowed_payment_gateway-wrapper").style.display = 'none';
        }

        if ($("sitestore_admin_gateway-wrapper")) {
            if (($("sitestore_payment_for_orders-1").checked && $("is_sitestore_admin_driven-0").checked) || $("is_sitestore_admin_driven-1").checked)
                $("sitestore_admin_gateway-wrapper").style.display = 'block';
            else
                $("sitestore_admin_gateway-wrapper").style.display = 'none';
        }
        showAdminChequeInformation();
        billPaymentSettings();
    }

    function billPaymentSettings() {

        if ($("sitestore_payment_for_orders-0").checked && $("sitestoreproduct_paymentmethod-wrapper")) {
            $("sitestoreproduct_paymentmethod-label").innerHTML = "Payment for 'Commissions Bill'";
            $("sitestoreproduct_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to sellers for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Sellers’ is selected.";
            if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0); ?> && $("sitestoreproduct_paymentmethod-stripe")) {
                $("sitestoreproduct_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by sellers to pay admin 'Commissions Bill' which are collected through other than Stripe Connect payment gateway.]";
            }
        }
        else if ($("sitestoreproduct_paymentmethod-wrapper")) {
            $("sitestoreproduct_paymentmethod-label").innerHTML = "Payment for Sellers 'Payment Requests'";
            $("sitestoreproduct_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to site admin for making payments against the 'Payment Requests' made by sellers, if ‘Payment to Website / Site Admin’ is selected.";
            if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0); ?> && $("sitestoreproduct_paymentmethod-stripe")) {
                $("sitestoreproduct_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by admin to pay seller's payments which are collected through other than Stripe Connect payment gateway.]";
            }
        }
    }

    function showAdminChequeInformation() {
        if ($("is_sitestore_admin_driven-1").checked || ($("is_sitestore_admin_driven-0").checked && $("sitestore_payment_for_orders-1").checked))
            $("send_cheque_to-wrapper").style.display = 'block';
        else
            $("send_cheque_to-wrapper").style.display = 'none';
    }
    // END FUNCTIONS TO MANAGE PAYMENT FLOW FOR ORDERS AND PAYMNET GATEWAYS

    //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
    function showpackageOption(option) {
        if ($('sitestore_package_enable-wrapper')) {
            if (option == 1) {
                if ($('sitestore_payment_benefit-wrapper'))
                    $('sitestore_payment_benefit-wrapper').style.display = 'block';
                if ($('sitestore_package_view-wrapper'))
                    $('sitestore_package_view-wrapper').style.display = 'block';
                if ($('sitestore_package_information-wrapper'))
                    $('sitestore_package_information-wrapper').style.display = 'block';
            }
            else {
                if ($('sitestore_payment_benefit-wrapper'))
                    $('sitestore_payment_benefit-wrapper').style.display = 'none';
                if ($('sitestore_package_view-wrapper'))
                    $('sitestore_package_view-wrapper').style.display = 'none';
                if ($('sitestore_package_information-wrapper'))
                    $('sitestore_package_information-wrapper').style.display = 'none';
            }
        }
    }

    function showlocationOption(option) {


        if (option == 1) {
            if ($('sitestore_location-wrapper'))
                $('sitestore_location-wrapper').style.display = 'block';
            if ($('sitestore_proximitysearch-wrapper'))
                $('sitestore_proximitysearch-wrapper').style.display = 'block';
            if ($('sitestore_proximitysearch-1'))
                if ($('sitestore_proximitysearch-1').checked)
                    showlocationKM(1);
                else
                    showlocationKM(0);
            if ($('sitestore_location-1'))
                if ($('sitestore_location-1').checked)
                    showMapOptions(1);
                else
                    showMapOptions(0);
            if ($('sitestore_multiple_location-wrapper'))
                $('sitestore_multiple_location-wrapper').style.display = 'block';
        }
        else {
            if ($('sitestore_location-wrapper'))
                $('sitestore_location-wrapper').style.display = 'none';
            if ($('sitestore_proximitysearch-wrapper'))
                $('sitestore_proximitysearch-wrapper').style.display = 'none';
            if ($('sitestore_proximity_search_kilometer-wrapper'))
                $('sitestore_proximity_search_kilometer-wrapper').style.display = 'none';
            if ($('sitestore_multiple_location-wrapper'))
                $('sitestore_multiple_location-wrapper').style.display = 'none';
            showMapOptions(0);
        }

    }
    //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
    function showlocationKM(option) {
        if ($('sitestore_proximity_search_kilometer-wrapper')) {
            if (option == 1) {
                if ($('sitestore_proximity_search_kilometer-wrapper'))
                    $('sitestore_proximity_search_kilometer-wrapper').style.display = 'block';
            }
            else {
                if ($('sitestore_proximity_search_kilometer-wrapper'))
                    $('sitestore_proximity_search_kilometer-wrapper').style.display = 'none';
            }
        }
    }

    //  HERE WE CREATE A FUNCTION FOR SHOWING BOUNCING
    function showMapOptions(option) {
        if ($('sitestore_location-wrapper')) {
            if (option == 1) {
                if ($('sitestore_map_sponsored-wrapper'))
                    $('sitestore_map_sponsored-wrapper').style.display = 'block';
                if ($('sitestore_map_zoom-wrapper'))
                    $('sitestore_map_zoom-wrapper').style.display = 'block';
                if ($('sitestore_map_city-wrapper'))
                    $('sitestore_map_city-wrapper').style.display = 'block';
            }
            else {
                if ($('sitestore_map_sponsored-wrapper'))
                    $('sitestore_map_sponsored-wrapper').style.display = 'none';
                if ($('sitestore_map_zoom-wrapper'))
                    $('sitestore_map_zoom-wrapper').style.display = 'none';
                if ($('sitestore_map_city-wrapper'))
                    $('sitestore_map_city-wrapper').style.display = 'none';
            }
        }
    }

    function showclaim(option)
    {
        if ($('sitestore_claim_show_menu-wrapper')) {
            if (option == 1) {
                $('sitestore_claim_show_menu-wrapper').style.display = 'block';
            }
            else {
                $('sitestore_claim_show_menu-wrapper').style.display = 'none';
            }
        }
        if ($('sitestore_claim_email-wrapper')) {
            if (option == 1) {
                $('sitestore_claim_email-wrapper').style.display = 'block';
            }
            else {
                $('sitestore_claim_email-wrapper').style.display = 'none';
            }
        }
    }

    function showcategoryblock(option)
    {
        if (option == 1 && display_msg == 1) {
            alert("<?php echo $this->string()->escapeJavascript($this->translate('After giving this permission members can edit categories but it can cause content loss like reviews rating data, profile type details etc.')) ?>");
        }
    }
    function showDefaultNetwork(option) {
        if ($('sitestore_default_show-wrapper')) {
            if (option == 0) {
                $('sitestore_default_show-wrapper').style.display = 'block';
                showDefaultNetworkType($('sitestore_default_show-1').checked);
            } else {
                showDefaultNetworkType(1);
                $('sitestore_default_show-wrapper').style.display = 'none';
            }
        }
    }

    function showDefaultNetworkType(option) {
        if ($('sitestore_networks_type-wrapper')) {
            if (option == 1) {
                $('sitestore_networks_type-wrapper').style.display = 'block';
            } else {
                $('sitestore_networks_type-wrapper').style.display = 'none';
            }
        }
    }

    function showDescription(option) {
        if ($('sitestore_requried_description-wrapper')) {
            if (option == 1) {
                $('sitestore_requried_description-wrapper').style.display = 'block';
            } else {
                $('sitestore_requried_description-wrapper').style.display = 'none';
            }
        }
    }
    function selectPaymentMethod(value) {
        if (value == 'escrow') {
            $('sitestore_allowed_payment_split_gateway-wrapper').hide();
            $('sitestore_allowed_payment_gateway-wrapper').hide();
            $('sitestore_allowed_payment_escrow_gateway-wrapper').show();
            $('sitestore_payment_for_orders-wrapper').hide();
            $('sitestoreproduct_paymentmethod-wrapper').hide();
            $('sitestore_admin_gateway-wrapper').hide();
            $('send_cheque_to-wrapper').hide();
         //   $('sitestore_payout_method-wrapper').show();
        } else if (value == 'split') {
            $('sitestore_allowed_payment_escrow_gateway-wrapper').hide();
            $('sitestore_allowed_payment_gateway-wrapper').hide();
            $('sitestore_allowed_payment_split_gateway-wrapper').show();
            $('sitestore_payment_for_orders-wrapper').hide();
            $('sitestoreproduct_paymentmethod-wrapper').hide();
            $('sitestore_admin_gateway-wrapper').hide();
            $('send_cheque_to-wrapper').hide();
          //  $('sitestore_payout_method-wrapper').hide();

        } else {
            $('sitestore_allowed_payment_escrow_gateway-wrapper').hide();
            $('sitestore_allowed_payment_split_gateway-wrapper').hide();
            $('sitestore_payment_for_orders-wrapper').show();
            $('sitestoreproduct_paymentmethod-wrapper').show();
           // $('sitestore_payout_method-wrapper').hide();
            showPaymentForOrdersGateway();
            

        }
    }
    function showPaymentMethod() {
        if ($('sitestore_payment_method-split')) {
            if ($('sitestore_payment_method-split').checked) {
                selectPaymentMethod($('sitestore_payment_method-split').value);
            } else if ($('sitestore_payment_method-escrow').checked) {
                selectPaymentMethod($('sitestore_payment_method-escrow').value);
            }
            else {
                selectPaymentMethod($('sitestore_payment_method-normal').value);
            }
        }
    }
    showPaymentMethod();
</script>
