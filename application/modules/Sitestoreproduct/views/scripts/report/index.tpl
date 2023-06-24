<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl';
  include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; 
?>
	<div class="layout_middle">
    <?php $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js');
    ?>

    <div class="sitestore_edit_content">
      <div class="sitestore_edit_header">
				<a href='<?php echo $this->url(array('store_url' => $this->sitestoreUrl), 'sitestore_entry_view', true) ?>' class="sitestoreproduct_buttonlink"><?php echo $this->translate('View Store'); ?></a>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
			</div>
      <div id="show_tab_content">

<script type="text/javascript">
  var maxRecipients = 10;

function removeFromToValue(id, elmentValue,element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $(elmentValue).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray, elmentValue);
      }
    } else {
      removeToValue(id, toValueArray, elmentValue);
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }
 
  function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }
    toValueArray.splice(toValueIndex, 1);
    $(elmentValue).value = toValueArray.join();
  }
  
  var packageRequest;
  var storeidsAutocomplete;
  var productidsAutocomplete;
  en4.core.runonce.add(function() {

		storeidsAutocomplete = new SEAOAutocompleter.Request.JSON('store_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggeststores'), 'default', true) ?>', {
			'postVar' : 'search',
      'postData' : {'store_ids': $('store_ids').value},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'store_ids',
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
				if ($('store_ids-wrapper')) {
					$('store_ids-wrapper').style.display='block';
				}
				
				if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
					this.element.disabled = true;
				}
        storeidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value}
      });
      productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
      });
			}
		});


		 productidsAutocomplete = new SEAOAutocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts'), 'default', true) ?>', {
			'postVar' : 'search',
      'postData' : {'store_id' : <?php echo $this->store_id ?>, 'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value},
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
				
				if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
					this.element.disabled = true;
				}
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
      });
			}
		});
})

</script>

<script type="text/javascript">

en4.core.runonce.add(function()
  {
    
    monthList = [];
          
    if( $('start_cal-minute') ) $('start_cal-minute').style.display= 'none';
    if( $('start_cal-ampm') ) $('start_cal-ampm').style.display= 'none';
    if( $('start_cal-hour') ) $('start_cal-hour').style.display= 'none';
    if( $('end_cal-minute') ) $('end_cal-minute').style.display= 'none';
    if( $('end_cal-ampm') ) $('end_cal-ampm').style.display= 'none';
    if( $('end_cal-hour') ) $('end_cal-hour').style.display= 'none';
    if( $('store_name-wrapper') ) $('store_name-wrapper').style.display = 'none';

    var empty = '<?php echo $this->empty; ?>';
    var no_ads = '<?php echo $this->no_ads ?>';
   
    form = $('store_report_form');
    form.setAttribute("method","get");
    var e3 = $('product_name-wrapper');
    e3.setStyle('display', 'none');
    
    var e4 = $('store_name-wrapper');
    e4.setStyle('display', 'none');
    
    var e5 = $('select_product-wrapper');
    e5.setStyle('display', 'none');
    
    var e6 = $('store_ids-wrapper');
    e6.setStyle('display', 'none');
    
    var e7 = $('product_ids-wrapper');
    e7.setStyle('display', 'none');
//    
//    var e8 = $('listing_based_on-wrapper');
//    e8.setStyle('display', 'none');

    onstoreChange($('select_store'));
    onproductChange($('select_product'));
    onChangeTime($('time_summary'));
    onchangeFormat($('format_report'));

    // display message tip
    if(empty == 1) {
      if(no_ads == 1) {
	$('tip2').style.display= 'block';
      } else {
	$('tip').style.display= 'block';
      }
    }
          
  });

  function onstoreChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'all' || e1 == 'current_store') 
    {
      $('store_name-wrapper').setStyle('display', 'none');
      
      if($('store_ids').value)
      {
        $('store_ids').value = null;
        $('store_ids-element').getElements('.tag').destroy();
        
        storeidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value}
        });
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
        });
      }
      
      if($('product_ids').value)
      {
        $('product_ids').value = null;
        $('product_ids-element').getElements('.tag').destroy();
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
        });
      }
      
    }
    else {
      $('store_name-wrapper').setStyle('display', 'block');
      
      if($('product_ids').value)
      {
        $('product_ids').value = null;
        $('product_ids-element').getElements('.tag').destroy();
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
      });
      }
    }
    
//    if( e1 != 'current_store' )
//      $('listing_based_on-wrapper').setStyle('display', 'block');
//    else
//      $('listing_based_on-wrapper').setStyle('display', 'none');
  }
  
  function onreportDependChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'order') {
      $('select_product-wrapper').setStyle('display', 'none');
      $('product_name-wrapper').setStyle('display', 'none');
      
    }
    else if(e1 == 'product') {
      $('select_product-wrapper').setStyle('display', 'block');
    }
  }
  
  function onproductChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'all') {
      $('product_name-wrapper').setStyle('display', 'none');
      if($('product_ids').value)
      {
        $('product_ids').value = null;
        $('product_ids-element').getElements('.tag').destroy();
        
        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
      });
      }
    }
    else if(e1 == 'specific_product') {
      $('product_name-wrapper').setStyle('display', 'block');
    }
  }

  function onChangeTime(formElement) {

    if(formElement.value == 'Monthly') {
      $('start_group').setStyle('display', 'block');
      $('end_group').setStyle('display', 'block');
      $('time_group2').setStyle('display', 'none');
    }
    else if(formElement.value == 'Daily') {
      $('start_group').setStyle('display', 'none');
      $('end_group').setStyle('display', 'none');
      $('time_group2').setStyle('display', 'block');
    }
    
  }
  
  function onchangeFormat(formElement) {

    form = $('store_report_form');
		if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>

      <div class="tip" id = 'tip' style='display:none;'>
        <span>
          <?php echo $this->translate("There are no activities found in the selected date range.") ?>
        </span>
      </div>
      <div class="tip" id ='tip2' style='display:none;'>
        <span>
          <?php echo $this->translate("No orders have been placed on your site yet.") ?>
        </span>
      </div>
      <br />
      <div>
        <?php echo $this->reportform->render($this) ?>
      </div>    
      <div id="report_loading_image"></div>
    </div> 
  </div> 
</div>