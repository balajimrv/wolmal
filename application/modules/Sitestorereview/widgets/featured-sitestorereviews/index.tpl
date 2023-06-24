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
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>

<script type="text/javascript">
  function viewMoreReview()
  {
    $('review_view_more').style.display ='none';
    $('review_loding_image').style.display = 'block';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitestorereview/name/featured-sitestorereviews',
      'data' : {
        format : 'html',
        isajax : 1,
				itemCount : '<?php echo $this->itemCount; ?>',
				store: getNextReviewStore()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$('review_loding_image').style.display = 'none';
				$('review_view_more').style.display ='block';
        $('reviews_layout').innerHTML = responseHTML;
				var review_content = $('reviews_layout').getElement('.layout_sitestorereview_featured_sitestorereviews').innerHTML;
				$('reviews_layout').innerHTML = review_content;
      }
    }));

    return false;
  }  

	function getNextReviewStore(){
		var next_store = '<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>';
		var total_store = '<?php echo $this->total_store ?>';
		if(total_store >= next_store) {
			return next_store;
		}
	}
</script>

<?php if(empty($this->is_ajax)):?>
	<ul class="sitestore_sidebar_list" id="reviews_layout">
<?php endif; ?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<?php foreach ($this->paginator as $review): ?>
  <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $review->store_id);?>
	<li class="sitestorereview_show_tooltip_wrapper">
		<div class="sitestorereview_show_tooltip">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/tooltip_arrow.png" alt="" class="arrow" />
			<?php echo Engine_Api::_()->sitestorereview()->truncateText($review->body, 100) ?>
		</div>
		<?php $user = Engine_Api::_()->getItem('user', $review->owner_id); ?>
		<?php if(!empty($photo_review)):?>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle())) ?>
      <?php else:?>
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()), $this->itemPhoto($sitestore_object, 'thumb.icon'), array('title' => $sitestore_object->getTitle()));?>
    <?php endif;?>
		<div class='sitestore_sidebar_list_info'>
			<div class='sitestore_sidebar_list_title'>
				<?php $review_title = Engine_Api::_()->sitestorereview()->truncateText($review->title, 28);?>
				<?php echo $this->htmlLink($review->getHref(), $review_title, array('title' => $review->title)) ?>
			</div>

			<div class='sitestore_sidebar_list_details'>
				<?php $store_title = Engine_Api::_()->sitestorereview()->truncateText($review->store_title, 18); ?>
				<?php echo $this->translate("on ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($review->store_id, $review->owner_id, $review->getSlug()), $store_title, array('title' => $review->store_title,'class' => 'bold')) ?>
			</div>

			<div class='sitestore_sidebar_list_details'>
				<span title="<?php echo $review->rating . $this->translate(' rating'); ?>">
					<?php if (($review->rating > 0)): ?>
						<?php for ($x = 1; $x <= $review->rating; $x++): ?>
							<span class="rating_star_generic rating_star"></span>
						<?php endfor; ?>
						<?php if ((round($review->rating) - $review->rating) > 0): ?>
							<span class="rating_star_generic rating_star_half"></span>
						<?php endif; ?>
					<?php endif; ?>
				</span>
			</div>
		</div>
	</li>
<?php endforeach; ?>

<?php if($this->total_store > 1): ?>
	<li>
		<div id="review_view_more" onclick="viewMoreReview()" class="sitestore_sidebar_list_seeall">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('More &raquo;'), array(
				'id' => 'review_feed_viewmore_link'
			)) ?>
		</div>

		<div id="review_loding_image" style="display:none;" class="sitestore_sidebar_list_seeall">
			<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
			<?php echo $this->translate("Loading...") ?>
		</div>
	</li>
<?php endif?>

<?php if(empty($this->is_ajax)):?>
	</ul>
<?php endif; ?>