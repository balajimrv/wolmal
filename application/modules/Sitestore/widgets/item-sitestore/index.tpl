<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_browse_sitestore_day">
	<li>
		<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->dayitem->store_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->itemPhoto($this->dayitem, 'thumb.profile')) ?>
		<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->dayitem->store_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->dayitem->getTitle(), array('title' => $this->dayitem->getTitle())) ?>
	</li>
</ul>