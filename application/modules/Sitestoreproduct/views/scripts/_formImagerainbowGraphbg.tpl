<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImageraionbowGraphbg.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.graph.bgcolor', '#ffffff') ?>

<script type="text/javascript">

	window.addEvent('domready', function() { 
		var b = new MooRainbow('myRainbowBG', { 
			id: 'myDemoBG',
			'startColor': hexcolorTonumbercolor("<?php echo $bg_color ?>"),
			'onChange': function(color) {
				$('sitestoreproduct_graph_bgcolor').value = color.hex;
			}
		});		
	});
</script>

<?php
echo '
	<div id="sitestoreproduct_graph_bgcolor-wrapper" class="form-wrapper">
		<div id="sitestoreproduct_graph_bgcolor-label" class="form-label">
			<label for="sitestoreproduct_graph_bgcolor" class="optional">
				'.$this->translate('Background Color').'
			</label>
		</div>
		<div id="sitestoreproduct_graph_bgcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select the background color of the graphs showing store statistics. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestoreproduct_graph_bgcolor" id="sitestoreproduct_graph_bgcolor" value=' .$bg_color . ' type="text">
			<input name="myRainbowBG" id="myRainbowBG" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>