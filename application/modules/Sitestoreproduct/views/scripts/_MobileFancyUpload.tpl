<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _FancyUpload.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<input type="hidden" name="file" id="fancyuploadfileids" value ="" />
<fieldset id="demo-fallback">
  <label for="demo-photoupload">
    <?php echo $this->translate('Upload a Photo:');?>
  </label>
</fieldset>

<div id="demo-status" class="hide">
  <div>
    <?php echo $this->translate("Click 'Add Photo' to select one photo from your device. Click the button below your photo to save them to your product.");?>
  </div>
</div>
