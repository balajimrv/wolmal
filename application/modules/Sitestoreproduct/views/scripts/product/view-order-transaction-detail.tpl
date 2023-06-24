<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-order-transaction-detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2><?php echo $this->translate('Order Transaction Detail'); ?></h2>

<dl class="sitestoreproduct_transaction_details">
  <dd>
    <?php echo $this->translate('Transaction Id') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toNumber($this->transaction_id) ?>
  </dt>
  
  <dd>
    <?php echo $this->translate('Order Id') ?>
  </dd>
  <dt>
  <a href="javascript:void(0)" onclick="redirectLink('<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $this->order_id), 'sitestore_store_dashboard', true) ?>')">
    <?php echo '#'.$this->order_id ?>
  </a>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Gateway') ?>
  </dd>
  <dt>
    <?php if( !empty($this->payment_gateway) ): ?>
        <?php echo Engine_Api::_()->sitestoreproduct()->getGatwayName($this->payment_gateway); ?>
    <?php else: ?>
      <i><?php echo $this->translate('Unknown Gateway') ?></i>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Type') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->payment_type)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment State') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->payment_state)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Amount') ?>
  </dd>
  <dt>
    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->grand_total);?>
  </dt>

  <dd>
    <?php echo $this->translate('Gateway Transaction Id') ?>
  </dd>
  <dt>
    <?php if( !empty($this->gateway_transaction_id) ): ?>
      <a href="sitestoreproduct/payment/detail-transaction/transaction_id/<?php echo $this->transaction_id ?>" target="_blank"><?php echo $this->gateway_transaction_id ?></a>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Transaction Date') ?>
  </dd>
  <dt>
    <?php echo gmdate('M d,Y, g:i A',strtotime($this->date)); ?>
  </dt>

</dl>
<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
</div>

<script type="text/javascript">
function redirectLink(url)
{
  parent.window.location.href = url;
  parent.Smoothbox.close();
}
</script>