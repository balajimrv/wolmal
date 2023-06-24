<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: confirm.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<h2>
  <?php echo $this->translate("Thanks for joining!") ?>
</h2>

<p>
  <?php
  if (!($this->verified || $this->approved)) {
    echo $this->translate("Welcome! A verification message has been sent to your email address with instructions on how to activate your account. Once you have clicked the link provided in the email and we have approved your account, you will be able to sign in.");
  } else if (!$this->verified) {
    echo $this->translate("Welcome! A verification message has been sent to your email address with instructions for activating your account. Once you have activated your account, you will be able to sign in.");
  } else if (!$this->approved) {
    echo $this->translate("Welcome! Once we have approved your account, you will be able to sign in.");
  }
  ?>
</p>

<br />

<h3>
  <a href="<?php echo $this->url(array(), 'user_login', true) ?>"><?php echo $this->translate("OK, thanks!") ?></a>
</h3>