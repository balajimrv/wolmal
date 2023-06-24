<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php if( COUNT($this->latestOrders) > 0 ): ?>

<div class="product_detail_table sitestoreproduct_data_table fleft">
  <table>
    <tr class="product_detail_table_head">
      <th style="width:5%;"><?php echo $this->translate("Id") ?></th>
      <th style="width:20%;"><?php echo $this->translate("Status") ?></th>
      <th style="width: 20%;"><?php echo $this->translate("Buyer Name") ?></th>
      <th style="width: 25%;"><?php echo $this->translate("Placed On") ?></th>
      <th style="width: 10%;"><?php echo $this->translate("Products") ?></th>
      <th style="width: 10%;"><?php echo $this->translate("Ship In") ?></th>
      <th style="width: 20%;"><?php echo $this->translate("Total") ?></th>
    </tr>
    <?php foreach($this->latestOrders as $latestOrder) :  ?>
    <tr>
      <td class="txt_center ">
        <a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-view/order_id/<?php echo $latestOrder->order_id; ?>', 'index')"><?php echo '#'.$latestOrder->order_id ?></a>
      </td>
      <?php $tempStatus = $this->getOrderStatus($latestOrder->order_status, true); ?>
      <td class="<?php echo $tempStatus['class'] ?>"><?php echo $tempStatus['title'] ?></td>
      <td><?php echo empty($latestOrder->buyer_id) ? $this->translate("Guest") : $this->htmlLink($latestOrder->getHref(), ucfirst($latestOrder->getTitle())) ?></td>
      <td><?php echo $latestOrder->order_date ?></td>
      <td class="txt_center "><?php echo $latestOrder->item_count ?></td>
      <td class="txt_center "><?php echo ( $latestOrder->order_status < 2 || empty($latestOrder->delivery_time) ) ? '-' : $latestOrder->delivery_time;  ?></td>
      <td class="txt_right"><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($latestOrder->grand_total) ?></b></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php else: ?>
  <div class="tip">
    <span>
<?php echo $this->translate("No orders have been placed in your store yet.");
  if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite')): 
    $invitePeople = $this->htmlLink(array('route' => 'sitestoreinvite_invite', 'user_id' => $this->viewer_id, 'sitestore_id' => $this->storeId), $this->translate('Invite people')) ;
    echo $this->translate(" %s to shop from your store.", $invitePeople) ;
  endif; ?>
    </span> 
  </div>
<?php endif; ?>