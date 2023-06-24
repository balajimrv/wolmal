<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profilepicture.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css'); ?>

<div class="seaocore_upload_photo_popup">
	<div id="form_photo_cover" <?php if($this->status): ?>class="dnone"<?php endif; ?>>
		<?php echo $this->form->setAttrib('class', 'upload_cover_photo_form')->render($this) ?>
		<button name="sitestore_cancel" id="sitestore_cancel" type="button" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></button>
	</div>
	<div id="loading_content" <?php if(!$this->status): ?>class="dnone"<?php endif; ?>>
		<div class="seaocore_content_loader"></div>
	</div>
</div>
<?php if($this->status):?>
<script type="text/javascript">
  parent.document.seaoCoverPhoto.getCoverPhoto(1);
</script>
<?php else:?>
<script type="text/javascript">
  function uploadPhoto(){
    $('cover_photo_form').submit();
    $('form_photo_cover').addClass('dnone');
    $('loading_content').removeClass('dnone');
  }
</script>
<?php endif; ?>


