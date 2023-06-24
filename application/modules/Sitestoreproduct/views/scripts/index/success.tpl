<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: success.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); 
      include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; ?>

<div class="sitestoreproduct_alert_msg b_medium">
  <?php if( empty($this->state) || ($this->state == 'active') ): ?>
    <p>
      <?php if( !empty($this->downpayment_make_payment) ) : ?>
      <?php $orderViewPage = '<a href="'.$this->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $this->order_id), 'sitestoreproduct_general', true).'">#'.$this->order_id.'</a>'; ?>
        <?php echo $this->translate("Thank you for submitting your remaining amount payment for order %s.", $orderViewPage); ?>
      <?php else: ?>
        <?php echo $this->success_message; ?>
      <?php endif; ?>
    </p>

  <?php elseif( $this->state == 'pending' ): ?>
    <h3>
      <?php echo $this->translate('Payment Pending') ?>
    </h3>
    <p>
      <?php echo $this->translate('Thank you for submitting your payment. Your payment is currently pending - your order will be placed when we are notified that the payment has completed successfully.') ?>
    </p>

  <?php else:?>
    <h3>
      <?php echo $this->translate('Payment Failed') ?>
    </h3>
    <p>
      <?php echo $this->translate('There was an error processing your transaction for the %s: %s.', $this->translate(array('order', 'orders', $this->indexNo)), $this->viewerOrders) ?>
      <?php if( !empty($this->viewer_id) ) : ?>
        <?php echo $this->translate('We suggest that you please try again with another payment method after clicking on "make payment" from my orders page.') ?>
      <?php endif; ?>
    </p>

  <?php endif; ?>
</div>

<button class="mtop10" onclick="continueShopping()">
  <?php echo $this->translate('Continue Shopping') ?>
</button>
<?php if( !empty($this->viewer_id) ) : ?>
  <button class="mtop10" onclick="viewYourOrder()">
    <?php echo $this->translate('Go to My Orders') ?>
  </button>
<?php endif; ?>

<script type="text/javascript">
  function viewYourOrder()
  {
    window.location.href = '<?php echo $this->url(array('action' => 'account', 'menuType' => 'my-orders'), 'sitestoreproduct_general', true) ?>';
  }
  
  function continueShopping()
  {
    window.location.href = '<?php echo $this->url(array("action" => "home"), "sitestoreproduct_general", true) ?>';
  }
</script>
    