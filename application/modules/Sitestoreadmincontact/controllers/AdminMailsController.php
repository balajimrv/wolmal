<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMailsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreadmincontact_AdminMailsController extends Core_Controller_Action_Admin {

  //ACTION FOR SENDING THE EMAIL
  public function indexAction() {
    
//GET NAVIGATION
$this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreadmincontact');   

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreadmincontact_admin_main', array(), 'sitestoreadmincontact_admin_main_mails');

    //GET FORM
    $this->view->form = $form = new Sitestoreadmincontact_Form_Admin_Mail_Mail();

    //CHECK METHOD
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      //GET VALUES
      $values = $form->getValues();

      //GET VIEW OBJECT
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

      //check if Sitemailtemplates Plugin is enabled
      $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

      //GET SITE TITLE
      $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

      //INITIALISE VARIABLES
      $contact_string = "";
      $template_header = "";
      $template_footer = "";

      if(!$sitemailtemplates) {
   
				//GET SITE "Email Template Header Background Color" COLOR
				$site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.color', "#ffffff");

				//GET SITE "Email Template Header Title Text Color" COLOR
				$site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.header.color', "#79b4d4");

				//GET SITE "Email Body Outer Background" COLOR
				$site_bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.bg.color', "#f7f7f7");
				//SET TEMPLATE HEADER
				$template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='$site_bg_color' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
				$template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";

				//SET TEMPLATE FOOTER
				$template_footer.= "</td></tr></table></td></tr></td></table></td></tr></table>";
      }

			$validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      //ATTACH BODY
      $contact_string .= $values['body'];

      //GET SET EMAIL ADDRESS OF THE ADMIN
      $emailAdmin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

      //SET MESSAGESENT TO 0
      $this->view->messageSent = 0;

      //CHECK WHETHER THE TEST EMAIL HAS TO BE SENT
      if (!empty($values['store_contactemail_demo'])) {

        $contactAdminEmail = $values['store_contactemail_admin'];
        if (!$validator->isValid($contactAdminEmail)) {
          continue;
        }

        Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['store_contactemail_admin'], 'SITESTOREADMINCONTACT_CONTACTS_EMAIL_NOTIFICATION', array(
            'subject' => $values['subject'],
            'template_header' => $template_header,
            'message' => $contact_string,
            'template_footer' => $template_footer,
            'site_title' => $site_title,
            'email' => $emailAdmin,
            'queue' => true));
        $this->view->messageSent = 1;
        $this->view->successMessge = $this->view->translate("Your test email has been sent successfully.");
      } else {
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $manageadminsTableName = $manageadminsTable->info('name');
        $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $rName = $tableStore->info('name');
        
        $results = $tableStore->checkStore();
        if(!empty($results)) {
          if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
            if (!isset($values['packages'])) {
              $values['packages'][0] = 0;
            }
            if (empty($values['categories'][0])) {
              $values['categories'][0] = 0;
            }
          } else {
            if (!isset($values['categories'][0])) {
              $values['categories'][0] = 0;
            }
          }

          if (isset($values['categories']) && empty($values['categories'][0]) && isset($values['packages']) && empty($values['packages'][0]) && isset($values['status']) && empty($values['status'][0])) {
            $select = $manageadminsTable->select()->setIntegrityCheck(false)
                    ->from($manageadminsTableName, array('user_id'))
                    ->join($rName, $rName . '.store_id = ' . $manageadminsTableName . '.store_id', array())
                    ->group($manageadminsTableName . '.user_id')
                    ->order('user_id ASC');
          } else {
            $select = $manageadminsTable->select()->setIntegrityCheck(false)
                    ->from($manageadminsTableName, array('user_id'))
                    ->join($rName, $rName . '.store_id = ' . $manageadminsTableName . '.store_id', array())
                    ->group($manageadminsTableName . '.user_id')
                    ->order('user_id ASC');
            $categories_ids = $values['categories'];
            if (is_array($categories_ids) && !empty($categories_ids) && !empty($values['categories'][0])) {
              $select->where('category_id IN (?)', $categories_ids);
            }
            if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
              $packages_ids = $values['packages'];
              if (is_array($packages_ids) && !empty($packages_ids) && !empty($values['packages'][0])) {
                $select->where('package_id IN (?)', $packages_ids);
              }
            }

            $status_ids = $values['status'];

            if (is_array($status_ids) && (in_array("Draft", $status_ids) && in_array("Published", $status_ids) )) {
              $select->where($rName . '.draft = ?', '0')->orWhere($rName . '.draft = ?', '1');
            } elseif (is_array($status_ids) && in_array("Draft", $status_ids)) {
              $select->where($rName . '.draft = ?', '0');
            } elseif (is_array($status_ids) && in_array("Published", $status_ids)) {
              $select->where($rName . '.draft = ?', '1');
            }

            if (is_array($status_ids) && (in_array("Open", $status_ids) && in_array("Closed", $status_ids) )) {
              $select->where($rName . '.closed = ?', '0')->orWhere($rName . '.closed = ?', '1');
            } elseif (is_array($status_ids) && in_array("Open", $status_ids)) {
              $select->where($rName . '.closed = ?', '0');
            } elseif (is_array($status_ids) && in_array("Closed", $status_ids)) {
              $select->where($rName . '.closed = ?', '1');
            }

            if (is_array($status_ids) && in_array("Featured", $status_ids)) {
              $select->where($rName . '.featured = ?', '1');
            }

            if (is_array($status_ids) && in_array("Sponsored", $status_ids)) {
              $select->where($rName . '.sponsored = ?', '1');
            }

            if (is_array($status_ids) && (in_array("Approved", $status_ids) && in_array("DisApproved", $status_ids) )) {
              $select->where($rName . '.approved = ?', '0')->orWhere($rName . '.approved = ?', '1');
            } elseif (is_array($status_ids) && in_array("Approved", $status_ids)) {
              $select->where($rName . '.approved = ?', '1');
            } elseif (is_array($status_ids) && in_array("DisApproved", $status_ids)) {
              $select->where($rName . '.approved = ?', '0');
            }

            if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
              if (is_array($status_ids) && (in_array("Running", $status_ids) && in_array("Expired", $status_ids) )) {
                $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"))->orWhere($rName . '.expiration_date < ?', date("Y-m-d H:i:s"));
              } elseif (is_array($status_ids) && in_array("Running", $status_ids)) {
                $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
              } elseif (is_array($status_ids) && in_array("Expired", $status_ids)) {
                $select->where($rName . '.expiration_date  < ?', date("Y-m-d H:i:s"));
              }
            }
          }

          foreach ($select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0) as $id) {
            $email = Engine_Api::_()->getItem('user', $id)->email;
            if (!$validator->isValid($email)) {
              continue;
            }

            if (!empty($email)) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITESTOREADMINCONTACT_CONTACTS_EMAIL_NOTIFICATION', array(
                  'subject' => $values['subject'],
                  'template_header' => $template_header,
                  'message' => $contact_string,
                  'template_footer' => $template_footer,
                  'site_title' => $site_title,
                  'email' => $emailAdmin,
                  'queue' => true));
            }
          }

          //GET ADMIN INFO
          $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
          $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

          //GET MAIL CORE TABLE
          $mailApi = Engine_Api::_()->getApi('mail', 'core');
          $mailComplete = $mailApi->create();
          $mailComplete
                  ->addTo(Engine_Api::_()->user()->getViewer()->email)
                  ->setFrom($fromAddress, $fromName)
                  ->setSubject('Mailing Complete: ' . $values['subject'])
                  ->setBodyHtml('Your email blast to your members has completed.  Please note that, while the emails have been
              sent to the recipients\' mail server, there may be a delay in them actually receiving the email due to
              spam filtering systems, incoming mail throttling features, and other systems beyond SocialEngine\'s control.')
          ;
          $mailApi->send($mailComplete);
          $this->view->messageSent = 1;
          $this->view->successMessge = $this->view->translate("Your emails have been queued for sending.");
        } else {
          $this->view->messageSent = 1;
          $this->view->successMessge = $this->view->translate("Your email can not be sent because no Stores have been created yet."); 
        }     
        
      }
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
			//START LANGUAGE WORK
			Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
			//END LANGUAGE WORK
    }
  }

  //ACTION FOR UPLOADING THE IMAGES
  public function uploadPhotoAction() {

    //GET THE DIRECTORY WHERE WE ARE UPLOADING THE PHOTOS
    $adminContactFile = APPLICATION_PATH . '/public/storeadmincontact';

    if (!is_dir($adminContactFile) && mkdir($adminContactFile, 0777, true)) {
      chmod($adminContactFile, 0777);
    }
    
    //GET THE PATH
    $path = realpath($adminContactFile);
     $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    //PREPARE
    if (empty($_FILES[$fileName])) {
      $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
      return;
    }

    //GET PHOTO INFORMATION
    $info = $_FILES[$fileName];

    //SET TARGET FILE
    $targetFile = $path . '/' . $info['name'];

    //TRY TO MOVE UPLOADED FILE
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }

    //SEND THE STATUS TO THE TPl
    $this->view->status = true;

    //SEND THE PHOTO URL TO THE TPl
    $this->view->photo_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/storeadmincontact/' . $info['name'];
  }

}

?>
