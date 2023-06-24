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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestorealbum_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="itemCount" name="itemCount"  value="<?php echo $this->itemCount;?>"/>
  </form>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitestore): ?>
				<li id="sitestorevideo-item-<?php echo $sitestore->video_id ?>">
				<div class="seaocore_browse_list_photo"> 
				<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);?>
				<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $sitestore->store_id, $layout);?>
					<a href="<?php echo $this->url(array('user_id' => $sitestore->owner_id, 'video_id' =>  $sitestore->video_id,'tab' => $tab_id,'slug' => $sitestore->getSlug()),'sitestorevideo_view', true)?>">
		
					<div class="sitestore_video_thumb_wrapper">
						<?php if ($sitestore->duration): ?>
							<span class="sitestore_video_length">
								<?php
									if ($sitestore->duration > 360)
										$duration = gmdate("H:i:s", $sitestore->duration); else
										$duration = gmdate("i:s", $sitestore->duration);
									if ($duration[0] == '0')
										$duration = substr($duration, 1); echo $duration;
								?>
							</span>
						<?php endif; ?>
						<?php  if ($sitestore->photo_id): ?>
							<?php echo   $this->itemPhoto($sitestore, 'thumb.normal'); ?>
						<?php else: ?>
							<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestorevideo/externals/images/video.png" class="thumb_normal item_photo_video thumb_normal" />
						<?php endif;?>
					</div>
				</a>
						</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
             <span>
              <?php if (($sitestore->price>0)): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						<?php endif; ?>
						<?php if ($sitestore->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						<?php endif; ?>
              </span>
              
							<span class="list_rating_star">
								<span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star_half"></span>
								<?php if($sitestore->rating>0):?>
									<?php
									$currentRatingValue = $sitestore->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
									$finalRatingValue = (int)$currentRatingValue;
									}
									else {
									$finalRatingValue = (int)$currentRatingValue + .5;
									}
									?>
									<?php for($x=1; $x<=$sitestore->rating; $x++): ?>
										<span class="rating_star_generic rating_star" title= "<?php echo $finalRatingValue.$this->translate(' rating');?>" ></span>
									<?php endfor; ?>
									<?php if((round($sitestore->rating)-$sitestore->rating)>0):?>
										<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue ?> rating"></span>
									<?php endif; ?>
								<?php endif; ?>
							</span>
							<h3><?php echo $this->htmlLink($sitestore->getHref(), $sitestore->getTitle(), array('title' => $sitestore->getTitle())); ?> </h3>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()),  $sitestore->sitestore_title) ?>
						</div>
						<div class="seaocore_browse_list_info_date">
	            <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>,
							<?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comments()->getCommentCount()),$this->locale()->toNumber($sitestore->comments()->getCommentCount())) ?>, <?php echo $this->translate(array('%s like', '%s likes', $sitestore->likes()->getLikeCount()),$this->locale()->toNumber($sitestore->likes()->getLikeCount())) ?>, <?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count),$this->locale()->toNumber($sitestore->view_count)) ?>


						</div>	

						<div class='seaocore_browse_list_info_blurb'>
							<?php echo $this->viewMore($sitestore->description); ?><br />
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestorevideo"), array("orderby" => $this->orderby,"itemCount" => $this->itemCount)); ?>

<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>


<script type="text/javascript">
  var storeAction = function(store){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_store')){
				form=$('filter_form_store');
			}
    form.elements['store'].value = store;
    $('filter_form_store').elements['itemCount'].value = '<?php echo $this->itemCount;?>';
    
		form.submit();
  } 
</script>