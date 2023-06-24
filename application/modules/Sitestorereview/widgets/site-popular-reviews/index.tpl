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
<?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
<ul class="sitestore_sidebar_list">
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
          <?php echo $this->translate("on ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($review->store_id, $review->owner_id, $review->getSlug()), $store_title, array('title' => $review->store_title)) ?>
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
</ul>