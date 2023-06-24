<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-shipment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php 
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
?>

<div class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Shipping Details'); ?></h3>
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;">
      <ul class="payment_transaction_details">
          <li>
            <b><?php echo $this->translate("Shipping Details Id %s", $this->shipping_tracking_obj->shippingtracking_id); ?></b>
          </li>
          <li>
            <b><?php echo $this->translate("Order Id #%s", $this->shipping_tracking_obj->order_id); ?></b>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Service'); ?> </b> </div>
            <div>: &nbsp;<?php echo $this->shipping_tracking_obj->service ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Title'); ?></b> </div>
            <div>: &nbsp;<?php echo empty($this->shipping_tracking_obj->title) ? '-' : $this->shipping_tracking_obj->title; ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Tracking Number'); ?></b> </div>
            <div>: &nbsp;<?php echo $this->shipping_tracking_obj->tracking_num ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Status'); ?></b> </div>
            <div>: &nbsp;<?php
              if($this->shipping_tracking_obj->status == 1):
                $status = 'Active';
              elseif($this->shipping_tracking_obj->status == 2):
                $status = 'Completed';
              elseif($this->shipping_tracking_obj->status == 2):
                $status = 'Canceled';
              endif; 
              echo $this->translate("%s", $status) ?> 
            </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Note'); ?></b> </div>
            <div>: &nbsp;<?php echo empty($this->shipping_tracking_obj->note) ? '-' : $this->shipping_tracking_obj->note; ?> </div>
          </li>
          <li>
            <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Shipping Details Date'); ?></b> </div>
            <div>: &nbsp;<?php echo gmdate('M d,Y, g:i A',strtotime($this->shipping_tracking_obj->creation_date)); ?> </div>
          </li>
        </ul>
    </div>
  </div>
  <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>

