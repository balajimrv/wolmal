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
<?php if (empty($this->isajax)) : ?>
  <div id="main_layout" class="ui-page-content">
  <?php endif; ?>

  <?php if ($this->is_ajax_load): ?>
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

    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
    <?php if ($this->paginator->count() > 0): ?>
      <?php if (!$this->viewmore): ?>  
          <div>
            <ul class="p_list_grid"> 
            <?php endif; ?>    
            <?php $isLarge = ($this->columnWidth > 170); ?>
      <?php foreach ($this->paginator as $sitestoreproduct): ?>          
              <li style="height:<?php echo $this->columnHeight ?>px;">
                <a href="<?php echo $sitestoreproduct->getHref(); ?>" class="ui-link-inherit">
                  <div class="p_list_grid_top_sec">
                    <div class="p_list_grid_img">
                      <?php
                      $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_listing_thumb_normal.png';
                      $temp_url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                      if (!empty($temp_url)): $url = $sitestoreproduct->getPhotoUrl('thumb.profile');
                      endif;
                      ?>
                      <span style="background-image: url(<?php echo $url; ?>);"> </span>
                    </div>                 
                    <div class="p_list_grid_title">
                    <span><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationGrid) ?></span> 
                    </div>
                  </div>
                  <div class="p_list_grid_info">
        <?php if ($ratingValue == 'rating_both'): ?>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?></span>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?></span>
                    <?php else: ?>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?></span>
                      <?php endif; ?> 
                    <span class="p_list_grid_stats">
        <?php echo '<b>' . $sitestoreproduct->getCategory()->getTitle(true) . '</b>' ?>
                    </span>
                    <span class="p_list_grid_stats">                                  
                      <?php
                      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showInStock);
                      ?>
                    </span>
                    <?php if (!empty($this->statistics)): ?>
                      <?php $contentArray = array(); ?>
                      <?php
                      if (in_array('likeCount', $this->statistics)) {
                        $contentArray[] = $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count));
                      }

                      if (in_array('viewCount', $this->statistics)) {
                        $contentArray[] = $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count));
                      }

                      if (in_array('commentCount', $this->statistics)) {
                        $contentArray [] = $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count));
                      }

                      if (in_array('reviewCount', $this->statistics)) {
                        $contentArray[] = $this->partial(
                                '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct));
                      }
                      ?>
                      <?php if (!empty($contentArray)): ?>
                        <span class="p_list_grid_stats"><?php echo join(" - ", $contentArray); ?></span>
                      <?php endif; ?>
                      <?php endif; ?>  
                    <span class="p_list_grid_stats">
                      <?php $contentArray = array(); ?>
                      <?php if (!empty($this->showContent) && is_array($this->showContent) && in_array('postedDate', $this->showContent)): ?>
                        <?php $contentArray[] = $this->timestamp(strtotime($sitestoreproduct->creation_date)); ?>
                      <?php endif; ?>
                      <?php
                      if (!empty($this->postedby)):
                        $contentArray[] = $this->translate('created by') . '  <b>' . $sitestoreproduct->getOwner()->getTitle() . '</b>';
                        ?>
                      <?php endif; ?>
                      <?php
                      if (!empty($contentArray)) {
                        echo join(" - ", $contentArray);
                      }
                      ?> 
                    </span>
                  </div> 
                </a>
              </li>
            <?php endforeach; ?>
      <?php if (!$this->viewmore): ?>
            </ul>
          </div>
        <?php endif; ?>  
      
  <?php else: ?>
      <div class="tip mtop10"> 
        <span> 
    <?php echo $this->translate('No products have been created yet.'); ?>
        </span>
      </div>
    <?php endif; ?>
      <?php if ($this->params['page'] < 2 && $this->totalCount > ($this->params['page'] * $this->params['limit'])) : ?>
      <div class="feed_viewmore clr">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => 'feed_viewmore_link',
            'class' => 'ui-btn-default icon_viewmore',
            'onclick' => 'sm4.switchView.viewMoreEntity(' . $this->identity . ',widgetUrl)'
        ))
        ?>
      </div>
      <div class="seaocore_loading feeds_loading" style="display: none;">
        <i class="ui-icon-spinner ui-icon icon-spin"></i>
      </div>
    <?php endif; ?>
  <?php endif; ?>
<?php if (empty($this->isajax)) : ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
    var widgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/store-profile-products';
       sm4.core.runonce.add(function() {
      var currentpageid = $.mobile.activePage.attr('id') + '-' + <?php echo $this->identity; ?>;
      sm4.switchView.pageInfo[currentpageid] = $.extend({},  {'params': $.extend(<?php echo json_encode($this->params) ?>, {'renderDefault': 1,'totalCount': '<?php echo $this->totalCount; ?>'})},{'viewType': '<?php echo $this->viewType; ?>'} );
    });
    
 </script>