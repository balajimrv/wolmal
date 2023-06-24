<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImageraionbowSponsored.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $sponsored_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');?>

<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', { 
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo $sponsored_color ?>"),
			'onChange': function(color) {
				$('sponsored_color').value = color.hex;
			}
		});		
	});
</script>

<?php
echo '
	<div id="sponsored_color-wrapper" class="form-wrapper">
		<div id="sponsored_color-label" class="form-label">
			<label for="sponsored_color" class="optional">
				'. $this->translate('Sponsored Label Color').'
			</label>
		</div>
		<div id="sponsored_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the "SPONSORED" labels. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sponsored_color" id="sponsored_color" value=' . $sponsored_color . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showsponsored(option) {
		if(option == 1) {
			$('sponsored_color-wrapper').style.display = 'block';
		}
		else {
			$('sponsored_color-wrapper').style.display = 'none';
		}
	}
  
  window.addEvent('domready', function() {
    showsponsored('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsored', 1);?>');
  });   
</script>