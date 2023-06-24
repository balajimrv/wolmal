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
<div class="quicklinks layout_sitestorecontent_link">
	<ul>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitestorereview_browse'), $this->translate('Browse Reviews Link'), array(
												'class' => 'buttonlink icon_sitestores_review'
			)) ?>
		</li>
	</ul>
</div>		