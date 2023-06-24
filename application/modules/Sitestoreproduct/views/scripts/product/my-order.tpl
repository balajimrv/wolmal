<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my-order.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
$order_address_table_obj = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
$storeTableObj = Engine_Api::_()->getDbtable('stores', 'sitestore');
$paginationCount = @count($this->paginator);
?>

<?php if (empty($this->call_same_action)) : ?>
  <div><h3><?php echo $this->translate('My Orders') ?></h3></div><br />
  <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
    <form method="post" class="field_search_criteria" id="filter_form">
      <div>
        <ul>  
          <li>
            <span><label> <?php echo $this->translate("Order Id (#)") ?></label></span>
            <input type="text" name="order_id" id="order_id" /> 
          </li>
          <li>
            <span><label> <?php echo $this->translate("Order Date : ex (2000-12-25)") ?></label></span>
            <input type="text" name="creation_date" id="creation_date" /> 
          </li>
          <li>
            <span><label><?php echo $this->translate("Billing Name") ?></label></span>
            <input type="text" name="billing_name" id="billing_name" />
          </li>
          <li>
            <span><label><?php echo $this->translate("Shipping Name") ?></label></span>
            <input type="text" name="shipping_name" id="shipping_name" />
          </li>
          <li id="integer-wrapper">
            <label><?php echo $this->translate("Order Total") ?></label>
            <div class="form-element"><input type="text" name="order_min_amount" id="order_min_amount" placeholder="min"/></div>
            <div class="form-element"><input type="text" name="order_max_amount" id="order_max_amount" placeholder="max"/></div>
          </li>
          <li>
            <span><label><?php echo $this->translate("Delivery Time (In Days)") ?></label></span>
            <input type="text" name="delivery_time" id="delivery_time" />
          </li>
          <li>
            <span><label><?php echo $this->translate("Status") ?>	</label></span>
            <select id="order_status" name="order_status" >
              <option value="0" ></option>
              <?php
              for ($index = 0; $index < 7; $index++):
                echo '<option value="' . ($index + 1) . '">' . $this->translate("%s", $this->getOrderStatus($index)) . '</option>';
              endfor;
              ?>
            </select>
          </li>
          <?php if( !empty($this->isDownPaymentEnable) ) : ?>
            <li>
              <span><label><?php echo  $this->translate("Downpayment") ?>	</label></span>
              <select id="downpayment" name="downpayment" >
                <option value="0" ></option>
                <option value="1" <?php if( $this->downpayment == 1) echo "selected";?> >
                  <?php echo $this->translate("Yes, with downpayment") ?>
                </option>
                <option value="2" <?php if( $this->downpayment == 2) echo "selected";?> >
                  <?php echo $this->translate("Yes, with downpayment and remaining amount payment completed") ?>
                </option>
                <option value="3" <?php if( $this->downpayment == 3) echo "selected";?> >
                  <?php echo $this->translate("Yes, with downpayment and remaining amount payment not completed") ?>
                </option>
                <option value="4" <?php if( $this->downpayment == 4) echo "selected";?> >
                  <?php echo $this->translate("No, without downpayment") ?>
                </option>
              </select>
            </li>
          <?php endif; ?>
          <li class="clear mtop10">
            <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>        
          </li>
          <li>
            <span id="search_spinner"></span>
          </li>
        </ul>
      </div>
    </form>
  </div>

  <?php endif; ?>

<div id="manage_order_pagination">
  <?php if ($paginationCount): ?>
    <div class="mbot5"><span><?php echo $this->translate('%s order(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>
  <div id="manage_order_tab">

<?php if ($paginationCount): ?>
      <div class="sitestoreproduct_data_table product_detail_table fleft mbot10">
        <table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate("Order Id"); ?></th>
            <th><?php echo $this->translate("Billing Name"); ?></th>
            <th><?php echo $this->translate("Shipping Name"); ?></th>
            <th><?php echo $this->translate("Order Date"); ?></th>
            <th><?php echo $this->translate("Qty") ?></th>
            <th><?php echo $this->translate("Order Total"); ?></th>
            <th><?php echo $this->translate("Status"); ?></th>
            <th><?php echo $this->translate("Payment"); ?></th>
            <th><?php echo $this->translate("Delivery Time"); ?></th>
            <th><?php echo $this->translate("Options"); ?></th>
          </tr>
          <?php
          foreach ($this->paginator as $order):
            $billing_address = $order_address_table_obj->getAddress($order->order_id, false);
            $shipping_address = $order_address_table_obj->getAddress($order->order_id, true);

            if( $order->order_status == 8 ) : 
              $payment_status = 'marked as non-payment';
            elseif( $order->payment_status == 'active' ) :
              $payment_status = 'Yes';
            else:
              $payment_status = 'No';
            endif;
            
            if ($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4) :
              $delivery_time = empty($order->delivery_time) ? '-' : $order->delivery_time;
            else:
              $delivery_time = '-';
            endif;

            $tempViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $order->order_id, 'page_viewer' => $this->page_user), 'sitestoreproduct_general', true);
            $tempShipmentUrl = $this->url(array('action' => 'order-ship', 'order_id' => $order->order_id, 'page_viewer' => $this->page_user), 'sitestoreproduct_general', true);
            $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order->order_id)), 'sitestoreproduct_general', true);
            $tempReorder = $this->url(array('action' => 'cart', 'reorder' => 1, 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order->order_id)), 'sitestoreproduct_product_general', true);
            ?>
            <tr>
              <td><a href="javascript:void(0)" onclick = "myAccountUrl('my-orders', 'order-view', '<?php echo $order->order_id; ?>', '<?php echo $tempViewUrl; ?>');"> <?php echo '#' . $order->order_id; ?> </a></td>
              <td><?php echo $billing_address->f_name . ' ' . $billing_address->l_name; ?></td>
              <td><?php echo $shipping_address->f_name . ' ' . $shipping_address->l_name; ?></td>
              <td><?php echo $this->locale()->toDateTime($order->creation_date); ?></td>
              <td class="txt_center"><?php echo $this->locale()->toNumber($order->item_count); ?></td>
              <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($order->grand_total); ?></td>
              <?php if( $order->order_status == 8 ) : ?>
                <td><i>-</i></td>
              <?php else: ?>
                <?php $tempStatus = $this->getOrderStatus($order->order_status, true); ?>
                <td class="<?php echo $tempStatus['class'] ?>"><?php echo $tempStatus['title']; ?></td>
              <?php endif; ?>
              <td class="txt_center"><?php echo $this->translate($payment_status); ?></td>
              <td title="<?php echo $delivery_time ?>"><?php echo Engine_Api::_()->sitestoreproduct()->truncation($delivery_time, 18); ?></td>
              <td>
                <a href="javascript:void(0)" onclick = "myAccountUrl('my-orders', 'order-view', '<?php echo $order->order_id; ?>', '<?php echo $tempViewUrl; ?>');">
                  <?php echo $this->translate("view"); ?>
                </a>
                <?php $isStoreExist = $storeTableObj->getStoreAttribute($order->store_id, 'store_id'); ?>
                <?php $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $order->order_id, 'virtual' => true)); ?>
                <?php if( !empty($isStoreExist) && !empty($anyOtherProducts) ) : ?>
                  | <a href="<?php echo $tempReorder; ?>" ><?php echo $this->translate("reorder"); ?></a>
                <?php endif; ?>
                <?php if( $order->order_status != 6 && $order->order_status != 8 ) : ?>
                  <?php $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $order->order_id)); ?>
                  <?php if (!empty($anyOtherProducts) && empty($bundleProductShipping) ) : ?>
                  | <a href="javascript:void(0)" onclick = "myAccountUrl('my-orders', 'order-shipment', '<?php echo $order->order_id; ?>', '<?php echo $tempShipmentUrl; ?>');">
                      <?php echo $this->translate("shipping details"); ?>
                    </a>
                  <?php endif; ?>

                  <?php if ($payment_status == 'Yes') : ?>
                    | <a href="<?php echo $tempInvoice; ?>" target="_blank"><?php echo $this->translate("print invoice"); ?></a>
                  <?php endif; ?>

                  <?php $makePaymentText = ''; ?>
                  <?php if( empty($this->directPayment) && empty($this->isDownPaymentEnable) && empty($order->is_downpayment) && empty($order->direct_payment) && (($order->gateway_id == 1 || $order->gateway_id == 2) && $order->payment_status != 'active') ) : ?>
                    <?php $makePaymentText = $this->translate("make payment"); ?>
                  <?php elseif( !empty($this->isDownPaymentEnable) && empty($this->onlyCodGatewayEnable) && $order->is_downpayment == 1 && ( (($order->gateway_id == 1 || $order->gateway_id == 2) && $order->payment_status == 'active') || $order->gateway_id == 3 || $order->gateway_id == 4 ) ): ?>
                    <?php $makePaymentText = $this->translate("pay remaining amount"); ?>
                  <?php endif; ?>
                  <?php if( !empty($makePaymentText) ): ?>
                  | <a href="javascript:void(0);" class="seaocore_txt_red" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'make-payment', 'order_id' => $order->order_id), 'default', true); ?>')">
                      <?php echo $makePaymentText ?>
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr> 
        <?php endforeach; ?>
        </table>
      </div>
    </div>
   <div class="clr dblock sitestoreproduct_data_paging">
      <div id="store_manage_order_previous" class="paginator_previous sitestoreproduct_data_paging_link">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
        <span id="manage_spinner_prev"></span>
      </div>

      <div id="store_manage_order_next" class="paginator_next sitestoreproduct_data_paging_link">
        <span id="manage_spinner_next"></span>
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next'
        ));
        ?>
      </div>


<?php
else:
  echo '<div class="tip"><span>' . $this->translate('There are no orders found yet.') . '</span></div>';
endif;
?>
  </div>
</div>

<script type="text/javascript">
            en4.core.runonce.add(function() {

              var anchor = document.getElementById('manage_order_tab').getParent();
<?php if ($paginationCount): ?>
                document.getElementById('store_manage_order_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
                $('store_manage_order_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

                $('store_manage_order_previous').removeEvents('click').addEvent('click', function() {
                  $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';


                  var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'account', 'menuType' => 'my-orders', 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestoreproduct_general', true); ?>';
                  if (tempManagePaginationUrl && typeof history.pushState != 'undefined') {
                    history.pushState({}, document.title, tempManagePaginationUrl);
                  }

                  var downpayment = 0;
                  <?php if( !empty($this->isDownPaymentEnable) ) : ?>
                    downpayment = $('downpayment').value;
                  <?php endif; ?>

                  en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'sitestoreproduct/product/my-order',
                    data: {
                      format: 'html',
                      search: 1,
                      subject: en4.core.subject.guid,
                      call_same_action: 1,
                      creation_date: $('creation_date').value,
                      billing_name: $('billing_name').value,
                      shipping_name: $('shipping_name').value,
                      order_min_amount: $('order_min_amount').value,
                      order_max_amount: $('order_max_amount').value,
                      delivery_time: $('delivery_time').value,
                      order_status: $('order_status').value,
                      downpayment : downpayment,
                      page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    },
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                      $('manage_spinner_prev').innerHTML = '';
                    }
                  }), {
                    'element': anchor
                  })
                });

                $('store_manage_order_next').removeEvents('click').addEvent('click', function() {
                  $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

                  var tempManagePaginationUrl = '<?php echo $this->url(array('action' => 'account', 'menuType' => 'my-orders', 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestoreproduct_general', true); ?>';
                  if (tempManagePaginationUrl && typeof history.pushState != 'undefined') {
                    history.pushState({}, document.title, tempManagePaginationUrl);
                  }
                  
                  var downpayment = 0;
                  <?php if( !empty($this->isDownPaymentEnable) ) : ?>
                    downpayment = $('downpayment').value;
                  <?php endif; ?>

                  en4.core.request.send(new Request.HTML({
                    url: en4.core.baseUrl + 'sitestoreproduct/product/my-order',
                    data: {
                      format: 'html',
                      search: 1,
                      subject: en4.core.subject.guid,
                      call_same_action: 1,
                      creation_date: $('creation_date').value,
                      billing_name: $('billing_name').value,
                      shipping_name: $('shipping_name').value,
                      order_min_amount: $('order_min_amount').value,
                      order_max_amount: $('order_max_amount').value,
                      delivery_time: $('delivery_time').value,
                      order_status: $('order_status').value,
                      downpayment : downpayment,
                      page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    },
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                      $('manage_spinner_next').innerHTML = '';
                    }
                  }), {
                    'element': anchor
                  })
                });

<?php endif; ?>

              $('filter_form').removeEvents('submit').addEvent('submit', function(e) {
                e.stop();
                $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

                var downpayment = 0;
                <?php if( !empty($this->isDownPaymentEnable) ) : ?>
                  downpayment = $('downpayment').value;
                <?php endif; ?>
                en4.core.request.send(new Request.HTML({
                  url: en4.core.baseUrl + 'sitestoreproduct/product/my-order',
                  method: 'POST',
                  data: {
                    search: 1,
                    subject: en4.core.subject.guid,
                    call_same_action: 1,
                    order_id: $('order_id').value,
                    creation_date: $('creation_date').value,
                    billing_name: $('billing_name').value,
                    shipping_name: $('shipping_name').value,
                    order_min_amount: $('order_min_amount').value,
                    order_max_amount: $('order_max_amount').value,
                    delivery_time: $('delivery_time').value,
                    order_status: $('order_status').value,
                    downpayment : downpayment,
                  },
                  onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    $('search_spinner').innerHTML = '';

                  }
                }), {
                  'element': anchor
                })
              });

            });
</script>