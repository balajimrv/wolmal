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
 

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
  if ($this->ratingType == 'rating_editor') {$ratingType = 'editor';} elseif ($this->ratingType == 'rating_avg') {$ratingType = 'overall';} else { $ratingType = 'user';}
?>
<?php if ($this->enableLocation): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>
<?php endif; ?>
<?php if (!empty($this->is_ajax_load)): ?>
<?php if (empty($this->is_ajax)): ?>
  <?php

  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()
          ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
 $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
  ?>

  <div class="layout_core_container_tabs">
    <?php if ($this->tabCount > 1 || count($this->layouts_views)>1): ?>
      <div class="tabs_alt tabs_parent tabs_parent_sitestoreproduct_home">
        <ul id="main_tabs">
          <?php if ($this->tabCount > 1): ?>
            <?php foreach ($this->tabs as $key => $tab): ?>
              <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                <a  href='javascript:void(0);' >
                  <?php 
                    $word = ucwords(str_replace('_', ' ', $tab));
//                    if($word == 'Recent') { $word = 'New Arrival'; }
                    echo $this->translate($word); 
                  ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
          <?php
          if(COUNT($this->layouts_views) > 1):
          for ($i = count($this->layouts_views) - 1; $i >= 0; $i--):
            ?>
            <li class="seaocore_tab_select_wrapper fright" rel='<?php echo $this->layouts_views[$i] ?>'>
              <div class="seaocore_tab_select_view_tooltip">
                <?php 
                  $word = ucwords(str_replace('_', ' ', $this->layouts_views[$i]));
//                  if($word == 'Recent') { $word = 'New Arrival'; }
                  echo $this->translate($word); 
                ?>                
              </div>
              <span id="<?php echo $this->layouts_views[$i] . "_" . $this->identity ?>" class="seaocore_tab_icon tab_icon_<?php echo $this->layouts_views[$i] ?>" onclick="sitestoreproductTabSwitchview($(this));" ></span>

            </li>
          <?php endfor;
          endif;
          ?>
        </ul>
      </div>
    <?php endif; ?>
    <div id="dynamic_app_info_sr">
    <?php endif; ?>
    <?php if (in_array('list_view', $this->layouts_views)): ?> 
      <div class="sr_sitestoreproduct_container" id="list_view_sr" style="<?php echo $this->defaultLayout !== 'list_view' ? 'display: none;' : '' ?>">
        <ul class="sr_sitestoreproduct_browse_list sr_sitestoreproduct_list_view">
          <?php if($this->totalCount):?>
          
          <?php foreach ($this->paginator as $sitestoreproduct): ?>
          
          
           <?php if($this->listViewType=='list'):?>    
            <li class="b_medium sitestoreproduct_q_v_wrap">

              <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                <?php $product_id = $sitestoreproduct->product_id; ?>
                <?php $quickViewButton = true; ?>
                <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                
								<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
									<?php if($sitestoreproduct->featured):?>
										<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                  <?php endif;?>
                  <?php if($sitestoreproduct->newlabel):?>
                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
									<?php endif;?>
								<?php endif;?>
								
                <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))); ?>
                
								<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
									<?php if (!empty($sitestoreproduct->sponsored)): ?>
											<div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
												<?php echo $this->translate('SPONSORED'); ?>                 
											</div>
									<?php endif; ?>
								<?php endif; ?>
              </div>
              
              <div class='sr_sitestoreproduct_browse_list_info'>
              	<div class="sr_sitestoreproduct_browse_list_show_rating fright">  
	                <?php if ($ratingValue == 'rating_both'): ?>
	                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
	                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
	                                <?php else: ?>
	                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
	                                <?php endif; ?> 
	                              </div>
                <div class='sr_sitestoreproduct_browse_list_info_header'>
	               	<div class="sr_sitestoreproduct_list_title_small o_hidden">
                     <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationList), array('title' => $sitestoreproduct->getTitle())); ?>
                  </div>
                </div>
                <?php if( !empty($this->showCategory) ) : ?>
                  <?php if (empty($this->category_id)): ?>
                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                      <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> 
                        <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?>
                      </a>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
                
                <?php if(!empty($sitestoreproduct->location) && $this->enableLocation && !empty($this->showLocation)): ?>
									<?php  $locationId = Engine_Api::_()->getDbTable('locations', 'sitestoreproduct')->getLocationId($sitestoreproduct->product_id, $sitestoreproduct->location);?>
									<div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
									<?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitestoreproduct->product_id, 'resouce_type' => 'sitestoreproduct_product', 'location_id' => $locationId, 'flag' => 'map'), $this->translate($sitestoreproduct->location), array('class' => 'smoothbox')); ?>
                  </div>
                <?php endif; ?>

                <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                  <?php echo $this->timestamp($sitestoreproduct->creation_date); ?>
                  <?php if($this->postedby): ?>
                    <?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle()) ?>
                  <?php endif; ?>
                </div>
                
                
                <?php echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true, $this->priceWithTitle); ?>
                
                <?php if(!empty($this->statistics)): ?>
                  <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                    <?php 
                      $statistics = '';

                      if(in_array('commentCount', $this->statistics)) {
                        $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                      }

                      if(in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                     
                        $statistics .= $this->partial(
                        '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct)).', ';
                      }

                      if(in_array('viewCount', $this->statistics)) {
                        $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                      }

                      if(in_array('likeCount', $this->statistics)) {
                        $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                      }                 

                      $statistics = trim($statistics);
                      $statistics = rtrim($statistics, ',');

                    ?>

                    <?php echo $statistics; ?>
                  </div>
                <?php endif ?>
                                  
                <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden mtop5"> 
                  <div><?php echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity); ?></div>
                  <div><?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));?></div>
                  
                  <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) :?>
                  	<div class="sr_sitestoreproduct_browse_list_info_footer_icons">
											<?php if ($sitestoreproduct->sponsored == 1 ): ?>
												<i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
											<?php endif; ?>
											<?php if ($sitestoreproduct->featured == 1): ?>
												<i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
											<?php endif; ?>
										</div>
                  <?php endif;?>
                </div>
              </div>
            </li>
           <?php else: ?>
              <?php if(!empty($sitestoreproduct->sponsored)):?>
						<li class="list_sponsered b_medium sitestoreproduct_q_v_wrap">
					<?php else: ?>
						<li class="b_medium sitestoreproduct_q_v_wrap">
					<?php endif;?>
              <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
<!--              	<a class="sitestoreproduct_quick_view_btn" href="javascript:void(0);" onclick="quickView(<?php echo $sitestoreproduct->product_id ?>)">
                  <?php //echo $this->translate('Quick View'); ?>        
                </a>-->
                <?php $product_id = $sitestoreproduct->product_id; ?>
                <?php $quickViewButton = true; ?>
                <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
								<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
									<?php if($sitestoreproduct->featured):?>
										<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                  <?php endif;?>
									<?php if($sitestoreproduct->newlabel):?>
                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
									<?php endif;?>
								<?php endif;?>
              	<?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>
								<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
									<?php if (!empty($sitestoreproduct->sponsored)): ?>
											<div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
												<?php echo $this->translate('SPONSORED'); ?>                 
											</div>
									<?php endif; ?>
								<?php endif; ?>
              </div>
              
              <div class='sr_sitestoreproduct_browse_list_info'>
                
              <div class="sr_sitestoreproduct_browse_list_price_info">
                <?php if($sitestoreproduct->price > 0): ?>
                  <div class="sr_sitestoreproduct_price">
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price) ?>
                  </div>
                <?php else: ?>
                  <div class="sr_sitestoreproduct_browse_list_price_info_stats">
                    <?php echo $this->translate("No price available.")?>
                  </div>  
                <?php endif; ?>
              </div>  

                <div class="sr_sitestoreproduct_browse_list_rating">
                  <div class="sr_sitestoreproduct_browse_list_show_rating fright">  
	                  <?php if(!empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both'|| $ratingValue == 'rating_editor')): ?>
                  	<div class="clr">	
	                  	<div class="sr_sitestoreproduct_browse_list_rating_stats">
	                    	<?php echo $this->translate("Editor Rating");?>
	                    </div>
	                    <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
	                      <span class="sr_sitestoreproduct_browse_list_rating_stars">
	                          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
	                      </span>
	                    </div>
	                   </div> 
                   <?php endif; ?>
                  <?php if(!empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both'|| $ratingValue == 'rating_users')): ?>
                  	<div class="clr">
	                  	<div class="sr_sitestoreproduct_browse_list_rating_stats">
	                    <?php echo $this->translate("User Ratings");?><br />
	                      <?php  $totalUserReviews=($sitestoreproduct->rating_editor)? ($sitestoreproduct->review_count - 1):$sitestoreproduct->review_count ?>
	                      <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
	                    </div>
		                    <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
		                      <span class="sr_sitestoreproduct_browse_list_rating_stars">
	                           <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
	                       </span>
	                     </div>
	                   </div>  
                 <?php endif;?>

                  <?php if(!empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                  	<div class="clr">
	                  	<div class="sr_sitestoreproduct_browse_list_rating_stats">
	                    
	                      <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
	                    </div>
		                  <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
		                      <span class="sr_sitestoreproduct_browse_list_rating_stars">
	                           <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
	                       </span>
	                     </div>
	                  </div>  
                 <?php endif;?>
                </div>
                </div>
                
                <div class="sr_sitestoreproduct_browse_list_info">
                	<div class="sr_sitestoreproduct_browse_list_info_header">
	                  <div class="sr_sitestoreproduct_list_title_small">
	                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationList), array('title' => $sitestoreproduct->getTitle())); ?>
	                  </div>
	                </div>  

                  <div class='sr_sitestoreproduct_browse_list_info_blurb'>
                    <?php if($this->bottomLine): ?>
                      <?php echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000);?>
                    <?php else: ?>
                      <?php echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000);?>
                    <?php endif; ?>
                  </div>
                  
                  <?php if(!empty($this->statistics)): ?>
                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                      <?php 

                        $statistics = '';

                        if(in_array('commentCount', $this->statistics)) {
                          $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)).', ';
                        }

                        if(in_array('reviewCount', $this->statistics)) {
                          $statistics .= $this->partial(
                        '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct)).', ';
                        }

                        if(in_array('viewCount', $this->statistics)) {
                          $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)).', ';
                        }

                        if(in_array('likeCount', $this->statistics)) {
                          $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)).', ';
                        }                 

                        $statistics = trim($statistics);
                        $statistics = rtrim($statistics, ',');

                      ?>
                      <?php echo $statistics ?>
                    </div>   
                  <?php endif; ?>

                  <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                    <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?><?php if($this->postedby): ?> - <?php echo $this->translate('created by'); ?>
                    <?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle()) ?><?php endif; ?>
                  </div>
                  <?php if( !empty($this->showCategory) ) : ?>
                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                      <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> 
                        <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?>
                      </a>
                    </div>
                  <?php endif; ?>
                
                  
                  <div class="mtop10 sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                    <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct);?>
                    <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));?>
	                  <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) :?>  
										<span class="sr_sitestoreproduct_browse_list_info_footer_icons">
											<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $sitestoreproduct->closed ): ?>
												<img alt="close" src='<?php echo $this->layout()->staticBaseUrl?>application/modules/Sitestoreproduct/externals/images/close.png'/>
											<?php endif;?>
											<?php if ($sitestoreproduct->sponsored == 1): ?>
												<i class="sr_sitestoreproduct_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored');?>"></i>
											<?php endif; ?>
											<?php if ($sitestoreproduct->featured == 1): ?>
												<i class="sr_sitestoreproduct_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
											<?php endif; ?>
										</span>
										<?php endif;?>
                	</div>
             		</div>
          	</li>
           <?php endif; ?>
          <?php endforeach; ?>
            <?php else:?>
            <div class="tip">
              <span>
                <?php echo $this->translate('No products have been created yet.'); ?>
              </span>
            </div>
            <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>
              

    <?php if (in_array('grid_view', $this->layouts_views)): ?> 
      <?php $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct'); ?>
      <div class="sr_sitestoreproduct_container" id="grid_view_sr" style="<?php echo $this->defaultLayout !== 'grid_view' ? 'display: none;' : '' ?>">
        <ul class="sitestoreproduct_grid_view">
           <?php if($this->totalCount):?>
          <?php $isLarge = ($this->columnWidth>170); ?>
          <?php foreach ($this->paginator as $sitestoreproduct): ?>
          <?php $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id);
          $temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($sitestoreproduct->store_id);
          ?>

            <li class="sitestoreproduct_q_v_wrap g_b <?php if($isLarge): ?>largephoto<?php endif;?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
              <div>
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
								<?php if($sitestoreproduct->newlabel):?>
									<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
								<?php endif;?>
							<?php endif;?>
              <div class="sitestoreproduct_grid_view_thumb_wrapper">  
                
<!--                <a class="sitestoreproduct_quick_view_btn" href="javascript:void(0);" onclick="quickView(<?php echo $sitestoreproduct->product_id ?>)">
                  <?php //echo $this->translate('Quick View'); ?>        
                </a>-->
                <?php $product_id = $sitestoreproduct->product_id; ?>
                <?php $quickViewButton = true; ?>
                <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                <a href="<?php echo $sitestoreproduct->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
                   <?php
                  $url = $sitestoreproduct->getPhotoUrl($isLarge ?'thumb.midum' :'thumb.normal');
                  if (empty($url)):  $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
                  endif;
                  ?>
                  <span style="background-image: url(<?php echo $url; ?>); <?php if($isLarge): ?> height:160px; <?php endif;?> "></span>
                </a>
              </div>
              <div class="sitestoreproduct_grid_title">
                <?php if( !empty($this->priceWithTitle) && ((!empty($temp_allowed_selling) && $sitestoreproduct->allow_purchase) || !empty($temp_non_selling_product_price)) ) : ?>
                  <span class="fright sitestoreproduct_price_sale">
                  <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productTable->getProductDiscountedPrice($sitestoreproduct->product_id)); ?>
                  </span>
                <?php endif; ?>
                <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationGrid), array('title' => $sitestoreproduct->getTitle())) ?>
              </div>
              <?php if( !empty($this->showCategory) ) : ?>
                <div class="sitestoreproduct_grid_stats clr">
                  <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "". $this->categoryRouteName .""); ?>"> <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?> </a>
                </div>
              <?php endif; ?>
                  
              <?php if(!empty($sitestoreproduct->location) && $this->enableLocation && !empty($this->showLocation)): ?>
									<?php  $locationId = Engine_Api::_()->getDbTable('locations', 'sitestoreproduct')->getLocationId($sitestoreproduct->product_id, $sitestoreproduct->location);?>
									<div class="sitestoreproduct_grid_stats clr">
									<?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitestoreproduct->product_id, 'resouce_type' => 'sitestoreproduct_product', 'location_id' => $locationId, 'flag' => 'map'), $this->translate($sitestoreproduct->location), array('class' => 'smoothbox')); ?>
                  </div>
                <?php endif; ?>
              
             
                <?php if( empty($this->priceWithTitle) ) : ?>
                  <?php 
                // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock, false, $this->priceWithTitle); ?>
                <?php endif; ?>

                <!-- DISPLAY PRODUCTS -->
                
                <div class="sitestoreproduct_grid_rating">
                  <?php if ($ratingValue == 'rating_both'): ?>
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                  <?php else: ?>
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                  <?php endif; ?>
                  <span>
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->partial(
                    '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct'=>$sitestoreproduct))); ?>
                  </span>
                </div>
                <div class="sitestoreproduct_grid_view_list_btm">
                  <div class="sitestoreproduct_grid_view_list_footer b_medium">
                    <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                    <span class="fright">
                      <?php if ($sitestoreproduct->sponsored == 1 ): ?>
                        <i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
                      <?php endif; ?>
                      <?php if ($sitestoreproduct->featured == 1 ): ?>
                        <i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
                      <?php endif; ?>
                      <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => ''));?>

                    </span>
                  </div>
                </div>  
              </div>
            </li>
          <?php endforeach; ?>
           <?php else:?>
          <div class="tip">
							<span>

                <?php echo $this->translate('No products have been created yet.'); ?>

              </span>
          </div>
          <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>
    
   <?php //if (in_array('map_view', $this->layouts_views)): ?> 
       <?php if ($this->enableLocation): ?>
                <div class="sr_sitestoreproduct_container sitestoreproduct_map_view o_hiddden" id="map_view_sr" style="<?php echo $this->defaultLayout !== 'map_view' ? 'display: none;' : '' ?>">
                    <div class="seaocore_map clr" style="overflow:hidden;">
                        <div id="rmap_canvas_<?php echo $this->identity ?>" class="sitestoreproduct_list_map"> </div>
                        <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                        <?php if (!empty($siteTitle)) : ?>
                            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                        <?php endif; ?>
                    </div>	
                    <a  href="javascript:void(0);" onclick="srToggleBounce(<?php echo $this->identity ?>)" class="fleft sitestoreproduct_list_map_bounce_link" style="<?php echo $this->flagSponsored ? '' : 'display:none' ?>"> <?php echo $this->translate('Stop Bounce'); ?></a>
                </div>
            <?php endif; ?>
  <?php //endif; ?>

    <div class="seaocore_view_more mtop10">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => '',
          'class' => 'buttonlink icon_viewmore'
      ))
      ?>
    </div>
    <div class="seaocore_loading" id="" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <?php if (empty($this->is_ajax)): ?>

    </div>
  </div>
  <script type="text/javascript">   
//    window.addEvent('load', function() {
//      var request = new Request.JSON({
//        url : en4.core.baseUrl + 'sitestoreproduct/index/get-product-type',
//        data : {
//          format: 'json',
//          isAjax: 1,
//          type: 'layout_sitestoreproduct'
//        },
//        'onSuccess' : function(responseJSON) {
//          if( !responseJSON.getProductType ) {
//            document.getElement("." + responseJSON.getClassName + "recently_popular_random_sitestoreproduct").empty();
//          }
//        }
//      });
//      request.send();
//    });
    function sendAjaxRequestSR(params){
      var url = en4.core.baseUrl+'widget';
                                                                                                                                   
      if(params.requestUrl)
        url= params.requestUrl;

      var request = new Request.HTML({
        url : url,
        data : $merge(params.requestParams,{
          format : 'html',
          subject: en4.core.subject.guid,
          is_ajax:true
        }),
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          if(params.requestParams.page ==1){
            params.responseContainer.empty();
            Elements.from(responseHTML).inject(params.responseContainer);
            <?php if ($this->enableLocation): ?>
                                srInitializeMap(params.requestParams.content_id);
    <?php endif; ?>
          }else{
            var element= new Element('div', {      
              'html' : responseHTML  
            });
            params.responseContainer.getElements('.seaocore_loading').setStyle('display','none');

            <?php if (in_array('list_view', $this->layouts_views)): ?> 
              if($$('.sr_sitestoreproduct_list_view'))
                Elements.from(element.getElement('.sr_sitestoreproduct_list_view').innerHTML).inject(params.responseContainer.getElement('.sr_sitestoreproduct_list_view'));
            <?php endif; ?>

            <?php if (in_array('grid_view', $this->layouts_views)): ?>
              Elements.from(element.getElement('.sitestoreproduct_grid_view').innerHTML).inject(params.responseContainer.getElement('.sitestoreproduct_grid_view'));
            <?php endif; ?>
          }
          en4.core.runonce.trigger();
          Smoothbox.bind(params.responseContainer);                                      
        } 
      });
      en4.core.request.send(request);
    }
                                                                                                       
    en4.core.runonce.add(function(){
      <?php if (count($this->tabs) > 1): ?>
        $$('.tab_li_<?php echo $this->identity ?>').addEvent('click',function(event){
          if( en4.core.request.isRequestActive() ) return;
          var element = $(event.target);
          if( element.tagName.toLowerCase() == 'a' ) {
            element = element.getParent('li');
          }
          var type=element.get('rel');                     
          element.getParent('ul').getElements('li').removeClass("active")
          element.addClass("active");
          var params={
            requestParams :<?php echo json_encode($this->params) ?>,
            responseContainer :$('dynamic_app_info_sr')  
          }
          params.requestParams.content_type = type;
          params.requestParams.page=1;
          params.requestParams.content_id='<?php echo $this->identity ?>';
          params.responseContainer.empty();
          new Element('div', {      
            'class' : 'seaocore_content_loader'      
          }).inject(params.responseContainer);
          sendAjaxRequestSR(params);
        });
      <?php endif; ?>
    });
    
    <?php $latitude = $this->settings->getSetting('sitestoreproduct.map.latitude', 0); ?>
    <?php $longitude = $this->settings->getSetting('sitestoreproduct.map.longitude', 0); ?>
    <?php $defaultZoom = $this->settings->getSetting('sitestoreproduct.map.zoom', 1); ?>
      
    function sitestoreproductTabSwitchview(element){
      if( element.tagName.toLowerCase() == 'span' ) {
        element = element.getParent('li');
      }
      var type=element.get('rel');
      $('dynamic_app_info_sr').getElements('.sr_sitestoreproduct_container').setStyle('display','none');
      $(type+"_sr").style.display='block';                                                                              
    }
  </script>
  
  <?php if ($this->enableLocation): ?>
            <?php $latitude = $this->settings->getSetting('sitestoreproduct.map.latitude', 0); ?>
            <?php $longitude = $this->settings->getSetting('sitestoreproduct.map.longitude', 0); ?>
            <?php $defaultZoom = $this->settings->getSetting('sitestoreproduct.map.zoom', 1); ?>
            <script type="text/javascript">
                // var rgmarkers = [];

                function srInitializeMap(element_id) {
                    en4.sitestoreproduct.maps[element_id] = [];
                    en4.sitestoreproduct.maps[element_id]['markers'] = [];
                    // create the map
                    var myOptions = {
                        zoom: <?php echo $defaultZoom ?>,
                        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }

                    en4.sitestoreproduct.maps[element_id]['map'] = new google.maps.Map(document.getElementById("rmap_canvas_" + element_id), myOptions);

                    google.maps.event.addListener(en4.sitestoreproduct.maps[element_id]['map'], 'click', function() {
                        en4.sitestoreproduct.maps[element_id]['infowindow'].close();
                        google.maps.event.trigger(en4.sitestoreproduct.maps[element_id]['map'], 'resize');
                        en4.sitestoreproduct.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                    });
                    if ($("rmap_canvas_" + element_id)) {
                        if($("map_view_" + element_id)) {
                            $("map_view_" + element_id).addEvent('click', function() {
                                google.maps.event.trigger(en4.sitestoreproduct.maps[element_id]['map'], 'resize');
                                en4.sitestoreproduct.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                                en4.sitestoreproduct.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                            });
                        }
                        $$("li.tab_"+element_id).addEvent('click', function() {
                            google.maps.event.trigger(en4.sitestoreproduct.maps[element_id]['map'], 'resize');
                            en4.sitestoreproduct.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                            en4.sitestoreproduct.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
                        });
                    }

                    en4.sitestoreproduct.maps[element_id]['infowindow'] = new google.maps.InfoWindow(
                            {
                                size: new google.maps.Size(250, 50)
                            });

                }

                function setSRMarker(element_id, latlng, bounce, html, title_list) {
                    var contentString = html;
                    if (bounce == 0) {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: en4.sitestoreproduct.maps[element_id]['map'],
                            title: title_list,
                            animation: google.maps.Animation.DROP,
                            zIndex: Math.round(latlng.lat() * -100000) << 5
                        });
                    }
                    else {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: en4.sitestoreproduct.maps[element_id]['map'],
                            title: title_list,
                            draggable: false,
                            animation: google.maps.Animation.BOUNCE
                        });
                    }
                    en4.sitestoreproduct.maps[element_id]['markers'].push(marker);

                    google.maps.event.addListener(marker, 'click', function() {
                        en4.sitestoreproduct.maps[element_id]['infowindow'].setContent(contentString);
                        google.maps.event.trigger(en4.sitestoreproduct.maps[element_id]['map'], 'resize');

                        en4.sitestoreproduct.maps[element_id]['infowindow'].open(en4.sitestoreproduct.maps[element_id]['map'], marker);
                    });
                }
                function srToggleBounce(element_id) {
                    var markers = en4.sitestoreproduct.maps[element_id]['markers'];
                    for (var i = 0; i < markers.length; i++) {
                        if (markers[i].getAnimation() != null) {
                            markers[i].setAnimation(null);
                        }
                    }
                }
                en4.core.runonce.add(function() {
                    srInitializeMap("<?php echo $this->identity ?>");
                });
            </script>
        <?php endif; ?>
<?php endif; ?>
    
<script type="text/javascript">
  en4.core.runonce.add(function(){
    var view_more_content=$('dynamic_app_info_sr').getElements('.seaocore_view_more');
    view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');

    view_more_content.removeEvents('click');
    view_more_content.addEvent('click',function(){
      if( en4.core.request.isRequestActive() ) return;
      var params={
        requestParams :<?php echo json_encode($this->params) ?>,
        responseContainer :$('dynamic_app_info_sr')  
      }
      params.requestParams.content_type = "<?php echo $this->content_type ?>";
      params.requestParams.page=<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
      params.requestParams.content_id='<?php echo $this->identity ?>';
      view_more_content.setStyle('display','none');
      params.responseContainer.getElements('.seaocore_loading').setStyle('display','');
      
      sendAjaxRequestSR(params);
    });
    
    <?php if (!empty($this->enableLocation) && is_array($this->enableLocation)): ?>
                <?php foreach ($this->locations as $location) : ?>
                    var point = new google.maps.LatLng(<?php echo $location->latitude ?>,<?php echo $location->longitude ?>);
                    var contentString = '<div id="content">'+
       '<div id="siteNotice">'+
       '</div>'+'  <ul class="sitestores_locationdetails"><li>'+

       '<div class="sitestores_locationdetails_info_title">'+
       
       '<?php echo $this->htmlLink($this->locationsProduct[$location->product_id]->getHref(), $this->locationsProduct[$location->product_id]->getTitle()); ?>' + 
              '<div class="fright">'+
       '<span >'+
              <?php if ($this->locationsProduct[$location->product_id]->featured == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
                  '</span>'+
                    '<span>'+
              <?php if ($this->locationsProduct[$location->product_id]->sponsored == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
              <?php endif; ?>
		        '</span>'+
		      '</div>'+
	      '<div class="clr"></div>'+
	      '</div>'+

       '<div class="sitestores_locationdetails_photo" >'+
       '<?php echo $this->htmlLink($this->locationsProduct[$location->product_id]->getHref(), $this->itemPhoto($this->locationsProduct[$location->product_id], 'thumb.normal', '', array('align' => 'center'))); ?>'+
       '</div>'+
       '<div class="sitestores_locationdetails_info">'+

				<?php if (in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
					<?php if (($this->locationsProduct[$location->product_id]->rating > 0)): ?>
							'<span class="clr">'+
							<?php for ($x = 1; $x <= $this->locationsProduct[$location->product_id]->rating; $x++): ?>
									'<span class="rating_star_generic rating_star"></span>'+
							<?php endfor; ?>
							<?php if ((round($this->locationsProduct[$location->product_id]->rating) - $this->locationsProduct[$location->product_id]->rating) > 0): ?>
									'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
									'</span>'+
					<?php endif; ?>
				<?php endif; ?>

               <?php if (!empty($this->statistics)) : ?>
							'<div class="sitestores_locationdetails_info_date">'+
							<?php 
                $statistics = '';
                
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->locationsProduct[$location->product_id]->like_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->like_count))).', ';
                }

                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s follower', '%s followers', $this->locationsProduct[$location->product_id]->follow_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->follow_count))).', ';
                }

                if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
									if ($this->locationsProduct[$location->product_id]->member_title && $memberTitle) {
										if ($this->locationsProduct[$location->product_id]->member_count == 1) : 
											$statistics .=  $this->locationsProduct[$location->product_id]->member_count . ' member'.', ';
										else:  
											$statistics .=  $this->locationsProduct[$location->product_id]->member_count . ' ' .  $this->locationsProduct[$location->product_id]->member_title.', ';
										endif; 
									} else {
										$statistics .= $this->string()->escapeJavascript($this->translate(array('%s member', '%s members', $this->locationsProduct[$location->product_id]->member_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->member_count))).', ';
									}
                }
                
                if(in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->locationsProduct[$location->product_id]->review_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->review_count))).', ';
                }
                
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->locationsProduct[$location->product_id]->comment_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->comment_count))).', ';
                }


                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->locationsProduct[$location->product_id]->view_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->view_count))).', ';
                }


                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              '<?php echo $statistics; ?>'+
							'</div>'+
						<?php endif; ?>      
							'<div class="sitestores_locationdetails_info_date">'+
								"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?>"+
							'</div>'+
              '</div>'+
              '<div class="clr"></div>'+
                '</li> </ul>' +
               '</div>';

                    setSRMarker(<?php echo $this->identity ?>, point,<?php echo!empty($this->flagSponsored) ? $this->locationsProduct[$location->product_id]->sponsored : 0 ?>, contentString, "<?php echo $this->string()->escapeJavascript($this->locationsProduct[$location->product_id]->getTitle()) ?>");
                <?php endforeach; ?>
            <?php endif; ?>
  });
  
  function quickView(product_id)
  {
    Smoothbox.open("<div id='productQuickViewContainer'><img src='"+en4.core.staticBaseUrl+"'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15></div>");

    en4.core.request.send(new Request.HTML({
      url : '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'quick-view'), 'default', true) ?>/product_id/'+product_id,
      method : 'POST',
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) 
      {
        Smoothbox.close();
        Smoothbox.open("<div id='productQuickViewContainer'>"+responseHTML+"</div>");
      }
    })
    );
  }
</script>
<?php else :?>

        <div id="layout_sitestoreproduct_recently_popular_random_sitestoreproduct_<?php echo $this->identity; ?>">
            <!--    <div class="seaocore_content_loader"></div>-->
        </div>

        <?php if ($this->detactLocation): ?>
            <script type="text/javascript">
                var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
                var params = {
                    'detactLocation': <?php echo $this->detactLocation; ?>,
                    'responseContainer': 'layout_sitestoreproduct_recently_popular_random_sitestoreproduct_<?php echo $this->identity; ?>',
                    requestParams: requestParams
                };

                en4.seaocore.locationBased.startReq(params);
            </script>  
        <?php else: ?>
            <script type="text/javascript">
                window.addEvent('domready', function () {
                    en4.sitestoreproduct.ajaxTab.sendReq({
                        loading: true,
                        requestParams: $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                        responseContainer: [$('layout_sitestoreproduct_recently_popular_random_sitestoreproduct_<?php echo $this->identity; ?>')]
                    });
                });
            </script>  
        <?php endif; ?>


    <?php endif; ?>
