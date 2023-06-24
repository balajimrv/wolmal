<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowFeatured.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

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
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.color', '#0cf523') ?>"),
      'onChange': function(color) {
        $('sitestore_featured_color').value = color.hex;
      }
    });
    showfeatured("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1) ?>")
  });	
</script>

<?php
echo '
	<div id="sitestore_featured_color-wrapper" class="form-wrapper">
		<div id="sitestore_featured_color-label" class="form-label">
			<label for="sitestore_featured_color" class="optional">
				' . $this->translate('Featured Label Color') . '
			</label>
		</div>
		<div id="sitestore_featured_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the "FEATURED" labels. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitestore_featured_color" id="sitestore_featured_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.color', '#0CF523') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
  function showfeatured(option) {
    if(option == 1) {
      $('sitestore_featured_color-wrapper').style.display = 'block';
    }
    else {
      $('sitestore_featured_color-wrapper').style.display = 'none';
    }
  }
</script>