<!-- START - Menu Bar Widget -->
<div class="header_6 layout_theme_header_menu_bar <?php echo (!$this->viewer->getIdentity()) ? "header_menu_visitors" : "header_menu_members"; ?> <?php if(!$this->viewer->getIdentity() && $this->header_mainmenu == 0){ echo "no_menu"; }; ?> clearfix">

  <div class="layout_core_menu_container">
  
	<?php include 'mini_menu.php'; ?>
    
    <?php include 'logo.php'; ?>

    <?php include 'search.php'; ?>
    
    <?php include 'main_menu.php'; ?>
  
  </div>
  
  <?php include 'popup_login.php'; ?>
  
</div>
<!-- END - Menu Bar Widget -->

<?php include 'mobile_menu.php'; ?>


<script type='text/javascript'>
window.addEvent('domready', function() {
	function footerPosition() {
		var windowHeight = window.getSize().y;
		var wrapperHeight = $('global_wrapper').getSize().y;
		var footerHeight = $('global_footer').getSize().y;
		var sum = windowHeight - wrapperHeight - footerHeight;
		if(windowHeight > wrapperHeight + footerHeight) {
			$('global_footer').setStyles({'margin-top': sum});
		}
	};
	footerPosition();
	window.addEvent('resize', function() {
		footerPosition();
	});
});
</script>
