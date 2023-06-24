<?php 
    $this->headLink()
	    ->appendStylesheet(
	    $this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/styles/admin.css');
?>

<h2 class="theme_title"><?php echo $this->translate("Zephyr Theme - Options Panel") ?></h2>

<div class="support_box">
  <?php echo $this->translate("Need Help?") ?> <a href="http://pixythemes.com/tickets/" target="_blank">Submit a Ticket</a> or <a href="http://pixythemes.com/contact/" target="_blank">Contact Us</a>
</div>

<ul class="form-notices" style="width: 100%; padding: 15px 0px;"><li>After you are done editing your changes might not be visible on frontend unless you <a href="http://en.wikipedia.org/wiki/Wikipedia:Bypass_your_cache#Bypassing_cache" target="_blank" style="font-weight: bold;">Force Refresh</a> to clear your browser cache.</li></ul>

<div style="clear: both;"></div>