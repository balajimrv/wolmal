<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: googleapi.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//CHECKING FOR AUTHENTICATION.
function GoogleContactsAuth($token) {
        
  include_once('Zend/Loader.php');
  $GoogleContactsService = 'cp';
  try {
  $GoogleContactsClient   =  Zend_Gdata_AuthSub::getHttpClient(trim($token));
	//Zend_Gdata_ClientLogin::getHttpClient($GoogleContactsEmail,$GoogleContactsPass, $GoogleContactsService);
	return $GoogleContactsClient;
  }catch (Exception $e) {
	echo Zend_Registry::get('Zend_Translate')->_('You are not authorize to access this location.');
  }

}

//GETTING ALL GOOGLE CONTACTS.
function GoogleContactsAll($GoogleContactsClient) { 
  $scope          = "http://www.google.com/m8/feeds/contacts/default/";
 try {
	$gdata          = new Zend_Gdata($GoogleContactsClient);
	
	$query          = new Zend_Gdata_Query($scope.'full');
	
	$query->setMaxResults(10000);
	
	$feed           = $gdata->retrieveAllEntriesForFeed($gdata->getFeed($query));
	$contactMail = '';
	$contactName = '';
	$arrContactsData = array();
  if (!empty($feed)) { 
		foreach ($feed as $entry) {
      $contactMail = '';
      $contactName = '';
			$contactName = $entry->title->text;

			$ext = $entry->getExtensionElements();

			foreach($ext as $extension) {

			if($extension->rootElement == "email") {

				$attr=$extension->getExtensionAttributes();

				$contactMail = $attr['address']['value'];

			}
			if($contactName=="") {
				$contactName = $contactMail;
			}

			}
      $email_temp = explode('@', $contactMail);
      if (!empty($email_temp['0'])) {
        $arrContactsData['contactMail'] = $contactMail;

        $arrContactsData['contactName'] = $contactName;
        if (!empty($contactMail)) {
          $arrContacts[] = $arrContactsData;
        }
      }
		}
  }
  
  if (!empty($arrContacts)) {
		sort($arrContacts);
	}

	return $arrContacts;
  }
  catch (Exception $e) {
   echo Zend_Registry::get('Zend_Translate')->_('Your contacts could not be retrive right now .Please try again after some time..');die;
 }
}

?>