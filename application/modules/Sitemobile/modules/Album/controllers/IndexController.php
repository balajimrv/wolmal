<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_IndexController extends Core_Controller_Action_Standard {

  public function browseAction() { 
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
      return;

    $settings = Engine_Api::_()->getApi('settings', 'core');
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
            ->where("search = 1")
            ->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id)
      $select->where("owner_id = ?", $user_id);
    if ($this->_getParam('category_id'))
      $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1)){
      $select->where('type IS NULL');    
    }  
    // Create new array filtering out private albums
    $viewer = Engine_Api::_()->user()->getViewer();
    $album_select = $select;
    $new_select = array();
    $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.privacybase', 0);
    if (empty($privacybase)) {
      $new_select = $album_select;
    } else {
      $i = 0;
      foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
        if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
          $new_select[$i++] = $album;
        }
      }
    }
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    if (Engine_Api::_()->sitemobile()->isApp()) {
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    
    $paginator = $this->view->paginator = Zend_Paginator::factory($new_select);
        if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
            $defaultLimit = 9;
        } else {
            $defaultLimit = 18;
        }
    
    $paginator->setItemCountPerPage($defaultLimit);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $searchForm = new Sitemobile_modules_Album_Form_Filter_Search();
    $searchForm->getElement('sort')->setValue($this->_getParam('sort'));
    $searchForm->getElement('search')->setValue($this->_getParam('search'));
    $category_id = $searchForm->getElement('category_id');
    if ($category_id) {
      $category_id->setValue($this->_getParam('category_id'));
    }
    $this->view->searchParams = $searchForm->getValues();
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalAlbums = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalAlbums) / $defaultLimit);

    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('sitemapPageHeaderTitle', 'Albums');
    }
    // Render
    if (!$isappajax)
      $this->_helper->content
              //->setNoRender()
              ->setEnabled();
  }

  public function photosAction() {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
      return;
    //IF SITEALBUM PLUGIN IS NOT INSTALLED THEN WE WILL NOT SHOW THE BROWSE PHOTOS TAB.
    $sitealbum = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
    if (empty($sitealbum))
      return $this->_forward('notfound', 'error', 'core');

    // Render
    $this->_helper->content
            ->setNoRender()
            ->setEnabled()
    ;
  }

  public function manageAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
      return;
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);

    $search_form = $this->view->search_form = new Sitemobile_modules_Album_Form_Filter_Search();
    $this->view->page = $page = $this->_getParam('page', 1);
    if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost()) && !$isappajax) {
      $this->_helper->redirector->gotoRouteAndExit(array(
          'page' => 1,
          'sort' => $this->getRequest()->getPost('sort'),
          'search' => $this->getRequest()->getPost('search'),
          'category_id' => $this->getRequest()->getPost('category_id'),
          ''
      ));
    } else {
      $search_form->getElement('search')->setValue($this->_getParam('search'));
      $search_form->getElement('sort')->setValue($this->_getParam('sort'));
      if ($search_form->getElement('category_id'))
        $search_form->getElement('category_id')->setValue($this->_getParam('category_id'));
    }
    //$this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);

    if (!$isappajax)
    // Render
      $this->_helper->content
              //->setNoRender()
              ->setEnabled();

    // Get params

    $this->view->clear_cache = true;
    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }

    // Prepare data
    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('album');

    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
            ->where('owner_id = ?', $user->getIdentity())
            ->order($order . ' DESC');
    ;

    if ($this->_getParam('category_id'))
      $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    
          if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
            $defaultTempLimit = 9;
        } else {
            $defaultTempLimit = 15;
        }
    
    $paginator->setItemCountPerPage($defaultTempLimit);
    $paginator->setCurrentPageNumber($page);
    if (Engine_Api::_()->sitemobile()->isApp()) {
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->searchParams = $search_form->getValues();
    $this->view->totalAlbums = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalAlbums) / 10);
  }

  public function uploadAction() {

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
      return;

    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;

    // Get form
    $this->view->form = $form = new Album_Form_Album();

    if (Engine_Api::_()->sitemobile()->isApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      //Zend_Registry::set('setFixedCreationFormBack', 'Cancel');
      Zend_Registry::set('setFixedCreationHeaderTitle', 'Add Photos');
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'form_album_creation');
      Zend_Registry::set('setFixedCreationFormId', '#form_album_creation');
      $this->view->form->removeElement('submit');
      $form->setTitle('');
    }
    if (!$this->getRequest()->isPost()) {
      if (null !== ($album_id = $this->_getParam('album_id'))) {
        $form->populate(array(
            'album' => $album_id
        ));
      }
      return;
    }
    $this->view->clear_cache = true;
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $form->addError(Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).'));
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    //COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
    $count = 0;
    foreach ($_FILES['Filedata']['name'] as $data) {
      if (!empty($data)) {
        $count = 1;
        break;
      }
    }

    try {
      if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
        $this->view->status = false;
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
        return;
      }
      $values = $form->getValues();

      $viewer = Engine_Api::_()->user()->getViewer();
      $values['file'] = array();
      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
        $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

        if (!is_uploaded_file($file['tmp_name'])) {
          continue;
        }
        Engine_Api::_()->sitemobile()->autoRotationImage($file);
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();
        $photo->order = $photo->photo_id;
        $photo->setPhoto($file);
        $photo->save();
        $values['file'][] = $photo->photo_id;
      }

      if (count($values['file']) < 1) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
        return;
      }
      $db->commit();
    } catch (Album_Model_Exception $e) {
      $db->rollBack();
      throw $e;
      return;
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
      return;
    }
    $db = Engine_Api::_()->getItemTable('album')->getAdapter();
    $db->beginTransaction();

    try {
      $set_cover = false;

      $params = Array();
      if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
        $params['owner_type'] = 'user';
      } else {
        $params['owner_id'] = $values['owner_id'];
        $params['owner_type'] = $values['owner_type'];
        throw new Zend_Exception("Non-user album owners not yet implemented");
      }

      if (($values['album'] == 0)) {
        $params['title'] = $values['title'];
        if (empty($params['title'])) {
          $params['title'] = "Untitled Album";
        }
        $params['category_id'] = (int) @$values['category_id'];
        $params['description'] = $values['description'];
        $params['search'] = $values['search'];

        $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
        $album->setFromArray($params);
        $album->save();

        $set_cover = true;

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = key($form->auth_view->options);
          if (empty($values['auth_view'])) {
            $values['auth_view'] = 'everyone';
          }
        }
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = key($form->auth_comment->options);
          if (empty($values['auth_comment'])) {
            $values['auth_comment'] = 'owner_member';
          }
        }
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = key($form->auth_tag->options);
          if (empty($values['auth_tag'])) {
            $values['auth_tag'] = 'owner_member';
          }
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $tagMax = array_search($values['auth_tag'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
        }
      } else {
        if (!isset($album)) {
          $album = Engine_Api::_()->getItem('album', $values['album']);
        }
      }

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));

      // Do other stuff
      $count = 0;
      foreach ($values['file'] as $photo_id) {
        $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
          continue;

        if ($set_cover) {
          $album->photo_id = $photo_id;
          $album->save();
          $set_cover = false;
        }

        $photo->album_id = $album->album_id;
        $photo->order = $photo_id;
        $photo->save();

        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //$this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'album_id' => $album->album_id), 'album_specific', true);
    return $this->_forward('success', 'utility', 'core', array(
                'redirect' => $this->_helper->url->url(array('action' => 'view', 'album_id' => $album->album_id), 'album_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Album has been created successfully.')),
    ));
    return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'album_id' => $album->album_id), 'album_specific', true);
  }

}