<?php 
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.min.js');
?>

<?php echo $this->partial('_header.tpl', 'zephyrtheme', '') ?>

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

<script>
  jQuery(document).ready(function($){
	$('#tab1').show();  
	$('.tabs_lp > ul.navigation').each(function(){
	  var $active, $content, $links = $(this).find('a');
	  $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
	  $active.parent().addClass('active');
	  $content = $($active.attr('href'));
	  $links.not($active).each(function () {
		  $($(this).attr('href')).hide();
	  });
	  $(this).on('click', 'a', function(e){
		  $active.parent().removeClass('active');
		  $content.hide();
		  $active = $(this);
		  $content = $($(this).attr('href'));
		  $active.parent().addClass('active');
		  $content.show();
		  e.preventDefault();
	  });
	});
  });
</script>