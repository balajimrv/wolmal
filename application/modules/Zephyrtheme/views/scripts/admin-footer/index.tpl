<?php 
    $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/admin-header.js');

    echo $this->partial('_header.tpl', 'zephyrtheme', '');
?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<div class="clear">
  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>
</div>
