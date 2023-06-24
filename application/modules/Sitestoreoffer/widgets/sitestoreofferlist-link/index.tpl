<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<div class="quicklinks" style="margin-bottom:15px;">
	<ul>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_browse'), $this->translate('Browse Coupons'), array(
												'class' => 'buttonlink item_icon_sitestoreoffer'
			)) ?>
		</li>
	</ul>
</div>		