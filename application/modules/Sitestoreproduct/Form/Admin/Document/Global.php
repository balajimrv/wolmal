<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Document_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all the documents of the products.');

    $settings = Engine_Api::_()->getApi('settings', 'core');
//    $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestoreproduct')->hasDirectoryPermissions();
    $storeName = Zend_Controller_Front::getInstance()->getRequest()->getParam('store', 'sitestore');

//    if (!empty($hasLanguageDirectoryPermissions)) {

        $this->addElement('Radio', 'sitestoreproduct_document_enable', array(
          'label' => 'Enable Document',
          'description' => "Do you want to enable Product Document?",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sitestoreproduct.document.enable', 0),
          'onchange' =>'showOtherSettings();'
      ));
    
        $this->addElement('Radio', 'sitestoreproduct_document_auto', array(
          'label' => 'Document Approval Moderation',
          'description' => "Do you want new Document to be automatically approved?",
          'multiOptions' => array(
              1 => 'Yes, automatically approve Document.',
              0 => 'No, site admin approval will be required for all Documents.'
          ),
          'value' => $settings->getSetting('sitestoreproduct.document.auto', 1),
      ));
        
        $this->addElement('Radio', 'sitestoreproduct_document_privacy', array(
          'label' => 'Product Document downloading',
          'description' => "Allow store admins to choose who can download the documents. (If enabled Product admin will be able to configure this setting from create and edit document page.)",
          'multiOptions' => array(
              1 => 'Yes, let Store Admin to choose who can download the documents.',
              0 => 'No, everyone will be able to download document.'
          ),
          'value' => $settings->getSetting('sitestoreproduct.document.privacy', 1),
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit'
      ));
//    }
  }
}