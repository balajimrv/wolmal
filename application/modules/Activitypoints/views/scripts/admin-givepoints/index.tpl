
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
function userpoints_gp_onchange(elem) {
  SEMods.B.hide("div1","div2","div3");
  SEMods.B.show("div"+elem.value);
}
function from_user_id_suggest_onChanged(value) {
  if(value == 0) {
    document.getElementById("user_id_suggest_other").style.display = "inline";
  }
  else {
    SEMods.B.hide("user_id_suggest_other");
  }
}
</script>


<?php if ($this->result != 0): ?>
<div class='success'> <?php echo $this->translate('100016616') ?></div>
<?php endif; ?>

<?php if ($this->is_error != 0): ?>
<div class='error'> <?php echo $this->translate($this->error_message) ?> </div>
<?php endif; ?>

<div class="settings">

<div id="admin_settings_form" class="global_form">
  
<form class="global_form" action="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'givepoints'), 'admin_default') ?>" method="POST">
<div>
<div>
  
  <h3><?php echo $this->translate('100016617') ?></h3>
  <p class="form-description"> <?php echo $this->translate('100016615') ?> </p>

  <?php if ($this->sent == 1): ?>
  <ul class="form-notices"><li><?php echo $this->translate("100016616"); ?></li></ul>
  <?php endif; ?>

<table cellpadding='0' cellspacing='0' width='700'>
<td class='setting1'>

  <table cellpadding='0' cellspacing='0'>
  <tr>
    <td width="180" style='font-size: 10pt; font-weight: bold'> <?php echo $this->translate('100016618') ?> </td>
    <td>
      <select name="sendtotype" id="sendtotype" onchange="userpoints_gp_onchange(this)">
      <option value="0" <?php if ($this->sendtotype == 0): ?>SELECTED<?php endif; ?>><?php echo $this->translate('100016820') ?></option>
      <option value="1" <?php if ($this->sendtotype == 1): ?>SELECTED<?php endif; ?>><?php echo $this->translate('100016821') ?></option>
      <option value="2" <?php if ($this->sendtotype == 2): ?>SELECTED<?php endif; ?>><?php echo $this->translate('100016822') ?></option>
      <option value="3" <?php if ($this->sendtotype == 3): ?>SELECTED<?php endif; ?>><?php echo $this->translate('100016823') ?></option>
      </select>
    </td>
    <td>
      <div id="div1" <?php if ($this->sendtotype != 1): ?>style="display:none"<?php endif; ?>>&nbsp; <?php echo $this->translate('100016824') ?> &nbsp;<select class='text' name='level'><?php foreach($this->levels as $level_item) : ?><option value='<?php echo $level_item->level_id ?>'<?php if ($this->level == $level_item->level_id): ?> SELECTED<?php endif; ?>><?php echo $level_item->getTitle() ?></option><?php endforeach; ?></select></div>
      <div id="div2" <?php if ($this->sendtotype != 2): ?>style="display:none"<?php endif; ?>>&nbsp; <?php echo $this->translate('100016825') ?> <select class='text' name='subnet'><?php foreach($this->subnets as $subnet_item) : ?><option value='<?php echo $subnet_item->network_id ?>'<?php if ($this->subnet == $subnet_item->network_id): ?> SELECTED<?php endif; ?>><?php echo $subnet_item->getTitle() ?></option><?php endforeach; ?></select></div>
      <div id="div3" <?php if ($this->sendtotype != 3): ?>style="display:none"<?php endif; ?>>&nbsp; <?php echo $this->translate('100016826') ?> <input type='text' class='text' name='username' value='<?php echo $this->username ?>'></div>
    </td>
  </tr>
  <tr>
    <td width="50" style="padding-top: 10px; font-size: 10pt; font-weight: bold"> <?php echo $this->translate('100016827') ?> </td>
    <td style="padding-top: 10px"> <input type='text' class='text' name='points' size=5 value=<?php echo $this->points ?>></td>
  </tr>
  <tr>
    <td width="50"> &nbsp;</td>
    <td style="padding-top: 2px"> <span style="color: #BBB"> <?php echo $this->translate('100016626') ?> </span> </td>
  </tr>
<tr>
 <td width="50" style="padding-top: 10px; font-size: 10pt; font-weight: bold"> <?php echo 'Description' ?> </td>
    <td style="padding-top: 10px"> <input type='text' class='text' name='points' size=5 value='Shopping from' style="width: 500px;"></td>
</tr>
  
  </table>
  <br>
    
</td>
</tr>

  </table>
  <br>
</td>
</tr>

<tr>
<td class='setting2'>

<div class="form-wrapper" style='padding: 0px'>&nbsp; </div>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td width="180" style='font-size: 10pt; font-weight: bold'> &nbsp; </td>
  <td valign="top"><input type='checkbox' name='send_message' id='send_message' value='1'<?php if ($this->send_message == 1): ?> checked='checked'<?php endif; ?>></td>
  <td><label for='send_message'><?php echo $this->translate('100016622') ?></label></td>
  </tr>
  </table>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td width="180" style='font-size: 10pt; font-weight: bold; padding: 15px 0px'><?php echo $this->translate('100016864') ?></td>
  <td>
    <select name="from_user_id_suggest" onchange="from_user_id_suggest_onChanged(this.value)">
      <?php foreach($this->from_users_suggest as $user_suggest): ?>
      <option value="<?php echo $user_suggest->user_id ?>" <?php if ($this->from_user_id_suggest == $user_suggest->user_id): ?>SELECTED<?php endif; ?>> <?php echo $user_suggest->getTitle() ?> </option>
      <?php endforeach; ?>
      <option value="0" <?php if ($this->from_user_id_suggest == 0 AND $this->from_user_id_suggest != ''): ?>SELECTED<?php endif; ?>> <?php echo $this->translate('100016865') ?> </option>
    </select>
    <span id='user_id_suggest_other' style="padding-left: 5px; display: <?php if (($this->from_user_id_suggest == 0 AND $this->from_user_id != "") OR count($this->from_users_suggest) == 0): ?>inline<?php else: ?>none<?php endif; ?>">
      <?php echo $this->translate('100016866') ?> <input type='text' class='text' size='30' name='from_user_id' value='<?php echo $this->from_user_id ?>'>
    </span>
  </td>
  </tr>
  <tr>
  <td width="180" style='font-size: 10pt; font-weight: bold'; padding: 15px 0px><?php echo $this->translate('100016619') ?></td>
  <td><input style='width: 400px' type='text' class='text' size='30' name='subject' value='<?php echo $this->subject ?>' maxlength='200'></td>
  </tr>
  <tr>
  <td width="180" style='font-size: 10pt; font-weight: bold; padding: 15px 0px' valign='top'><?php echo $this->translate('100016620') ?></td>
  <td style='padding: 15px 0px'><textarea style='width: 400px' rows='6' cols='80' class='text' name='message'><?php echo $this->message ?></textarea></td>
  </tr>
  <tr>
  <td width="180" style='font-size: 10pt; font-weight: bold'>&nbsp;</td>
  <td>
    
    <span> <?php echo $this->translate('100016867') ?> </span>
    
  </td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<button type='submit' class='button'><?php echo $this->translate('100016621') ?></button>
<input type='hidden' name='task' value='dogivepoints'>

</div>
</div>
</form>
</div>
</div>
