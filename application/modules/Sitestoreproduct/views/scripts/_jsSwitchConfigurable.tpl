<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _jsSwitchConfigurable.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">

	var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
	var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';
	var elementCache = {};
  var selectedFields = {};

	function getFieldsElements(selector)
	{
		if( selector in elementCache || $type(elementCache[selector]) ) {
			return elementCache[selector];
		} else {
			return elementCache[selector] = $$(selector);
		}
	}

	function changeProfileFields(element, force)
	{
		element = $(element);
    
		// We can call this without an argument to start with the top level fields
		if( !$type(element) ) {
			getFieldsElements('.parent_' + topLevelId).each(function(element) {
				changeProfileFields(element);
			});
			return;
		}

		// If this cannot have dependents, skip
		if( !$type(element) || !$type(element.onchange) ) {
			return;
		}

		// Get the input and params
		var field_id = element.get('class').match(/field_([\d]+)/i)[1];
		var parent_field_id = element.get('class').match(/parent_([\d]+)/i)[1];
		var parent_option_id = element.get('class').match(/option_([\d]+)/i)[1];

		//console.log(field_id, parent_field_id, parent_option_id);

		if( !field_id || !parent_option_id || !parent_field_id ) {
			return;
		}

		force = ( $type(force) ? force : false );

		// Now look and see
		// Check for multi values
		var option_id = [];
		if( $type(element.name) && element.name.indexOf('[]') > 0 ) {
			if( element.type == 'checkbox' ) { // MultiCheckbox
				getFieldsElements('.field_' + field_id).each(function(multiEl) {
					if( multiEl.checked ) {
						option_id.push(multiEl.value);
					}
				});
			} else if( element.get('tag') == 'select' && element.multiple ) { // Multiselect
				element.getChildren().each(function(multiEl) {
					if( multiEl.selected ) {
						option_id.push(multiEl.value);
					}
				});
			}
		} else if( element.type == 'radio' ) {
			if( element.checked ) {
				option_id = [element.value];
			}
		} else {
			option_id = [element.value];
		}

		//console.log(option_id, $$('.parent_'+field_id));

		// Iterate over children
		getFieldsElements('.parent_' + field_id).each(function(childElement) {
			//console.log(childElement);
			var childContainer;
			if( childElement.getParent('form').get('class') == 'field_search_criteria' ) {
				childContainer = $try(function(){ return childElement.getParent('li').getParent('li'); });
			}
			if( !childContainer ) {
				childContainer = childElement.getParent('div.form-wrapper');
			}
			if( !childContainer ) {
				childContainer = childElement.getParent('div.form-wrapper-heading');
			}
			if( !childContainer ) {
				childContainer = childElement.getParent('li');
			}
			
			//var childLabel = childContainer.getElement('label');
			var childOptionId = childElement.get('class').match(/option_([\d]+)/i)[1];
			var childIsVisible = ( 'none' != childContainer.getStyle('display') );
			var skipPropagation = false;
			//var childFieldId = childElement.get('class').match(/field_([\d]+)/i)[1];
			
			// Forcing hide
			var nextForce;
			if( force == 'hide' ) {
				if( !childElement.hasClass('field_toggle_nohide') ) {
					childContainer.setStyle('display', ''); //MODIFIED FOR SITESTOREPRODUCT
				}
				nextForce = force;
			} else if( force == 'show' ) {
				childContainer.setStyle('display', '');
				nextForce = force;
			} else if( !$type(option_id) == 'array' || !option_id.contains(childOptionId) ) {
				// Hide fields not tied to the current option (but propogate hiding)
				if( !childElement.hasClass('field_toggle_nohide') ) {
					childContainer.setStyle('display', ''); //MODIFIED FOR SITESTOREPRODUCT
				}
				nextForce = 'hide';
				if( !childIsVisible ) {
					skipPropagation = true;
				}
			} else {
				// Otherwise show field and propogate (nothing, show?)
				childContainer.setStyle('display', '');
				nextForce = undefined;
				//if( childIsVisible ) {
				//  skipPropagation = true;
				//}
			}

			if( !skipPropagation ) {
				changeProfileFields(childElement, nextForce);
			}
		});

		window.fireEvent('onChangeFields');
	}
  
  function setConfigurablePrice(element, key, values, field_type){

    values = values.replace('a', '"', "g");
    values = JSON.parse(values);
    document.getElementById("configuration_price_loading").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    
    if(field_type == 'checkbox')
        var field_key = key;
    else
        var field_key = key + '_' + element.value;
     
    if(values[field_key] != null){
      if(field_type == 'multi_checkbox'){
      if(element.checked){
        if(selectedFields[key] != null)
          selectedFields[key] = parseFloat(selectedFields[key]) + parseFloat(values[field_key]);
        else
          selectedFields[key] = values[field_key];
      }
      else
          selectedFields[key] =   parseFloat(selectedFields[key]) + ((-1) * parseFloat(values[field_key]));
    }
    else if(field_type == 'multiselect'){
      selectedFields[key] = null;
      var select = document.getElementById(key);
      for (var i = 0; i < select.options.length; i++) {
        if (select.options[ i ].selected) {
          var field_key = key + '_' + select.options[ i ].value;
          if(selectedFields[key] != null)
            selectedFields[key] = parseFloat(selectedFields[key]) + parseFloat(values[field_key]);
          else
            selectedFields[key] = values[field_key];
        }
      }
    }
    else if(field_type == 'checkbox'){
      if(element.checked)
          selectedFields[key] = values[field_key];
      else
          selectedFields[key] =   parseFloat(selectedFields[key]) + ((-1) * parseFloat(values[field_key]));
    }
    else{
      selectedFields[key] = values[field_key];
    }
    }
    else{
      selectedFields[key] = '0.00';
    }
    
    var x;
    var configurable_price = 0;
    
    for(x in selectedFields){
      configurable_price += parseFloat(selectedFields[x]);
    }
    configurable_price += parseFloat(discounted_price);
    
    if(show_msg == 1){
          if( !isFixed ) {
            configurable_price = configurable_price + vatValue;
          } else {
            configurable_price = configurable_price + ((vatValue * configurable_price) / 100);
          }
    }
    
    var url = en4.core.baseUrl + 'sitestoreproduct/index/configuration-price';
    temp = new Request({
			method: 'post',
			'url': url,
			'data' : {
				'format' : 'json',
        'price' :  configurable_price,
        show_msg : show_msg,
			},
      onSuccess : function(responseJSON) {
        document.getElementById("configuration_price_loading").innerHTML = '';
        document.getElementsByClassName('sr_sitestoreproduct_profile_price')[0].innerHTML = responseJSON;
      }
    });
    temp.send();
    changeProfileFields(element);
  }
  
  

	window.addEvent('load', function()
	{
		changeProfileFields();
	});

</script>