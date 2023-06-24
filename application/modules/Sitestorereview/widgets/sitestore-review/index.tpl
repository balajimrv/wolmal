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
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorereview/externals/styles/style_sitestorereview.css')
	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestorereview/externals/styles/show_star_rating.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestorereview_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>

	<ul class="seaocore_browse_list">
		<?php foreach ($this->paginator as $review): ?>
			<li>
				<div class="seaocore_browse_list_photo"> 
          <?php $user = Engine_Api::_()->getItem('user', $review->owner_id); ?>
					<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $review->store_id);?>
          <?php if(!empty($photo_review)):?>
						<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('title' => $user->getTitle())) ?>
					<?php else:?>
					<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()), $this->itemPhoto($sitestore_object, 'thumb.normal'), array('title' => $sitestore_object->getTitle())) ?>
          <?php endif;?>
				</div>
				<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($review->review_id); ?>
				<div class="sitestorereview_overallrating sitestorereview_browse_overallrating">		
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
				<div class="seaocore_browse_list_info sitestorereview_browse_info">
					<div class="seaocore_browse_list_info_title">
						<?php if($review->featured == 1): ?>
							<span>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/icons/sitestoredocument_goldmedal1.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							</span>
						<?php endif;?>
						<h3><?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->sitestorereview()->truncateText($review->title, 60), array('title' => $review->title)) ?></h3>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($review->store_id, $review->owner_id, $review->getSlug()),  $sitestore_object->title,array('title' => $sitestore_object->title,'class' => 'bold')) ?>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php echo $this->timestamp(strtotime($review->modified_date)) ?>
							-
							<?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
					</div>

					<div class="seaocore_browse_list_info_date"> 
						<?php echo $this->translate(array('%s comment', '%s comments', $review->comment_count), $this->locale()->toNumber($review->comment_count)) ?>,
						<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count)) ?>,
						<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)) ?>
					</div>

					<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1)):?>
						<div class="seaocore_browse_list_info_blurb">
							<?php echo '<b>' .$this->translate("Pros: "). '</b>' .$this->viewMore($review->pros) ?>
						</div>

						<div class="seaocore_browse_list_info_blurb">
							<?php echo '<b>' .$this->translate("Cons: "). '</b>' .$this->viewMore($review->cons) ?>
						</div>
					<?php endif;?>
					
					<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
						<div class='seaocore_browse_list_info_blurb'>
							<?php if($review->recommend):?>
								<?php echo $this->translate("<b>Member's Recommendation:</b> Yes"); ?>
							<?php else: ?>
								<?php echo $this->translate("<b>Member's Recommendation:</b> No"); ?>
							<?php endif;?>
						</div>
					<?php endif;?>

					<div class='seaocore_browse_list_info_blurb'>
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
			</li>
		<?php endforeach; ?>
	</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestorereview"), array("orderby" => $this->orderby)); ?>
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
    
		form.submit();
  } 
</script>