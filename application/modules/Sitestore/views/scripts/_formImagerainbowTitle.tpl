<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowTitle.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var s = new MooRainbow('myRainbow2', { 
      id: 'myDemo2',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitestore_title_color').value = color.hex;
      }
    });
  });
</script>

<?php

echo '
 	<div id="sitestore_title_color-wrapper" class="form-wrapper">
 		<div id="sitestore_title_color-label" class="form-label">
 			<label for="sitestore_title_color" class="optional">
 				' . $this->translate('Email Template Header Title Text Color') . '
 			</label>
 		</div>
 		<div id="sitestore_title_color-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the header title text of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="sitestore_title_color" id="sitestore_title_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', '#ffffff') . ' type="text">
 			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>