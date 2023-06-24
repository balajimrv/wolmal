<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9878 2013-02-13 03:18:43Z shaun $
 * @author     Steve
 */

class Music_IndexController extends Seaocore_Controller_Action_Standard
{
  public function init()
  {
    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->view->viewer     = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
  }
  
  public function browseAction()
  {
    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');

    // Get browse params
    $this->view->formFilter = $formFilter = new Sitemobile_modules_Music_Form_Filter_Search();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->searchValues = $values;
    $this->view->formValues = array_filter($values);

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $items_count = 9;//(int) Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10);
    $this->view->paginator->setItemCountPerPage($items_count);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $totalItemCount = $paginator->getTotalItemCount();
    $this->view->totalPages = @ceil(($totalItemCount) /$items_count);
    $this->view->page = $this->_getParam('page', 1);

    // Render
    if(!$isappajax){
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
    }
  }
  
  public function manageAction()
  {

    // only members can manage music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // Can create?
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
    
    // Get browse params
    $this->view->formFilter = $formFilter = new Sitemobile_modules_Music_Form_Filter_Search();
    $formFilter->removeElement('show');
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->searchValues = $values;
    $this->view->formValues = array_filter($values);

    // Get paginator
    $values['user'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
   
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10);
    $this->view->paginator->setItemCountPerPage($items_count);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $totalItemCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($totalItemCount) /$items_count);
    
     $this->view->page = $this->_getParam('page', 1);
    
     // Render
    if(!$isappajax){
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
    }
  }

  public function createAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }
    if (!Engine_Api::_()->sitemobile()->isApp()) {
    $this->_helper->content
        // ->setNoRender()
        ->setEnabled()
        ;
    }
    
       $this->view->clear_cache = true;
    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload', 'song', null, array('format' => 'json'));
    }

    // Get form
    $this->view->form = $form = new Sitemobile_modules_Music_Form_Create();
    $this->view->playlist_id = $this->_getParam('playlist_id', '0');

    if (Engine_Api::_()->sitemobile()->isApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'form_music_creation');
      Zend_Registry::set('setFixedCreationFormId', '#form_music_creation');
      $this->view->form->removeElement('submit');
      $form->setTitle('');
    }
     
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
     $tempPost = $this->getRequest()->getPost();
      if (isset($tempPost['art']))
        $form->removeElement('art');
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {          
      $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);         
        if (!is_uploaded_file($file['tmp_name'])) {
              continue;
        }
        if( is_array($file) ) {
        if( !is_uploaded_file($file['tmp_name']) ) {
          return $form->addError('Invalid upload or file too large');
        }
        $filename = $file['name'];
        } else if( is_string($file) ) {
          $filename = $file;
        } else {
          return $form->addError('Invalid upload or file too large');
        }
        // Check file extension
        if( !preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename) ) {
          return $form->addError('Invalid file type');
        }
    }
    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist = $this->view->form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    return $this->_redirectCustom($playlist->getHref(), array('prependBase' => false));
  }
}