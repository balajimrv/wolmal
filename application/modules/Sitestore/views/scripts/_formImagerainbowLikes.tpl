<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLikes.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphlike.color', '#CD6839') ?>"),
      'onChange': function(color) {
        $('sitestore_graphlike_color').value = color.hex;
      }
    });
  });
</script>

<?php
echo '
 	<div id="sitestore_graphlike_color-wrapper" class="form-wrapper">
 		<div id="sitestore_graphlike_color-label" class="form-label">
 			<label for="sitestore_graphlike_color" class="optional">
 				' . $this->translate('Likes Line Color') . '
 			</label>
 		</div>
 		<div id="sitestore_graphlike_color-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the line which is used to represent Likes in the graph. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="sitestore_graphlike_color" id="sitestore_graphlike_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphlike.color', '#CD6839') . ' type="text">
 			<input name="myRainbow2" id="myRainbow2" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>