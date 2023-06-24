<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php 
	if( !empty($this->isModsSupport) ):
		foreach( $this->isModsSupport as $modName ) {
			echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Verify Plugin.", ucfirst($modName)) . "</span></div>";
		}
	endif;
?>

<h2><?php echo $this->translate("Members Verification Plugin"); ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
 <div class='seaocore_settings_form'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>

  </div>
</div>
