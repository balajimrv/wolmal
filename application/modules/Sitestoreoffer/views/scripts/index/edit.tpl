<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
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
<?php if(empty($this->offer_store)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="sitestore_viewstores_head">
		 <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
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
    var showCurrentTime = false;
    var currentTime = "<?php echo date('m/d/Y', time()); ?>";
    
    <?php if(strtotime($this->sitestoreoffer->start_time) > time() ) : ?>
      showCurrentTime = true;
    <?php endif; ?>
//    setTimeout(function(){initializeCalendarDate(seao_dateFormat, cal_start_time, cal_end_time, 'start_time', 'end_time', showCurrentTime, currentTime);
//    cal_start_time_onHideStart();}, 200);
      initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date', showCurrentTime, currentTime);
    cal_start_date_onHideStart();
  });
  
  var cal_start_time_onHideStart = function(){
    cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_time, cal_end_time, 'start_time', 'end_time');
  };
// END CALENDAR WORK FOR COUPON START- END DATE

  //<!--
   var maxRecipients = 10000;
   var temp_product_ids = '<?php echo $this->tempMappedIdsStr; ?>';
    window.addEvent('domready', function() {
//    var productType = $('product_type').value;    
//    if(productType == 'bundled' || productType == 'grouped'){
      productidsAutocomplete = new SEAOAutocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts', 'store_id' => $this->sitestore->store_id), 'default', true); ?>', {
        'postVar' : 'search',
        'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': temp_product_ids},      
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
          if ($('product_ids-wrapper')) {
            $('product_ids-wrapper').style.display='block';
          }
				
//          if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
//            this.element.disabled = true;
//          }
        
          productidsAutocomplete.setOptions({
            'postData' : {'store_ids': <?php echo $this->sitestore->store_id ?>, 'product_ids': $('product_ids').value}
          });
        }
      });
    //}
//    if(productType == 'bundled')
//      bundleProductTypes();
  });

window.addEvent('domready', function() {
//		var enable_public_private = '<?php //echo $this->enable_public_private;?>';
    var url_enable = '<?php echo $this->enable_url;?>';
//    if(enable_public_private != 1)
//        $('public-wrapper').style.display = 'block';
//    else
//        $('public-wrapper').style.display = 'none';
      
    if(url_enable != 0)
        $('url-wrapper').style.display = 'block';
    else
        $('url-wrapper').style.display = 'none';
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

  });

  // -->
</script>


<script type="text/javascript">

  var myCalStart = false;
  var myCalEnd = false;

  var endsettingss = '<?php echo $this->sitestoreoffer->end_settings;?>';
  
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

  en4.core.runonce.add(updateTextFields(endsettingss));
  
      function removeFromToValue(id, elmentValue, element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = temp_product_ids;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";
    


      for (var i = 0; i < toValueArray.length; i++){
        if (toValueArray[i] == id) 
          toValueIndex = i;
      }
      toValueArray.splice(toValueIndex, 1);
      temp_product_ids = toValueArray.join();

//    var checkMulti = id.search(/,/);
    // check if we are removing multiple recipients
//    if (checkMulti!=-1) {
//      var recipientsArray = id.split(",");
//      for (var i = 0; i < recipientsArray.length; i++){
//        // removeToValue(recipientsArray[i], toValueArray, elmentValue);
//        for (var i = 0; i < toValueArray.length; i++){
//          if (toValueArray[i]==recipientsArray[i]) 
//            toValueIndex =i;
//        }
//        toValueArray.splice(toValueIndex, 1);
//        $(elmentValue).value = toValueArray.join();
//      }
//    } else {
//      //      removeToValue(id, toValueArray, elmentValue);
//      for (var i = 0; i < toValueArray.length; i++){
//        if (toValueArray[i]==id) toValueIndex =i;
//      }
//      toValueArray.splice(toValueIndex, 1);
//      $(elmentValue).value = toValueArray.join();
//    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }

</script>
<?php
// SHOW DEFAULT ADDED PRODUCTS IN THE EDIT FORM.
if (!empty($this->productArray) && !empty($this->tempMappedIdsStr)):
  $productSpan = '<input type="hidden" id="product_ids" value="' . $this->tempMappedIdsStr . '" name="product_ids">';
  foreach ($this->productArray as $product) {
    $product['title'] = str_replace("'", "\'", $product['title']);
    $product['title'] = str_replace('"', '\"', $product['title']);
    $productSpan .= '<span id="tospan_' . $product['title'] . '_' . $product['id'] . '" class="tag">' . $product['title'] . '<a onclick="this.parentNode.destroy();removeFromToValue(&quot;' . $product['id'] . '&quot;, &quot;product_ids&quot; , &quot;product_name&quot;);" href="javascript:void(0);">x</a></span>';
  }
  ?>
  <script type="text/javascript">
    window.addEvent('domready', function() {
      document.getElementById("product_ids-element").innerHTML = '<?php echo $productSpan; ?>';
      document.getElementById("product_ids-wrapper").style.display = 'block';
    });
  </script>
<?php endif; ?>

