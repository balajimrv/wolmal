<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if($this->paginator->getTotalItemCount() > 0) :?>

	<?php if ($this->can_create): ?>
		<div class="seaocore_add " data-role="controlgroup" data-type="horizontal">
			<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'tab' => $this->identity), 'sitestorevideo_create', true) ?>' class='buttonlink icon_type_sitestorevideo_new'><?php echo $this->translate('Add a Video'); ?></a>
		</div>
	<?php endif; ?>

	<div class="sm-content-list ui-listgrid-view"  id="profile_sitestorevideos">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
		  <?php foreach( $this->paginator as $item ): ?>
				<li>  
					<a href="<?php echo $item->getHref(); ?>">
					<?php
						if( $item->photo_id ) {
							echo $this->itemPhoto($item, 'thumb.profile');
						} else {
							echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Sitestorevideo/externals/images/video.png">';
						}
					?>
					<div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
					<h3><?php echo $item->getTitle() ?></h3>
					<?php if( $item->duration ): ?>
						<p class="ui-li-aside">
							<?php
								if( $item->duration >= 3600 ) {
									$duration = gmdate("H:i:s", $item->duration);
								} else {
									$duration = gmdate("i:s", $item->duration);
								}
								echo $duration;
							?>
						</p>
					<?php endif ?>
					<p><?php echo $this->translate('By'); ?>
						<strong><?php echo $item->getOwner()->getTitle(); ?></strong>
					</p>
					<p class="ui-li-aside-rating"> 
						<?php if( $item->rating > 0 ): ?>
							<?php for( $x=1; $x<=$item->rating; $x++ ): ?>
								<span class="rating_star_generic rating_star"></span>
							<?php endfor; ?>
							<?php if( (round($item->rating) - $item->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half"></span>
							<?php endif; ?>
						<?php endif; ?>
					</p>
					</a> 
				</li>
		  <?php endforeach; ?>
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitestorevideos');
			?>
		<?php endif; ?>

	</div>
	
<?php else :?>
	<div class="tip" id='sitestorevideo_search'>
		<span>
			<?php echo $this->translate('No videos have been added in this Store yet.'); ?>
			<?php if ($this->can_create): ?>
				<?php echo $this->translate('Be the first to %1$sadd%2$s one!', '<a href="' . $this->url(array('store_id' => $this->sitestore->store_id, 'tab' => $this->identity), 'sitestorevideo_create') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>