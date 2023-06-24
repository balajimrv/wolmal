<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowCommission.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

 <?php
$commission_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphcommission.color', '#CD6839')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowComissiom', {
    
			id: 'myDemoCommission',
			'startColor':hexcolorTonumbercolor("<?php echo $commission_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphcommission_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphcommission_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphcommission_color-label" class="form-label">
			<label for="sitestoreproduct_graphcommission_color" class="optional">
				'.$this->translate('Commission Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphcommission_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Commission in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphcommission_color" id="sitestoreproduct_graphcommission_color" value=' . $commission_color . ' type="text">
			<input name="myRainbowComissiom" id="myRainbowComissiom" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>