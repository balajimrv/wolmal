<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partial_widget.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), $this->itemPhoto($this->sitestore, 'thumb.icon'), array('title' => $this->sitestore->getTitle())) ?>
	<div class='sitestore_sidebar_list_info'>
		<div class='sitestore_sidebar_list_title'>
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($this->sitestore->getTitle()), array('title' => $this->sitestore->getTitle())) ?>
		</div>
		<div class='sitestore_sidebar_list_details'>