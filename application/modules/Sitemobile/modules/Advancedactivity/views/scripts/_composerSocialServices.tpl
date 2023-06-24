<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composerSocialServices.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
//FACEBOOK SERVICE.... 
$showFBIcon = true;
$settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish' ? 1 : 0) || (empty($settings['secret']) || empty($settings['appid']))) {
  $showFBIcon = false;
}

//NOW CHECK IF THE USER IS ACTIVE USING FACEBOOK:
if ($showFBIcon):
  $facebookApi = $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
  if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
    $fbLoginUrl = '';
  } else {
    $fbLoginUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
            ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'facebook'), 'default', true);
  }
endif;
//TWITTER SERVICE.... 
$showTwitterIcon = true;
$settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
if (!function_exists('mb_strlen')) {
  $showTwitterIcon = false;
}
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('twitter.enable', Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable == 'publish' ? 1 : 0) || empty($settings['secret']) || empty($settings['key'])) {
  $showTwitterIcon = false;
}

if ($showTwitterIcon) :
  //$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
  $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
  $twitterOauth = $twitter = $Api_twitter->getApi();
  if ($twitterOauth &&
          $Api_twitter->isConnected()) {
    $twitterLoginUrl = '';
  } else {
    $twitterLoginUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
            ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'twitter'), 'default', true);
  }
endif;

//LINKEDIN SERVICE.... 
$showLinkedinIcon = true;
$linkedin_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
$linkedin_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
$linkedin_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.enable', 0);
if (empty($linkedin_apikey) || empty($linkedin_secret) || empty($linkedin_enable)) {
  $showLinkedinIcon = false;
}

if ($showLinkedinIcon):
  $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
  $OBJ_linkedin = $Api_linkedin->getApi();     
  if ($OBJ_linkedin && $Api_linkedin->isConnected()) {
    $linkedinLoginUrl = '';
  } else {
    $linkedinLoginUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
            ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'linkedin'), 'default', true);
  }
endif;
?>
<?php if ($showLinkedinIcon) : ?>
  <span id="composer_linkedin_toggle" class="composer_linkedin_toggle" href="javascript:void(0);" style="display: block;">    
    <input type="checkbox" id="compose-linkedin-form-input" class="compose-form-input-linkedin" name="post_to_linkedin" style="display: none;" value='1'/>    
    <span for="compose-linkedin-form-input" class="cm-icons cm-icon-linkedin"></span>
  </span>
  <script> 
    var linkedin_loginURL = '<?php echo $linkedinLoginUrl; ?>';
  </script>
<?php endif; ?> 
<?php if ($showTwitterIcon) : ?>
  <span id="composer_twitter_toggle" class="composer_twitter_toggle" href="javascript:void(0);" style="display: block;">    
    <input type="checkbox" id="compose-twitter-form-input" class="compose-form-input-twitter" name="post_to_twitter" style="display: none;" value='1'/>    
    <span for="compose-twitter-form-input" class="cm-icons cm-icon-twitter"></span>
  </span>
  <script> 
    var twitter_loginURL = '<?php echo $twitterLoginUrl; ?>'; 
  </script>
<?php endif; ?>
<?php if ($showFBIcon) : ?>
  <span id="composer_facebook_toggle" class="composer_facebook_toggle" href="javascript:void(0);" style="display: block;">    
    <input type="checkbox" id="compose-facebook-form-input" class="compose-form-input-facebook" name="post_to_facebook" style="display: none;" value='1'/>    
    <span for="compose-facebook-form-input" class="cm-icons cm-icon-facebook"></span>
  </span>
  <script>
    var fb_loginURL = '<?php echo $fbLoginUrl; ?>';
  </script>
<?php endif; ?>
