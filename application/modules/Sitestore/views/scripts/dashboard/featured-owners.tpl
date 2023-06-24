<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featuredowners.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php if (empty($this->is_ajax)) : ?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
	<div class="sitestore_edit_content">
		<div class="sitestore_edit_header">
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
			<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
		</div>
 <div id="show_tab_content">
<?php endif; ?>

		<div class="global_form">
			<div>
				<div>
					<h3><?php echo $this->translate('Featured Store Admins'); ?></h3>
					<p class="form-description">
						<?php echo $this->translate('Below you can see all the featured admins of this store. Featured admins are shown on the store profile.') ?>
					</p>
					<?php $featuredhistories_array = $this->featuredhistories->toarray();
						if(!empty($featuredhistories_array)) :
							$count = count($featuredhistories_array);
							echo '<div class="sitestore_featuredadmins_count">' . $this->translate(array('%s featured store admin', '%s featured store admins', $count), $this->locale()->toNumber($count)); ?></div>

						<div class='sitestore_featuredadmins_list'>
							<?php foreach ($this->featuredhistories as $item):?>
								<div class='sitestore_featuredadmins_thumb' id='<?php echo $item->manageadmin_id ?>_storethumb'>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="sitestore_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitestore_manageadmins', 'action' => 'list','store_id' => $this->sitestore->store_id), $this->translate('Manage Featured Store Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php else : ?>
						<div class="tip">
							<span>
								<?php echo $this->translate("No featured admins have been added for this store yet."); ?>
							</span>
						</div>
						<div class="sitestore_featuredadmins_add">
							<?php echo $this->htmlLink(array('route' => 'sitestore_manageadmins', 'action' => 'list', 'store_id' => $this->sitestore->store_id), $this->translate('Add Featured Store Admins'), array('onclick' => 'owner(this);return false',)) ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php if (empty($this->is_ajax)) : ?>
		</div>
	</div>
  </div>
<?php endif; ?>