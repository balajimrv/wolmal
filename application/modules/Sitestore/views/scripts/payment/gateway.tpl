<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: gateway.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<?php if ($this->status == 'pending'): // Check for pending status ?>
  <?php echo $this->translate('Your store is pending payment. You will receive an email when the
		payment completes.'); ?>
<?php else: ?>

  <form method="get" action="<?php echo $this->escape($this->url(array(), "sitestore_process_payment", true)) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Order your Store') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('You have created a store that requires payment. You will be taken to a secure checkout area where you can pay for your store.') ?>
        </p>
        <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;max-width:none;">
          <?php if ($this->package->recurrence): ?>
            <?php echo $this->translate('Your Store requires payment:') ?>
          <?php else: ?>
            <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
          <?php endif; ?>
          <?php echo $this->package->getPackageDescription() . "." ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <?php foreach ($this->gateways as $gatewayInfo):
              $gateway = $gatewayInfo['gateway'];
              $plugin = $gatewayInfo['plugin'];
              $first = (!isset($first) ? true : false );
              ?>
              <?php if (!$first): ?>
                <?php echo $this->translate('or') ?>
                <?php endif; ?>
              <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
              <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="gateway_id" id="gateway_id" value="" />
  </form>
<?php endif; ?>