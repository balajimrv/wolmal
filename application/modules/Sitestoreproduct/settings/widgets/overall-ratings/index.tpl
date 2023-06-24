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

<div class="sr_sitestoreproduct_up_overall_rating b_medium">
  <?php if ($this->editorReview && ($this->show_rating == 'both' || $this->show_rating == 'editor')): ?> 
    <div class="sr_sitestoreproduct_up_overall_rating_title o_hidden">
      <div class="fright"><?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_editor, 'editor', 'big-star'); ?></div>
			<div class="o_hidden"><?php echo $this->translate("Editor Rating") ?></div>
    </div>
    <?php if(count($this->ratingEditorData)>1 && $this->ratingParameter): ?>
      <div class="sr_sitestoreproduct_up_overall_rating_paramerers clr">
        <?php foreach ($this->ratingEditorData as $reviewcat): ?>
          <?php if (!empty($reviewcat['ratingparam_id'])): ?>
            <div class="o_hidden">
              <div class="parameter_count">&nbsp;
              </div>
              <div class="parameter_value">
                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box',$reviewcat['ratingparam_name']); ?>
              </div>

              <div class="parameter_title">
                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if($this->sitestoreproduct->rating_editor): ?>
      <div class="sr_sitestoreproduct_up_overall_rating_stat">
        <?php echo $this->translate(
      '%s contributed to this review on %1s.', $this->htmlLink($this->editorReview->getOwner('editor')->getHref(), $this->editorReview->getOwner('editor')->getTitle(), array('')),$this->timestamp(strtotime($this->editorReview->creation_date))) ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  
  <?php if ($this->show_rating == 'both' || $this->show_rating == 'avg'): ?>

    <?php if($this->show_rating == 'both'): ?>
      <?php if( $this->editorReview ) : ?>
      <div class="sr_sitestoreproduct_up_overall_rating_sep b_medium"></div>
      <?php endif; ?>
      
      <div class="sr_sitestoreproduct_up_overall_rating_title o_hidden">
        <div class="fright"><?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_users, 'user', 'big-star'); ?></div>
        <div class="o_hidden"><?php echo $this->translate('User Ratings') ?></div>
      </div>
      <div class="sr_sitestoreproduct_up_overall_rating_stat">
        <?php echo $this->translate(
    array('Based on %s review', 'Based on %s reviews', $this->subject()->getNumbersOfUserRating($this->type)), '<b>'.$this->locale()->toNumber($this->subject()->getNumbersOfUserRating($this->type)).'</b>') ?>
      </div>
    <?php else: ?>
      <div class="sr_sitestoreproduct_up_overall_rating_title o_hidden">
        <div class="fright"><?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_avg, 'overall', 'big-star'); ?></div>
        <?php if($this->reviewsAllowed == 2): ?>
          <div class="o_hidden"><?php echo $this->translate('Average User Rating') ?></div>
        <?php else: ?>
          <div class="o_hidden"><?php echo $this->translate('Average Rating') ?></div>
        <?php endif; ?>
      </div>
      <div class="sr_sitestoreproduct_up_overall_rating_stat">
        <?php echo $this->translate(
    array('Based on %s review', 'Based on %s reviews', $this->subject()->getNumbersOfUserRating($this->type)), '<b>'.$this->locale()->toNumber($this->subject()->getNumbersOfUserRating($this->type)).'</b>') ?>
      </div>
    <?php endif; ?>

    <?php if(count($this->ratingData)>1 && $this->ratingParameter): ?>
      <div class="sr_sitestoreproduct_up_overall_rating_paramerers clr">
        <?php foreach ($this->ratingData as $reviewcat): ?>
        <?php if (!empty($reviewcat['ratingparam_id'])): ?>
        <div class="o_hidden">
          <div class="parameter_count">
            <?php echo $this->subject()->getNumbersOfUserRating($this->type, $reviewcat['ratingparam_id']); ?> </div>
          <div class="parameter_value">
            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box',$reviewcat['ratingparam_name']); ?>
          </div>

          <div class="parameter_title">
            <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
          </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
      
    <?php if ($this->sitestoreproduct->rating_users): ?>
      <div class="sr_sitestoreproduct_up_overall_rating_title mtop10">
        <?php echo $this->translate("Recommendations") ?>
      </div>
      <div class="sr_sitestoreproduct_up_overall_rating_stat">
        <?php echo $this->translate("Recommended by %s users", '<b>' . $this->recommend_percentage . '%</b>'); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
