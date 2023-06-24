<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<li> 
	<?php
	echo $this->htmlLink(
					$this->sitestoreproduct_video->getHref(), $this->itemPhoto($this->sitestoreproduct_video, 'thumb.icon', $this->sitestoreproduct_video->getTitle()), array('class' => 'list_thumb', 'title' => $this->sitestoreproduct_video->getTitle())
	)
	?>
	<div class='seaocore_sidebar_list_info'>
		<div class='seaocore_sidebar_list_title'>
			<?php echo $this->htmlLink($this->sitestoreproduct_video->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->sitestoreproduct_video->getTitle(), 16), array('title' => $this->sitestoreproduct_video->getTitle(),'class'=>'sitestoreproduct_video_title')); ?> 	
		</div>
		<div class='seaocore_sidebar_list_details'>