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

?>

<div class="layout_theme_home_logos"><div class="width_main">
  <ul>
  
  	<?php if($this->feat1_img): ?>
    <li><a href="<?php echo ($this->feat1_link) ? $this->feat1_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat1_img; ?>" alt="" />
      <?php if(!empty($this->feat1_text)): ?><div><?php echo $this->translate($this->feat1_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
    <?php if($this->feat2_img): ?>
    <li><a href="<?php echo ($this->feat2_link) ? $this->feat2_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat2_img; ?>" alt="" />
      <?php if(!empty($this->feat2_text)): ?><div><?php echo $this->translate($this->feat2_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
    <?php if($this->feat3_img): ?>
    <li><a href="<?php echo ($this->feat3_link) ? $this->feat3_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat3_img; ?>" alt="" />
      <?php if(!empty($this->feat3_text)): ?><div><?php echo $this->translate($this->feat3_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
    <?php if($this->feat4_img): ?>
    <li><a href="<?php echo ($this->feat4_link) ? $this->feat4_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat4_img; ?>" alt="" />
      <?php if(!empty($this->feat4_text)): ?><div><?php echo $this->translate($this->feat4_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
    <?php if($this->feat5_img): ?>
    <li><a href="<?php echo ($this->feat5_link) ? $this->feat5_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat5_img; ?>" alt="" />
      <?php if(!empty($this->feat5_text)): ?><div><?php echo $this->translate($this->feat5_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
    <?php if($this->feat6_img): ?>
    <li><a href="<?php echo ($this->feat6_link) ? $this->feat6_link : 'javascript:void(0)'; ?>">
      <img src="<?php echo $this->feat6_img; ?>" alt="" />
      <?php if(!empty($this->feat6_text)): ?><div><?php echo $this->translate($this->feat6_text); ?></div><?php endif; ?>
    </a></li>
    <?php endif; ?>
    
  </ul>
</div></div>