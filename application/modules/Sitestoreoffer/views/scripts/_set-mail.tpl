<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _set-mail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(!$this->enable_mailtemplate):?>
	<table border="0" cellpadding="10" cellspacing="0"><tbody><tr><td bgcolor="#f7f7f7"><table border="0" cellpadding="0" cellspacing="0" align="center" style="width:600px"><tbody><tr><td align="left" style="background-color:#79b4d4;padding:10px;font-family:tahoma,verdana,arial,sans-serif;vertical-align:middle;font-size:17px;font-weight:bold;color:#fff;"><?php echo $this->site_title;?></td></tr><tr><td colspan="0" style="font-family:tahoma,verdana,arial,sans-serif;padding:10px;border:1px solid #cccccc;" valign="top">
<?php endif;?>

<table cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-top:10px" width="100%"><tbody><tr><td valign="top" style="padding-right:10px;font-size:0px;width:50px;"><img src='<?php echo $this->store_photo_path;?>' /></td><td valign="top">	<table cellspacing="0" cellpadding="0" style="border-collapse:collapse;" width="100%"><tbody><tr><td style="font-size:13px;font-family:tahoma,verdana,arial,sans-serif;font-weight:bold;color:#3b5998;margin-bottom:10px;"><?php echo $this->store_title;?></td></tr><tr><td style="font-size:11px;font-family:'lucida grande',tahoma,verdana,arial,sans-serif"><div style="padding:10px;overflow:hidden;"><div style="float:left;margin-right:10px;"><img src='<?php echo $this->offer_photo_path;?>' /></div><div style="overflow:hidden;font-family:tahoma,verdana,arial,sans-serif;"><div style="font-weight:bold;font-size:13px;margin-bottom:5px;"><?php echo $this->offer_title;?></div><div style="margin-bottom:5px;"><?php if($this->offer_time_setting):?><?php echo $this->translate('Expires: ').$this->offer_time;?><?php else:?><?php echo $this->translate('Expires: Never Expires');?><?php endif;?><?php if(!empty($this->offer_url)):?><?php echo ' | '.'URL: '.$this->offer_url?><?php endif;?></div><div style="margin-bottom:5px;"><?php if($this->coupon_code):?><?php echo $this->translate('Coupon Code: ').$this->coupon_code;?><?php endif;?></div><div style="margin-bottom:5px;"><?php if($this->discount_amount):?><?php echo $this->translate('Discount Amount: ').$this->discount_amount;?><?php endif;?></div><div style="margin-bottom:10px;"><?php echo $this->translate('Used by: ').$this->claim_owner_name;?></div><div style="margin-bottom:10px;"><?php echo $this->translate('Order Id: ').$this->order_no;?></div></div></div></td></tr><tr><td style="font-size:11px;font-family:tahoma,verdana,arial,sans-serif;"></td></tr><tr><td style="font-size:11px;font-family:tahoma,verdana,arial,sans-serif;padding:10px 0 20px 0;color:gray;"></td></tr></tbody></table></td></tr></tbody></table>

<?php if(!$this->enable_mailtemplate):?>
	</td></tr></tbody></table></td></tr></tbody></table>          
<?php endif;?>