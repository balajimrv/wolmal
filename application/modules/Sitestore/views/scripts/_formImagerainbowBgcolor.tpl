<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowHeader.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/mooRainbow.js');
?> 

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var r = new MooRainbow('myRainbow3', { 
      id: 'myDemo3',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitestore_bg_color').value = color.hex;
      }
    });
  });	
</script>

<?php

echo '
<div id="sitestore_bg_color-wrapper" class="form-wrapper">
	<div id="sitestore_bg_color-label" class="form-label">
		<label for="sitestore_bg_color" class="optional">
			' . $this->translate('Email Body Outer Background') . '
		</label>
	</div>
	<div id="sitestore_bg_color-element" class="form-element">
		<p class="description">' . $this->translate('Select the background color of the outer area in the email around the mail content. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="sitestore_bg_color" id="sitestore_bg_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.bg.color', '#f7f7f7') . ' type="text">
		<input name="myRainbow3" id="myRainbow3" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>