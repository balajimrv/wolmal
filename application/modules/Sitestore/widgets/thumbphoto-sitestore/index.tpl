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

<div class="sitestore_viewstores_head" id='thumb_icon' style="display:none;">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<?php if (!empty($this->showTitle )) : ?>
		<h2>	
			<?php echo $this->sitestore->getTitle(); ?>	
		</h2>  
  <?php endif ; ?>
</div>

