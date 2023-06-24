<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-shipping-method.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$localeObject = Zend_Registry::get('Locale');
$currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

$weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');
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

<?php $weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs'); ?>

<script type="text/javascript">
    var optn = document.createElement("OPTION");
    en4.core.runonce.add(function() {
      $('create_shipping_methods').addEvent('submit', function(e) {
        e.stop();
        $('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
        en4.core.request.send(new Request.JSON({
          url: en4.core.baseUrl + 'sitestoreproduct/product/saveshippment',
          method: 'POST',
          data: {
            format: 'json',
            shipping_method: $('create_shipping_methods').toQueryString(),
            method_id: <?php echo $this->method_id; ?>,
            store_id: <?php echo $this->store_id; ?>
          },
          onSuccess: function(responseJSON) {
            $('spiner-image').innerHTML = '';
            if ($('create_shipping_methods').getElement('.form-errors'))
              $('create_shipping_methods').getElement('.form-errors').destroy();
            // IF THERE ARE NO ERROR FOUND THEN REDIRECT TO MANAGE SHIPPING METHODS PAGE.
            if (responseJSON.errorFlag === '0') {
              new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
              manage_store_dashboard(51, "shipping-methods/notice/2", "index");
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
      document.getElementById('allow_weight_from').set('value', '<?php echo $this->flagWeightFrom ?>');
      document.getElementById('allow_weight_to').set('value', '<?php echo $this->flagWeightTo ?>');

<?php if (isset($this->flagWeightFromValidate)): ?>
        document.getElementById('allow_weight_from').set('value', '<?php echo $this->flagWeightFromValidate ?>');
<?php endif; ?>
<?php if (isset($this->flagWeightToValidate)): ?>
        document.getElementById('allow_weight_to').set('value', '<?php echo $this->flagWeightToValidate ?>');
<?php endif; ?>

<?php if (isset($this->flagShipFrom)): ?>
        document.getElementById('ship_start_limit').set('value', '<?php echo $this->flagShipFrom ?>');
<?php endif; ?>
<?php if (isset($this->flagShipTo)): ?>
        document.getElementById('ship_end_limit').set('value', '<?php echo $this->flagShipTo ?>');
<?php endif; ?>

      showDependency();
    });

    function showDependency() {
      var handlingTypeLength = document.getElementById("handling_type").length;
      var optn1 = document.createElement("OPTION");
      var optn2 = document.createElement("OPTION");
      if ($('dependency').value == 1) {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'block';
        document.getElementById('handling_type-wrapper').style.display = 'block';
        document.getElementById("ship_type-wrapper").style.display = 'none';
        document.getElementById('weight_limit-wrapper').style.display = 'none';
<?php if (!empty($this->flagShipType)): ?>
          document.getElementById('handling_type').set('value', 2);
<?php endif; ?>
        document.getElementById('ship_limit-label').innerHTML = '<label class="optional" for="price"><?php echo $this->translate('Order Weight between (in %s)', $weightUnit) ?></label>';
        showShipType();
      } else if ($('dependency').value == 2) {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'none';
        document.getElementById('ship_type-wrapper').style.display = 'block';
        document.getElementById('weight_limit-wrapper').style.display = 'block';
<?php if (!empty($this->flagShipType)): ?>
          document.getElementById('ship_type').set('value', '<?php echo $this->flagShipType ?>');
<?php endif; ?>
        document.getElementById('ship_limit-label').innerHTML = '<label class="optional" for="price"><?php echo $this->translate('Products Quantity in Order') ?></label>';
        showShipType();
      }
      else {
        document.getElementById("handling_type-element").getFirst('p').style.display = 'none';
        document.getElementById('handling_type-wrapper').style.display = 'block';
        document.getElementById('ship_type-wrapper').style.display = 'none';
        document.getElementById('weight_limit-wrapper').style.display = 'block';
        if (handlingTypeLength == 1) {
          optn.text = '<?php echo $this->translate("Percentage") ?>';
          optn.value = 1;
          $('handling_type').options.add(optn);
        }
        showHandlingType();
        document.getElementById('ship_limit-label').innerHTML = '<label class="optional" for="price"><?php echo $this->translate('Order Cost Between (In %s)', $currencyName) ?></label>';
      }
    }


    function showShipType() {
      var handlingTypeLength = document.getElementById("handling_type").length;
      var optn = document.createElement("OPTION");
      if (document.getElementById('dependency').value == 1) {
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
      } else {
        if (document.getElementById('ship_type').value == 0)
          document.getElementById('handling_type-wrapper').style.display = 'none';
        else {
          document.getElementById('handling_type-wrapper').style.display = 'block';
          if (handlingTypeLength == 1) {
            optn.text = '<?php echo $this->translate("Percentage") ?>';
            optn.value = 1;
            $('handling_type').options.add(optn);
          }
          if (handlingTypeLength == 3)
            $('handling_type').options.remove(2);
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

</script>

<?php echo $this->form->render($this) ?>