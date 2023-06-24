<h2>
  <?php echo $this->translate('Timeline Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class="cover-tab">
    <h3><?php echo $this->translate('Current cover') ?></h3>
    <?php if ($this->hasAdminCover)
             echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'timeline', 'controller' => 'settings', 'action' => 'reset-cover'), $this->translate('Reset Cover'), array('class' => 'smoothbox reset_cover_btn')) ?>
    <p><?php echo $this->translate('You can change default profile cover.')?></p>
    <img alt="Timeline Cover" style="max-height: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('cover_height', '250') ?>px !important;"  src="<?php echo $this->cover ?>" id="timeline_cover" />    
    <div class="upload-cover-wrapper settings">
    	<?php echo $this->cover_form->render($this); ?>
  	</div>
  </div>
</div>
