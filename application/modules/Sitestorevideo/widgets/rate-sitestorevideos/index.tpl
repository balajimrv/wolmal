<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitestore_sidebar_list">
	<?php foreach ($this->paginator as $sitestorevideo): ?>
    <?php  $this->partial()->setObjectKey('sitestorevideo');
        echo $this->partial('application/modules/Sitestorevideo/views/scripts/partialWidget.tpl', $sitestorevideo);
		?>	
          <?php if (($sitestorevideo->rating > 0)): ?>

            <?php
            $currentRatingValue = $sitestorevideo->rating;
            $difference = $currentRatingValue - (int) $currentRatingValue;
            if ($difference < .5) {
              $finalRatingValue = (int) $currentRatingValue;
            } else {
              $finalRatingValue = (int) $currentRatingValue + .5;
            }
            ?>

            <?php for ($x = 1; $x <= $sitestorevideo->rating; $x++): ?><span class="rating_star_big_generic rating_star sitestore_video_rate" title="<?php echo $finalRatingValue ?> <?php echo $this->translate('rating'); ?>"></span><?php endfor; ?><?php if ((round($sitestorevideo->rating) - $sitestorevideo->rating) > 0): ?><span class="rating_star_big_generic rating_star_half sitestore_video_rate" title="<?php echo $finalRatingValue . $this->translate('rating'); ?>"></span><?php endif; ?>
          <?php endif; ?>
        </div>
      </div>  
    </li>
  <?php endforeach; ?>
</ul>