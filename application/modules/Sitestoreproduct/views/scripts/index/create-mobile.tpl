<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<style>
    #location-wrapper,
    #special_vat-wrapper,
    #product_selling_price-wrapper,
    #discount-wrapper,
    #min_order_quantity-wrapper,
    #max_order_quantity-wrapper,
    #out_of_stock_action-wrapper,
    .hide_this,
    .hide_this + div.content {
        display:none !important;
    }
</style>
<script type="text/javascript">
    $(document).on('pageshow', function (event) {
        setTimeout(function () {
            // Mobile redirect, prevent bug when loading page via AJAX
<?php if ($this->create_bundle_completed || $this->create_simple_completed): ?>
                window.location.href = "<?php
    echo $this->url(array(
        'module' => 'sitestoreproduct',
        'controller' => 'index',
        'action' => 'edit-mobile',
        'product_id' => $this->product_id,
            ), 'default', true);
    ?>";
<?php elseif ($this->create_configurable_completed): ?>
                window.location.href = "<?php
    echo $this->url(array(
        'module' => 'sitestoreproduct',
        'controller' => 'siteform',
        'action' => 'index-mobile',
        'product_id' => $this->product_id,
        'option_id' => $this->option_id,
            ), 'default', true);
    ?>";
    <?php return; ?>
<?php endif; ?>
        }, 1000);
    });
</script>
<?php if ($this->create_bundle_completed || $this->create_simple_completed || $this->create_configurable_completed): ?>
    <?php return; ?>
<?php endif; ?>
<script type="text/javascript">
    $(document).on('pageshow', function (event) {
<?php if (!empty($this->form_post)): ?>
            setTimeout(function () {
                $("#category_id").val("0");
                if ($("#category_id").prev("span")) {
                    $("#category_id").prev("span").html("");
                }

                $("#1").click();
            }, 1000);
<?php endif; ?>
    });

    $(document).ready(function () {
        // CTSTYLE-24
        // fix scroll
        if ($(".layout_sitemobile_dashboard")) {
            $(".layout_sitemobile_dashboard").css({"height": window.getSize().y, "overflow-y": "scroll"});
        }
    });
</script>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<div class='layout_middle sitestoreproduct_create_product'>
<?php if (!empty($this->quota) && $this->current_count >= $this->quota): ?>
        <div class="tip">
            <span>
        <?php echo $this->translate("Not allowed to create products because maximum number of products already created. You have <b>%s</b> products created in this store and package allowed you <b>%s</b> product's creation.", $this->current_count, $this->quota); ?>         
            </span>
        </div>
                <?php return; ?>
        <br/>
<?php elseif (!empty($this->allowSellingProducts) && empty($this->isAnyCountryEnable) && !empty($this->sitestoreproduct_render) && ($this->sitestoreproduct_render != "downloadable") && ($this->sitestoreproduct_render != "virtual")): ?>
        <div class="tip">
            <span>
        <?php echo $this->translate("There are no location configured by site administrator for the shipment."); ?>
            </span>
        </div>
                <?php return; ?>
<?php elseif (!empty($this->allowSellingProducts) && empty($this->shipping_method_exist) && !empty($this->sitestoreproduct_render) && ($this->sitestoreproduct_render != "downloadable") && ($this->sitestoreproduct_render != "virtual")): ?>
        <div class="tip">
            <span>
        <?php echo $this->translate("No shipping methods have been configured for this store yet. Please %1sclick here%2s to configure shipping methods for your store so that you can start selling.", '<a href="' . $this->url(array('action' => 'store', 'store_id' => $this->sitestore->store_id, 'type' => 'index', 'menuId' => '51', 'method' => 'shipping-methods'), 'sitestore_store_dashboard', true) . '">', '</a>'); ?>
            </span>
        </div>
    <?php return; ?>
            <?php elseif ($this->category_count > 0): ?>
                <?php if (!empty($this->sitestoreproduct_render)) : ?>
        <?php if (!empty($this->lessSimpleProductType)) : ?>
                <div class="tip">
                    <span>
                <?php echo $this->translate("You can not create this type of product currently, because at least two products are required to create this product type."); ?>
                    </span>
                </div>
                <?php return; ?>
            <?php endif; ?>
            <?php
            if ($this->countProductTypes == 1) :
                $this->form->setTitle("Create New Product");
            else:
                $this->form->setTitle("2. Create New Product");
            endif;
            $this->form->setDescription("<p>Create your product by configuring the various properties below.</p>");
            $this->form->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
            echo $this->form->setAttrib('class', 'global_form sr_sitestoreproduct_create_list_form')->render($this);
            ?>
        <?php if ($this->languageCount > 1 && $this->multiLanguage): ?>

                <div id="multiLanguageTitleLinkShow" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Title in the multiple languages supported by this website."); ?></b></a>
                    </div>
                </div>

                <div id="multiLanguageTitleLinkHide" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageTitleOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Title in the primary language of this website."); ?></b></a>
                    </div>

                </div>
                <div id="multiLanguageBodyLinkShow" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Short Description in the multiple languages supported by this website."); ?></b></a>
                    </div>
                </div>

                <div id="multiLanguageBodyLinkHide" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageBodyOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Short Description in the primary language of this website."); ?></b></a>
                    </div>

                </div>
                <div id="multiLanguageOverviewLinkShow" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(2);" style="text-decoration: none;"><b><?php echo $this->translate("Create Overview in the multiple languages supported by this website."); ?></b></a>
                    </div>
                </div>

                <div id="multiLanguageOverviewLinkHide" class="form-wrapper">
                    <div class="form-label">&nbsp;</div>
                    <div class="form-element">
                        <a href="javascript: void(0);" onclick="return multiLanguageOverviewOption(1);" style="text-decoration: none;"><b><?php echo $this->translate("Create Overview in the primary language of this website."); ?></b></a>
                    </div>
                </div>
            <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>


<?php $this->tinyMCESEAO()->addJS(); ?>


    <!--CONDITIONS FOR TWO STEP FORM AND VARIOUS DEPENDENCIES ON PRODUCT-->
    <?php if (!empty($this->withNoSingleProduct)): ?>
        <div class="tip">
            <span>
        <?php echo $this->translate("You do not have created products in this store that's why you will not be permitted for creating %s products.", $this->productTypeName); ?>
            </span>
        </div>
                <?php return;
            endif; ?>

    <?php if (!empty($this->viewType)): ?>
        <?php if (empty($this->productType)) : ?>
            <div class="tip">
                <span>
                <?php echo $this->translate("There are no product type available for creating products."); ?>
                </span>
            </div>
                    <?php return;
                endif; ?>

    <?php if (!empty($this->page_id)) : ?>
            <form data-ajax="false" id='product_type_form' class="global_form" method="post" action="<?php echo $this->url(array('action' => 'create-mobile', 'store_id' => $this->sitestore->store_id, 'page_id' => $this->page_id), 'sitestoreproduct_general', true); ?>" >
    <?php else: ?>
                <form data-ajax="false" id='product_type_form' class="global_form" method="post" action="<?php echo $this->url(array('action' => 'create-mobile', 'store_id' => $this->sitestore->store_id, 'tab' => $this->tab_selected_id), 'sitestoreproduct_general', true); ?>" >
    <?php endif; ?>
                <div>
                    <div>
                        <h3>1. <?php echo $this->translate("Choose a Product Type"); ?></h3>
                        <p class="form-description"><?php echo $this->translate("Select a product type that best matches your product's profile. This selection will allow you to access the appropriate set of features required to sell your product on %s. (Note: You can not change the type of your product later.)", $this->site_title); ?></p>
                        <div class="form-elements">
                            <div id="product_type-wrapper" class="form-wrapper">
                                <div id="product_type-label" class="form-label">
                                    <label class="required" for="product_type"><?php echo $this->translate("Product Type") ?></label>
                                </div>
                                <div id="product_type-element" class="form-element">
                                    <select id="product_type" name="product_type" class="mright5">
                                            <?php foreach ($this->productType as $type) : ?>
                                            <option value="<?php echo $type ?>" >
                                                <?php
                                                switch ($type) {
                                                    case 'simple':
                                                        echo $this->translate('Simple Product');
                                                        break;

                                                    case 'grouped':
                                                        echo $this->translate('Grouped Product');
                                                        break;

                                                    case 'configurable':
                                                        echo $this->translate('Configurable Product');
                                                        break;

                                                    case 'virtual':
                                                        echo $this->translate('Virtual Product');
                                                        break;

                                                    case 'bundled':
                                                        echo $this->translate('Bundled Product');
                                                        break;

                                                    case 'downloadable':
                                                        echo $this->translate('Downloadable Product');
                                                        break;

                                                    default:
                                                        echo $this->translate('Simple Product');
                                                        break;
                                                }
                                                ?>
                                            </option>
    <?php endforeach; ?>
                                    </select>
                                    <a href="javascript:void(0)" onclick="window.open('<?php echo $this->url(array('action' => 'product-type-details'), 'sitestoreproduct_general', true); ?>', null, 'width=450, height=400 resizable=0')" ><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/help.gif" /></a>
                                </div>
                            </div>
                            <div id="buttons-wrapper" class="form-wrapper">
                                <div id="buttons-label" class="form-label">
                                </div>
                                <div id="buttons-element" class="form-element">
                                    <button type="submit" name="select" ><?php echo $this->translate("Create Product") ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    <?php return; ?>
<?php endif; ?>
        <!--END OF PRODUCT TYPE CONDITIONS-->
</div>
<script type="text/javascript">
    var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
    sm4.core.runonce.add(function () {
        sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {'singletextbox': true, 'limit': 10, 'minLength': 1, 'showPhoto': false, 'search': 'text'});


        var locationEl = document.getElementById('location');
        if (locationEl && (('<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>' && '<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
            google.maps.event.addListener(autocompleteSECreateLocation, 'place_changed', function () {
                var place = autocompleteSECreateLocation.getPlace();
                if (!place.geometry) {
                    return;
                }
                var address = '', country = '', state = '', zip_code = '', city = '';
                if (place.address_components) {
                    var len_add = place.address_components.length;

                    for (var i = 0; i < len_add; i++) {
                        var types_location = place.address_components[i]['types'][0];
                        if (types_location === 'country') {
                            country = place.address_components[i]['long_name'];
                        } else if (types_location === 'administrative_area_level_1') {
                            state = place.address_components[i]['long_name'];
                        } else if (types_location === 'administrative_area_level_2') {
                            city = place.address_components[i]['long_name'];
                        } else if (types_location === 'zip_code') {
                            zip_code = place.address_components[i]['long_name'];
                        } else if (types_location === 'street_address') {
                            if (address === '')
                                address = place.address_components[i]['long_name'];
                            else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'locality') {
                            if (address === '')
                                address = place.address_components[i]['long_name'];
                            else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'route') {
                            if (address === '')
                                address = place.address_components[i]['long_name'];
                            else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'sublocality') {
                            if (address === '')
                                address = place.address_components[i]['long_name'];
                            else
                                address = address + ',' + place.address_components[i]['long_name'];
                        }
                    }
                }
                var locationParams = '{"location" :"' + locationEl.value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
                document.getElementById('locationParams').value = locationParams;
            });
        }

    });



    window.addEvent('domready', function () {
<?php if (!empty($this->allowProductCode)) : ?>
            var e4 = document.getElementById('product_code_msg-wrapper');
            document.getElementById('product_code_msg-wrapper').style.display = "none";

            var pageurlcontainer = document.getElementById('product_code-element');
            var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
            var newdiv = document.createElement('div');
            newdiv.id = 'product_code_varify';
            newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";

            pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
<?php endif; ?>
//				checkDraft();

        checkDraft();

<?php $accordian = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.accordian', 0); ?>
<?php if (!empty($accordian)) : ?>
            new Fx.Accordion(document.getElementById('sitestoreproducts_create'), '#sitestoreproducts_create h4', '#sitestoreproducts_create .content');
<?php endif; ?>
    });

<?php if (!empty($this->allowProductCode)) : ?>
        function PageUrlBlur() {
            if (document.getElementById('product_code_alert') == null) {
                var pageurlcontainer = document.getElementById('product_code-element');
                var newdiv = document.createElement('span');
                newdiv.id = 'product_code_alert';
                newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
                pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[3]);
            }
            else {
                document.getElementById('product_code_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
            }
            var url = '<?php echo $this->url(array('action' => 'product-code-validation'), 'sitestoreproduct_general', true); ?>';
            en4.core.request.send(new Request.JSON({
                url: url,
                method: 'get',
                data: {
                    product_code: document.getElementById('product_code').value,
                    format: 'html'
                },
                onSuccess: function (responseJSON) {
                    if (responseJSON.success == 0) {
                        document.getElementById('product_code_alert').innerHTML = responseJSON.error_msg;
                        if (document.getElementById('product_code_alert')) {
                            document.getElementById('product_code_alert').innerHTML = responseJSON.error_msg;
                        }
                    }
                    else {
                        document.getElementById('product_code_alert').innerHTML = responseJSON.success_msg;
                        if (document.getElementById('product_code_alert')) {
                            document.getElementById('product_code_alert').innerHTML = responseJSON.success_msg;
                        }
                    }
                }
            }));
        }
<?php endif; ?>

    function checkDraft() {
        if (document.getElementById('draft')) {
            if (document.getElementById('draft').value == 1) {
                document.getElementById("search-wrapper").style.display = "none";

                if (document.getElementById("allow_purchase"))
                    document.getElementById("allow_purchase-wrapper").style.display = "none";

//				document.getElementById("search").checked= false;
            } else {
                document.getElementById("search-wrapper").style.display = "block";

                if (document.getElementById("allow_purchase"))
                    document.getElementById("allow_purchase-wrapper").style.display = "block";
//				document.getElementById("search").checked= true;
            }
        }
    }

    function expand(el) {
        // new Fx.Scroll(window).start(0, document.getElementById('sitestoreproducts_create').getCoordinates().top);
        for (var i = 1; i <= 7; i++) {
            var previous_id = 'img_' + parseInt(i);
            if (document.getElementById(previous_id))
                document.getElementById(previous_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/leftarrow.png" />';
        }
        if (document.getElementById('img_' + el.id))
            document.getElementById('img_' + el.id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/icons/arrow.png" />';
    }

    // START CALENDAR WORK FOR PRODUCT START-END DATE AND DISCOUNT START-END DATE
    en4.core.runonce.add(function ()
    {
        if ('<?php echo $this->expiry_setting; ?>' != 1) {
            document.getElementById("end_date_enable-wrapper").style.display = "none";
        }
        initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date');
        initializeCalendarDate(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date');
        cal_start_date_onHideStart();
        cal_discount_start_date_onHideStart();
    });

    var cal_start_date_onHideStart = function () {
        cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date');
    };

    var cal_discount_start_date_onHideStart = function () {
        cal_starttimeDate_onHideStart(seao_dateFormat, cal_discount_start_date, cal_discount_end_date, 'discount_start_date', 'discount_end_date');
    };
    // END CALENDAR WORK FOR PRODUCT START-END DATE AND DISCOUNT START-END DATE

    if (document.getElementById('product_ids-wrapper')) {
        document.getElementById('product_ids-wrapper').style.display = "none";
    }
</script>
        <?php // endif;  ?>
        <?php
        /* Include the common user-end field switching javascript */
        echo $this->partial('_jsSwitch.tpl', 'fields', array())
        ?>

<?php if ($this->category_count <= 0): ?>
    <div class="tip"> 
        <span>
    <?php echo $this->translate("Oops! Sorry it looks like something went wrong and you can not post a new product right now. Please try again after sometime."); ?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    if (document.getElementById('subcategory_id'))
        document.getElementById('subcategory_id').style.display = 'none';
</script>

<script type="text/javascript">

    var getProfileType = function (category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
<?php if (!empty($this->form_post)): ?>
        /* correct UI after AJAX navigation*/
        $(document).on('pageshow', function (event) {
            setTimeout(function () {
                if ($type(document.getElementById(defaultProfileId)) && typeof document.getElementById(defaultProfileId) != 'undefined') {
                    document.getElementById(defaultProfileId).style.display = "none";
                }

                showStock();
            }, 1000);
        });
<?php else: ?>
        if ($type(document.getElementById(defaultProfileId)) && typeof document.getElementById(defaultProfileId) != 'undefined') {
            document.getElementById(defaultProfileId).style.display = "none";
        }
<?php endif; ?>

    if (document.getElementById('overview-wrapper') && false) {
<?php
echo $this->tinyMCESEAO()->render(array('element_id' => '"overview"',
    'language' => $this->language,
    'directionality' => $this->directionality,
    'upload_url' => $this->upload_url));
?>
    }
<?php
foreach ($this->languageData as $language_code):
    if ($this->defaultLanguage == $language_code) {
        continue;
    }
    if ($language_code == 'en') {
        $language_code = '';
    } else {
        $language_code = "_$language_code";
    }
    ?>

        if (document.getElementById('overview' + '<?php echo $language_code; ?>' + '-wrapper')) {

    <?php
    echo $this->tinyMCESEAO()->render(array('element_id' => '"overview' . $language_code . '"',
        'language' => $this->language,
        'directionality' => $this->directionality,
        'upload_url' => $this->upload_url));
    ?>

        }
<?php endforeach; ?>
    window.addEvent('domready', function () {

        var productType = document.getElementById('product_type').value;
        if (productType == 'bundled') {
            showWeightType();
        }

        if (productType != 'grouped') {
            showDiscount();
<?php if (!empty($this->showProductInventory)) : ?>
                showOutOfStock();
                showStock();
<?php endif; ?>
<?php if (!empty($this->directPayment) && !empty($this->isDownPaymentEnable)) : ?>
                showDownpayment();
<?php endif; ?>
        }
        showEndDate();

<?php if (!empty($this->form_post)): ?>
            for (var i = 1; i <= 7; i++) {
                var previous_id = 'img_' + parseInt(i);
                if (document.getElementById(previous_id))
                    document.getElementById(previous_id).innerHTML = '';
                i = i.toString();
                if (document.getElementById(i))
                    document.getElementById(i).removeAttribute("onclick");
            }
<?php endif; ?>
    });


    function showDownpayment() {
        var downpayment_radios = document.getElementsByName("downpayment");
        var downpayment_radioValue;
        if (downpayment_radios[0].checked) {
            downpayment_radioValue = downpayment_radios[0].value;
        } else {
            downpayment_radioValue = downpayment_radios[1].value;
        }
        if (downpayment_radioValue == 1) {
            document.getElementById('downpaymentvalue-wrapper').style.display = 'block';
        } else {
            document.getElementById('downpaymentvalue-wrapper').style.display = 'none';
        }
    }

    function showDiscount() {
        var radios = document.getElementsByName("discount");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 0) {
            document.getElementById('handling_type-wrapper').style.display = 'none';
            document.getElementById('discount_rate-wrapper').style.display = 'none';
            document.getElementById('discount_price-wrapper').style.display = 'none';
            document.getElementById('discount_start_date-wrapper').style.display = 'none';
            document.getElementById('discount_end_date-wrapper').style.display = 'none';
            document.getElementById('discount_permanant-wrapper').style.display = 'none';
            document.getElementById('user_type-wrapper').style.display = 'none';
        } else {
            document.getElementById('handling_type-wrapper').style.display = 'block';
            document.getElementById('discount_start_date-wrapper').style.display = 'block';
            document.getElementById('discount_end_date-wrapper').style.display = 'block';
            document.getElementById('discount_permanant-wrapper').style.display = 'block';
            document.getElementById('user_type-wrapper').style.display = 'block';
            showDiscountType();
            showDiscountEndDate();
        }
    }


    function showOutOfStock() {
        var radios = document.getElementsByName("out_of_stock");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 0) {
            document.getElementById('out_of_stock_action-wrapper').style.display = "none";
        } else {
            document.getElementById('out_of_stock_action-wrapper').style.display = "block";

        }
    }

    function showWeightType() {
        var radios = document.getElementsByName("weight_type");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 1) {
            document.getElementById('weight-wrapper').style.display = "none";
        } else {
            document.getElementById('weight-wrapper').style.display = "block";
        }
    }

    function showStock() {
        var radios = document.getElementsByName("stock_unlimited");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 1) {
            document.getElementById('in_stock-wrapper').style.display = "none";
            document.getElementById('out_of_stock-wrapper').style.display = "none";
            document.getElementById('out_of_stock_action-wrapper').style.display = "none";
        } else {
            document.getElementById('in_stock-wrapper').style.display = "block";
            document.getElementById('out_of_stock-wrapper').style.display = "block";
            showOutOfStock();
        }
    }

    function showDiscountEndDate() {
        var radios = document.getElementsByName("discount_permanant");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 1) {
            document.getElementById('discount_end_date-wrapper').style.display = "none";
        } else {
            document.getElementById('discount_end_date-wrapper').style.display = "block";

        }
    }

    function showEndDate() {
        var radios = document.getElementsByName("end_date_enable");
        var radioValue;
        if (radios[0].checked) {
            radioValue = radios[0].value;
        } else {
            radioValue = radios[1].value;
        }
        if (radioValue == 0) {
            document.getElementById('end_date-wrapper').style.display = "none";
        } else {
            document.getElementById('end_date-wrapper').style.display = "block";

        }
    }

    function showDiscountType() {
        if (document.getElementById('handling_type')) {
            if (document.getElementById('handling_type').value == 1) {
                document.getElementById('discount_price-wrapper').style.display = 'none';
                document.getElementById('discount_rate-wrapper').style.display = 'block';
            } else {
                document.getElementById('discount_price-wrapper').style.display = 'block';
                document.getElementById('discount_rate-wrapper').style.display = 'none';
            }
        }
    }

    var maxRecipients = 10000;
    var packageRequest;
    var storeidsAutocomplete;
    var productidsAutocomplete;

    var is_simple;
    var is_configurable;
    var is_virtual;
    var is_downloadable;

    sm4.core.runonce.add(function () {
        var productType = document.getElementById('product_type').value;
        if ((productType == 'bundled') || productType == 'grouped') {
            sm4.core.Module.autoCompleter.attach("product_name", '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts', 'store_id' => $this->sitestore->store_id), 'default', true); ?>',
                    {
                        'singletextbox': false,
                        'limit': 10,
                        'minLength': 1,
                        'showPhoto': false,
                        'search': 'search',
                        'noResults': "<?php echo $this->translate("No matching contact found."); ?>",
                        'postData': {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': document.getElementById('product_ids').value, 'create': 1, 'is_simple': is_simple, 'is_configurable': is_configurable, 'is_virtual': is_virtual, 'is_downloadable': is_downloadable},
                    },
                    'product_ids');
        }
        if (productType == 'bundled')
            bundleProductTypes();
    });

    function bundleProductTypes()
    {
        if (document.getElementById("bundle_product_type-simple"))
            is_simple = document.getElementById("bundle_product_type-simple").checked;
        if (document.getElementById("bundle_product_type-configurable"))
            is_configurable = document.getElementById("bundle_product_type-configurable").checked;
        if (document.getElementById("bundle_product_type-virtual"))
            is_virtual = document.getElementById("bundle_product_type-virtual").checked;
        if (document.getElementById("bundle_product_type-downloadable"))
            is_downloadable = document.getElementById("bundle_product_type-downloadable").checked;

        if (!is_simple && !is_configurable && !is_virtual && !is_downloadable)
            document.getElementById("product_name").disabled = true;
        else
            document.getElementById("product_name").disabled = false;

        $("#product_ids").val("");
    }
</script>


<?php
// SHOW DEFAULT ADDED PRODUCTS IN THE EDIT FORM.
if (!empty($this->productArray) && !empty($this->tempMappedIdsStr)):
    $productSpan = '<input type="hidden" id="product_ids" value="' . $this->tempMappedIdsStr . '" name="product_ids">';
    foreach ($this->productArray as $product) {
        $product['title'] = str_replace("'", "\'", $product['title']);
        $product['title'] = str_replace('"', '\"', $product['title']);
        $productSpan .= '<span id="tospan_' . $product['title'] . '_' . $product['id'] . '" class="tag">' . $product['title'] . '<a onclick="this.parentNode.destroy();removeFromToValue(&quot;2&quot;, &quot;product_ids&quot; , &quot;product_name&quot;, &quot;product_ids&quot;);" href="javascript:void(0);">x</a></span>';
    }
    ?>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            document.getElementById("product_ids-element").innerHTML = '<?php echo $productSpan; ?>';
            document.getElementById("product_ids-wrapper").style.display = 'block';
        });
    </script>
<?php endif; ?>

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_brands', 1)): ?>
    <script type="text/javascript">
        document.getElementById('tags').addEvent('keypress', function (event) {
            if (event.key == ',') {
                alert('<?php echo $this->string()->escapeJavascript($this->translate("Only one brand can be associate with one product. You are not allowed to use comma.")) ?>');
                return false;
            }
        });

        document.getElementById('tags').addEvent('paste', function (event) {
            console.log(event);

            (function () {
                if (document.getElementById('tags').value.indexOf(',') != -1) {
                    var tagValues = document.getElementById('tags').value.split(',');
                    document.getElementById('tags').value = tagValues[0];
                    alert('<?php echo $this->string()->escapeJavascript($this->translate("Only one brand can be associate with one product. You are not allowed to use comma.")) ?>');
                }
            }).delay(100);

        });

    </script>
<?php endif; ?>
<script type="text/javascript">

    en4.core.runonce.add(function () {
        var multiLanguage = '<?php echo $this->multiLanguage; ?>';
        var languageCount = '<?php echo $this->languageCount; ?>';
        var titleParent = document.getElementById('<?php echo $this->add_show_hide_title_link; ?>').getParent().getParent();
        var bodyParent = document.getElementById('<?php echo $this->add_show_hide_body_link; ?>').getParent().getParent();
        var overviewParent = document.getElementById('<?php echo $this->add_show_hide_overview_link; ?>').getParent().getParent();
        if (multiLanguage == 1 && languageCount > 1) {
            document.getElementById('multiLanguageTitleLinkShow').inject(titleParent, 'after');
            document.getElementById('multiLanguageTitleLinkHide').inject(titleParent, 'after');
            document.getElementById('multiLanguageBodyLinkShow').inject(bodyParent, 'after');
            document.getElementById('multiLanguageBodyLinkHide').inject(bodyParent, 'after');
            document.getElementById('multiLanguageOverviewLinkShow').inject(overviewParent, 'after');
            document.getElementById('multiLanguageOverviewLinkHide').inject(overviewParent, 'after');
            multiLanguageTitleOption(1);
            multiLanguageBodyOption(1);
            multiLanguageOverviewOption(1);
        }

    });

    var multiLanguageTitleOption = function (show) {

<?php
foreach ($this->languageData as $language_code):
    if ($this->defaultLanguage == $language_code) {
        continue;
    }
    if ($language_code == 'en') {
        $language_code = '';
    } else {
        $language_code = "_$language_code";
        ?>
                if (show == 1) {
                    document.getElementById('title' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "none";
                    document.getElementById('multiLanguageTitleLinkShow').style.display = "block";
                    document.getElementById('multiLanguageTitleLinkHide').style.display = "none";
                }
                else {
                    document.getElementById('title' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "block";
                    document.getElementById('multiLanguageTitleLinkShow').style.display = "none";
                    document.getElementById('multiLanguageTitleLinkHide').style.display = "block";
                }
    <?php } endforeach; ?>
    }

    var multiLanguageBodyOption = function (show) {

<?php
foreach ($this->languageData as $language_code):
    if ($this->defaultLanguage == $language_code) {
        continue;
    }
    if ($language_code == 'en') {
        $language_code = '';
    } else {
        $language_code = "_$language_code";
        ?>
                if (show == 1) {
                    document.getElementById('body' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "none";
                    document.getElementById('multiLanguageBodyLinkShow').style.display = "block";
                    document.getElementById('multiLanguageBodyLinkHide').style.display = "none";
                }
                else {
                    document.getElementById('body' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "block";
                    document.getElementById('multiLanguageBodyLinkShow').style.display = "none";
                    document.getElementById('multiLanguageBodyLinkHide').style.display = "block";
                }
    <?php } endforeach; ?>
    }



    var multiLanguageOverviewOption = function (show) {

<?php
foreach ($this->languageData as $language_code):
    if ($this->defaultLanguage == $language_code) {
        continue;
    }
    if ($language_code == 'en') {
        $language_code = '';
    } else {
        $language_code = "_$language_code";
        ?>
                if (show == 1) {
                    document.getElementById('overview' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "none";
                    document.getElementById('multiLanguageOverviewLinkShow').style.display = "block";
                    document.getElementById('multiLanguageOverviewLinkHide').style.display = "none";
                }
                else {
                    document.getElementById('overview' + '<?php echo $language_code; ?>' + '-wrapper').style.display = "block";
                    document.getElementById('multiLanguageOverviewLinkShow').style.display = "none";
                    document.getElementById('multiLanguageOverviewLinkHide').style.display = "block";
                }
    <?php } endforeach; ?>
    }
<?php if (empty($this->isCommentsAllow)) : ?>
        document.getElementById('auth_comment-wrapper').style.display = "none";
<?php endif; ?>
    //FUNCTION FOR SHOWING THE SELLING PRICE
    function showSellingPrice() {
        if (document.getElementById('product_selling_price-wrapper')) {

            var url = '<?php echo $this->url(array('action' => 'get-product-selling-price'), 'sitestoreproduct_product_general', true); ?>';
            var product_price = document.getElementById('price').value;
            var special_vat = document.getElementById('special_vat').value;
            var handling_type = document.getElementById('handling_type').value;
            var discount_value = 0;
            var isDiscount = false;

            if (document.getElementById('discount-wrapper') && document.getElementById('discount-1').checked) {
                isDiscount = true;
                if (document.getElementById('handling_type').value == 0) {
                    discount_value = document.getElementById('discount_price').value;
                } else {
                    discount_value = document.getElementById('discount_rate').value;
                }
            } else {
                discount_value = 0;
            }

            en4.core.request.send(new Request.JSON({
                url: url,
                data: {
                    format: 'json',
                    store_id: <?php echo $this->sitestore->store_id; ?>,
                    price: product_price,
                    special_vat: special_vat,
                    discount_value: discount_value,
                    discount_type: handling_type,
                    is_discount: isDiscount
                },
                onRequest: function () {
                    document.getElementById('sellingPriceLoading').style.display = 'block';
                },
                onSuccess: function (responseJSON) {
                    document.getElementById('sellingPriceLoading').style.display = 'none';
                    if (document.getElementById('product_selling_price')) {
                        document.getElementById('product_selling_price').value = responseJSON.value;
                    }
                }
            }));
        }
    }

    window.addEvent('load', function () {
        var locationEl = document.getElementById('location');
        var locationId = '<?php echo $this->locationId; ?>';
        var latitudeValue = '<?php echo $this->locationDetails->latitude; ?>';
        var longitudeValue = '<?php echo $this->locationDetails->longitude; ?>';
        var formattedAddressValue = '<?php echo $this->locationDetails->formatted_address; ?>';
        var addressValue = '<?php echo $this->locationDetails->address; ?>';
        var countryValue = '<?php echo $this->locationDetails->country; ?>';
        var stateValue = '<?php echo $this->locationDetails->state; ?>';
        var zipcodeValue = '<?php echo $this->locationDetails->zipcode; ?>';
        var cityValue = '<?php echo $this->locationDetails->city; ?>';
        if (locationEl && locationEl.value && locationId) {
            var locationParams = '{"location" :"' + locationEl.value + '","latitude" :"' + latitudeValue + '","longitude":"' + longitudeValue + '","formatted_address":"' + formattedAddressValue + '","address":"' + addressValue + '","country":"' + countryValue + '","state":"' + stateValue + '","zip_code":"' + zipcodeValue + '","city":"' + cityValue + '"}';
            document.getElementById('locationParams').value = locationParams;
        }
    });
</script>