<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Message.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreadmincontact_Form_Admin_Message_Message extends Engine_Form {

  public function init() {

    //GET DECORATORS
    $this->loadDefaultDecorators();

    //GET DESCRIPTION
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Using this form, you will be able to send a message out to all of the Admins of Stores on your website.<br />You can use this tool to communicate to users about tips to more effectively use Stores on your website. You can also inform them about new features added to Stores. SocialEngineAddOns is regularly releasing %s and enhancements and this tool can help you inform your users about them as you make these features available on your website.<br />This can be a great way to motivate users to keep their Stores updated and active on your website."), '<a href="http://www.socialengineaddons.com/catalog/directory-stores-extensions" target="_blank">new extensions</a>');

    $this->getDecorator('Description')->setOption('escape', false);

    //SET TITLE AND DESCRIPTION
    $this
            ->setTitle('Message All Store Admins')
            ->setDescription($description)
            ->setAttrib('name', 'admincontact');

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
          'description' => 'Hold down the CTRL key to select or de-select specific Packages for which Admins of corresponding Stores need to be sent messages.',
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
        'description' => 'Hold down the CTRL key to select or de-select specific Store Categories for which Admins of corresponding Stores need to be sent messages.',
        'required' => false,
        'allowEmpty' => true,
        'multiOptions' => $categories_prepared,
        'value' => 0
    ));

    $this->categories->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $status_prepared = array("All" => "All Stores", "Draft" => "Draft Stores",  "Published" => "Published Stores","Open" => "Open Stores",  "Closed" => "Closed Stores", "Featured" => "Featured Stores", "Sponsored" => "Sponsored Stores", "Approved" => "Approved Stores", "DisApproved" => "Dis-approved Stores");   
   
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $status_prepared = array_merge($status_prepared, array("Running" => "Running Stores", "Expired" => "Expired Stores"));
    }      
    
    $this->addElement('Multiselect', 'status', array(
        'label' => 'Status',
         'RegisterInArrayValidator' => false,
        'description' => 'Hold down the CTRL key to select or de-select specific Store Status for which Admins of corresponding Stores need to be sent messages.',
        'required' => false,
        'allowEmpty' => true,
        'multiOptions' => $status_prepared,
        'value' => "All"
    ));
    $this->status->getDecorator('Description')->setOptions(array('placement' => 'APPEND')); 

    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("To compose a message click %s."), '<a href="javascript:void(0);" onclick="getValues();">here</a>');
    $this->addElement('Dummy', 'sitestoreadmincontact_sendmessage', array(
        'label' => 'Compose Message',
        'description' => $description,
    ));
    $this->getElement('sitestoreadmincontact_sendmessage')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
  }

}

?>