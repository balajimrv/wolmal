<!-- START - Menu Bar Widget -->
<div class="header_3 layout_theme_header_menu_bar <?php echo (!$this->viewer->getIdentity()) ? "header_menu_visitors" : "header_menu_members"; ?> <?php if(!$this->viewer->getIdentity() && $this->header_mainmenu == 0){ echo "no_menu"; }; ?> clearfix">

  <div class="layout_core_menu_container">
  
  	<?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) { ?> 
    <div class="header_Top">
	  <?php include 'main_menu.php'; ?>
    </div>
    <?php } ?>
  
    <div class="header_Bottom">
    
      <?php include 'logo.php'; ?>
      
	  <?php include 'search.php'; ?>

	  <?php include 'mini_menu.php'; ?>

    </div>
  
  </div>
  
  <?php include 'popup_login.php'; ?>
  
</div>
<!-- END - Menu Bar Widget -->

<?php include 'mobile_menu.php'; ?>