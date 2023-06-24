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

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<div class="sm-content-list">
	<ul id="profile_stores" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $item): ?>
			<li>
				<a href="<?php echo $item->getHref(); ?>">
					<?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
					<h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 25) ?></h3>
          <p>
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
          </p>
          <p>
            <?php if($postedBy):?><?php echo $this->translate('posted by'); ?>
								<strong><?php echo $item->getOwner()->getTitle() ?></strong> - 
						<?php endif; ?>
					 <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          </p>
					<p class="ui-li-aside">
            <?php if ($item->closed): ?>
               <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
            <?php endif; ?>
						<?php if ($item->sponsored == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						<?php endif; ?>
						<?php if ($item->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						<?php endif; ?>
					</p>
          <p>
						<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
						<?php $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview'); ?>
						<?php if ($sitestorereviewEnabled): ?>
							<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
						<?php endif; ?>
            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
            <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
          </p>
          <p>
						<?php if (!empty($item->store_owner_id)) : ?>
							<?php if ($item->store_owner_id == $item->owner_id) : ?>
								<i class="icon_sitestores_store-owner"><?php echo $this->translate("STOREMEMBER_OWNER"); ?></i>
							<?php  else: ?>
								<i class="icon_sitestore_member"><?php echo $this->translate("STOREMEMBER_MEMBER"); ?></i>
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
						$this->paginator, $this->identity, 'profile_stores', array('storeAdmin' => $this->storeAdmin, "category_id" => $this->category_id));
		?>
	<?php endif; ?>
</div>