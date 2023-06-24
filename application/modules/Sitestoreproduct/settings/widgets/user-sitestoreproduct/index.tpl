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

<?php if ($this->loaded_by_ajax): ?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitestoreproduct_user_sitestoreproduct')
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif; ?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
				->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
?>

<?php if ($this->showContent): ?>
  <?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitestoreproduct'); ?>
  <?php $reviewDescriptionsTable = Engine_Api::_()->getDbtable('reviewDescriptions', 'sitestoreproduct'); ?>
  <div id="user_review"></div>
  <?php if (empty($this->isajax)) : ?>
    <div class="sr_sitestoreproduct_profile_review b_medium sr_sitestoreproduct_review_block">
      <div class="sr_sitestoreproduct_profile_review_left">
        <div class="sr_sitestoreproduct_profile_review_title">
          <?php if (empty($reviewcatTopbox['ratingparam_name'])): ?>
            <?php echo $this->translate("Average User Rating"); ?>
          <?php endif; ?>
        </div>
        <?php $iteration = 1; ?>
        <div class="sr_sitestoreproduct_profile_review_stars">
          <span class="sr_sitestoreproduct_profile_review_rating">
            <span class="fleft">
              <?php echo $this->showRatingStarSitestoreproduct($this->subject()->rating_users, 'user', 'big-star'); ?>
            </span>
            <?php if (count($this->ratingDataTopbox) > 1): ?>
              <i class="arrow_btm fleft"></i>
            <?php endif; ?>
          </span>    
        </div>

        <?php if (count($this->ratingDataTopbox) > 1): ?>
          <div class="sr_sitestoreproduct_ur_bdown_box_wrapper br_body_bg b_medium">
            <div class="sr_sitestoreproduct_ur_bdown_box">
              <div class="sr_sitestoreproduct_profile_review_title">
                <?php echo $this->translate("Average User Rating"); ?>
              </div>
              <div class="sr_sitestoreproduct_profile_review_stars">
                <?php echo $this->showRatingStarSitestoreproduct($this->subject()->rating_users, 'user', 'big-star'); ?>
              </div>

              <div class="sr_sitestoreproduct_profile_rating_parameters">
                <?php $iteration = 1; ?>
                <?php foreach ($this->ratingDataTopbox as $reviewcatTopbox): ?>
                  <?php if (!empty($reviewcatTopbox['ratingparam_name'])): ?>	         
                    <div class="o_hidden">
                      <div class="parameter_title">
                        <?php echo $this->translate($reviewcatTopbox['ratingparam_name']) ?>
                      </div>
                      <div class="parameter_value">
                        <?php echo $this->showRatingStarSitestoreproduct($reviewcatTopbox['avg_rating'], 'user', 'small-box', $reviewcatTopbox['ratingparam_name']); ?>     
                      </div>
                      <div class="parameter_count"><?php echo $this->subject()->getNumbersOfUserRating('user', $reviewcatTopbox['ratingparam_id']); ?></div>
                    </div>
                  <?php endif; ?>
                  <?php $iteration++; ?>
                <?php endforeach; ?>
              </div>
              <div class="clr"></div>
            </div>
          </div>
        <?php endif; ?>

        <div class="sr_sitestoreproduct_profile_review_stat clr">
          <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
        </div>

        <div class="sr_sitestoreproduct_profile_review_stat clr">
          <?php echo $this->translate("Recommended by %s users", '<b>' . $this->recommend_percentage . '%</b>'); ?>
        </div>

        <?php if (!empty($this->viewer_id) && $this->can_create && empty($this->isajax)): ?>
          <?php $rating_value_2 = 0; ?>	
          <?php if (!empty($this->reviewRateData)): ?>	
            <?php foreach ($this->reviewRateData as $reviewRateData): ?>
              <?php if ($reviewRateData['ratingparam_id'] == 0): ?>
                <?php $rating_value_2 = $reviewRateData['rating']; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          <div class="sr_sitestoreproduct_profile_review_title" id="review-my-rating" style="margin-top:5px;">
            <?php echo $this->translate("My Rating"); ?>
          </div>	
          <div class="sr_sitestoreproduct_profile_review_stars">
            <?php echo $this->showRatingStarSitestoreproduct($rating_value_2, 'user', 'big-star'); ?>		     
          </div>
          <?php $tempClickHereText = $this->translate("click here"); ?>
          <?php if (!empty($this->reviewRateData) && !empty($this->hasPosted) && !empty($this->can_update)): ?>
            <div class="sr_sitestoreproduct_profile_review_stat mtop10">
              <?php echo $this->translate("Please %s to update your reviews for this product.", "<a href='javascript:void(0);' onclick='showForm();'>$tempClickHereText</a>"); ?>
            </div>	
          <?php endif; ?>
          <?php if (empty($this->reviewRateData) && empty($this->hasPosted) && !empty($this->create_level_allow)): ?>
            <div class="sr_sitestoreproduct_profile_review_stat">
              <?php echo $this->translate("Please %s to give your review and ratings for this product.", "<a href='javascript:void(0);' onclick='showForm();'>$tempClickHereText</a>"); ?>
            </div>	
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!--Rating Breakdown Hover Box Starts-->
      <div class="sr_sitestoreproduct_profile_review_right">
        <div class="sr_sitestoreproduct_rating_breakdowns">
          <div class="sr_sitestoreproduct_profile_review_title">
            <?php echo $this->translate("Ratings Breakdown"); ?>
          </div>
          <ul>
            <?php for ($i = 5; $i > 0; $i--): ?>
              <li>
                <div class="left"><?php echo $this->translate(array("%s star:", "%s stars:", $i), $i); ?></div>
                <?php $count = $this->subject()->getNumbersOfUserRating('user', 0, $i);
                $pr = $count ? ($count * 100 / $this->totalReviews) : 0; ?>
                <div class="count"><?php echo $count; ?></div>
                <div class="rate_bar b_medium">
                  <span style="width:<?php echo $pr; ?>%;" <?php echo empty($count) ? "class='sr_sitestoreproduct_border_none'" : "" ?>></span>
                </div>
              </li>
            <?php endfor; ?>
          </ul>
        </div>
        <div class="clr"></div>
      </div>
      <!--Rating Breakdown Hover Box Ends-->
    </div>
  <?php endif; ?>

  <?php if (!empty($this->totalReviews)): ?>
    <?php if (empty($this->isajax)) : ?>
      <!--User Over All Rating Block End -->
      <!--Filters Starts-->
      <?php if ($this->sitestoreproduct_proscons || ($this->paginator->getTotalItemCount() > 1)): ?>
        <div class="sr_sitestoreproduct_profile_userreview_filters b_medium" <?php if (!$this->sitestoreproduct_proscons): ?> style="border:none;"<?php endif; ?>>
          <?php if ($this->sitestoreproduct_proscons): ?>
            <ul section="show"> 
              <li class="filter_lebel">
                <span><b><?php echo $this->translate("Show:"); ?></b></span>
              </li> 
              <li class="onView" id="fullreviews"> 
                <a href="javascript:void(0);" onclick="showReviews('fullreviews')"><?php echo $this->translate("Full reviews"); ?></a> 
              </li> 
              <li id="prosreviews"> 
                <a href="javascript:void(0);" onclick="showReviews('prosonly')"><?php echo $this->translate("Pros Only"); ?></a> 
              </li> 
              <li id="consreviews"> 
                <a href="javascript:void(0);" onclick="showReviews('consonly')"><?php echo $this->translate("Cons Only"); ?></a>
              </li> 
            </ul>
          <?php endif ?>

          <?php if (($this->paginator->getTotalItemCount() > 1)): ?>
            <div class="sr_sitestoreproduct_profile_userreview_filters_options">
              <label><?php echo $this->translate("Sort By:"); ?></label>
              <select onchange="loadUserReviews('<?php echo $this->reviewOption ?>', this.value, '<?php echo $this->rating_value ?>', '<?php echo $this->current_page; ?>');" name="sortReviews" class="searchTarget"> 
                <option value="creationDate"><?php echo $this->translate("Most Recent"); ?></option>	
                <option value="helpful"><?php echo $this->translate("Most Helpful"); ?></option> 
                <option value="highestRating"><?php echo $this->translate("Highest Rating"); ?></option>  
                <option value="lowestRating"><?php echo $this->translate("Lowest Rating"); ?></option>
                <option value="featured"><?php echo $this->translate("Featured"); ?></option>
                <option value="recommend"><?php echo $this->translate("Recommended"); ?></option>
              </select>
            </div>
            <div class="sr_sitestoreproduct_profile_userreview_filters_options">
              <label><?php echo $this->translate("Rating:"); ?></label>
              <select onchange="loadUserReviews('<?php echo $this->reviewOption ?>', '<?php echo $this->reviewOrder ?>', this.value, '<?php echo $this->current_page; ?>');" name="sortReviewsByRating" class="searchTarget"> 
                <option value=""><?php echo $this->translate(""); ?></option> 
                <option value="5"><?php echo $this->translate("5 Stars"); ?></option>  
                <option value="4"><?php echo $this->translate("4 Stars"); ?></option> 
                <option value="3"><?php echo $this->translate("3 Stars"); ?></option>
                <option value="2"><?php echo $this->translate("2 Stars"); ?></option> 
                <option value="1"><?php echo $this->translate("1 Star"); ?></option>  
              </select>
            </div>
          <?php endif ?>
        </div>
      <?php endif ?>
      <!--Filters Ends-->
    <?php endif; ?>
  <?php endif; ?>

  <div id="sitestoreproduct_user_review_content">

    <?php if (( $this->paginator->getTotalItemCount() > 0)): ?>
      <div class="sr_sitestoreproduct_browse_lists_view_options b_medium">
        <div> 
          <?php echo $this->translate(array("%s review found.", "%s reviews found.", $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
        </div>
      </div>
      <ul class="sr_sitestoreproduct_reviews_product">
        <?php foreach ($this->paginator as $review): ?>
          <li class="b_medium">
            <?php if ($this->reviewOption == 'fullreviews'): ?>
              <div class="sr_sitestoreproduct_reviews_product_title">
                <?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->profileRatingbyCategory($review->review_id); ?>
                <?php $rating_value = 0; ?>
                <?php foreach ($ratingData as $reviewcat): ?>
                  <?php if (empty($reviewcat['ratingparam_name'])): ?>
                    <?php $rating_value = $reviewcat['rating'];
                    break; ?>
                  <?php endif; ?>
                <?php endforeach; ?>
                <?php $countR = count($ratingData); ?>
                <div class="sr_sitestoreproduct_ur_show_rating_star">
                  <span class="fright">
                    <span class="fleft">
                      <?php echo $this->showRatingStarSitestoreproduct($rating_value, 'user', 'big-star'); ?>
                    </span>
                    <?php if ($countR > 1): ?>
                      <i class="fright arrow_btm"></i>
                    <?php endif; ?>
                  </span>
                  <?php if ($countR > 1): ?>
                    <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                      <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">
                        <?php foreach ($ratingData as $reviewcat): ?>
                          <div class="o_hidden">
                            <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                              <div class="parameter_title">
                                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                              </div>
                              <div class="parameter_value">
                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                              </div>
                            <?php else: ?>
                              <div class="parameter_title">
                                <?php echo $this->translate("Overall Rating"); ?>
                              </div>	
                              <div class="parameter_value" style="margin: 0px 0px 5px;">
                                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['rating'], 'user', 'big-star'); ?>
                              </div>
                            <?php endif; ?> 
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                <?php if ($review->featured): ?>
                  <i class="sr_sitestoreproduct_icon seaocore_icon_featured fright" title="<?php echo $this->translate('Featured'); ?>"></i> 
                <?php endif; ?>	          	
                <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, 60), array('title' => $review->title)) ?>
              </div>
            <?php endif; ?>

            <?php if ($this->reviewOption == 'fullreviews'): ?>
              <div class="sr_sitestoreproduct_reviews_product_stat seaocore_txt_light">
                <?php echo date("F j, Y", strtotime($review->modified_date)); ?> - 
                <?php if (!empty($review->owner_id)): ?>
                  <?php echo $this->translate('by'); ?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
                <?php else: ?>
                  <?php echo $this->translate('by'); ?> <?php echo $review->anonymous_name; ?>
                <?php endif; ?>
                <?php if ($review->recommend): ?>
                  <span class="fright sr_sitestoreproduct_reviews_product_recommended">
                    <?php echo '<span>' . $this->translate('Recommended') . '</span>'; ?>  
                    <span class='sitestoreproduct_icon_tick sr_sitestoreproduct_icon'></span>
                  </span>	
                <?php endif; ?>
              </div>  
            <?php endif; ?>

            <?php if ($this->reviewOption == 'prosonly' || $this->reviewOption == 'fullreviews'): ?>
              <?php if ($review->pros): ?>
                <div class="sr_sitestoreproduct_reviews_product_proscons">
                  <b><?php echo $this->translate("Pros:") ?></b>
                  <?php echo $this->viewMore($review->pros) ?> 
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->reviewOption == 'consonly' || $this->reviewOption == 'fullreviews'): ?>
              <?php if ($review->pros): ?>
                <div class="sr_sitestoreproduct_reviews_product_proscons"> 
                  <b><?php echo $this->translate("Cons:") ?></b>
                  <?php echo $this->viewMore($review->cons) ?>
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($review->profile_type_review): ?>
              <div class="sr_sitestoreproduct_reviews_product_proscons"> 
                <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($review); ?>
                <?php $custom_field_values = $this->fieldValueLoopReviewSitestoreproduct($review, $fieldStructure); ?>
                <?php echo htmlspecialchars_decode($custom_field_values); ?>
              </div>
            <?php endif; ?>

            <?php if ($this->reviewOption == 'fullreviews'): ?>
              <?php if ($review->body): ?>
                <div class='sr_sitestoreproduct_reviews_product_proscons'>
                  <b><?php echo $this->translate("Summary:") ?></b>
                  <?php
                  if (strlen($review->body) > 300) {
                    $read_complete_review = $this->htmlLink($review->getHref(), $this->translate('Read complete review'), array('title' => ''));
                    $truncation_limit = 300;
                    $tmpBody = strip_tags($review->body);
                    $item_body = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... $read_complete_review" : $tmpBody );
                  } else {
                    $item_body = $review->body;
                  }
                  ?>
                  <?php echo $item_body; ?>
                </div>
              <?php endif; ?>
              <?php $this->reviewDescriptions = $reviewDescriptionsTable->getReviewDescriptions($review->review_id); ?>
              <?php if (count($this->reviewDescriptions) > 0): ?>
                <div class="sitestoreproduct_profile_info_des_update sr_sitestoreproduct_review_block">        
                  <?php foreach ($this->reviewDescriptions as $value) : ?>
                    <?php if ($value->body): ?>
                      <div class="b_medium">
                        <div class="sitestoreproduct_profile_info_des_update_date">
                          <?php echo $this->translate("Updated On %s", $this->timestamp(strtotime($value->modified_date))); ?>
                        </div>
                        <?php
                        if (strlen($value->body) > 300) {
                          $read_complete_review = $this->htmlLink($review->getHref(), $this->translate('Read complete review'), array('title' => ''));
                          $truncation_limit = 300;
                          $tmpBody = strip_tags($value->body);
                          $value_body = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . "... $read_complete_review" : $tmpBody );
                        } else {
                          $value_body = $value->body;
                        }
                        ?>
                        <div>
                          <?php echo $value_body; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div> 
              <?php endif; ?> 
            <?php endif; ?> 

            <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_formReplyReview.tpl'; ?> 
          </li>
        <?php endforeach; ?>
        <?php if ($this->paginator->count() > 1): ?>
          <div class="o_hidden mtop10">
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
              <div class="paginator_previous">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => 'paginateSitestoreReview(sitestoreReviewPage - 1)',
                    'class' => 'buttonlink icon_previous'
                ));
                ?>
              </div>
            <?php endif; ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
              <div class="paginator_next">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => 'paginateSitestoreReview(sitestoreReviewPage + 1)',
                    'class' => 'buttonlink_right icon_next'
                ));
                ?>
              </div>
            <?php endif; ?>
          </div>	
        <?php endif; ?> 
      </ul>

    <?php elseif (!empty($this->rating_value)): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has written a review with that criteria.'); ?> 
        </span>
      </div>    
    <?php else: ?>
      <?php if (!$this->can_create): ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("No reviews have been written for this Product yet."); ?>	
            <?php if (empty($this->viewer_id) && empty($this->create_level_allow)): ?>
              <?php
              $show_link = $this->htmlLink(
                      array('action' => 'create', 'route' => "sitestoreproduct_review_general", 'product_id' => $this->product_id), $this->translate('here'));
              echo sprintf(Zend_Registry::get('Zend_Translate')->_('Click %s to write a review.'), $show_link);
              ?>
            <?php endif; ?>
          </span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div id="sr_sitestoreproduct_user_review_form_wapper">
    <?php if (empty($this->isajax) && $this->hasPosted && $this->can_update): ?>
      <?php echo $this->update_form->setAttrib('class', 'sr_sitestoreproduct_review_form global_form')->render($this) ?>
      <?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_formUpdateReview.tpl'; ?>
    <?php endif; ?>

    <?php if (empty($this->isajax) && (!$this->hasPosted && $this->can_create)): ?>
      <?php echo $this->form->setAttrib('class', 'sr_sitestoreproduct_review_form global_form')->render($this) ?>
    <?php endif; ?>

    <?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_formCreateReview.tpl'; ?>
  </div>

  <script type="text/javascript">
		en4.core.runonce.add(function() {
			<?php if (count($this->ratingDataTopbox) > 1): ?>
				$$('.sr_sitestoreproduct_profile_review_rating').addEvents({
					'mouseover': function(event) {
						document.getElements('.sr_sitestoreproduct_ur_bdown_box_wrapper').setStyle('display','block');
					},
					'mouseleave': function(event) {    
						document.getElements('.sr_sitestoreproduct_ur_bdown_box_wrapper').setStyle('display','none');
					}});
				$$('.sr_sitestoreproduct_ur_bdown_box_wrapper').addEvents({
					'mouseenter': function(event) {
						document.getElements('.sr_sitestoreproduct_ur_bdown_box_wrapper').setStyle('display','block');
					},
					'mouseleave': function(event) {
						document.getElements('.sr_sitestoreproduct_ur_bdown_box_wrapper').setStyle('display','none');
					}});
			<?php endif; ?> 
		});

		function showReviews(option) {
			document.getElementById('consreviews').className=""; 
			document.getElementById('fullreviews').className=""; 
			document.getElementById('prosreviews').className=""; 
			if(option == "prosonly") {
				document.getElementById('prosreviews').className="onView"; 
			} else if(option == "consonly") {
				document.getElementById('consreviews').className="onView"; 
			} else if(option == "fullreviews") {
				document.getElementById('fullreviews').className="onView";
			}  
			loadUserReviews(option, '<?php echo $this->reviewOrder ?>', '<?php echo $this->rating_value ?>', '<?php echo $this->current_page; ?>');
		}

		var sitestoreReviewPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		var paginateSitestoreReview = function(page) {
			loadUserReviews('<?php echo $this->reviewOption ?>', '<?php echo $this->reviewOrder ?>', '<?php echo $this->rating_value ?>',page);
		}

		function loadUserReviews(option, order, rating, page) {
			$('sitestoreproduct_user_review_content').innerHTML = '<div class="sr_sitestoreproduct_profile_loading_image"></div>';
			var url = en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/user-sitestoreproduct';
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'option' : option, 
					'rating_value' : rating,
					'page': page,
					'order' : order,
					'isajax' : '1',
					'product_id': '<?php echo $this->product_id; ?>',
					'itemProsConsCount': '<?php echo $this->itemProsConsCount ?>',
					'itemReviewsCount' : '<?php echo $this->itemReviewsCount ?>'
				}
			}), {
				'element' : $('sitestoreproduct_user_review_content')
			});
		}
	</script>
<?php endif; ?>