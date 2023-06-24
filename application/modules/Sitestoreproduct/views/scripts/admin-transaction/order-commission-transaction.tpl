<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-commission-transaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction) {
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    }
    else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'payment', 'action' => 'index'), $this->translate('Stores - Package Related Transactions'), array())
      ?>
    </li>
    <?php if( empty($this->directPaymentEnable) ) : ?>
      <li>
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'index'), $this->translate('Products - Order Related Transactions'), array())
        ?>
      </li>		
      <li>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'admin-transaction'), $this->translate('Products - Payments to Sellers')) ?>
      </li>
    <?php else: ?>
      <li class="active">
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'order-commission-transaction'), $this->translate('Products - Commission Related Transactions')) ?>
      </li>
    <?php endif; ?>
  </ul>
</div>

<div class='settings clr'>
  <h3><?php echo $this->translate("Products - Commission Related Transactions"); ?></h3>
  <p class="description">
    <?php echo $this->translate('Browse through the transactions made by sellers to pay commissions for the sales made from their stores. The search box below will search through the store names, store owner names, amount and transaction duration. You can also use the filters below to filter the transactions.'); ?>
  </p>
</div>

<br style="clear:both;" />
<div class="admin_search sitestoreproduct_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <input type="hidden" name="post_search" /> 
      <div>
	      <label>
	      	<?php echo  $this->translate("Store Name") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>
    
      <div>
	      <label>
	      	<?php echo  $this->translate("Owner Name") ?>
	      </label>
	      <?php if( empty($this->username)):?>
	      	<input type="text" name="username" /> 
	      <?php else: ?>
	      	<input type="text" name="username" value="<?php echo $this->translate($this->username)?>"/>
	      <?php endif;?>
      </div>

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

          echo '<label>'.$this->translate('Transaction Duration').'</label>';
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

      <div>
        <label>
          <?php echo $this->translate("Amount") ?>
        </label>
        <div>
          <?php if ($this->min_amount == ''): ?>
            <input type="text" name="min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="min_amount" placeholder="min" value="<?php echo $this->translate($this->min_amount) ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->max_amount == ''): ?>
            <input type="text" name="max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="max_amount" placeholder="max" value="<?php echo $this->translate($this->max_amount) ?>" class="input_field_small" />
          <?php endif; ?>
        </div>

      </div>

      <div class="clr">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>

    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>


<div class='admin_members_results'>
  <?php
  if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
  }
  if (!empty($counter)):
    ?>
    <div class="">
      <?php echo $this->translate(array('%s transaction found.', '%s transactions found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo $this->translate("No results were found.") ?></span>
    </div>
  <?php endif; ?> 
</div>
<br />

<?php if (!empty($counter)): ?>

  <table class='admin_table'>
    <thead>
      <tr>
        <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" style="width:1%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>

        <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store Name'); ?></a></th>

        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate('Owner Name'); ?></a></th>

        <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');"><?php echo $this->translate('Amount'); ?></a></th>
        
        <th class='admin_table_short'><?php echo 'Message'; ?></th>
        <th class='admin_table_short'><?php echo "Payment" ?></th>
        <th class='admin_table_short'><?php echo "Gateway" ?></th>
        <th class='admin_table_short'><?php echo "Date" ?></th>
        <th class='admin_table_short'><?php echo "Options" ?></th>
      </tr>
    </thead>
    <?php foreach ($this->paginator as $transaction): ?>
      <tr>
        <td class='admin_table_short'><?php echo $transaction->transaction_id; ?></td>
        <td>
          <?php $sitestore = Engine_Api::_()->getItem('sitestore_store', $transaction->store_id); ?>
          <?php if( empty($sitestore) ) : ?>
            <i>Store Deleted</i>
          <?php else: ?>
            <?php echo $this->htmlLink($sitestore->getHref(), $this->string()->truncate($this->string()->stripTags($sitestore->getTitle()), 10), array('title' => $sitestore->getTitle(), 'target' => '_blank')) ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if( empty($sitestore) ) : ?>
            <i>-</i>
          <?php else: ?>
            <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($sitestore->getOwner()->getTitle()), 10), array('title' => $sitestore->getOwner()->getTitle(), 'target' => '_blank')) ?>
          <?php endif; ?>
        </td>
        <td class='admin_table_short'><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($transaction->amount) ?></td>
        <td class='admin_table_short' title='<?php echo $transaction->message ?>'>
          <?php echo empty($transaction->message) ? '-' : Engine_Api::_()->sitestoreproduct()->truncation($transaction->message, 25) ?>
        </td>
        <td class='admin_table_short'>
          <?php if( $transaction->state == 'okay' && $transaction->status == 'active' ) : ?>
            <?php echo 'Yes'; ?>
          <?php else: ?>
            <?php echo 'No'; ?>
          <?php endif; ?> 
        </td>
        <td>
           <?php echo Engine_Api::_()->sitestoreproduct()->getGatwayName($transaction->gateway_id); ?>
        </td>
        <td class='admin_table_short'><?php echo gmdate('M d,Y, g:i A',strtotime($transaction->date)) ?></td>
        <td class='admin_table_short'><?php echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'detail-order-commission-transaction', 'transaction_id' => $transaction->transaction_id, 'store_id' => $transaction->store_id, 'message' => $transaction->message), 'admin_default', true) . '\')">details</a>' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <br />
  <div>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    ?>
  </div>
  <br />
<?php endif; ?>
<style type="text/css">
  table.admin_table tbody tr td{
    white-space: nowrap;
  }
</style>

<script type="text/javascript">
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
</script>