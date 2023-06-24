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

<style type="text/css">
select{
  float:left;
  margin-right:10px;
}
</style>
<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/composer.js');
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
     ->appendFile($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/scripts/autocompleter/Autocompleter.js')
 		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
 		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

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

		storeidsAutocomplete = new Autocompleter.Request.JSON('store_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggeststores'), 'admin_default', true) ?>', {
			'postVar' : 'search',
      'postData' : {'store_ids': $('store_ids').value},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'store_ids',
			'autocompleteType': 'message',
			'multiple': true,
			'className': 'seaocore-autosuggest',
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
        
        <?php if( !empty($this->reportType) ) : ?>
          productidsAutocomplete.setOptions({
              'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
          });
        <?php endif; ?>
			}
		});


		 productidsAutocomplete = new Autocompleter.Request.JSON('product_name', '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'report', 'action' => 'suggestproducts'), 'admin_default', true) ?>', {
			'postVar' : 'search',
      'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'product_ids',
			'autocompleteType': 'message',
			'multiple': true,
			'className': 'seaocore-autosuggest',
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

  window.addEvent('domready', function() { 
    $('start_cal-minute').style.display= 'none';
    if($('start_cal-ampm'))
      $('start_cal-ampm').style.display= 'none';
    $('start_cal-hour').style.display= 'none';
    $('end_cal-minute').style.display= 'none';
    if($('end_cal-ampm'))
      $('end_cal-ampm').style.display= 'none';
    $('end_cal-hour').style.display= 'none';

    var empty = '<?php echo $this->empty; ?>';
    var no_ads = '<?php echo $this->no_ads ?>';
   
    form = $('adminreport_form');
    form.setAttribute("method","get");
    
    var e3 = $('store_name-wrapper');
    e3.setStyle('display', 'none');
    
    var e4 = $('store_ids-wrapper');
    e4.setStyle('display', 'none');
    
    <?php if( !empty($this->reportType) ) : ?>   
//      var e5 = $('select_product-wrapper');
//      e5.setStyle('display', 'none');
      
      var e6 = $('product_name-wrapper');
      e6.setStyle('display', 'none');
      
      var e7 = $('product_ids-wrapper');
      e7.setStyle('display', 'none');
    <?php endif; ?>
    
    onstoreChange($('select_store'));
    onChangeTime($('time_summary'));
    onchangeFormat($('format_report'));
    <?php if( !empty($this->reportType) ) : ?>    
      onproductChange($('select_product'));
    <?php endif; ?>

    // display message tip
    if(empty == 1) {
      if(no_ads == 1) 
        $('tip2').style.display= 'block';
      else 
        $('tip').style.display= 'block';
    }
  });

  function onstoreChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'all') 
    {
      $('store_name-wrapper').setStyle('display', 'none');
      
      if($('store_ids').value)
      {
        $('store_ids').value = null;
        $('store_ids-element').getElements('.tag').destroy();
        
        storeidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value}
        });
        
        <?php if( empty($this->reportType) ) : ?>
          productidsAutocomplete.setOptions({
            'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value}
          });
        <?php else: ?>
          productidsAutocomplete.setOptions({
            'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
          });
        <?php endif; ?>
      }
    }
    else if(e1 == 'specific_store') 
      $('store_name-wrapper').setStyle('display', 'block');
    
    <?php if( !empty($this->reportType) ) : ?>
      if($('product_ids').value)
      {
        $('product_ids').value = null;
        $('product_ids-element').getElements('.tag').destroy();

        productidsAutocomplete.setOptions({
          'postData' : {'store_ids': $('store_ids').value, 'select_store' : $('select_store').value, 'product_ids': $('product_ids').value}
        });
      }
    <?php endif; ?>
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
    form = $('adminreport_form');
		if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

  
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="<?php echo empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'report', 'type' => 0), $this->translate('Order Wise Sales Report')) ?>
    </li>
    <li class="<?php echo !empty($this->reportType) ? 'active' : '' ?>">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'report', 'type' => 1), $this->translate('Product Wise Sales Report')) ?>
    </li>
  </ul>
</div>

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
<?php if( !empty($this->reportType) ):
    $this->reportform->setTitle("Product Wise Sales Report");
    $this->reportform->setDescription("Here, you may view performance report of products sold from the stores on your site. You can also view the performance of sales of any desired product from all or any desired stores. Report can be viewed over multiple durations and time intervals. Reports can also be viewed for any desired order status. You can also export and save the report.");
endif; ?>
<div class="seaocore_settings_form">
	<div class="settings">
		<?php echo $this->reportform->render($this) ?>
	</div>
</div>	