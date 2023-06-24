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

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

	<?php if ($this->can_create == 1 && empty($this->is_manageadmin)): ?>
		<div class="seaocore_add" data-role="controlgroup" data-type="horizontal">
			<?php
			echo $this->htmlLink(array(
					'route' => 'sitestorereview_create',
					'store_id' => $this->store_id,
					'tab' => $this->identity,
							), $this->translate('Write a Review'), array(
					'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"));
			?>
		</div>
	<?php endif; ?>

<?php endif; ?>

<?php if(!empty($this->ratingData)):?>
  <section class="sm-widget-block">
    <table class="sm-rating-table">
      <?php foreach($this->ratingData as $rating):?>
        <?php if (!empty($rating['reviewcat_name'])): ?>
          <?php 
            $showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($rating['avg_rating'], 'box');
            $rating_value = $showRatingImage['rating_value'];
          ?>
          <tr valign="middle">
            <td class="rating-title">
              <?php echo $rating['reviewcat_name']; ?>
            </td>
            <td>
							<div class="review_cat_rating">
								<ul class='rating-box-small <?php echo $rating_value; ?>'>
									<li id="1" class="rate one">1</li>
									<li id="2" class="rate two">2</li>
									<li id="3" class="rate three">3</li>
									<li id="4" class="rate four">4</li>
									<li id="5" class="rate five">5</li>
								</ul>
							</div>
            </td>
          </tr>
        <?php else: ?>
				<?php
					$showRatingImage = Engine_Api::_()->sitestorereview()->showRatingImage($rating['avg_rating'], 'star');
					$rating_value = $showRatingImage['rating_value'];
					$rating_valueTitle = $showRatingImage['rating_valueTitle'];
				?>
        <tr valign="middle">
          <td class="rating-title">
            <b><?php echo $this->translate("Overall Rating"); ?></b>
          </td>
          <td>
						<div class="review_cat_rating">
							<ul title="<?php echo $rating_valueTitle.$this->translate(" rating"); ?>" class='show-rating <?php echo $rating_value; ?>'>
								<li id="1" class="rate one">1</li>
								<li id="2" class="rate two">2</li>
								<li id="3" class="rate three">3</li>
								<li id="4" class="rate four">4</li>
								<li id="5" class="rate five">5</li>
							</ul>
						</div>
          </td>
          </tr>
        <?php endif;?>
      <?php endforeach;?>

    <tr>
      <td colspan="2">
      <?php echo $this->translate(array('Total <b>%s</b> Review', 'Total <b>%s</b> Reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)) ?>
      </td>
    </tr>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.recommend', 1)):?>
    <tr>
      <td colspan="2">
        <?php echo $this->translate("Recommended by ") .'<b>' .$this->recommend_percentage .'%</b>'. $this->translate(" members");?>
      </td>
    </tr>
    <?php endif;?>
    </table>
  </section>
<?php endif;?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list" id="profile_sitestorereviews">
     <ul data-role="listview" data-inset="false">
			<?php foreach ($this->paginator as $review): ?>
				<li>
					<a href='<?php echo $review->getHref(); ?>'>  
						<h3><?php echo Engine_Api::_()->sitestorereview()->truncateText($review->title, 60) ?></h3>
            <p>
							<?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->profileRatingbyCategory($review->review_id); ?>
							<?php //foreach ($ratingData as $reviewcat): ?>
								<?php if( $ratingData[0]['rating'] > 0 ): ?>
									<?php for( $x=1; $x<=$ratingData[0]['rating']; $x++ ): ?>
										<span class="rating_star_generic rating_star"></span>
									<?php endfor; ?>
									<?php if( (round( $ratingData[0]['rating']) - $ratingData[0]['rating']) > 0): ?>
										<span class="rating_star_generic rating_star_half"></span>
									<?php endif; ?>
								<?php endif; ?>
							<?php //endforeach; ?>
            </p>
						<p>
							<?php echo $this->translate('Posted by') ?>
							<strong><?php echo $review->getOwner()->getTitle(); ?></strong>
							-
							<?php echo $this->timestamp(strtotime($review->modified_date)) ?>
            </p>
					</a>
        </li>
			<?php endforeach; ?>
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, "profile_sitestorereviews");
			?>
		<?php endif; ?>
	</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No reviews have been posted for this Store yet.'); ?>
      <?php if ($this->can_create == 1 && empty($this->is_manageadmin)): ?>
        <?php
        $show_link = $this->htmlLink(
                array('route' => 'sitestorereview_create', 'store_id' => $this->store_id, 'tab' => $this->identity), $this->translate('here'));
        $show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to write a review.');
        $show_label = sprintf($show_label, $show_link);
        echo $show_label;
        ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>