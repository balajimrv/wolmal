<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowViews.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphview.color', '#3299CC') ?>"),
      'onChange': function(color) {
        $('sitestore_graphview_color').value = color.hex;
      }
    });
  });	
</script>

<?php
echo '
	<div id="sitestore_graphview_color-wrapper" class="form-wrapper">
		<div id="sitestore_graphview_color-label" class="form-label">
			<label for="sitestore_graphview_color" class="optional">
				' . $this->translate('Views Line Color') . '
			</label>
		</div>
		<div id="sitestore_graphview_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the line which is used to represent Views in the graph. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitestore_graphview_color" id="sitestore_graphview_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphview.color', '#3299CC') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>