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

<ul class="seaocore_sidebar_list">
  <li>
    <?php echo $this->htmlLink($this->review->getOwner($this->review->type), $this->itemPhoto($this->review->getOwner(), 'thumb.icon')) ?>
    <div class='seaocore_sidebar_list_info'>
    	<div class="seaocore_sidebar_list_title">
      	<?php echo $this->htmlLink($this->review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->getTitle(),20), array('title' => $this->review->getTitle())) ?>
      </div>	
      <div class="seaocore_sidebar_list_details">
      	<?php echo $this->translate(" on "); ?>
      	<?php echo $this->htmlLink($this->review->getParent()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->getParent()->getTitle(), 20), array('title' => $this->review->getParent()->getTitle())) ?>
      </div>
	    <div class='seaocore_sidebar_list_details'>  
	      <?php echo $this->showRatingStarSitestoreproduct($this->overallRating, $this->review->type, 'small-star'); ?>
	    </div>	
    </div>  

    
		<div class="clr sr_sitestoreproduct_review_quotes">
			<b class="c-l fleft"></b>
			<?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->getDescription(), 100) ?>
			<b class="c-r fright"></b>
		</div>    
  </li>
  
  <li class="sitestore_sidebar_list_seeall">
    <?php echo $this->htmlLink($this->review->getHref(), $this->translate('More &raquo;'), array('class' => 'more_link'));?>
  </li>
  
</ul>
