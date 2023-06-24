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
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/modernizr.js')  
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.flexslider.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.backstretch.min.js');  
    
$home_slider_height = ($this->home_slider_height) ? $this->home_slider_height : '550px';
$home_slider_time = ($this->home_slider_time) ? $this->home_slider_time : '5';
?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(window).load(function() {
  jQuery('.px_slider').flexslider({
    animation: "fade",
	slideshow: true,
	pauseOnHover: true,
	smoothHeight: true,
	slideshowSpeed: <?php echo $home_slider_time.'000'; ?>,
	start: function(){
	  <?php foreach ($this->slider_items as $item) { ?>
	  jQuery('.px_slider .slides > li#px_slide_<?php echo $item->id; ?>').backstretch([
		"<?php echo ($item->image_path) ? $item->image_path : $this->baseUrl('application/modules/Zephyrtheme/externals/images/slider/background.jpg'); ?>" 
	  ], {duration: 0, fade: 0}); 
	  <?php } ?>
    }
  });
});
</script>
<div class="px_slider" style="height: <?php echo $home_slider_height; ?>;">
  <ul class="slides">
    <?php foreach ($this->slider_items as $item) { ?>
    <?php 
    if(in_array('image', $item->show_item)){
      if($item->text_align == 'left'){ $img_status = 'img_right'; } else { $img_status = 'img_left'; } 
    } else { $img_status = 'img_disabled'; } 
    
    if ($item->video !='') { $has_imgOrVideo = 'has_video'; } else { $has_imgOrVideo = 'has_img'; }
    ?>
    <li id="px_slide_<?php echo $item->id; ?>" style="height: <?php echo $home_slider_height; ?>;">
        <div class="px_slider_container <?php echo $img_status; ?> <?php echo $has_imgOrVideo; ?>"><div class="width_main clearfix">
        
            <?php if (in_array('image', $item->show_item)) { ?>
            <div class="px_slider_illustration" <?php if($item->colonetop){ echo 'style="padding-top: '.$item->colonetop.'"'; } ?>>
              <?php if ($item->video !='') { echo $item->video; } else { ?>
                <img src="<?php echo $item->illustration_path; ?>" alt="<?php echo $this->translate($item->subtitle); ?>" />
              <?php } ?>
            </div>
            <?php } ?>
            
            <?php if (in_array('firsthl', $item->show_item) && $item->title!='' || in_array('secondhl', $item->show_item) && $item->subtitle!='' || in_array('signupbtn', $item->show_item) || in_array('loginbtn', $item->show_item)) { ?>
            <div class="px_slider_content" <?php if($item->coltwotop){ echo 'style="padding-top: '.$item->coltwotop.'"'; } ?>>
              <?php if (in_array('firsthl', $item->show_item) && $item->title!='') { ?>
              <h1 <?php if($item->color){ echo 'style="color: '.$item->color.'"'; } ?>><?php echo $this->translate($item->title); ?></h1>
              <?php } ?>
              
              <?php if (in_array('secondhl', $item->show_item) && $item->subtitle!='') { ?>
              <h2 <?php if($item->color){ echo 'style="color: '.$item->color.'"'; } ?>><?php echo $this->translate($item->subtitle); ?></h2>
              <?php } ?>
              
              <?php if (in_array('signupbtn', $item->show_item) || in_array('loginbtn', $item->show_item)) { ?>
              <div class="slider_buttons">
                <?php if (in_array('signupbtn', $item->show_item)) { ?>
                <div class="slider_signup_button">
                  <a href="<?php echo ($this->header_signupbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'signup'), 'default', true); ?>" class="button_signup button"><?php echo $this->translate("Register for Free"); ?></a>
                </div>
                <?php } ?>

                <?php if (in_array('loginbtn', $item->show_item)) { ?>
                <div class="slider_login_button">
                  <a href="<?php echo ($this->header_loginbutton == 0) ? 'javascript:void(0)' : $this->url(array('module'=>'','controller' => '','action'=>'login'), 'default', true); ?>" class="button_login button"><?php echo $this->translate("Member Login"); ?></a>
                </div>
                <?php } ?>
              </div>
              <?php } ?>
            </div>
            <?php } ?>
            
        </div></div>
    </li>
    <?php } ?>
  </ul>
</div>