<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _productSellingPrice.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div id="product_selling_price-wrapper" class="form-wrapper">
  <div id="product_selling_price-label" class="form-label">
    <label for="product_selling_price" class="optional"><?php echo $this->translate('Selling price (%s)', $this->currency_name); ?></label>
  </div>
  
  <div id="product_selling_price-element" class="form-element" style="min-width: 220px;">
    <input name="product_selling_price" id="product_selling_price" value="<?php echo !empty($this->selling_price)? $this->selling_price : ""?>" type="text" readonly>	
    <span class="document_show_tooltip_wrapper">
      <div class="document_show_tooltip" style="margin-left: 1%; margin-top: 0%;">
				<?php echo $this->translate('Selling price mentioned here is the price that will be shown to the customers. This Selling price is calculated on the basis of VAT, product price and discount mentioned above. To know Selling price of your product, click on the "Show Selling Price" button for new Selling Price.');?>
			</div>
      <img class="mright5" src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/help16.gif' ;?>">
		</span>
  </div>
  <div id="buttons-element" class="form-element" style="min-width: 110px;">
    <button name="sellingPriceButton" id="sellingPriceButton" type="button" onclick="showSellingPrice();" style="padding: 1px; font-size: 10px; "><?php echo $this->translate('Show Selling price'); ?></button>
  </div>
  <div class="form-element" id="sellingPriceLoading" style="display: none; min-width: 30px">
    <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/core/loading.gif' ?>" />
  </div>
  
</div>
