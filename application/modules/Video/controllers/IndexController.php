<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 10248 2014-05-30 21:48:38Z andres $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Video_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    //$this->getNavigation();
    
    // only show videos if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) return;

    $id = $this->_getParam('video_id', $this->_getParam('id', null));
    if( $id )
    {
      $video = Engine_Api::_()->getItem('video', $id);
      if( $video )
      {
        Engine_Api::_()->core()->setSubject($video);
      }
    }
    if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) return;
  }

  public function browseAction()
  {
    // Permissions
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
    
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    // Note: this code is duplicated in the video.browse-search widget
    $this->view->form = $form = new Video_Form_Search();
    
    // Process form
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = $values;

    $values['status'] = 1;
    $values['search'] = 1;

    $this->view->category = @$values['category'];
    $this->view->text = @$values['text'];


    if( !empty($values['tag']) ) {
      $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
    }
    
    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }

    // Get videos
    $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 12);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
  
  public function rateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    
    $rating = $this->_getParam('rating');
    $video_id =  $this->_getParam('video_id');

    
    $table = Engine_Api::_()->getDbtable('ratings', 'video');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      Engine_Api::_()->video()->setRating($video_id, $user_id, $rating);
         
      $video = Engine_Api::_()->getItem('video', $video_id);
      $video->rating = Engine_Api::_()->video()->getRating($video->getIdentity());
      $video->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $total = Engine_Api::_()->video()->ratingCount($video->getIdentity());

    $data = array();
    $data[] = array(
      'total' => $total,
      'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid()) return;

    // Upload video
    if( isset($_GET['ul'])) return $this->_forward('upload-video', null, null, array('format' => 'json'));
    if( isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name'])) $_POST['id'] = $this->uploadVideoAction();
    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
    
    // set up data needed to check quota
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);

    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();

    // Create form
    $this->view->form = $form = new Video_Form_Video();

    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue( $this->_getParam('type') );

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $values = $form->getValues();
    $values['owner_id'] = $viewer->getIdentity();

    $insertAction = false;

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try {
      // Create video
      $table = Engine_Api::_()->getDbtable('videos', 'video');
      if( $values['type'] == 'upload' ) {
        $video = Engine_Api::_()->getItem('video', $this->_getParam('id'));
        unset($values['duration']);
      } else {
        $information = $this->handleIframelyInformation($values['url']);
        if( empty($information) ) {
          $form->addError('We could not find a video there - please check the URL and try again.');
        }
        $values['code'] = $information['code'];
        $values['thumbnail'] = $information['thumbnail'];
        $values['duration'] = $information['duration'];
        $video = $table->createRow();
      }

      $video->setFromArray($values);
      $video->save();

      // Now try to create thumbnail
      if( $values['type'] !== 'upload' ) {
        $thumbnail = $values['thumbnail'];
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        if( @GetImageSize($thumbnail) ) {
          $valid_thumb = true;
        } else {
          $valid_thumb = false;
        }

        if( $valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) ) {
          $tmpFile = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
          $thumbFile = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

          $srcFh = fopen($thumbnail, 'r');
          $tmpFh = fopen($tmpFile, 'w');
          stream_copy_to_stream($srcFh, $tmpFh, 1024 * 1024 * 2);

          $image = Engine_Image::factory();
          $image->open($tmpFile)
            ->resize(330, 240)
            ->write($thumbFile)
            ->destroy();

          try {
            $thumbFileRow = Engine_Api::_()->storage()->create($thumbFile, array(
              'parent_type' => $video->getType(),
              'parent_id' => $video->getIdentity()
            ));
            $video->photo_id = $thumbFileRow->file_id;
            // Remove temp file
            @unlink($thumbFile);
            @unlink($tmpFile);
          } catch( Exception $e ) {
            
          }
        }
        $video->status = 1;
        $video->save();
        // Insert new action item
        $insertAction = true;
      }


      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if( isset($values['auth_view']) ) {
        $authView = $values['auth_view'];
      } else {
        $authView = "everyone";
      }
      $viewMax = array_search($authView, $roles);
      foreach( $roles as $i => $role ) {
        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if( isset($values['auth_comment']) ) {
        $authComment = $values['auth_comment'];
      } else {
        $authComment = "everyone";
      }
      $commentMax = array_search($authComment, $roles);
      foreach ($roles as $i=>$role)
      {
        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
      }
      

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $video->tags()->addTagMaps($viewer, $tags);
      

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      if( $insertAction ) {
        $owner = $video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
        if( $action != null ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($video) as $action ) {
        $actionTable->resetActivityBindings($action);
      }


      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    if ($video->type == 'upload'){
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'video_general', true);
    }
    return $this->_helper->redirector->gotoRoute(array('user_id' => $viewer->getIdentity(), 'video_id' => $video->getIdentity()), 'video_view', true);
  }

  public function uploadVideoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($_FILES['Filedata']) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload').print_r($_FILES, true);
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if( in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->video()->createVideo($params, $_FILES['Filedata'], $values);

      $this->view->status   = true;
      $this->view->name     = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;

      // sets up title and owner_id now just incase members switch page as soon as upload is completed
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();

      $db->commit();
      return $video->video_id;
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.').$e;
      // throw $e;
      return;
    }
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $video = Engine_Api::_()->getItem('video', $this->getRequest()->getParam('video_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid()) return;

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    
    $this->view->form = $form = new Video_Form_Delete();

    if( !$video )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      Engine_Api::_()->getApi('core', 'video')->deleteVideo($video);
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true),
      'messages' => Array($this->view->message)
    ));
  }
  
  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));
    //Engine_Api::_()->core()->setSubject($video);
    if( !$this->_helper->requireSubject()->isValid() ) return;


    if( $viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('video_main', array(), 'video_main_manage');
    
    $this->view->video = $video;
    $this->view->form = $form = new Video_Form_Edit();
    $form->getElement('search')->setValue($video->search);
    $form->getElement('title')->setValue($video->title);
    $form->getElement('description')->setValue($video->description);
    $form->getElement('category_id')->setValue($video->category_id);


    // authorization
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    foreach( $roles as $role )
    {
      if( 1 === $auth->isAllowed($video, $role, 'view') )
      {
        $form->auth_view->setValue($role);
      }
      if( 1 === $auth->isAllowed($video, $role, 'comment') )
      {
        $form->auth_comment->setValue($role);
      }
    }
    
    // prepare tags
    $videoTags = $video->tags()->getTagMaps();

    $tagString = '';
    foreach( $videoTags as $tagmap )
    {
      if( $tagString !== '' ) $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $video->setFromArray($values);
      $video->save();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if($values['auth_view']) $authView =$values['auth_view'];
      else $authView = "everyone";
      $viewMax = array_search($authView, $roles);
      foreach( $roles as $i=>$role )
      {
        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if($values['auth_comment']) $authComment =$values['auth_comment'];
      else $authComment = "everyone";
      $commentMax = array_search($authComment, $roles);
      foreach ($roles as $i=>$role)
      {
        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $video->tags()->setTagMaps($viewer, $tags);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($video) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }


    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'video_general', true);
  }

  public function uploadAction()
  {
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-video', null, null, array('format' => 'json'));

    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->form = $form = new Video_Form_Video();
    $this->view->navigation = $this->getNavigation();

    if( !$this->getRequest()->isPost() )
    {
      if( null !== ($album_id = $this->_getParam('album_id')) )
      {
        $form->populate(array(
          'album' => $album_id
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    $album = $form->saveValues();
    //$this->_helper->redirector->gotoRoute(array('album_id'=>$album->album_id), 'album_editphotos', true);
  }

  public function viewAction()
  {
    //$video_id = $this->_getParam('video_id');
    //$video = Engine_Api::_()->getItem('video', $video_id);
    //if( $video ) Engine_Api::_()->core()->setSubject($video);
    if( !$this->_helper->requireSubject()->isValid() ) return;

    $video = Engine_Api::_()->core()->getSubject('video');
    $viewer = Engine_Api::_()->user()->getViewer();

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if( $message_id ) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if( $conversation->hasRecipient(Engine_Api::_()->user()->getViewer()) ) {
        $message_view = true;
      }
    }
    $this->view->message_view = $message_view;
    if( !$message_view && 
        !$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid() ) {
      return;
    }

    $this->view->videoTags = $video->tags()->getTagMaps();

    // Check if edit/delete is allowed
    $this->view->can_edit = $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
    $this->view->can_delete = $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

    // check if embedding is allowed
    $can_embed = true;
    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1) ) {
      $can_embed = false;
    } else if( isset($video->allow_embed) && !$video->allow_embed ) {
      $can_embed = false;
    }
    $this->view->can_embed = $can_embed;

    // increment count
    $embedded = "";
    if( $video->status == 1 ) {
      if( !$video->isOwner($viewer) ) {
        $video->view_count++;
        $video->save();
      }
      $embedded = $video->getRichContent(true);
    }
    
    if( $video->type == 'upload' && $video->status == 1 ) {
      if( !empty($video->file_id) ) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if( $storage_file ) {
          $this->view->video_location = $storage_file->map();
          $this->view->video_extension = $storage_file->extension;
        }
      }
    }

    $this->view->viewer_id = $viewer->getIdentity();
    $this->view->rating_count = Engine_Api::_()->video()->ratingCount($video->getIdentity());
    $this->view->video = $video;
    $this->view->rated = Engine_Api::_()->video()->checkRated($video->getIdentity(), $viewer->getIdentity());
    //Zend_Registry::get('Zend_View')?
    $this->view->videoEmbedded = $embedded;
    if( $video->category_id ) {
      $this->view->category = Engine_Api::_()->video()->getCategory($video->category_id);
    }

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }

  public function manageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$this->_helper->requireUser()->isValid() ) return;
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    // prepare categories
    $this->view->form = $form = new Video_Form_Search();
    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->video()->getCategories();
    foreach( $categories as $category )
    {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }
    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $this->view->category = $values['category'];

    $this->view->paginator = $paginator =
      Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 10);
    $this->view->paginator->setItemCountPerPage($items_count);
    
    $this->view->paginator->setCurrentPageNumber( $this->_getParam('page',1) );

    // maximum allowed videos
    $this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();
  }
  
  public function composeUploadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer->getIdentity() )
    {
      $this->_redirect('login');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composerType = $this->_getParam('c_type', 'wall');

    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $current_count = $paginator->getTotalItemCount();
    
    if (($current_count >= $quota)&& !empty($quota)){
      // return error message
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
      return;
    }
    $information = $this->handleIframelyInformation($video_url);
    if (empty($information)) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
      return;
    }
    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try
    {

      // create video
      $table = Engine_Api::_()->getDbtable('videos', 'video');
      $video = $table->createRow();
      $video->title = $information['title'];
      $video->description = $information['description'];
      $video->duration = $information['duration'];
      $video->owner_id = $viewer->getIdentity();
      $video->code = $information['code'];
      $video->type = $video_type;
      $video->save();

      // Now try to create thumbnail
      if ($information['thumbnail']) {
        $thumbnail = $information['thumbnail'];
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnailParsed = @parse_url($thumbnail);
        $validThumb = @GetImageSize($thumbnail) ? true : false;
        if( $validThumb && $thumbnail && $ext && $thumbnailParsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) ) {

          $tmpFile = APPLICATION_PATH . '/temporary/link_'.md5($thumbnail).'.'.$ext;
          $thumbFile = APPLICATION_PATH . '/temporary/link_thumb_'.md5($thumbnail).'.'.$ext;

          $srcFh = fopen($thumbnail, 'r');
          $tmpFh = fopen($tmpFile, 'w');
          stream_copy_to_stream($srcFh, $tmpFh, 1024 * 1024 * 2);

          $image = Engine_Image::factory();
          $image->open($tmpFile)
            ->resize(330, 240)
            ->write($thumbFile)
            ->destroy();

          $thumbFileRow = Engine_Api::_()->storage()->create($thumbFile, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));
          $video->photo_id = $thumbFileRow->file_id;
        }
      }
      // If video is from the composer, keep it hidden until the post is complete
      if( $composerType ) $video->search = 0;
      $video->status = 1;
      $video->save();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    // make the video public
    if( $composerType === 'wall' ) {
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach( $roles as $i => $role ) {
        $auth->setAllowed($video, $role, 'view', ($i <= $roles));
        $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
      }
    }

    $this->view->status = true;
    $this->view->video_id = $video->video_id;
    $this->view->photo_id = $video->photo_id;
    $this->view->title = $video->title;
    $this->view->description = $video->description;
    $photoUrl = $video->getPhotoUrl();
    $this->view->src = $photoUrl ? $photoUrl : '';
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
  }

  public function getIframelyInformationAction()
  {
    $url = trim(strip_tags($this->_getParam('uri')));
    $ajax = $this->_getParam('ajax', false);
    $information = $this->handleIframelyInformation($url);
    $this->view->ajax = $ajax;
    $this->view->valid = !empty($information['code']);
    $this->view->iframely = $information;
  }

  public function getNavigation()
  {
    $this->view->navigation = $navigation = new Zend_Navigation();
    $navigation->addPage(array(
      'label' => 'Browse Videos',
      'route' => 'video_general',
      'action' => 'browse',
      'controller' => 'index',
      'module' => 'video'
    ));

    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $navigation->addPages(array(
        array(
          'label' => 'My Videos',
          'route' => 'video_general',
          'action' => 'manage',
          'controller' => 'index',
          'module' => 'video'
        ),
        array(
          'label' => 'Post New Video',
          'route' => 'video_general',
          'action' => 'create',
          'controller' => 'index',
          'module' => 'video'
        )
      ));
    }

    return $navigation;
  }

  // HELPER FUNCTIONS

  public function handleIframelyInformation($uri)
  {
    $iframelyDisallowHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('video_iframely_disallow');
    if( parse_url($uri, PHP_URL_SCHEME) === null ) {
      $uri = "http://" . $uri;
    }
    $uriHost = Zend_Uri::factory($uri)->getHost();
    if( $iframelyDisallowHost && in_array($uriHost, $iframelyDisallowHost) ) {
      return;
    }
    $config = Engine_Api::_()->getApi('settings', 'core')->core_iframely;
    $iframely = Engine_Iframely::factory($config)->get($uri);
    if( !in_array('player', array_keys($iframely['links'])) ) {
      return;
    }
    $information = array('thumbnail' => '', 'title' => '', 'description' => '', 'duration' => '');
    if( !empty($iframely['links']['thumbnail']) ) {
      $information['thumbnail'] = $iframely['links']['thumbnail'][0]['href'];
      if( parse_url($information['thumbnail'], PHP_URL_SCHEME) === null ) {
        $information['thumbnail'] = str_replace(array('://', '//'), '', $information['thumbnail']);
        $information['thumbnail'] = "http://" . $information['thumbnail'];
      }
    }
    if( !empty($iframely['meta']['title']) ) {
      $information['title'] = $iframely['meta']['title'];
    }
    if( !empty($iframely['meta']['description']) ) {
      $information['description'] = $iframely['meta']['description'];
    }
    if( !empty($iframely['meta']['duration']) ) {
      $information['duration'] = $iframely['meta']['duration'];
    }
    $information['code'] = $iframely['html'];
    return $information;
  }
}
