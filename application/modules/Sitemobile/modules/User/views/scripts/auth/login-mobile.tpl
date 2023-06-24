<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: login.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="ui-login-page-content-wrap">
  <div class="ui-page-content ui-login-page-content">
    <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?> 
      <h3 class="sm-ui-cont-cont-heading">
        <?php echo $this->translate('Sign In or %1$sJoin%2$s', '<a href="' . $this->url(array(), "user_signup") . '">', '</a>'); ?>
      </h3>
    <?php endif?>
    <?php echo $this->form->render($this) ?>
    
    <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?> 
      <p><b><?php echo $this->translate('New to %s?', Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title'))); ?></b></p>
      <a class="ui-btn-success signup-btn"  data-ajax="<?php  echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.spam.signup', 0) && !Engine_Api::_()->sitemobile()->isApp()  ? "false" : "true";?>" href="<?php  echo $this->url(array(), "user_signup") ?>"><?php  echo $this->translate('Create New Account'); ?></a>
    <?php endif?>
    <div class="ui-login-page-btm-links">
      <a href='<?php echo $this->url(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true) ?>'><?php echo $this->translate('Forgot Password?') ?></a>
      <span>&#183;</span>
      <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitefaq')): ?>
        <a href='<?php echo $this->url(array('action' => 'home'), 'sitefaq_general', true) ?>'><?php echo $this->translate('Help') ?></a>
      <?php else:?>
        <a href='<?php echo $this->url(array('module' => 'core', 'controller' => 'help', 'action' => 'contact'), 'default', true) ?>'><?php echo $this->translate('Help') ?></a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<style type="text/css">
  #facebook-element a:after{
  content: " <?php echo $this->translate("Login with Facebook") ?>";
  }
  #twitter-element a:after{
  content: "<?php echo $this->translate("Login with Twitter") ?>";
  }
</style>
<?php endif;?>
<script type="text/javascript">
    sm4.core.runonce.add(function() { 
       if ($.mobile.activePage.find('#facebook-element').length)
          $.mobile.activePage.find('#facebook-element').find('a').attr('data-ajax', 'false');
       if ( $.mobile.activePage.find('#twitter-element').length) 
          $.mobile.activePage.find('#twitter-element').find('a').attr('data-ajax', 'false');
      
    })
    
</script> 