<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getoffer.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(!empty($this->private_message)):?>
	<div class="tip global_form_popup">
		<span>
			<?php echo $this->translate("You are not authorized to get this coupon."); ?>
		</span>
	</div>
  <?php return;?>
<?php endif;?>

<div class="global_form_popup">
<?php $email =  Engine_Api::_()->user()->getViewer()->email;?>
	<h4><?php echo $this->translate("Coupon Sent");?></h4>
	<div class="clr" style="overflow:hidden;">
		<div class="fleft" style="margin-right:10px;">
			<?php echo "<img src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/mail.png' alt='' class='fleft' />" ?>
		</div>
		<div style="padding-top:5px;">
			<?php $email = "<b>$email</b>";?>
      <?php $store_title = "<b>$this->store_title</b>";?>
			<?php echo $this->translate("We've sent an email to $email. To redeem your coupon, take the email to $store_title in print or in your phone and show it to the staff.");?>
		</div>
	</div>	
	<div class="clr" style="margin-top:10px;">
    <a href="#" data-rel="back" data-role="button">
          <?php echo $this->translate('Okay') ?>
    </a>
	</div>	
</div>