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
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>

<script type="text/javascript">
  var isWishlistListView;
  en4.core.runonce.add(function() {

<?php if ($this->viewType == 'listview') : ?>
      $("wishlist_list_view").style.display = 'block';
      isWishlistListView = 1;
<?php elseif ($this->viewType == 'gridview') : ?>
      $("wishlist_grid_view").style.display = 'block';
      isWishlistListView = 0;
<?php endif; ?>

    var anchor_2 = $('wishlist_sitestoreproduct_search').getParent();
    if (document.getElementById('wishlist_store_table_rate_previous')) {
      document.getElementById('wishlist_store_table_rate_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
      $('wishlist_store_table_rate_previous').removeEvents('click').addEvent('click', function() {
        $('tablerate_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/wishlist-member-profile-items',
          data: {
            format: 'html',
            subject: en4.core.subject.guid,
            isListView: isWishlistListView,
            add_to_cart: '<?php echo $this->showAddToCart ?>',
            in_stock: '<?php echo $this->showinStock ?>',
            ratingType: '<?php echo $this->ratingType ?>',
            truncation: '<?php echo $this->title_truncation ?>',
            viewType: '<?php echo $this->viewType ?>',
            columnWidth: '<?php echo $this->columnWidth ?>',
            columnHeight: '<?php echo $this->columnHeight ?>',
            statistics: '<?php echo $this->statistics ?>',
            itemCount: '<?php echo $this->itemCount ?>',
            titleCount: '<?php echo $this->titleCount ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('tablerate_spinner_prev').innerHTML = '';
          }
        }), {
          'element': anchor_2
        })
      });
    }
    if ($('wishlist_store_table_rate_next')) {
      $('wishlist_store_table_rate_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
      $('wishlist_store_table_rate_next').removeEvents('click').addEvent('click', function() {
        $('wishlist_tablerate_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/wishlist-member-profile-items',
          data: {
            format: 'html',
            subject: en4.core.subject.guid,
            isListView: isWishlistListView,
            add_to_cart: '<?php echo $this->showAddToCart ?>',
            in_stock: '<?php echo $this->showinStock ?>',
            ratingType: '<?php echo $this->ratingType ?>',
            truncation: '<?php echo $this->title_truncation ?>',
            viewType: '<?php echo $this->viewType ?>',
            columnWidth: '<?php echo $this->columnWidth ?>',
            columnHeight: '<?php echo $this->columnHeight ?>',
            statistics: '<?php echo $this->statistics ?>',
            itemCount: '<?php echo $this->itemCount ?>',
            titleCount: '<?php echo $this->titleCount ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('wishlist_tablerate_spinner_next').innerHTML = '';
          }
        }), {
          'element': anchor_2
        })
      });
    }
  });

  function switchview1(flage)
  {
    if (flage)
    {
      isWishlistListView = 0;
      $('wishlist_grid_view').style.display = 'block';
      $('wishlist_list_view').style.display = 'none';
    }
    else
    {
      isWishlistListView = 1;
      $('wishlist_list_view').style.display = 'block';
      $('wishlist_grid_view').style.display = 'none';
    }
    // tempCurrentViewType = flage;
  }
  
  
    function getWishlistProducts()
    {
      if($('wishlist_sitestoreproduct_search')) {
        $('wishlist_sitestoreproduct_search').innerHTML = '<div class="seaocore_content_loader"></div>';
      }
      en4.core.request.send(new Request.HTML({
        'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/wishlist-member-profile-items',
        'data' : {
            format: 'html',
            subject: en4.core.subject.guid,
            viewSearch: 1,            
            productSearchValue: $('sitestoreproduct_products_search_input_text').value,
            wishlistId: $('wishlist_sitestoreproduct_products_search_input_selectbox').value,
            isListView: isWishlistListView,
            add_to_cart: '<?php echo $this->showAddToCart ?>',
            in_stock: '<?php echo $this->showinStock ?>',
            ratingType: '<?php echo $this->ratingType ?>',
            truncation: '<?php echo $this->title_truncation ?>',
            viewType: '<?php echo $this->viewType ?>',
            columnWidth: '<?php echo $this->columnWidth ?>',
            columnHeight: '<?php echo $this->columnHeight ?>',
            statistics: '<?php echo $this->statistics ?>',
            itemCount: '<?php echo $this->itemCount ?>',
            titleCount: '<?php echo $this->titleCount ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){           
          $('wishlist_sitestoreproduct_search').innerHTML = responseHTML;
        }
      }), {
        
      });
    }
</script>

<?php if( empty($this->viewSearch) ): ?>
<div id="wishlist_sitestoreproduct_header" class="sitestoreproduct_product_list_filters">

  <?php if (empty($this->isajax) && $this->paginator->count() > 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox))): ?>

    <div class="sitestoreproduct_product_list_filters_field">
      <?php echo $this->translate("Search: "); ?>
      <input id="sitestoreproduct_products_search_input_text" type="text" value="<?php echo $this->search; ?>" onchange="getWishlistProducts()"/>
    </div>
    <?php if( !empty($this->wishlistObjArray) ): ?>
    <div class="sitestoreproduct_product_list_filters_field">
      <?php echo $this->translate('Wishlists:'); ?>	
      <select name="default_visibility" id="wishlist_sitestoreproduct_products_search_input_selectbox" onchange = "getWishlistProducts();">
        <option value="0"><?php echo ""; ?></option>
        <?php foreach ($this->wishlistObjArray as $wishlist): ?>
          <!--selected='selected'-->
          <option value="<?php echo $wishlist->wishlist_id ?>"><?php echo $wishlist->title; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; endif; ?>      
</div>
<?php endif; ?>


<div id="wishlist_sitestoreproduct_search">


  <?php
  $getPaginatorCount = $this->paginator->getTotalItemCount();
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
  
  <div class="sr_sitestoreproduct_browse_lists_view_options b_medium" id="sr_sitestoreproduct_browse_lists_view_options_b_medium">
    <div class="fleft"> 
      <?php echo $this->translate(array('%s product found.', '%s product found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
    </div>

    <?php if( !empty($getPaginatorCount) ): ?>
      <span class="seaocore_tab_select_wrapper fright">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
        <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview1(1);" ></span>
      </span>
      <span class="seaocore_tab_select_wrapper fright">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
        <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview1(0);" ></span>
      </span>
    <?php endif; ?>
  </div>
<?php  
  if( empty($getPaginatorCount) ):
    ?>
  <div class="tip mtop10">
    <span>
      <?php echo $this->translate('No products found in the wishlist.'); ?>
    </span>
  </div>
  <?php
  else:
  ?>
  <div id="wishlist_list_view" style="display: none;">
    <ul id="profile_sitestoreproducts" class="sr_sitestoreproduct_browse_list">
      <?php foreach ($this->paginator as $sitestoreproduct): ?>

        <li class="b_medium">
          <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
              <?php if ($sitestoreproduct->featured): ?>
               <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
              <?php endif; ?>
              <?php if ($sitestoreproduct->newlabel): ?>
                <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
              <?php endif; ?>
            <?php endif; ?>
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
              <?php if (!empty($sitestoreproduct->sponsored)): ?>
                <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <div class='sr_sitestoreproduct_browse_list_info'>
            <?php if(in_array('reviewCount', $this->statistics)): ?>
              <div class="sr_sitestoreproduct_browse_list_show_rating fright">
                <?php if ($ratingValue == 'rating_both'): ?>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                  <br/>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                <?php else: ?>
                  <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class='sr_sitestoreproduct_browse_list_info_header'>
              <div class="sr_sitestoreproduct_list_title_small o_hidden">
                <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())) ?>
              </div>
            </div>
            
            
            <?php if (!empty($this->wishlistObjArray) && !empty($sitestoreproduct->wishlist_id)): ?>
            <div class="sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light">
                <?php
                $getWishlistObj = $this->wishlistObjArray[$sitestoreproduct->wishlist_id];
                if( $this->viewer_id == $getWishlistObj->owner_id )
                    echo 'Wishlist: ' . $this->htmlLink($getWishlistObj->getHref(), $getWishlistObj->getTitle());
                else
                    echo 'Wishlist: ' . $getWishlistObj->getTitle();
                ?>
              </div>  
            <?php endif; ?>  

            <div class="sitestoreproduct_grid_stats clr">
              <a href="<?php echo $sitestoreproduct->getCategory()->getHref() ?>"> 
                <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?>
              </a>
            </div>

            <?php
            // CALLING HELPER FOR GETTING PRICE INFORMATIONS
            echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true);
            ?>

            <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
              <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?>,

              <?php if (!empty($this->statistics)): ?>
                <?php
                $statistics = '';

                if (in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)) . ', ';
                }

                if (in_array('reviewCount', $this->statistics)) {
                  $statistics .= $this->partial(
                                  '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct)) . ', ';
                }

                if (in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)) . ', ';
                }

                if (in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . ', ';
                }

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
                ?>

                <?php echo $statistics; ?>

              <?php endif ?>
            </div>      

            <div class='sr_sitestoreproduct_browse_list_info_blurb'>
              <?php echo substr(strip_tags($sitestoreproduct->body), 0, 350);
              if (strlen($sitestoreproduct->body) > 349)
                echo $this->translate("...");
              ?>
            </div>
            <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden mtop5">
              <div class="sr_sitestoreproduct_browse_list_info_footer_icons"> 
                <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) : ?>
                  <?php if ($sitestoreproduct->sponsored == 1): ?>
                    <i title="<?php echo $this->translate('Sponsored'); ?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
                  <?php endif; ?>
                  <?php if ($sitestoreproduct->featured == 1): ?>
                    <i title="<?php echo $this->translate('Featured'); ?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $sitestoreproduct->closed): ?>
                  <i class="sr_sitestoreproduct_icon icon_sitestoreproducts_close" title="<?php echo $this->translate('Closed'); ?>"></i>
  <?php endif; ?>
              </div>
            </div>
          </div>
        </li>
  <?php endforeach; ?>
    </ul>
  </div>
    <?php //elseif($this->viewType == 'gridview'):  ?>
  <div id="wishlist_grid_view" style="display: none">
      <?php $isLarge = ($this->columnWidth > 170); ?>
    <ul  class="sitestoreproduct_grid_view sitestoreproduct_sidebar_grid_view mtop10"> 
          <?php foreach ($this->paginator as $product): ?>
        <li class="sitestoreproduct_q_v_wrap g_b <?php if ($isLarge): ?>largephoto<?php endif; ?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
          <div>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
              <?php if ($product->newlabel): ?>
                <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                <?php endif; ?>
              <?php endif; ?>
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
                <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php endif; ?> "></span>
              </a>
            </div>  
            <div class="sitestoreproduct_grid_title">
  <?php echo $this->htmlLink($product->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($product->getTitle(), $this->truncation), array('title' => $product->getTitle())) ?>
            </div>

              <?php if (!empty($this->wishlistObjArray) && !empty($product->wishlist_id)): ?>
              <div class="sitestoreproduct_grid_stats clr seaocore_txt_light">
                <?php
                $getWishlistObj = $this->wishlistObjArray[$product->wishlist_id];
                if( $this->viewer_id == $getWishlistObj->owner_id )
                    echo 'Wishlist: ' . $this->htmlLink($getWishlistObj->getHref(), $getWishlistObj->getTitle());
                else
                    echo 'Wishlist: ' . $getWishlistObj->getTitle();
                ?>
              </div>  
  <?php endif; ?> 
                
            <div class="sitestoreproduct_grid_stats clr">
              <a href="<?php echo $product->getCategory()->getHref() ?>"> 
  <?php echo $this->translate($product->getCategory()->getTitle(true)) ?>
              </a>
            </div>

            <?php
            // CALLING HELPER FOR GETTING PRICE INFORMATIONS
            echo $this->getProductInfo($product, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock);
            ?>

              <?php if (!empty($this->statistics)): ?>  
              <div class="sitestoreproduct_grid_stats clr seaocore_txt_light">
                <?php
                $statistics = '';
                if (in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $product->comment_count), $this->locale()->toNumber($product->comment_count)) . ', ';
                }

                if (in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                  $statistics .= $this->translate(array('%s review', '%s reviews', $product->review_count), $this->locale()->toNumber($product->review_count)) . ', ';
                }

                if (in_array('followCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s follow', '%s follows', $product->follow_count), $this->locale()->toNumber($product->follow_count)) . ', ';
                }

                if (in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $product->view_count), $this->locale()->toNumber($product->view_count)) . ', ';
                }

                if (in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $product->like_count), $this->locale()->toNumber($product->like_count)) . ', ';
                }

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
                ?>
              <?php echo $statistics; ?> 
              </div>
              <?php endif; ?>


            <?php if(in_array('reviewCount', $this->statistics)): ?>
              <div class="sitestoreproduct_grid_rating">
                <?php if ($ratingValue == 'rating_both'): ?>
                  <?php echo $this->showRatingStarSitestoreproduct($product->rating_editor, 'editor', $ratingShow); ?>
                  <?php echo $this->showRatingStarSitestoreproduct($product->rating_users, 'user', $ratingShow); ?>
                <?php else: ?>
                  <?php echo $this->showRatingStarSitestoreproduct($product->$ratingValue, $ratingType, $ratingShow); ?>
                <?php endif; ?>

                  <?php if (!empty($this->statistics) && in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)): ?>
                  <span>
                    <?php
                    echo $this->htmlLink($product->getHref(), $this->partial(
                                    '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $product)));
                    ?>
                  </span>
                  <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="sitestoreproduct_grid_view_list_btm">
              <div class="sitestoreproduct_grid_view_list_footer b_medium">
                  <?php echo $this->compareButtonSitestoreproduct($product); ?>
                <span class="fright">
  <?php echo $this->addToWishlistSitestoreproduct($product, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => '')); ?>  
                </span>
              </div>
            </div>
          </div>
        </li>
  <?php endforeach; ?>
    </ul>
  </div>
<?php //endif;   ?>



  <div class="clear"></div>
  <div class="seaocore_pagination">
        <?php // echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true,));   ?>
    <div>
        <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
        <div id="wishlist_store_table_rate_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="tablerate_spinner_prev"></span>
        </div>
        <?php endif; ?>
        <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div id="wishlist_store_table_rate_next" class="paginator_next">
          <span id="wishlist_tablerate_spinner_next"></span>
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>
<?php endif; ?>
    </div>
  </div>	
</div>
<?php endif; ?>

