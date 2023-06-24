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
  <?php if ($paginationCount): ?>
    <div class="sm-ui-message-tip"><span><?php echo $this->translate('%s order(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>

<?php if ($paginationCount): ?>
    <div class="ui-store-table sm-widget-block">
        <table data-role="table" id="movie-table" data-mode="reflow" class="ui-responsive table-stroke">
          <thead>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate("Order Id"); ?></th>
            <th><?php echo $this->translate("Billing Name"); ?></th>
            <th><?php echo $this->translate("Order Date"); ?></th>
            <th><?php echo $this->translate("Status"); ?></th>
            <!--<th><?php //echo $this->translate("Payment"); ?></th>-->
            <th><?php echo $this->translate("Delivery Time"); ?></th>
          </tr>
          </thead>
          <tbody>
          <?php
          foreach ($this->paginator as $order):
            $billing_address = $order_address_table_obj->getAddress($order->order_id, false);
            $shipping_address = $order_address_table_obj->getAddress($order->order_id, true);

            if ($order->payment_status != 'active'):
              $payment_status = 'No';
            else:
              $payment_status = 'Yes';
            endif;

            if ($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4) :
              $delivery_time = empty($order->delivery_time) ? '-' : $order->delivery_time;
            else:
              $delivery_time = '-';
            endif;

            ?>
            <tr>
              <?php $tempViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $order->order_id, 'page_viewer' => $this->page_user), 'sitestoreproduct_general', true);?>
              <td><a href="<?php echo $tempViewUrl;?>"><?php echo '#' . $order->order_id; ?></a></td>
              <td><?php echo $billing_address->f_name . ' ' . $billing_address->l_name; ?></td>
              <td><?php echo gmdate('M d,Y, g:i A', strtotime($order->creation_date)); ?></td>
              <?php $tempStatus = $this->getOrderStatus($order->order_status, true); ?>
              <td class="<?php echo $tempStatus['class'] ?>"><?php echo $tempStatus['title']; ?></td>
              <!--<td class="txt_center"><?php //echo $this->translate($payment_status); ?></td>-->
              <td title="<?php echo $delivery_time ?>"><?php echo Engine_Api::_()->sitestoreproduct()->truncation($delivery_time, 18); ?></td>            
              
            </tr> 
            <tr class="ui-store-table-sep"><td colspan="5" class="b_dark"></td></tr>
        <?php endforeach; ?>
          </tbody>
        </table>
    </div>
 <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>
<?php
else:
  echo '<div class="tip"><span>' . $this->translate('There are no orders found yet.') . '</span></div>';
endif;
?>

