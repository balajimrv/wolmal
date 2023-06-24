<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowSponsred.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#FC0505') ?>"),
      'onChange': function(color) {
        $('sitestore_sponsored_color').value = color.hex;
      }
    });
		
    showsponsored("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1) ?>")
		
  });
</script>

<?php
echo '
	<div id="sitestore_sponsored_color-wrapper" class="form-wrapper">
		<div id="sitestore_sponsored_color-label" class="form-label">
			<label for="sitestore_sponsored_color" class="optional">
				' . $this->translate('Sponsored Label Color') . '
			</label>
		</div>
		<div id="sitestore_sponsored_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the "SPONSORED" labels. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitestore_sponsored_color" id="sitestore_sponsored_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#FC0505') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
  function showsponsored(option) {
    if(option == 1) {
      $('sitestore_sponsored_color-wrapper').style.display = 'block';
    }
    else {
      $('sitestore_sponsored_color-wrapper').style.display = 'none';
    }
  }
</script>