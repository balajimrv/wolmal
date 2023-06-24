<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: utility.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-video/_navigationAdmin.tpl'; ?>

<h3>
	<?php echo $this->translate('Review Video Utilities'); ?>
</h3>
<p>
	<?php echo $this->translate("This page contains utilities to help configure and troubleshoot the videos of this plugin.") ?>
</p>
<br/>

<div class="settings">
	<form>
		<div>
			<h3><?php echo $this->translate("Ffmpeg Version") ?></h3>
			<p class="form-description"><?php echo $this->translate("This will display the current installed version of ffmpeg.") ?></p>
			<textarea><?php echo $this->version; ?></textarea><br/><br/><br/>

			<h3><?php echo $this->translate("Supported Video Formats") ?></h3>
			<p class="form-description"><?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this product for more info.') ?></p>
			<textarea><?php echo $this->format; ?></textarea><br/><br/>
			<?php if (TRUE): ?>
			<?php else: ?>
			<?php endif; ?>
		</div>
	</form>
</div>