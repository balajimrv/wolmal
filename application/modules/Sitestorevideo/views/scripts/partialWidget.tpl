<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<li> 
	<?php
	echo $this->htmlLink(
					$this->sitestorevideo->getHref(), $this->itemPhoto($this->sitestorevideo, 'thumb.icon', $this->sitestorevideo->getTitle()), array('class' => 'list_thumb', 'title' => $this->sitestorevideo->getTitle())
	)
	?>
	<div class='sitestore_sidebar_list_info'>
		<div class='sitestore_sidebar_list_title'>
			<?php echo $this->htmlLink($this->sitestorevideo->getHref(), Engine_Api::_()->sitestorevideo()->truncation($this->sitestorevideo->getTitle()), array('title' => $this->sitestorevideo->getTitle(),'class'=>'sitestorevideo_title')); ?>
		</div>
		<div class='sitestore_sidebar_list_details'>