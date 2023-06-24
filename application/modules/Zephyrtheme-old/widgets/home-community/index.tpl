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
jQuery(function() { jQuery('.layout_theme_home_community').backstretch([
   "<?php echo ($this->home_commbgimg) ? $this->home_commbgimg : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_community.jpg'); ?>" 
], {duration: 3000, fade: 200}); }); });
</script>

<div class="layout_theme_home_community"><div class="width_main">
  <?php if($this->home_commh1 == 1): ?>
  <h2 class="home_community_first"><?php echo $this->translate('Showcases for Buyers & Showroom for Sellers'); ?></h2>
  <?php endif; ?>
  
  <?php if($this->home_commh2 == 1): ?>
  <h3 class="home_community_second"><?php echo $this->translate('Meet all sellers and brands from all over the world.'); ?></h3>
  <?php endif; ?>
  
  <?php if($this->home_commimgshow == 1): ?>
  <div class="home_community_img">
  	<img src="<?php echo ($this->home_commimg) ? $this->home_commimg : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_community_img.jpg'); ?>" alt="" />
  </div>
  <?php endif; ?>
</div></div>