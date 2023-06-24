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
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>
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

<?php $doNotShowTopContent = 0; ?>
<?php if ($this->categoryName && !empty($this->categoryObject->top_content)): ?>

    <h4 class="sr_sitestoreproduct_browse_lists_view_options_head mbot10" style="display: inherit;">
    <?php echo $this->translate($this->categoryName); ?>
    </h4>

    <?php $doNotShowTopContent = 1; ?>
<?php endif; ?>

<?php if ($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->category_id)->top_content; ?></div>
<?php elseif ($this->subcategory_id && $this->category_id && !$this->subsubcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->subcategory_id)->top_content; ?></div>
<?php elseif ($this->subsubcategory_id && $this->category_id && $this->subcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->subsubcategory_id)->top_content; ?></div>
<?php endif; ?> 

<?php if ($this->paginator->count() > 0): ?>

    <script type="text/javascript">
        var pageAction = function (page) {

            var form;
            if ($('filter_form')) {
                form = document.getElementById('filter_form');
            } else if ($('filter_form_sitestoreproduct')) {
                form = $('filter_form_sitestoreproduct');
            }
            form.elements['page'].value = page;

            form.submit();
        }
    </script>

    <form id='filter_form_sitestoreproduct' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true) ?>' style='display: none;'>
        <input type="hidden" id="page" name="page"  value=""/>
    </form>

                <?php if (($this->list_view && $this->grid_view) || ($this->grid_view) || ($this->list_view)): ?>
        <div class="sr_sitestoreproduct_browse_lists_view_options b_medium">
            <div class="fleft"> 
                <?php if ($this->categoryName && $doNotShowTopContent != 1): ?>
                    <h4 class="sr_sitestoreproduct_browse_lists_view_options_head">
                <?php echo $this->translate($this->categoryName); ?>
                    </h4>
        <?php endif; ?>
        <?php echo $this->translate(array('%s product found.', '%s product found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
            </div>

            <?php if ($this->list_view): ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0);" ></span>
                </span>
        <?php endif; ?>

            <?php if ($this->grid_view): ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1);" ></span>
                </span>
        <?php endif; ?>

        </div>
        <?php endif; ?>

    <?php if ($this->list_view): ?>

        <div id="grid_view" <?php if($this->defaultView !=0): ?>style="display: none;" <?php else:?>style="display: block;"<?php endif; ?>>

                <?php if (empty($this->viewType)): ?>

                <ul class="sr_sitestoreproduct_browse_list">
                        <?php foreach ($this->paginator as $sitestoreproduct): ?>

                                <?php if (!empty($sitestoreproduct->sponsored)): ?>
                            <li class="list_sponsered b_medium sitestoreproduct_q_v_wrap">
                                <?php else: ?>
                            <li class="b_medium sitestoreproduct_q_v_wrap">
                                <?php endif; ?>
                            <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                                <?php $product_id = $sitestoreproduct->product_id; ?>
                                <?php $quickViewButton = true; ?>
                                <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                    <?php if ($sitestoreproduct->featured && !empty($this->featuredIcon)): ?>
                                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                                    <?php endif; ?>
                                    <?php if ($sitestoreproduct->newlabel && !empty($this->newIcon)): ?>
                                        <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                    <?php endif; ?>
                                <?php endif; ?>

                                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>

                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                    <?php if (!empty($sitestoreproduct->sponsored) && !empty($this->sponsoredIcon)): ?>
                                        <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');
                        ; ?>">
                                        <?php echo $this->translate('SPONSORED'); ?>                 
                                        </div>
                    <?php endif; ?>
                                        <?php endif; ?>
                            </div>
                                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)): ?>
                                <div class="sr_sitestoreproduct_browse_list_rating">
                    <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                                        <div class="clr">	
                                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                                        <?php echo $this->translate("Editor Rating"); ?>
                                            </div>
                                                    <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                                <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                    <span class="fleft">
                                                <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                                                    </span>
                        <?php if (count($ratingData) > 1): ?>
                                                        <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                </span>

                                                            <?php if (count($ratingData) > 1): ?>
                                                    <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                                                        <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                        <div class="parameter_title">
                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                        <div class="parameter_value">
                                                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="parameter_title">
                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                        </div>	
                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                                                        </div>
                                                        <?php endif; ?> 
                                                                </div>

                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div> 
                                            <?php endif; ?>
                                            <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                                        <div class="clr">
                                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                                <?php echo $this->translate("User Ratings"); ?><br />
                                                <?php
                                                $totalUserReviews = $sitestoreproduct->review_count;
                                                if ($sitestoreproduct->rating_editor) {
                                                    $totalUserReviews = $sitestoreproduct->review_count - 1;
                                                }
                                                ?>
                                                        <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                                            </div>
                                                    <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                                <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                    <span class="fleft">
                                                <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                                                    </span>
                        <?php if (count($ratingData) > 1): ?>
                                                        <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                </span>

                                                            <?php if (count($ratingData) > 1): ?>
                                                    <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                                        <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                        <div class="parameter_title">
                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                        <div class="parameter_value">
                                                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="parameter_title">
                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                        </div>	
                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                        </div>
                                                        <?php endif; ?> 
                                                                </div>

                                            <?php endforeach; ?>
                                                        </div>
                                                    </div> 
                        <?php endif; ?> 
                                            </div>
                                        </div>  
                                            <?php endif; ?>

                                        <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                                        <div class="clr">
                                            <div class="sr_sitestoreproduct_browse_list_rating_stats">
                        <!--	                    <?php //echo $this->translate("Overall Rating"); ?><br />-->

                                                        <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                                            </div>
                                                    <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                                            <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                                <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                    <span class="fleft">
                                                <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                                                    </span>
                        <?php if (count($ratingData) > 1): ?>
                                                        <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                </span>

                                                            <?php if (count($ratingData) > 1): ?>
                                                    <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                                        <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                            <?php foreach ($ratingData as $reviewcat): ?>

                                                                <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                        <div class="parameter_title">
                                                                            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                        <div class="parameter_value">
                                                                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="parameter_title">
                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                        </div>	
                                                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                        </div>
                                                        <?php endif; ?> 
                                                                </div>

                                            <?php endforeach; ?>
                                                        </div>
                                                    </div> 
                        <?php endif; ?> 
                                            </div>
                                        </div>  
                    <?php endif; ?>
                                </div>
                                        <?php endif; ?>

                            <div class='sr_sitestoreproduct_browse_list_info'>  

                                <div class='sr_sitestoreproduct_browse_list_info_header o_hidden'>
                                    <div class="sr_sitestoreproduct_list_title">
                                        <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                                    <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                                <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                                    </a>
                                </div>

                                <!-- DISPLAY PRODUCTS -->
                                    <?php
                                    // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                    echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true);
                                    ?>

                                <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                                    <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?><?php if ($this->postedby): ?> - <?php echo $this->translate('created by'); ?>
                                        <?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle()) ?><?php endif ?><?php if (!empty($this->statistics)): ?>,

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
                                    <?php endif; ?>
                                </div>

                                <div class='sr_sitestoreproduct_browse_list_info_blurb'>
                <?php if ($this->bottomLine): ?>
                    <?php echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000); ?>
                                        <?php else: ?>
                                            <?php echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000); ?>
                <?php endif; ?>
                                </div>
                                <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                                    <div>
                                    <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity); ?>
                                    </div>
                                    <div>
                                        <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
                                    </div>  
                                        <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) : ?>   
                                        <div class="sr_sitestoreproduct_browse_list_info_footer_icons">
                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $sitestoreproduct->closed): ?>
                                                <img alt="close" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/close.png'/>
                                            <?php endif; ?>  
                                            <?php if ($sitestoreproduct->sponsored == 1 && !empty($this->sponsoredIcon)): ?>
                                                <i class="sr_sitestoreproduct_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
                                        <?php endif; ?>
                    <?php if ($sitestoreproduct->featured == 1 && !empty($this->featuredIcon)): ?>
                                                <i class="sr_sitestoreproduct_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
                            <?php endif; ?>
                                        </div>
                <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php else: ?>

                <ul class="sr_sitestoreproduct_browse_list">
                        <?php foreach ($this->paginator as $sitestoreproduct): ?>

                            <?php if (!empty($sitestoreproduct->sponsored)): ?>
                            <li class="list_sponsered b_medium sitestoreproduct_q_v_wrap">
                            <?php else: ?>
                            <li class="b_medium sitestoreproduct_q_v_wrap">
                            <?php endif; ?>
                            <?php $product_id = $sitestoreproduct->product_id; ?>
                            <?php $quickViewButton = true; ?>
                            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                <?php if ($sitestoreproduct->featured && !empty($this->featuredIcon)): ?>
                                    <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                                <?php endif; ?>
                                <?php if ($sitestoreproduct->newlabel && !empty($this->newIcon)): ?>
                                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>
                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                                <?php if (!empty($sitestoreproduct->sponsored) && !empty($this->sponsoredIcon)): ?>
                                    <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                    </div>
                    <?php endif; ?>
                                        <?php endif; ?>
                            
                                    <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                                <div class="sr_sitestoreproduct_browse_list_rating">
                                    <div class="clr">	
                                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                                    <?php echo $this->translate("Editor Rating"); ?>
                                        </div>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                <span class="fleft">
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                                                </span>
                                            <?php if (count($ratingData) > 1): ?>
                                                    <i class="fright arrow_btm"></i>
                    <?php endif; ?>
                                            </span>

                    <?php if (count($ratingData) > 1): ?>
                                                <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                                                    <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                            <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <div class="parameter_value">
                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <?php else: ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                    </div>	
                                                                    <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                                                    </div>
                                                    <?php endif; ?> 
                                                            </div>

                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                    <?php endif; ?>
                                        </div>
                                    </div> 
                                        <?php endif; ?>
                                        <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                                    <div class="clr">
                                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                            <?php echo $this->translate("User Ratings"); ?><br />
                                            <?php
                                            $totalUserReviews = $sitestoreproduct->review_count;
                                            if ($sitestoreproduct->rating_editor) {
                                                $totalUserReviews = $sitestoreproduct->review_count - 1;
                                            }
                                            ?>
                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                                        </div>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                <span class="fleft">
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                                                </span>
                                            <?php if (count($ratingData) > 1): ?>
                                                    <i class="fright arrow_btm"></i>
                    <?php endif; ?>
                                            </span>

                    <?php if (count($ratingData) > 1): ?>
                                                <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                                    <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                            <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <div class="parameter_value">
                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <?php else: ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                    </div>	
                                                                    <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                    </div>
                                                    <?php endif; ?> 
                                                            </div>

                                        <?php endforeach; ?>
                                                    </div>
                                                </div> 
                                    <?php endif; ?> 
                                        </div>
                                    </div>  
                <?php endif; ?>

                                        <?php if (in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                                    <div class="clr">
                                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
                    <!--	                    <?php //echo $this->translate("Overall Rating"); ?><br />-->

                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                                        </div>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                                                <span class="fleft">
                    <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                                                </span>
                                            <?php if (count($ratingData) > 1): ?>
                                                    <i class="fright arrow_btm"></i>
                    <?php endif; ?>
                                            </span>

                    <?php if (count($ratingData) > 1): ?>
                                                <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                                    <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                            <div class="o_hidden">
                                                                    <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <div class="parameter_value">
                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                    </div>
                                                                    <?php else: ?>
                                                                    <div class="parameter_title">
                                                                    <?php echo $this->translate("Overall Rating"); ?>
                                                                    </div>	
                                                                    <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'big-star'); ?>
                                                                    </div>
                                                    <?php endif; ?> 
                                                            </div>

                        <?php endforeach; ?>
                                                    </div>
                                                </div> 
                    <?php endif; ?> 
                                        </div>
                                    </div>  
                                </div>
                                        <?php endif; ?>


                            <div class="sr_sitestoreproduct_browse_list_info">
                                <div class="sr_sitestoreproduct_browse_list_info_header">
                                    <div class="sr_sitestoreproduct_list_title">
                                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                                    </div>
                                </div>  

                                <div class='sr_sitestoreproduct_browse_list_info_blurb'>
                <?php if ($this->bottomLine): ?>
                    <?php echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000); ?>
                <?php else: ?>
                                            <?php echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000); ?>
                                        <?php endif; ?>
                                </div>

                                <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                                    <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                                <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                                    </a>
                                </div>

                                <!-- DISPLAY PRODUCTS -->
                                    <?php
                                    // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                    echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true);
                                    ?>

                                    <?php if (!empty($this->statistics)): ?>
                                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
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
                                        <?php echo $statistics ?>
                                    </div>   
                                    <?php endif; ?>

                                <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?><?php if ($this->postedby): ?> - <?php echo $this->translate('created by'); ?>
                                            <?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle()) ?><?php endif; ?>
                                </div>

                                <div class="mtop10 sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                                    <div>
                <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                                    </div>
                                    <div>
                                            <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
                                    </div>
                                    <div>
                                            <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) : ?>  
                                            <div class="sr_sitestoreproduct_browse_list_info_footer_icons">
                                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && $sitestoreproduct->closed): ?>
                                                    <img alt="close" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/close.png'/>
                                                <?php endif; ?>
                                                <?php if ($sitestoreproduct->sponsored == 1 && !empty($this->sponsoredIcon)): ?>
                                                    <i class="sr_sitestoreproduct_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
                                            <?php endif; ?>
                                            <?php if ($sitestoreproduct->featured == 1 && !empty($this->featuredIcon)): ?>
                                                    <i class="sr_sitestoreproduct_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
                    <?php endif; ?>
                                            </div>
                        <?php endif; ?>
                                    </div>
                                </div>
                        </li>
            <?php endforeach; ?>
                </ul>

        <?php endif; ?>

        </div>

            <?php endif; ?>

            <?php if ($this->grid_view): ?>
        <div id="image_view" class="sr_sitestoreproduct_container" <?php if($this->defaultView !=1): ?>style="display: none;" <?php else:?>style="display: block;"<?php endif; ?>>
            <ul class="sitestoreproduct_grid_view mtop10">
                        <?php $isLarge = ($this->columnWidth > 170); ?>
                        <?php foreach ($this->paginator as $sitestoreproduct): ?>          
                    <li class="sitestoreproduct_q_v_wrap g_b <?php if ($isLarge): ?>largephoto<?php endif; ?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                        <div>
                                <?php if ($sitestoreproduct->newlabel && !empty($this->newIcon)): ?>
                                <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                <?php endif; ?>
                            <div class="sitestoreproduct_grid_view_thumb_wrapper">
                                    <?php $product_id = $sitestoreproduct->product_id; ?>
                                    <?php $quickViewButton = true; ?>
                                    <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                                <a href="<?php echo $sitestoreproduct->getHref() ?>" class="sitestoreproduct_grid_view_thumb">
                                    <?php
                                    $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                                    if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
                                    endif;
                                    ?>
                                    <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?>height:160px; <?php endif; ?>"></span>
                                </a>
                            </div>  
                            <div class="sitestoreproduct_grid_title">
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationGrid), array('title' => $sitestoreproduct->getTitle())) ?>
                            </div>
                            <div class="sitestoreproduct_grid_stats clr">
                                <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?> </a>
                            </div>

                            <!-- DISPLAY PRODUCTS -->
                            <?php
                            // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                            echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock);
                            ?>

                                <?php if (in_array('viewRating', $this->statistics)): ?>
                                <div class="sitestoreproduct_grid_rating"> 
                                    <?php if ($ratingValue == 'rating_both'): ?>
                                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                                        <?php else: ?>
                                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                                        <?php endif; ?> 
                                    <?php if (in_array('reviewCount', $this->statistics)): ?>
                                        <span>
                                    <?php
                                    echo $this->htmlLink($sitestoreproduct->getHref(), $this->partial(
                                                    '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct)), array('title' => $this->partial(
                                                '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct))));
                                    ?>
                                        </span>
                                            <?php endif; ?>
                                </div>
                                        <?php endif; ?>

                            <div class="sitestoreproduct_grid_view_list_btm">
                                <div class="sitestoreproduct_grid_view_list_footer b_medium">
                                        <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                                    <span class="fright">
            <?php if ($sitestoreproduct->sponsored == 1 && !empty($this->sponsoredIcon)): ?>
                                            <i class="sr_sitestoreproduct_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
            <?php endif; ?>
            <?php if ($sitestoreproduct->featured == 1 && !empty($this->featuredIcon)): ?>
                                            <i class="sr_sitestoreproduct_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
                    <?php endif; ?>
            <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => '')); ?>
                                    </span>
                                </div>
                            </div>  
                        </div>
                    </li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="clear"></div>
    
    <div class="clr" id="scroll_bar_height"></div>
      <?php if (empty($this->is_ajax)) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => '',
              'class' => 'buttonlink icon_viewmore'
          ))
          ?>
        </div>
    <div class="seaocore_view_more" id="loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <div id="hideResponse_div"> </div>
  <?php endif; ?>
<?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
    <br/>
    <div class="tip mtip10">
        <span> <?php echo $this->translate('Nobody has created a product with that criteria.'); ?>
        </span> 
    </div>
<?php else: ?>
    <div class="tip mtop10"> 
        <span> 
    <?php echo $this->translate('No products have been created yet.'); ?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript" >
    function switchview(flage) {
        if (flage == 1) {
            if ($('image_view')) {
                if ($('sr_sitestoreproduct_map_canvas_view_browse'))
                    $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
                if ($('grid_view'))
                    $('grid_view').style.display = 'none';
                $('image_view').style.display = 'block';
            }
        } else {
            if ($('grid_view')) {
                if ($('sr_sitestoreproduct_map_canvas_view_browse'))
                    $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
                $('grid_view').style.display = 'block';
                if ($('image_view'))
                    $('image_view').style.display = 'none';
            }
        }
    }
</script>

<script type="text/javascript">

    /* moo style */
    en4.core.runonce.add(function () {
        //opacity / display fix
        $$('.sitestoreproduct_tooltip').setStyles({
            opacity: 0,
            display: 'block'
        });
        //put the effect in place
        $$('.jq-sitestoreproduct_tooltip li').each(function (el, i) {
            el.addEvents({
                'mouseenter': function () {
                    el.getElement('div').fade('in');
                },
                'mouseleave': function () {
                    el.getElement('div').fade('out');
                }
            });
        });
<?php if ($this->paginator->count() > 0): ?>
            switchview(<?php echo $this->defaultView ?>);
<?php endif; ?>
    });

</script>

<?php if ($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->category_id)->bottom_content; ?></div>
<?php elseif ($this->subcategory_id && $this->category_id && !$this->subsubcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->subcategory_id)->bottom_content; ?></div>
<?php elseif ($this->subsubcategory_id && $this->category_id && $this->subcategory_id): ?>
    <div class="sr_sitestoreproduct_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitestoreproduct_category', $this->subsubcategory_id)->bottom_content; ?></div>
<?php endif; ?> 

    <?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMoreEvent()
    {
      var viewType;
      if($('grid_view')) {
        if($('grid_view').style.display== 'block')
          viewType = 0;
      }
      if($('image_view')) {
      if($('image_view').style.display== 'block')
        viewType = 1;
      }
      
      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/browse-products-sitestoreproduct',
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            is_ajax: 1,
            show_content: '<?php echo $this->paginationType;?>',
            view_type: viewType,
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            if($('grid_view')) {
              $('grid_view').getElement('.sr_sitestoreproduct_browse_list').innerHTML = $('grid_view').getElement('.sr_sitestoreproduct_browse_list').innerHTML + $('hideResponse_div').getElement('.sr_sitestoreproduct_browse_list').innerHTML;
            }
            if($('image_view')) {
              $('image_view').getElement('.sitestoreproduct_grid_view').innerHTML = $('image_view').getElement('.sitestoreproduct_grid_view').innerHTML+ $('hideResponse_div').getElement('.sitestoreproduct_grid_view').innerHTML;
            }
            $('hideResponse_div').innerHTML = '';
            $('loding_image').style.display = 'none';
            switchview(viewType);
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

<?php if ($this->paginationType == 3): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->paginationType; ?>');
    });</script>
<?php elseif ($this->paginationType == 2): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->paginationType; ?>');
    });</script>
<?php else: ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'none';
    });
  </script>
  <?php
  echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitestore"), array("orderby" => $this->orderby, "query" => $this->formValues));
  ?>
<?php endif; ?>

<script type="text/javascript">

  function getNextPage() {
    return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
  }

  function hideViewMoreLink(showContent) {

    if (showContent == 3) {
      $('seaocore_view_more').style.display = 'none';
      var totalCount = '<?php echo $this->paginator->count(); ?>';
      var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

      function doOnScrollLoadPage()
      {
        if($('scroll_bar_height')) {
          if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
            var elementPostionY = $('scroll_bar_height').offsetTop;
          } else {
            var elementPostionY = $('scroll_bar_height').y;
          }
          if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
            if ((totalCount != currentPageNumber) && (totalCount != 0))
              viewMoreEvent();
          }
        }
      }
      
      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMoreEvent();
      });
    }
  }
  
</script>
