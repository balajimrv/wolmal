<h2> <?php echo $this->translate("Total Eb/BB values") ?> </h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php

foreach($this->full_bridges as $x => $value) {
    $total_bb = $this->full_bridges[$x]['total_full_bb'];
    $total_cb = $this->full_bridges[$x]['total_full_cb'];
    $previous_db = $this->full_bridges[$x]['total_full_db'];
    $creation_date = $this->full_bridges[$x]['creation_date'];
    
    $monthYear = date('m-Y', strtotime($creation_date));
    $dateMonth = date('Y-m', strtotime($creation_date));
    
    $valueRs = Engine_Api::_()->sescustomize()->getValue($monthYear);
    $bridges_value = $valueRs['value'];
    
      
    $EB_value = $EB_value + (($total_bb*$bridges_value) + ($total_cb*$bridges_value) + ($previous_db*$bridges_value));
    $totalBB = $totalBB+$total_bb;
}

$totalEB = ($EB_value - $this->redeemCount);

?>
<b style="font-weight: bold;font-size: 26px;">Total EB count: <?php echo $totalEB; ?></b><br>
<b style="font-weight: bold;font-size: 26px;">Total BB count: <?php echo $totalBB; ?></b>
