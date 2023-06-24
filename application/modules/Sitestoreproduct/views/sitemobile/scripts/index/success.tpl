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
<div class="sitestoreproduct_alert_msg b_medium">
  <?php if (empty($this->state) || ($this->state == 'active')): ?>
    <p>
      <?php echo $this->success_message; ?>
    </p>

  <?php elseif ($this->state == 'pending'): ?>
    <h3>
      <?php echo $this->translate('Payment Pending') ?>
    </h3>
    <p>
      <?php echo $this->translate('Thank you for submitting your payment. Your payment is currently pending - your order will be placed when we are notified that the payment has completed successfully.') ?>
    </p>

  <?php else: ?>
    <h3>
      <?php echo $this->translate('Payment Failed') ?>
    </h3>
    <p>
      <?php echo $this->translate('There was an error processing your transaction for the %s: %s.', $this->translate(array('order', 'orders', $this->indexNo)), $this->viewerOrders) ?>
      <?php if (!empty($this->viewer_id)) : ?>
        <?php echo $this->translate('We suggest that you please try again with another payment method after clicking on "make payment" from my orders page.') ?>
      <?php endif; ?>
    </p>

  <?php endif; ?>
</div>

<?php if (!empty($this->viewer_id)) : ?>
  <a data-role="button" data-theme="b" href="<?php echo $this->url(array('action' => 'my-order'), 'sitestoreproduct_product_general', true); ?>"> <?php echo $this->translate('Go to My Orders') ?></a>
<?php endif; ?>

<a data-role="button" data-theme="b" href="<?php echo $this->url(array("action" => "home"), "sitestoreproduct_general", true) ?>"><?php echo $this->translate('Continue Shopping') ?></a>






