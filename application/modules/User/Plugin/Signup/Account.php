<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 10099 2013-10-19 14:58:40Z ivan $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Plugin_Signup_Account extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';
  protected $_formClass = 'User_Form_Signup_Account';
  protected $_script = array('signup/form/account.tpl', 'user');
  protected $_adminFormClass = 'User_Form_Admin_Signup_Account';
  protected $_adminScript = array('admin-signup/account.tpl', 'user');
  public $email = null;

  public function onView()
  {
    if (!empty($_SESSION['facebook_signup']) ||
      !empty($_SESSION['twitter_signup']) ||
      !empty($_SESSION['janrain_signup'])) {

      // Attempt to preload information
      if (!empty($_SESSION['facebook_signup'])) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($facebook && $settings->core_facebook_enable) {
            // Get email address
            $apiInfo = $facebook->api('/me?fields=name,gender,email,locale');
            // General
            $form = $this->getForm();

            if (($emailEl = $form->getElement('email')) && !$emailEl->getValue()) {
              $emailEl->setValue($apiInfo['email']);
            }
            if (($usernameEl = $form->getElement('username')) && !$usernameEl->getValue()) {
              $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $apiInfo['name']));
            }

            // Locale
            $localeObject = new Zend_Locale($apiInfo['locale']);
            if (($localeEl = $form->getElement('locale')) && !$localeEl->getValue()) {
              $localeEl->setValue($localeObject->toString());
            }
            if (($languageEl = $form->getElement('language')) && !$languageEl->getValue()) {
              if (isset($languageEl->options[$localeObject->toString()])) {
                $languageEl->setValue($localeObject->toString());
              } else if (isset($languageEl->options[$localeObject->getLanguage()])) {
                $languageEl->setValue($localeObject->getLanguage());
              }
            }
          }
        } catch (Exception $e) {
          // Silence?
        }
      }

      // Attempt to preload information
      if (!empty($_SESSION['twitter_signup'])) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if ($twitter && $settings->core_twitter_enable) {
            $accountInfo = $twitter->account->verify_credentials();

            // General
            $this->getForm()->populate(array(
              //'email' => $apiInfo['email'],
              'username' => preg_replace('/[^A-Za-z]/', '', $accountInfo->name), // $accountInfo->screen_name
              // 'timezone' => $accountInfo->utc_offset, (doesn't work)
              'language' => $accountInfo->lang,
            ));
          }
        } catch (Exception $e) {
          // Silence?
        }
      }

      // Attempt to preload information
      if (!empty($_SESSION['janrain_signup']) &&
        !empty($_SESSION['janrain_signup_info'])) {
        try {
          $form = $this->getForm();
          $info = $_SESSION['janrain_signup_info'];

          if (($emailEl = $form->getElement('email')) && !$emailEl->getValue() && !empty($info['verifiedEmail'])) {
            $emailEl->setValue($info['verifiedEmail']);
          }
          if (($emailEl = $form->getElement('email')) && !$emailEl->getValue() && !empty($info['email'])) {
            $emailEl->setValue($info['email']);
          }

          if (($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() && !empty($info['preferredUsername'])) {
            $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $info['preferredUsername']));
          }
        } catch (Exception $e) {
          // Silence?
        }
      }
    }
  }

  public function onProcess()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $random = ($settings->getSetting('user.signup.random', 0) == 1);
    $emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
    if ($emailadmin) {
      // the signup notification is emailed to the first SuperAdmin by default
      $users_table = Engine_Api::_()->getDbtable('users', 'user');
      $users_select = $users_table->select()
        ->where('level_id = ?', 1)
        ->where('enabled >= ?', 1);
      $super_admin = $users_table->fetchRow($users_select);
    }
    $data = $this->getSession()->data;

    // Add email and code to invite session if available
    $inviteSession = new Zend_Session_Namespace('invite');
    if (isset($data['email'])) {
      $inviteSession->signup_email = $data['email'];
    }
    if (isset($data['code'])) {
      $inviteSession->signup_code = $data['code'];
    }

    if ($random) {
      $data['password'] = Engine_Api::_()->user()->randomPass(10);
    }

    if (!empty($data['language'])) {
      $data['locale'] = $data['language'];
    }

    // Create user
    // Note: you must assign this to the registry before calling save or it
    // will not be available to the plugin in the hook
    $this->_registry->user = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
    $user->setFromArray($data);
    $user->save();

    Engine_Api::_()->user()->setViewer($user);
	
	
    // Increment signup counter
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');
	
	
	//Award Assign
	if ($data['code']!="") {
	  $new_signup_user_id = $user->getIdentity();
	  $user_tbl = Engine_Api::_()->getDbTable('users', 'user');
	  
	  $signup_code = $data['code'];
	  
	  //Get User ID
	  $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
	  $user_id = $codes_tbl->getUserId($signup_code);
	  
	  //$user_tbl = Engine_Api::_()->getDbTable('users', 'user');
	  $select_row = $user_tbl->select('level_id')->where('user_id = ?', $user_id);
	  $query = $user_tbl->fetchRow($select_row);
      $level_id = $query['level_id'];
	  
	  	$rso = 7;
		$cso = 8;
		$cso_plus = 9;
		$wac = 10;
		$wbc = 11;
		$wcc = 12;
		$wdc = 13;
		$wec = 14;
		$rec = 15;
		$efc = 16;
		
	    if($level_id == $rso){ //5000 AB
			$reward = 5000;
		}else if($level_id == $cso){//10000 AB
			$reward = 10000;
		}else if($level_id == $cso_plus){//15000 AB
			$reward = 15000;
		}else if($level_id == $rec){//20000 AB
			$reward = 20000;
		}else if($level_id == $efc){//25000 AB
			$reward = 25000;
		}else if($level_id == $wac){//30000 AB
			$reward = 30000;
		}else if($level_id == $wbc){//35000 AB
			$reward = 35000;
		}else if($level_id == $wcc){//40000 AB
			$reward = 40000;
		}else if($level_id == $wdc){//50000 AB
			$reward = 50000;
		}else if($level_id == $wec){//100000 AB
			$reward = 100000;
		}
		
		$userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($user_id);
		$userpoints['userpoints_count'];
		$userpoints['userpoints_totalearned'];
		$userpoints_count = $userpoints['userpoints_count']+$reward;
		$userpoints_totalearned = $userpoints['userpoints_totalearned']+$reward;
		
		Engine_Api::_()->getDbtable('points', 'activitypoints')->update(array('userpoints_count' => $userpoints_count, 'userpoints_totalearned' => $userpoints_totalearned), array('userpoints_user_id =?' => $user_id));
		
		
		//Insert transaction details into semods_uptransactions table
		$transaction_text = "Handshake Reward from (User ID:<a href='https://wolmal.com/profile/".$new_signup_user_id."' target='_blank'>$new_signup_user_id</a>)";
		
		
		$transaction_id = Engine_Api::_()->getDbTable('transactions','activitypoints')->add(
														$user_id,
														1,
														1,  // cat
														0,
														$transaction_text,
														$reward
                                                   );
		
    }

    if ($user->verified && $user->enabled) {
      // Create activity for them
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
      // Set user as logged in if not have to verify email
      Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
    }

    $mailType = null;
    $mailParams = array(
      'host' => $_SERVER['HTTP_HOST'],
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
    );

    // Add password to email if necessary
    if ($random) {
      $mailParams['password'] = $data['password'];
    }

    // Mail stuff
    switch ($settings->getSetting('user.signup.verifyemail', 0)) {
      case 0:
        // only override admin setting if random passwords are being created
        if ($random) {
          $mailType = 'core_welcome_password';
        }
        if ($emailadmin) {
          $mailAdminType = 'notify_admin_user_signup';
          $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
          $date = new DateTime("now", new DateTimeZone($siteTimezone));
          $mailAdminParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => $date->format('F j, Y, g:i a'),
            'recipient_title' => $super_admin->displayname,
            'object_title' => $user->displayname,
            'object_link' => $user->getHref(),
          );
        }
        break;

      case 1:
        // send welcome email
        $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
        if ($emailadmin) {
          $mailAdminType = 'notify_admin_user_signup';
          $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
          $date = new DateTime("now", new DateTimeZone($siteTimezone));
          $mailAdminParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => $date->format('F j, Y, g:i a'),
            'recipient_title' => $super_admin->displayname,
            'object_title' => $user->getTitle(),
            'object_link' => $user->getHref(),
          );
        }
        break;

      case 2:
        // verify email before enabling account
        $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
        $verify_row = $verify_table->createRow();
        $verify_row->user_id = $user->getIdentity();
        $verify_row->code = md5($user->email
          . $user->creation_date
          . $settings->getSetting('core.secret', 'staticSalt')
          . (string) rand(1000000, 9999999));
        $verify_row->date = $user->creation_date;
        $verify_row->save();

        $mailType = ($random ? 'core_verification_password' : 'core_verification');

        $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'verify',
          'email' => $user->email,
          'verify' => $verify_row->code
          ), 'user_signup', true);

        if ($emailadmin) {
          $mailAdminType = 'notify_admin_user_signup';

          $mailAdminParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => date("F j, Y, g:i a"),
            'recipient_title' => $super_admin->displayname,
            'object_title' => $user->getTitle(),
            'object_link' => $user->getHref(),
          );
        }
        break;

      default:
        // do nothing
        break;
    }

    if (!empty($mailType)) {
      $this->_registry->mailParams = $mailParams;
      $this->_registry->mailType = $mailType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }

    if (!empty($mailAdminType)) {
      $this->_registry->mailAdminParams = $mailAdminParams;
      $this->_registry->mailAdminType = $mailAdminType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }

    // Attempt to connect facebook
    if (!empty($_SESSION['facebook_signup'])) {
      try {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if ($facebook && $settings->core_facebook_enable) {
          $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            //'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        }
      } catch (Exception $e) {
        // Silence
        if ('development' == APPLICATION_ENV) {
          echo $e;
        }
      }
    }

    // Attempt to connect twitter
    if (!empty($_SESSION['twitter_signup'])) {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if ($twitter && $twitterOauth && $settings->core_twitter_enable) {
          $accountInfo = $twitter->account->verify_credentials();
          $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $accountInfo->id,
            'twitter_token' => $twitterOauth->getToken(),
            'twitter_secret' => $twitterOauth->getTokenSecret(),
          ));
        }
      } catch (Exception $e) {
        // Silence?
        if ('development' == APPLICATION_ENV) {
          echo $e;
        }
      }
    }

    // Attempt to connect twitter
    if (!empty($_SESSION['janrain_signup'])) {
      try {
        $janrainTable = Engine_Api::_()->getDbtable('janrain', 'user');
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $info = $_SESSION['janrain_signup_info'];
        if ($settings->core_janrain_enable) {
          $janrainTable->insert(array(
            'user_id' => $user->getIdentity(),
            'identifier' => $info['identifier'],
            'provider' => $info['providerName'],
            'token' => (string) @$_SESSION['janrain_signup_token'],
          ));
        }
      } catch (Exception $e) {
        // Silence?
        if ('development' == APPLICATION_ENV) {
          echo $e;
        }
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    if ($values['inviteonly'] == 1) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }
}
