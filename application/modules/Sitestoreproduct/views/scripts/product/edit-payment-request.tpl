<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-payment-request.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<div class="global_form_popup sitestoreproduct_dashbord_popup_form">
  <!-- IF ADMIN HAS DELETED THE PAYMENT REQUEST -->
  <?php if( !empty($this->sitestoreproduct_payment_request_deleted) ) : ?> 
    <div class="tip">
      <span>
        <?php echo $this->translate("Site administrator has deleted this payment request. So you can't edit this payment request."); ?>
        <div class='buttons'>
          <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; endif; ?>

  <!-- IF ADMIN HAS COMPLETED THE PAYMENT REQUEST -->
  <?php if( !empty($this->sitestoreproduct_payment_request_completed) ) : ?> 
    <div class="tip">
      <span>
        <?php echo $this->translate("Site administrator has completed this payment request. So you can't edit this payment request."); ?>
        <div class='buttons'>
          <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; endif; ?>

  <!-- IF ADMIN IS RESPONDING THE PAYMENT REQUEST -->
  <?php if( !empty($this->sitestoreproduct_admin_responding_request) ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate("Site administrator is responding your request. So you can't change your request."); ?>
        <div class='buttons'>
          <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; endif; ?>

  <!-- IF THERE IS NO PAYMENT GATEWAY ENABLE -->
  <?php if( !empty($this->gateway_disable) ) : ?>
    <!-- IF STORE ADMIN REQUEST FOR PAYMENT -->
    <?php if( empty($this->req_page_owner) ) : ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("Store owner has not enabled any paymnet gateway. So you can\'t proceed for payment request. Please contact to store owner.") ?>
          <div class='buttons'>
            <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
          </div>
        </span>
      </div>
    <?php return; endif; ?>

    <div class="tip">
      <span>
        <?php echo $this->translate("You have not enabled any paymnet gateway. Please first enable the payment gateway, then proceed for payment request. %s to enable the payment gateway.", '<a href="javascript:void(0)" onclick="gatewayEnable();">'.$this->translate("Click here").'</a>'); ?>
        <div class='buttons'>
          <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; endif; ?>
  
<?php echo $this->form->render($this); ?>
</div>

<script type="text/javascript">
function gatewayEnable()
{
  parent.window.location.href = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 53, 'method' => 'payment-info'), 'sitestore_store_dashboard', true); ?>';
  parent.Smoothbox.close();
}
</script>