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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
	<?php foreach ($this->paginator as $sitestorevideo): ?>
    <?php  $this->partial()->setObjectKey('sitestorevideo');
        echo $this->partial('application/modules/Sitestorevideo/views/scripts/partialWidget.tpl', $sitestorevideo);
		?>	
          <?php
	          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
	          $tmpBody = strip_tags($sitestorevideo->store_title);
	          $store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestorevideo->store_id, $sitestorevideo->owner_id, $sitestorevideo->getSlug()), $store_title, array('title' => $sitestorevideo->store_title)) ?> 

          <?php if (($sitestorevideo->rating > 0)): ?>
           <div class="sitestore_sidebar_list_details">
							<?php
							$currentRatingValue = $sitestorevideo->rating;
							$difference = $currentRatingValue - (int) $currentRatingValue;
							if ($difference < .5) {
								$finalRatingValue = (int) $currentRatingValue;
							} else {
								$finalRatingValue = (int) $currentRatingValue + .5;
							}
							?>
            </div>
						
            <?php for ($x = 1; $x <= $sitestorevideo->rating; $x++): ?>
            	<span class="rating_star_generic rating_star sitestore_video_rate" title="<?php echo $finalRatingValue .' '. $this->translate('rating'); ?>"></span>
            <?php endfor; ?>
            <?php if ((round($sitestorevideo->rating) - $sitestorevideo->rating) > 0): ?>
            	<span class="rating_star_generic rating_star_generic_half sitestore_video_rate" title="<?php echo $finalRatingValue .' '. $this->translate('rating'); ?>"></span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>  
    </li>
  <?php endforeach; ?>
  <li class="sitestore_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('ratedvideo'=> 1), 'sitestorevideo_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>