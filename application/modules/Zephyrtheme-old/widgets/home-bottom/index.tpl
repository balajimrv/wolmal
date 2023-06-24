<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

// Head scripts
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.min.js')  
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.backstretch.min.js');  

?>
<script type="text/javascript">
jQuery(document).ready(function () {
jQuery(function() { jQuery('.layout_theme_home_bottom').backstretch([
   "<?php echo ($this->homebottom_img) ? $this->homebottom_img : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_bottom.jpg'); ?>" 
], {duration: 3000, fade: 200}); }); });
</script>

<div class="layout_theme_home_bottom"><div class="width_main">
  <?php if($this->homebottom_h1 == 1): ?>
  <h2 class="home_bottom_first"><?php echo $this->translate('Signup Today For Free'); ?></h2>
  <?php endif; ?>
  
  <?php if($this->homebottom_h2 == 1): ?>
  <h3 class="home_bottom_second"><?php echo $this->translate('Maecenas non lectus libero. Nam dictum massa sapien, ut tempus leo lacinia vitae.'); ?></h3>
  <?php endif; ?>
  
  <?php if($this->homebottom_button == 1): ?>
  <div class="homebottom_signup_button">
  <?php echo $this->htmlLink($this->url(array('module'=>'','controller' => '','action'=>'signup'), 'default', true), $this->translate('Join Today'), array(
    'class' => 'homebottom_signup_button_link'
  )); ?>
  <?php endif; ?>
  </div>
</div></div>