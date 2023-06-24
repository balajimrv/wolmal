<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process-payment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/admin/style_sitestoreproduct.css'); ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'payment'), $this->translate("Back to Payment Requests from Sellers"), array('class' => 'seaocore_icon_back buttonlink mbot10')) ?>

<?php 
if( !empty($this->sitestoreproduct_payment_req_delete) ) :
  echo '<div class="tip">
          <span>
            ' . $this->sitestore->getTitle() . ' store has cancelled this payment request.
          </span>
        </div>';
  return;
endif;

if( !empty($this->gateway_disable) ) :
  echo '<div class="tip">
          <span>
            ' . $this->sitestore->getTitle() . ' store has currently disabled its payment gateway, so you can not approve the payment request currently.
          </span>
        </div>';
else : 
  if( empty($this->payment_req_obj->request_status) ):
    $request_status = 'Requested';
  else:
    echo '<div class="tip">
          <span>
             This payment request has been already processed for approval.
          </span>
        </div>';
    return;
  endif;
  echo $this->htmlLink($this->url(array('action' => 'store', 'store_id' => $this->payment_req_obj->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'payment-to-me' ), 'sitestore_store_dashboard', true), $this->translate("store payment details"), array('target' => '_blank', 'class' => 'buttonlink sitestoreproduct_icon_payment'));
?>
 <div>
 	<div class="invoice_order_details_wrap mtop10" style="border-width:1px;">
      <ul class="payment_transaction_details">
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Store Owner'); ?> </b> </div>
            <div>: &nbsp;<?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('target' => "_blank")) ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Store Name'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('target' => "_blank")) ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Status'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->translate($request_status); ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Requested Amount'); ?></b> </div>
            <div>: &nbsp;<?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->payment_req_obj->request_amount); ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Request Date'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->payment_req_obj->request_date ?> </div>
          </li>
        <?php if( !empty($this->payment_req_obj->request_message) ): ?>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Request Message'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->payment_req_obj->request_message; ?> </div>
          </li>
        <?php endif; ?>
        </ul>
    </div>
   <div class="settings mtop10"> 
  <?php echo $this->form->render($this); ?>
  </div>
<?php endif; ?>
 </div>
