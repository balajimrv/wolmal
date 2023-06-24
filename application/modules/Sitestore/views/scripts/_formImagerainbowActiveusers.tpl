<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowActiveuserstpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphuser.color', '#458B00') ?>"),
      'onChange': function(color) {
        $('sitestore_graphuser_color').value = color.hex;
      }
    });
  });	
</script>

<?php
echo '
	<div id="sitestore_graphuser_color-wrapper" class="form-wrapper">
		<div id="sitestore_graphuser_color-label" class="form-label">
			<label for="sitestore_graphuser_color" class="optional">
				' . $this->translate('Active Users line Color') . '
			</label>
		</div>
		<div id="sitestore_graphuser_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the line which is used to represent Active Users in the graph.(Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitestore_graphuser_color" id="sitestore_graphuser_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphuser.color', '#458B00') . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>