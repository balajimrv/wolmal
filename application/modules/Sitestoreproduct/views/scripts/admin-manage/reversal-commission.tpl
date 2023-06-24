<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reversal-commission.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

 <div class="global_form_popup sitestore_claim_action_popup">
	<div class="settings">
	  <form class="global_form" method="POST">
	    <div>
        <?php if ($this->order->non_payment_admin_reason == 1): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the reversal commission request that was approved.") ?></p>
	      <?php elseif ($this->order->non_payment_admin_reason == 2): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the reversal commission request that was declined.") ?></p>
	      <?php else: ?>	
	        <h3><?php echo $this->translate("Take an Action"); ?></h3>
	        <p><?php echo $this->translate("Please take an appropriate action on the reversal commission request for this order. Once you take an action, an email will be sent to store owner stating the action taken by you.") ?></p><br />
	      <?php endif; ?>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo "Order Id:" ?></label>
	        </div>
	        <div class="form-element">
	          <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $this->order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $this->order->order_id), 'sitestore_store_dashboard', false) ?>" target="_blank">
              <?php echo '#'.$this->order->order_id; ?>
            </a>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Store Name:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('target' => '_blank')); ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Owner Name:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->htmlLink($this->storeOwner->getHref(), $this->storeOwner->getTitle(), array('target' => '_blank')); ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Order Amount:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->order->grand_total); ?>
	        </div>
	      </div>		
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Commission Amount:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->order->commission_value); ?>
	        </div>
	      </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo $this->translate("Seller Reason:") ?></label>
          </div>
          <div class="form-element">
            <?php if( $this->order->non_payment_seller_reason == 1 ) : ?>
              <?php echo 'Chargeback' ?>
            <?php elseif( $this->order->non_payment_seller_reason == 2 ) : ?>
              <?php echo 'Payment not received' ?>
            <?php elseif( $this->order->non_payment_seller_reason == 3 ) : ?>
              <?php echo 'Canceled payment' ?>
            <?php endif; ?>
          </div>
        </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Seller Message:") ?></label>
	        </div>
	        <div class="form-element">
	          <?php echo empty($this->order->non_payment_seller_message) ? '-' : $this->order->non_payment_seller_message ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Action:") ?> </label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->order->non_payment_admin_reason == 1) : ?>
	            <?php echo $this->translate("Approved") ?>
	          <?php elseif ($this->order->non_payment_admin_reason == 2) : ?>
	            <?php echo $this->translate("Declined") ?>
	          <?php else: ?>
	            <select name="reversal_commission_action">
	              <option value="1"><?php echo $this->translate("Approved") ?></option>
	              <option value="2"><?php echo $this->translate("Declined") ?></option>
	              <option value="3" <?php if ($this->order->non_payment_admin_reason == 3) : echo 'selected'; endif; ?>><?php echo $this->translate("Hold") ?></option>
	            </select>
	          <?php endif; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Admin's Comments:") ?> </label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->order->non_payment_admin_reason == 1 || $this->order->non_payment_admin_reason == 2) : ?>
	            <?php if (!empty($this->order->non_payment_admin_message)): ?>
	              <?php echo $this->order->non_payment_admin_message; ?>
	            <?php else: ?>
	              <?php echo '---'; ?>
	            <?php endif; ?>
	          <?php else: ?>		
	            <textarea name="non_payment_admin_message"><?php echo $this->order->non_payment_admin_message; ?></textarea>
	          <?php endif; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label>&nbsp;</label>
	        </div>
	        <div class="form-element">
	          <?php if ($this->order->non_payment_admin_reason == 1) : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
	          <?php elseif ($this->order->non_payment_admin_reason == 2) : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
	          <?php else: ?>
	            <button type='submit'><?php echo $this->translate('Save'); ?></button>
	            <?php echo $this->translate(" or ") ?> 
	            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
	          <?php endif; ?>
	        </div>
	      </div>
	    </div>
	  </form>
	</div>
</div>