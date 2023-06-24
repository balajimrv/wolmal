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
<ul class="sitestore_sidebar_list">
	<?php foreach ($this->topRatedStores as $sitestore): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.icon')) ?>
			<div class='sitestore_sidebar_list_info'>
				<div class='sitestore_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle()), array('title' => $sitestore->getTitle())) ?>
				</div>
				<div class='sitestore_sidebar_list_details'>
					<?php if (($sitestore->rating > 0)): ?>

						<?php
						$currentRatingValue = $sitestore->rating;
						$difference = $currentRatingValue - (int) $currentRatingValue;
						if ($difference < .5) {
							$finalRatingValue = (int) $currentRatingValue;
						} else {
							$finalRatingValue = (int) $currentRatingValue + .5;
						}
						?>

						<span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
							<?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
								<span class="rating_star_generic rating_star"></span>
							<?php endfor; ?>
							<?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half"></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
  <li class="sitestore_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('action' => 'index','orderby'=> 'rating'), 'sitestore_general', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>