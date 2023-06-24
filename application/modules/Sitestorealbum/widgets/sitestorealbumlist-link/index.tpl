<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<div class="quicklinks">
	<ul>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitestorealbum_browse'), $this->translate('Browse Albums'), array(
												'class' => 'buttonlink sitestore_icon_photos_manage'
			)) ?>
		</li>
	</ul>
</div>		