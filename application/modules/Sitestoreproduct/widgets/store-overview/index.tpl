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
	
<div>
  <ul>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Products sold worth'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->store_overview['sub_total']); ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Total Commission'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->store_overview['commission']); ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Your Tax Collection'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->store_overview['store_tax']); ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate("Website's Tax Collection"); ?> </b> </div>
      <div class="fright">: &nbsp;<?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->store_overview['admin_tax']); ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Orders with completed payment'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php  echo $this->store_overview['order_count']; ?> </div>
    </li>
    <br><li class="clr mtop10"><h3><?php echo $this->translate("Store Orders"); ?></h3></li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Total Orders'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->total_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order", "index");\' id="id_55">'.$this->total_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Orders with payment approval pending'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->approval_pending_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/1", "index");\' id="id_55">'.$this->approval_pending_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Orders with payment pending'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->payment_pending_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/2", "index");\' id="id_55">'.$this->payment_pending_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Orders in process'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->processing_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/3", "index");\' id="id_55">'.$this->processing_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Orders on hold'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->on_hold_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/4", "index");\' id="id_55">'.$this->on_hold_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Fraudulent Orders'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->fraud_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/5", "index");\' id="id_55">'.$this->fraud_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Completed Orders'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->complete_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/6", "index");\' id="id_55">'.$this->complete_orders.' </a>' ?> </div>
    </li>
    <li class="clr">
      <div class="store_overview_info fleft"> <b><?php echo $this->translate('Cancelled Orders'); ?> </b> </div>
      <div class="fright">: &nbsp;<?php echo empty($this->cancel_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_store_dashboard (55, "manage-order/search/1/status/7", "index");\' id="id_55">'.$this->cancel_orders.' </a>' ?> </div>
    </li>
  </ul>
</div>