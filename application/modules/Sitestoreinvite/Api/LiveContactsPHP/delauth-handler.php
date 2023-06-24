<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delauth-hendler.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
/**
 * This store handles the 'delauth' Delegated Authentication action.
 * When you create a Windows Live application, you must specify the URL 
 * of this handler store.
 */
// Load common settings.  For more information, see settings.php.
// Initialize the WindowsLiveLogin module.
$wll = WindowsLiveLogin::initFromXml($KEYFILE);
$wll->setDebug($DEBUG);

// Extract the 'action' parameter, if any, from the request.
$action = @$_REQUEST['action'];

if ($action == 'delauth') {
  $consent = $wll->processConsent($_REQUEST);

// If a consent token is found, store it in the cookie that is 
// configured in the settings.php file and then redirect to 
// the main store.
  if ($consent) {
    setcookie($COOKIE, $consent->getToken(), $COOKIETTL);
  } else {
    setcookie($COOKIE);
  }
  $session->redirect = 0;
  header("Location: $INDEX");
}
?>
