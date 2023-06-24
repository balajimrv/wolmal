<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-user-transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php 
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/admin/style_sitestoreproduct.css');
?>

<div class="global_form_popup">
  <div id="manage_order_tab">
  <h3><?php echo $this->translate('Transaction Details'); ?></h3>
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;">
      <ul class="payment_transaction_details">
      	<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Transaction ID'); ?></b></div>
          <div><?php echo $this->locale()->toNumber($this->transaction_obj->transaction_id) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Order Id') ?></b></div>
          <div>
            <?php
              $index = 1;
              $tempCount = @COUNT($this->order_ids);
              foreach ($this->order_ids as $order_id) {
                if ($index != 1) {
                  if ($tempCount == $index) {
                    echo $this->translate(" SITESTOREPRODUCT_CHECKOUT_AND ");
                  } else {
                    echo ', ';
                  }
                }
                $order_view_url = $this->url(array('action' => 'store', 'store_id' => $order_id['store_id'], 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id['order_id']), 'sitestore_store_dashboard', true);
                echo $this->htmlLink($order_view_url, "#" . $order_id['order_id'], array('target' => '_blank'));
                $index++;
              } ?>
           </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Buyer Name'); ?></b></div>
          <div><?php echo empty($this->transaction_obj->user_id) ? '-' : $this->htmlLink($this->user_obj->getHref(), $this->user_obj->getTitle(), array('target' => '_blank')) ?></div>
        </li>
				<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Gateway'); ?></b></div>
        <?php if( empty($this->gateway_name) ): ?>
          <div><i><?php echo $this->translate('Unknown Gateway') ?></i></div>
        <?php else: ?>
        	<div><?php echo $this->translate('%s', $this->gateway_name) ?></div>
        <?php endif; ?>
        </li> 
			<!--IF PAYMENT VIA CHEQUE THEN DISPLAY CHEQUE DETAIL  -->
			<?php if( !empty($this->cheque_detail) ) : ?>
				<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Cheque No'); ?></b></div>
          <div><?php echo $this->translate('%s', $this->cheque_detail['cheque_no']) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Account Holder Name'); ?></b></div>
          <div><?php echo $this->translate('%s', $this->cheque_detail['customer_signature']) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Account Number'); ?></b></div>
          <div><?php echo $this->translate('%s', $this->cheque_detail['account_number']) ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Bank Routing Number'); ?></b></div>
          <div><?php echo $this->translate('%s', $this->cheque_detail['bank_routing_number']) ?></div>
        </li> 
			<?php  endif; ?>

				<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Type'); ?></b></div>
          <div><?php echo $this->translate(ucfirst($this->transaction_obj->type)) ?></div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment State'); ?></b></div>
          <div><?php echo $this->translate(ucfirst($this->transaction_obj->state)) ?></div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Amount'); ?></b></div>
          <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->transaction_obj->amount) ?></div>
        </li> 
  			<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Gateway Transaction ID'); ?></b></div>
          <div><?php if( !empty($this->transaction_obj->gateway_transaction_id) && $this->transaction_obj->gateway_id != 3):
               echo $this->htmlLink(array(
                        'route' => 'admin_default',
                        'module' => 'sitestoreproduct',
                        'controller' => 'payment',
                        'action' => 'detail-transaction',
                        'transaction_id' => $this->transaction_obj->transaction_id,
                        ), $this->transaction_obj->gateway_transaction_id, array(
                          'target' => '_blank',
                     )) ;
                    elseif( !empty($this->transaction_obj->gateway_transaction_id) && $this->transaction_obj->gateway_id == 3): 
                      echo $this->transaction_obj->gateway_transaction_id;
                    else:
                      echo '-'; 
                    endif;
                ?>
          </div>
        </li> 
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Date'); ?></b></div>
          <div><?php echo gmdate('M d,Y, g:i A',strtotime($this->transaction_obj->date)) ?></div>
        </li> 
			</ul>
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
</div>