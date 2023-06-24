
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
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>

      <div class="global_form" id="admin_settings_form">
      <?php if ($this->form->saved_successfully): ?><h3 class="slowfade"><?php echo $this->translate("Settings were saved successfully.") ?></h3><?php endif; ?>
      
      <?php echo $this->form->render($this); ?>
      
      </div>

  </div>
</div>

