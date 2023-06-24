<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AlbumController.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_AlbumController extends Core_Controller_Action_Standard {

  //album constructor function
  public function init() {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
      return;
    if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
            null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
      Engine_Api::_()->core()->setSubject($photo);
    } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
            null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
      Engine_Api::_()->core()->setSubject($album);
    }
    //if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
      //return $this->_forward('notfound', 'error', 'core');
  }

  function relatedAlbumAction() {
    $album = Engine_Api::_()->core()->getSubject();
    $this->view->album_id = $album->album_id;
    //fetch related albums to corresponding albums
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums(array('userId' => $album->owner_id, 'join' => true, 'album_id' => $album->album_id, 'widget' => true, 'notInclude' => $album->album_id));
    $paginator->setItemCountPerPage(30);
    $this->view->viewmore = isset($_POST['viewmore']) ? $_POST['viewmore'] : '';
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $paginator->setCurrentPageNumber($page);
    $this->view->page = $page + 1;
  }

  //save related album action
  function saveRelatedAlbumAction() {
    if (!isset($_POST['id'])) {
      return $this->_forward('requireauth', 'error', 'core');
      die;
    }
    $album = Engine_Api::_()->core()->getSubject();
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $albumId = $_POST['id'];
    $db = Engine_Db_Table::getDefaultAdapter();
    //delete selected related album
    if ($data == 'delete')
      $db->query("Delete FROM `engine4_sesalbum_relatedalbums` WHERE resource_id = " . $album->album_id . " && album_id = " . $albumId);
    //insert related album
    else
      $db->query("INSERT INTO `engine4_sesalbum_relatedalbums` (`resource_id`,`album_id`) VALUES ('" . $album->album_id . "','" . $albumId . "')");
    echo "TRUE";
    die;
  }

  //favourite album action
  function favAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }
    //get album id
    $album_id = $this->_getParam('album_id');
    if (intval($album_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();
    //get favourite status as per album id.
    $Fav = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getItemfav('album', $album_id);
    if (count($Fav) > 0) {
      //delete		
      $db = $Fav->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Fav->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
        return $this->_forward('notfound', 'error', 'core');
      //update favourite count 
      $album->getTable()->update(array(
          'favourite_count' => new Zend_Db_Expr('favourite_count - 1'),
              ), array(
          'album_id = ?' => $album_id,
      ));
      $albumObj = Engine_Api::_()->getItem('album', $album_id);
      // Remove activity attachments for this album
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesalbum_favouritealbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $albumObj->getType(), "object_id = ?" => $albumObj->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type =?' => "sesalbum_favouritealbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $albumObj->getType(), "object_id = ?" => $albumObj->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($albumObj);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'favourite_count' => $albumObj->favourite_count));
      die;
    } else {
      //update
      $db = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getAdapter();
      $db->beginTransaction();
      try {
        $fav = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->createRow();
        $fav->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $fav->resource_type = 'album';
        $fav->resource_id = $album_id;
        $fav->save();
        $album->getTable()->update(array(
            'favourite_count' => new Zend_Db_Expr('favourite_count + 1'),
                ), array(
            'album_id = ?' => $album_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //notification send to album owner and send to activity feed.
      $albumObj = $subject = Engine_Api::_()->getItem('album', $album_id);
      $owner = $subject->getOwner();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesalbum_favouritealbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'sesalbum_favouritealbum');
        $result = $activityTable->fetchRow(array('type =?' => "sesalbum_favouritealbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        if (!$result) {
          $action = $activityTable->addActivity($viewer, $subject, 'sesalbum_favouritealbum');
          if ($action)
            $activityTable->attachActivity($action, $subject);
        }
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'favourite_count' => $albumObj->favourite_count));
      die;
    }
  }

  //function show location on map
  public function locationAction() {
    if (!$this->_helper->requireSubject('album')->isValid())
      return;
    $this->view->type = $this->_getParam('type', 'photo');
    if ($this->view->type != 'location') {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
        return;
    }
    $this->view->photo_id = $this->_getParam('album_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject('album');
    $this->view->photo = $album;
    $this->view->form = $form = new Sesalbum_Form_Photo_Location();
    $form->populate($album->toArray());
    $location = Engine_Api::_()->getDbTable('locations', 'sesbasic')->getLocationData('sesalbum_album', $album->album_id);
    if ($location) {
			if($form->getElement('lat'))
      	$form->getElement('lat')->setValue($location->lat);
			if($form->getElement('lng'))
      	$form->getElement('lng')->setValue($location->lng);
    }
  }

  //album like action
  function likeAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }
    $album_id = $this->_getParam('album_id');
    if (intval($album_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }
    //get album like status as per user id.
    $tableLike = Engine_Api::_()->getDbtable('likes', 'core');
    $tableMainLike = $tableLike->info('name');
    $album = Engine_Api::_()->core()->getSubject();
    $select = $tableLike->select()->from($tableMainLike)->where('resource_type =?', 'album')->where('poster_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('poster_type =?', 'user')->where('resource_id =?', $album_id);
    $Like = $tableLike->fetchRow($select);
    if (count($Like) > 0) {
      //delete		
      $db = $Like->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Like->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $album->getTable()->update(array(
          'like_count' => new Zend_Db_Expr('like_count - 1'),
              ), array(
          'album_id = ?' => $album_id,
      ));
      $albumObj = Engine_Api::_()->getItem('album', $album_id);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'like_count' => $albumObj->like_count));
      die;
    } else {
      //update
      $db = $tableLike->getAdapter();
      $db->beginTransaction();
      try {
        $like = $tableLike->createRow();
        $like->poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $like->resource_type = 'album';
        $like->resource_id = $album_id;
        $like->poster_type = 'user';
        $like->save();
        $album->getTable()->update(array(
            'like_count' => new Zend_Db_Expr('like_count + 1'),
                ), array(
            'album_id = ?' => $album_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //notification send to album owner and activity feed work
      $albumObj = Engine_Api::_()->getItem('album', $album_id);
      $viewer = Engine_Api::_()->user()->getViewer();
      $owner = $albumObj->getOwner();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $albumObj, 'liked', array(
            'label' => $albumObj->getShortType()
        ));
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'like_count' => $albumObj->like_count));
      die;
    }
  }

  //album view function.
  public function viewAction() {
    if (!$this->_helper->requireSubject('album')->isValid())
      return;
    $album = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($album->album_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesalbum_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $album->album_id . '", "album","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
		
    // Render
		if((Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent(array('module'=>'sesalbum')) && $album->adult) || $album->owner_id == $viewer->getIdentity()) {
    	$this->_helper->content->setEnabled();
		} elseif($album->adult) {
			$this->view->adultContent = true;
    } else {
      $this->_helper->content->setEnabled();
    }
  }

  //function for autosuggest album
  public function getAlbumAction() {
    $sesdata = array();
    $value['text'] = $this->_getParam('text');
    $albums = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbumsAction($value);
    foreach ($albums as $album) {
      $album_icon_photo = $this->view->itemPhoto($album, 'thumb.icon');
      $sesdata[] = array(
          'id' => $album->album_id,
          'label' => $album->title,
          'photo' => $album_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
  }

  //album edit action
  public function editAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    if (!$this->_helper->requireSubject('album')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    // Prepare data
    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesalbum')->profileFieldId();
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
      return $this->_forward('notfound', 'error', 'core');
    if (isset($album->category_id) && $album->category_id != 0) {
      $this->view->category_id = $album->category_id;
    } else if (isset($_POST['category_id']) && $_POST['category_id'] != 0)
      $this->view->category_id = $_POST['category_id'];
    else
      $this->view->category_id = 0;
    if (isset($album->subsubcat_id) && $album->subsubcat_id != 0) {
      $this->view->subsubcat_id = $album->subsubcat_id;
    } else if (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0)
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    else
      $this->view->subsubcat_id = 0;
    if (isset($album->subcat_id) && $album->subcat_id != 0) {
      $this->view->subcat_id = $album->subcat_id;
    } else if (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0)
      $this->view->subcat_id = $_POST['subcat_id'];
    else
      $this->view->subcat_id = 0;
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesalbum_main');
    // Hack navigation
    foreach ($navigation->getPages() as $page) {
      if ($page->route != 'sesalbum_general' || $page->action != 'manage')
        continue;
      $page->active = true;
    }
    // Make form
    $this->view->form = $form = new Sesalbum_Form_Album_Edit(array('defaultProfileId' => $defaultProfileId));
		 if ($form->getElement('is_locked'))
      $form->getElement('is_locked')->setValue($album->is_locked);
   	 if ($form->getElement('password'))
      $form->getElement('password')->setValue($album->password);
    if (isset($_POST['category_id'])) {
      $this->view->category_id = $_POST['category_id'];
    } else if (isset($form->category_id)) {
      $this->view->category_id = $album->category_id;
    }
    if (isset($_POST['subcat_id'])) {
      $this->view->subcat_id = $_POST['subcat_id'];
    } else if (isset($album->subcat_id)) {
      $this->view->subcat_id = $album->subcat_id;
    }
    if (isset($_POST['subsubcat_id'])) {
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    } else if (isset($album->subsubcat_id)) {
      $this->view->subsubcat_id = $album->subsubcat_id;
    }
    $tagStr = '';
    foreach ($album->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!isset($tag->text))
        continue;
      if ('' !== $tagStr)
        $tagStr .= ', ';
      $tagStr .= $tag->text;
    }
    $form->populate(array(
        'tags' => $tagStr,
    ));
    //is post
    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role) {
        if (1 === $auth->isAllowed($album, $role, 'view') && isset($form->auth_view)) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'comment') && isset($form->auth_comment)) {
          $form->auth_comment->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'tag') && isset($form->auth_tag)) {
          $form->auth_tag->setValue($role);
        }
      }
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    // Process
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
			$sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
    	//check dependent module sesprofile install or not
			if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module)) {
				//disable lock if password not set.
				if (!$values['is_locked'] || !$values['password']){
					$values['is_locked'] = '0';
					$values['password'] = '';
				}
			}else{
				$values['is_locked']	= '';
				$values['password']	= '';
			}
      $album->setFromArray($values);
      $album->save();
      //save lat lng for location in sesbasic location table.
      if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $this->_getParam('album_id') . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesalbum_album")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $previousArtCover = $album->art_cover;
      $deleteTh = false;
      if (isset($_FILES['art_cover_file']) && !empty($_FILES['art_cover_file']['name'])) {
        $art_cover = $album->setCoverPhoto($form->art_cover);
        $deleteTh = true;
      }
      if ((isset($values['remove_art_cover']) && !empty($values['remove_art_cover']) || $deleteTh) && $previousArtCover != 0) {
        //Delete categories icon
        $im = Engine_Api::_()->getItem('storage_file', $previousArtCover);
        $album->art_cover = 0;
        $album->save();
        $im->delete();
      }
      // Add fields
      $customfieldform = $form->getSubForm('fields');
			if($customfieldform){
      	$customfieldform->setItem($album);
      	$customfieldform->saveValues();
			}
      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $album->tags()->setTagMaps($viewer, $tags);
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
      //set roles
      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $url = Engine_Api::_()->sesalbum()->getHref($album->getIdentity(), $album->album_id);
    header('location:' . $url);
  }

  // album delete action
  public function deleteAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid())
      return;
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = $form = new Sesalbum_Form_Album_Delete();
    if (!$album) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
      return;
    }
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $album->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected albums have been successfully deleted.');
    return $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesalbum_general', true),
                'messages' => Array($this->view->message)
    ));
  }

  // function for edit photo action
  public function editphotosAction() {
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $pageNumber = isset($_POST['page']) ? $_POST['page'] : 1;
    if (!$is_ajax) {
      if (!$this->_helper->requireUser()->isValid())
        return;
      if (!$this->_helper->requireSubject('album')->isValid())
        return;
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
        return;
    }
    if (!$is_ajax) {
      // Get navigation
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('sesalbum_main');
      // Hack navigation
      foreach ($navigation->getPages() as $page) {
        if ($page->route != 'sesalbum_general' || $page->action != 'manage')
          continue;
        $page->active = true;
      }
    }
    // Prepare data
    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
        'order' => 'order ASC'
    ));
    $this->view->album_id = $album->album_id;
    $paginator->setCurrentPageNumber($pageNumber);
    $itemCount = (count($_POST) > 0 && !$is_ajax) ? count($_POST) : 10;
    $paginator->setItemCountPerPage($itemCount);
    $this->view->page = $pageNumber;
    // Get albums
    $myAlbums = Engine_Api::_()->getDbtable('albums', 'sesalbum')->editPhotos();
    $albumOptions = array('' => '');
    foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    if (count($albumOptions) == 1) {
      $albumOptions = array();
    }
    // Make form
    $this->view->form = $form = new Sesalbum_Form_Album_Photos();
    foreach ($paginator as $photo) {
      $subform = new Sesalbum_Form_Album_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
      if (empty($albumOptions)) {
        $subform->removeElement('move');
      } else {
        $subform->move->setMultiOptions($albumOptions);
      }
    }
    if ($is_ajax) {
      return;
    }
    if (!$this->getRequest()->isPost()) {
      return;
    }
    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $values = $_POST;
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }
      // Process
      foreach ($paginator as $photo) {
        if (isset($_POST[$photo->getGuid()])) {
          $values = $_POST[$photo->getGuid()];
        } else {
          continue;
        }
        unset($values['photo_id']);
        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();
        } else if (!empty($values['move'])) {
          $nextPhoto = $photo->getNextPhoto();
          $old_album_id = $photo->album_id;
          $photo->album_id = $values['move'];
          $photo->save();
          // Change album cover if necessary
          if (($nextPhoto instanceof Sesalbum_Model_Photo) &&
                  (int) $album->photo_id == (int) $photo->getIdentity()) {
            $album->photo_id = $nextPhoto->getIdentity();
            $album->save();
          }
          // Remove activity attachments for this photo
          Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
        } else {
          $photo->setFromArray($values);
          $photo->save();
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //send to specific album view page.
    return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'album_id' => $album->album_id), 'sesalbum_specific', true);
  }

  //album photo change order function
  public function orderAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    if (!$this->_helper->requireSubject('album')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    $album = Engine_Api::_()->core()->getSubject();
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    // Get a list of all photos in this album, by order
    $currentOrder = Engine_Api::_()->getDbtable('photos', 'sesalbum')->order($album);
    // Find the starting point?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }
    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      $photoTable->update(array(
          'order' => $i,
              ), array(
          'photo_id = ?' => $photo_id,
      ));
    }
    $this->view->status = true;
  }

  //album photo upload function
  public function composeUploadAction() { 
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_redirect('login');
      return;
    }
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }
    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    
    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'sesalbum');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $type = $this->_getParam('type', 'wall');
      if (empty($type))
        $type = 'wall';
      $album = $table->getSpecialAlbum($viewer, $type);
      $photoTable = Engine_Api::_()->getDbtable('photos', 'sesalbum');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
          'owner_type' => 'user',
          'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      
      $photo->setPhoto($_FILES['Filedata']);
      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }
      $photo->order = $photo->photo_id;
      $photo->album_id = $album->album_id;
      $photo->save();
      
      if (!$album->photo_id) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }
      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }
      $db->commit();
      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      
			$this->view->src = $this->view->url = $photo->getPhotoUrl('thumb.normalmain');
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected photos have been successfully saved.');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
      $this->view->status = false;
    }
    if($this->_getParam('isactivity',false)){
      echo json_encode(array('src'=>$this->view->src ,'photo_id'=>$this->view->photo_id,'status'=>$this->view->status));die;  
      
    }
  }

}
