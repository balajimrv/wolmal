<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-packing-slip.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css'); ?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_print.css'?>" type="text/css" rel="stylesheet" media="print">

<?php if( !empty($this->sitestoreproduct_packing_slip_no_permission) ) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("You don't have permission to print the packing slip of this order.") ?>
    </span>
  </div>
<?php return; endif; ?>

<div class="invoice_wrap">
  <?php if( !empty($this->shipping_address) ) : ?>
	<div class="txt_center">
    <?php
      echo '<b>'.$this->translate("Name & Shipping Address") . '</b><br />';
      echo $this->shipping_address->f_name . ' ' . $this->shipping_address->l_name . '<br />';
      echo $this->shipping_address->address . '<br />';
      echo @strtoupper($this->shipping_address->city) . ' - ' . $this->shipping_address->zip . '<br />';
      echo @strtoupper($this->shipping_region_name) . '<br />';
      echo @strtoupper(Zend_Locale::getTranslation($this->shipping_address->country, 'country')) . '<br />';
      echo $this->translate("Ph: %s", $this->shipping_address->phone) . '<br /><br />';
    ?>
  </div>
  <?php endif; ?>
  <div class="sitestoreproduct_slip_cutter mtop10 mbot10" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/scissors.png) no-repeat scroll 40px -12px;"></div><br>
    <div class="invoice_details_wrap"> <!--Address Details outer div-->
    	<div class="invoice_add_details_wrap fleft">
      	<div class="invoice_add_details"> <!--Store Address-->
        	<b><?php echo $this->translate("Store Name & Address") ?></b><br/>
          <?php echo $this->storeAddress; ?>
        </div>
        <div class="invoice_add_details"> <!--Bill Address-->
          <?php
            echo '<b>'.$this->translate("Name & Billing Address") . '</b><br />';
            echo $this->billing_address->f_name . ' ' . $this->billing_address->l_name . '<br />';
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
            echo $this->shipping_address->f_name . ' ' . $this->shipping_address->l_name . '<br />';
            echo $this->shipping_address->address . '<br />';
            echo @strtoupper($this->shipping_address->city) . ' - ' . $this->shipping_address->zip . '<br />';
            echo @strtoupper($this->shipping_region_name) . '<br />';
            echo @strtoupper(Zend_Locale::getTranslation($this->shipping_address->country, 'country')) . '<br />';
            echo $this->translate("Ph: %s", $this->shipping_address->phone) . '<br />';
          ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="invoice_order_details_wrap fright">
      	<ul>
        	<li>
          	<div class="txt_center"><b><?php echo $this->translate("Packing slip for order id: #%s", $this->orderObj->order_id); ?> </b> </div>
          </li>
          <li>
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Date'); ?> </b> </div>
            <div>: &nbsp;<?php  echo $this->locale()->toDateTime($this->orderObj->creation_date) . '<br/>';?> </div>
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
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Payment Method'); ?></b> </div>
            <div>: &nbsp;<?php  echo $this->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($this->orderObj->gateway_id)) . '<br/>';?> </div>
          </li>
          <li>
          	<div class="invoice_order_info fleft"> <b><?php echo $this->translate('Shipping Method'); ?></b> </div>
            <div>: &nbsp;<?php  echo $this->orderObj->shipping_title . '<br/>';?> </div>
          </li>
        </ul>
      </div>
    </div>
    
    <b class="dblock clr mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
    <div id="manage_order_tab">
      <div class="product_detail_table sitestoreproduct_data_table fleft mbot10" style="width:100%;">
        <table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate("Product"); ?></th>
            <th><?php echo $this->translate("Product SKU"); ?></th>
            <th class="txt_right"><?php echo $this->translate("Quantity"); ?></th>
          </tr>
        <?php foreach( $this->order_products as $product ) : ?>
          <tr>
            <?php
            $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($product->product_title);
            ?>
            <td title="<?php echo $temp_lang_title ?>">
              <?php echo Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40); ?>
              <?php if( !empty($product->configuration) ):
                      $configuration = Zend_Json::decode($product->configuration);
                      echo '<br/>';
                      foreach($configuration as $config_name => $config_value):
                        echo "<b>".$config_name."</b>: $config_value<br/>";
                      endforeach;
                    endif; ?>
            </td>
            <td><?php echo empty($product->product_sku) ? '-' : $product->product_sku; ?></td>
            <td class="txt_right"><?php echo $product->quantity; ?></td>
          </tr>
        <?php endforeach; ?>  
        </table>
      </div>
    </div>
	</div>
</div>

<script type="text/javascript">
window.print();
</script>