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
    var r = new MooRainbow('myRainbow1', { 
      id: 'myDemo1',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitestore_header_color').value = color.hex;
      }
    });
  });	
</script>

<?php

echo '
<div id="sitestore_header_color-wrapper" class="form-wrapper">
	<div id="sitestore_header_color-label" class="form-label">
		<label for="sitestore_header_color" class="optional">
			' . $this->translate('Email Template Header Background Color') . '
		</label>
	</div>
	<div id="sitestore_header_color-element" class="form-element">
		<p class="description">' . $this->translate('Select the color of the header background of email template. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="sitestore_header_color" id="sitestore_header_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.header.color', '#79b4d4') . ' type="text">
		<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>