<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 6072 2010-06-02 02:36:45Z john $
 * @author     Jung
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css');
?>
<form method="post" class="global_form_popup sitestore_addremove_fav_popup_wrapper">
	<div class="sitestore_addremove_fav_popup_title"><?php echo $this->translate('Link ') . $this->sitestore->title. $this->translate("  to your Store:") ?></div>
		<div class="sitestore_addremove_fav_popup">
			<div class="sitestore_addremove_fav_popup_img">
				<?php echo  $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore), array('target' => '_blank')); ?>
			</div>
			<div class="sitestore_addremove_fav_popup_detail">
			<b> <?php  echo $this->translate('Select your Store to be linked to ') . $this->sitestore->title . '.' ?> </b>
			<select name="store_id" id="free_packageslist" >
				<option value = "" ></option>
				<?php foreach ($this->userListings as $package) { ?>
					<option value='<?php echo $package['store_id']; ?>' ><?php echo $package['title'] ?></option>
				<?php } ?>
			</select>
			<p>
				<!--<input type="hidden" name="confirm" value="<?php //echo $this->_id ?>"/>-->
				<button type='submit'><?php echo $this->translate("Link") ?></button> <?php echo $this->translate("or") ?>
				<a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
				<!-- <button onclick='javascript:parent.Smoothbox.close()' ><?php //echo $this->translate("Cancel") ?></button>-->
			</p>
		</div>
	</div>
</form>
<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>