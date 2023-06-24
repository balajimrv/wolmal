<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
 //Current Module Name
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $module = $p['module'];
    $pos = preg_match('/music/', $module);
 ?>  
<!--CHECK FOR MUSIC PLUGIN, IF MUSIC PLUGIN THEN ADD MUSIC INSTEAD OF UPLOAD PHOTOS-->
<?php if(!empty($pos)): ?>
<div id="photo-wrapper" class="form-wrapper">
  <div>
    <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
     <?php echo $this->translate('Click "Add Music" to select  one or more songs from your system. After you have selected the songs click the button "Save" to save them to your playlist.') ?>
    <?php else: ?>
    <?php echo $this->translate('SITEMOBILE_STORAGE_UPLOAD_MUSIC_DESCRIPTION') ?>
    <?php endif; ?>
  </div>
  <div id="photo-label" class="form-label">
    <label for="Filedata" class="optional ui-input-text"><?php echo $this->translate('Add Music') ?></label></div>
  <div id="photo-element" class="form-element">
    <input type="file" name="Filedata[]" multiple="multiple" accept="/*" />
  </div>
</div>
<?php else:?>
<div id="photo-wrapper" class="form-wrapper">
  <div>
    <?php echo $this->translate('SITEMOBILE_STORAGE_UPLOAD_DESCRIPTION') ?>
  </div>
  <div id="photo-label" class="form-label">
    <label for="Filedata" class="optional ui-input-text"><?php echo $this->translate('Upload Photos') ?></label></div>
  <div id="photo-element" class="form-element">
    <input type="file" name="Filedata[]" multiple="multiple" accept="image/*" />
  </div>
</div>
<?php endif;?>
