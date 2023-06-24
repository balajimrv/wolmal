<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sitestoreproduct_checkout_tabs sitestoreproduct_ckout_pro">
	<ul>
  	<li class="seaocore_txt_light">
    	<span><?php echo $this->translate("CHECKOUT_ADDRESS_BOOK") ?></span>
    	<div id="sitestoreproduct_checkout_process_address" class="sitestoreproduct_ckout_procont b_medium">
 				<div>
          <strong class="bold"><?php echo $this->translate("Billing Address") ?></strong>
          <a href="javascript:void(0)" onclick="checkoutProcess(2)" class="fright"><?php echo $this->translate("Change") ?></a>
        </div>  
        <div id="sitestoreproduct_checkout_process_billing_address"></div>
        <?php if( !empty($this->sitestoreproduct_virtual_product) || !empty($this->sitestoreproduct_other_product_type)) : ?>
         	<div class="mtop10"><strong class="bold"><?php echo $this->translate("Shipping Address") ?></strong></div>
          <div id="sitestoreproduct_checkout_process_shipping_address"></div>
        <?php endif; ?>
      </div>
    </li>
    	
    <?php if( !empty($this->sitestoreproduct_other_product_type) ) : ?>
      <li class="seaocore_txt_light">
        <span><?php echo $this->translate("Shipping Methods") ?></span>
        <div id="sitestoreproduct_checkout_process_shipping" class="sitestoreproduct_ckout_procont b_medium">
          <div>
            <a href="javascript:void(0)" onclick="checkoutProcess(3)" class="fright"><?php echo $this->translate("Change") ?></a>
          </div>
          <div id="sitestoreproduct_checkout_process_shipping_method"></div>
        </div>
      </li>
    <?php endif; ?>
    
    <li class="seaocore_txt_light">
    	<span><?php echo $this->translate("Payment Method") ?></span>
        <div id="sitestoreproduct_checkout_process_payment" class="sitestoreproduct_ckout_procont b_medium">
          <div>
            <a href="javascript:void(0)" onclick="checkoutProcess(4)" class="fright"><?php echo $this->translate("Change") ?></a>
          </div>
          <div id="sitestoreproduct_checkout_process_payment_information"></div>
        </div>
    </li>
  </ul>
</div>


