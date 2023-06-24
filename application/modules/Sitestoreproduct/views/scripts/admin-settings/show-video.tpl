<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-video.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-video/_navigationAdmin.tpl'; ?>

<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		showDefaultVideo('<?php echo $settings->getSetting('sitestoreproduct.show.video', 1) ?>');
	});
  var videoModuleEnabled = '<?php echo Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');?>';
  function showDefaultVideo(option) {
		if(option == 0 || (videoModuleEnabled == 0)) {
			$('sitestoreproduct_video_ffmpeg_path-wrapper').style.display='block';
			$('sitestoreproduct_video_jobs-wrapper').style.display='block';
			$('sitestoreproduct_video_embeds-wrapper').style.display='block';
		}
		else  {
			$('sitestoreproduct_video_ffmpeg_path-wrapper').style.display='none';
			$('sitestoreproduct_video_jobs-wrapper').style.display='none';
			$('sitestoreproduct_video_embeds-wrapper').style.display='none';
		}
  }
</script>