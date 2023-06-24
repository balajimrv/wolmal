<!-- START - Menu Bar Widget -->
<div class="header_4 layout_theme_header_menu_bar <?php echo (!$this->viewer->getIdentity()) ? "header_menu_visitors" : "header_menu_members"; ?> <?php if(!$this->viewer->getIdentity() && $this->header_mainmenu == 0){ echo "no_menu"; }; ?> clearfix">

  <div class="layout_core_menu_container">
  
    <div class="header_Top"><div class="width_main">
    
	  <?php include 'search.php'; ?>
      
	  <?php include 'mini_menu.php'; ?>

    </div></div>
    
    <div class="header_Middle <?php if( !empty($this->header_banner) ) { echo 'has_banner'; } ?>">
      
      <?php include 'logo.php'; ?>
      
      <?php if( !empty($this->header_banner) ) { ?>
      <div class="header_bannerad">
        <?php echo $this->header_banner; ?>
      </div>
      <?php } ?>
    </div>
    
    <?php if($this->viewer->getIdentity() || $this->header_mainmenu == 1) { ?> 
    <div class="header_Bottom">
	  <?php include 'main_menu.php'; ?>
    </div>
    <?php } ?>
  
  </div>
  
  <?php include 'popup_login.php'; ?>
  
</div>
<!-- END - Menu Bar Widget -->

<?php include 'mobile_menu.php'; ?>