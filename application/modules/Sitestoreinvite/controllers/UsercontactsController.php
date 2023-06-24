<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UsercontactsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_UsercontactsController extends Core_Controller_Action_Standard {

  public function init() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    // PACKAGE BASE PRIYACY START

    $store_id = $sitestore_id = $this->_getParam('sitestore_id', null);

    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreinvite")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'invite');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
   // }
    // PACKAGE BASE PRIYACY END
    //$sitestore_id = $this->_getParam('sitestore_id', null);
    //if (!empty($sitestore_id)) {
     // $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestore_id);
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'invite');
      if (empty($isManageAdmin)) {
        return $this->_forward('notfound', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

     //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        return $this->_forward('notfound', 'error', 'core');
      }
    }
  }

  //FUNCTION FOR GETTING GOOGLE CONTACTS OF THE REQUESTED USER.
  public function getgooglecontactsAction() {
    global $sitestoreinvite_getTaskType;
    ini_set('display_errors', FALSE);
    error_reporting(0);
    $this->_helper->layout->disableLayout();
    $getPackageInvite = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreinvite');
    include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/googleapi.php';
    $session = new Zend_Session_Namespace();
    $session->windowlivemsnredirect = 0;
    $session->yahooredirect = 0;
    $session->googleredirect = 1;
    $session->aolredirect = 0;
    $viewer = Engine_Api::_()->user()->getViewer();

    $user_id = $_GET['user_id'];
    $sitestore_id = $_GET['sitestore_id'];
    if (!empty($user_id) && !empty($sitestore_id)) {
      $keep_original_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']
              . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                  'sitestore_id' => $sitestore_id,
                  'user_id' => $user_id
                      ), 'sitestoreinvite_invite', true);
    }
    $gmail_RedirectURL_Confirm = Engine_Api::_()->getApi('settings', 'core')->getSetting('gmail.redirecturl.confirm', 0);
   // the domain that you entered when registering your application for redirecting from google site.
   //$keep_original_url = $this->_getParam('redirect_uri', null);
    
    if ($gmail_RedirectURL_Confirm)
      $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl().'/seaocore/usercontacts/getgooglecontacts';
    else    // the domain that you entered when registering your application for redirecting from google site.
    if (!empty($user_id) && !empty($sitestore_id)) {
      $callback = $keep_original_url;
    }
    //HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO GOOGLE SITE.FOR GETTING TOKEN.
    
    if (!empty($keep_original_url)) {
     $session->keep_original_url = $keep_original_url;	
   }
	
	 $this->view->moduletype = '';
	
	//HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO GOOGLE SITE.FOR GETTING TOKEN.
    if (empty($_POST['task']) && !empty($keep_original_url)) {
      //CHECK IF ADMIN HAS SET THE THE GOOGLE API KEYS THERE:
      $google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
      if (!empty($google_Apikey)) {
        $google_redirect_URL = 'https://accounts.google.com/o/oauth2/auth?client_id=' . $google_Apikey . '&redirect_uri=' . urlencode($callback) . '&scope=' . urlencode('https://www.google.com/m8/feeds') . '&response_type=token';
      }
      else {
         $scope  = "http://www.google.com/m8/feeds/contacts/default/";
        $google_redirect_URL = Zend_Gdata_AuthSub::getAuthSubTokenUri($callback,urlencode($scope),
  0, 1);
      }
        
      if (!empty($sitestoreinvite_getTaskType) && !empty($getPackageInvite)) {
        header('location: ' . $googleUri);
      }
    }    
    
    
   

    //IF THE TASK IS TO SHOWING THE LIST OF FRIENDS.
    if (!empty($_POST['task']) && $_POST['task'] == 'get_googlecontacts') {
      //IF WE GET THE TOKEN MEANS GOOGLE HAS RESPOND SUCCESSFULLY.THIS IS ONE TIME USES TOKEN
      if (!empty($_POST['token'])) {
        $token = urldecode($_POST['token']);

        //CHECKING THE AUTHENTICITY OF REQUESTED USER EITHER THIS TOKEN IS VALID OR NOT.
        $result = GoogleContactsAuth($token);

        if (!empty($result)) {
          $session->googleredirect = 0;
          //FETCHING THE ALL GOOGLE CONTACTS OF THIS USER.
          $GoogleContacts = GoogleContactsAll($result);
          //NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
          if (!empty($GoogleContacts)) {
            $SiteNonSiteFriends = $this->parseUserContacts($GoogleContacts);

            if (!empty($SiteNonSiteFriends[0])) {
              $this->view->task = 'show_sitefriend';
              $this->view->addtofriend = $SiteNonSiteFriends[0];
            }
            if (!empty($SiteNonSiteFriends[1])) {
              $this->view->addtononfriend = $SiteNonSiteFriends[1];
            }
          }
          if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
            $this->view->errormessage = true;
          }
        }
      }
    }
    else {       
        $this->view->redirectToOrigine = $session->keep_original_url;
    }
  }

  //FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function getyahoocontactsAction() {
    global $sitestoreinvite_getTaskType;
    ini_set('display_errors', FALSE);
    error_reporting(0);

    $session = new Zend_Session_Namespace();
    $this->_helper->layout->disableLayout();
    $user_id = $_GET['user_id'];
    $sitestore_id = $_GET['sitestore_id'];
    $getPackageInvite = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreinvite');
     // the domain that you entered when registering your application for redirecting from google site.
    if (!empty($user_id) && !empty($sitestore_id)) {
      $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']
              . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                  'sitestore_id' => $sitestore_id,
                  'user_id' => $user_id
                      ), 'sitestoreinvite_invite', true);
    }
    include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/yahoo/getreqtok.php';
    //STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM YAHOO.
    if (empty($_POST['oauth_verifier'])) {
      $session->windowlivemsnredirect = 0;
      $session->yahooredirect = 1;
      $session->googleredirect = 0;
      $session->aolredirect = 0;
      // Get the request token using HTTP GET and HMAC-SHA1 signature
      $retarr = get_request_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback, false, true, true);

      if (!empty($retarr)) {
        unset($session->oauth_token_secret);
        unset($session->oauth_token);
        $session->oauth_token_secret = $retarr[3]['oauth_token_secret'];
        $session->oauth_token = $retarr[3]['oauth_token'];
        $redirecturl = urldecode($retarr[3]['xoauth_request_auth_url']);
        if (!empty($sitestoreinvite_getTaskType) && !empty($getPackageInvite)) {
          header('location: ' . $redirecturl);
        }
      }
    }
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
    else if (!empty($_POST['oauth_verifier'])) {
      $session->redirect = 0;
      $request_token = $session->oauth_token;
      $request_token_secret = $session->oauth_token_secret;
      $oauth_verifier = $_POST['oauth_verifier'];
      //STEP:3 AFTER GETTING OAUTH VERIFIER AND OTHER TOKENS WE WILL AGAIN CALL YAHOO API TO GET OAUTH VERIFY SUCCESS TOKEN.
      $retarr = get_access_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $request_token, $request_token_secret, $oauth_verifier, false, true, true);

      if (!empty($retarr)) {
        $guid = $retarr[3]['xoauth_yahoo_guid'];
        $access_token = urldecode($retarr[3]['oauth_token']);
        $access_token_secret = $retarr[3]['oauth_token_secret'];
        // Call Contact API
        $YahooContacts = callcontact(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $guid, $access_token, $access_token_secret, false, true);

        //NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
        if (!empty($YahooContacts)) {
          $SiteNonSiteFriends = $this->parseUserContacts($YahooContacts);

          if (!empty($SiteNonSiteFriends[0])) {
            $this->view->task = 'show_sitefriend';
            $this->view->addtofriend = $SiteNonSiteFriends[0];
          }
          if (!empty($SiteNonSiteFriends[1])) {
            $this->view->addtononfriend = $SiteNonSiteFriends[1];
          }
        }
        if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
          $this->view->errormessage = true;
        }
      }
    }
  }

  //FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
  public function getwindowlivecontactsAction() {
    global $sitestoreinvite_getTaskType;
    ini_set('display_errors', FALSE);
    error_reporting(0);
    $session = new Zend_Session_Namespace();
    $session->windowlivemsnredirect = 1;
    $session->yahooredirect = 0;
    $session->googleredirect = 0;
    $session->aolredirect = 0;
    $this->_helper->layout->disableLayout();
    $getPackageInvite = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreinvite');

    $invitestore_id = $this->_getParam('sitestore_id', null);
    $invitestore_userid = $this->_getParam('user_id', null);
    $inviteUrl = '';
    if (empty($session->user_id) && empty($session->sitestore_id) && empty($_GET['task'])) {
      $session->user_id = $invitestore_userid;
      $session->sitestore_id = $invitestore_id;
    } else if (!empty($session->user_id) && !empty($session->sitestore_id) && empty($invitestore_id) && empty($invitestore_userid)) {
      $invitestore_userid = $session->user_id;
      $invitestore_id = $session->sitestore_id;
    }

    // the domain that you entered when registering your application for redirecting from google site.
    if (!empty($invitestore_userid) && !empty($invitestore_id)) {
      $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']
              . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                  'sitestore_id' => $invitestore_id,
                  'user_id' => $invitestore_userid
                      ), 'sitestoreinvite_invite', true);
    }

    $returnurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/sitestoreinvites/usercontacts/getwindowlivecontacts';

    // the domain that you entered when registering your application for redirecting from google site.
    include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/LiveContactsPHP/contacts_fn.php';
    $cookie = @$_COOKIE[$COOKIE];
    //WE WILL INCLUDE THIS FILE AFTER WE WILL GET BACK FROM WINDOW LIVE SITE HAVING SUCCESSFULL AUTHENTICATION.THIS FILE IS USED TO SET THE COOKIES AFTER GETTING SUCCESSFULL AUTHENTICATION AND THEN REDIRECTING TO AT CALLBACK URL.
    if ($session->redirect) {
      include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/LiveContactsPHP/delauth-handler.php';
    }
    //initialize Windows Live Libraries
    $wll = WindowsLiveLogin::initFromXml($KEYFILE);
    // If the raw consent token has been cached in a site cookie, attempt to
    // process it and extract the consent token.
    $token = null;
    if ($cookie) {
      $token = $wll->processConsentToken($cookie);
    }
    //Check if there's consent and, if not, redirect to the login store
    if ($token && !$token->isValid()) {
      $token = null;
    }
    if ($token == null) {
      $session->redirect = 1;
      $consenturl = $wll->getConsentUrl($OFFERS);
      if (!empty($sitestoreinvite_getTaskType) && !empty($getPackageInvite)) {
        header('Location:' . $consenturl);
      }
    }

    //HERE WE ARE CHECKING IF THE VALID TOKEN IS ALREADY COOKIED AND ACTIVE AND REQUEST IS NOT AJAX THEN WE WILL REDIRECT IT TO CALLBACK URL.
    if ($token && empty($_GET['task'])) {
      unset($session->user_id);
      unset($session->sitestore_id);
      if (!empty($sitestoreinvite_getTaskType) && !empty($getPackageInvite)) {
        header("Location: $callback");
      }
    }

    //IF REQUEST IS AJAX FOR SHOWING WINDOW LIVE CONTACTS.
    if ($token) {
      $WindowLiveContacts = get_people_array($token);
      $session->windowliveredirect = 0;
      //NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
      if (!empty($WindowLiveContacts)) {
        $SiteNonSiteFriends = $this->parseUserContacts($WindowLiveContacts);
        if (!empty($SiteNonSiteFriends[0])) {
          $this->view->task = 'show_sitefriend';
          $this->view->addtofriend = $SiteNonSiteFriends[0];
        }
        if (!empty($SiteNonSiteFriends[1])) {
          $this->view->addtononfriend = $SiteNonSiteFriends[1];
        }
      }
      if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
        $this->view->errormessage = true;
      }
    }
  }

  //FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
  public function getaolcontactsAction() {
    ini_set('display_errors', FALSE);
    error_reporting(0);
    $this->_helper->layout->disableLayout();
    $session = new Zend_Session_Namespace();
    $session->windowlivemsnredirect = 0;
    $session->yahooredirect = 0;
    $session->googleredirect = 0;
    $session->aolredirect = 1;
    include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/aolapi.php';
    if (!empty($session->aol_email)) {
      $AolContacts = getaolcontacts($session->aol_email, $session->aol_password, false);
      //NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
      if (!empty($AolContacts)) {
        $SiteNonSiteFriends = $this->parseUserContacts($AolContacts);
        if (!empty($SiteNonSiteFriends[0])) {
          $this->view->task = 'show_sitefriend';
          $this->view->addtofriend = $SiteNonSiteFriends[0];
        }
        if (!empty($SiteNonSiteFriends[1])) {
          $this->view->addtononfriend = $SiteNonSiteFriends[1];
        }
      }
      if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
        $this->view->errormessage = true;
      }
    }
  }

  //FUNTION FOR GETTING USERNAME AND PASSWORD OF AOL MAIL.
  public function aolloginAction() {
    global $sitestoreinvite_getTaskType;
    $this->_helper->layout->disableLayout();
    $this->view->form = $form = new Sitestoreinvite_Form_Aollogin();
    $getPackageInvite = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreinvite');
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      include_once APPLICATION_PATH . '/application/modules/Sitestoreinvite/Api/aolapi.php';
      $values = $form->getValues();
      $session = new Zend_Session_Namespace();
      $session->windowlivemsnredirect = 0;
      $session->yahooredirect = 0;
      $session->googleredirect = 0;
      $session->aolredirect = 1;
      $invitestore_id = $this->_getParam('sitestore_id', null);
      $invitestore_userid = $this->_getParam('user_id', null);
      $loginsuccess = getaolcontacts($values['email'], $values['password'], true);
      if ($loginsuccess) {
        $session->aol_email = $values['email'];
        $session->aol_password = $values['password'];
        // the domain that you entered when registering your application for redirecting from google site.
       
        if (!empty($invitestore_userid) && !empty($invitestore_id)) {
          $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']
                  . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                      'sitestore_id' => $invitestore_id,
                      'user_id' => $invitestore_userid
                          ), 'sitestoreinvite_invite', true);
        }
        if (!empty($sitestoreinvite_getTaskType) && !empty($getPackageInvite)) {
          header('location:' . $callback);
        }
      } else {
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Incorrect Username or Password");
      }
    }
  }

  //FUNCTION FOR PARSING USER CONTACTS IN 2 PARTS SITE MEMBERS AND NONSITE MEMBERS.
  public function parseUserContacts($UserContacts) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $table_user = Engine_Api::_()->getitemtable('user');
    $tableName_user = $table_user->info('name');
    $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');
    $tableName_user_memberships = $table_user_memberships->info('name');
    $SiteNonSiteFriends[] = '';
    foreach ($UserContacts as $values) {
      $is_site_members = '';
      //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
      //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
      $SiteNonSiteFriends[1][] = $values;
    }
    $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
    return $result;
  }

  //FUNCTION FOR GETTING CSV FILE CONTACTS OF THE REQUESTED USER.
  function getcsvcontactsAction() {
    ini_set('display_errors', FALSE);
    error_reporting(0);
    $this->_helper->layout->disableLayout();
    $session = new Zend_Session_Namespace();
    $filebaseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/sitestoreinvite/csvfiles/';
    $validator = new Zend_Validate_EmailAddress();
    $validator->getHostnameValidator()->setValidateTld(false);
    //READING THE CSV FILE AND FINDING THE EMAIL FOR CORROSPONDING ROW.
    //WE ARE READING THE FILE FOR VERIOUS DELIMITERS TYPE.
    $probable_delimiters = array(",", ";", "|", " ");
    foreach ($probable_delimiters as $delimiter) {
      $fp = fopen($filebaseurl . $session->filename, 'r') or die("can't open file");
      $k = 0;
      while ($csv_line = fgetcsv($fp, 4096, $delimiter)) {
        for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
          if ($validator->isValid($csv_line[$i])) {
            $usercontacs_csv[$k]['contactMail'] = $csv_line[$i];
            $usercontacs_csv[$k]['contactName'] = $csv_line[$i];
            $k++;
            break;
          }
        }
      }
      if (!empty($usercontacs_csv[0]['contactMail'])) {
        break;
      }

      //CLOSING THE FILE AFTER READING.
      fclose($fp) or die("can't close file");
    }
    //AFTER READING THE FILE WE ARE UNLINKING THE FILE.
    $filebaseurl = APPLICATION_PATH . '/public/sitestoreinvite/csvfiles/' . $session->filename;
    @unlink($filebaseurl);
    unset($session->filename);
    if (!empty($usercontacs_csv)) {
      sort($usercontacs_csv);
      $SiteNonSiteFriends = $this->parseUserContacts($usercontacs_csv);
      if (!empty($SiteNonSiteFriends[0])) {
        $this->view->task = 'show_sitefriend';
        $this->view->addtofriend = $SiteNonSiteFriends[0];
      }
      if (!empty($SiteNonSiteFriends[1])) {
        $this->view->addtononfriend = $SiteNonSiteFriends[1];
      }
    }
    if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
      $this->view->errormessage = true;
    }
  }

  public function uploadsAction() {
    // Prepare
    if (empty($_FILES['Filedata'])) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File failed to upload. Check your server settings (such as php.ini max_upload_filesize).');
      return;
    }
    $session = new Zend_Session_Namespace();
    $session->filename = $_FILES['Filedata']['name'];
    $file_path = APPLICATION_PATH . '/public/sitestoreinvite/csvfiles';
    if (!is_dir($file_path) && !mkdir($file_path, 0777, true)) {
      //$filename = APPLICATION_PATH . "/application/languages/$localeCode/custom.csv";
      mkdir(dirname($file_path));
      chmod(dirname($file_path), 0777);
      touch($file_path);
      chmod($file_path, 0777);
    }
    else {
       chmod(dirname($file_path), 0777);
				touch($file_path);
				chmod($file_path, 0777);
    }

    // Prevent evil files from being uploaded
    $disallowedExtensions = array('php');
    if (in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File type or extension forbidden.');
      return;
    }

    $info = $_FILES['Filedata'];
    $targetFile = $file_path . '/' . $info['name'];
    $vals = array();

    if (file_exists($targetFile)) {
      $deleteUrl = $this->view->url(array('action' => 'delete')) . '?path=' . $file_path . '/' . $info['name'];
      $deleteUrlLink = '<a href="' . $this->view->escape($deleteUrl) . '">' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists. Please %s before trying to upload.", $deleteUrlLink);
      return;
    }

    if (!is_writable($file_path)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please CHMOD 0777 the public/admin directory.');
      return;
    }

    // Try to move uploaded file
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }

    $this->view->status = 1;
  }

}

?>