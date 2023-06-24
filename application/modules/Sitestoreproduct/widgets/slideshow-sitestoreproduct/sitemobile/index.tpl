<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
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
<div class="iscroll_carousal  data-width="" data-height="200 px">
      <div class='iscroll_carousal_wrapper prelative sm-widget-block' data-itemcount="<?php echo $this->num_of_slideshow ?>">
        <div class="iscroll_carousal_scroller" style="width: <?php echo $this->num_of_slideshow * 300 ?>px">
          <?php $i = 0; ?>
          <ul class="">
            <?php foreach ($this->show_slideshow_object as $type => $item):  ?>
              <li class="liPhoto">            
                <a href="<?php echo $item->getHref() ?>" class="ui-link-inherit">
                  <span class="slideshow-img prelative">
                    <!--NEW LABEL-->
                    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)):?>
                      <?php if (!empty($item->newlabel) && !empty($this->newIcon)): ?> 
                        <i class="new-label" title="<?php echo $this->translate('New'); ?>"></i>
                      <?php endif; ?>
                    <?php endif; ?>
                    <?php echo $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'center')); ?>
                  </span>
                  <div class="o_hidden slideshow-content">
                    <div class="slideshow-title bold">
                      <b><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->truncation); ?></b>
                    </div>
                    <!--RATINGS-->
                    <?php if ($ratingValue == 'rating_both'): ?>
                      <div class="clr">
                        <?php echo $this->showRatingStarSitestoreproduct($item->rating_editor, 'editor', $ratingShow); ?>
                        <?php echo $this->showRatingStarSitestoreproduct($item->rating_users, 'user', $ratingShow); ?>
                      </div>
                    <?php else: ?>
                      <div class="clr">
                        <?php echo $this->showRatingStarSitestoreproduct($item->$ratingValue, $ratingType, $ratingShow); ?>
                      </div>
                    <?php endif; ?>

                    <?php if (empty($this->category_id)): ?>
                        <div class="f_small"><?php echo '<b>' . $item->getCategory()->getTitle(true) . '</b>'; ?></div>
                    <?php endif; ?>

                    <div class="f_small">
                      <?php
                        // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                        echo $this->getProductInfo($item, $this->identity, 'list_view', 0, $this->showinStock);
                      ?>
                    </div>
                    <?php $contentArray = array(); ?>
                    <?php if (!empty($this->statistics)): ?>
                      <div class="f_small">
                        <?php
                        $statistics = '';
                        if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                            $contentArray[] = $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count));
                        }

                        if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                            $contentArray[] = $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count));
                        }

                        if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                            $contentArray[] = $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count));
                        }

                        if (in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)) {
                            $statistics .= $this->partial(
                                            '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $item)) . ', ';
                        }
                        if (!empty($contentArray)) {
                            echo join(" - ", $contentArray);
                        }
                        ?>
                      </div>
                    <?php endif; ?>
                    <div class="f_small">
                      <?php
                      $contentsArray = array();
                      if (!empty($this->showContent) && in_array('postedDate', $this->showContent)):
                          $contentsArray[] = $this->timestamp(strtotime($item->creation_date));
                      endif;
                      if (!empty($this->postedby)):
                          $contentsArray[] = $this->translate('created by') . ' <b>' . $item->getOwner()->getTitle() . '</b>';
                          if (!empty($contentsArray)) {
                              echo join(" - ", $contentsArray);
                          }
                          ?>
                      <?php endif; ?>   
                    </div> 
                  </div>
                </a>
              </li>
              <?php $i++; ?>
            <?php endforeach; ?>
          </ul>
        </div>       
      </div>
      <div class="iscroll_carousal_nav clr">
        <?php if($i>1): ?>
        <span class="iscroll_carousal_prev ui-icon ui-icon-caret-left" ></span>
        <span class="iscroll_carousal_next ui-icon ui-icon-caret-right" ></span>
        <ul class="iscroll_carousal_indicator" id="indicator">
          <?php for ($j = 1; $j <= $i; $j++): ?>
            <li class="<?php echo $j == 1 ? 'active' : '' ?>"><?php echo $j; ?></li>
          <?php endfor; ?>
        </ul>        
        <?php endif; ?>
      </div> 
    </div>