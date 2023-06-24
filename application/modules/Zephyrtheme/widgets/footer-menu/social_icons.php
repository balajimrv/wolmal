<?php if($this->footer_socialicons == 1) : ?>
<?php if(!empty($this->footer_facebook) || !empty($this->footer_twitter) || !empty($this->footer_linkedin) || !empty($this->footer_google) || !empty($this->footer_youtube)) : ?>
<div class="social_icons">
  <ul>
	<?php if(!empty($this->footer_facebook)) : ?>
	<li class="social_icons_icon facebook_icon">
	  <a href="<?php echo $this->footer_facebook ?>" target="_blank"><span><?php echo $this->translate('Facebook') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Like us on Facebook!') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_twitter)) : ?>
	<li class="social_icons_icon twitter_icon">
	  <a href="<?php echo $this->footer_twitter ?>" target="_blank"><span><?php echo $this->translate('Twitter') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Follow us on Twitter!') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_linkedin)) : ?>
	<li class="social_icons_icon linkedin_icon">
	  <a href="<?php echo $this->footer_linkedin ?>" target="_blank"><span><?php echo $this->translate('Linkedin') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Linkedin Profile') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_google)) : ?>
	<li class="social_icons_icon google_icon">
	  <a href="<?php echo $this->footer_google ?>" target="_blank"><span><?php echo $this->translate('Google+') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Google Plus Profile') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_youtube)) : ?>
	<li class="social_icons_icon youtube_icon">
	  <a href="<?php echo $this->footer_youtube ?>" target="_blank"><span><?php echo $this->translate('YouTube') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our YouTube Page') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_vimeo)) : ?>
	<li class="social_icons_icon vimeo_icon">
	  <a href="<?php echo $this->footer_vimeo ?>" target="_blank"><span><?php echo $this->translate('Vimeo') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Vimeo Page!') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_tumblr)) : ?>
	<li class="social_icons_icon tumblr_icon">
	  <a href="<?php echo $this->footer_tumblr ?>" target="_blank"><span><?php echo $this->translate('Tumblr') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Tumblr Page!') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_pinterest)) : ?>
	<li class="social_icons_icon pinterest_icon">
	  <a href="<?php echo $this->footer_pinterest ?>" target="_blank"><span><?php echo $this->translate('Pinterest') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Pinnterest Page') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_instagram)) : ?>
	<li class="social_icons_icon instagram_icon">
	  <a href="<?php echo $this->footer_instagram ?>" target="_blank"><span><?php echo $this->translate('Instagram') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Instagram Page') ?></div>
	</li>
	<?php endif; ?>
	<?php if(!empty($this->footer_flickr)) : ?>
	<li class="social_icons_icon flickr_icon">
	  <a href="<?php echo $this->footer_flickr ?>" target="_blank"><span><?php echo $this->translate('Flickr') ?></span></a>
	  <div class="social_icons_tip"><?php echo $this->translate('Our Flickr Page') ?></div>
	</li>
	<?php endif; ?>

  </ul>
</div>
<?php endif; ?>
<?php endif; ?>
