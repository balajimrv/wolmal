<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeTwitter.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php
$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
$twitter = $twitterTable->getApi();

// Not connected
if (!$twitter || !$twitterTable->isConnected()) {
  return;
}

// Disabled
if ('publish' != Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
  return;
}

// Add script
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/composer_twitter.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Twitter({
      lang : {
        'Publish this on Twitter' : '<?php echo $this->translate('Publish this on Twitter') ?>'
      }
    }));
  });
</script>
