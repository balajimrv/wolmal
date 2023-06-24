<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$sitestoreproduct = $this->sitestoreproduct;
$ratingShow = $this->ratingShow;
$ratingType = $this->ratingType;
$ratingValue = $this->ratingValue;
$showAddToCart = $this->showAddToCart;
$showinStock = $this->showinStock;
$widget_id = $this->widget_id;
$priceWithTitle = $this->priceWithTitle;
?>
<?php $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct'); ?>
  <li class="sitestoreproduct_q_v_wrap sr_sitestoreproduct_carousel_content_item g_b" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
    <div>
      <?php if($sitestoreproduct->newlabel && $this->newIcon):?>
        <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
      <?php endif;?>
      <div class="sitestoreproduct_grid_view_thumb_wrapper">
        <?php $product_id = $sitestoreproduct->product_id; ?>
        <?php $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id);
        $temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($sitestoreproduct->store_id);
        ?>

        <?php $quickViewButton = true; ?>
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
        <a href="<?php echo $sitestoreproduct->getHref() ?>" class="sitestoreproduct_grid_view_thumb" title="<?php echo $sitestoreproduct->getTitle()?>">
          <?php
          $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png';
          $temp_url = $sitestoreproduct->getPhotoUrl('thumb.profile');
          if (!empty($temp_url)): $url = $sitestoreproduct->getPhotoUrl('thumb.profile');
          endif;
          ?>
          <span style="background-image: url(<?php echo $url; ?>);"></span>
        </a>
      </div>
      <div class="sitestoreproduct_grid_title">
        <?php if( !empty($priceWithTitle)  && ((!empty($temp_allowed_selling) && $sitestoreproduct->allow_purchase) || !empty($temp_non_selling_product_price)) ) : ?>
          <span class="fright sitestoreproduct_price_sale">
          <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productTable->getProductDiscountedPrice($sitestoreproduct->product_id)); ?>
          </span>
        <?php endif; ?>
        <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())) ?>
      </div>

        <?php if(!empty($this->showOptions) && in_array('category', $this->showOptions)): ?>
          <div class="sitestoreproduct_grid_stats clr"> 
            <a href="<?php echo $sitestoreproduct->getCategory()->getHref() ?>"> 
              <?php $catTitle = $sitestoreproduct->getCategory()->getTitle(true); ?>
              <?php $catTitle = @trim($catTitle); ?>
              <?php echo $this->translate($catTitle); ?>
            </a>
          </div>
        <?php endif; ?>
        <?php if( !empty($priceWithTitle) ) : ?>
          <?php echo $this->getProductInfo($sitestoreproduct, $widget_id, 'list_view', $showAddToCart, $showinStock, true); ?>
        <?php endif; ?>
        
        <?php if(!empty($this->showOptions) && (in_array('review', $this->showOptions) ||in_array('rating', $this->showOptions))): ?>
        <div class="sitestoreproduct_grid_rating"> 
          <?php if(!empty($this->showOptions) && in_array('rating', $this->showOptions)): ?>          
            <?php if ($ratingValue == 'rating_both'): ?>
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
            <?php else: ?>
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
            <?php endif; ?>
          <?php endif; ?> 
          <?php if((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3) == 2) && (!empty($this->showOptions) && in_array('review', $this->showOptions))): ?>
            <span>
              <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->partial(
                              '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct))); ?>
            </span>
          <?php endif; ?>
        </div>  
      <?php endif; ?>
        
      <?php if((!empty($this->showOptions) && (in_array('compare', $this->showOptions)||in_array('wishlist', $this->showOptions))) || $this->sponsoredIcon || $this->featuredIcon): ?>
        <div class="sitestoreproduct_grid_view_list_btm">
          <div class="sitestoreproduct_grid_view_list_footer b_medium">
            <?php if(!empty($this->showOptions) && in_array('compare', $this->showOptions)): ?>
              <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity); ?>
            <?php endif; ?>
            <span class="fright">
              <?php if ($sitestoreproduct->sponsored == 1 && $this->sponsoredIcon): ?>
                <i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
              <?php endif; ?>
              <?php if ($sitestoreproduct->featured == 1 && $this->featuredIcon): ?>
                <i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
              <?php endif; ?>
              <?php if (!empty($this->showOptions) && in_array('wishlist', $this->showOptions)): ?> 
                <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>
              <?php endif; ?>
            </span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </li>

