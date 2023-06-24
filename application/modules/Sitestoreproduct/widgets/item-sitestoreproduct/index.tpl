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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js') ?>

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

<ul class="sitestoreproduct_grid_view sitestoreproduct_sidebar_grid_view mtop10">
  <li class="g_b sitestoreproduct_q_v_wrap">
  	<div>
      <?php if($this->sitestoreproduct->newlabel):?>
        <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
      <?php endif;?>
    
      <div class="sitestoreproduct_grid_view_thumb_wrapper"> 
        <?php $product_id = $this->sitestoreproduct->product_id; ?>
        <?php $quickViewButton = true; ?>
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
        <a href="<?php echo $this->sitestoreproduct->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
          <?php $url = $this->sitestoreproduct->getPhotoUrl('thumb.normal'); ?>
          <span style="background-image: url(<?php echo $url; ?>); height:160px; "></span>
        </a>
      </div>
      
      <div class="sitestoreproduct_grid_title">
      <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($this->sitestoreproduct->getTitle(), 25), array('title' => $this->sitestoreproduct->getTitle())) ?>
        </div>
      
      <?php //echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.profile')) ?>
      
      <div class='seaocore_browse_list_info_date'>
        <?php echo $this->htmlLink($this->sitestoreproduct->getCategory()->getHref(), $this->sitestoreproduct->getCategory()->getTitle(true), array()) ?>
      </div>
     
      <!-- DISPLAY PRODUCTS -->
      <?php 
      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
      echo $this->getProductInfo($this->sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock); ?>
        
      <div class="sitestoreproduct_grid_rating">
        <?php if ($ratingValue == 'rating_both'): ?>
          <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
          <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_users, 'user', $ratingShow); ?>
        <?php else: ?>
          <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
        <?php endif; ?>
          
        <span>
          <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->partial(
                          '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $this->sitestoreproduct))); ?>
        </span>
      </div>
        
      <div class="sitestoreproduct_grid_view_list_btm">
        <div class="sitestoreproduct_grid_view_list_footer b_medium">
        <?php echo $this->compareButtonSitestoreproduct($this->sitestoreproduct, $this->identity); ?>
        <span class="fright">
          <?php if ($this->sitestoreproduct->sponsored == 1): ?>
            <i title="<?php echo $this->translate('Sponsored'); ?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
          <?php endif; ?>
          <?php if ($this->sitestoreproduct->featured == 1): ?>
             <i title="<?php echo $this->translate('Featured'); ?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
          <?php endif; ?>
          <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>
        </span>
      </div>
      </div>
    </div>
  </li>
</ul>