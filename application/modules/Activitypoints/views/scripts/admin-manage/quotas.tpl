
<h2>
  <?php echo $this->translate("Activity Points") ?> &raquo; 
  &nbsp;<a href="<?php echo $this->url(array('module' => 'activitypoints', 'controller' => 'manage'),'admin_default', true) ?>"><?php echo $this->translate("Members") ?></a> &raquo; 
  &nbsp;<a href="<?php echo $this->user->getHref() ?>"><?php echo $this->user->getTitle() ?></a> (<?php echo $this->user->username ?>)
</h2>

<br>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("ACTIVITYPOINTS_ADMIN_QUOTAS_DESCRIPTION") ?>
</p>

<br />


<script>
function up_edit_inplace(id) {
  document.getElementById("upshow_"+id).style.display='none';
  document.getElementById("upedit_"+id).style.display='block';
  document.getElementById("upinput_"+id).focus();
}
</script>

<style>
table.tabs {
	margin-bottom: 12px;
}
td.tab {
	background: #FFFFFF;
	padding-left: 1px;
	border-bottom: 1px solid #CCCCCC;
}
td.tab0 {
	font-size: 1pt;
	padding-left: 7px;
	border-bottom: 1px solid #CCCCCC;
}
td.tab1 {
	border: 1px solid #CCCCCC;
	border-top: 3px solid #AAAAAA;
	border-bottom: none;
	font-weight: bold;
	padding: 6px 8px 6px 8px;
}
td.tab2 {
	background: #F8F8F8;
	border: 1px solid #CCCCCC;
	border-top: 3px solid #CCCCCC;
	font-weight: bold;
	padding: 6px 8px 6px 8px;
}
td.tab3 {
	background: #FFFFFF;
	border-bottom: 1px solid #CCCCCC;
	padding-right: 12px;
	width: 100%;
	text-align: right;
	vertical-align: middle;
}

.tabs A {
  text-decoration: none;
}

.tabs A:hover {
  text-decoration: underline;
}

td.result {
	font-weight: bold;
	text-align: center;
	border: 1px dashed #CCCCCC;
	background: #FFFFFF;
	padding: 7px 8px 7px 7px;
}
td.error {
	font-weight: bold;
	color: #FF0000;
	text-align: center;
	padding: 7px 8px 7px 7px;
	background: #FFF3F3;
}
td.success {
	font-weight: bold;
	padding: 7px 8px 7px 7px;
	background: #f3fff3;
}
</style>

<br>

<br>

  <?php for($action_loop = 0; $action_loop < count($this->actions); $action_loop++) : ?>
  
  <div style="font-weight: bold; Xwidth: 600px; text-align: center; margin-bottom: 15px"> <?php echo $this->action_types[$action_loop] ?> </div>
  <table class='admin_table' cellpadding='0' cellspacing='0' Xstyle="width:700px;" width='100%'>
  <thead>
  <tr>
  <th class='header' width="100%"><?php echo $this->translate('100016524') ?></th>
  <th class='header'><?php echo $this->translate('100016525') ?></th>
  <th class='header'><?php echo $this->translate('100016529') ?></th>
  <th class='header' nowrap='nowrap'><?php echo $this->translate('100016530') ?></th>

  <th class='header' nowrap='nowrap'><?php echo $this->translate('100016532') ?></th>
  <th class='header' nowrap='nowrap'><?php echo $this->translate('100016533') ?></th>
  <th class='header' nowrap='nowrap'><?php echo $this->translate('100016534') ?></th>
  </tr>
  </thead>
  
  
  <?php for($action_gloop = 0; $action_gloop < count($this->actions[$action_loop]); $action_gloop++) : ?>
    <tr class=''>
    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['action_name'] ?>&nbsp;</td>
    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['action_points'] ?></td>
    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['action_pointsmax'] ?></td>
    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['action_rolloverperiod'] ?> <?php echo $this->translate('100016531') ?></td>

    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['userpointcounters_amount'] ?></td>
    <td class='item'><?php echo $this->actions[$action_loop][$action_gloop]['userpointcounters_cumulative'] ?></td>
    <td class='item'>
	  <div style='width:130px'>
	  <?php if ($this->actions[$action_loop][$action_gloop]['userpointcounters_lastrollover']): ?> <?php echo $this->timestamp( $this->actions[$action_loop][$action_gloop]['userpointcounters_lastrollover']) ?>
	  <?php else: ?>
	  <?php echo $this->translate('100016840') ?>
	  <?php endif; ?>
	  <?php if ($this->actions[$action_loop][$action_gloop]['userpointcounters_amount'] > 0) : ?>
	  (<a href="<?php echo $this->url(array('module' => 'activitypoints', 'controller' => 'manage', 'action' => 'quotas', 'id' => $this->user->getIdentity(), 'task' => 'reset', 'action_type' => $this->actions[$action_loop][$action_gloop]['action_type'] ),'admin_default', true) ?>"><?php echo $this->translate('reset now') ?></a>)
	  <?php endif; ?>
	  </div>
	</td>
    </tr>
  <?php endfor; ?>
    
  
  </table>

  <br><br>

  <?php endfor; ?>

<br><br>
  
