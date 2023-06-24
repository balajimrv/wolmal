
<script>
function semods_add_row(id) {
  var el = document.getElementById(id+'_template').cloneNode(true);
  el.id = '';
  var moreRow = document.getElementById(id+"_addmorerow");
  moreRow.parentNode.insertBefore(el, moreRow)
}
</script>

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


<script>
function up_edit_inplace(id) {
  document.getElementById("upshow_"+id).style.display='none';
  document.getElementById("upedit_"+id).style.display='block';
  document.getElementById("upinput_"+id).focus();
}
</script>

<?php echo $this->translate('100016536') ?>

<br><br>

<?php if ($this->result != 0): ?>

  <?php if (empty($this->error)): ?>
  <div class='success'> <?php echo $this->translate('100016540') ?></div>
  <?php else: ?>
    <div class='error'> <?php echo $this->error ?> </div>
  <?php endif; ?>

<?php endif; ?>

<br>

<form action='<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'assignpoints'), 'admin_default') ?>' method='POST'>

  
  <?php for($action_loop = 0; $action_loop < count($this->actions); $action_loop++) : ?>
  
  <div style="font-weight: bold; width: 600px; text-align: center; padding-bottom: 2px"> <?php echo $this->action_types[$action_loop] ?></div>
  <table cellpadding='0' cellspacing='0' class='admin_table' style="width:600px;" Xwidth='100%'>
  <thead>
  <tr>
  <th class='header'><?php echo $this->translate('100016537') ?></th>
  <th class='header'><?php echo $this->translate('100016538') ?></th>
  <th class='header'><?php echo $this->translate('100016542') ?></th>
  <th class='header'><?php echo $this->translate('100016543') ?></th>
  </tr>
  </thead>
  
  
  <?php for($action_gloop = 0; $action_gloop < count($this->actions[$action_loop]); $action_gloop++) : ?>

  <?php if ((is_null($this->actions[$action_loop][$action_gloop]['type']) && !is_null($this->actions[$action_loop][$action_gloop]['action_requiredplugin']))): ?>
  <?php $unavailable = true; ?>
  <?php else: ?>
  <?php $unavailable = false; ?>
  <?php endif; ?>
    <tr>
    <td class='item'>  <div id='upedit_<?php echo $action_loop ?>_<?php echo $action_gloop ?>' style="display:none;width:260px"><input id='upinput_<?php echo $action_loop ?>_<?php echo $action_gloop ?>' <?php if ($unavailable): ?>disabled<?php endif; ?> type='text' class='text' size=40 name='actionsname[<?php echo $this->actions[$action_loop][$action_gloop]['action_id'] ?><?php echo $this->actions[$action_loop][$action_gloop]['type'] ?>]' value='<?php echo $this->actions[$action_loop][$action_gloop]['action_name'] ?>'></div>  <div id='upshow_<?php echo $action_loop ?>_<?php echo $action_gloop ?>' style="display:block;width:260px" <?php if (!$unavailable): ?>onclick="up_edit_inplace('<?php echo $action_loop ?>_<?php echo $action_gloop ?>')"<?php endif; ?>> <?php echo $this->actions[$action_loop][$action_gloop]['action_name'] ?>&nbsp;<?php if ($unavailable): ?><br><font color="red"> <?php echo $this->translate('100016541') ?> <?php echo $this->actions[$action_loop][$action_gloop]['action_requiredplugin'] ?></font> <?php endif; ?> </div></td>
    <td class='item' width="20px"><input <?php if ($unavailable): ?>disabled<?php endif; ?>  type='text' class='text' size=5 name='actions[<?php echo $this->actions[$action_loop][$action_gloop]['action_id'] ?><?php echo $this->actions[$action_loop][$action_gloop]['type'] ?>]' value='<?php echo $this->actions[$action_loop][$action_gloop]['action_points'] ?>'></td>
    <td class='item' width="20px"><input <?php if ($unavailable): ?>disabled<?php endif; ?>  type='text' class='text' size=5 name='actionsmax[<?php echo $this->actions[$action_loop][$action_gloop]['action_id'] ?><?php echo $this->actions[$action_loop][$action_gloop]['type'] ?>]' value='<?php echo $this->actions[$action_loop][$action_gloop]['action_pointsmax'] ?>'></td>
    <td class='item' width="100px"><input <?php if ($unavailable): ?>disabled<?php endif; ?>  type='text' class='text' size=5 name='actionsrollover[<?php echo $this->actions[$action_loop][$action_gloop]['action_id'] ?><?php echo $this->actions[$action_loop][$action_gloop]['type'] ?>]' value='<?php echo $this->actions[$action_loop][$action_gloop]['action_rolloverperiod'] ?>'> <?php echo $this->translate('100016544') ?></td>
    </tr>
  <?php endfor; ?>
    
  
  </table>

  <br><br>

  <?php endfor; ?>


<button type='submit' class='button'><?php echo $this->translate('100016539') ?></button>
<input type='hidden' name='task' value='dosave'>
</form>

<br><br>
