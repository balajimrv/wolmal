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
      <li class="active">
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'index'), $this->translate('Products - Order Related Transactions'), array())
        ?>
      </li>		
      <li>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'admin-transaction'), $this->translate('Products - Payments to Sellers')) ?>
      </li>
    <?php else: ?>
      <li>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'order-commission-transaction'), $this->translate('Products - Order Commission Related Transactions')) ?>
      </li>
    <?php endif; ?>
  </ul>
</div>

<div class='settings clr'>
  <h3><?php echo $this->translate("Products - Order Related Transactions"); ?></h3>
  <p class="description">
    <?php echo $this->translate('Browse through the transactions made by users for products. The search box below will search through the buyer names, transaction date, amount, gateway and state. You can also use the filters below to filter the transactions.'); ?>
  </p>
</div>

<br style="clear:both;" />
<div class="admin_search sitestoreproduct_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <input type="hidden" name="post_search" /> 
      <div>
        <label>
          <?php echo $this->translate("Buyer Name") ?>
        </label>
        <?php if (empty($this->username)): ?>
          <input type="text" name="username" /> 
        <?php else: ?>
          <input type="text" name="username" value="<?php echo $this->translate($this->username) ?>"/>
        <?php endif; ?>
      </div>


      <div>
        <label>
          <?php echo $this->translate("Transaction Date: ex (2000-12-25)") ?>
        </label>
        <?php if (empty($this->date)): ?>
          <input type="text" name="date" /> 
        <?php else: ?>
          <input type="text" name="date" value="<?php echo $this->translate($this->date) ?>"/>
        <?php endif; ?>
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

      <div>
        <label>
          <?php echo $this->translate("Gateway") ?>	
        </label>
        <select id="" name="gateway_id">
          <option value="0" ></option>
          <option value="1" <?php if ($this->gateway_id == 1) echo "selected"; ?> ><?php echo $this->translate("2Checkout") ?></option>
          <option value="2" <?php if ($this->gateway_id == 2) echo "selected"; ?> ><?php echo $this->translate("PayPal") ?></option>
          <option value="3" <?php if ($this->gateway_id == 3) echo "selected"; ?> ><?php echo $this->translate("By Cheque") ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo $this->translate("State") ?>	
        </label>
        <select id="" name="state">
          <option value="0" ></option>
          <option value="failed" <?php if ($this->state == "failed") echo "selected"; ?> ><?php echo $this->translate("Failed") ?></option>
          <option value="okay" <?php if ($this->state == "okay") echo "selected"; ?> ><?php echo $this->translate("Okay") ?></option>
          <option value="pending" <?php if ($this->state == "pending") echo "selected"; ?> ><?php echo $this->translate("Pending") ?></option>

          <?php //if( !empty($this->transaction_state) ) : ?>
          <?php //foreach( $this->transaction_state as $state ) : ?>
<!--              <option value="<?php //echo $state  ?>" <?php //if( $this->state == "$state" ) echo "selected"; ?> ><?php //echo $this->translate("%s", ucfirst($state))  ?></option>-->
          <?php //endforeach; ?>
          <?php //endif; ?>
        </select>
      </div>

      <div style="margin-top: 17px;">
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

  <!--      <th class='admin_table_short'><?php //echo $this->translate("Store Name")  ?></th>-->
        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Buyer Name'); ?></a></th>

        <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'DESC');"><?php echo $this->translate('Gateway'); ?></a></th>

        <th class='admin_table_short'><?php echo $this->translate("Type") ?></th>

        <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');"><?php echo $this->translate('State'); ?></a></th>

        <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');"><?php echo $this->translate('Amount'); ?></a></th>

        <th class='admin_table_short'><?php echo $this->translate("Date") ?></th>
        <th class='admin_table_short'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <?php
    $order_address_table_obj = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
    foreach ($this->paginator as $transaction):
      $gateway_name = Engine_Api::_()->sitestoreproduct()->getGatwayName($transaction->gateway_id);
      $amount = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($transaction->amount);
      ?>
      <tr>
        <td class='admin_table_short'><?php echo $transaction->transaction_id; ?></td>
        <td class='admin_table_short'>
          <?php
          if (empty($transaction->user_id)) :
            $billing_address_obj = $order_address_table_obj->getAddress($transaction->parent_order_id);
            echo $billing_address_obj->f_name . ' ' . $billing_address_obj->l_name . ' (Guest)';
          else:
            echo $this->htmlLink($transaction->getOwner()->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($transaction->getOwner()->getTitle(), 10), array('title' => $transaction->getOwner()->getTitle(), 'target' => '_blank'));
          endif;
          ?>
        </td>
        <td class='admin_table_short'><?php echo $gateway_name ?></td>
        <td class='admin_table_short'><?php echo $this->translate(ucfirst($transaction->type)) ?></td>
        <td class='admin_table_short'><?php echo $this->translate(ucfirst($transaction->state)) ?></td>
        <td class='admin_table_short'><?php echo $amount ?></td>
        <td class='admin_table_short'><?php echo gmdate('M d,Y, g:i A', strtotime($transaction->date))//$this->locale()->toDateTime($transaction->date) ?></td>
        <td class='admin_table_short'><?php echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'detail-user-transaction', 'transaction_id' => $transaction->transaction_id), 'admin_default', true) . '\')">details</a>' ?></td>
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