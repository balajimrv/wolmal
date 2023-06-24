<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-invoice.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php
$isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
?> 
<?php if( !empty($this->sitestoreproduct_print_invoice_no_permission) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("You don't have permission to print the invoice of this order.") ?>
    </span>
  </div>
<?php return; endif; ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css'); ?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css'?>" type="text/css" rel="stylesheet" media="print">

	<div class="invoice_wrap">
    <div class="invoice_head_wrap">
      <div class="invoice_head">
        <div class="logo fleft">
        	<b><?php echo ($this->logo) ? $this->htmlImage($this->logo) : $this->site_title; ?></b>
        </div>
        <div class="name fright">
        	<strong><?php echo $this->translate('INVOICE') ?></strong>
        </div>
      </div>
    </div>

 		<div class="invoice_details_wrap"> <!--Address Details outer div-->
    	<div class="invoice_add_details_wrap fleft">
      	<div class="invoice_add_details"> <!--Store Address-->
        	<b><?php echo $this->translate("Store Name & Address") ?></b><br/>
          <?php echo $this->storeAddress; ?>
        </div>
        <div class="invoice_add_details"> <!--Bill Address-->
        <?php
        	echo '<b>'.$this->translate("Name & Billing Address") . '</b><br />';
          echo $this->billing_address->f_name, $this->billing_address->l_name . '<br />';
          echo $this->billing_address->address . '<br />';
          echo @strtoupper($this->billing_address->city) . ' - ' . $this->billing_address->zip . '<br />';
          echo @strtoupper($this->billing_region_name) . '<br />';
          echo @strtoupper(Zend_Locale::getTranslation($this->billing_address->country, 'country')) . '<br />';
          echo $this->translate("Ph: %s", $this->billing_address->phone) . '<br />';
        ?>
        </div>
        <?php if( !empty($this->shipping_address) ) : ?>
        <div class="invoice_add_details"> <!--Ship Address-->
        <?php
        	echo '<b>'.$this->translate("Name & Shipping Address") . '</b><br />';
          echo $this->shipping_address->f_name, $this->shipping_address->l_name . '<br />';
          echo $this->shipping_address->address . '<br />';
          echo @strtoupper($this->shipping_address->city) . ' - ' . $this->shipping_address->zip . '<br />';
          echo @strtoupper($this->shipping_region_name) . '<br />';
          echo @strtoupper(Zend_Locale::getTranslation($this->shipping_address->country, 'country')) . '<br />';
          echo $this->translate("Ph: %s", $this->shipping_address->phone);
        ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="invoice_order_details_wrap fright">
      	<ul>
          <?php if( !empty($this->orderObj->buyer_id) ): ?>
            <li>
              <div class="invoice_order_info fleft"><b><?php echo $this->translate("Name"); ?></b></div>
              <div>: &nbsp;<?php echo $this->translate("%s", $this->user_detail->displayname); ?></div>
            </li>
           <?php endif; ?>
        	<li>
            <div class="invoice_order_info fleft"><b><?php echo $this->translate("Order Id:")?></b></div> 
          	<div>: &nbsp;<?php	echo $this->translate("#%s", $this->orderObj->order_id);?></div>
          </li>
          <li>
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Status'); ?> </b> </div>
            <?php if(!empty($this->orderObj->direct_payment) && $this->orderObj->order_status == 8) : ?>
              <div>: &nbsp;-</div>
            <?php else: ?>
              <div>: &nbsp;<?php  echo $this->getOrderStatus($this->orderObj->order_status) . '<br/>';?> </div>
            <?php endif; ?>
          </li>
          <li>
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Placed on'); ?></b> </div>
            <div class="o_hidden">: &nbsp;<?php  echo $this->locale()->toDateTime($this->orderObj->creation_date) . '<br/>';?> </div>
          </li>
          <?php if( !empty($this->orderObj->shipping_title) ) : ?>
            <li>
              <div class="invoice_order_info fleft"> <b><?php echo $this->translate('Shipping Method'); ?></b> </div>
              <div>: &nbsp;<?php  echo $this->orderObj->shipping_title . '<br/>';?> </div>
            </li>
          <?php endif; ?>
          <li>
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Payment Method'); ?></b> </div>
            <div>: &nbsp;<?php  echo Engine_Api::_()->sitestoreproduct()->getGatwayName($this->orderObj->gateway_id) . '<br/>';?> </div>
          </li>
          
            <?php if( $this->orderObj->gateway_id == 3 ) : ?>
              <?php if( empty($this->directPayment) && !empty($this->site_title) && !empty($this->admin_cheque_detail) ) : ?>
                <li>
                  <b><?php echo $this->translate("%s's Bank Account Details", $this->site_title) ?></b>
                  <div><pre><?php echo $this->admin_cheque_detail ?></pre></div>
                </li>
              <?php elseif( !empty($this->directPayment) && !empty($this->storeTitle) && !empty($this->storeChequeDetail) ) :?>
                <li>
                  <b><?php echo $this->translate("%s store's Bank Account Details", $this->storeTitle) ?></b>
                  <div><pre><?php echo $this->storeChequeDetail ?></pre></div>
                </li>
              <?php endif; ?>
              <li>
                <b><?php echo $this->translate('Buyer Account Info') ?></b>
                <div class="o_hidden">
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate('Cheque No') ?></div>
                    <div>: &nbsp;<?php echo $this->cheque_info['cheque_no'] ?></div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate('Account Holder Name') ?></div>
                    <div>: &nbsp;<?php echo $this->cheque_info['customer_signature'] ?></div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate('Account Number') ?></div>
                    <div>: &nbsp;<?php echo $this->cheque_info['account_number'] ?></div>
                  </div>
                  <div class="clr">
                    <div class="invoice_order_info fleft"><?php echo $this->translate('Bank Rounting Number') ?></div>
                    <div>: &nbsp;<?php echo $this->cheque_info['bank_routing_number'] ?></div>
                  </div>
                </div>
              </li>
            <?php endif; ?>
        </ul>
      </div>
    </div>
    
    <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
    <div id="manage_order_tab">
      <div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
        <table>
          <tr class="product_detail_table_head">
            <th class="product"><?php echo $this->translate("Product"); ?></th>
            <th class="quantity txt_right"><?php echo $this->translate("Quantity"); ?></th>
            <th class="price txt_right"><?php echo $this->translate("Unit Price"); ?></th>
            <th class="total txt_right"><?php echo $this->translate("Total"); ?></th>
          </tr>
        <?php foreach( $this->order_products as $product ) : ?>
          <tr>
            <?php
            $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product->product_title);
            ?>
            <td title="<?php echo $temp_lang_title ?>">
              <?php
              echo Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40); ?>
              <?php if( !empty($product->configuration) ):
                  $configuration = Zend_Json::decode($product->configuration);
                  echo '<br/>';
                  foreach($configuration as $config_name => $config_value):
                    echo "<b>".$config_name."</b>: $config_value<br/>";
                  endforeach;
                endif; ?>
            </td>
            <td class="txt_right"><?php echo $product->quantity; ?></td>
            <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->price); ?></td>
            <td class="txt_right"><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($product->price * $product->quantity); ?></b></td>
          </tr>
        <?php endforeach; ?>  
        </table>
      </div>
    </div>
    
    <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Order Summary") ?></b>
		<div class="invoice_ttlamt_box_wrap mbot10 fright">
    	<div class="invoice_ttlamt_box fleft">
        <?php $orderCouponAmount = 0; ?>
      <?php if( !empty($this->orderObj->coupon_detail) ) : ?>
        <?php $orderCouponDetail = unserialize($this->orderObj->coupon_detail); ?>
        <?php if( !empty($orderCouponDetail) ) : ?>
          <?php $orderCouponAmount = $orderCouponDetail['coupon_amount'];?>
        <?php endif; ?>
      <?php endif; ?>
       <?php if($isSitecreditEnabled && $this->orderObj->credit_value):?>
        <div class="clr">
          <div class="invoice_order_info fleft"><?php echo $this->translate('Credits Redeemed'); ?></div>
          <div class="fright">-<?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->credit_value) . '<br/>';?></div>
        </div>
      <?php endif; ?>
        <div class="clr">
          <div class="invoice_order_info fleft"><?php echo $this->translate('Subtotal'); ?></div>
          <div class="fright"><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->sub_total + $orderCouponAmount)) . '<br/>';?></div>
        </div>
        <?php if( !empty($orderCouponDetail) ) : ?>
        <div>
          <div  class="clr">
            <div class="fleft"><strong><?php echo $orderCouponDetail['coupon_code']; ?>&nbsp;&nbsp;</strong></div>
            <div class="fright"><strong><?php echo '-'.Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($orderCouponDetail['coupon_amount']);?></strong></div>
          </div>
        </div>
        <div>
          <div  class="clr">
            <div class="fleft"><strong><?php echo $this->translate("Total"); ?>&nbsp;&nbsp;</strong></div>
            <div class="fright"><strong><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->sub_total);?></strong></div>
          </div>
        </div>
      <?php endif; ?>
        <div class="clr">  
          <div class="invoice_order_info fleft"><?php echo $this->translate('Taxes'); ?></div>
          <div class="fright"><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->store_tax + $this->orderObj->admin_tax)) . '<br/>';?> </div>
        </div>
        <div class="clr">
          <div class="invoice_order_info fleft"><?php echo $this->translate('Shipping price'); ?></div>
          <div class="fright"><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->shipping_price) . '<br/>';?></div>
        </div>
        
      </div>
      <div>
        <div  class="clr">
          <div class="fleft"><strong><?php echo $this->translate('Grand Total'); ?>&nbsp;&nbsp;</strong></div>
          <div class="fright"><strong><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->grand_total);?></strong></div>
        </div>
      </div>
    </div>
     
    <?php if(!empty($this->orderObj->order_note)): ?>
      <div class="fleft">
        <div class="invoice_note_box mbot10 clr">
        	<div style="margin-bottom:2px;"><b><?php echo $this->translate("Order Note:").' '; ?></b></div>
          <?php echo Engine_Api::_()->sitestoreproduct()->truncation($this->orderObj->order_note, 310); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php $profileFields = $this->billFieldValueLoop($this->sitestore, $this->fieldStructure) ?>
    <?php if (!empty($profileFields)) : ?>
      <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Profile Information") ?></b>
      <?php echo '<div class="seao_listings_stats"><div class="o_hidden f_small">' . $profileFields . '</div></div>'; ?>
    <?php endif; ?>
	</div>

<script type="text/javascript">
	window.print();
</script>