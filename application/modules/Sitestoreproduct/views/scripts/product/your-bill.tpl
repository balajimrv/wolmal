<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: your-bill.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sitestoreproduct_manage_store sitestoreproduct_payment_to_me">
  <h3><?php echo $this->translate('Your Bill') ?></h3>
  <p class="mbot10">
    <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
        <?php echo $this->translate("Below, you can view the history of commissions paid by you so far for your store's product sales and can also pay the unpaid commissions. [Note: The payments made using 'Stripe Connect' gateway are not added here because those commissions are already paid at the time of payment.]"); ?>
    <?php else: ?>
        <?php echo $this->translate("Below, you can view history of commissions made by you and can also pay commissions for new orders."); ?>
    <?php endif; ?>      
  </p>

  <table class="sitestoreproduct_amount_table sitestoreproduct_data_table">
    <tr>
      <td class="txt_center">
        <span><?php echo $this->translate('Total Bill Amount [A = B+C]') ?></span>
        <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Amount need to pay)') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->totalBillAmount); ?></div>
      </td>
      <td class="txt_center">
        <span><?php echo $this->translate('New Bill Amount [B]') ?></span>
        <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Since last bill)'); ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->newBillAmount); ?></div>
      </td>
      <td class="txt_center">
        <span><?php echo $this->translate('Remaining Bill Amount [C]') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->remainingBillAmount) ?></div>
      </td>
      <td class="txt_center">
        <span><?php echo $this->translate('Paid Bill Amount') ?></span>
        <div class="txt_center bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->paidBillAmount) ?></div>
      </td>
    </tr> 
  </table>

  <?php if( !empty($this->totalBillAmount) ) : ?>
    <div class="clr mbot10">
      <a class="buttonlink sitestoreproduct_icon_payment" href="javascript:void(0)" onClick="Smoothbox.open('sitestoreproduct/product/bill-payment/store_id/<?php echo $this->store_id ?>');"><?php echo $this->translate("Make bill payment") ?></a>
    </div>
  <?php endif; ?>

  <div id="store_bill_details">
  <h4><?php echo $this->translate('Monthly Statements') ?></h4>
  <?php if (count($this->paginator)): ?>
    <div class="product_detail_table sitestoreproduct_data_table fleft">
      <table>
        <tr class="product_detail_table_head">
          <th><?php echo $this->translate("Month") ?></th>
          <th><?php echo $this->translate("Order Count") ?></th>
          <th><?php echo $this->translate("Order Amount") ?></th>
          <th><?php echo $this->translate("Commission Amount") ?></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
        <?php foreach ($this->paginator as $payment) : ?>        
          <tr>
            <td>
              <a href="javascript:void(0)" onclick="manage_store_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'product')">
                <?php echo $this->translate("%s %s", $payment->month, $payment->year) ?>
              </a>
            </td>
            <td><?php echo $payment->order_count ?></td>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->grand_total) ?></td>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->commission) ?></td>
            <td class="txt_center">
              <a href="javascript:void(0)" onclick="manage_store_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'product')">
                <?php echo $this->translate("details") ?>
              </a>
            </td>   
          </tr>
        <?php endforeach; ?>  
      </table>
    </div>
  </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("You have not paid any commission yet."); ?>
      </span>
    </div>
  <?php endif; ?>
  </div>