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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
			var anchor = $('profile_sitestoremembers_<?php echo $this->category_id?>').getParent();
			$('profile_sitestoremember_previous_<?php echo $this->category_id?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
			$('profile_sitestoremember_next_<?php echo $this->category_id?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

			$('profile_sitestoremember_previous_<?php echo $this->category_id?>').removeEvents('click').addEvent('click', function(){
				en4.core.request.send(new Request.HTML({
					url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
					data : {
						format : 'html',
						subject : en4.core.subject.guid,
						store : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
            isajax: 1
					}
				}), {
					'element' : anchor
				})
			});

			$('profile_sitestoremember_next_<?php echo $this->category_id?>').removeEvents('click').addEvent('click', function(){
				en4.core.request.send(new Request.HTML({
					url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
					data : {
						format : 'html',
						subject : en4.core.subject.guid,
						store : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
            isajax: 1
					}
				}), {
					'element' : anchor
				})
			});
    <?php endif; ?>
  });
</script>
<ul id="profile_sitestoremembers_<?php echo $this->category_id;?>"  class="sitestores_profile_tab">
	<?php if ($this->subject_id == $this->viewer_id) : ?>
	<li>
		<?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'joined-more-stores', 'user_id' => $this->viewer_id), $this->translate('Join More Stores'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_join_store')); ?>
	</li>
	<?php endif; ?>
  <?php foreach ($this->paginator as $item): ?>
    				<li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?><?php if($item->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
					<?php if($item->featured):?>
						<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/featured-label.png', '',  array('title' => 'Featured','class' => 'sitestore_featured_label')) ?>
				<?php endif;?>
			<?php endif;?>
      <div class='sitestores_profile_tab_photo'>
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'left'))) ?>
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
          <?php if (!empty($item->sponsored)): ?>
						<?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
						if (!empty($sponsored)) { ?>
							<div class="sitestore_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
								<?php echo $this->translate('SPONSORED'); ?>                 
							</div>
						<?php } ?>
					<?php endif; ?>
				<?php endif; ?>
      </div>
      <div class='sitestores_profile_tab_info'>
        <div class='sitestores_profile_tab_title'>
          <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($item->store_id, $item->owner_id, $item->getSlug()), $item->getTitle()) ?>
          <div class="fright">
            <?php if ($this->ratngShow): ?>
              <?php if (($item->rating > 0)): ?>

                <?php
                $currentRatingValue = $item->rating;
                $difference = $currentRatingValue - (int) $currentRatingValue;
                if ($difference < .5) {
                  $finalRatingValue = (int) $currentRatingValue;
                } else {
                  $finalRatingValue = (int) $currentRatingValue + .5;
                }
                ?>

                <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                  <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                    <span class="rating_star_generic rating_star" ></span>
                  <?php endfor; ?>
                  <?php if ((round($item->rating) - $item->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half" ></span>
                  <?php endif; ?>
                </span>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($item->closed): ?>
              <span>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
              </span>
            <?php endif; ?>
            <span>
                <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)) :?>
                  <?php if ($item->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($item->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
            </span>

						<?php if (!empty($item->store_owner_id)) : ?>
							<span>
							<?php if ($this->viewer_id != $item->owner_id && $this->subject_id == $this->viewer_id) : ?>
								<?php echo $this->htmlLink(array('route' => 'sitestore_profilestoremember', 'action' => 'leave', 'store_id' => $item->store_id), $this->translate('Leave Store'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_leave')); ?>
							<?php endif; ?>
							</span>
						<?php endif; ?>
          </div>
          <div class="clr"></div>
        </div>
        <div class='sitestores_browse_info_date  seaocore_txt_light'>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?> 
            <?php if($postedBy):?>
              - <?php echo $this->translate('posted by'); ?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
          <?php endif; ?>
          <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
          
					<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) : ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
						if ($item->member_title && $memberTitle) : ?>
								<?php if ($item->member_count == 1) : ?>
									<?php echo $item->member_count . ' member'; ?>,
								<?php  else: ?>
									<?php echo $item->member_count . ' ' . $item->member_title; ?>,
								<?php endif; ?>
						<?php else : ?>
								<?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>,
						<?php endif; ?>
					<?php endif; ?>
					
					<?php $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview'); ?>
					<?php if ($sitestorereviewEnabled): ?>
						<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
					<?php endif; ?>

          <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
          <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
        </div>
        <div class='sitestores_browse_info_blurb clr'>
            <?php //echo $item->member_approval; ?>
            <?php if (!empty($item->store_owner_id)) : ?>
							<div class='sitestores_profile_info_member'>
									<?php if ($this->viewer_id == $item->owner_id) : ?>
									<i class="icon_sitestores_store-owner"><?php echo $this->translate("STOREMEMBER_OWNER"); ?></i>
									<?php else: ?>
									<?php if(!empty($this->showMemberText)) : ?>
										<i class="icon_sitestore_store-member">
										<?php if (empty($item->member_title)) : ?>
											<?php echo $this->translate("STOREMEMBER_MEMBER"); ?>
										<?php else: ?>
											<?php echo $item->member_title; ?>
										<?php endif ?>
										</i>
									<?php endif; ?>
									<?php endif; ?>
							</div>
            <?php endif; ?>
            <?php if (!empty($this->textShow)) : ?>
							<?php if (!empty($item->store_owner_id)) : ?>
								<div class='sitestores_profile_info_member'>
										<?php if ($this->viewer_id != $item->owner_id && empty($item->member_approval)) : ?>
												<i class="icon_sitestore_verified_store"><?php echo $this->translate($this->textShow); ?></i>
										<?php endif; ?>
								</div>
							<?php endif; ?>
            <?php endif; ?>
        </div>
        <div class='sitestores_browse_info_blurb clr'>
          <?php
          // Not mbstring compat
          echo substr(strip_tags($item->body), 0, 350);
          if (strlen($item->body) > 349)
            echo $this->translate("...");
          ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<div>
  <div id="profile_sitestoremember_previous_<?php echo $this->category_id?>" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_sitestoremember_next_<?php echo $this->category_id?>" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>