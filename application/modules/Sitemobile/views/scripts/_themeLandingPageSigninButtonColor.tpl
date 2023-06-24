<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _themeLandingPageSigninButtonColor.tpl 2014-10-09 00:00:00Z SocialEngineAddOns $
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

    var r = new MooRainbow('myRainbow3', {
    
      id: 'myDemo3',
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.landingpage.signinbtn', '#ff5f3f') ?>"),
      'onChange': function(color) {
        $('sitemobile_landingpage_signinbtn').value = color.hex;
      }
    });
//    showfeatured("<?php // echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feature.image', 1) ?>")
  });	
</script>

<?php
echo '
	<div id="sitemobile_landingpage_signinbtn-wrapper" class="form-wrapper">
		<div id="sitemobile_landingpage_signinbtn-label" class="form-label">
			<label for="sitemobile_landingpage_signinbtn" class="optional">
				' . $this->translate('Landing Page Sign In Button') . '
			</label>
		</div>
		<div id="sitemobile_landingpage_signinbtn-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the sign in button available on the landing page. (Click on the rainbow below to choose your color.)<br>[Note:  If you will change the sign in button color from here then transparency in color will not be visible in the button as it is looking on our demo, but if you want to do so, please read our FAQ section.]') . '</p>
			<input name="sitemobile_landingpage_signinbtn" id="sitemobile_landingpage_signinbtn" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.landingpage.signinbtn', '#ff5f3f') . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitemobile/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
//  function showfeatured(option) {
//    if(option == 1) {
//      $('sitemobile_landingpage_signinbtn-wrapper').style.display = 'block';
//    }
//    else {
//      $('sitemobile_landingpage_signinbtn-wrapper').style.display = 'none';
//    }
//  }
</script>