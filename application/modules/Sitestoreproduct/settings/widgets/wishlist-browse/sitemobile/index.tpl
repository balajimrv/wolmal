<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php if (count($this->paginator) > 0): ?>

  <div class="ui-member-list-head">
    <?php echo $this->translate(array('%s wishlist found.', '%s wishlists found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>

  <div class="sm-content-list">
    <ul class="sr_reviews_listing" data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $wishlist):?>
        <li>
          <a href="<?php echo $wishlist->getHref() ?>">
            <?php echo $this->itemPhoto($wishlist->getCoverItem(), 'thumb.icon') ?>
            <h3><?php echo $wishlist->title ?></h3>
            <?php if (!empty($this->statisticsWishlist)): ?>
              <p>
                <?php
                $statistics = '';
                if (in_array('followCount', $this->statisticsWishlist)) {
                  $statistics .= $this->translate(array('%s follower', '%s followers', $wishlist->follow_count), $this->locale()->toNumber($wishlist->follow_count)).' - ';
                }

                if(in_array('productCount', $this->statisticsWishlist)) {
                    $statistics .= $this->translate(array('%s product', '%s products', $wishlist->total_item), $this->locale()->toNumber($wishlist->total_item)).' - ';
                  }  

                if (in_array('viewCount', $this->statisticsWishlist)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $wishlist->view_count), $this->locale()->toNumber($wishlist->view_count)).' - ';
                }

                if (in_array('likeCount', $this->statisticsWishlist)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $wishlist->like_count), $this->locale()->toNumber($wishlist->like_count)).' - ';
                }
                  $statistics = trim($statistics);
                  $statistics = rtrim($statistics, ' - ');

                ?>
                <?php echo $statistics; ?>
              </p>
            <?php endif; ?>
            <p>
              <?php echo $this->translate('%s - created by %s', $this->timestamp($wishlist->creation_date), "<b>".$wishlist->getOwner()->getTitle()."</b>") ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>

<?php elseif ($this->isSearched > 2): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a wishlist with that criteria.'); ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a wishlist yet.'); ?>
    </span>
  </div>
<?php endif; ?>
