<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_LoginOrSignupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Do not show if logged in
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->setNoRender();
      return;
    }

    // Display form
    $form = $this->view->form = new Sitemobile_modules_User_Form_Login();
    $form->setTitle(null)->setDescription(null);
    $form->getElement('email')->setAttrib('autofocus',null);

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.login.ajax', 1) && !Engine_Api::_()->sitemobile()->isApp()) {
      $form->setAttrib('data-ajax', 'false');
    }
    // Facebook login
    if ('none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
      $form->removeElement('facebook');
    }

    $sitemobileLoginSignup = Zend_Registry::isRegistered('sitemobileLoginSignup') ? Zend_Registry::get('sitemobileLoginSignup') : null;

    // Check for recaptcha - it's too fat
    $this->view->noForm = false;
    if (($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha &&
            $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha) {
      $this->view->noForm = true;
      $form->removeElement('email');
      
      $form->removeElement('password');
      $form->removeElement('captcha');
      $form->removeElement('submit');
//      $form->removeElement('remember');
//      $form->removeElement('facebook');
//      $form->removeElement('twitter');
      $form->removeDisplayGroup('buttons');
    }

    if (empty($sitemobileLoginSignup)) {
      return $this->setNoRender();
    }
  }

  public function getCacheKey() {
    return false;
  }

}