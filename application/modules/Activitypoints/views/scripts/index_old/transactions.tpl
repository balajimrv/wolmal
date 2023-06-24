
<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Activitypoints/externals/scripts/activitypoints.js')
?>

<?php if( count($this->navigation) ): ?>
<div class="headline">
  <h2>
	<?php echo $this->translate('Bridges');?>
  </h2>
  <div class="tabs">
	<?php
	  // Render the menu
	  echo $this->navigation()
		->menu()
		->setContainer($this->navigation)
		->render();
	?>
  </div>
</div>
<?php endif; ?>

<h2><?php echo $this->translate("100016685") ?></h2>

<p><?php echo $this->translate("100016686") ?></p>


<br />

<?php if (!empty($this->error_message)): ?>
  <table cellpadding=0 cellpadding=0 style="margin: 0px auto">
  <tr><td>
	<ul class="form-errors">
	  <li> <?php echo $this->translate($this->error_message) ?> </li>
	</ul>
  </td>
  </tr>
  </table>
  <br>
<?php endif; ?>

<?php if (!empty($this->success_message)): ?>
  <table cellpadding=0 cellpadding=0 style="margin: 0px auto">
  <tr><td>
	<ul class="form-notices">
	  <li> <?php echo $this->translate($this->success_message) ?> </li>
	</ul>
  </td>
  </tr>
  </table>
  <br>
<?php endif; ?>


<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class='activitypoints_transactions_search_wrapper'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='activitypoints_transactions_results'>
  <div>
    <?php $tCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s transaction found", "%s transaction found", $tCount), ($tCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />

  <table class='activitypoints_transactions_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('uptransaction_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
        <th><?php echo $this->translate("Description") ?></th>
        <th><?php echo $this->translate("Status") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('uptransaction_amount', 'ASC');"><?php echo $this->translate("Amount") ?></a></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): if($item->uptransaction_amount == 0) continue; ?>
          <tr>
            <td><?php echo $this->timestamp($item->uptransaction_date) ?></td>
            <td><?php echo $item->uptransaction_text ?></td>
            <td><?php echo $this->translate(100016024 + $item->uptransaction_state) ?></td>
            <td><?php echo $item->uptransaction_amount ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
