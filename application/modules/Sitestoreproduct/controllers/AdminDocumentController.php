<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDocumentController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminDocumentController extends Core_Controller_Action_Admin {

  public function init() {    
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->_viewer_id = $this->_viewer->getIdentity();
  }

  //ACTION FOR MANAGE PACKAGE LISTINGS
  public function indexAction() {

        $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_document_admin_main_settings');
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')){
          $this->view->navigationSubStore = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_document_admin_main', array(), 'sitestoreproduct_document_admin_sub_settings');
        }
        
          $this->view->form = $form = new Sitestoreproduct_Form_Admin_Document_Global();
            $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestoreproduct')->hasDirectoryPermissions();
             $settings = Engine_Api::_()->getApi('settings', 'core');
              if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
          $values = $form->getValues();
               Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.document.auto', $values['sitestoreproduct_document_auto']);
               Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.document.privacy', $values['sitestoreproduct_document_privacy']);
               Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.document.enable', $values['sitestoreproduct_document_enable']);
             }

  }
  
  public function manageAction() {

        $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_document_admin_main_settings');
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')){
          $this->view->navigationSubStore = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_document_admin_main', array(), 'sitestoreproduct_document_admin_sub_settings');

    $params = array();
    $params['page'] = $this->_getParam('page', 1);
    $params['limit'] = 20;
    
     $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('documents', 'sitestoreproduct')->getDocumentsPaginator($params);
        }      

  }

}
?>