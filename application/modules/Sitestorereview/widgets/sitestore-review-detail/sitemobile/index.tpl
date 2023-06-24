<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="pr_view">
  <section class="sm-widget-block">
    <table class="sm-rating-table">
      <tbody>
    <?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($this->sitestorereview->review_id); ?>
    <?php foreach($ratingData as $reviewcat): ?>
          <tr valign="middle">
            <td class="rating-title">
        <?php if(!empty($reviewcat['reviewcat_name'])): ?>
            <?php echo $this->translate($reviewcat['reviewcat_name']); ?>
        <?php else:?>
        <strong> <?php echo $this->translate("Overall Rating");?></strong>
        <?php endif; ?>
        </td>
        <td>
        <?php if(!empty($reviewcat['reviewcat_name'])): ?>
          <?php 
            $showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcat['rating'], 'box');
            $rating_value = $showRatingImage['rating_value'];
          ?>
        <?php else:?>
          <?php
            $showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($reviewcat['rating'], 'star');
            $rating_value = $showRatingImage['rating_value'];
            $rating_valueTitle = $showRatingImage['rating_valueTitle'];
          ?>
        <?php endif; ?>
        <?php if(!empty($reviewcat['reviewcat_name'])): ?>
          <div class="review_cat_rating">
            <ul class='rating-box-small <?php echo $rating_value; ?>'>
              <li id="1" class="rate one">1</li>
              <li id="2" class="rate two">2</li>
              <li id="3" class="rate three">3</li>
              <li id="4" class="rate four">4</li>
              <li id="5" class="rate five">5</li>
            </ul>
          </div>
        <?php else:?>
          <div class="review_cat_rating">
            <ul title="<?php echo $rating_valueTitle.$this->translate(" rating"); ?>" class='show-rating <?php echo $rating_value; ?>'>
              <li id="1" class="rate one">1</li>
              <li id="2" class="rate two">2</li>
              <li id="3" class="rate three">3</li>
              <li id="4" class="rate four">4</li>
              <li id="5" class="rate five">5</li>
            </ul>
          </div>
        <?php endif;?>
        </td>
      </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
  </section>
  <section>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.proscons', 1)):?>
      <p>
        <strong><?php echo $this->translate("Pros: ")?></strong>
        <?php echo $this->viewMore($this->sitestorereview->pros); ?>
      </p>
      <p>
        <strong><?php echo $this->translate("Cons: ")?></strong>
        <?php echo $this->viewMore($this->sitestorereview->cons) ?>
      </p> 
    <?php endif;?>
      <p>
        <?php echo nl2br($this->sitestorereview->body) ?>
      </p>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>

      <?php if($this->sitestorereview->recommend):?>
      <p>
          <?php echo $this->translate("Member's Recommendation:"); ?>
          <strong><?php echo "Yes";?></strong>
        </p>
      <?php else: ?>
        <p>
          <?php echo $this->translate("Member's Recommendation:"); ?>
          <strong><?php echo "No";?></strong>
        </p>
      <?php endif;?>
    <?php endif;?>
  </section>
</div>
