<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photo.php 10044 2013-05-15 17:45:46Z andres $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_User_Plugin_Signup_Photo extends User_Plugin_Signup_Photo {

  public function getSession() {
    if (is_null($this->_session)) {
      $this->_session = new Zend_Session_Namespace('User_Plugin_Signup_Photo');
      if (!isset($this->_session->active)) {
        $this->_session->active = true;
      }
    }
    return $this->_session;
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request) {
    // Form was valid
    $skip = $request->getParam("skip");
    $uploadPhoto = $request->getParam("uploadPhoto");
    $finishForm = $request->getParam("nextStep");
    $photoIsRequired = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.photo');

    if ($this->getForm()->isValid($request->getPost()) &&
            $skip != "skipForm" &&
            $uploadPhoto == true &&
            $finishForm != "finish") {
      Engine_Api::_()->sitemobile()->autoRotationImage($_FILES['Filedata']);
    } else if ($photoIsRequired  &&
            $finishForm == "finish" &&
            !isset($_SESSION['TemporaryProfileImg']) && !$uploadPhoto) {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();

      return false;
    }


    parent::onSubmit($request);
  }

}
