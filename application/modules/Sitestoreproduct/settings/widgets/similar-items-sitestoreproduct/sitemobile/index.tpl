<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php 
  $ratingValue = $this->ratingType; 
  $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {$ratingType = 'editor';} elseif ($this->ratingType == 'rating_avg') {$ratingType = 'overall';} else { $ratingType = 'user';}
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');?>
<?php if ($this->viewType=='listview'): ?>
	<div class="sm-content-list">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r" id="list-view">
			<?php foreach($this->products as $sitestoreproduct):?>
				<li>
					<a href="<?php echo $sitestoreproduct->getHref(array('profile_link' => 1));?>">
						<?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon');?>
						<h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->truncation) ?></h3>
						<p>
							<?php if ($ratingValue == 'rating_both'): ?>
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?><br />
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
							<?php else: ?>
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
							<?php endif; ?>
						</p>
						<p>
							<b><?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?></b>
						</p>
            <p class="ui-li-aside">
							<?php if ($sitestoreproduct->sponsored == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
							<?php if ($sitestoreproduct->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							<?php endif; ?>
            </p>
						<p>
							<?php 
								$statistics = '';
								if(in_array('likeCount', $this->statistics)) {
									$statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).' - ';
								}    
								if(in_array('viewCount', $this->statistics)) {
									$statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).' - ';
								}
								if(in_array('commentCount', $this->statistics)) {
									$statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).' - ';
								}
								if(in_array('reviewCount', $this->statistics)) {
	                $statistics .= $this->partial(
                    '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct)).' - ';
                  }
								$statistics = trim($statistics);
								$statistics = rtrim($statistics, '-');
							?>
							<?php echo $statistics; ?>
						</p>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
			<?php if ($this->products->count() > 1): ?>
				<?php
					echo $this->paginationAjaxControl(
							$this->products, $this->identity, 'list-view', array('count' => $this->count, 'truncation' => $this->truncation, 'viewType' => $this->viewType, 'ratingType' => $this->ratingType, 'statistics'=>$this->statistics, 'columnHeight' => $this->columnHeight));
				?>
			<?php endif; ?>
	</div>
<?php else: ?>
	<div class="ui-page-content">
		<div id="grid_view">
			<ul class="p_list_grid">
				<?php foreach ($this->products as $sitestoreproduct): ?>
					<li style="height:<?php echo $this->columnHeight ?>px;">
						<a href="<?php echo $sitestoreproduct->getHref(array('profile_link' => 1)); ?>" class="ui-link-inherit">
							<div class="p_list_grid_top_sec">
								<div class="p_list_grid_img">
									<?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_listing_thumb_profile.png';
										$temp_url = $sitestoreproduct->getPhotoUrl('thumb.profile');
											if (!empty($temp_url)): $url = $sitestoreproduct->getPhotoUrl('thumb.profile');
											endif; ?>
										<span style="background-image: url(<?php echo $url; ?>);"> </span>
								</div>
							<div class="p_list_grid_title">
								<span><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->truncation); ?></span>
							</div>
						</div>
						<div class="p_list_grid_info">	
							<span class="p_list_grid_stats">
								<?php if ($ratingValue == 'rating_both'): ?>
									<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?><br />
									<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
								<?php else: ?>
									<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
								<?php endif; ?>
							</span>

							<span class="p_list_grid_stats">
								<b><?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?></b>
							</span>

							<span class="p_list_grid_stats">
								<?php 
									$statistics = '';
									if($this->statistics &&  in_array('likeCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).' - ';
									}  
									if($this->statistics &&  in_array('viewCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).' - ';
									}
									if($this->statistics && in_array('commentCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).' - ';
									}

                  if(in_array('reviewCount', $this->statistics)) {
	                $statistics .= $this->partial(
                    '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct)).' - ';
                  }

									$statistics = trim($statistics);
									$statistics = rtrim($statistics, '-');
								?>
								<?php echo $statistics; ?> 
							</span>
							
							<span class="p_list_grid_stats">
								<?php if ($sitestoreproduct->sponsored == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
								<?php endif; ?>
								<?php if ($sitestoreproduct->featured == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								<?php endif; ?>
							</span>
						</div>
					</li>
				<?php endforeach;?>
			</ul>
			<?php if ($this->products->count() > 1): ?>
				<?php
					echo $this->paginationAjaxControl(
							$this->products, $this->identity, 'grid_view', array('count' => $this->count, 'truncation' => $this->truncation, 'viewType' => $this->viewType, 'ratingType' => $this->ratingType, 'statistics'=>$this->statistics, 'columnHeight' => $this->columnHeight));
				?>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<style type="text/css">

.layout_sitestoreproduct_similar_items_sitestoreproduct > h3 {
	display:none;
}

</style>