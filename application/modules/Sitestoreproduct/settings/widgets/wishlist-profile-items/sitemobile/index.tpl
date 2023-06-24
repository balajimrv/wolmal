<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
  $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
  $ratingType = 'overall';
} else {
  $ratingType = 'user';
}
?>

<div class="sr_wishlist_view">
  <h3>
    <?php echo $this->subject()->title; ?> 
  </h3>
  <p class="sr_wishlist_view_des mbot10">
    <?php echo $this->subject()->body; ?>
  </p>

  <div class="sm-ui-cont-head">
    <?php if ($this->postedby): ?>
      <div class="sm-ui-cont-author-photo">
        <?php echo $this->htmlLink($this->subject()->getOwner(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
      </div>
    <?php endif; ?>
    <div class="sm-ui-cont-cont-info">
      <?php if ($this->postedby): ?>
        <div class="sm-ui-cont-author-name">
          <?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>
        </div>
      <?php endif; ?>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->timestamp($this->subject()->creation_date) ?> 
      </div>
      <?php if (!empty($this->statisticsWishlist)): ?>
        <div class="sm-ui-cont-cont-date">
          <?php
          $statistics = array();
          if (in_array('followCount', $this->statisticsWishlist)) {
            $statistics [] = $this->translate(array('<b>%s</b> Follower', '<b>%s</b> Followers', $this->subject()->follow_count), $this->locale()->toNumber($this->subject()->follow_count));
          }

           if (in_array('productCount', $this->statisticsWishlist)) {
                $statistics .= $this->translate(array('<b>%s</b> Product', '<b>%s</b> Products', $this->total_item), $this->locale()->toNumber($this->total_item));
              }

          if (in_array('viewCount', $this->statisticsWishlist)) {
            $statistics [] = $this->translate(array('<b>%s</b> View', '<b>%s</b> Views', $this->subject()->view_count), $this->locale()->toNumber($this->subject()->view_count));
          }

          if (in_array('likeCount', $this->statisticsWishlist)) {
            $statistics [] = $this->translate(array('<b>%s</b> Like', '<b>%s</b> Likes', $this->subject()->like_count), $this->locale()->toNumber($this->subject()->like_count));
          }
          ?>
          <?php echo join($statistics, ' - '); ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
  <?php if ($this->viewer_id && (!empty($this->followLike) || !empty($this->messageOwner))): ?>
    <div class="seaocore_profile_cover_buttons">
      <table cellpadding="2" cellspacing="0">
        <tbody> 
          <tr>
            <?php if (!empty($this->followLike) && in_array('like', $this->followLike)): ?>
              <td>
                <a href ="javascript://"  data-role='button' data-icon='thumbs-down' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>unlike_link" <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display:none;" <?php endif; ?> onclick="sm4.core.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');">
                  <span><?php echo $this->translate('Unlike') ?></span>
                </a>
                <a data-role='button' data-icon='thumbs-up' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' href = "javascript://" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>like_link" <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> onclick="sm4.core.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');"  >
                  <span><?php echo $this->translate('Like') ?></span>
                </a>
              </td>
            <?php endif; ?>
            <?php if (!empty($this->followLike) && in_array('follow', $this->followLike)): ?>
              <td>
                <?php $check_availability = $this->subject()->follows()->isFollow($this->viewer); ?>
                <a href ="javascript://" data-role='button' data-icon='delete' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="sitestoreproduct_wishlist_unfollows_<?php echo $this->subject()->wishlist_id; ?>" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->subject()->wishlist_id; ?>', 'sitestoreproduct_wishlist');" style ='display:<?php echo $check_availability ? "block" : "none" ?>'>
                  <span><?php echo $this->translate('Unfollow') ?></span>
                </a>
                <a href = "javascript://" data-role='button' data-icon='plus' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="sitestoreproduct_wishlist_most_follows_<?php echo $this->subject()->wishlist_id; ?>" style ='display:<?php echo empty($check_availability) ? "block" : "none" ?>' onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->subject()->wishlist_id; ?>', 'sitestoreproduct_wishlist');" >
                  <span><?php echo $this->translate('Follow') ?></span>
                </a>
                <input type ="hidden" id = "sitestoreproduct_wishlist_follow_<?php echo $this->subject()->wishlist_id; ?>" value = '<?php echo $check_availability ? $check_availability : 0; ?>' />
              </td>
            <?php endif; ?>
            <?php if (!empty($this->messageOwner)): ?>
              <td>
                <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_wishlist_general', 'action' => 'message-owner', 'wishlist_id' => $this->wishlist->getIdentity()), $this->translate('Message Owner'), array('class' => 'smoothbox icon_sitestoreproduts_messageowner', 'data-role' => 'button', 'data-inset' => 'false', 'data-mini' => 'true', 'data-corners' => 'false', 'data-shadow' => 'true', 'data-icon' => 'envelope')) ?>
              </td>
            <?php endif; ?>
          </tr></tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php if ($this->total_item > 0): ?>
  <div class="sm-content-list">
    <ul class="sr_reviews_listing" data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $product): ?>
        <li>
          <a href="<?php echo $product->getHref(array('profile_link' => 1)) ?>" >
            <?php echo $this->itemPhoto($product, 'thumb.normal') ?>
            <h3><?php echo $product->getTitle(); ?></h3>
            <p>
              <?php if ($ratingValue == 'rating_both'): ?>
                <?php echo $this->showRatingStarSitestoreproduct($product->rating_editor, 'editor', $ratingShow); ?>
                <br/>
                <?php echo $this->showRatingStarSitestoreproduct($product->rating_users, 'user', $ratingShow); ?>
              <?php else: ?>
                <?php echo $this->showRatingStarSitestoreproduct($product->$ratingValue, $ratingType, $ratingShow); ?>
              <?php endif; ?>
            </p>
            <p> <?php echo $this->translate("in %s", '<b>' . $product->getCategory()->getTitle(true). '</b>')?></p>
            <p><?php echo $this->timestamp(strtotime($product->date)) ?>
            </p>
            <p><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($product->body, 150); ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($this->paginator->count() > 1): ?>
    <br />
    <?php
    echo $this->paginationControl(
            $this->paginator, null, null);
    ?>
  <?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no entries in this wishlist.'); ?>
    </span> 
  </div>
<?php endif; ?>