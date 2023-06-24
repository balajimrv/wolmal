<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 

?>

<?php if($this->paginator->getTotalItemCount() > 0) :?>

	<div data-role="controlgroup" data-type="horizontal">
		<?php if ($this->can_edit && !empty($this->allowed_upload_photo)): ?>
			<div class="seaocore_add">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => 0, 'tab' => $this->identity), 'sitestore_photoalbumupload', true) ?>' ><?php echo $this->translate('Create an Album'); ?></a>
			</div>
		<?php elseif (!empty($this->allowed_upload_photo) && ($this->sitestore->owner_id != $this->viewer_id)): ?>
			<div class="seaocore_add">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => $this->default_album_id, 'tab' => $this->identity), 'sitestore_photoalbumupload', true) ?>'  class='buttonlink icon_sitestore_photo_new '><?php echo $this->translate('Add Photos'); ?></a>
			</div>
		<?php endif; ?>
	</div>

	<div class="sm-content-list ui-listgrid-view" id="profile_sitestorealbums">

		<ul data-role="listview" data-inset="false" data-icon="arrow-r" >
			<?php foreach ($this->paginator as $album): ?>
				<li>
					<a href="<?php echo $album->getHref(); ?>">
						<?php if(empty($album->photo_id)):?>
            <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>	
            <?php else:?>
            <?php echo $this->itemPhoto($album, 'thumb.profile'); ?>	
            <?php endif;?>
						<h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
						<p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count());?></p>
						<p><?php echo $this->translate('Posted By'); ?>
							<strong><?php echo $album->getOwner()->getTitle(); ?></strong>
						</p>
					</a> 
				</li>
			<?php endforeach; ?>   
		</ul>
	 
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitestorealbums');
			?>
		<?php endif; ?>

  </div> 
<?php endif;?>

<?php if($this->paginators->getTotalItemCount() > 0):?>
	<div class="ui-store-content" id="profile_sitestorephotos">
		<br /><b><?php echo $this->translate('Photos by Others'); ?></b> &#8226;
    <?php echo $this->translate(array('%s photo', '%s photos', $this->paginators->getTotalItemCount()), $this->locale()->toNumber($this->paginators->getTotalItemCount())) ?><br /><br />
		<ul class="thumbs thumbs_nocaptions">
			<?php foreach ($this->paginators as $photo): ?>
				<li>
					<a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
						<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginators->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginators, $this->identity, 'profile_sitestorephotos');
			?>
		<?php endif; ?>

	</div>
<?php endif; ?>