<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow2.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		
		var s = new MooRainbow('myRainbow2', { 
			id: 'myDemo2',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('sitestoreproduct_button_color2').value = color.hex;
			}
		});
	});
	
</script>

<?php
echo '
	<div id="sitestoreproduct_button_color2-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_button_color2-label" class="form-label">
			<label for="sitestoreproduct_button_color2" class="optional">
				'. $this->translate('Customize cart button hover color').'
			</label>
		</div>
		<div id="sitestoreproduct_button_color2-element" class="form-element">
			<p class="description">'.$this->translate('Select the hover color of the cart button. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_button_color2" id="sitestoreproduct_button_color2" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.button.color2', '#ff0000') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'.$this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
