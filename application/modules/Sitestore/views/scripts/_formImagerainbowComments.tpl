<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowcomments.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/mooRainbow.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var s = new MooRainbow('myRainbow5', { 
      id: 'myDemo5',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphcomment.color', '#9F5F9F') ?>"),
      'onChange': function(color) {
        $('sitestore_graphcomment_color').value = color.hex;
      }
    });
  });
</script>

<?php
echo '
 	<div id="sitestore_graphcomment_color-wrapper" class="form-wrapper">
 		<div id="sitestore_graphcomment_color-label" class="form-label">
 			<label for="sitestore_graphcomment_color" class="optional">
 				' . $this->translate('Comments Line Color') . '
 			</label>
 		</div>
 		<div id="sitestore_graphcomment_color-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the line which is used to represent Comments in the graph. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="sitestore_graphcomment_color" id="sitestore_graphcomment_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphcomment.color', '#9F5F9F') . ' type="text">
 			<input name="myRainbow5" id="myRainbow5" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>