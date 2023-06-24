<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sendoffer.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php $email =  Engine_Api::_()->user()->getViewer()->email;?>
	<h4><?php echo $this->translate("Coupon Emailed");?></h4>
	<div class="clr" style="overflow:hidden;">
		<div class="fleft" style="margin-right:10px;">
			<?php echo "<img src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/mail.png' alt='' class='fleft' />" ?>
		</div>
		<div style="padding-top:5px;">
			<?php $email = "<b>$email</b>";?>
			<?php echo $this->translate("We’ve emailed you this coupon at $email. Please check spam folder if you do not see the coupon in your inbox.");?>
		</div>
	</div>	
	<div class="clr" style="margin-top:10px;">
		<button class="fright" onclick="javascript:window.parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('Okay'); ?></button>
	</div>	
</div>