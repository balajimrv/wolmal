<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SearchController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_SearchController extends Core_Controller_Action_Standard {

  public function indexAction() {
    $searchApi = Engine_Api::_()->getApi('globalsearch', 'sitemobile');

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid())
        return;
    }

    // Prepare form
    $this->view->form = $form = new Sitemobile_modules_Core_Form_Filter_Search();
    if (Engine_Api::_()->sitemobile()->isApp() ) {
      Zend_Registry::set('setFixedCreationForm', true);
    //  Zend_Registry::set('setFixedCreationHeaderTitle', "Refine Search");
      Zend_Registry::set('setFixedCreationHeaderSubmit', $form->submit->getLabel());
      $this->view->form->setAttrib('id', 'form_core_filter_search');
      Zend_Registry::set('setFixedCreationFormId', '#form_core_filter_search');
      $this->view->form->removeElement('submit');
      $form->setTitle('');
    }
    // Get available types
    $availableTypes = $searchApi->getAvailableTypes();
    if (is_array($availableTypes) && count($availableTypes) > 0) {
      $options = array();
      foreach ($availableTypes as $index => $type) {
        $options[$type] = strtoupper('ITEM_TYPE_' . $type);
      }
      $form->type->addMultiOptions($options);
    } else {
      $form->removeElement('type');
    }

    // Check form validity?
    $values = array();
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    }

    $this->view->query = $query = (string) @$values['query'];
    $this->view->type = $type = (string) @$values['type'];
    $this->view->page = $page = (int) $this->_getParam('page', 1);
    $this->view->pageapp = 1;
    if (Engine_Api::_()->sitemobile()->isApp()) {
      $this->view->pageapp = $page;
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->totalPages = 0;
    if ($query) {
      $this->view->paginator = $paginator = $searchApi->getPaginator($query, $type);
      $this->view->paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage(10);
      $this->view->totalPages = ceil(($paginator->getTotalItemCount()) / 10);
      
     if (Engine_Api::_()->sitemobile()->isApp() ) {
      Zend_Registry::set('setFixedCreationHeaderTitle', $this->view->translate("Results (%s)",$this->view->locale()->toNumber($paginator->getTotalItemCount())));

    } 
      
    }


    // Render the page
    if (!$this->_getParam('isappajax')) {
      $this->_helper->content
              // ->setNoRender()
              ->setEnabled();
    }
  }

}