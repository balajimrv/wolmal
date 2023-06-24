<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesalbum/views/scripts/dismiss_message.tpl';?>
<h2>
  <?php echo $this->translate("Advanced Photos & Albums Plugin") ?>
</h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'settings', 'action' => 'contact-us'),'admin_default',true); ?>" class="request-btn">Feature Request</a>
</div>
<?php if( count($this->navigation) ): ?>
  <div class='sesbasic-admin-navgation'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.pluginactivated')) { ?>
<?php $flushData = Engine_Api::_()->sesalbum()->getFlushPhotoData();
   if($flushData >0){ ?>
  <div class="sesalbum_warning">
      You have <?php echo $flushData; ?> unmapped photos <a href="<?php echo $this->url(array('module' => 'sesalbum', 'controller' => 'settings', 'action' => 'flush-photo'),'admin_default',true); ?>">click here</a> to remove them.
  </div>
<?php  } } ?>
<div class="settings sesbasic_admin_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<div class="sesbasic_waiting_msg_box" style="display:none;">
	<div class="sesbasic_waiting_msg_box_cont">
    <?php echo $this->translate("Please wait.. It might take some time to activate plugin."); ?>
    <i></i>
  </div>
</div>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.pluginactivated',0)){ 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php } ?>


<script type="text/javascript">
function confirmChangeLandingPage(value){
	if(value == 1 && !confirm('Are you sure want to set the default Welcome page of this plugin as the Landing page of your website. Your old landing page will not be recoverable after changing it using this setting.')){
		document.getElementById('sesalbum_set_landingpage-0').checked = true;
	}
}
function rating_album(value){
		if(value == 1){
			document.getElementById('sesalbum_ratealbum_own-wrapper').style.display = 'block';		
			document.getElementById('sesalbum_ratealbum_again-wrapper').style.display = 'block';
			document.getElementById('sesalbum_ratealbum_show-wrapper').style.display = 'none';	
		}else{
			document.getElementById('sesalbum_ratealbum_show-wrapper').style.display = 'block';
			document.getElementById('sesalbum_ratealbum_own-wrapper').style.display = 'none';
			document.getElementById('sesalbum_ratealbum_again-wrapper').style.display = 'none';
		}
}
function show_position(value){
	if(value == 1){
			document.getElementById('sesalbum_position_watermark-wrapper').style.display = 'block';
	}else{
			document.getElementById('sesalbum_position_watermark-wrapper').style.display = 'none';		
	}
}
if(document.querySelector('[name="sesalbum_watermark_enable"]:checked').value == 0){
	document.getElementById('sesalbum_position_watermark-wrapper').style.display = 'none';	
}else{
		document.getElementById('sesalbum_position_watermark-wrapper').style.display = 'block';
}
function rating_photo(value){
		if(value == 1){
			document.getElementById('sesalbum_ratephoto_show-wrapper').style.display = 'none';
			document.getElementById('sesalbum_ratephoto_own-wrapper').style.display = 'block';
			document.getElementById('sesalbum_ratephoto_again-wrapper').style.display = 'block';			
		}else{
			document.getElementById('sesalbum_ratephoto_show-wrapper').style.display = 'block';
			document.getElementById('sesalbum_ratephoto_own-wrapper').style.display = 'none';
			document.getElementById('sesalbum_ratephoto_again-wrapper').style.display = 'none';	
		}
}
if(document.querySelector('[name="sesalbum_album_rating"]:checked').value == 0){
	document.getElementById('sesalbum_ratealbum_own-wrapper').style.display = 'none';		
	document.getElementById('sesalbum_ratealbum_again-wrapper').style.display = 'none';
	document.getElementById('sesalbum_ratealbum_show-wrapper').style.display = 'block';
}else{
	document.getElementById('sesalbum_ratealbum_show-wrapper').style.display = 'none';
}
if(document.querySelector('[name="sesalbum_photo_rating"]:checked').value == 0){
			document.getElementById('sesalbum_ratephoto_own-wrapper').style.display = 'none';	
			document.getElementById('sesalbum_ratephoto_again-wrapper').style.display = 'none';	
			document.getElementById('sesalbum_ratephoto_show-wrapper').style.display = 'block';	
}else{
			document.getElementById('sesalbum_ratephoto_show-wrapper').style.display = 'none';	
}
</script>