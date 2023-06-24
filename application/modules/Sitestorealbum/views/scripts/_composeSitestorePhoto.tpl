<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id:_composeSitestorePhoto.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  if (Engine_Api::_()->core()->hasSubject() && in_array($this->subject()->getType(),array('sitestore_store','sitestoreevent_event'))):?>
<style type="text/css">
  /*
ACTIVITY FEED COMPOSER  ALBUM PHOTO
These styles are used for the attachment composer above the
main feed.
*/
#compose-photo-activator,
#compose-photo-menu span
{
 display: none !important;
}
</style>
 <?php
 $subject = $this->subject();
 if(in_array($subject->getType(),array('sitestoreevent_event'))):
    $subject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
 endif;
 //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorealbum")) {
        return;
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'spcreate');
      if (empty($isStoreOwnerAllow)) {
        return;
      }
    }
 if (!Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit') &&!Engine_Api::_()->sitestore()->isManageAdmin($subject,'spcreate') ):
    return;
  endif;
?>
<?php
  $this->headScript()
     ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestorealbum/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestorealbum/externals/scripts/composer_photo.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
  ));
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.SitestorePhoto({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
      lang : {
        'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'sitestore/album/compose-upload/type/'+type+'/store_id/'+<?php echo $subject->getIdentity() ?>
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'sitestore/album/compose-upload/format/json/type/'+type+'/store_id/'+<?php echo $subject->getIdentity() ?>,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>
<?php endif; ?>