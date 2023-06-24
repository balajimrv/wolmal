
<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Activitypoints/externals/scripts/activitypoints.js');

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


<br>
<br>

<div style="padding-left: 10px;">

  <div class='activitypoints_vault_balance'>
    <div class='activitypoints_vault_balance_title'>
      <strong>Collection Bridges</strong> <br>
    </div>
    
    <img class='activitypoints_vault_balance_coin' src="application/modules/Activitypoints/externals/images/userpoints_coins32.png">
    <div class='activitypoints_vault_balance_text'>
      <span id='userpoints_balance'><?php echo $this->collectionPoint ?></span>
    </div>
    <br>
    </div>

