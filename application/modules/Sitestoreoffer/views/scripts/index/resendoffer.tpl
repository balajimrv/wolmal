<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: resendoffer.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
<?php $email =  Engine_Api::_()->user()->getViewer()->email;?>
<div class="global_form_popup">
	<h4><?php echo $this->translate("Email Coupon");?></h4>
  <?php $email = "<b>$email</b>";?>
	<div class="clr" style="overflow:hidden;">
		<div style="padding-top:5px;">
			<?php echo $this->translate("If you want us to email this coupon at your Email Id %s, please click on Email Coupon Button below.", $email);?>
		</div>
	</div>	
	<div class="clr" style="margin-top:10px;">
		<button onclick="resendoffer('<?php echo $this->offer_id;?>')" type="button" id="resend" name="resend" style="margin-right:10px;"><?php echo $this->translate('Email Coupon'); ?></button>
    <?php echo $this->translate(" or "); ?> 
    <a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" id="cancel" name="cancel"><?php echo $this->translate('Cancel'); ?></a>
	</div>	
</div>

<script type="text/javascript" >
  function resendoffer(offer_id) {  
    var url = en4.core.baseUrl + 'sitestoreoffer/index/sendoffer/id/'+ offer_id;
    window.location = url;
  }
</script>