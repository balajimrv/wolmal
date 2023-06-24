<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-ship.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php if( !empty($this->sitestoreproduct_order_ship_no_permission) ) : ?>
<div class="tip">
  <span>
    <?php echo $this->translate("Order not available or you are not permitted to view shipping details of this order.") ?>
  </span>
</div>
<?php return; endif; ?>

<?php if( !empty($this->callingStatus) ): ?>
<div id='manage_order_menue'>
  <div>
    <a href="javascript:void(0);" onclick = 'manage_store_dashboard(55, "manage-order", "index");' id="sitestoreproduct_menu_1" class="buttonlink icon_previous mbot5"><?php echo $this->translate('Back to Manage Orders') ?></a>
  </div>

<?php if( $this->orderObj->payment_status == 'active' && $this->orderObj->order_status != 6 ) : ?>
  <div class="tabs">
    <ul class="navigation sr_sitestoreproduct_navigation_common">
      <li>
        <a href="javascript:void(0);" onclick = "manage_store_dashboard(55, 'order-view/order_id/<?php echo $this->order_id; ?>', 'index')" id="sitestoreproduct_menu_1" ><?php echo $this->translate('View') ?></a> 
      </li>
      <li class="active">
        <a href="javascript:void(0);" onclick = "manage_store_dashboard(55, 'order-ship/order_id/<?php echo $this->order_id; ?>', 'index')" id="sitestoreproduct_menu_1" ><?php echo $this->translate('Shipping Details') ?></a> 
      </li>
      <li>
        <a href="sitestoreproduct/index/print-invoice/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id);?>" target="_blank" ><?php echo $this->translate('Print Invoice') ?></a> 
      </li>
      <li>
        <a href="sitestoreproduct/index/print-packing-slip/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id);?>" target="_blank"><?php echo $this->translate('Print Packing Slip') ?></a> 
      </li>
    </ul>
  </div>
  <?php endif; ?>
  
</div>
<?php else: ?>
  <!-- CALLING FOR BUYER -->
  <div id='manage_order_menue'>
    <div class="paginator_previous">
      <?php $tempUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'my-order'), 'default', true); ?>
      <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', '', <?php echo $this->order_id; ?>, '<?php echo $tempUrl; ?>');" id="sitestoreproduct_menu_1" class="buttonlink icon_previous mbot5"><?php echo $this->translate('Back to Manage Orders') ?></a>
    </div>
    <br />
    <?php 
      $tempViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $this->order_id, 'page_viewer' => $this->page_user) , 'sitestoreproduct_general', true); 
      $tempShipmentUrl = $this->url(array('action' => 'order-ship', 'order_id' => $this->order_id, 'page_viewer' => $this->page_user) , 'sitestoreproduct_general', true);
      $tempReorder = $this->url(array('action' => 'cart', 'reorder' => 1, 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id)) , 'sitestoreproduct_product_general', true);
      $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($this->order_id)) , 'sitestoreproduct_general', true);
    ?>

    <div class="tabs">
    	<ul class="navigation sr_sitestoreproduct_navigation_common">
        <li id="buyer_account_view_order">
          <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', 'order-view', <?php echo $this->order_id; ?>, '<?php echo $tempViewUrl; ?>');" id="sitestoreproduct_menu_1"><?php echo $this->translate('View') ?></a>
        </li>
        <li id="buyer_account_shipment" class="active">
          <a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', 'order-shipment', <?php echo $this->order_id; ?>, '<?php echo $tempShipmentUrl; ?>');" id="sitestoreproduct_menu_1"><?php echo $this->translate('Shipping Details') ?></a>
        </li>
        <?php if( !empty($this->isStoreExist) ) : ?>
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
  </div>
<?php endif; ?>
<br />

<?php if( empty($this->page_user) && !empty($this->callingStatus) ) :?>
  <a class="buttonlink seaocore_icon_add mbot10" href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'add-shipment', 'order_id' => $this->order_id, 'store_id' => $this->store_id), 'default', true); ?>')"><?php echo $this->translate("Add Shipping Details") ?></a>
<?php endif; ?>

<?php if(COUNT($this->shipTrackObj)): ?>
<div class="product_detail_table sitestoreproduct_data_table fleft">
  <table>
    <tr class="product_detail_table_head">
      <th><?php echo $this->translate('Service') ?></th>
      <th><?php echo $this->translate('Title') ?></th>
      <th><?php echo $this->translate('Tracking Number') ?></th>
      <th><?php echo $this->translate('Date') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Note') ?></th>
      <th><?php echo $this->translate('Options') ?></th>
    </tr>	
    <?php foreach( $this->shipTrackObj as $item ):?>
      <?php if( $item->status == 1)
          $status = 'Active';
        else if( $item->status == 2 )
          $status = 'Completed';
        else if( $item->status == 3 )
          $status = 'Canceled';
      ?>
    <tr>
      <td><?php echo $item->service ?></td>
      <td><?php echo empty($item->title) ? '-' : $item->title; ?></td>
      <td><?php echo $item->tracking_num ?></td>
      <td><?php echo gmdate('M d,Y, g:i A',strtotime($item->creation_date)); ?></td>
      <td><?php echo $this->translate("%s", $status); ?></td>
      <td><?php echo empty($item->note)? "-" : Engine_Api::_()->sitestoreproduct()->truncation($item->note); ?></td>
      <td><?php 
       echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'sitestoreproduct/index/detail-shipment/shippingtracking_id/'.base64_encode($item->shippingtracking_id).'/store_id/'.base64_encode($this->store_id).'\')"> ' . $this->translate("details") . '</a>';
       if( empty($this->page_user) && !empty($this->callingStatus) ) : 
         if( $item->status == 1 && empty($item->is_deleted) ):
            $edit_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'edit-shipment', 'shippingtracking_id' => $item->shippingtracking_id, 'store_id' => $this->store_id), 'sitestoreproduct_general', true);
            echo ' | <a href="javascript:void(0);" onclick="Smoothbox.open(\''.$edit_url.'\')">'. $this->translate('edit') . '</a>';
          endif;

          if( empty($item->is_deleted) ):
            $delete_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'delete-shipment', 'shippingtracking_id' => $item->shippingtracking_id, 'store_id' => $this->store_id), 'sitestoreproduct_general', true);  
            echo ' | <a href="javascript:void(0);" onclick="Smoothbox.open(\''.$delete_url.'\')">' . $this->translate('delete') . '</a>'; 
          else:
            echo ' | <span style="color:red; font-style:italic;">' . $this->translate('deleted') . '</span>';  
          endif;
        endif;
        ?>
      </td>
    </tr>        
    <?php endforeach; ?>
  </table>
</div>
<?php else: ?>
  <div class="tip">
    <span><?php echo $this->translate("There are no shipping details available yet.") ?></span>
  </div>
<?php endif; ?>