<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowGrossAmount.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

 <?php
$gross_amount_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphgrossamount.color', '#3299CC')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowGrossAmount', {
    
			id: 'myDemoGrossAmount',
			'startColor':hexcolorTonumbercolor("<?php echo $gross_amount_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphgrossamount_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphgrossamount_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphgrossamount_color-label" class="form-label">
			<label for="sitestoreproduct_graphgrossamount_color" class="optional">
				'.$this->translate('Grand Total Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphgrossamount_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Grand Total in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphgrossamount_color" id="sitestoreproduct_graphgrossamount_color" value=' . $gross_amount_color . ' type="text">
			<input name="myRainbowGrossAmount" id="myRainbowGrossAmount" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>