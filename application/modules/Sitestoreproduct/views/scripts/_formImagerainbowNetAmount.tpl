<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowNetAmount.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

 <?php
$net_amount_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphnetamount.color', '#458B00')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowNetAmount', {
    
			id: 'myDemoNetAmount',
			'startColor':hexcolorTonumbercolor("<?php echo $net_amount_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphnetamount_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphnetamount_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphnetamount_color-label" class="form-label">
			<label for="sitestoreproduct_graphnetamount_color" class="optional">
				'.$this->translate('Subtotal Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphnetamount_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Subtotal in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphnetamount_color" id="sitestoreproduct_graphnetamount_color" value=' . $net_amount_color . ' type="text">
			<input name="myRainbowNetAmount" id="myRainbowNetAmount" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>