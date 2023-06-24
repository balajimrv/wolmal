<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowShippingPrice.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$shipping_price_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphshippingprice.color', '#00ffe1')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowShippingPrice', {
    
			id: 'myDemoShippingPrice',
			'startColor':hexcolorTonumbercolor("<?php echo $shipping_price_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphshippingprice_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphshippingprice_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphshippingprice_color-label" class="form-label">
			<label for="sitestoreproduct_graphshippingprice_color" class="optional">
				'.$this->translate('Shipping Price Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphshippingprice_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Shipping Price in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphshippingprice_color" id="sitestoreproduct_graphshippingprice_color" value=' . $shipping_price_color . ' type="text">
			<input name="myRainbowShippingPrice" id="myRainbowShippingPrice" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>