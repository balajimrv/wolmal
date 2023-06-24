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
	
<?php if(false):?>
	<?php if ($this->can_edit && $this->can_edit_overview):?>
		<?php if(!empty($this->sitestore->overview)):?>
			<div class="seaocore_add"  data-role="controlgroup" data-type="horizontal">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'action' => 'overview', 'tab' => $this->identity), 'sitestore_dashboard', true) ?>'  class="icon_sitestores_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
			</div>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>

<div>
	<?php if(!empty($this->sitestore->overview)):?>
		<?php echo $this->sitestore->overview ?>
	<?php else:?>
		<div class="tip">
			<span>
				<?php   echo $this->translate("No overview has been composed for this Store yet.");?>
			</span>
		</div>
	<?php endif;?>
</div>