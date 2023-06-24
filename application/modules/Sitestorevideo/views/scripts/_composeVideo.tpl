<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composeVideo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitestorevideo/externals/scripts/composer_video.js') ?>
<?php
$allowed = 0;
$user = Engine_Api::_()->user()->getViewer();
$youtubeEnabled = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
//$allowed_upload = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestorevideo', $user, 'upload');
$ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->sitestorevideo_ffmpeg_path;
if ($ffmpeg_path)
  $allowed = 1;
$allowed = 0;
?>



<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.SitestoreVideo({
      title : '<?php echo $this->translate('Add ') ?>',
      lang : {
        'Add ' : '<?php echo $this->string()->escapeJavascript($this->translate('Add ')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
        'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
        'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
        'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
        'To upload a video from your computer, please use our full uploader.': '<?php echo addslashes($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create', 'type' => 3), 'sitestorevideo_general'))) ?>'
      },
      allowed : <?php echo $allowed; ?>,
      youtubeEnabled: <?php echo (int) $youtubeEnabled?>,
      requestOptions : {
        'url' : en4.core.baseUrl + 'sitestorevideo/index/compose-upload/format/json/c_type/'+type
      }
    }));
  });
</script>
