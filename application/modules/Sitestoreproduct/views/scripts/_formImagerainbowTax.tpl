<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowTax.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

 <?php
$tax_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtax.color', '#f705ff')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowTax', {
    
			id: 'myDemoTax',
			'startColor':hexcolorTonumbercolor("<?php echo $tax_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphtax_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphtax_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphtax_color-label" class="form-label">
			<label for="sitestoreproduct_graphtax_color" class="optional">
				'.$this->translate('Tax Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphtax_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Tax in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphtax_color" id="sitestoreproduct_graphtax_color" value=' . $tax_color . ' type="text">
			<input name="myRainbowTax" id="myRainbowTax" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>