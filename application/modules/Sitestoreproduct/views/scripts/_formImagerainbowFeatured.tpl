<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowFeatured.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $featured_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featuredcolor', '#30a7ff'); ?>

<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
		var hexcolorAlphabets = "0123456789ABCDEF";
		var valueNumber = new Array(3);
		var j = 0;
		if(hexcolor.charAt(0) == "#")
		hexcolor = hexcolor.slice(1);
		hexcolor = hexcolor.toUpperCase();
		for(var i=0;i<6;i+=2) {
			valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
			j++;
		}
		return(valueNumber);
	}

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbow1', {
    
			id: 'myDemo1',
			'startColor':hexcolorTonumbercolor("<?php echo $featured_color ?>"),
			'onChange': function(color) {
				$('featured_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="featured_color-wrapper" class="form-wrapper">
		<div id="featured_color-label" class="form-label">
			<label for="featured_color" class="optional">
				'.$this->translate('Featured Label Color').'
			</label>
		</div>
		<div id="featured_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the "FEATURED" labels. (Click on the rainbow below to choose your color.)').'</p>
			<input name="featured_color" id="featured_color" value=' . $featured_color . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showfeatured(option) {
   $('featured_color-wrapper').style.display = 'none';
   return; //Not Currently Used Show display none, Not remove this
		if(option == 1) {
			$('featured_color-wrapper').style.display = 'block';
		}
		else {
			$('featured_color-wrapper').style.display = 'none';
		}
	}
  
  window.addEvent('domready', function() {
    showfeatured('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featured', 1);?>');
  });    
</script>