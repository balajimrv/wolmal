<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: preview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
?>

<div class="sitestore_offer_priview_popup">      
	<div class="sitestore_offer_priview_popup_head">
		<b><?php echo $this->translate('Preview') ?></b>
	</div>
	<div class="sitestore_offer_block">
		<div class="sitestore_offer_photo">
			<img class='thumb_normal'src='<?php echo $this->image_path;?>' alt='' />
		</div>
		<div class="sitestore_offer_details">
			<div id='title' class="sitestore_offer_title"></div>
      <div id='start_date' class="sitestore_offer_stats"></div>
      <div id='date' class="sitestore_offer_stats"></div>
      <div id="minimum_purchase" class="sitestore_offer_stats"></div>
      <span id='coupon_code' class="sitestore_offer_stats"></span>
      <span id="discount_amount" class="sitestore_offer_stats"></span>
	    <span id="claim" class="sitestorecoupon_stat sitestorecoupon_left fright"></span>		
      <div id="description" class="sitestore_offer_stats"></div>
		</div>		
	</div>
	<button onclick="parent.window.post()" type="submit_button"><?php echo $this->translate("Create");?></button>&nbsp;&nbsp;<?php echo $this->translate('or');?>&nbsp;
	<a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('Edit'); ?></a>
</div>	

<script type="text/javascript">
  
  var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
  var start_date = parent.window.$('calendar_output_span_start_time-date').innerHTML;
  if( seao_dateFormat == 'dmy' ) {
      start_date = new Date(en4.seaocore.covertdateDmyToMdy(start_date));
  }else{
      start_date = new Date(start_date);
  }
  var start_day = start_date.getDate();
  var start_month = ["January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"][start_date.getMonth()];
  var start_str = start_day + ' ' + start_month + ' ' + start_date.getFullYear();
  $('start_date').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Start date:'));?>' + ' ' + start_str ;
  
  var mydate = parent.window.$('calendar_output_span_end_time-date').innerHTML;
  if( seao_dateFormat == 'dmy' ) {
      mydate = new Date(en4.seaocore.covertdateDmyToMdy(mydate));
  }else{
      mydate = new Date(mydate);
  }
  
  var day = mydate.getDate();

	var month = ["January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"][mydate.getMonth()];
	var str = day + ' ' + month + ' ' + mydate.getFullYear();
  var url = parent.window.$('url').value;
  var myTruncatedUrl = url.substring(0,30);
  var end_date = parent.window.$('end_settings-0').checked;
  var final_url = '<a href = "' + url + '" target="_blank" title= "' + url + '" >' +  myTruncatedUrl + '</a>';

  if(parent.window.$('url').value) {
		if (end_date) {
			$('date').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('End date: Never Expires'));?>' + ' ' + '|' + ' ' + 'URL:' + ' ' + final_url;
		}
		else{
			$('date').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('End date:'));?>' + ' ' + str + ' ' + '|' + ' ' + 'URL:' + ' ' +final_url;
		}
  }
  else {
		if(end_date) {
			$('date').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('End date: Never Expires'));?>';
		}
		else {
			$('date').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('End date:'));?>' + ' ' + str;
		}
  }
  
  $('title').innerHTML = '<h3>' + parent.window.$('title').value + '</h3>';
  if(parent.window.$('claim_count').value == 0) {
    $('claim').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Unlimited Use'));?>';
  }
  else {
    if(parent.window.$('claim_count').value == 1) {
			$('claim').innerHTML = parent.window.$('claim_count').value + ' ' + '<?php echo $this->string()->escapeJavascript($this->translate('coupon Left'));?>';
     }
    else {   
      $('claim').innerHTML = parent.window.$('claim_count').value + ' ' + '<?php echo $this->string()->escapeJavascript($this->translate(' coupons Left'));?>';
    }
  }

  var coupon_code = parent.window.$('coupon_code').value;
  if(coupon_code != '') {
    $('coupon_code').innerHTML = '<span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper"><span class="sitestorecoupon_tip"><span><?php echo $this->translate("Select and Copy Code to use");?></span><i></i></span><input type="text" value="'+ parent.window.$('coupon_code').value +'" class="sitestorecoupon_code_num" onclick="this.select()" readonly></span>';
  }
  
  var discount_type = parent.window.$('discount_type').value;
  var discount_amount  = ' <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper"><span class="sitestorecoupon_tip"><span><?php echo $this->translate("Coupon Discount Value");?></span><i></i></span><span class="discount_value"><?php echo $this->discount_amount . '%';?></span>&nbsp;&nbsp;&nbsp</span>';
		if(discount_type == 1)
			{
				var discount = '<?php echo Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($this->discount_amount);?>';
				$('discount_amount').innerHTML = '<span class="discount_value">' + ' ' + discount + '</span>';
		}
		else{
			$('discount_amount').innerHTML = '<span class="discount_value">' + ' ' + discount_amount + '</span>';
			}
    
    var minimum_purchase = parent.window.$('minimum_purchase').value;
    if(minimum_purchase != 0 &&  minimum_purchase !== '')
      {
//       minimum_purchase = '<?php //echo Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($this->minimum_purchase);?>';
        $('minimum_purchase').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Minimum Purchase:'));?>'+ ' ' + minimum_purchase;
      }
      else{
        $('minimum_purchase').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Minimum Purchase : Applicable for all amounts of purchase'));?>';
      }
		var description = parent.window.$('description').value;
			  $('description').innerHTML = description;
</script>