<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
if(empty($this->coutErrorMessage)):
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>

<?php 
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/scripts/ajaxupload.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
			. 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<?php $this->offer_store = 0;?>
<?php if(empty($this->offer_store)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="sitestore_viewstores_head">
		<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
    <?php if(!empty($this->can_edit) && empty($this->offer_store)):?>
      <div class="fright">
				<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
      </div>
	  <?php endif;?>
		<h2>	
			<?php echo $this->sitestore->__toString() ?>	
			<?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Coupons')) ?>
		</h2>
	</div>
<?php endif;?>
<div class="clr">
  <?php echo $this->form->render($this) ?>
</div>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>

<script type="text/javascript">
var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
  
// START CALENDAR WORK FOR COUPON START- END DATE
  en4.core.runonce.add(function()
  {
    initializeCalendarDate(seao_dateFormat, cal_start_time, cal_end_time, 'start_time', 'end_time');
    cal_start_time_onHideStart();
  });
  
  var cal_start_time_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_time, cal_end_time, 'start_time', 'end_time');
  };
// END CALENDAR WORK FOR COUPON START- END DATE

  var maxRecipients = 10000;
  en4.core.runonce.add(function() {

//    var productType = $('product_type').value;
//  if((productType == 'bundled' ) || productType == 'grouped'){
		productidsAutocomplete = new SEAOAutocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts', 'store_id' => $this->sitestore->store_id), 'default', true); ?>', {
			'postVar' : 'search',
         'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'product_ids',
			'autocompleteType': 'message',
			'multiple': true,
			'className': 'tag-autosuggest seaocore-autosuggest',
			'filterSubset' : true,
			'tokenFormat' : 'object',
			'tokenValueKey' : 'label',
			'injectChoice': function(token) {
				var choice = new Element('li', {
					'class': 'autocompleter-choices',
					'html': token.photo,
					'id':token.label
				});

				new Element('div', {
					'html': this.markQueryValue(token.label),
					'class': 'autocompleter-choice'
				}).inject(choice);
        
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
            
			},
			onPush : function() {
//   var test = choice.label;alert('hi = '+ test);
				if ($('product_ids-wrapper')) {
					$('product_ids-wrapper').style.display='block';
				}

//				if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
//					this.element.disabled = true;
//				}
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value}
        });
			}
		});
    //}
//    if(productType == 'bundled')
//        bundleProductTypes();
    });
  //<!--
window.addEvent('domready', function() {
    var url_enable = '<?php echo $this->enable_url;?>'; 
    if(url_enable != 0)
        $('url-wrapper').style.display = 'block';
    else
        $('url-wrapper').style.display = 'none';
    var pageurlcontainer = $('coupon_code-element');
    var language = '<?php echo $this->translate($this->string()->escapeJavascript("Check Availability")) ?>';
    var newdiv = document.createElement('div');
    newdiv.id = 'coupon_code_varify';
    newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";
    pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[3]);
    });
    
    if($('discount_type')) {
		$('discount_type').addEvent('change', function(){
				if($('discount_type').value == 1)
          {
            document.getElementById('price-wrapper').style.display = 'block';
            document.getElementById('rate-wrapper').style.display = 'none';
          }
          else{
            document.getElementById('price-wrapper').style.display = 'none';
            document.getElementById('rate-wrapper').style.display = 'block';
          }
		});
		window.addEvent('domready', function() {
			if($('discount_type').value == 1)
          {
            document.getElementById('price-wrapper').style.display = 'block';
            document.getElementById('rate-wrapper').style.display = 'none';
          }
          else{
            document.getElementById('price-wrapper').style.display = 'none';
            document.getElementById('rate-wrapper').style.display = 'block';
          }
		});
	}
        
   function PageUrlBlur(post) {
			
      //var returnvalue = true;
      if ($('coupon_code_alert') == null) {
				var pageurlcontainer = $('coupon_code-element');
				var newdiv = document.createElement('span');
				newdiv.id = 'coupon_code_alert';
        if('undefined' === typeof post){
				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[4]);
       }
       else{
         newdiv.innerHTML = '';
				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[4]);
       }
			}
			else {
      if('undefined' === typeof post)
				$('coupon_code_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
      else
        $('coupon_code_alert').innerHTML = '';
    }
			var url = '<?php echo $this->url(array('action' => 'coupon-url-validation' ), 'sitestoreoffer_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					coupon_code : $('coupon_code').value,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					if (responseJSON.success == 0) {
             if('undefined' === typeof post){
						if ($('coupon_code_alert'))
							$('coupon_code_alert').innerHTML = responseJSON.error_msg;
            }
            else{
						$('coupon_code_alert').innerHTML = responseJSON.error_msg;
						if ($('coupon_code_alert')) {
							$('coupon_code_alert').innerHTML = responseJSON.error_msg;
						}
            showdetail(false);
            }
					}
					else{
            if('undefined' === typeof post){
						if ($('coupon_code_alert'))
							$('coupon_code_alert').innerHTML = responseJSON.success_msg;
            }
            else{
						if ($('coupon_code_alert'))
							$('coupon_code_alert').innerHTML = '';
              showdetail(true);
            }
            
					}
				}
		}));
	}
        
  function post () {
	document.getElementById('submit_form').submit();
  }

  function imageupload () {

    $('imageName').value='';
		$('imageenable').value=0;
		$m('photo').innerHTML='';
		$m('photo_id_filepath').value='';
		
		if($('validation_image')){
			document.getElementById("image-element").removeChild($('validation_image'));
		}
		form = $m('submit_form');

		var  url_action= '<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index', 'action' => 'upload'), 'default', true) ?>';

		ajaxUpload(form,
		url_action,'photo',
		'<center><img src=\"<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreoffer/externals/images/loader.gif\" border=\'0\' />','');
		
		$m("loading_image").style.display="block";
		$m("loading_image").innerHTML='<img src=\"<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreoffer/externals/images/loader.gif\" border=\'0\' /> ' + '<?php echo $this->string()->escapeJavascript($this->translate("Uploading image...")) ?>';
		
		$m('photo').style.visibility="Hidden";
  }

  function showdetail(return_value) {
    var validationFlage = 0;
		if ($('title').value == '')
		{
			if(!$('validation_offer_description')){
				var div_title_name = document.getElementById("title-element");
        if($('validation_offer_title'))
          $('validation_offer_title').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a title.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_offer_title";
				div_title_name.appendChild(myElement);
				validationFlage=1;
			}
		}
    else{
        if($('validation_offer_title'))
          $('validation_offer_title').innerHTML = '';
    }

//		var str = $('coupon_code').value;
//    if(!$('coupon_code').value){
//    var div_title_name = document.getElementById("coupon_code-element");
//					var myElement = new Element("p");
//					myElement.innerHTML = '<?php //echo $this->string()->escapeJavascript($this->translate("* Please enter a valid coupon code.")) ?>';
//					myElement.addClass("error");
//					myElement.id = "validation_offer_code";
//					div_title_name.appendChild(myElement);
//					validationFlage=1;
//    }else{
//     var coupon_error = setTimeout('PageUrlBlur(1)', 100);
//     alert('manisha' + coupon_error);
//     if(coupon_error == 0)
//       return false;
//    }

		if ($('description').value == '')
		{
			if(!$('validation_offer_description')){
				var div_description_name = document.getElementById("description-element");
        if($('validation_offer_description'))
            $('validation_offer_description').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter description.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_offer_description";
				div_description_name.appendChild(myElement);
				validationFlage=1;
			}
		}
    else{
        if($('validation_offer_description'))
            $('validation_offer_description').innerHTML = '';
    }
    
    if($('discount_type').value == 1)
        var discount_amount = $('price').value;
    else
        var discount_amount = $('rate').value;
    
    if(discount_amount == '' || discount_amount == 0)
    {
        if($('discount_type').value == 1)
              var discount_error_name = document.getElementById("price-element");
        else
              var discount_error_name = document.getElementById("rate-element");
        if($('validation_discount_amount'))
           $('validation_discount_amount').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a value for discount other than 0.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_discount_amount";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
    }
    
    else {
    if($('discount_type').value == 1)
    {
        if(!(/^(?:\d+|\d*\.\d+)$/.test(discount_amount))){
        var discount_error_name = document.getElementById("price-element");
        if($('validation_discount_amount'))
           $('validation_discount_amount').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a valid Number.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_discount_amount";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
     }
    }
    else{
        if(isNaN(discount_amount) || discount_amount < 0 || discount_amount > 100){
        var discount_error_name = document.getElementById("rate-element");
        if($('validation_discount_amount'))
           $('validation_discount_amount').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter discount amount in 1 - 100 range.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_discount_amount";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
       }
       else{
         if($('validation_discount_amount'))
           $('validation_discount_amount').innerHTML = '';
       }
    }
  }
  
   var min_purchase = $('minimum_purchase').value;
   if(min_purchase != 0 && min_purchase != ''){
   if(!(/^(?:\d+|\d*\.\d+)$/.test(min_purchase))){
        var discount_error_name = document.getElementById("minimum_purchase-element");
        if($('validation_min_purchase'))
            $('validation_min_purchase').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a valid positive number.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_min_purchase";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
   }
   else{
     if($('validation_min_purchase'))
       $('validation_min_purchase').innerHTML = '';
   }
 }
   
   var min_product = $('min_product_quantity').value;
   if(min_product != 0 || min_product != ''){
   if(!(/^\+?[0-9]\d*$/.test(min_product))){
        var discount_error_name = document.getElementById("min_product_quantity-element");
        if($('validation_product_qty'))
       $('validation_product_qty').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a valid positive number.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_product_qty";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
   }
   else{
     if($('validation_product_qty'))
       $('validation_product_qty').innerHTML = '';
   }
 }
   var claim_user_count = $('claim_user_count').value;
   if(claim_user_count != 0 || claim_user_count != ''){
   if(!(/^\+?[0-9]\d*$/.test(claim_user_count))){
        var discount_error_name = document.getElementById("claim_user_count-element");
        if($('validation_user_claim'))
          $('validation_user_claim').innerHTML = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a valid positive number.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_user_claim";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
   }
   else{
     if($('validation_user_claim'))
       $('validation_user_claim').innerHTML = '';
   }
 }
 
 var claim_count = $('claim_count').value;
   if(claim_count != 0 || claim_count != ''){
   if(!(/^\+?[0-9]\d*$/.test(claim_count))){
        var discount_error_name = document.getElementById("claim_count-element");
        if($('validation_claim_count'))
            $('validation_claim_count').innerHTML  = '';
				var myElement = new Element("p");
				myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("* Please enter a valid positive number.")) ?>';
				myElement.addClass("error");
				myElement.id = "validation_claim_count";
				discount_error_name.appendChild(myElement);
				validationFlage=1;
   }
   else{
     if($('validation_claim_count'))
       $('validation_claim_count').innerHTML  = '';
   }
 }
  
    if(return_value){
        if(validationFlage == 1){
          return false;
        }
    }
    else 
        return false;
		
		var title = $('title').value;
		var description = $('description').value;
		var claims = $('claim_count').value;
    if($('discount_type').value == 1)
        var discount_amount = $('price').value;
    else
        var discount_amount = $('rate').value;
      
    var min_purchase = $('minimum_purchase').value;
		var url = en4.core.baseUrl + 'sitestoreoffer/index/preview/discount_amount/' + discount_amount + '/minimum_purchase/' + min_purchase;
		Smoothbox.open(url);
  }

  en4.core.runonce.add(function(){

    // check end date and make it the same date if it's too
    cal_end_time.calendars[0].start = new Date( $('end_time-date').value );
    // redraw calendar
    cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
    cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);
    
    cal_start_time.calendars[0].start = new Date( $('start_time-date').value );
    // redraw calendar
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', 1);
    cal_start_time.navigate(cal_start_time.calendars[0], 'm', -1);
    
    initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_time', 'end_time');
    cal_start_date_onHideStart();
  });
  
  var cal_start_date_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_date, cal_end_date, 'start_time', 'end_time');
  };

  window.addEvent('domready', function() {
   
    if($('end_settings-1').checked) {
      document.getElementById("end_time-wrapper").style.display = "block";
    }
   
  });
</script>


<script type="text/javascript">

  var endsettingss = 0;
  
  function updateTextFields(value) {
		if (value == 0)
    {
      if($("end_time-wrapper"))
      $("end_time-wrapper").style.display = "none";
    } else if (value == 1)
    { if($("end_time-wrapper"))
      $("end_time-wrapper").style.display = "block";
    }
  }
  
  function showproducts()
  {
    
  }
  en4.core.runonce.add(updateTextFields(endsettingss));

</script>
<?php else: ?>
<?php if(empty($this->offer_store)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="sitestore_viewstores_head">
		<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
    <?php if(!empty($this->can_edit) && empty($this->offer_store)):?>
      <div class="fright">
				<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
      </div>
	  <?php endif;?>
		<h2>	
			<?php echo $this->sitestore->__toString() ?>	
			<?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Coupons')) ?>
		</h2>
	</div>
<?php endif;?>
<div class="tip">
  <span>
    <?php echo $this->translate("Your maximum limit to create coupon has been reached. Please contact to site admin."); ?>
  </span>
</div>
<?php endif; ?>
