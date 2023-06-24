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

<div class='sr_sitestoreproduct_profile_cover_photo_wrapper'>
	<div class="sr_sitestoreproduct_profile_cover_photo">
		<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
	</div>
	<div class="sr_sitestoreproduct_profile_cover_name">
  	<?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
  </div>
</div>