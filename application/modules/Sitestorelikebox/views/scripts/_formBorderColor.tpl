<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formBorderColor.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/mooRainbow.js');
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('border_color_rainbow', {
      id: 'myDemo2',
      'onChange': function(color) {
        $('border_color').value = color.hex;
      }
    });

  });
</script>
<div id="border_color-wrapper" class="form-wrapper">
	<label for="border_color" class="optional"><?php echo $this->translate('Border Color'); ?>
		<a href="javascript:void(0);" class="sitestorelikebox_show_tooltip_wrapper"> [?]
			<span class="sitestorelikebox_show_tooltip">
				<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/tooltip_arrow.png"><?php echo $this->translate('Border color of the embeddable badge. Choose color code of your choice by clicking on the rainbow.'); ?>
			</span>
		</a>
	</label>
	<div id="border_color-element" class="form-element">
		<input type="text" value="" style="width:80px; max-width:80px;" onblur="setLikeBox()" />
		<input name="border_color_rainbow" id="border_color_rainbow" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/rainbow.png" link="true" type="image" />
		<input type="hidden" name="border_color" id="border_color" value=""  onblur="setLikeBox()" />
	</div>
</div>