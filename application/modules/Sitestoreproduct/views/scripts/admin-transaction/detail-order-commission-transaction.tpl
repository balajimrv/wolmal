<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-order-commission-transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Store Name'); ?></b></div>
          <div>
            <?php if(empty($this->sitestore)) : ?>
            <i><?php echo 'Store Deleted'; ?></i>
            <?php else: ?>
              <?php echo $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('title' => $this->sitestore->getTitle(), 'target' => '_blank')) ?>
            <?php endif; ?>
          </div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Owner Name'); ?></b></div>
          <div>
            <?php if(empty($this->sitestore)) : ?>
              <?php echo '-'; ?>
            <?php else: ?>
              <?php echo $this->htmlLink($this->sitestore->getOwner()->getHref(), $this->sitestore->getOwner()->getTitle(), array('title' => $this->sitestore->getOwner()->getTitle(), 'target' => '_blank')) ?>
            <?php endif; ?>
          </div>
        </li>
				<li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Gateway'); ?></b></div>
          <div><i><?php echo Engine_Api::_()->sitestoreproduct()->getGatwayName($this->transaction_obj->gateway_id); ?></i></div>
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
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Message'); ?></b></div>
          <div><?php echo empty($this->message) ? '-' : $this->message; ?></div>
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
          <div><?php echo gmdate('M d,Y, g:i A',strtotime($this->transaction_obj->date))  ?></div>
        </li> 
			</ul>
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
</div>