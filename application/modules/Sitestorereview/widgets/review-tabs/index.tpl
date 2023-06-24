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
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<?php if(empty($this->is_ajax)): ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">

  <ul id="main_tabs">
   
		<?php if(in_array('recent', $this->visibility)): ?>
			<li id = 'sitestorereview_recent_tab' class = 'active' >
				<a href='javascript:void(0);'  onclick="tabSwitchSitestorereview('recent');"><?php echo $this->translate('Recent') ?></a>
			</li>
		<?php endif; ?>

		<?php if(in_array('popular', $this->visibility) && !in_array('recent', $this->visibility)): ?>
			<li id = 'sitestorereview_popular_tab' class = 'active' >
				<a href='javascript:void(0);'  onclick="tabSwitchSitestorereview('popular');"><?php echo $this->translate('Popular') ?></a>
			</li>
		<?php elseif(in_array('popular', $this->visibility)): ?>
			<li id = 'sitestorereview_popular_tab' >
				<a href='javascript:void(0);'  onclick="tabSwitchSitestorereview('popular');"><?php echo $this->translate('Popular') ?></a>
			</li>
		<?php endif; ?>

		<?php if(in_array('reviewer', $this->visibility) && !in_array('popular', $this->visibility) && !in_array('recent', $this->visibility)): ?>
			<li id = 'sitestorereview_reviewer_tab' class = 'active'>
				<a href='javascript:void(0);'  onclick="tabSwitchSitestorereview('reviewer');"><?php echo $this->translate('Top Reviewers') ?></a>
			</li>
		<?php elseif(in_array('reviewer', $this->visibility)): ?>	
			<li id = 'sitestorereview_reviewer_tab'>
				<a href='javascript:void(0);'  onclick="tabSwitchSitestorereview('reviewer');"><?php echo $this->translate('Top Reviewers') ?></a>
			</li>
		<?php endif; ?>
  </ul>

</div>
<div id="sitestorereview_ajax_tabs">
<?php endif; ?>

<div id="sitelbum_albums_tabs">

  <?php if( Count($this->paginator) > 0 && $this->tabName == 'reviewer'): ?>

		<ul class="seaocore_browse_list">
			<?php foreach( $this->paginator as $user ): ?>
				<li>
					<div class="seaocore_browse_list_photo">
						<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => 'popularmembers_thumb', 'title' => $user->getTitle())) ?>
					</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle())) ?>
						</div>
						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->translate("No. of Reviews: %d", $user->review_count); ?>
						</div>

						<?php $store = Engine_Api::_()->sitestorereview()->getLinkedStore($user->max_review_id); ?>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->translate("Latest on: ").$this->htmlLink(Engine_Api::_()->sitestore()->getHref($store->store_id, $store->owner_id), Engine_Api::_()->sitestore()->truncation($store->title), array('title' => $store->title,'class' => 'bold')); ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php elseif( Count($this->paginator) > 0 && ($this->tabName == 'recent' || $this->tabName == 'popular')): ?>

		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $review): ?>
			  <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $review->store_id);?>
				<?php $user = Engine_Api::_()->getItem('user', $review->owner_id); ?>
		    <li>
          <?php if(!empty($photo_review)):?>
						<div class="seaocore_browse_list_photo">
							<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('title' => $user->getTitle())) ?>
		        </div>
				  <?php else:?>
						<div class="seaocore_browse_list_photo"><?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()), $this->itemPhoto($sitestore_object, 'thumb.normal'), array('title' => $sitestore_object->getTitle())); ?></div>
				  <?php endif;?>	
		      <div class='seaocore_browse_list_info'>
		        <div class='seaocore_browse_list_info_title'>
		          <?php
		          $truncation_limit = 60;//Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.truncation.limit', 65);
		          $review_title = Engine_Api::_()->sitestorereview()->truncateText($review->title, $truncation_limit);
		
		          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		          $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $review->store_id, $layout);
		          ?>
		          <?php echo $this->htmlLink($review->getHref(), $review_title, array('title' => $review->title)) ?>
		        </div>
						<span title="<?php echo $review->rating . $this->translate(' rating'); ?>" class="clear">
							<?php if (($review->rating > 0)): ?>
								<?php for ($x = 1; $x <= $review->rating; $x++): ?>
									<span class="rating_star_generic rating_star"></span>
								<?php endfor; ?>
								<?php if ((round($review->rating) - $review->rating) > 0): ?>
									<span class="rating_star_generic rating_star_half"></span>
								<?php endif; ?>
							<?php endif; ?>
						</span> 

						<div class='seaocore_browse_list_info_date'>
							<?php $store_title = Engine_Api::_()->sitestorereview()->truncateText($review->store_title, 60); ?>
							<?php echo $this->translate("on ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($review->store_id, $review->owner_id, $review->getSlug()), $store_title, array('title' => $review->store_title,'class' => 'bold')) ?>
						</div>

		        <div class='seaocore_browse_list_info_date'>
							<?php echo $this->translate(array('%s comment', '%s comments', $review->comment_count), $this->locale()->toNumber($review->comment_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count)) ?>,
							<?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)) ?>
						</div>
						<div class="seaocore_browse_list_info_blurb">
							<?php echo Engine_Api::_()->sitestorereview()->truncateText($review->body, 125); ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No reviews have been posted yet.');?>
      </span>
    </div>
  <?php endif; ?>
<?php if(empty($this->is_ajax)): ?>
</div>
<?php endif; ?>


<?php if(empty($this->is_ajax)): ?>
	</div>
	</div>

	<script type="text/javascript">
		
		var tabSwitchSitestorereview = function (tabName) {

		if($('sitestorereview_recent_tab'))
					$('sitestorereview_recent_tab').erase('class');
		if($('sitestorereview_popular_tab'))
					$('sitestorereview_popular_tab').erase('class');
		if($('sitestorereview_reviewer_tab'))
					$('sitestorereview_reviewer_tab').erase('class');
		
	if($('sitestorereview_'+tabName+'_tab'))
					$('sitestorereview_'+tabName+'_tab').set('class', 'active');
		if($('sitestorereview_ajax_tabs')) {
				$('sitestorereview_ajax_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="sitestore_tabs_loader_img" /></center>';
			}

			var request = new Request.HTML({
			method : 'post',
				'url' : en4.core.baseUrl + 'widget/index/mod/sitestorereview/name/review-tabs',
				'data' : {
					format : 'html',
					isajax : 1,
					category_id : '<?php echo $this->category_id?>',
					tabName: tabName,
					itemCount: '<?php echo $this->itemCount; ?>',
					popularity: '<?php echo $this->popularity; ?>'
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
							$('sitestorereview_ajax_tabs').innerHTML = responseHTML;
				}
			});

			request.send();
		}
	</script>
<?php endif; ?>