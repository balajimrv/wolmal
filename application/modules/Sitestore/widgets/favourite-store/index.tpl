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
<ul class="sitestore_sidebar_list">
	<?php  foreach ($this->userListings as $sitestore): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id_for, $sitestore->owner_id,$sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.icon'),array('title' => $sitestore->getTitle())) ?>
			<div class='sitestore_sidebar_list_info'>
				<div class='sitestore_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id_for, $sitestore->owner_id,$sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle()), array('title' => $sitestore->getTitle())) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>