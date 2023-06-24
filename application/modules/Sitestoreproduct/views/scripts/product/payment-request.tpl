<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-request.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<div class="global_form_popup sitestoreproduct_dashbord_popup_form">

<!-- IF THERE IS NO PAYMENT GATEWAY ENABLE -->
<?php if( !empty($this->gateway_disable) ) : ?>
  <!-- IF PAGE ADMIN REQUEST FOR PAYMENT -->
  <?php if( empty($this->req_page_owner) ) : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("Store owner has not enabled any payment gateway. So you can't proceed for payment request. Please contact to store owner.") ?>
        <div class="buttons mtop10">
          <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; endif; ?>
  
  <div class="tip">
    <span>
      <?php echo $this->translate('You have not configured or enabled the payment gateways for your store. Please first configure and enable payment gateways to request for payment. Please %s to go to Manage Gateways.', '<a href="javascript:void(0)" onclick="gatewayEnable();" class="bold">'.$this->translate("Click here").'</a>') ?>
      <div class="buttons mtop10">
        <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
      </div>
    </span>
  </div>
<?php elseif( !empty($this->not_allowed_for_payment_request) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Sorry, you are not allowed to send request for this amount as the balance amount is less than the threshold amount. So, please request when your balance amount is greater than or equal to the threshold amount.') ?>
    <div class="buttons mtop10">
      <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
    </div>
    </span>
  </div>
<?php else: ?>
  <?php echo $this->form->render($this); ?>
<?php endif; ?>
</div>

<script type="text/javascript">
function gatewayEnable()
{
  parent.window.location.href = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 53, 'method' => 'payment-info'), 'sitestore_store_dashboard', true)?>';
  parent.Smoothbox.close();
}
</script>
