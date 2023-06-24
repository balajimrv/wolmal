<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $sitestore = $this->sitestore; ?>

<li class="seaocore_carousel_content_item_wrapper b_medium" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
  <div class="seaocore_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;">
    <center>
        <a href="<?php echo $sitestore->getHref() ?>" class="seaocore_carousel_thumb" title="<?php echo $sitestore->getTitle()?>">

          <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_store_thumb_normal.png'; $temp_url=$sitestore->getPhotoUrl('thumb.normal'); if(!empty($temp_url)): $url=$sitestore->getPhotoUrl('thumb.normal'); endif;?>
          
        <span style="background-image: url(<?php echo $url; ?>); "></span>
        
      </a>
    </center>
    <div class="seaocore_carousel_title">
      <?php echo $this->htmlLink($sitestore->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestore->getTitle(), $this->title_truncation), array('title' => $sitestore->getTitle())) ?>
    </div>

    <div class="seaocore_carousel_cnt clr">
      <div class="seaocore_txt_light"> 
        <a href="<?php echo $sitestore->getCategory()->getHref() ?>"> 
          <?php echo $this->translate($sitestore->getCategory()->getTitle(true)); ?>
        </a>
      </div>
      
		<?php if ($this->statistics): ?>
			<?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('likeCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>
					<?php endif; ?>
					<?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('followCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s follower', '%s followers', $sitestore->follow_count), $this->locale()->toNumber($sitestore->follow_count)) ?>	
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('viewCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>
					<?php endif; ?>
					<?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics)) : ?>  - <?php endif; ?>
					<?php if(in_array('memberCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')): ?>
													<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
							if ($sitestore->member_title && $memberTitle) : ?>
							<?php if ($sitestore->member_count == 1) : ?><?php echo $sitestore->member_count . ' member'; ?><?php else: ?>	<?php echo $sitestore->member_count . ' ' .  $sitestore->member_title; ?><?php endif; ?>
							<?php else : ?>
							<?php echo $this->translate(array('%s member', '%s members', $sitestore->member_count), $this->locale()->toNumber($sitestore->member_count)) ?>
						<?php endif; ?>		<?php endif; ?>
					<?php endif; ?>		
				</div>
			<?php endif; ?>	
			<?php if(in_array('commentCount', $this->statistics) || in_array('reviewCount', $this->statistics)) : ?>
				<div class="seaocore_txt_light">
					<?php if(in_array('commentCount', $this->statistics)): ?>
						<?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>
					<?php endif; ?>
					<?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics)) : ?> - <?php endif; ?>
					<?php if(in_array('reviewCount', $this->statistics)): ?>
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')): ?>
							<?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)) ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
    <?php endif; ?>
      
			<?php if(($this->sponsoredIcon && $sitestore->sponsored) || ($this->featuredIcon && $sitestore->featured) || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && $sitestore->rating)): ?>
				<div class="seaocore_carousel_grid_view_list_btm b_medium">
				
					<?php if((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && $sitestore->rating)): ?>
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
						<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
							<?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
							<span class="rating_star_generic rating_star" ></span>
							<?php endfor; ?>
							<?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half" ></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
						
					<span class="fright">
						<?php if ($sitestore->sponsored == 1 && $this->sponsoredIcon): ?>
							<i title="<?php echo $this->translate('Sponsored');?>" class="seaocore_icon seaocore_icon_sponsored"></i>
						<?php endif; ?>
						<?php if ($sitestore->featured == 1 && $this->featuredIcon): ?>
							<i title="<?php echo $this->translate('Featured');?>" class="seaocore_icon seaocore_icon_featured"></i>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
    </div>  
  </div>
</li>
