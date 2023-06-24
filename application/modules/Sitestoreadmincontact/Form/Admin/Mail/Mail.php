<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mail.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreadmincontact_Form_Admin_Mail_Mail extends Engine_Form {

  public function init() {

    //GET DECORATORS
    $this->loadDefaultDecorators();

    //GET DESCRIPTION
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Using this form, you will be able to send an email out to all of the Admins of Stores on your website. Emails are sent out using a queue system, so they will be sent out over time. An email will be sent to you when all emails have been sent. You can also first send out a test email to your account before sending it to your site's Store Admins. To configure settings for the outgoing emails from this tool, please visit the 'Email Settings' tab.<br />You can use this tool to communicate to users about tips to more effectively use Stores on your website. You can also inform them about new features added to Stores. SocialEngineAddOns is regularly releasing %s and enhancements and this tool can help you inform your users about them as you make these features available on your website.<br />This can be a great way to motivate users to keep their Stores updated and active on your website."), '<a href="http://www.socialengineaddons.com/catalog/directory-storees-extensions" target="_blank">new extensions</a>');

    $this->getDecorator('Description')->setOption('escape', false);

    //SET TITLE AND DESCRIPTION
    $this
            ->setTitle('Email All Store Admins')
            ->setDescription("$description");

    //CHECK WHETHER THE PACKAGE IS ENABLED OR NOT
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $packageTable = Engine_Api::_()->getDbtable('packages', 'sitestore');
      $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
      $packageList = $packageTable->fetchAll($packageselect);
      foreach ($packageList as $package) {
        $package_prepared[0] = "All Packages";
        $package_prepared[$package->package_id] = $package->title;
      }

      $this->addElement('Multiselect', 'packages', array(
          'label' => 'Packages',
          'description' => 'Hold down the CTRL key to select or de-select specific Packages for which Admins of corresponding Stores need to be sent emails.',
          'required' => false,
          'allowEmpty' => true,
          'multiOptions' => $package_prepared,
          'value' => 0
      ));

      $this->packages->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }

    //PREPARE CATEGORIES
    $categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
    if (count($categories) != 0) {
      $categories_prepared[0] = "All Categories";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }
    }

    $this->addElement('Multiselect', 'categories', array(
        'label' => 'Store Categories',
        'description' => 'Hold down the CTRL key to select or de-select specific Store Categories for which Admins of corresponding Stores need to be sent emails.',
        'required' => false,
        'allowEmpty' => true,
        'multiOptions' => $categories_prepared,
        'value' => 0
    ));
    $this->categories->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $status_prepared = array("All" => "All Storees", "Draft" => "Draft Storees", "Published" => "Published Storees", "Open" => "Open Storees", "Closed" => "Closed Storees", "Featured" => "Featured Storees", "Sponsored" => "Sponsored Storees", "Approved" => "Approved Storees", "DisApproved" => "Dis-approved Storees");

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $status_prepared = array_merge($status_prepared, array("Running" => "Running Storees", "Expired" => "Expired Storees"));
    }

    $this->addElement('Multiselect', 'status', array(
        'label' => 'Status',
        'RegisterInArrayValidator' => false,
        'description' => 'Hold down the CTRL key to select or de-select specific Store Status for which Admins of corresponding Storees need to be sent emails.',
        'required' => false,
        'allowEmpty' => true,
        'multiOptions' => $status_prepared,
        'value' => "All"
    ));
    $this->status->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $this->addElement('Text', 'subject', array(
        'label' => 'Subject:',
        'required' => true,
        'allowEmpty' => false,
    ));

    //UPLOAD PHOTO URL
    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestoreadmincontact', 'controller' => 'mails', 'action' => "upload-photo"), 'admin_default', true);

    //GET FILTER
    $filter = new Engine_Filter_Html();

    //BODY
    $this->addElement('TinyMce', 'body', array(
        'label' => 'Body',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            new Engine_Filter_Censor(),
            $filter,
        ),

        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
    ));
    $this->body->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    //SEND TEST EMAIL
    $this->addElement('Checkbox', 'store_contactemail_demo', array(
        'label' => 'Send me a test email. Do not send emails to Store Admins right now.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.contactemail.demo', 1),
        'description' => 'Test Email',
        'onclick' => 'showStoreOption(this.checked)'
    ));

    //EMAIL ID FOR TESTING
    $this->addElement('Text', 'store_contactemail_admin', array(
        'label' => 'Email ID for Testing',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
            array('EmailAddress', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.contactemail.admin', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
    ));

    $this->store_contactemail_admin->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $this->store_contactemail_admin->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    //SUBMIT
    $this->addElement('Button', 'submit', array(
        'label' => 'Send Emails',
        'type' => 'submit',
        'ignore' => true,
    ));
  }

}

?>