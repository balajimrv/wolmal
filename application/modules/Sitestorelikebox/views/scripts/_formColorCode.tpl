<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formColorCode.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
      'onChange': function(color) {
        $('sitestore_sponsored_color').value = color.hex;
      }
    });

  });
</script>

<?php
echo '
	<div class="splb-admin-colorpicker-wrapper">
		<div class="splb-admin-colorpicker-label">
				' . $this->translate('Color Code:') . '
		</div>
		<div class="splb-admin-colorpicker-element">
			<input name="sitestore_sponsored_color" id="sitestore_sponsored_color" value="#FC0505" type="text">
			<input name="myRainbow2" id="myRainbow2" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>