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
<?php if ($this->is_ajax_load): ?>
    <?php if (!$this->viewmore) : ?>
        <?php if (count($this->layouts_views) > 1 && $this->paginator->count() > 0): ?>
            <?php
// Parse query and remove page
            if (!empty($this->formValuesSM) && (is_array($this->formValuesSM))) :
                $query = $this->formValuesSM;
            endif;
            $query['view_selected'] = 'listview';
            $queryL = http_build_query($query);
            $queryL = '?' . $queryL;
            $query['view_selected'] = 'gridview';
            $queryG = http_build_query($query);
            $queryG = '?' . $queryG;
            ?>
            <div class="p_view_op ui-page-content">
                <?php
                echo $this->htmlLink(array(
                    'reset' => false,
                    'QUERY' => $queryL,
                        ), '<span  class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span>', array(
                    'class' => 'ui-link-inherit'
                ))
                ?>
                <?php
                echo $this->htmlLink(array(
                    'reset' => false,
                    'QUERY' => $queryG,
                        ), '<span  class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span>', array(
                    'class' => 'ui-link-inherit'
                ))
                ?>
            <!--      <a href="<?php //echo $this->url(array()); ?>" class="ui-link-inherit"> <span  class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span></a>
               <a href="<?php //echo $this->url(array());  ?>?view_selected=gridview" class="ui-link-inherit" ><span  class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span></a>-->
            </div>
        <?php endif; ?>
        <div id="main_layout" class="ui-page-content">
        <?php endif; ?>
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

        <?php if ($this->paginator->count() > 0): ?>
            <?php if ($this->view_selected == 'listview'): ?>
                <?php //if (!$this->viewmore): ?>
                <div id="list_view" class="sm-content-list">
                    <ul data-role="listview" data-inset="false">
                        <?php //endif;  ?>    
                        <?php foreach ($this->paginator as $sitestoreproduct): ?>
                            <li data-icon="arrow-r">
                                <a href="<?php echo $sitestoreproduct->getHref(); ?>">
                                    <?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon'); ?>
                                    <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation); ?></h3>
                                    <!--NEW LABEL-->
                                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                        <?php if (!empty($sitestoreproduct->newlabel)): ?> 
                                            <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($ratingValue == 'rating_both'): ?>
                                        <p><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?></p>
                                        <p><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?> </p>
                                    <?php else: ?>
                                        <p><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?> </p>
                                    <?php endif; ?>
                                    <p><?php echo '<b>' . $sitestoreproduct->getCategory()->getTitle(true) . '</b>' ?></p>
                                                                  <!--<span class="p_list_grid_stats">--> 

                                    <!--CALLING HELPER FOR GETTING PRODUCT INFORMATIONS--> 
                                    <p><?php echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showinStock); ?></p>
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
                                            <p><?php echo join(" - ", $contentArray); ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <p>
                                        <?php $contentArray = array(); ?>
                                        <?php if (!empty($this->showContent) && is_array($this->showContent) && in_array('postedDate', $this->showContent)): ?>
                                            <?php $contentArray[] = $this->timestamp(strtotime($sitestoreproduct->creation_date)); ?>
                                        <?php endif; ?>

                                        <?php
                                        if ($this->postedby):
                                            $contentArray[] = $this->translate('created by') . '  <b>' . $sitestoreproduct->getOwner()->getTitle() . '</b>';
                                            ?>
                                        <?php endif; ?>
                                        <?php
                                        if (!empty($contentArray)) {
                                            echo join(" - ", $contentArray);
                                        }
                                        ?> 
                                    </p>    
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php //if (!$this->viewmore): ?>
                    </ul>
                </div>
                <?php //endif;  ?>    

            <?php elseif ($this->view_selected == 'gridview'): ?>
                <?php //if (!$this->viewmore): ?>  
                <div>
                    <ul class="p_list_grid"> 
                        <?php //endif;  ?>    
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
                                        <!--NEW LABEL-->
                                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                            <?php if (!empty($sitestoreproduct->newlabel)): ?> 
                                                <span class="p_list_grid_stats">                
                                                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                                </span>            
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($ratingValue == 'rating_both'): ?>
                                            <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?></span>
                                            <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?></span>
                                        <?php else: ?>
                                            <span class="p_list_grid_stats"><?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?></span>
                                        <?php endif; ?> 
                                        <span class="p_list_grid_stats">
                                            <?php echo '<b>' . $this->translate($sitestoreproduct->getCategory()->getTitle(true)) . '</b>' ?>
                                        </span>
                                        <span class="p_list_grid_stats">                                  
                                            <?php
                                            // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                            echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', 0, $this->showinStock);
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
                        <?php //if (!$this->viewmore): ?>
                    </ul>
                </div>
                <?php //endif;  ?>  
            <?php endif; ?>
        <?php else: ?>
            <div class="tip mtop10"> 
                <span> 
                    <?php echo $this->translate('No products have been created yet.'); ?>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($this->paginator->count() > 1): ?>
            <?php
            echo $this->paginationControl($this->paginator, null, null, array(
                'query' => $this->formValuesSM, 'pageAsQuery' => true
            ));
            ?>
        <?php endif; ?>
        <?php //endif; ?>
    <?php if (!$this->viewmore) : ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div id="layout_sitestoreproduct_browse_products_<?php echo $this->identity; ?>">
    </div>  
    <script type="text/javascript">
        var requestParams = $.extend(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_sitestoreproduct_browse_products_<?php echo $this->identity; ?>',
            'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
            requestParams: requestParams
        };
        sm4.core.runonce.add(function () {
            setTimeout((function () {
                $.mobile.loading().loader("show");
            }), 100);

            sm4.core.locationBased.startReq(params);
        });
    </script>
<?php endif; ?>