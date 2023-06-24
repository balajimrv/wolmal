<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _sitestorereview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$currentRatingValue = $this->rating;
$difference = $currentRatingValue - (int) $currentRatingValue;
if ($difference < .5) {
  $finalRatingValue = (int) $currentRatingValue;
} else {
  $finalRatingValue = (int) $currentRatingValue + .5;
}
?>
<div>
  <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>" style="margin-right:5px;float:left;">
    <?php for ($x = 1; $x <= $this->rating; $x++): ?>
      <span class="rating_star_generic rating_star" ></span>
    <?php endfor; ?>
    <?php if ((round($this->rating) - $this->rating) > 0): ?>
      <span class="rating_star_generic rating_star_half" ></span>
    <?php endif; ?>
  </span>
</div>