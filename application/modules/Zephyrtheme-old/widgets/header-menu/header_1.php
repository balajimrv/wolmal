<!-- START - Menu Bar Widget -->
<div class="header_1 layout_theme_header_menu_bar <?php echo (!$this->viewer->getIdentity()) ? "header_menu_visitors" : "header_menu_members"; ?> <?php if(!$this->viewer->getIdentity() && $this->header_mainmenu == 0){ echo "no_menu"; }; ?> clearfix">

  <div class="layout_core_menu_container">
    
    <?php include 'logo.php'; ?>
    
    <?php include 'main_menu.php'; ?>
        
    <?php include 'mini_menu.php'; ?>
    
    <?php include 'search.php'; ?>
        
  </div>
  
  <?php include 'popup_login.php'; ?>
  
</div>
<!-- END - Menu Bar Widget -->

<?php include 'mobile_menu.php'; ?>