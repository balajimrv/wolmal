<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: vat.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestoreproduct_manage_store">
  <h3 class="mbot10"><?php echo $this->translate('Default VAT') ?></h3>
  <p>
    <?php echo $this->translate('Enter the default VAT for all Products. But, If store owner has entered "Special VAT" for product then in that case special VAT will be applied on the product.'); ?>
  </p>
  <br />
<!--<div class="mtop10">
    <?php //echo $this->translate("Here, you can configure tax. For tax, you can configure tax percentage / amount. The amount for the taxes created by you will be payable to you. <br /><strong>Note:</strong> General taxes on %s will be applied on all the products in addition to the taxes created by you.", $this->site_title); ?>
</div>-->

<ul class="form-errors" id="vat_invalid_rate_message" style="display: none">
  <li>
    <?php echo $this->translate("Please enter the Rate(%) value between 0 to 100."); ?>
  </li>                                   
</ul>

<ul class="form-notices" id="vat_creation_success_message" style="display: none">
  <li>
    <?php echo $this->translate("Changes Successfully Saved."); ?>
  </li>                                   
</ul>



  <div>
    <?php echo $this->form->render($this); ?>
  </div>
  
<script>
en4.core.runonce.add(function(){
  showPriceType();
  
  $("store_vat").removeEvents('submit').addEvent('submit', function(e) {
  e.stop();//alert($("store_vat").toQueryString());return;
  en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'sitestoreproduct/tax/save-vat-detail/store_id/<?php echo $this->store_id ?>',
        method : 'POST',
        onRequest: function(){
          $('vat_loading_image-wrapper').style.display = 'block';
          $('vat_loading_image-wrapper').style.clear = 'none';
          $("vat_creation_success_message").style.display = 'none';
          $("vat_invalid_rate_message").style.display = 'none';
        },
        data : {
          format : 'json',
          vat_id : '<?php echo $this->vat_id ?>',
          storeVatValues : $("store_vat").toQueryString()
        },
        onSuccess : function(responseJSON) {
          $('vat_loading_image-wrapper').style.display = 'none';
          
          if( responseJSON.VATinvalidRateMessage )
            $("vat_invalid_rate_message").style.display = 'block';
          
          if( responseJSON.VATSuccessMessage )
            $("vat_creation_success_message").style.display = 'block';
          
        }
      })
    ); 
  });
});

function showPriceType(){
  if(document.getElementById('handling_type')){
    if(document.getElementById('handling_type').value == 1) {
      document.getElementById('tax_price-wrapper').style.display = 'none';
      document.getElementById('tax_rate-wrapper').style.display = 'block';

    } else{
      document.getElementById('tax_price-wrapper').style.display = 'block';
      document.getElementById('tax_rate-wrapper').style.display = 'none';
    }
  }
}


</script>