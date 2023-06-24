<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: listowners.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestore/externals/styles/style_sitestore_dashboard.css');
?>
<form method="post" action="<?php echo $this->url(array('action'=>'list'));?>" class="global_form global_form_popup">
	<div>
		<div>
			<h3> <?php echo $this->translate('Manage Featured Store Admins'); ?> </h3>
			<p><?php echo $this->translate("Below you can select / unselect store admins as featured.") ?></p>
			<div class="sitestore_featuredadmins_add_list">
				<table style="display:block;">
					<?php foreach($this->owners as $item) : ?>
						<tr>
							<td><input id='<?php echo $item->user_id ?>' value='<?php echo $item->featured; ?>' name='<?php echo $item->user_id; ?>' type='checkbox' class='checkbox' <?php if(!empty($item->featured)) {echo "checked"; } ?> /> </td>
							<td><?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>	</td>
							<td><span><?php echo $item->getOwner()->getTitle()?></span> </td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>	
			<div class='buttons'>
				<button type='submit'><?php echo $this->translate('Save'); ?></button>
				 <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</div>
		</div>
	</div>
</form>