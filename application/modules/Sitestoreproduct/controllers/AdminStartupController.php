<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminStartupController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminStartupController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_manage_startup');

        $page = $this->_getParam('page', 1);
        $sortingColumnName = $this->_getParam('idSorting', 0);
        $pagesettingsTable = Engine_Api::_()->getItemTable('sitestoreproduct_startuppage');
        $pagesettingsSelect = $pagesettingsTable->select();
        $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);

        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $contentObject = Engine_Api::_()->getItem('communityad_infopage', $value);
                    if (!empty($contentObject->delete)) {
                        $contentObject->delete();
                    }
                }
            }
        }
    }

    public function editAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_manage_startup');

        $this->view->page_id = $page_id = $this->_getParam('startuppages_id', 0);

        $this->view->form = $form = new Sitestoreproduct_Form_Admin_Startupcreate();


        $textFlag = $this->_getParam('textFlag', 1);
        if (empty($textFlag)) {
            $this->view->showTinyMce = true;
            $textLinkFlag = $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'startup', 'action' => 'edit', 'textFlag' => 1, 'startuppages_id' => $page_id), 'admin_default', true);
            $textDescription = $this->view->translate("If your site supports multiple laguage then <a href='%s'> click here </a> for the compatible Text input box.", $textLinkFlag);
        } else {
            $textLinkFlag = $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'startup', 'action' => 'edit', 'textFlag' => 0, 'startuppages_id' => $page_id), 'admin_default', true);
            $textDescription = $this->view->translate("If your site supports only one laguage then <a href='%s'> click here </a> for the compatible Text input box.", $textLinkFlag);
        }

        $form->text_flag->setDescription($textDescription);

        $form->text_flag->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];



        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            if (!empty($values['text_description'])) {
                $values['description'] = $values['text_description'];
                unset($values['text_description']);
            }
            $contentTable = Engine_Api::_()->getDbTable('startuppages', 'sitestoreproduct');
            $contentTable->update(array('title' => $values['title'], 'description' => $values['description'], 'short_description' => $values['short_description']), array('startuppages_id =?' => $values['is_opration']));
            $this->_helper->redirector->gotoRoute(array('module' => 'sitestoreproduct', 'controller' => 'startup'), 'admin_default', true);
        }
    }

  // Function: When approved or disapproved page (Help & Learn more page).
  public function statusAction() {
    $status = $this->_getParam('status');
    $infoId = $this->_getParam('id');
    if (empty($status)) {
      $this->view->title = $this->view->translate("Disable Page?");
      $this->view->discription = $this->view->translate("Are you sure that you want to disable this page? After being disabled this will not be shown to users.");
      $this->view->bouttonLink = $this->view->translate("Disable");
    } else {
      $this->view->title = $this->view->translate("Enable Page?");
      $this->view->discription = $this->view->translate("Are you sure that you want to enable this page? After being enabled this will be shown to users.");
      $this->view->bouttonLink = $this->view->translate("Enable");
    }
    // Check post
    if ($this->getRequest()->isPost()) {
      $pagesettingsTable = Engine_Api::_()->getDbTable('startuppages', 'sitestoreproduct');
      $pagesettingsTable->update(array('status' => $status), array('startuppages_id =?' => $infoId));

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('Successfully done.')
      ));
    }
  }
}