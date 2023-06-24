
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
function addPointsRow() {
    var el = document.getElementById("pointsrow").cloneNode(true);
    el.id = '';
    var moreRow = document.getElementById("addmorerow");
    moreRow.parentNode.insertBefore(el, moreRow)
}
</script>
<style>
A.addmorerow {
  text-decoration: none;
  font-size: 10pt;
}

A.addmorerow:hover {
  text-decoration: underline;
}
</style>

<div class="settings">

<div id="admin_settings_form" class="global_form">

<form class="global_form" action='<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'pointranks'), 'admin_default') ?>' method='POST'>
<input type='hidden' name='point_ranks_count' value='{$point_ranks_count}'>
<input type='hidden' name='task' value='dosave'>

<div>
<div>
  
  <h3><?php echo $this->translate('Points Ranking') ?></h3>
  <!-- <p class="form-description"> <?php echo $this->translate('100016130') ?> </p> -->

  <?php if ($this->result != 0): ?>
  <ul class="form-notices"><li><?php echo $this->translate("Your changes have been saved."); ?></li></ul>
  <?php endif; ?>
  

<div class="form-elements">
    
  <div class="form-wrapper">
    <div class="form-label">
        <label><?php echo $this->translate('100016131') ?></label>
    </div>
    <div class="form-element">
      <p class="description"><?php echo $this->translate('100016132') ?></p>
  
      <ul class="form-options-wrapper">
        <li>
          <input type="radio" <?php if ($this->setting_userpoints_enable_pointrank == 1): ?>checked="checked"<?php endif; ?> value="1" id="setting_userpoints_enable_pointrank-1" name="setting_userpoints_enable_pointrank">
            <label for="setting_userpoints_enable_pointrank-1"><?php echo $this->translate('100016133') ?></label>
        </li>
        <li>
          <input type="radio" <?php if ($this->setting_userpoints_enable_pointrank == 0): ?>checked="checked"<?php endif; ?> value="0" id="setting_userpoints_enable_pointrank-0" name="setting_userpoints_enable_pointrank">
          <label for="setting_userpoints_enable_pointrank-0"><?php echo $this->translate('100016134') ?></label>
        </li>
      </ul>
    </div>
  </div>

  <div class="form-wrapper">
    <div class="form-label">
        <label><?php echo $this->translate('Ranks are based on -') ?></label>
    </div>
    <div class="form-element">
      <p class="description"><?php echo $this->translate('Ranks can be based on total earned points or current points balance.') ?></p>
  
      <ul class="form-options-wrapper">
        <li>
          <input type="radio" <?php if ($this->setting_userpoints_ranktype == 0): ?>checked="checked"<?php endif; ?> value="0" id="setting_userpoints_ranktype-0" name="setting_userpoints_ranktype">
          <label for="setting_userpoints_ranktype-0"><?php echo $this->translate('Rank by Total Points Earned') ?> (<a href="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'help'), 'admin_default') ?>?show=10">?</a>)</label>
        </li>
        <li>
          <input type="radio" <?php if ($this->setting_userpoints_ranktype == 1): ?>checked="checked"<?php endif; ?> value="1" id="setting_userpoints_ranktype-1" name="setting_userpoints_ranktype">
            <label for="setting_userpoints_ranktype-1"><?php echo $this->translate('Rank by Current Points Balance') ?> (<a href="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'help'), 'admin_default') ?>?show=11">?</a>) </label>
        </li>
      </ul>
    </div>
  </div>


<div class="form-wrapper">

  <div class="form-label">
      <label><?php echo $this->translate('100016135') ?></label>
  </div>
  <div class="form-element">
    <p class="description"><?php echo $this->translate('100016136') ?></p>

  <table cellpadding='0' cellspacing='0' width='600px'>
    <tr>
        <td class='setting2'>

    <table cellpadding='0' cellspacing='0' class='admin_table'>
    <thead>
    <tr><th> <?php echo $this->translate('100016137') ?> </th><th> <?php echo $this->translate('100016138') ?> </th></tr>
    </thead>

    <?php for($p_loop = 0; $p_loop < count($this->point_ranks); $p_loop++): ?>
    <tr>
    <td class='form1'> <input name="point_rank_points[]" <?php if ($p_loop == 0): ?> readonly style="color: #AAA; background-color: #CCC" <?php endif; ?> value="<?php echo $this->point_ranks[$p_loop]['userpointrank_amount'] ?>" type="text" class="text"> </td>
    <td class='form2'> <input name="point_rank_text[]" value="<?php echo $this->point_ranks[$p_loop]['userpointrank_text'] ?>" type="text" class="text"> </td>
    </tr>
    <?php endfor; ?>

    <tr id="addmorerow" name="addmorerow">
    <td style="padding-left: 10px"> <a class="addmorerow" href="" onclick="addPointsRow(); return false;"> <?php echo $this->translate('100016139') ?> </a> </td>
    <td>  </td>
    </tr>

    </table>


  </td></tr>
  
  </table>
  
  </div>

</div>


<div class="form-wrapper">
<button type='submit'><?php echo $this->translate('100016140') ?></button>
</div>


</div>
</div>
</div>
</form>
</div>
</div>


<div style="display:none">
  <table>
  <tr id="pointsrow" name="pointsrow">
  <td class='form1'> <input name="point_rank_points[]" value="" type="text" class="text"> </td>
  <td class='form2'> <input name="point_rank_text[]" value="" type="text" class="text"> </td>
  </tr>
  </table>
</div>
