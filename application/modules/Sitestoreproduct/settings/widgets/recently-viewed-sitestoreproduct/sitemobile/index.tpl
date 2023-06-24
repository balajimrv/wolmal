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
<?php 
  $ratingValue = $this->ratingType; 
  $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {$ratingType = 'editor';} elseif ($this->ratingType == 'rating_avg') {$ratingType = 'overall';} else { $ratingType = 'user';}
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');?>
	<div class="sm-content-list">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r" id="list-view">
			<?php foreach($this->products as $sitestoreproduct): ?>
				<li>
					<a href="<?php echo $sitestoreproduct->getHref(array('profile_link' => 1));?>">
						<?php echo $this->itemPhoto($sitestoreproduct, 'thumb.icon');?>
						<h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation) ?></h3>
						<p>
							<?php if ($ratingValue == 'rating_both'): ?>
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?><br />
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
							<?php else: ?>
								<?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
							<?php endif; ?>
						</p>
						<p>
								<b><?php echo $sitestoreproduct->getCategory()->getTitle(true) ?></b>
						</p>
            <p class="ui-li-aside">
							<?php if ($sitestoreproduct->sponsored == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
							<?php if ($sitestoreproduct->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							<?php endif; ?>
                <p><?php
                                // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock);
                                ?></p>
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
						
					</a>
				</li>
			<?php endforeach; ?>
		</ul>			
	</div>
