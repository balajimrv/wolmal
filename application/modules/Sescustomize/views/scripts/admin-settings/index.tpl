<?php

?>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<div class='sesbasic-form sesbasic-categories-form'>
	<div>
		<div class='sesbasic-form-cont'>
			<div class='clear'>
				<div class='settings sesbasic_admin_form'>
					<?php echo $this->form->render($this); ?>
				</div>
			</div>
		</div>
	</div>
</div>
