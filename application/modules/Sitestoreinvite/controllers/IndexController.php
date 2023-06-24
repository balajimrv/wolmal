<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_IndexController extends Core_Controller_Action_Standard {

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
    //}
    // PACKAGE BASE PRIYACY END

    //$sitestore_id = $this->_getParam('sitestore_id', null);
    //if (!empty($sitestore_id)) {
      //$sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestore_id);
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'invite');
      if (empty($isManageAdmin)) {
        return $this->_forward('notfound', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

			$canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($canEdit)) {
        return $this->_forward('notfound', 'error', 'core');
      }
    }
  }

  // Function for view all store which are linked by "suggestion home" widget.
  public function friendsstoreinviteAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->invitestore_id = $invitestore_id = $this->_getParam('sitestore_id', null);
    $this->view->invitestore_userid = $invitestore_userid = $this->_getParam('user_id', null);
		//SAVING THE INVITED USERS IN DATABASE INVITE TABLE.
		$this->view->success_fbinvite = false;
		$fbinvited = $this->getRequest()->get('redirect_fbinvite',0);
		if ($fbinvited && $this->getRequest()->isPost()) {
			$facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
			$facebookInvite->seacoreInvite($this->getRequest()->get('ids'), 'facebook');
			$this->view->success_fbinvite = true; 
		}
		
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');
    if (empty($invitestore_id) || empty($invitestore_userid)) {
      $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
		$sitestore = Engine_Api::_()->getItem('sitestore_store', $invitestore_id);
    if ($invitestore_id) {
      $this->view->sitestore = $sitestore;
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
		if (empty($isManageAdmin)) {
			$this->view->can_edit = $can_edit = 0;
		} else {
			$this->view->can_edit = $can_edit = 1;
		}
		$webmail_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('storeinvite.show.webmail');
		$this->view->webmail_show = unserialize($webmail_show);
  }

  public function invitepreviewAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_name = $viewer->getTitle();

    $this->view->is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

    $storeinvite_id = $_GET['sitestore_id'];

    if ($storeinvite_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $storeinvite_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id),
                    ), 'sitestore_entry_view', true);

    //GETTING THE STORE PHOTO.
    $file = $sitestore->getPhotoUrl('thumb.icon');
    if (empty($file)) {
      $storephoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Sitestore/externals/images/nophoto_list_thumb_normal.png';
    } else {
      $storephoto_path = $file;
    }

    $inviteUrl_link = '<table><tr valign="top"><td style="color:#999999;font-size:11px;padding-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $storephoto_path . '" style="width:100px;"/>' . '</a>';
    //GETTING NO OF LIKES TO THIS STORE.
    $num_of_like = Engine_Api::_()->sitestore()->numberOfLike('sitestore_store', $sitestore->store_id);

    $this->view->site_title = $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
    $store_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $sitestore->title . '</a>';
    $store_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';

    $body = $inviteUrl_link . '<br/>' . $this->view->translate(array('%s person likes this', '%s people like this', $num_of_like), $this->view->locale()->toNumber($num_of_like)) . '</td><td>';

    // Initialize the strings to be sent in the mail
    $template_header = '';
    $template_footer = '';
    $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.site.title', $site_title);
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.font.color', "#79b4d4");
    $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
    $template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
    $inviter_name = $viewer->getTitle();
    $link = '<a href="' . $base_url . '">' . $base_url . '</a>';

    $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";

    $type = 'Sitestoreinvite_User_Invite';
    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
    if (null === $mailTemplate) {
      return;
    }
    //$subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');
    $recipientLanguage = $translate = Zend_Registry::get('Zend_Translate')->getLocale();

    // Get body
    $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
    $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

    if (!$bodyHtmlTemplate && !$bodyTextTemplate) {
      throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
    }

    // Get headers and footers
    $headerPrefix = '_EMAIL_HEADER_' . '';
    $footerPrefix = '_EMAIL_FOOTER_' . '';
    $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY', $recipientLanguage);
    $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY', $recipientLanguage);
    $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML', $recipientLanguage);
    $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML', $recipientLanguage);

    $rParams = array('template_header' => $template_header,
        'template_footer' => $template_footer,
        'inviter_name' => $inviter_name,
        'site_title_linked' => $site_title_linked,
        'store_title_linked' => $store_title_linked,
        'store_link' => $store_link,
        'site_title' => $site_title,
        'body' => $body,
        'store_title' => $sitestore->title,
        'link' => $link,
        'host' => $host,
        'email' => $viewer->email,
        'queue' => true
    );


    // Do replacements
    foreach ($rParams as $var => $val) {
      $raw = trim($var, '[]');
      $var = '[' . $var . ']';
      // Fix nbsp
      $val = str_replace('&amp;nbsp;', ' ', $val);
      $val = str_replace('&nbsp;', ' ', $val);
      // Replace
      $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
      $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
      $bodyTextHeader = str_replace($var, $val, $bodyTextHeader);
      $bodyTextFooter = str_replace($var, $val, $bodyTextFooter);
      $bodyHtmlHeader = str_replace($var, $val, $bodyHtmlHeader);
      $bodyHtmlFooter = str_replace($var, $val, $bodyHtmlFooter);
    }

    // Do header/footer replacements
    $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
    $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
    $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
    $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

    // Check for missing text or html
    if (!$bodyHtmlTemplate) {
      $bodyHtmlTemplate = nl2br($bodyTextTemplate);
    }
    $this->view->bodyHtmlTemplate = $bodyHtmlTemplate;
    $this->view->servicetype = $_GET['servicetype'];
  }

  protected function _translate($key, $locale, $noDefault = false) {
    $translate = Zend_Registry::get('Zend_Translate');
    $value = $translate->translate($key, $locale);
    if ($value == $key || '' == trim($value)) {
      if ($noDefault) {
        return false;
      } else {
        $value = $translate->translate($key);
        if ($value == $key || '' == trim($value)) {
          return false;
        }
      }
    }
    return $value;
  }

  public function invitetositeAction() {
    $invite_friend = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.ltsug');
    $friendsToJoin = $_POST['nonsitemembers'];
    $storeinvite_id = $_POST['invitestore_id'];
    $storeinvite_userid = $_POST['invitestore_userid'];
    $this->sendInvites($friendsToJoin, $storeinvite_id, $storeinvite_userid);
    exit();
  }

  public function sendInvites($recipients, $storeinvite_id = null, $storeinvite_userid = null, $invite_message=null) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
    $message = $this->view->translate(Engine_Api::_()->getApi('settings', 'core')->invite_message);
    $message = trim($message);

    $template_header = '';
    $template_footer = '';
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.site.title', $site_title);
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.font.color', "#79b4d4");
    $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
    $template_header.= "<tr><td style='background:" . $site_header_color . "; color:" . $site_title_color . ";font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
    $inviter_name = $viewer->getTitle();
    $sitestoreModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    if ($storeinvite_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $storeinvite_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id),
                    ), 'sitestore_entry_view', true);

    //GETTING THE STORE PHOTO.
     $file = $sitestore->getPhotoUrl('thumb.icon');
    if (empty($file)) {
      $storephoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Sitestore/externals/images/nophoto_list_thumb_normal.png';
    } else {
      $storephoto_path = $file;
    }

    $inviteUrl_link = '<table><tr valign="top"><td style="color:#999999;font-size:11px;padding-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $storephoto_path . '" style="width:100px;"/>' . '</a>';
    //GETTING NO OF LIKES TO THIS STORE.
    $num_of_like = Engine_Api::_()->sitestore()->numberOfLike('sitestore_store', $sitestore->store_id);
    $body_message = $inviteUrl_link . $sitestore->title . '<br /> ' . $this->view->translate(array('%s Person Likes This', '%s People Like This', $num_of_like), $this->view->locale()->toNumber($num_of_like));

    $recepients_array = array();

    $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
    $store_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $sitestore->title . '</a>';
    $store_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreinvite.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreinvite.utility.type', convert_uuencode($sitestoreModHostName));
    }

    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
    if (is_array($recipients) && !empty($recipients) && !empty($storeinvite_id) && !empty($storeinvite_userid)) {

      $table_message = Engine_Api::_()->getDbtable('messages', 'messages');
      $tableName_message = $table_message->info('name');

      $table_user = Engine_Api::_()->getitemtable('user');
      $tableName_user = $table_user->info('name');

      $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');
      $tableName_user_memberships = $table_user_memberships->info('name');

      foreach ($recipients as $recipient) {
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);

        // watch out for poorly formatted emails
        if (!empty($recipient)) {
          //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
          $select = $table_user->select()
                  ->setIntegrityCheck(false)
                  ->from($tableName_user, array('user_id'))
                  ->where('email = ?', $recipient);
          $is_site_members = $table_user->fetchAll($select);

          //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
          if (isset($is_site_members[0]) && !empty($is_site_members[0]->user_id) && $is_site_members[0]->user_id != $viewer->user_id) {
            $contact = Engine_Api::_()->user()->getUser($is_site_members[0]->user_id);

            // check that user has not blocked the member
            if (!$viewer->isBlocked($contact)) {
              $recepients_array[] = $is_site_members[0]->user_id;
              $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

              // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND
              if ($is_suggenabled) {
                Engine_Api::_()->sitestoreinvite()->sendSuggestion($is_site_members[0], $viewer, $sitestore->store_id);
              }
              // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND
              else {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members[0], $viewer, $sitestore, 'sitestore_suggested');
              }
            }
          }
          // BODY OF STORE COMPRISING LIKES
          $body = $inviteUrl_link . '<br/>' . $this->view->translate(array('%s person likes this', '%s people like this', $num_of_like), $this->view->locale()->toNumber($num_of_like)) . '</td><td>';

          if (!empty($invite_message)) {
            $body .= $invite_message . "<br />";
          }
          $link = '<a href="' . $base_url . '">' . $base_url . '</a>';
          $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";

          // IF THE PERSON IS NOT THE SITE MEMBER
          if (!isset($is_site_members[0])) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'Sitestoreinvite_User_Invite', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'site_title_linked' => $site_title_linked,
                'store_title_linked' => $store_title_linked,
                'store_link' => $store_link,
                'site_title' => $site_title,
                'body' => $body,
                'store_title' => $sitestore->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
          // IF THE PERSON IS A SITE MEMBER
          else {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'SITESTOREINVITE_MEMBER_INVITE', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'store_title_linked' => $store_title_linked,
                'store_link' => $store_link,
                'site_title' => $site_title,
                'body' => $body,
                'store_title' => $sitestore->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
        }
      } // end foreach
    } // end if (is_array($recipients) && !empty($recipients))
    return;
  }

  // end public function sendInvites()

  public function inviteusersAction() {
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->invite_emails = "";
    // Get the current loggedin user information
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->storeinvite_id = $storeinvite_id = $this->_getParam('sitestore_id', null);

    $this->view->storeinvite_userid = $storeinvite_userid = $this->_getParam('user_id', null);
    // Get the current loged in userid
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if (isset($_POST['submit']) && $_POST['submit']) {
      $invite_emails = $_POST['invite_email'];

      if (empty($invite_emails[0])) {
        $this->view->is_error = $this->view->translate('Please enter email address.');
      }

      $invite_message = $_POST['invite_message'];
      $total_prefill = 1;

      $this->view->invite_emails = $invite_emails;
      $this->view->invite_message = $invite_message;
      $message = trim($invite_message);
      //Check email is valid or not.
      foreach ($invite_emails as $email) {
        if (!empty($email)) {
          //Calculate total prefill if invitemail is not empty.
          $total_prefill = count($invite_emails);
          if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
            $this->view->is_error = $this->view->translate('Please enter valid email address.');
          }
        }
      }

      if (empty($this->view->is_error)) {
        $this->sendInvites($invite_emails, $storeinvite_id, $storeinvite_userid, $invite_message);
        $this->view->successmessage = $this->view->translate('Your Store invitation(s) were sent successfully.');
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => false,
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('<ul class="form-notices" style="margin:0px;"><li style="margin:0px;">Your Store invitation(s) were sent successfully.</li></ul>'))
        ));
        $total_prefill = 1;
      }

      $this->view->total_prefill = $total_prefill;
    }
  }

}

?>
