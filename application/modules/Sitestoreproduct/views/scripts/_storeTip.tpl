<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _storeTip.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>  


<?php $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName(); ?>
<?php $allowPaymentrequest = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.paymentrequest', 1); ?>

<!--CHECK FOR SHIPPING METHODS-->
<?php if( $moduleName == 'sitestore' && !isset($_COOKIE[$moduleName . '_dismiss_shipping']) && empty($this->isAnyShippingMethodExist) && ($this->countProductTypes > 2 || $this->product_types == 'simple' || $this->product_types == 'grouped' || $this->product_types == 'bundled' || $this->product_types == 'configurable')) : ?>
  <div id="dismiss_shipping">
    <div class="sitestore_notice">
      <div class="sitestore_notice_icon">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
      </div>
      <div class="fright">
        <button onclick="dismiss('<?php echo $moduleName; ?>', 'shipping');"><?php echo $this->translate('Dismiss'); ?></button>
      </div>
      <div class="sitestore_notice_text">
        <?php echo $this->translate("You have not configured any shipping method for this store yet. Please %s to configure the shipping methods.", "<a href='javascript:void(0)' onclick='manage_store_dashboard(51, \"shipping-methods\", \"index\");'>".$this->translate("click here")."</a>") ?>
      </div>	
    </div>
  </div>
<?php endif; ?>

<!--CHECK FOR PAYMENT INFO-->
<?php if( $moduleName == 'sitestore' && !isset($_COOKIE[$moduleName . '_dismiss_payment']) && empty($this->store_gateway) && !empty($allowPaymentrequest) ) : ?>
  <div id="dismiss_payment">
    <div class="sitestore_notice">
      <div class="sitestore_notice_icon">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
      </div>
      <div class="fright">
        <button onclick="dismiss('<?php echo $moduleName; ?>', 'payment');"><?php echo $this->translate('Dismiss'); ?></button>
      </div>
      <div class="sitestore_notice_text">
        <?php if( empty($this->isPaymentToSellerEnable) ) : ?>
          <?php echo $this->translate("You have not configured or enabled the payment gateways for this store yet. Please %s to configure and enable the payment gateways.", "<a href='javascript:void(0)' onclick='manage_store_dashboard(53, \"payment-info\", \"product\");'>".$this->translate("click here")."</a>") ?>
        <?php else: ?>
          <?php echo $this->translate("You have not configured or enabled the payment gateways for this store yet. So, buyers will not be able to purchase products from this store. Please %s to configure and enable the payment gateways.", "<a href='javascript:void(0)' onclick='manage_store_dashboard(53, \"payment-info\", \"product\");'>".$this->translate("click here")."</a>") ?>
        <?php endif; ?>
      </div>	
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        if(sitestore_dismiss_shipping && $('dismiss_shipping')){
            $('dismiss_shipping').style.display = 'none';
        }
        if(sitestore_dismiss_payment && $('dismiss_payment')){
            $('dismiss_payment').style.display = 'none';
        }
    });  

</script> 
