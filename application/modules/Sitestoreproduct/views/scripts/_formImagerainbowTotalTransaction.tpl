<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowtotalTransaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$transaction_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graphtransactions.color', '#394BAA')
 ?>

<script type="text/javascript">

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbowTotalTransaction', {
    
			id: 'myDemoTotalTransaction',
			'startColor':hexcolorTonumbercolor("<?php echo $transaction_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graphtransactions_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="sitestoreproduct_graphtransactions_color-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graphtransactions_color-label" class="form-label">
			<label for="sitestoreproduct_graphtransactions_color" class="optional">
				'.$this->translate('Total Transactions Line Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graphtransactions_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Total Transactions in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graphtransactions_color" id="sitestoreproduct_graphtransactions_color" value=' . $transaction_color . ' type="text">
			<input name="myRainbowTotalTransaction" id="myRainbowTotalTransaction" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>