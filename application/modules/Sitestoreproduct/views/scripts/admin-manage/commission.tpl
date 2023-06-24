<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: commission.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<!-- TABS -->
<div class='tabs'>
  <ul class="navigation">
    <li class="<?php echo ( $this->tab == 0 ? 'active' : '') ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'manage', 'action' => 'commission'), $this->translate('Manage Commission'), array()) ?>

    </li>
    <li class="<?php echo ( $this->tab != 0 ? 'active' : '') ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'manage', 'action' => 'commission', 'tab' => 1), $this->translate('Reversal Requests'), array()) ?>
    </li>
  </ul>
</div>

<?php if( empty($this->tab) ) : ?>
  <h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Commission"); ?></h3>
  <p>
    <?php echo $this->translate('Below, you can manage commissions on your site. The search box below will search through the store titles, store owner names, order min and max amount, commission min and max amount and commission duration. You can also use the filters below to filter the transactions.') ?>
  </p>
<?php else: ?>
  <h3 style="margin-bottom:6px;"><?php echo $this->translate("Reversal Requests"); ?></h3>
  <p>
    <?php echo $this->translate('Below, you can manage reversal commission requests and take action on these requests. The search box below will search through the order id, store titles, store owner names, order min and max amount, commission min and max amount and duration. You can also use the filters below to filter the transactions.') ?>
  </p>
<?php endif; ?>
<br style="clear:both;" />

<!-- SEARCH FORM -->
<div class="admin_search sitestoreproduct_admin_search">
  <div class="search">
    <form name="manage_orders_search_form" id="manage_orders_search_form" method="post" class="global_form_box" action="">
      <input type="hidden" name="post_search" /> 
      <?php if( !empty($this->tab) ) : ?>
        <div>
          <label><?php echo "Order Id" ?></label>
          <?php if( empty($this->title)):?>
            <input type="text" name="order_id" /> 
          <?php else: ?>
            <input type="text" name="order_id" value="<?php echo $this->order_id ?>"/>
          <?php endif;?>
        </div>
      <?php endif; ?>
      
      <div>
        <label><?php echo "Store Name" ?></label>
        <?php if( empty($this->title)):?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->title ?>"/>
        <?php endif;?>
      </div>
      
      <div>
        <label><?php echo "Owner Name" ?></label>
        <?php if( empty($this->username)):?>
          <input type="text" name="username" /> 
        <?php else: ?>
          <input type="text" name="username" value="<?php echo $this->username ?>"/>
        <?php endif;?>
      </div>
    
      <div>
        <label><?php echo  $this->translate("Order Amount") ?></label>
        <div>
          <?php if( $this->order_min_amount == ''):?>
            <input type="text" name="order_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="order_min_amount" placeholder="min" value="<?php echo $this->order_min_amount ?>" class="input_field_small" />
          <?php endif;?>

          <?php if( $this->order_max_amount == ''):?>
            <input type="text" name="order_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="order_max_amount" placeholder="max" value="<?php echo $this->order_max_amount ?>" class="input_field_small" />
          <?php endif;?>
        </div>   
      </div>
    
      <div>
        <label><?php echo  $this->translate("Commission Amount") ?></label>
        <div>
          <?php if( $this->commission_min_amount == ''):?>
            <input type="text" name="commission_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="commission_min_amount" placeholder="min" value="<?php echo $this->commission_min_amount ?>" class="input_field_small" />
          <?php endif;?>

          <?php if( $this->commission_max_amount == ''):?>
            <input type="text" name="commission_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="commission_max_amount" placeholder="max" value="<?php echo $this->commission_max_amount ?>" class="input_field_small" />
          <?php endif;?>
        </div>
      </div>
      
      <?php if( empty($this->tab) ) : ?>
        <div style="margin-top:16px;">
          <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
        </div>
      <?php else: ?>
        <div>
          <?php 
            //MAKE THE STARTTIME AND ENDTIME FILTER
            $starttime = $this->locale()->toDateTime(time());
            $attributes = array();
            $attributes['dateFormat'] = $this->locale()->useDateLocaleFormat(); //'ymd';

            $form = new Engine_Form_Element_CalendarDateTime('starttime');
            $attributes['options'] = $form->getMultiOptions();
            $attributes['id'] = 'starttime';

            if( !empty($this->starttime) ) :
              $attributes['starttimeDate'] = $this->starttime;
            endif;

            if( empty($this->tab) ) :
              echo '<label>'.$this->translate('Commission Duration').'</label>';
            else:
              echo '<label>'.$this->translate('Duration').'</label>';
            endif;
            echo '<div>';
            echo $this->formCalendarDateTimeElement('starttime', $starttime, array_merge(array('label' => 'From'), $attributes), $attributes['options'] );
            if( !empty($this->endtime) ) :
              $attributes['endtimeDate'] = $this->endtime;
            endif;
            $attributes['starttimeDate'] = '';
            echo $this->formCalendarDateTimeElement('endtime', $starttime, array_merge(array('label' => 'To'), $attributes), $attributes['options'] );
            echo '</div>';
          ?>
        </div>
      
        <div class="clr">
          <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
        </div>
      <?php endif; ?>
  
    </form>
  </div>
</div>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results'>
  <?php if (!empty($this->paginator)) : ?>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
  <?php endif; ?>
  
  <?php if (!empty($counter)): ?>
    <div>
      <br />
      <?php if( empty($this->tab) ) : ?>
        <?php echo $this->translate(array('%s store commission detail found.', '%s stores commission details found.', $counter), $this->locale()->toNumber($counter)) ?>
      <?php else: ?>
        <?php echo $this->translate(array('%s reversal request found.', '%s reversal requests found.', $counter), $this->locale()->toNumber($counter)) ?>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php if( empty($this->tab) ) : ?>
          <?php echo $this->translate("There are no commission details available yet.") ?>
        <?php else: ?>
          <?php echo $this->translate("There are no reversal requests available yet.") ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?> 
</div>
<br />

<?php if( empty($this->tab) ) : ?>
  <?php if (!empty($counter)): ?>
    <div class="clr">
      <table class='admin_table seaocore_admin_table' width="100%">
        <thead>
          <tr>
            <?php $class = ( $this->order == 'store_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('store_id', 'DESC');"><?php echo $this->translate('Store Id'); ?></a></th>

            <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store Name'); ?></a></th>

            <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Store Owner'); ?></a></th>

            <?php $class = ( $this->order == 'order_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('order_count', 'ASC');"><?php echo $this->translate('Order Count'); ?></a></th>

            <?php $class = ( $this->order == 'order_total' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('order_total', 'ASC');"><?php echo $this->translate('Order Amount'); ?></a></th>

            <?php $class = ( $this->order == 'commission' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('commission', 'ASC');"><?php echo $this->translate('Commission Amount'); ?></a></th>
            
            <th><?php echo $this->translate('Commission Paid'); ?></th>
            <th><?php echo $this->translate('Remaining Commission'); ?></th>
            <th class='admin_table_short'><?php echo $this->translate('Options') ?></th>
          </tr>	
        </thead>
        <?php foreach( $this->paginator as $item ): ?>
          <tbody>
            <?php $storeItem = $this->item('sitestore_store', $item->store_id); ?>
            <?php if( empty($storeItem) ): ?>
              <td><i>Store Deleted</i></td>
            <?php else: ?>
              <td><?php echo $this->htmlLink($storeItem->getHref(), $item->store_id, array('target' => '_blank')) ?></td>
            <?php endif; ?>

            <?php if( empty($storeItem) ): ?>
              <td><i>Store Deleted</i></td>
            <?php else: ?>
              <td><?php echo $this->htmlLink($storeItem->getHref(), $this->string()->truncate($this->string()->stripTags($storeItem->getTitle()), 10), array('title' => $storeItem->getTitle(), 'target' => '_blank')) ?></td>
            <?php endif; ?>
            <td>
              <?php echo $this->htmlLink($storeItem->getOwner(), $this->string()->truncate($this->string()->stripTags($storeItem->getOwner()), 10), array('title' => $storeItem->getOwner(), 'target' => '_blank')) ?>
            </td>

            <td class="admin_table_centered"><?php echo $item->order_count ?></td>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->order_total) ?></td>          
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->commission) ?></td>
            <td>                 
              <?php if( !empty($this->storePaidCommission[$item->store_id]['paid_commission']) ) : ?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($this->storePaidCommission[$item->store_id]['paid_commission']) ?>
                <?php $remaining_commission = $item->commission - $this->storePaidCommission[$item->store_id]['paid_commission']; ?>
              <?php else: ?>
                <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway')):?>

                    <?php $commissionPaid = Engine_Api::_()->sitegateway()->getSplitNEscrowGatewayCommission(array('resource_type' => 'sitestoreproduct_order', 'resource_id' => $item->store_id, 'resource_key' => 'store_id')); ?>
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($commissionPaid) ?>
                    <?php $remaining_commission = $item->commission - $commissionPaid; ?>

                <?php else: ?>
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin(0) ?>
                    <?php $remaining_commission = $item->commission; ?>
                <?php endif; ?>  
              <?php endif; ?>
            </td>
            <td>
              <?php if( $remaining_commission > 0 ) : ?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($remaining_commission) ?>
              <?php else: ?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin(0) ?>
              <?php endif; ?>
            </td>
            <td>
              <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'your-bill'), 'sitestore_store_dashboard', false) ?>" target="_blank"><?php echo $this->translate("view store bill") ?></a> |
              <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'product', 'menuId' => 54, 'method' => 'store-transaction', 'tab' => 1), 'sitestore_store_dashboard', false) ?>" target="_blank"><?php echo $this->translate("view store transaction") ?></a>
            </td>
          </tbody>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="clr mtop10">
      <?php echo $this->paginationControl($this->paginator, null, null, array(
  				'pageAsQuery' => true,
  				'query' => $this->formValues,
  			));
      ?>
    </div>
  <?php endif; ?>

<?php elseif( !empty($this->tab) ) : ?>
  <?php if (!empty($counter)): ?>
    <div style="overflow-x:scroll;">
      <table class='admin_table seaocore_admin_table' width="100%">
        <thead>
          <tr>
            <?php $class = ( $this->order == 'order_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate('Order Id'); ?></a></th>

            <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store Name'); ?></a></th>

            <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Store Owner'); ?></a></th>

            <?php $class = ( $this->order == 'grand_total' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('grand_total', 'ASC');"><?php echo $this->translate('Order Amount'); ?></a></th>

            <?php $class = ( $this->order == 'commission_value' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
            <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('commission_value', 'ASC');"><?php echo $this->translate('Commission Amount'); ?></a></th>

            <th class='admin_table_short'>Reason</th>
            <th class='admin_table_short'>Admin Action</th>
            <th class='admin_table_short'>Seller Message</th>
            <th class='admin_table_short'>Admin Message</th>
            <th class='admin_table_short'>Options</th>
          </tr>	
        </thead>
        <?php foreach( $this->paginator as $item ): ?>
          <tbody>
            <td>
              <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $item->order_id), 'sitestore_store_dashboard', false) ?>" target="_blank">
                <?php echo '#'.$item->order_id; ?>
              </a>
            </td>
            <td>
              <?php $storeItem = $this->item('sitestore_store', $item->store_id); ?>
              <?php if( empty($storeItem) ): ?>
                <i>Store Deleted</i>
              <?php else: ?>
                <?php echo $this->htmlLink($storeItem->getHref(), $this->string()->truncate($this->string()->stripTags($storeItem->getTitle()), 10), array('title' => $storeItem->getTitle(), 'target' => '_blank')) ?>
              <?php endif; ?>
            </td>
            <td>
              <?php echo $this->htmlLink($storeItem->getOwner(), $this->string()->truncate($this->string()->stripTags($storeItem->getOwner()), 10), array('title' => $storeItem->getOwner(), 'target' => '_blank')) ?>
            </td>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->grand_total) ?></td>
            <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->commission_value) ?></td>          
            <td>
              <?php if( $item->non_payment_seller_reason == 1 ) : ?>
                <?php echo 'Chargeback' ?>
              <?php elseif( $item->non_payment_seller_reason == 2 ) : ?>
                <?php echo 'Payment not received' ?>
              <?php elseif( $item->non_payment_seller_reason == 3 ) : ?>
                <?php echo 'Canceled payment' ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if( empty($item->non_payment_admin_reason) ) : ?>
                <?php echo '-' ?>
              <?php elseif( $item->non_payment_admin_reason == 1 ) : ?>
                <?php echo 'Approved' ?>
              <?php elseif( $item->non_payment_admin_reason == 2 ) : ?>
                <?php echo 'Declined' ?>
              <?php elseif( $item->non_payment_admin_reason == 3 ) : ?>
                <?php echo 'Hold' ?>
              <?php endif; ?>
            </td>
            <td title="<?php echo $item->non_payment_seller_message ?>">
              <?php echo empty($item->non_payment_seller_message) ? '-' : Engine_Api::_()->sitestoreproduct()->truncation($item->non_payment_seller_message, 30); ?>
            </td>
            <td title="<?php echo $item->non_payment_admin_message ?>">
              <?php echo empty($item->non_payment_admin_message) ? '-' : Engine_Api::_()->sitestoreproduct()->truncation($item->non_payment_admin_message, 30); ?>
            </td>
            <td>
              <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('action' => 'reversal-commission', 'order_id' => $item->order_id)) ?>')">
                <?php echo $this->translate("take action") ?>
              </a> |
              <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $item->order_id), 'sitestore_store_dashboard', false) ?>" target="_blank">
                <?php echo $this->translate("view order") ?>
              </a> |
              <a href="<?php echo $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'product', 'menuId' => 56, 'method' => 'your-bill'), 'sitestore_store_dashboard', false) ?>" target="_blank">
                <?php echo $this->translate("view store bill") ?>
              </a>
            </td>
          </tbody>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="clr mtop10">
      <?php echo $this->paginationControl($this->paginator, null, null, array(
  				'pageAsQuery' => true,
  				'query' => $this->formValues,
  			));
      ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">

var currentOrder = '<?php echo $this->order ?>';
	var currentOrderDirection = '<?php echo $this->order_direction ?>';
	var changeOrder = function(order, default_direction) {
		if( order == currentOrder ) {
			$('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
		} 
		else {
			$('order').value = order;
			$('order_direction').value = default_direction;
		}
		$('filter_form').submit();
	}

<?php if( !empty($this->tab) ) : ?>
  window.addEvent('domready', function() { 
    initializeCalendar();
  });

  var initializeCalendar = function() { 
     // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    
    // check start date and make it the same date if it's too		
    cal_starttime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
  var cal_starttime_onHideStart = function() { 
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    
    //CHECK IF THE END TIME IS LESS THEN THE START TIME THEN CHANGE IT TO THE START TIME.
     var startdatetime = new Date($('starttime-date').value);
     var enddatetime = new Date($('endtime-date').value);
     if(startdatetime.getTime() > enddatetime.getTime()) {
       $('endtime-date').value = $('starttime-date').value;
       $('calendar_output_span_endtime-date').innerHTML = $('endtime-date').value;
       cal_endtime.changed(cal_endtime.calendars[0]);
     }
  }
<?php endif; ?>
</script>
