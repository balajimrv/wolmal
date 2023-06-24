
<h2><?php echo $this->translate("Activity Points Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("100016573") ?>
</p>

<br />

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

function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected transactions?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function userpoints_growtable() {
  SEMods.B.toggle("userpoints_basic_table", "userpoints_full_table");
}

function confirm_transaction(tid) {
  var asyncform = document.getElementById('asyncform');
  document.getElementById('asyncform_task').value = "confirm";
  document.getElementById('asyncform_transaction_id').value = tid;
  
  asyncform.submit();
}

function cancel_transaction(tid) {
  var asyncform = document.getElementById('asyncform');
  document.getElementById('asyncform_task').value = "cancel";
  document.getElementById('asyncform_transaction_id').value = tid;
  
  asyncform.submit();
}

</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $memberCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s transaction found", "%s transaction found", $memberCount), ($memberCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  </div>
</div>

<br />

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'transactionsmodify'));?>" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('uptransaction_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('uptransaction_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Display Name") ?></a></th>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username') == 1) : ?>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate("Username") ?></a></th>
        <?php endif; ?>
        <th><?php echo $this->translate("Description") ?></th>
        <th><?php echo $this->translate("State") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('uptransaction_amount', 'ASC');"><?php echo $this->translate("Amount") ?></a></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><input name='transactions[]' value='<?php echo $item->uptransaction_id;?>' type='checkbox' class='checkbox'></td>
            <td><?php echo $item->uptransaction_id ?></td>
            <td class="nowrap"><?php echo $this->timestamp($item->uptransaction_date) ?></td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(),
                  $this->string()->truncate($this->item('user', $item->user_id)->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username') == 1) : ?>
            <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <?php endif; ?>
            <td><?php echo $item->uptransaction_text ?></td>
            <td>
            <?php echo $this->translate(100016024 + $item->uptransaction_state) ?>
            <?php if($item->uptransaction_state == 1): ?> ( <a href="javascript:confirm_transaction(<?php echo $item->uptransaction_id ?>)"><?php echo $this->translate('100016593') ?></a> | <a href="javascript:cancel_transaction(<?php echo $item->uptransaction_id ?>)"><?php echo $this->translate('100016594') ?></a> ) <?php endif; ?>
            </td>
            <td><?php echo $item->uptransaction_amount ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>


  <form method=POST id="asyncform" action="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'transactions', 'action' => 'index'), 'admin_default') ?>">
    <input type="hidden" id="asyncform_task" name="task">
    <input type="hidden" id="asyncform_transaction_id" name="transaction_id">
      
    <input type="hidden" name="f_title" value="<?php echo $this->formFilter->f_title->getValue() ?>">
    <input type="hidden" name="f_state" value="<?php echo $this->formFilter->f_state->getValue() ?>">
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username') == 1) : ?>
    <input type="hidden" name="f_user" value="<?php echo $this->formFilter->f_user->getValue() ?>">
    <?php endif; ?>
    <input type="hidden" name="f_displayname" value="<?php echo $this->formFilter->f_displayname->getValue() ?>">
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username') != 1) : ?>
    <input type="hidden" name="f_email" value="<?php echo $this->formFilter->f_email->getValue() ?>">
    <?php endif; ?>
    <input type="hidden" name="p" value="<?php echo $this->paginator->getCurrentPageNumber() ?>">
  </form>
