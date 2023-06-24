<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (empty($this->isajax)) : ?> 
    <div id="product_profile_layout" class="ui-page-content">
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

        <div id="list_view" class="sm-content-list">

            <ul data-role="listview" data-inset="false">
                    <?php foreach ($this->paginator as $sitestoreproduct): ?>
                <li data-icon="arrow-r">
                    <a href="<?php echo $sitestoreproduct->getHref(); ?>">
                        <?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon'); ?>
                        <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation); ?></h3>
                        <!--NEW LABEL-->
                          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
                            <?php if (!empty($sitestoreproduct->newlabel)): ?> 
                              <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                            <?php endif; ?>
                          <?php endif; ?>
                                <!--RATINGS-->
                          <?php if ($ratingValue == 'rating_both'): ?>
        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                            <br/>
                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                        <?php else: ?>
                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                        <?php endif; ?>
                        <p><?php echo '<b>' . $sitestoreproduct->getCategory()->getTitle(true) . '</b>' ?></p>               

                        <!-- DISPLAY PRODUCTS -->
                        <p><?php
                        // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                        echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', 0, $this->showinStock);
                        ?></p>
                        <p>

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

<!--                        <p> 
                                <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)) : ?>
                                <span class="sr_browse_list_info_footer_icons">
                                    <?php if ($sitestoreproduct->closed): ?>
                                        <i class="sr_icon icon_sitestoreproducts_close" title="<?php echo $this->translate('Closed'); ?>"></i>
                                    <?php endif; ?> 
                                <?php if ($sitestoreproduct->sponsored == 1): ?>
                                        <i title="<?php echo $this->translate('Sponsored'); ?>" class="sr_icon seaocore_icon_sponsored"></i>
        <?php endif; ?>
        <?php if ($sitestoreproduct->featured == 1): ?>
                                        <i title="<?php echo $this->translate('Featured'); ?>" class="sr_icon seaocore_icon_featured"></i>
                    <?php endif; ?>
                                </span>
                <?php endif; ?>

                        </p>       -->
                    </a>
                </li>
    <?php endforeach; ?>
            </ul>
        </div>
      <?php if (empty($this->isajax)) : ?> 
    </div>
    <?php endif; ?>

   <?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'product_profile_layout', array());
		?>
	<?php endif; ?>