<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLightBoxBg.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/mooRainbow.js');
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow1', {
			id: 'myDemo1',
			'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000') ?>"),
			'onChange': function(color) {
				$('sitestorealbum_photolightbox_bgcolor').value = color.hex;
			}
		});
		
		showphotolightboxBg("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.show', 1) ?>")
		
	});
</script>

<?php
echo '
	<div id="sitestorealbum_photolightbox_bgcolor-wrapper" class="form-wrapper">
		<div id="sitestorealbum_photolightbox_bgcolor-label" class="form-label">
			<label for="sitestorealbum_photolightbox_bgcolor" class="optional">
				'. $this->translate('Photos Lightbox Background Color').'
			</label>
		</div>
		<div id="sitestore_photolightbox_bgcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select a color for the background of the lightbox displaying photos. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestorealbum_photolightbox_bgcolor" id="sitestorealbum_photolightbox_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.bgcolor', '#000000') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showphotolightboxBg(option) {
		if(option == 1) {
			$('sitestorealbum_photolightbox_bgcolor-wrapper').style.display = 'block';
		}
		else {
			$('sitestorealbum_photolightbox_bgcolor-wrapper').style.display = 'none';
		}
	}

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

</script>