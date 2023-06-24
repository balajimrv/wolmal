<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php
$isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
?>   
<?php if( !empty($this->sitestoreproduct_view_no_permission) ) : ?>
<div class="tip">
  <span>
    <?php echo $this->translate("Order not available for view or you are not permitted to view the order.") ?>
  </span>
</div>
<?php return; endif; ?>

<script type="text/javascript">
function commentToggle(comment_class)
{
  var myVerticalSlide = new Fx.Slide(comment_class, {mode : 'vertical', resetHeight : true});
  myVerticalSlide.toggle();
}

var CommentLikesTooltips;
var show_tool_tip=false;
var counter_req_pendding=0;
  
en4.core.runonce.add(function() {
{
  $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {
    var el = $(event.target); 
    ItemTooltips.options.offset.y = el.offsetHeight;
    ItemTooltips.options.showDelay = 0;
      if(!el.hasAttribute("rel")){
                el=el.parentNode;      
         } 
     show_tool_tip=true;
    if( !el.retrieve('tip-loaded', false) ) {
     counter_req_pendding++;
     var resource='';
    if(el.hasAttribute("rel"))
       resource=el.rel;
     if(resource =='')
       return;

      el.store('tip-loaded', true);
      el.store('tip:title', '<div class="" style="">'+
' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">'+
  '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">'+
'<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>'+
'</div></div></div></div>'  
);
      el.store('tip:text', '');       
      // Load the likes
      var url = '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'show-tooltip-info', 'show_tax_type' => $this->show_tax_type), 'default', true) ?>';
      el.addEvent('mouseleave',function(){
       show_tool_tip=false;  
      });       

      var req = new Request.HTML({
        url : url,
        data : {
        format : 'html',
        'resource':resource,
        tax : this.rel
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {          
          el.store('tip:title', '');
          el.store('tip:text', responseHTML);
          ItemTooltips.options.showDelay=0;
          ItemTooltips.elementEnter(event, el); // Force it to update the text 
           counter_req_pendding--;
            if(!show_tool_tip || counter_req_pendding>0){               
            //ItemTooltips.hide(el);
            ItemTooltips.elementLeave(event,el);
           }           
          var tipEl=ItemTooltips.toElement();
          tipEl.addEvents({
            'mouseenter': function() {
             ItemTooltips.options.canHide = false;
             ItemTooltips.show(el);
            },
            'mouseleave': function() {                
            ItemTooltips.options.canHide = true;
            ItemTooltips.hide(el);                    
            }
          });
          Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
        }
      });
      req.send();
    }
  });
  // Add tooltips
  var window_size = window.getSize()
  var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      offset : {'x' : 0,'y' : 0},
      windowPadding: {'x':370, 'y':(window_size.y/2)}
    }); 
  }
});
</script>

<?php if( !empty($this->callingStatus) ): ?>
  <div id='manage_order_menue'>
    <div class="clr">
      <a href="javascript:void(0);" onclick = 'manage_store_dashboard(55, "manage-order", "index");' id="sitestoreproduct_menu_1" class="buttonlink icon_previous"><?php echo $this->translate('Back to My Orders') ?></a>
    </div>
    
<?php if( $this->orderObj->payment_status == 'active' && $this->orderObj->order_status != 6 && $this->orderObj->order_status != 8 ) : ?>
    <div class="tabs">
      <ul class="navigation sr_sitestoreproduct_navigation_common">
        <li class="active">
          <a href="javascript:void(0);" onclick = "manage_store_dashboard(55, 'order-view/order_id/<?php echo $this->order_id; ?>', 'index')" id="sitestoreproduct_menu_1"><?php echo $this->translate('View') ?></a> &nbsp;&nbsp;&nbsp;
        </li>
        <?php if( !empty($this->anyOtherProductTypes) && empty($this->bundleProductShipping) ) : ?>
          <li>
            <a href="javascript:void(0);" onclick = "manage_store_dashboard(55, 'order-ship/order_id/<?php echo $this->order_id; ?>', 'index')" id="sitestoreproduct_menu_1"><?php echo $this->translate('Shipping Details') ?></a> &nbsp;&nbsp;&nbsp;
          </li>
          <li>
            <a href="sitestoreproduct/index/print-packing-slip/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id);?>" target="_blank"><?php echo $this->translate('Print Packing Slip') ?></a> &nbsp;&nbsp;&nbsp;
          </li>
        <?php endif; ?>
        <li>
          <a href="sitestoreproduct/index/print-invoice/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id);?>" target="_blank"><?php echo $this->translate('Print Invoice') ?></a> &nbsp;&nbsp;&nbsp;
        </li>
      </ul>
    </div>
<?php endif; ?>
    <br/>
  </div>
<?php else: ?>
  <!-- CALLING FOR BUYER -->
  <div id='manage_order_menue'>
    <div class="clr">
      <?php $tempUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'my-order'), 'default', true); ?>
      <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', '', <?php echo $this->order_id; ?>, '<?php echo $tempUrl; ?>');" id="sitestoreproduct_menu_1" class="buttonlink icon_previous mbot5"><?php echo $this->translate('Back to My Orders') ?></a>
    <?php if( $this->orderObj->gateway_id == 3 && !empty($this->admin_cheque_detail) && empty($this->orderObj->direct_payment) ) : ?>&nbsp;
      <?php $chequeDetailUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'admin-cheque-detail'), 'default', true); ?>
        <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $chequeDetailUrl ?>')" class="sitestoreproduct_icon_cheque buttonlink"><?php echo $this->translate("%s's Bank Account Details", $this->site_title) ?></a>
    <?php elseif( !empty($this->orderObj->direct_payment) && $this->orderObj->gateway_id == 3 ) : ?>
      <?php $storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $this->orderObj->store_id, "title = 'ByCheque'", "enabled = 1")); ?>
      <?php if( !empty($storeChequeDetail) ) : ?>&nbsp;
        <?php $chequeDetailUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'store-cheque-detail', 'store_id' => $this->orderObj->store_id, 'title' => $this->storeTitle), 'default', true); ?>
        <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $chequeDetailUrl ?>')" class="sitestoreproduct_icon_cheque buttonlink"><?php echo $this->translate("%s store's Bank Account Details", $this->storeTitle) ?></a>
      <?php endif; ?>
    <?php endif; ?>
    </div><br />

    <?php 
      $tempViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $this->order_id, 'page_viewer' => $this->page_user) , 'sitestoreproduct_general', true); 
      $tempShipmentUrl = $this->url(array('action' => 'order-ship', 'order_id' => $this->order_id, 'page_viewer' => $this->page_user) , 'sitestoreproduct_general', true);
      $tempReorder = $this->url(array('action' => 'cart', 'reorder' => 1, 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id)) , 'sitestoreproduct_product_general', true);
      $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id)) , 'sitestoreproduct_general', true);
    ?>
    
    <?php if( $this->orderObj->order_status != 6 ) : ?>
      <div class="tabs">
        <ul class="navigation sr_sitestoreproduct_navigation_common">
          <li id="buyer_account_view_order" class="active">
            <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', 'order-view', <?php echo $this->order_id; ?>, '<?php echo $tempViewUrl; ?>');" id="sitestoreproduct_menu_1"><?php echo $this->translate('View') ?></a>
          </li>
          <?php if( !empty($this->anyOtherProductTypes) && empty($this->bundleProductShipping) ) : ?>
            <li id="buyer_account_shipment">
              <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', 'order-shipment', <?php echo $this->order_id; ?>, '<?php echo $tempShipmentUrl; ?>');" id="sitestoreproduct_menu_1"><?php echo $this->translate('Shipping Details') ?></a>
            </li>
          <?php endif; ?>
          <?php $anyOtherProducts = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->checkProductType(array('order_id' => $this->order_id, 'virtual' => true)); ?>
          <?php if( !empty($this->isStoreExist) && !empty($anyOtherProducts) ) : ?>
            <li id="buyer_account_reorder">
              <a href="<?php echo $tempReorder; ?>" id="sitestoreproduct_menu_1"><?php echo $this->translate('Reorder') ?></a>
            </li>
          <?php endif; ?>
          <?php if( $this->orderObj->payment_status == 'active' ) : ?>
            <li id="buyer_account_print_invoice">
              <a href="<?php echo $tempInvoice; ?>" target="_blank" id="sitestoreproduct_menu_1"><?php echo $this->translate('Print Invoice') ?></a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    <?php endif;?>
  </div><br />
<?php endif; ?>
  
  
<div class="clr">
  <div class="fleft"><h2><?php echo $this->translate('Order Id: #%s', $this->order_id) ?></h2></div>
  <div class="fright"><h2><?php echo $this->translate(' [ Total: %s ]', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->grand_total)) ?></h2></div>
</div>

<div class="invoice_details_wrap clr">
  
<!-- Billing Information -->
  <div class="invoice_add_details_wrap fleft">
    <div class="invoice_add_details">
      <b><?php echo $this->translate('Name & Billing Address') ?></b>
      <ul> 
        <li>
          <div><?php echo $this->billing_address->f_name . ' ' . $this->billing_address->l_name;; ?></div>
        </li>
        <li>
          <div><?php echo $this->billing_address->address; ?></div>
        </li>
        <li>
          <div><?php echo @strtoupper($this->billing_address->city) . ' - ' . $this->billing_address->zip ; ?></div>
        </li>
        <li>
          <div><?php echo @strtoupper($this->billing_region_name); ?></div>
        </li>
        <li>
          <div><?php echo @strtoupper(Zend_Locale::getTranslation($this->billing_address->country, 'country')); ?></div>
        </li>
        <li>
          <div><?php echo $this->translate("Ph: %s", $this->billing_address->phone); ?></div>
        </li>
      </ul>
    </div>

<!-- Shipping Information -->
    <?php if( !empty($this->shipping_address) ) : ?>
      <div class="invoice_add_details">
        <b><?php echo $this->translate('Name & Shipping Address') ?></b>   
        <ul>
          <li>
            <div><?php echo $this->shipping_address->f_name . ' ' . $this->shipping_address->l_name;; ?></div>
          </li>
          <li>
            <div><?php echo $this->shipping_address->address; ?></div>
          </li>
          <li>
            <div><?php echo @strtoupper($this->shipping_address->city) . ' - ' . $this->shipping_address->zip ; ?></div>
          </li>
          <?php if( !empty($this->shipping_region_name) ) : ?>
          <li>
            <div><?php echo @strtoupper($this->shipping_region_name); ?></div>
          </li>
          <?php endif; ?>
          <li>
            <div><?php echo @strtoupper(Zend_Locale::getTranslation($this->shipping_address->country, 'country')); ?></div>
          </li>
          <li>
            <div><?php echo $this->translate("Ph: %s", $this->shipping_address->phone); ?></div>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>

<div class="invoice_add_details_wrap fright">
<!-- Order Information -->
			<div class="invoice_add_details">
      <b><?php echo $this->translate('Order Information') ?></b>
      <ul class="o_hidden">
        <li>
        	<div class="invoice_order_info fleft"><?php echo $this->translate('Order Date') ?></div>
          <div><?php echo $this->locale()->toDateTime($this->orderObj->creation_date); ?></div>
        </li>
        <li>
        	<div class="invoice_order_info fleft"><?php echo $this->translate('Order Status') ?></div>
          <?php if(!empty($this->orderObj->direct_payment) && $this->orderObj->order_status == 8) : ?>
            <div>-</div>
          <?php else: ?>
            <div><?php echo $this->getOrderStatus($this->orderObj->order_status); ?></div>
          <?php endif; ?>
        </li>
        <?php if (empty($this->page_user)) : ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Commission Type') ?></div>
            <div><?php echo empty($this->orderObj->commission_type) ? $this->translate('Fixed') : $this->translate('Percentage'); ?></div>
          </li>
          <?php if (!empty($this->orderObj->commission_type)) : ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Commission Rate') ?></div>
            <div><?php echo number_format($this->orderObj->commission_rate, 2).' %'; ?></div>
          </li>
          <?php endif; ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Commission Amount') ?></div>
            <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->commission_value); ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Store Tax Amount') ?></div>
            <div colspan='2'><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->store_tax); ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Admin Tax Amount') ?></div>
            <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->admin_tax); ?></div>
          </li>
        <?php else : ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Tax Amount') ?></div>
            <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->store_tax + $this->orderObj->admin_tax)); ?></div>
          </li>
        <?php endif; ?>
        <?php if( !empty($this->orderObj->shipping_title) ) : ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Shipping Amount') ?></div>
            <div><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->shipping_price); ?></div>
          </li>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Delivery Time') ?></div>
            <div><?php echo empty($this->orderObj->delivery_time) || $this->orderObj->order_status < 2 ? '-' : $this->orderObj->delivery_time; ?></div>
          </li>
        <?php endif; ?>
        <?php if (empty($this->page_user)) : ?>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('User Type') ?></div>
            <div>
              <?php echo empty($this->orderObj->buyer_id) ? $this->translate('Guest') : $this->translate('Site Member'); ?>
            </div>
          </li>
        <?php endif; ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.ipaddress', 1) && !empty($this->orderObj->ip_address)) : ?>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('IP Address') ?></div>
          <div><?php $ipObj = new Engine_IP($this->orderObj->ip_address); echo $ipObj->toString();?></div>
        </li>
       <?php endif; ?>
      </ul>
    	</div>
    
<!-- Payment Information -->
    <div class="invoice_add_details">
      <b><?php echo $this->translate('Payment Information') ?></b>
      <ul>
        <?php if(!empty($this->orderObj->direct_payment)) : ?>
          <?php if( $this->orderObj->order_status == 8 ) : ?>
            <li class="o_hidden">
              <div class="invoice_order_info fleft seaocore_txt_red"><?php echo $this->translate('marked as non-payment') ?></div>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Payment Method') ?></div>
          <div><?php echo $this->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($this->orderObj->gateway_id)); ?></div>
        </li>
        <?php if(!empty($this->orderObj->direct_payment)) : ?>
          <?php if( !empty($this->orderObj->non_payment_seller_reason) ) : ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate("Non-Payment Reason") ?></div>
              <?php if( $this->orderObj->non_payment_seller_reason == 1 ) : ?>
                <div><?php echo $this->translate("Chargeback") ?></div>
              <?php elseif( $this->orderObj->non_payment_seller_reason == 2 ) : ?>
                <div><?php echo $this->translate("Payment not received") ?></div>
              <?php elseif( $this->orderObj->non_payment_seller_reason == 3 ) : ?>
                <div><?php echo $this->translate("Canceled payment") ?></div>
              <?php endif; ?>
            </li>
          <?php endif; ?>
          <?php if( !empty($this->orderObj->non_payment_seller_message) ) : ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate("Seller Message") ?></div>
              <div class="o_hidden"><?php echo $this->orderObj->non_payment_seller_message ?></div>
            </li>
          <?php endif; ?>
          <?php if( !empty($this->orderObj->non_payment_admin_reason) ) : ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate("Non-Payment Action") ?></div>
              <?php if( $this->orderObj->non_payment_admin_reason == 1 ) : ?>
                <div><?php echo $this->translate("Approved") ?></div>
              <?php elseif( $this->orderObj->non_payment_admin_reason == 2 ) : ?>
                <div><?php echo $this->translate("Declined") ?></div>
              <?php elseif( $this->orderObj->non_payment_admin_reason == 3 ) : ?>
                <div><?php echo $this->translate("Hold") ?></div>
              <?php endif; ?>
            </li>
          <?php endif; ?>
          <?php if( !empty($this->orderObj->non_payment_admin_message) ) : ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate("Site Administrator Message") ?></div>
              <div class="o_hidden"><?php echo $this->orderObj->non_payment_admin_message ?></div>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if($this->orderObj->gateway_id == 3): ?>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Cheque No') ?></div>
          <div><?php echo $this->cheque_info['cheque_no'] ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Account Holder Name') ?></div>
          <div><?php echo $this->cheque_info['customer_signature'] ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Account Number') ?></div>
          <div><?php echo $this->cheque_info['account_number'] ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><?php echo $this->translate('Bank Rounting Number') ?></div>
          <div><?php echo $this->cheque_info['bank_routing_number'] ?></div>
        </li>
        <?php endif; ?>
      </ul>
    </div>

    <?php if( !empty($this->isDownPaymentEnable) && !empty($this->remainingAmountPayment) ) : ?>
      <div class="invoice_add_details">
        <b><?php echo $this->translate('Remaining Amount Payment Information') ?></b>
        <ul>
          <li>
            <?php if( !empty($this->remainingAmountGatewayId) ) : ?>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Payment Method') ?></div>
              <div><?php echo $this->translate("%s", Engine_Api::_()->sitestoreproduct()->getGatwayName($this->remainingAmountGatewayId)); ?></div>
            <?php endif; ?>
          </li>
          <?php if($this->remainingAmountGatewayId == 3): ?>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Cheque No') ?></div>
              <div><?php echo $this->remaining_amount_cheque_info['cheque_no'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Account Holder Name') ?></div>
              <div><?php echo $this->remaining_amount_cheque_info['customer_signature'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Account Number') ?></div>
              <div><?php echo $this->remaining_amount_cheque_info['account_number'] ?></div>
            </li>
            <li>
              <div class="invoice_order_info fleft"><?php echo $this->translate('Bank Rounting Number') ?></div>
              <div><?php echo $this->remaining_amount_cheque_info['bank_routing_number'] ?></div>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if( !empty($this->orderObj->shipping_title) ) : ?>
      <div class="invoice_add_details">
        <b><?php echo $this->translate('Shipping Information') ?></b>
        <ul>
          <li>
            <div class="invoice_order_info fleft"><?php echo $this->translate('Shipping Method') ?></div>
            <div><?php echo $this->orderObj->shipping_title; ?></div>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>

</div>

<b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Details") ?></b>
<div id="manage_order_tab">
  <div class="product_detail_table sitestoreproduct_data_table fleft">
    <table>
      <tr class="product_detail_table_head">
        <th><?php echo $this->translate("Product"); ?></th>
        <th><?php echo $this->translate('SKU') ?></th>
        <th class="txt_right"><?php echo $this->translate('Price') ?></th>
        <?php if( !empty($this->isDownPaymentEnable) && !empty($this->orderObj->is_downpayment) ) : ?>
          <th class="txt_right"><?php echo $this->translate('Downpayment') ?></th>
        <?php endif; ?>
        <th class="txt_center"><?php echo $this->translate("Quantity"); ?></th>
        <th class="txt_right"><?php echo $this->translate('Tax Amount') ?></th>
        <?php if( !empty($this->isDownPaymentEnable) && !empty($this->orderObj->is_downpayment) ) : ?>
          <th class="txt_right"><?php echo $this->translate('Downpayment Total') ?></th>
          <th class="txt_right"><?php echo $this->translate('Remaining Amount Total') ?></th>
        <?php endif; ?>
        <th class="txt_right"><?php echo $this->translate('Subtotal') ?></th>
      </tr>
      <?php foreach( $this->orderProducts as $item ): ?>
        <?php if( !empty($item->order_product_info) ) : ?>
          <?php $order_product_info = unserialize($item->order_product_info); ?>
        <?php endif; ?>
        <tr>
          <?php
              $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($item->product_title);
            ?>
          <td title="<?php echo $temp_lang_title ?>">
            <?php echo Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40) ?>
            <?php if( !empty($order_product_info) && !empty($order_product_info['calendarDate']) && !empty($order_product_info['calendarDate']['starttime']) && !empty($order_product_info['calendarDate']['endtime']) ) : ?>
              <?php echo '</br><b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($order_product_info['calendarDate']['starttime']) . '</br>'; ?>
              <?php echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($order_product_info['calendarDate']['endtime']); ?>
            <?php endif; ?>
            <?php if( !empty($item->configuration) ):
                    $configuration = Zend_Json::decode($item->configuration);
                    echo '<br/>';
                    foreach($configuration as $config_name => $config_value):
                      echo "<b>".$config_name."</b>: $config_value<br/>";
                    endforeach;
                  endif; ?>
          </td>
          <td><?php echo empty($item->product_sku) ? '-' : $item->product_sku; ?></td>
          <td class="txt_right">
            <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->price); ?>
            <?php if( !empty($order_product_info) && !empty($order_product_info['price_range_text']) ) : ?>
              <?php echo $this->translate($order_product_info['price_range_text']) ?>
            <?php endif; ?>
          </td>
          <?php if( !empty($this->isDownPaymentEnable) && !empty($this->orderObj->is_downpayment) ) : ?>
            <td class="txt_right">
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->downpayment)); ?>
            </td>
          <?php endif; ?>
          <td class="txt_center"><?php echo $item->quantity; ?></td>
          <td class="txt_right">
            <?php 
              if( empty($item->tax_amount) ):
                echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->tax_amount));
              else:
                $taxTitle = unserialize($item->tax_title);
                if( !is_array($taxTitle) ) :
                  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->tax_amount));
                else:
                  echo $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->tax_amount), array('class'=>'sea_add_tooltip_link', 'rel'=>$item->tax_title));        
                endif;
              endif;
            ?>
          </td>
          <?php if( !empty($this->isDownPaymentEnable) && !empty($this->orderObj->is_downpayment) ) : ?>
            <td class="txt_right">
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->downpayment * $item->quantity)); ?>
            </td>
            <td class="txt_right">
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency((($item->price * $item->quantity) - ($item->downpayment * $item->quantity))); ?>
            </td>
          <?php endif; ?>
          <td class="txt_right"><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->price * $item->quantity)); ?></b></td>
        </tr>
      <?php endforeach; ?>  
    </table>
  </div>
</div>

<b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Order Summary") ?></b>
<div class="clr o_hidden">
  <div class="invoice_ttlamt_box_wrap fright mbot10">
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
        <div class="invoice_order_info fleft"><?php echo $this->translate('Shipping cost'); ?></div>
        <div class="fright"><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->shipping_price) . '<br/>';?></div>
      </div>
      <div class="clr">  
        <div class="invoice_order_info fleft"><?php echo $this->translate('Taxes'); ?></div>
        <div class="fright"><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->admin_tax + $this->orderObj->store_tax)) . '<br/>';?> </div>
      </div>
    </div>
    <?php if( !empty($this->isDownPaymentEnable) && !empty($this->orderObj->is_downpayment) ) : ?>
      <div>
        <div  class="clr">
          <div class="fleft"><strong><?php echo $this->translate('Downpayment Grand Total'); ?>&nbsp;&nbsp;</strong></div>
          <div class="fright"><strong><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->downpayment_total);?></strong></div>
        </div>
      </div>
      <div>
        <div  class="clr">
          <div class="fleft"><strong><?php echo $this->translate('Remaining Amount Grand Total'); ?>&nbsp;&nbsp;</strong></div>
          <?php $remainingAmount = $this->orderObj->grand_total - ($this->orderObj->downpayment_total + $this->orderObj->store_tax + $this->orderObj->admin_tax + $this->orderObj->shipping_price); ?>
          <div class="fright"><strong><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingAmount);?></strong></div>
        </div>
      </div>
    <?php endif; ?>
    <div>
      <div  class="clr">
        <div class="fleft"><strong><?php echo $this->translate('Grand Total'); ?>&nbsp;&nbsp;</strong></div>
        <div class="fright"><strong><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->grand_total);?></strong></div>
      </div>
    </div>
  </div>
  
  <?php if(!empty($this->orderObj->order_note)): ?>
    <div class="fleft">
      <div class="invoice_note_box clr">
        <div style="margin-bottom:2px;"><b>order note: </b></div>
        <?php echo Engine_Api::_()->sitestoreproduct()->truncation($this->orderObj->order_note, 310); ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php $profileFields  = $this->billFieldValueLoop($this->sitestore, $this->fieldStructure) ?>
<?php if(!empty($profileFields)) : ?>
<b class="clr dblock mtop10 mbot5"><?php echo $this->translate("Profile Information") ?></b>
<?php echo '<div class="seao_listings_stats"><div class="o_hidden f_small">' . $profileFields . '</div></div>'; ?>
<?php endif; ?>
<br/><br/>
<?php 
if( $this->orderObj->order_status != 6 ) :
  if( !empty($this->admin_calling) ):
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_siteadmin_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_buyer_comment.tpl';
  elseif( empty($this->page_user) ):
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_siteadmin_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_buyer_comment.tpl';
  else:
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_buyer_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/order-comment/_siteadmin_comment.tpl';
  endif;
endif;
?>

<script type="text/javascript">
var commented_div_id;
var comment_index = 0;

// FOR TOGGLE COMMENT BOX  
function commentBox(user_type)
{
  $("order_comment_"+user_type).toggle();
}
  
// SUBMIT THE COMMENT
<?php if( $this->orderObj->order_status != 6 ) : ?>
en4.core.runonce.add(function() {
  if($('order_comment_form_0'))
  {
    $('order_comment_form_0').removeEvent('submit').addEvent('submit', function(e)
    {
      commented_div_id = 'buyer_comment';
      e.stop();
       postComment(0);
    });
  }
  
  if($('order_comment_form_1'))
  {
    $('order_comment_form_1').removeEvent('submit').addEvent('submit', function(e)
    {
      commented_div_id = 'seller_comment';
      e.stop();
       postComment(1);
    });
  }
  
  if($('order_comment_form_2'))
  {
    $('order_comment_form_2').removeEvent('submit').addEvent('submit', function(e)
    {
      commented_div_id = 'site_admin_comment';
      e.stop();
       postComment(2);
    });
  }
});
<?php endif; ?>
function postComment(user_type)
{
  // IF COMMENT BOX IS EMPTY
  if( $('sitestoreproduct_order_comment_box_'+user_type).value.length == 0 )
    return;
  
  $('comment_loading_image_'+user_type).innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';

  en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/index/order-comment',
      method : 'POST',
      data : {
        format : 'json',
        order_comment : $('order_comment_form_'+user_type).toQueryString(),
        order_id : <?php echo $this->order_id; ?>
      },
      onSuccess : function(responseJSON) 
      {
        new Fx.Slide(commented_div_id,{mode: 'vertical', resetHeight : true}).slideIn().toggle();
        new Element('li', { 'id' : 'new_comment_'+comment_index, 'class': 'mbot10' }).inject(document.getElementById(commented_div_id).getFirst('ul'), 'top');
        new Element('div', { 'class': 'seaocore_txt_light mbot5', 'html' : responseJSON.comment_date }).inject($('new_comment_'+comment_index));;
        new Element('p', { 'class': 'pleft10', 'html' : responseJSON.comment_text }).inject($('new_comment_'+comment_index++));
        $('sitestoreproduct_order_comment_box_'+responseJSON.user_type).value = '';
        $('comment_loading_image_'+responseJSON.user_type).innerHTML = '';
        $('order_comment_'+responseJSON.user_type).style.display = 'none';
        var total_comment = $('total_comment_'+responseJSON.user_type).value;
        if(total_comment == 0)
        {
          $('tip_message_'+responseJSON.user_type).innerHTML = '';
        }
        ++total_comment;
        $('comment_count_'+responseJSON.user_type).innerHTML = '('+total_comment+')';
        $('total_comment_'+responseJSON.user_type).value = total_comment;
      }
    })
  );
}
</script>