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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
  <?php foreach ($this->paginator as $review): ?>
    <?php  $this->partial()->setObjectKey('review');
			echo $this->partial('application/modules/Sitestorereview/views/scripts/partialWidget.tpl', $review);
		?>
          <?php echo $this->translate(array('%s like', '%s likes', $review->like_count), $this->locale()->toNumber($review->like_count)) ?> |
          <?php echo $this->translate(array('%s view', '%s views', $review->view_count), $this->locale()->toNumber($review->view_count)) ?>
        </div>

        <div class='sitestore_sidebar_list_details'>
          <span title="<?php echo $review->rating . $this->translate(' rating'); ?>">
            <?php if (($review->rating > 0)): ?>
              <?php for ($x = 1; $x <= $review->rating; $x++): ?>
                <span class="rating_star_generic rating_star" /></span>
              <?php endfor; ?>
              <?php if ((round($review->rating) - $review->rating) > 0): ?>
                <span class="rating_star_generic rating_star_half" /></span>
              <?php endif; ?>
            <?php endif; ?>
          </span>
        </div> 
      </div>
    </li>
  <?php endforeach; ?>
</ul>