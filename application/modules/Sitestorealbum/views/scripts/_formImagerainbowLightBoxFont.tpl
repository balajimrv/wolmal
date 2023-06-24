<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLightBoxFont.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/mooRainbow.js');
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', {
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.fontcolor','#FFFFFF') ?>"),
			'onChange': function(color) {
				$('sitestorealbum_photolightbox_fontcolor').value = color.hex;
			}
		});
		
		showphotolightboxFont("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.show', 1) ?>")
		
	});
</script>

<?php
echo '
	<div id="sitestorealbum_photolightbox_fontcolor-wrapper" class="form-wrapper">
		<div id="sitestorealbum_photolightbox_fontcolor-label" class="form-label">
			<label for="sitestorealbum_photolightbox_fontcolor" class="optional">
				'. $this->translate('Photos Lightbox Font Color').'
			</label>
		</div>
		<div id="sitestorealbum_photolightbox_fontcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select a font color for the text in the lightbox displaying photos. (Click on the rainbow below to choose your color.)').'</p>
			<input name="sitestorealbum_photolightbox_fontcolor" id="sitestorealbum_photolightbox_fontcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.photolightbox.fontcolor', '#FFFFFF') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
	function showphotolightboxFont(option) {
		if(option == 1) {
			$('sitestorealbum_photolightbox_fontcolor-wrapper').style.display = 'block';
		}
		else {
			$('sitestorealbum_photolightbox_fontcolor-wrapper').style.display = 'none';
		}
	}
</script>