<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow1.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php 
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css'); 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');  
?> 
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var r = new MooRainbow('myRainbow1', { 
			id: 'myDemo1',
			'startColor': [58, 142, 246],
			'onChange': function(color) {
				$('sitestoreproduct_button_color1').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_button_color1-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_button_color1-label" class="form-label">
			<label for="sitestoreproduct_button_color1" class="optional">
				'.$this->translate('Customize cart button color').'
			</label>
		</div>
		<div id="sitestoreproduct_button_color1-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the cart button. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_button_color1" id="sitestoreproduct_button_color1" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.button.color1', '#0267cc') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'.$this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
