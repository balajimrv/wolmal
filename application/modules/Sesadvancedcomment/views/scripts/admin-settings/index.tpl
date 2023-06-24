<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesadvancedcomment/views/scripts/dismiss_message.tpl';
?>
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
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.pluginactivated',0)){ 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php } ?>
<script type="application/javascript">


function enablestickers(value){
  if(value == 1){
    document.getElementById('sesadvancedcomment_stickertitle-wrapper').style.display = 'block';
    document.getElementById('sesadvancedcomment_stickerdescription-wrapper').style.display = 'block';
    document.getElementById('sesadvancedcomment_backgroundimage-wrapper').style.display = 'block';
  }else{
    document.getElementById('sesadvancedcomment_stickertitle-wrapper').style.display = 'none';
    document.getElementById('sesadvancedcomment_stickerdescription-wrapper').style.display = 'none';
    document.getElementById('sesadvancedcomment_backgroundimage-wrapper').style.display = 'none';
  }
}

function showLanguage(value){
  if(value == 1){
    document.getElementById('sesadvancedcomment_language-wrapper').style.display = 'block';		
  }else{
    document.getElementById('sesadvancedcomment_language-wrapper').style.display = 'none';		
  }
}
enablestickers(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.enablestickers', 1); ?>);
showLanguage(<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.translate', 0); ?>);
</script>