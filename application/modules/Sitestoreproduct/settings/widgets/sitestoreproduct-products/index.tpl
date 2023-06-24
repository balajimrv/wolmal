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
  
<?php 
 if ($this->viewType=='gridview'): 
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
 endif;?>

<?php
  $ratingValue = $this->ratingType; 
  $ratingShow = 'small-star';
  if ($this->ratingType == 'rating_editor') :
    $ratingType = 'editor';
  elseif ($this->ratingType == 'rating_avg')  : 
    $ratingType = 'overall';
  else:
    $ratingType = 'user';
  endif;
?>

<?php if ($this->viewType=='listview'): ?>
<ul class="seaocore_sidebar_list">
<?php foreach( $this->sitestoreproduct_products as $product ):?>
<li class="sitestoreproduct_q_v_wrap">
    <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.icon')) ?>
    <?php $product_id = $product->product_id; ?>
    <?php $quickViewButton = false; ?>
    <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
    <div class="seaocore_sidebar_list_info" >
      <div class="seaocore_sidebar_list_title" >
        <?php echo $this->htmlLink($product->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($product->getTitle(), $this->truncation), array('title' => $product->getTitle())) ?>
      </div>
      <?php if(empty($this->category_id)):?>
          <div class='seaocore_sidebar_list_details'>
            <a href="<?php echo $this->url(array('category_id' => $product->category_id, 'categoryname' => $product->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> 
            <?php echo $this->translate($product->getCategory()->getTitle(true))?>
            </a>
          </div>
      <?php endif; ?>
<!--      <div class="seaocore_sidebar_list_details">
        <?php
//        if( $this->popularity == 'top_selling' ):
//          echo $this->translate('Sold Quantity : %s', $product->quantity);
//        elseif( $this->popularity == 'last_order_all' || $this->popularity == 'last_order_viewer' ):
//          echo $product->quantity . ' x ' .$this->currencySymbol.number_format($product->price, 2);
//        endif; 
        ?>
      </div>-->
      
      <?php 
      // CALLING HELPER FOR GETTING PRICE INFORMATIONS
      echo $this->getProductInfo($product, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock); ?>
      
      <span class='featured_slidshow_info'>
        <?php if(!empty($this->statistics)): ?>  
                <p>
                  <?php 
                    $statistics = '';

                    if(in_array('commentCount', $this->statistics)) :
                      $statistics .= $this->translate(array('%s comment', '%s comments', $product->comment_count), $this->locale()->toNumber($product->comment_count)).', ';
                    endif;
                    
                    if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) :
                      $statistics .= $this->translate(array('%s review', '%s reviews', $product->review_count), $this->locale()->toNumber($product->review_count)).', ';
                    endif;
                    
                    if(in_array('followCount', $this->statistics)) :
                      $statistics .= $this->translate(array('%s follow', '%s follows', $product->follow_count), $this->locale()->toNumber($product->follow_count)).', ';
                    endif;

                    if(in_array('viewCount', $this->statistics)) :
                      $statistics .= $this->translate(array('%s view', '%s views', $product->view_count), $this->locale()->toNumber($product->view_count)).', ';
                    endif;

                    if(in_array('likeCount', $this->statistics)) :
                      $statistics .= $this->translate(array('%s like', '%s likes', $product->like_count), $this->locale()->toNumber($product->like_count)).', ';
                    endif;

                    $statistics = trim($statistics);
                    $statistics = rtrim($statistics, ',');
                  ?>
                  <?php echo $statistics; ?>
                </p>
              <?php endif; ?>
      </span>
      
      <?php if($ratingValue == 'rating_both'): ?>
            <?php echo $this->showRatingStarSitestoreproduct($product->rating_editor, 'editor', $ratingShow); ?>
            <br/>
            <?php echo $this->showRatingStarSitestoreproduct($product->rating_users, 'user', $ratingShow); ?>
          <?php else: ?>
            <?php echo $this->showRatingStarSitestoreproduct($product->$ratingValue, $ratingType, $ratingShow); ?>
          <?php endif; ?>
            
            <div class="clr mtop5">
            <?php echo $this->compareButtonSitestoreproduct($product, $this->identity); ?>   
            
            <span class="fright">
              <?php echo $this->addToWishlistSitestoreproduct($product, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>
            </span>
 
          </div>
    </div>
  </li>
<?php endforeach; ?>
</ul>
<?php else: ?>
   <?php $isLarge = ($this->columnWidth>170); ?>
   <ul  class="sitestoreproduct_grid_view sitestoreproduct_sidebar_grid_view mtop10"> 
    <?php foreach ($this->sitestoreproduct_products as $product): ?>
     <li class="sitestoreproduct_q_v_wrap g_b <?php if($isLarge): ?>largephoto<?php endif;?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
      <div>
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
					<?php if($product->newlabel):?>
						<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
					<?php endif;?>
				<?php endif;?>
            <div class="sitestoreproduct_grid_view_thumb_wrapper">
            <?php $product_id = $product->product_id; ?>
            <?php $quickViewButton = true; ?>
            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
        <a href="<?php echo $product->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
          <?php 
          $url = $product->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
          if (empty($url)): 
            $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
          endif;
          ?>
          <span style="background-image: url(<?php echo $url; ?>); <?php if($isLarge): ?> height:160px; <?php endif;?> "></span>
        </a>
              </div>  
        <div class="sitestoreproduct_grid_title">
          <?php echo $this->htmlLink($product->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($product->getTitle(), $this->truncation), array('title' => $product->getTitle())) ?>
        </div>
        <div class="sitestoreproduct_grid_stats clr">
          <a href="<?php echo $product->getCategory()->getHref() ?>"> 
            <?php echo $this->translate($product->getCategory()->getTitle(true)) ?>
          </a>
        </div>
        
        <?php // if( $this->popularity == 'top_selling' ):
//                echo $this->translate('Sold Quantity : %s', $product->quantity);
//              elseif( $this->popularity == 'last_order_all' || $this->popularity == 'last_order_viewer' ):
//                echo $product->quantity . ' x ' .$this->currencySymbol.number_format($product->price, 2);
//              endif; 
        ?>
            
        <?php 
      // CALLING HELPER FOR GETTING PRICE INFORMATIONS
      echo $this->getProductInfo($product, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock); ?>
            
        <?php if(!empty($this->statistics)): ?>  
          <div class="sitestoreproduct_grid_stats clr seaocore_txt_light">
           <?php  
              $statistics = '';
              if(in_array('commentCount', $this->statistics)) {
                $statistics .= $this->translate(array('%s comment', '%s comments', $product->comment_count), $this->locale()->toNumber($product->comment_count)).', ';
              }
              
              if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                $statistics .= $this->translate(array('%s review', '%s reviews', $product->review_count), $this->locale()->toNumber($product->review_count)).', ';
              }
                    
              if(in_array('followCount', $this->statistics)) {
                $statistics .= $this->translate(array('%s follow', '%s follows', $product->follow_count), $this->locale()->toNumber($product->follow_count)).', ';
              }
                    
              if(in_array('viewCount', $this->statistics)) {
                $statistics .= $this->translate(array('%s view', '%s views', $product->view_count), $this->locale()->toNumber($product->view_count)).', ';
              }

              if(in_array('likeCount', $this->statistics)) {
                $statistics .= $this->translate(array('%s like', '%s likes', $product->like_count), $this->locale()->toNumber($product->like_count)).', ';
              }                 

              $statistics = trim($statistics);
              $statistics = rtrim($statistics, ',');

            ?>
            <?php echo $statistics; ?> 
          </div>
        <?php endif; ?>
         
        <div class="sitestoreproduct_grid_rating">
            <?php if($ratingValue == 'rating_both'): ?>
            <?php echo $this->showRatingStarSitestoreproduct($product->rating_editor, 'editor', $ratingShow); ?>
            <?php echo $this->showRatingStarSitestoreproduct($product->rating_users, 'user', $ratingShow); ?>
          <?php else: ?>
            <?php echo $this->showRatingStarSitestoreproduct($product->$ratingValue, $ratingType, $ratingShow); ?>
          <?php endif; ?>
            
          <?php if(!empty($this->statistics) && in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)):  ?>
          <span>
            <?php echo $this->htmlLink($product->getHref(), $this->partial(
                            '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $product))); ?>
          </span>
          <?php endif; ?>
        </div>
        <div class="sitestoreproduct_grid_view_list_btm">
          <div class="sitestoreproduct_grid_view_list_footer b_medium">
            <?php echo $this->compareButtonSitestoreproduct($product); ?>
            <span class="fright">
              <?php //if ($sitestoreproduct->sponsored == 1): ?>
  <!--              <i title="<?php //echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>-->
              <?php //endif; ?>
              <?php //if ($sitestoreproduct->featured == 1): ?>
  <!--              <i title="<?php //echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>-->
              <?php //endif; ?>
            <?php echo $this->addToWishlistSitestoreproduct($product, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>  
            </span>
          </div>
        </div>
      </div>
    </li>
     <?php endforeach; ?>
  </ul>
<?php endif; ?>
