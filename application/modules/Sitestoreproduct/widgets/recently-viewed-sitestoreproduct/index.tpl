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
  $ratingValue = $this->ratingType; 
  $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {$ratingType = 'editor';} elseif ($this->ratingType == 'rating_avg') {$ratingType = 'overall';} else { $ratingType = 'user';}
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>

<?php if ($this->viewType=='listview'): ?>
  <ul class="seaocore_sidebar_list">
    <?php foreach($this->products as $sitestoreproduct):?>
      <?php $viewer = Engine_Api::_()->getItem('user', $sitestoreproduct->viewer_id);?>
        <?php if(count($viewer->toArray()) != 0) :?>
      <li class="sitestoreproduct_q_v_wrap"> 
        <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.icon')) ?>
        <?php $product_id = $sitestoreproduct->product_id; ?>
        <?php $quickViewButton = false; ?>
        <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
        <div class='seaocore_sidebar_list_info'>
          <div class='seaocore_sidebar_list_title'>
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())) ?>
          </div>
        <?php if(!empty($sitestoreproduct->viewer_id)) : ?>
                <?php $viewer = Engine_Api::_()->getItem('user', $sitestoreproduct->viewer_id);?>
              <div class='seaocore_sidebar_list_title'>
                <?php echo $this->translate("Last viewed by:")  . ' ' . $this->htmlLink($viewer->getHref(), $viewer->getTitle()) ?>
              </div>
        <?php endif; ?>
          <?php if(empty($this->category_id)):?>
          <div class='seaocore_sidebar_list_details'>
            <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> 
            <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true))?>
            </a>
          </div>
          <?php endif; ?>

      <?php 
      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock); ?>
          
      <?php if(!empty($this->statistics)): ?>
            <div class='seaocore_sidebar_list_details'>
              <?php 

                $statistics = '';
                if(is_array($this->statistics) && in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                }

                if(is_array($this->statistics) && in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                  $statistics .= $this->partial(
                  '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct)).', ';
                }

                if(is_array($this->statistics) && in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                }

                if(is_array($this->statistics) && in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                }                 

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');

              ?>

              <?php echo $statistics; ?>
            </div>
          <?php endif ?>
          <?php if(is_array($this->statistics) && in_array('viewRating', $this->statistics)) : ?>
          <?php if($ratingValue == 'rating_both'): ?>
            <div class="clr">
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
              <br/>
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
            </div>
          <?php else: ?>
            <div class="clr">
              <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
            </div>  
          <?php endif; ?>
          <?php endif; ?>
          <div class="clr mtop5">
            <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity); ?>   
            
            <span class="fright">
              <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>
            </span>
 
          </div>
        </div>
      </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <?php $isLarge = ($this->columnWidth>170); ?>
    <ul class="sitestoreproduct_grid_view sitestoreproduct_sidebar_grid_view mtop10">
      <?php foreach ($this->products as $sitestoreproduct): ?>
        <li class="sitestoreproduct_q_v_wrap g_b <?php if($isLarge): ?>largephoto<?php endif;?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
      		<div>
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
							<?php if($sitestoreproduct->newlabel):?>
								<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
							<?php endif;?>
						<?php endif;?>
            <div class="sitestoreproduct_grid_view_thumb_wrapper"> 
              <?php $product_id = $sitestoreproduct->product_id; ?>
              <?php $quickViewButton = true; ?>
              <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
              <a href="<?php echo $sitestoreproduct->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
                <?php
                $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
                endif;
                ?>
                <span style="background-image: url(<?php echo $url; ?>); <?php if($isLarge): ?> height:160px; <?php endif;?> "></span>
              </a>
            </div>
            
            <div class="sitestoreproduct_grid_title">
              <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())) ?>
            </div>
             <?php if(!empty($sitestoreproduct->viewer_id)) : ?>
                    <?php $viewer = Engine_Api::_()->getItem('user', $sitestoreproduct->viewer_id);?>
                    <div class='sitestoreproduct_grid_stats clr'>
                      <?php echo $this->translate("Last viewed by:")  . ' ' . $this->htmlLink($viewer->getHref(), $viewer->getTitle()) ?>
                    </div>
            <?php endif; ?>                                   
            <div class="sitestoreproduct_grid_stats clr">
              <a href="<?php echo $sitestoreproduct->getCategory()->getHref() ?>"> 
                <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?>
              </a>
            </div>
            
                <?php 
                // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock); ?>
                
                <div class="sitestoreproduct_grid_stats clr seaocore_txt_light">
             <?php 
                $statistics = '';
                if($this->statistics):
                if(is_array($this->statistics) && in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                }
                if(is_array($this->statistics) && in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                }

                if(is_array($this->statistics) && in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                }                 

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
                endif;
              ?>
              <?php echo $statistics; ?> 
              </div>
               <?php if(is_array($this->statistics) && in_array('viewRating', $this->statistics)) : ?>
               <div class="sitestoreproduct_grid_rating">  
                <?php if(is_array($this->statistics) && $this->statistics && in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)):  ?>
                <?php endif; ?>
                <?php if ($ratingValue == 'rating_both'): ?>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                <?php else: ?>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                <?php endif; ?>
                <span>
                  <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->partial(
                                  '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct))); ?>
                </span>
              </div>
              <?php endif; ?>

              <div class="sitestoreproduct_grid_view_list_btm">
          			<div class="sitestoreproduct_grid_view_list_footer b_medium">
                <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                <span class="fright">
                  <?php if ($sitestoreproduct->sponsored == 1): ?>
                    <i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
                  <?php endif; ?>
                  <?php if ($sitestoreproduct->featured == 1): ?>
                    <i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
                  <?php endif; ?>
                  
                  <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>  
                  
                </span>
              </div>
           </div>
       	</div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>