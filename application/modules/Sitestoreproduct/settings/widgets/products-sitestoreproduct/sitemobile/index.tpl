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
<?php if (!$this->viewmore) : ?>
<?php if (count($this->layouts_views) > 1) :?>
        <div class="p_view_op ui-page-content p_l">
            <span <?php if($this->viewType == 'gridview'): ?> onclick='sm4.switchView.getViewTypeEntity("listview", <?php echo $this->identity; ?>,widgetUrl);' <?php endif;?> class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span>
            <span <?php if($this->viewType == 'listview'): ?> onclick='sm4.switchView.getViewTypeEntity("gridview", <?php echo $this->identity; ?>,widgetUrl);' <?php endif;?> class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span>
        </div>
   <?php endif; ?>
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

        <?php if ($this->viewType == 'listview'): ?> 
            <?php if (!$this->viewmore): ?>
                <div id="list_view" class="sm-content-list">
                    <ul data-role="listview" data-inset="false" >
                    <?php endif; ?>
                    <?php foreach ($this->products as $sitestoreproduct): ?>
                        <li data-icon="arrow-r">
                            <a href="<?php echo $sitestoreproduct->getHref(); ?>">
                                <?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon'); ?>
                                <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->truncation); ?></h3>
                                <!--NEW LABEL-->
                                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
                                  <?php if (!empty($sitestoreproduct->newlabel)): ?> 
                                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                  <?php endif; ?>
                                <?php endif; ?>
                                  <?php if ($ratingValue == 'rating_both'): ?>
                                    <p><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                                        <br/>
                                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?></p>
                                <?php else: ?>
                                    <p><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?></p>
                                <?php endif; ?>

                                <?php if (empty($this->category_id)): ?>
                                    <p><?php echo '<b>' . $sitestoreproduct->getCategory()->getTitle(true) . '</b>'; ?></p>
                                <?php endif; ?>

                                <p>
                                <?php
                                // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', 0, $this->showinStock);
                                ?>
                                </p>
                                <?php $contentArray = array(); ?>
                                    <?php if (!empty($this->statistics)): ?>
                                    <p>
                                        <?php
                                        $statistics = '';
                                        if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                                            $contentArray[] = $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count));
                                        }

                                        if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                                            $contentArray[] = $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count));
                                        }

                                        if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                                            $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count));
                                        }

                                        if (in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                                            $statistics .= $this->partial(
                                                            '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct)) . ', ';
                                        }
                                        if (!empty($contentArray)) {
                                            echo join(" - ", $contentArray);
                                        }
                                        ?>
                                    </p>
                                <?php endif; ?>
                                <p>
                                    <?php
                                    $contentsArray = array();
                                    if (!empty($this->showContent) && in_array('postedDate', $this->showContent)):
                                        $contentsArray[] = $this->timestamp(strtotime($sitestoreproduct->creation_date));
                                    endif;
                                    if (!empty($this->postedby)):
                                        $contentsArray[] = $this->translate('created by') . ' <b>' . $sitestoreproduct->getOwner()->getTitle() . '</b>';
                                        if (!empty($contentsArray)) {
                                            echo join(" - ", $contentsArray);
                                        }
                                        ?>
            <?php endif; ?>   
                                </p> 
                            </a>
                        </li>
        <?php endforeach; ?>
            <?php if (!$this->viewmore): ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php else: ?>
        <?php $isLarge = ($this->columnWidth > 170); ?>
                    <?php if (!$this->viewmore): ?>
                <div id="grid_view">
                    <ul class="p_list_grid"> 
        <?php endif; ?> 
        <?php foreach ($this->products as $sitestoreproduct): ?>
                        <li style="height:<?php echo $this->columnHeight ?>px;">
                            <a href="<?php echo $sitestoreproduct->getHref(); ?>" class="ui-link-inherit"> 
                                <div class="p_list_grid_top_sec">
                                    <div class="p_list_grid_img">
                                        <?php
                                        $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
                                        endif;
                                        ?>
                                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                                    </div>
                                    <div class="p_list_grid_title">
                                        <span>
            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->truncation) ?>
                                        </span>
                                    </div>
                                </div> 
                              
                                <div class="p_list_grid_info">
                                  <!--NEW LABEL-->
                                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
                                  <?php if (!empty($sitestoreproduct->newlabel)): ?> 
                                  <span class="p_list_grid_stats">
                                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                  </span>
                                  <?php endif; ?>
                                <?php endif; ?>
                                        <?php if ($ratingValue == 'rating_both'): ?>
                                        <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?><br />
                                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?></span>
                                    <?php else: ?>
                                        <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?></span>
                                        <?php endif; ?>
                                    <span class="p_list_grid_stats">
                                    <?php echo '<b>' . $sitestoreproduct->getCategory()->getTitle(true) . '</b>' ?>
                                    </span>
                                        
                                        <span class="p_list_grid_stats">                                  
                                            <?php 
              // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
              echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showinStock); ?>
                                        </span>
                                        
                                        <?php $contentArray = array(); ?>
                                        <?php if (!empty($this->statistics)): ?>
                                        <span class="p_list_grid_stats">
                                            <?php
                                            $statistics = '';
                                            if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                                                $contentArray[] = $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count));
                                            }

                                            if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                                                $contentArray[] = $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count));
                                            }

                                            if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                                                $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count));
                                            }
                                        
                                            if (!empty($this->statistics) && in_array('reviewCount', $this->statistics)) {
                                                $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count));
                                            }
                                            if (!empty($contentArray)) {
                                                echo join(" - ", $contentArray);
                                            }
                                            ?>
                                        </span>
                                        <?php endif; ?>
                                    <span class="p_list_grid_stats">
                                        <?php if (!empty($this->showContent) && in_array('postedDate', $this->showContent)): ?>
                                            <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)); ?> - 
                                        <?php endif; ?>
                                        <?php if (!empty($this->postedby)): ?>
                <?php echo $this->translate('created by') . '  <b>' . $sitestoreproduct->getOwner()->getTitle() . '</b>'; ?>
            <?php endif; ?>
                                    </span>						
                                </div>
                            </a>
                        </li>
        <?php endforeach; ?>
            <?php if (!$this->viewmore): ?> 
                    </ul>
                </div>
            <?php endif; ?>
    <?php endif; ?>
    <?php else: ?>
        <div id="layout_sitestoreproduct_products_sitestoreproduct_<?php echo $this->identity; ?>">
        </div>
    <?php endif; ?>
        <?php if ($this->params['page'] < 2 && $this->totalCount > ($this->params['page'] * $this->params['limit'])) : ?>
        <div class="feed_viewmore clr" style="margin-bottom: 5px;">
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
    <script type="text/javascript">
        var widgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/products-sitestoreproduct';
      sm4.core.runonce.add(function() {     
        var currentpageid = $.mobile.activePage.attr('id') + '-' + <?php echo $this->identity; ?>;     
         sm4.switchView.pageInfo[currentpageid] = $.extend({},sm4.switchView.pageInfo[currentpageid], {'viewType' : '<?php echo $this->viewType; ?>', 'params': <?php echo json_encode($this->params) ?>, 'totalCount' : <?php echo $this->totalCount; ?>});     
      });
    </script>


<?php if (!$this->viewmore) : ?>
    </div>
    <style type="text/css">
        .ui-collapsible-content{padding-bottom:0;}
    </style>
<?php endif; ?>