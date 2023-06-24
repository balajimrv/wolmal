<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_User_Plugin_Signup_Account extends User_Plugin_Signup_Account {

  protected $_formClass = 'Sitemobile_modules_User_Form_Signup_Account';

  public function getSession() {
    if (is_null($this->_session)) {
      $this->_session = new Zend_Session_Namespace('User_Plugin_Signup_Account');
      if (!isset($this->_session->active)) {
        $this->_session->active = true;
      }
    }
    return $this->_session;
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request) {
    $postData = $request->getPost();
    $useAntiSpam = !(!empty($_SESSION['facebook_signup']) ||
            !empty($_SESSION['twitter_signup']) ||
            !empty($_SESSION['janrain_signup']));
    if ('' != $postData['email'] && $useAntiSpam) {
      //bot attack!
      //Bot signup was prevented by plugin
      exit(1);
    }

    // Form was valid
    if ($this->getForm()->isValid($postData)) {
      $values = $this->getForm()->getValues();
      if ($useAntiSpam) {
        $antispam = $_SESSION['signup_email'];
        $values['email'] = $values[$antispam];
        unset($values[$antispam]);
        unset($_SESSION['signup_email']);
      }
      $this->getSession()->data = $values;
      $this->setActive(false);
      $this->onSubmitIsValid();
      return true;
    }

    // Form was not valid
    else {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }
  }

}
