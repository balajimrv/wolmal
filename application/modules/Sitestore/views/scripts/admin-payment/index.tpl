<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)) {
?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php } ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'payment','action'=>'index'), $this->translate('Stores - Package Related Transactions'), array())
    ?>
    </li>
    <?php if( empty($this->directPaymentEnable) ) : ?>
      <li>
      <?php
        echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'transaction','action'=>'index'), $this->translate('Products - Order Related Transactions'), array())
      ?>
      </li>		
      <li>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'admin-transaction'), $this->translate('Products - Payments to Sellers')) ?>
      </li>
    <?php else: ?>
      <li>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'transaction', 'action' => 'order-commission-transaction'), $this->translate('Products - Commission Related Transactions')) ?>
      </li>
    <?php endif; ?>
  </ul>
</div>

<h3><?php echo $this->translate("Stores - Package Related Transactions") ?></h3>
<p>
  <?php echo $this->translate("Browse through the transactions made by users for creating stores. The search box below will search through the user names, store titles, emails, transaction IDs and order IDs. You can also use the filters below to filter the transactions.") ?>
</p>

<br />

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php return; endif; ?>
  <?php if(Engine_Api::_()->sitestore()->enablePaymentPlugin()):?>
<?php  if (!Engine_Api::_()->sitestore()->hasPackageEnable()): ?>
  <div class="tip">
    <span >     
      <?php echo $this->translate("This transaction only for packages.") ?>
    </span>
  </div>
<?php endif; ?>

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>

  <br />


<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>


<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->filterValues,
      'storeAsQuery' => true,
    )); ?>
  </div>
</div>

<br />


<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
            <?php echo $this->translate("ID") ?>
          </a>
        </th>
         <?php $class = ( $this->order == 'store_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('store_id', 'ASC');">
            <?php echo $this->translate("Store Id") ?>
          </a>
        </th>
         <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
            <?php echo $this->translate("Store Title") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
            <?php echo $this->translate("User Name") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'ASC');">
            <?php echo $this->translate("Gateway") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'type' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('type', 'DESC');">
            <?php echo $this->translate("Type") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
            <?php echo $this->translate("State") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
            <?php echo $this->translate("Amount") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'timestamp' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
            <?php echo $this->translate("Date") ?>
          </a>
        </th>
        <th style='width: 1%;' class='admin_table_options'>
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ):
        $user = @$this->users[$item->user_id];
        $order = @$this->orders[$item->order_id];
        $gateway = @$this->gateways[$item->gateway_id];
        ?>
        <tr>
          <td><?php echo $item->transaction_id ?></td>
           <td><?php echo $item->store_id ?></td>
           <td>
         	<a href="<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($item->store_id)), 'sitestore_entry_view') ?>"  target='_blank' title="<?php echo  ucfirst($item->title) ?>">
						<?php echo $this->translate(Engine_Api::_()->sitestore()->truncation($item->getTitle(),25)) ?></a>
           </td>
           <td class='admin_table_bold'>
            <?php echo ( $user ? $user->__toString() : '<i>' . $this->translate('Deleted or Unknown Owner') . '</i>' ) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ( $gateway ? $gateway->title : '<i>' . $this->translate('Unknown Gateway') . '</i>' ) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $this->translate(ucfirst($item->type)) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $this->translate(ucfirst($item->state)) ?>
          </td>
          <td class='admin_table_centered'>
              <?php echo $this->locale()->toCurrency($item->amount,Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'));?>
            <?php echo $this->translate('(%s)', $item->currency) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $this->locale()->toDateTime($item->timestamp) ?>
          </td>
          <td class='admin_table_options'>
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id));?>'>
              <?php echo $this->translate("details") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php endif; ?>
