<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h3><?php echo $this->translate("%s's Reviews", $this->review->getOwner()->toString()); ?></h3>
<ul class="sr_sitestoreproduct_profile_side_product sr_sitestoreproduct_side_widget">
  <?php foreach ($this->reviews as $review): ?>
    <li>

      <?php echo $this->htmlLink($review->getParent()->getHref(), $this->itemPhoto($review->getParent(), 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $review->getParent()->title), array('title' => $review->getParent()->title)) ?>

      <div class='sr_sitestoreproduct_profile_side_product_info'>

        <div class='sr_sitestoreproduct_profile_side_product_title'>
          <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, $this->title_truncation), array('title' => $review->title)) ?>
        </div>

        <div class='sr_sitestoreproduct_profile_side_product_stats seaocore_txt_light'>
          <?php echo $this->translate("on ") . $this->htmlLink($review->getParent()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->getParent()->title, $this->title_truncation), array('title' => $review->getParent()->title)) ?>
        </div>

        <div class='seaocore_sidebar_list_details'>
          <?php echo $this->showRatingStarSitestoreproduct($review->rating, "$review->type", 'small-star'); ?>
        </div>

        <?php if ($this->type != 'editor' && !empty($this->statistics)): ?>
          <br />
          <div class='sr_sitestoreproduct_profile_side_product_stats seaocore_txt_light'>
            <?php
            $statistics = '';

            if (in_array('likeCount', $this->statistics)) {
              $statistics .= $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)) . ', ';
            }

            if (in_array('commentCount', $this->statistics)) {
              $statistics .= $this->translate(array('%s comment', '%s comments', $review->comment_count), $this->locale()->toNumber($review->comment_count)) . ', ';
            }

            if (in_array('viewCount', $this->statistics)) {
              $statistics .= $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count)) . ', ';
            }

            if (in_array('replyCount', $this->statistics)) {
              $statistics .= $this->translate(array('%s reply', '%s replies', $review->reply_count), $this->locale()->toNumber($review->reply_count)) . ', ';
            }

            if (in_array('helpfulCount', $this->statistics) && ($review->type == 'user' || $review->type == 'vistior')) {
              $statistics .= $this->translate('%s helpful', $review->helpful_count . '%') . ', ';
            }

            $statistics = trim($statistics);
            $statistics = rtrim($statistics, ',');
            ?>
            <?php echo $statistics; ?>
          </div>  
        <?php endif; ?>

      </div>
    </li>
  <?php endforeach; ?>
</ul>