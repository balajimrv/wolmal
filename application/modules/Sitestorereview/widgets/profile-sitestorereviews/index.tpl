<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorereview/externals/styles/style_sitestorereview.css')
	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestorereview/externals/styles/show_star_rating.css');
?>

<script type="text/javascript" >
  var store_url = '<?php echo $this->store_url;?>';
</script>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">
		var sitestoreReviewStore = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		var paginateSitestoreReview = function(store) {
			var url = en4.core.baseUrl + 'widget/index/mod/sitestorereview/name/profile-sitestorereviews';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'store' : store,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}
			}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
			});
		}
	</script>
<?php endif; ?>

<?php if (empty($this->isajax)): ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_review">
      <?php echo $this->translate($this->sitestore->getTitle());?><?php echo $this->translate("'s Reviews");?>
		</div>
	<?php endif;?>	
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)) :?>
			<div class="layout_right" id="communityad_review">

			<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>"store_review"))?>
			</div>
			<div class="layout_middle">
	<?php endif;?>

	<?php if ($this->can_create == 1 && empty($this->is_manageadmin)): ?>
		<div class="seaocore_add">
			<?php
				echo $this->htmlLink(array(
							'route' => 'sitestorereview_create',
							'store_id' => $this->store_id,
							'tab' => $this->identity_temp,
					), $this->translate('Write a Review'), array('class' => 'icon_sitestores_review buttonlink'));
			?>
		</div>
	<?php endif; ?>
	
<!--start top box code-->
<?php if(!empty($this->ratingDataTopbox) && !empty($this->noReviewCheck) && $this->current_store <= 1):?>
<ul class="sitestorereview_rating">
	<?php $iteration = 1;?>
	<?php foreach($this->ratingDataTopbox as $reviewcatTopbox): ?>
				<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
					<?php 
						$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcatTopbox['avg_rating'], 'box');
						$rating_valueTopbox = $showRatingImage['rating_value'];
					?>
				<?php else:?>
					<?php 
						$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcatTopbox['avg_rating'], 'star');
						$rating_valueTopbox = $showRatingImage['rating_value'];
						$rating_valueTitle = $showRatingImage['rating_valueTitle'];
					?>
				<?php endif; ?>
		<li class="sitestorereview_overall_rating">
			<div class="review_cat_title">
				<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
					<?php echo $this->translate($reviewcatTopbox['reviewcat_name'])?>
				<?php else:?>
					<b><?php echo $this->translate("Overall Rating");?></b>
				<?php endif; ?>
			</div>
			<?php if(!empty($reviewcatTopbox['reviewcat_name'])): ?>
				<div class="review_cat_rating">
					<ul class='rating-box-small <?php echo $rating_valueTopbox; ?>'>
						<li id="1" class="rate one">1</li>
						<li id="2" class="rate two">2</li>
						<li id="3" class="rate three">3</li>
						<li id="4" class="rate four">4</li>
						<li id="5" class="rate five">5</li>
					</ul>
				</div>
			<?php else:?>
				<div class="review_cat_rating">
					<ul title="<?php echo $rating_valueTitle.$this->translate(" rating"); ?>" class='rating <?php echo $rating_valueTopbox; ?>' style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/show-star-matrix.png);">
						<li id="1" class="rate one">1</li>
						<li id="2" class="rate two">2</li>
						<li id="3" class="rate three">3</li>
						<li id="4" class="rate four">4</li>
						<li id="5" class="rate five">5</li>
					</ul>
				</div>
		<?php endif;?>

		<?php if($iteration == 1):?>
			<span style="float:right;margin-top:6px;">
				<?php echo $this->translate(array('Total <b>%s</b> Review', 'Total <b>%s</b> Reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)) ?>
			</span>
		<?php endif; ?>

		</li>

		<?php if($iteration == 1):?>
			<li class="sitestorereview_overall_recommended">
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
					<?php echo $this->translate("Recommended by ") .'<b>' .$this->recommend_percentage .'%</b>'. $this->translate(" members");?>
				<?php endif;?>
			</li>
		<?php endif; ?>

		<?php $iteration++;?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<!--end top box code-->
	
	<?php if (($this->paginator->getTotalItemCount() > 0) && !empty($this->getPackageReviewView)): ?>
		<ul class="sitestorereview_profile">
			<?php foreach ($this->paginator as $review): ?>
				<li>
					<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($review->review_id); ?>
					<div class="sitestorereview_overallrating">
						
						<?php foreach($ratingData as $reviewcat): ?>
							<div class="sitestorereview_overallrating_rate">
								<div class="title">
									<?php if(!empty($reviewcat['reviewcat_name'])): ?>
										<?php 
											switch($reviewcat['rating']) {
												case 0:
														$rating_value = '';
														break;
												case $reviewcat['rating'] <= .5:
														$rating_value = 'halfstar-small-box';
														break;
												case $reviewcat['rating'] <= 1:
														$rating_value = 'onestar-small-box';
														break;
												case $reviewcat['rating'] <= 1.5:
														$rating_value = 'onehalfstar-small-box';
														break;
												case $reviewcat['rating'] <= 2:
														$rating_value = 'twostar-small-box';
														break;
												case $reviewcat['rating'] <= 2.5:
														$rating_value = 'twohalfstar-small-box';
														break;
												case $reviewcat['rating'] <= 3:
														$rating_value = 'threestar-small-box';
														break;
												case $reviewcat['rating'] <= 3.5:
														$rating_value = 'threehalfstar-small-box';
														break;
												case $reviewcat['rating'] <= 4:
														$rating_value = 'fourstar-small-box';
														break;
												case $reviewcat['rating'] <= 4.5:
														$rating_value = 'fourhalfstar-small-box';
														break;
												case $reviewcat['rating'] <= 5:
														$rating_value = 'fivestar-small-box ';
														break;
											}
										?>
										<?php echo $this->translate($reviewcat['reviewcat_name']); ?>
										
									<?php else:?>
										<?php 
											switch($reviewcat['rating']) {
												case 0:
														$rating_value = '';
														break;
												case $reviewcat['rating'] <= .5:
														$rating_value = 'halfstar';
														break;
												case $reviewcat['rating'] <= 1:
														$rating_value = 'onestar';
														break;
												case $reviewcat['rating'] <= 1.5:
														$rating_value = 'onehalfstar';
														break;
												case $reviewcat['rating'] <= 2:
														$rating_value = 'twostar';
														break;
												case $reviewcat['rating'] <= 2.5:
														$rating_value = 'twohalfstar';
														break;
												case $reviewcat['rating'] <= 3:
														$rating_value = 'threestar';
														break;
												case $reviewcat['rating'] <= 3.5:
														$rating_value = 'threehalfstar';
														break;
												case $reviewcat['rating'] <= 4:
														$rating_value = 'fourstar';
														break;
												case $reviewcat['rating'] <= 4.5:
														$rating_value = 'fourhalfstar';
														break;
												case $reviewcat['rating'] <= 5:
														$rating_value = 'fivestar';
														break;
											}
										?>
										<b><?php echo $this->translate("Overall Rating");?></b>
									<?php endif; ?>
								</div>

								<?php if(!empty($reviewcat['reviewcat_name'])): ?>
									<div class="rates">
										<ul class='rating-box-small <?php echo $rating_value; ?>'>
											<li id="1" class="rate one">1</li>
											<li id="2" class="rate two">2</li>
											<li id="3" class="rate three">3</li>
											<li id="4" class="rate four">4</li>
											<li id="5" class="rate five">5</li>
										</ul>
									</div>
								<?php else:?>
									<div class="rates">
										<ul title="<?php echo $reviewcat['rating'].$this->translate(" rating"); ?>" class='rating <?php echo $rating_value; ?>' style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestorereview/externals/images/show-star-matrix.png);">
											<li id="1" class="rate one">1</li>
											<li id="2" class="rate two">2</li>
											<li id="3" class="rate three">3</li>
											<li id="4" class="rate four">4</li>
											<li id="5" class="rate five">5</li>
										</ul>
									</div>
							<?php endif;?>

							</div>
						<?php endforeach; ?>

					</div>


					<div class="sitestorereview_profile_info">
						<div class="sitestorereview_profile_title">
							<?php if($review->featured == 1): ?>
								<span>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								</span>
							<?php endif;?>
							<?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->sitestorereview()->truncateText($review->title, 60), array('title' => $review->title)) ?>
						</div>
							
						<div class="sitestorereview_profile_info_date">
							<?php echo $this->timestamp(strtotime($review->modified_date)) ?>
								-
								<?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
						</div>

						<div class="sitestorereview_profile_info_date"> 
							<?php echo $this->translate(array('%s comment', '%s comments', $review->comment_count), $this->locale()->toNumber($review->comment_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count)) ?>,
							<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)) ?>
						</div>

						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1)):?>
							<div class="sitestorereview_profile_info_date">
								<?php echo '<b>' .$this->translate("Pros: "). '</b>' .$this->viewMore($review->pros) ?>
							</div>
	
							<div class="sitestorereview_profile_info_date">
								<?php echo '<b>' .$this->translate("Cons: "). '</b>' .$this->viewMore($review->cons) ?>
							</div>
						<?php endif;?>
						
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
							<div class='sitestorereview_profile_info_date'>
								<?php if($review->recommend):?>
									<?php echo $this->translate("<b>Member's Recommendation:</b> Yes"); ?>
								<?php else: ?>
									<?php echo $this->translate("<b>Member's Recommendation:</b> No"); ?>
								<?php endif;?>
							</div>
						<?php endif;?>

						<div class='sitestorereview_profile_info_des'>
							<?php 
								if(strlen($review->body) > 300) {
								$read_complete_review = $this->htmlLink($review->getHref(), $this->translate('Read complete review'), array('title' => ''));
								$truncation_limit = 300;//Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.truncation.limit', 13);
								$tmpBody = strip_tags($review->body);
								$item_body = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... $read_complete_review" : $tmpBody );
								}
								else {
									$item_body = $review->body;
								}
							?>
							<?php echo $item_body; ?>
						</div>

						<div class="sitestorereview_profile_options">

							<?php
								$slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($review->title))), '-');
								echo $this->htmlLink(array(
										'route' => 'sitestorereview_detail_view',
										'owner_id' => $review->owner_id,
										'review_id' => $review->review_id,
										'slug' => $slug,
										'tab' => $this->identity_temp,
								), $this->translate('View Review'), array('class' => 'buttonlink icon_sitestores_review'));
							?>

							<?php if ($this->viewer_id == $review->owner_id): ?>
								<?php
									echo $this->htmlLink(array(
											'route' => 'sitestorereview_edit',
											'review_id' => $review->review_id,
											'store_id' => $this->store_id,
											'tab' => $this->identity_temp,
									), $this->translate('Edit Review'), array('class' => 'buttonlink icon_sitestores_edit'));
								?>
							<?php endif; ?>
	
							<?php if ($this->viewer_id == $review->owner_id ||  $this->level_id == 1 ): ?>
								<?php
									echo $this->htmlLink(
										array(
														'route' => 'sitestorereview_delete',
														'review_id' => $review->review_id,
														'store_id' => $this->store_id,
														'tab' => $this->identity_temp,
										), $this->translate('Delete Review'), array('class' => 'buttonlink icon_sitestores_delete')); 
								?>
							<?php endif; ?>
						</div>

					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<div >
				<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
					<div id="user_group_members_previous" class="paginator_previous">
						<?php
                      echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                              'onclick' => 'paginateSitestoreReview(sitestoreReviewStore - 1)',
                              'class' => 'buttonlink icon_previous'
                      )); ?>
					</div>
				<?php endif; ?>
				<?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
					<div id="user_group_members_next" class="paginator_next">
						<?php  echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
											'onclick' => 'paginateSitestoreReview(sitestoreReviewStore + 1)',
											'class' => 'buttonlink_right icon_next'
							));
						?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No reviews have been posted for this Store yet.'); ?>
				<?php if ($this->viewer_id)?>
					<?php if($this->can_create == 1 && empty($this->is_manageadmin)): ?>
						<?php	$show_link = $this->htmlLink(
															array('route' => 'sitestorereview_create', 'store_id' => $this->store_id, 'tab' => $this->identity_temp,),
															$this->translate('here'));
							$show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to write a review.');
							$show_label = sprintf($show_label, $show_link);
							echo $show_label;
						?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif; ?>

	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
			</div>
	<?php endif; ?>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>


<script type="text/javascript">
  var review_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewwidget', 3);?>';
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore) ?>';
  var store_communityad_integration = '<?php echo $store_communityad_integration; ?>';
	var execute_Request_Review = '<?php echo $this->show_content;?>';
  var is_ajax_divhide = '<?php echo $this->isajax;?>';
  var show_widgets = '<?php echo $this->widgets ?>'; 
  var store_url = '<?php echo Engine_Api::_()->sitestore()->getStoreUrl($this->store_id)?>';
	//window.addEvent('domready', function () {
	var ReviewtabId = '<?php echo $this->module_tabid;?>';
	var ReviewTabIdCurrent = '<?php echo $this->identity_temp; ?>';
	if (ReviewTabIdCurrent == ReviewtabId) {
		if(store_showtitle != 0) {
			if($('profile_status')  && show_widgets == 1) {
			  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Reviews');?></h2>";	
			}		
			if($('layout_review')) {
		   $('layout_review').style.display = 'block';
		  }
		}
    hideWidgetsForModule('sitestorereview');
		prev_tab_id = '<?php echo $this->content_id; ?>'; 
		prev_tab_class = 'layout_sitestorereview_profile_sitestorereviews';
		execute_Request_Review = true;
		hideLeftContainer (review_ads_display, store_communityad_integration, adwithoutpackage);	
	} 
	else if (is_ajax_divhide != 1) {  	
  	if($('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews')) {
			$('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews').style.display = 'none';
	  } 	
	}
	//});

	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$('global_content').getElement('.layout_sitestorereview_profile_sitestorereviews').style.display = 'block';
		if(store_showtitle != 0) {
			if($('profile_status')  && show_widgets == 1) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Reviews');?></h2>";	
			}
		}		
    hideWidgetsForModule('sitestorereview');
	  $('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }
		
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Review = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';
			prev_tab_class = 'layout_sitestorereview_profile_sitestorereviews';
			
		}
		if(execute_Request_Review == false) {
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Review, '<?php echo $this->identity_temp?>', 'review', 'sitestorereview', 'profile-sitestorereviews', store_showtitle,'<?php echo Engine_Api::_()->sitestore()->getStoreUrl($this->store_id)?>', review_ads_display, store_communityad_integration, adwithoutpackage);
		} 
		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1);?>' && review_ads_display == 0)
{setLeftLayoutForStore();		    }	  			    
	});
</script>