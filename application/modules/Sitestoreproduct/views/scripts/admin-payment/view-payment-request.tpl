<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-payment-request.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
 <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/admin/style_sitestoreproduct.css'); ?>

<?php 
if( $this->payment_req_obj->request_status == 0 ):
  $request_status = 'Requested';
elseif( $this->payment_req_obj->request_status == 1 ):
  $request_status = '<i><font color="red">Deleted</font></i>';
elseif( $this->payment_req_obj->request_status == 2 ):
  $request_status = '<i><font color="green">Complete</font></i>';  
endif;
?>

<div class="global_form_popup">
  <div id="manage_order_tab">
    <div class="invoice_order_details_wrap" style="border-width:1px;">
      <ul>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Id'); ?></b></div>
          <div><?php echo $this->request_id; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Store Name'); ?></b></div>
          <div><?php echo $this->htmlLink($this->sitestore->getHref(), $this->sitestore->getTitle(), array('target' => '_blank')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Owner Name'); ?></b></div>
          <div><?php echo $this->htmlLink($this->user_obj->getHref(), $this->user_obj->getTitle(), array('target' => '_blank')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Amount'); ?></b></div>
          <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->payment_req_obj->request_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Message'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->request_message) ? '-' : $this->payment_req_obj->request_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Date'); ?></b></div>
          <div><?php echo $this->payment_req_obj->request_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Response Amount'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->payment_req_obj->response_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Response Message'); ?></b></div>
          <div><?php echo empty( $this->payment_req_obj->response_message ) ? '-' : $this->payment_req_obj->response_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Response Date'); ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : $this->payment_req_obj->response_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Remaining Amount'); ?></b></div>
          <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->payment_req_obj->remaining_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Request Status'); ?></b></div>
          <div><?php echo $this->translate($request_status); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payment Status'); ?></b></div>
          <div><?php echo $this->translate(ucfirst($this->payment_req_obj->payment_status)); ?></div>
        </li>
      </ul>
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>
