<script type="text/javascript" >
  var submitformajax = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
  <div id="manage_order_tab">
      <?php if(count($this->ordersobj)>0): ?>
       <div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
          <table>
            <tr class="product_detail_table_head">
              <th class="txt_center"><?php echo $this->translate('Order Id') ?></th>
              <th class="txt_center"><?php echo $this->translate('Buyer') ?></th>
              <th class="txt_center"><?php echo $this->translate('Order Date') ?></th>
              <th class="txt_center"><?php echo $this->translate('Qty') ?></th>
              <th class="txt_right"><?php echo $this->translate('Product Price(Incl Tax)') ?></th>
              <th ><?php echo $this->translate('Options') ?></th>
            </tr>
            <?php foreach ($this->ordersobj as $order): ?>
            <tr>
              <td class="txt_center">
                <span><?php
                $order_view_url = $this->url(array('action' => 'store', 'store_id' => $this->sitestore->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false);
                echo $this->htmlLink($order_view_url, "#".$order->order_id, array('target' => '_top'));?>
                </span>
              </td>
              <td class="txt_center">
                <?php if(!empty($order->order_owner_id)): echo $this->htmlLink(array('route' =>'user_profile', 'id' => $order->order_owner_id), $order->displayname, array('target' => '_top')); else: echo $this->translate('Guest'); endif;?>
              </td>
              <td class="txt_center">
                <?php echo gmdate('M d,Y, g:i A',strtotime($order->order_creation_date)); ?>
              </td>
              <td class="txt_center">
                <?php echo $this->locale()->toNumber($order->quantity); ?>
              </td>
              <td class="txt_right">
                <?php if(!empty ($order->price)): $temp_price = @round($order->price+$order->tax_amount); else: $temp_price = 0; endif;
                echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($temp_price); ?>
              </td>
              <td>
                <span><?php
                $order_view_url = $this->url(array('action' => 'store', 'store_id' => $this->sitestore->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false);
                echo $this->htmlLink($order_view_url, $this->translate("view order"), array('target' => '_top'));?> | 
                </span>
                <span><?php echo $this->htmlLink($this->sitestore->getHref(), $this->translate("seller"), array('target' => '_top'));?> </span>
                <span><?php  if(!empty($order->order_owner_id)): 
                  echo "| ".$this->htmlLink(array('route' =>'user_profile', 'id' => $order->order_owner_id), $this->translate("buyer"), array('target' => '_top'));
                endif;?></span>
              </td>
            </tr>
            <?php endforeach;?>
          </table>
        </div>
    <?php else: ?>
      <div class="tip"><span>
      <?php echo $this->translate('There are no orders placed for this product yet.') ?>
        </span></div>
    <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
  miniLoadingImage = 1;
</script>