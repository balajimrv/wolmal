<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _billing_address.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if (!empty($this->sitestoreproduct_virtual_product) || !empty($this->sitestoreproduct_other_product_type)) :
  $showShippingAddredss = 1;
else:
  $showShippingAddredss = 0;
endif;
?>
<div id="show_form_errors"></div>
<script type="text/javascript">
  var tempBillAddressErrorMsg = 0;
  var tempShipAddressErrorMsg = 0;
  sm4.core.runonce.add(function() {
    <?php if (!empty($showShippingAddredss)) : ?>
     <?php if (empty($this->flag_same_address)): ?>
        $.mobile.activePage.find('#common').attr('checked', false);
        $.mobile.activePage.find('#common').attr("value", 2);
       <?php else: ?>
        $.mobile.activePage.find('#common').attr('checked', true);
        $.mobile.activePage.find('#common').attr("value", 1);
       <?php endif; ?>
 <?php endif; ?>
    if ($.mobile.activePage.find('#common').length > 0)
    {
      var shipping_address_form_Div = $("<div id='sitestoreproduct_shipping_address_form' />").insertAfter($.mobile.activePage.find('#common-wrapper')[0]);

      $.mobile.activePage.find('#dummy_shipping_address_title-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#f_name_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#l_name_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#phone_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#country_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#state_shipping-wrapper').css("display", 'none');
      $.mobile.activePage.find('#shippingregion_backgroundimage').css("display", 'inline-block');// style.display = 'inline-block';
      $.mobile.activePage.find('#state_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#city_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#locality_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#zip_shipping-wrapper').inject(shipping_address_form_Div);
      $.mobile.activePage.find('#address_shipping-wrapper').inject(shipping_address_form_Div);

      if ($.mobile.activePage.find('#common').attr("value") == 1)
        shipping_address_form_Div.hide();
    }
<?php if (empty($this->is_populate)): ?>      
      $.mobile.activePage.find('#state_billing-wrapper').css("display", 'none');
<?php endif; ?>

<?php if (!empty($showShippingAddredss)) : ?>
      onSameAddress();
<?php endif; ?>
  });

  function onSameAddress() {
    if ($.mobile.activePage.find('#common').attr("value") == 1) {
      $.mobile.activePage.find('#sitestoreproduct_shipping_address_form').hide();
      $.mobile.activePage.find('#common').attr('checked', true);
      $.mobile.activePage.find('#common').attr("value", 2);
    } else {
      $.mobile.activePage.find('#common').attr('checked', false);
      $.mobile.activePage.find('#sitestoreproduct_shipping_address_form').show();
      $.mobile.activePage.find('#common').attr("value", 1);
<?php if (!empty($this->is_populate)): ?>
        if ($.mobile.activePage.find('#country_shipping').attr("value") != 0)
          $.mobile.activePage.find('#state_shipping-wrapper').css("display", 'block');
<?php endif; ?>
    <?php if (empty($this->is_populate)): ?>
      $.mobile.activePage.find('#f_name_shipping').attr("value", '');
      $.mobile.activePage.find('#l_name_shipping').attr("value", '');
      $.mobile.activePage.find('#phone_shipping').attr("value", '');
      $.mobile.activePage.find('#country_shipping').attr("value", 0);
      $.mobile.activePage.find('#city_shipping').attr("value", '');
      $.mobile.activePage.find('#locality_shipping').attr("value", '');
      $.mobile.activePage.find('#zip_shipping').attr("value", '');
      $.mobile.activePage.find('#address_shipping').attr("value", '');
  <?php endif; ?>
    }
  }

  function addOption(selectbox, text, value, state)
  {
    if (text != '' && value != '') {      
      $('<option value="'+value+'">'+text+'</option>').inject(selectbox);
      $.mobile.activePage.find('#'+state + '-wrapper').css("display", 'block');
    } else {
      $.mobile.activePage.find('#'+state + '-wrapper').css("display", 'none');
      $('<option value="'+value+'"></option>').inject(selectbox);
    }
  }

</script>

<div  id="dynamic_address_content">
<?php echo $this->form->setAttrib('class', 'sitestoreproduct_checkout_form_address')->render($this) ?>
</div>

<script type="text/javascript">
  sm4.core.runonce.add(function() {
    $.mobile.activePage.find('#billingregion_backgroundimage').insertAfter($.mobile.activePage.find('#country_billing')[0]);
    $.mobile.activePage.find('#billingregion_backgroundimage').css('display',  'none');
    if ($.mobile.activePage.find('#shippingregion_backgroundimage').length){
      $.mobile.activePage.find('#shippingregion_backgroundimage').insertAfter($.mobile.activePage.find('#country_shipping')[0]);
      $.mobile.activePage.find('#shippingregion_backgroundimage').css('display',  'none');
    }
  });
</script>

<div class='buttons'>
  <button type='button' data-theme="b" name="continue" onclick="billingAddress()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_2" class="t_center clr"></div>
</div>

<script type="text/javascript">

  function showRegions(flag) {

    var country = 'country_billing';
    var state = 'state_billing';
    var backgroundimage ='billingregion_backgroundimage';
    if (flag) {
      tempShipAddressErrorMsg = 0;
      country = 'country_shipping';
      state = 'state_shipping';
      backgroundimage ='shippingregion_backgroundimage';
    } else {
      tempBillAddressErrorMsg = 0;
    }

    $.mobile.activePage.find("#"+country + '_error').html('');
    $.mobile.activePage.find("#"+state + '-wrapper').css('display', 'none');
    // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
    if ($.mobile.activePage.find("#"+country).val() == 0) {     
      return;
    }
    $.mobile.activePage.find('#'+backgroundimage).css('display',  'inline-block');
    var addressType = state.split('_');
    $.mobile.activePage.find("#"+addressType[1] + 'region_backgroundimage').html( '<img class="pleft10" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif" />');

    sm4.core.request.send({
      type: "POST",
      dataType: "html",
      url: sm4.core.baseUrl + 'sitestoreproduct/index/changestate',
      method: 'GET',
      data: {
       'format': 'json',
        'country': $.mobile.activePage.find("#"+country).val()
      },

      success: function(responseJSON) {
        $.mobile.activePage.find("#"+addressType[1] + 'region_backgroundimage').html("");
        $.mobile.activePage.find('#'+backgroundimage).css('display',  'none');
        var subcatss = $.parseJSON(responseJSON);
       subcatss= $.parseJSON(subcatss.length);
        if (subcatss.length > 0) {
          $.mobile.activePage.find("#"+state + '-wrapper').css("display", 'block');
          $.mobile.activePage.find("#"+state).html();

          for (var i = 0; i < subcatss.length; ++i) {
            var subcatsss = subcatss[i].split('_');
            addOption($.mobile.activePage.find("#"+state), subcatsss[0], subcatsss[1], state);
          }

        } else {

          if (flag) {
            tempShipAddressErrorMsg = country + '_error';
          } else {
            tempBillAddressErrorMsg = country + '_error';
          }
          $.mobile.activePage.find("#"+country + '_error').html("<span id='" + state + "_error' class='r_text'><?php echo $this->translate("There are no region available for selected country.") ?></span>");
        }


    <?php if (!empty($this->is_populate)): ?>
            if (flag == 0) {
              $.mobile.activePage.find('#state_billing').val(<?php echo $this->billingRegionId ?>);
            }
  <?php endif; ?>
  <?php if (!empty($this->is_populate)): ?>
    <?php if (!empty($showShippingAddredss)): ?>
                if (flag == 1)
                  $.mobile.activePage.find('#state_shipping').val(<?php echo $this->shippingRegionId ?>);
    <?php endif; ?>
  <?php endif; ?>

      }

    });
  }

  function billingAddress()
  {
    sitestoreproduct_address = $.mobile.activePage.find('#store_address').formSerialize();
    sm4.core.request.send({
      url: sm4.core.baseUrl + 'sitestoreproduct/index/saveaddress',
      method: 'POST',
      dataType:'json',
      beforeSend: function() {
        $.mobile.activePage.find('#loading_image_2').html('<img src=' + sm4.core.staticBaseUrl + 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif />');

      },
      data: {
        format: 'json',
        address: sitestoreproduct_address,
        show_shipping: <?php echo $showShippingAddredss ?>,
        billing_add_id: <?php echo $this->billingAddId ?>,
        shipping_add_id: <?php echo $this->shippingAddId ?>
      },
      success: function(responseJSON)
      { 
        if (responseJSON.errorFlag === '0')
        {
          var subcatss = responseJSON.errorStr.split("::");
          for (var i = 0; i < subcatss.length; ++i)
          {
            var subcatsss = subcatss[i].split("=");
            $.mobile.activePage.find('#'+subcatsss[0]).html('');
          }

          var address = new Array(4);
          address[0] = responseJSON.shipping_region_id;
          address[1] = responseJSON.shipping_country_id;
          address[2] = responseJSON.billing_region_id;
          address[3] = responseJSON.billing_country_id;
          var addresses_id = address.toString();
          setTimeout("checkout(3, '" + addresses_id + "')", 100);
        }
        else
        {
          var subcatss = responseJSON.errorStr.split("::");
          for (var i = 0; i < subcatss.length; ++i)
          {
            var subcatsss = subcatss[i].split("=");

            if (subcatsss[1] != '0')
            {
              if ((subcatsss[0] == tempBillAddressErrorMsg) || (subcatsss[0] == tempShipAddressErrorMsg))
              {
                continue;
              }

              var country_billing = $.mobile.activePage.find('#country_billing').val();
              var country_shipping = '';
            <?php if (!empty($showShippingAddredss)) : ?>
               country_shipping = $.mobile.activePage.find('#country_shipping').val();
            <?php endif; ?>
              if (subcatsss[0] == 'country_billing_error' && country_billing.length != 0)
                subcatsss[1] = '<?php echo $this->translate("There are no region available for selected country.") ?>';

              if (subcatsss[0] == 'country_shipping_error' && country_shipping.length != 0)
                subcatsss[1] = '<?php echo $this->translate("There are no region available for selected country.") ?>';

               $.mobile.activePage.find('#'+subcatsss[0]).html( subcatsss[1]);
            }
            else
              $.mobile.activePage.find('#'+subcatsss[0]).html('');
          }
          
        }
        $.mobile.activePage.find('#loading_image_2').html('');
      }
    });
  }
</script>