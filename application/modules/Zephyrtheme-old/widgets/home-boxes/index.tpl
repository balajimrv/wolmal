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

// Box 1
$home_ico_one = $this->home_ico_one;
$home_ico_onelink = $this->home_icoone_link;
// Box 2
$home_ico_two = $this->home_ico_two;
$home_ico_twolink = $this->home_icotwo_link;
// Box 3
$home_ico_three = $this->home_ico_three;
$home_ico_threelink = $this->home_icothree_link;
// Box 4
$home_ico_four = $this->home_ico_four;
$home_ico_fourlink = $this->home_icofour_link;

if($this->home_icos == 0){$homeboxesclass = 'onecol';}
elseif($this->home_icos == 1){$homeboxesclass = 'twocol';}
elseif($this->home_icos == 2){$homeboxesclass = 'threecol';}
elseif($this->home_icos == 3){$homeboxesclass = 'fourcol';};

?>

<div class="layout_theme_home_boxes width_main"><div class="home_boxes clearfix <?php echo $homeboxesclass; ?>">
  <div class="home_box home_box_one"><div>
    <a href="<?php echo ($home_ico_onelink) ? $home_ico_onelink : 'javascript:void(0)'; ?>">
      <?php if($this->home_icosimgs != 3): ?>
      <img src="<?php echo ($home_ico_one) ? $home_ico_one : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_ico_1.png'); ?>" alt="" />
      <?php endif; ?>
      
      <?php if($this->home_icosimgs != 2): ?><h3><?php echo $this->translate('Meet'); ?></h3><?php endif; ?>
      <?php if($this->home_icosimgs != 1): ?><div><?php echo $this->translate('Meet consumers from all over world matches with your tastes and interests.'); ?></div><?php endif; ?>
    </a>
  </div></div>
  
  <?php if($this->home_icos == 1 || $this->home_icos == 2 || $this->home_icos == 3 ): ?>
  <div class="home_box home_box_two"><div>
    <a href="<?php echo ($home_ico_twolink) ? $home_ico_twolink : 'javascript:void(0)'; ?>">
      <?php if($this->home_icosimgs != 3): ?>
      <img src="<?php echo ($home_ico_two) ? $home_ico_two : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_ico_2.png'); ?>" alt="" />
      <?php endif; ?>
      
      <?php if($this->home_icosimgs != 2): ?><h3><?php echo $this->translate('Discover'); ?></h3><?php endif; ?>
      <?php if($this->home_icosimgs != 1): ?><div><?php echo $this->translate('Discover the best products and offers from leading brands and local sellers'); ?></div><?php endif; ?>
    </a>
  </div></div>
  <?php endif; ?>
  
  <?php if($this->home_icos == 2 || $this->home_icos == 3 ): ?>
  <div class="home_box home_box_three"><div>
    <a href="<?php echo ($home_ico_threelink) ? $home_ico_threelink : 'javascript:void(0)'; ?>">
      <?php if($this->home_icosimgs != 3): ?>
      <img src="<?php echo ($home_ico_three) ? $home_ico_three : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_ico_3.png'); ?>" alt="" />
      <?php endif; ?>
      
      <?php if($this->home_icosimgs != 2): ?><h3><?php echo $this->translate('Find Best'); ?></h3><?php endif; ?>
      <?php if($this->home_icosimgs != 1): ?><div><?php echo $this->translate('Discuss about products with your friends and community and take perfect decision based on their reviews'); ?></div><?php endif; ?>
    </a>
  </div></div>
  <?php endif; ?>
  
  <?php if($this->home_icos == 3 ): ?>
  <div class="home_box home_box_four"><div>
    <a href="<?php echo ($home_ico_fourlink) ? $home_ico_fourlink : 'javascript:void(0)'; ?>">
      <?php if($this->home_icosimgs != 3): ?>
      <img src="<?php echo ($home_ico_four) ? $home_ico_four : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_ico_4.png'); ?>" alt="" />
      <?php endif; ?>
      
      <?php if($this->home_icosimgs != 2): ?><h3><?php echo $this->translate('Buy it'); ?></h3><?php endif; ?>
      <?php if($this->home_icosimgs != 1): ?><div><?php echo $this->translate('Purchase best product available at best price compare to rest of all other markets'); ?></div><?php endif; ?>
    </a>
  </div></div>
  <?php endif; ?>
    
    
      <?php if($this->home_icos == 3 ): ?>
  <div class="home_box home_box_four"><div>
    <a href="<?php echo ($home_ico_fourlink) ? $home_ico_fourlink : 'javascript:void(0)'; ?>">
      <?php if($this->home_icosimgs != 3): ?>
      <img src="<?php echo ($home_ico_four) ? $home_ico_four : $this->baseUrl('application/modules/Zephyrtheme/externals/images/home_ico_5.png'); ?>" alt="" />
      <?php endif; ?>
      
      <?php if($this->home_icosimgs != 2): ?><h3><?php echo $this->translate('Bridges'); ?></h3><?php endif; ?>
      <?php if($this->home_icosimgs != 1): ?><div><?php echo $this->translate('Get rewarded with Bridges for activities on Wall & Business'); ?></div><?php endif; ?>
    </a>
  </div></div>
  <?php endif; ?>
</div></div>
                            
                            
                            