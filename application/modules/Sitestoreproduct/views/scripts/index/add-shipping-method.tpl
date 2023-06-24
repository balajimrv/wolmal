<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-shipping-method.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="store_back_to_tax" class="paginator_previous">
  <a href="javascript:void(0);" onclick="manage_store_dashboard(51, 'shipping-methods', 'index');" class="buttonlink icon_previous mbot10"><?php echo $this->translate('Back to Manage Shipping Methods'); ?></a>
</div>

<?php if (empty($this->noCountryEnable)) : ?>
  <div id="no_country_tip" class="tip">
    <span>
      <?php echo $this->translate("There are no locations enabled by site administrator.") ?>
    </span>
  </div>

  <div class='buttons'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
  <?php return;
endif; ?> 

<script type="text/javascript">
  
    var setRegion = null;
    var optn = document.createElement("OPTION");

    en4.core.runonce.add(function() {
    showHandlingType();
    if (document.getElementById('country').value == 'ALL') {
      document.getElementById('all_regions-wrapper').style.display = 'none';
      document.getElementById('state-wrapper').style.display = 'none';
    }
      $('create_shipping_methods').addEvent('submit', function(e) {
        e.stop();
        $('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
        en4.core.request.send(new Request.JSON({
          url: en4.core.baseUrl + 'sitestoreproduct/product/saveshippment',
          method: 'POST',
          data: {
            format: 'json',
            shipping_method: $('create_shipping_methods').toQueryString(),
            store_id: <?php echo $this->store_id; ?>
          },
          onSuccess: function(responseJSON) {
            $('spiner-image').innerHTML = '';
            if ($('create_shipping_methods').getElement('.form-errors'))
              $('create_shipping_methods').getElement('.form-errors').destroy();
            // IF THERE ARE NO ERROR FOUND THEN REDIRECT TO MANAGE SHIPPING METHODS PAGE.
            if (responseJSON.errorFlag === '0') {
              new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
              manage_store_dashboard(51, "shipping-methods/notice/1", "index");
            } else {
              var addErrors = new Element('ul', {
                'class': 'form-errors',
                'html': responseJSON.errorMsgStr
              });
              addErrors.inject($('create_shipping_methods').getElement('.form-elements'), 'before');
              new Fx.Scroll(window).start(0, $('create_shipping_methods').getElement('.form-errors').getCoordinates().top);
            }
          }
        }));
      });
      showDependency();
      showRegions(<?php echo $this->store_id ?>, 0, null, 0, null);
    });

    function showDependency() {
      var handlingTypeLength = document.getElementById("handling_type").length;
      if (document.getElementById('ship_start_limit') && document.getElementById('ship_start_limit').value == 0.00)
        document.getElementById('ship_start_limit').value = '0';
        
      if (document.getElementById('dependency').value == 1) {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'block';
        document.getElementById("ship_type-wrapper").style.display = 'none';
        document.getElementById('weight_limit-wrapper').style.display = 'none';
        document.getElementById('handling_type-wrapper').style.display = 'block';
        document.getElementById('ship_limit-label').innerHTML = "<label class='optional' for='price'><?php echo $this->translate('Order Weight between (in %s)', $this->weightUnit) ?></label>";
        showShipType();
      } 
      else if (document.getElementById('dependency').value == 2) {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'none';
        document.getElementById('ship_type-wrapper').style.display = 'block';
        document.getElementById('weight_limit-wrapper').style.display = 'block';
        document.getElementById('ship_type').set('value', 1);
        document.getElementById('ship_limit-label').innerHTML = "<label class='optional' for='price'><?php echo $this->translate('Products Quantity between') ?></label>";
        showShipType();
      }
      else {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'none';
        document.getElementById('handling_type-wrapper').style.display = 'block';
        document.getElementById('ship_type-wrapper').style.display = 'none';
        document.getElementById('weight_limit-wrapper').style.display = 'block';
        if (handlingTypeLength == 3) {
          document.getElementById('handling_type').options.remove(2);
        }
        if (handlingTypeLength == 1) {
          optn.text = '<?php echo $this->translate("Percentage") ?>';
          optn.value = 1;
          document.getElementById('handling_type').options.add(optn);
        }
        showHandlingType();
        document.getElementById('ship_limit-label').innerHTML = "<label class='optional' for='price'><?php echo $this->translate('Order Cost between (%s)', $this->currencyName) ?></label>";
      }
    }
    
    function showShipType()
    {
      var handlingTypeLength = document.getElementById("handling_type").length;

      if(document.getElementById('dependency').value == 1){
        if (handlingTypeLength == 1) {
            optn.text = '<?php echo $this->translate("Percentage") ?>';
            optn.value = 1;
            document.getElementById('handling_type').options.add(optn);
            optn.text = '<?php echo $this->translate("Per Unit Weight") ?>';
            optn.value = 2;
            document.getElementById('handling_type').options.add(optn);
          }
          if (handlingTypeLength == 2) {
            optn.text = '<?php echo $this->translate("Per Unit Weight") ?>';
            optn.value = 2;
            document.getElementById('handling_type').options.add(optn);
          }
      }else{
        if (document.getElementById('ship_type').value == 0)
            document.getElementById('handling_type-wrapper').style.display = 'none';
          else {
            document.getElementById('handling_type-wrapper').style.display = 'block';
            if (handlingTypeLength == 1) {
              optn.text = '<?php echo $this->translate("Percentage") ?>';
              optn.value = 1;
              document.getElementById('handling_type').options.add(optn);
            }
            if (handlingTypeLength == 3)
              document.getElementById('handling_type').options.remove(2);
          }  
      }
      showHandlingType();
    }
    
    function showHandlingType() {
      if ($('handling_type')) {
        if ($('handling_type').value == 1) {
          document.getElementById('price-wrapper').style.display = 'none';
          document.getElementById('rate-wrapper').style.display = 'block';
        } else {
          document.getElementById('price-wrapper').style.display = 'block';
          document.getElementById('rate-wrapper').style.display = 'none';
        }
      }
    }

    function showRegions(storeId, type, region, regionId, country) {
      // IF THERER IS NO COUNTRY SELECT THEN HIDE STATE WRAPPER
      if ($('country').value == 'ALL') {
        $('region_backgroundimage').innerHTML = "";
        document.getElementById('all_regions-wrapper').style.display = 'none';
        document.getElementById('state-wrapper').style.display = 'none';
        return;
      }

      document.getElementById('all_regions-wrapper').style.display = 'none';
      document.getElementById('state-wrapper').style.display = 'none';

      $('region_backgroundimage').innerHTML = '<div class="form-label" id="country-label"></div><div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></div>';
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'sitestoreproduct/index/changestate',
        method: 'GET',
        data: {
          format: 'json',
          country: $('country').value,
          store_id: storeId
        },
        onSuccess: function(responseJSON) {
          $('region_backgroundimage').innerHTML = "";
          var states = 0;
         
          states = JSON.decode(responseJSON.length);

         $('state').innerHTML = "";
          if (states && states != 0) {
          document.getElementById('all_regions-wrapper').style.display = 'block';
          document.getElementById('state-wrapper').style.display = 'block';
            // addSelectBoxOption($('state'), 'All Regions', 0,'state');
            for (var i = 0; i < states.length; ++i) {
              var statess = states[i].split('_');
              if((responseJSON.tempFlag > 0)) {
                if(statess[0]) {
                  addSelectBoxOption($('state'), statess[0], statess[1], 'state');
                }
              }else {
                addSelectBoxOption($('state'), statess[0], statess[1], 'state');
                document.getElementById('all_regions-yes').checked = 1;
                document.getElementById('all_regions-no').checked = 0;
                document.getElementById('all_regions-wrapper').style.display = 'none';
                document.getElementById('state-wrapper').style.display = 'none';
              }
//              addSelectBoxOption($('state'), statess[0], statess[1], 'state');
            }

            if (setRegion) {
              $('state').value = setRegion;
            }
            if((responseJSON.tempFlag > 0)) {
              showAllRegions();
            }
          } else {
            if (type == 1 && country == $('country').value) {
              addSelectBoxOptionTableRate($('state'), region, regionId, 'state');
            } else {
              document.getElementById('all_regions-yes').checked = "checked";
              $('region_backgroundimage').style.display = 'block';
              document.getElementById('all_regions-wrapper').style.display = 'none';
              document.getElementById('state-wrapper').style.display = 'none';

            }
          }          
        }

      })
              );
    }

    function addSelectBoxOption(selectbox, text, value, state)
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;

      if (optn.text != '' && optn.value != '') {
        $(state).style.display = 'block';
        $(state + '-wrapper').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $(state).style.display = 'none';
        $(state + '-wrapper').style.display = 'none';
        selectbox.options.add(optn);
      }
    }

    function showAllRegions()
    {
      var radios = document.getElementsByName("all_regions");
      var radioValue;
      if (radios[0].checked) {
        radioValue = radios[0].value;
      } else {
        radioValue = radios[1].value;
      }
      if (radioValue == 'yes') {
        $('state-wrapper').style.display = 'none';
      } else {
        $('state-wrapper').style.display = 'block';
      }
    }
</script>

<?php echo $this->form->render($this) ?>