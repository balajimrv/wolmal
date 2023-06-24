<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: bill-details.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
<?php
if( $this->storeBillObj->status != 'active' ):
  $payment_status = 'No';
else:
  $payment_status = 'Yes';
endif;  
?>
<h3><?php echo $this->translate('Bill Detail'); ?></h3>
<div class="global_form_popup" style="width:600px;">
	<div id="manage_order_tab">
   <div class="invoice_order_details_wrap mtop10" style="border-width:1px;width:600px;">
    <ul>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Transaction Id'); ?></b></div>
        <div><?php echo $this->transaction->transaction_id; ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Bill Id'); ?></b></div>
        <div><?php echo $this->storeBillObj->storebill_id; ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Store Name'); ?></b></div>
        <div><?php echo $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('onclick' => 'redirectLink(\''.$this->sitestore->getHref().'\')')); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Owner Name'); ?></b></div>
        <div><?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('onclick' => 'redirectLink(\''.$this->sitestore->getHref().'\')')); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Amount'); ?></b></div>
        <div><?php echo


                                       Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->storeBillObj->amount); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Message'); ?></b></div>
        <div><?php echo empty($this->storeBillObj->message) ? '-' : $this->storeBillObj->message; ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Date'); ?></b></div>
        <div><?php echo gmdate('M d,Y, g:i A',strtotime($this->storeBillObj->creation_date)); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment'); ?></b></div>
        <div><?php echo $this->translate("%s", $payment_status); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Gateway'); ?></b></div>
        <div><?php echo Engine_Api::_()->sitestoreproduct()->getGatwayName($this->storeBillObj->gateway_id); ?></div>
      </li>
      <li>
        <div class="invoice_order_info fleft"><b><?php echo $this->translate('Gateway Transaction Id'); ?></b></div>
        <div>
          <?php if( !empty($this->transaction->gateway_transaction_id) ): ?>
            <a href="sitestoreproduct/payment/detail-transaction/transaction_id/<?php echo $this->transaction->transaction_id ?>" target="_blank"><?php echo $this->transaction->gateway_transaction_id ?></a>
          <?php else: ?>
            -
          <?php endif; ?>
        </div>
      </li>
    </ul> 
  </div>
</div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>

<script type="text/javascript">
function redirectLink(url)
{
  parent.window.location.href = url;
  parent.Smoothbox.close();
}
</script>