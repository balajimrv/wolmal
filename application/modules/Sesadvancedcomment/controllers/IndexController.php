<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedcomment_IndexController extends Core_Controller_Action_Standard
{

  
  /**
   * Handles HTTP request to get an activity feed item's likes and returns a 
   * Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/viewlike
   * 
   * @return void
   */
  public function viewlikeAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);

    // Redirect if not json context
    if( null === $this->_getParam('format', null) ) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_getParam('format', null) ) {
      $this->view->body = $this->view->activity($action, array('viewAllLikes' => true, 'noList' => $this->_getParam('nolist', false)));
    }
  }

  /**
   * Handles HTTP request to like an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/like
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function likeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;
    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $page_id = $this->_getParam('page_id');
    $sbjecttype = $this->_getParam('sbjecttype',false);
    $subjectid = $this->_getParam('subjectid',false);
    if($subjectid){
      $mainFolder = 'list-comment/';  
      $fileName = '_subject';
    }else{
      $mainFolder = '';
      $fileName = '_activity';  
    }
      
    // Start transaction
   // $db = Engine_Api::_()->getDbtable('likes', 'sesadvancedactivity')->getAdapter();
   // $db->beginTransaction();
    try {
      if(!$sbjecttype)
        $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      else
        $action = Engine_Api::_()->getItem($sbjecttype,$subjectid);
  
      // Action
      if( !$comment_id ) {
        // Check authorization
        if( $action && !$sbjecttype && !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment') ) {
          $this->view->error = ('This user is not allowed to like this item');
        }
        
        if( $action->likes()->isLike($viewer) )
          $action->likes()->removeLike($viewer);
        $like = $action->likes()->addLike($viewer);
        $like->type = $this->_getParam('type',1);
        $like->save();
        $reactedType = $this->_getParam('type',1);
        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
          
          if($reactedType == 1) {
          
            //Remove Previous Notification
            Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => 'liked', "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $action->getType(), "object_id = ?" => $action->getIdentity()));
            
            Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->addNotification($actionOwner,$viewer, $action, 'liked', array('label' => 'post'));
          } else {
            if($reactedType == 2)
              $notiType = 'sesadvancedactivity_reacted_love';
            elseif($reactedType == 3)
              $notiType = 'sesadvancedactivity_reacted_haha';
            elseif($reactedType == 4)
              $notiType = 'sesadvancedactivity_reacted_wow';
            elseif($reactedType == 5)
              $notiType = 'sesadvancedactivity_reacted_angry';
            elseif($reactedType == 6)
              $notiType = 'sesadvancedactivity_reacted_sad';
            
            //Remove previous notification
            $reaction_array = array('liked', 'sesadvancedactivity_reacted_love', 'sesadvancedactivity_reacted_haha', 'sesadvancedactivity_reacted_wow', 'sesadvancedactivity_reacted_angry', 'sesadvancedactivity_reacted_sad');
            foreach($reaction_array as $reactionr) {
              Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $reactionr, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $action->getType(), "object_id = ?" => $action->getIdentity()));
            }
            
            //Send Reaction Notification
            Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->addNotification($actionOwner,$viewer, $action, $notiType, array('label' => 'post'));
          }
        }
      }
      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);
        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment') ) {
          $this->view->error = ('This user is not allowed to like this item');
        }
        $comment->likes()->addLike($viewer);
        // @todo make sure notifications work right
        if( $comment->poster_id != $viewer->getIdentity() ) {
          Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity')->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array('label' => 'comment'));
        }
        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
        }
      }
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
      //$db->commit();
    }

    catch( Exception $e )
    {
    //  $db->rollBack();
      $this->view->error = 'Error';
      //throw $e;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

    if( !$comment_id ) {
    $this->view->body = $this->view->partial(
                      $mainFolder.$fileName.'likereaction.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'action'=>$action)
                    );  
    }else{
      if($comment->parent_id){
        //reply
        $this->view->body = $this->view->partial(
                       $mainFolder.$fileName.'commentreply.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'action'=>$action,'canComment'=>1,'likeOptions'=>true)
                    );  
      }else{
        //main comment
        $this->view->body = $this->view->partial(
                       $mainFolder.$fileName.'commentbodyoptions.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'actionBody'=>$action,'canComment'=>1)
                      );
      }  
      
    }
  }

  /**
   * Handles HTTP request to remove a like from an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/unlike
   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function unlikeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $page_id = $this->_getParam('page_id');
    $sbjecttype = $this->_getParam('sbjecttype',false);
    $subjectid = $this->_getParam('subjectid',false);
     if($subjectid){
      $mainFolder = 'list-comment/';  
      $fileName = '_subject';
    }else{
      $mainFolder = '';
      $fileName = '_activity';  
    }
    // Start transaction
    $db = Engine_Api::_()->getDbtable('likes', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();

    try {
      if(!$sbjecttype)
      $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
      else
        $action = Engine_Api::_()->getItem($sbjecttype,$subjectid);
      // Action
      if( !$comment_id ) {

        // Check authorization
        if(!$subjectid &&  !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment') ) {
          $this->view->error = ('This user is not allowed to unlike this item');
        }
        
        //Remove reaction notification
        $reaction_array = array('liked', 'sesadvancedactivity_reacted_love', 'sesadvancedactivity_reacted_haha', 'sesadvancedactivity_reacted_wow', 'sesadvancedactivity_reacted_angry', 'sesadvancedactivity_reacted_sad');
        foreach($reaction_array as $reactionr) {
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $reactionr, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $action->getType(), "object_id = ?" => $action->getIdentity()));
        }
        
        $action->likes()->removeLike($viewer);
      }

      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment') ) {
          $this->view->error =  ('This user is not allowed to like this item');
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
       $this->view->error = 'error';
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

    // Redirect if not json context
    
    if( !$comment_id ) {
    $this->view->body = $this->view->partial(
                       $mainFolder.$fileName.'likereaction.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'action'=>$action)
                    );  
    }else{
      if($comment->parent_id){
        //reply
        $this->view->body = $this->view->partial(
                       $mainFolder.$fileName.'commentreply.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'action'=>$action,'canComment'=>1,'likeOptions'=>true)
                    );  
      }else{
        //main comment
        $this->view->body = $this->view->partial(
                       $mainFolder.$fileName.'commentbodyoptions.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'actionBody'=>$action,'canComment'=>1)
                      );
      }  
      
    }
    
  }

  /**
   * Handles HTTP request to get an activity feed item's comments and returns 
   * a Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/viewcomment
   *
   * @return void
   */
  public function viewcommentAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer    = Engine_Api::_()->user()->getViewer();
    $action    = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);        
    $this->view->body = $this->view->activity($action, array('noList' => $this->_getParam('nolist', true),'page'=>$this->_getParam('page'),'onlyComment'=>true,'type'=>$this->_getParam('searchtype','')),'update');
    echo json_encode(array('status'=> true,'body'=>$this->view->body),JSON_HEX_QUOT | JSON_HEX_TAG);die;
  }
  
  public function viewcommentreplyAction()
  {
    // Collect params
    $comment_id = $this->_getParam('comment_id');
    $comment = Engine_Api::_()->getItem($this->_getParam('moduleN').'_comment',$comment_id);
    $page = $this->_getParam('page');
    $viewer    = Engine_Api::_()->user()->getViewer();
    $action_id = $this->_getParam('action_id');
    $action    = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);  
    $this->view->body = $this->view->partial(
                      '_activitycommentbody.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'action'=>$action,'page'=>$page,'viewmore'=>true)
                    );
    echo json_encode(array('status'=> true,'body'=>$this->view->body),JSON_HEX_QUOT | JSON_HEX_TAG);die;
  }
  public function viewcommentreplysubjectAction()
  {
    // Collect params
    $comment_id = $this->_getParam('comment_id');
    $comment = Engine_Api::_()->getItem('core_comment',$comment_id);
    $page = $this->_getParam('page');
    $viewer    = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->getItem($this->_getParam('type'),$this->_getParam('action_id'));
    $this->view->body = $this->view->partial(
                      'list-comment/_subjectcommentbody.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'subject'=>$subject,'page'=>$page,'viewmore'=>true)
                    );
    echo json_encode(array('status'=> true,'body'=>$this->view->body),JSON_HEX_QUOT | JSON_HEX_TAG);die;
  }
  /**
   * Handles HTTP POST request to comment on an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/comment
   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function commentAction()         
  {     
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    
    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }
    $subject_id = $this->_getParam('subject_id',false);
    $subject_type = $this->_getParam('subject_type',false);

    // Start transaction
    if(!$subject_id)
      $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
    else{
      $action = Engine_Api::_()->getItem($subject_type,$subject_id);
      $db = Engine_Api::_()->getItemtable($action->getType())->getAdapter();
    }
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
      if(!$subject_id){
       $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
       $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
      }else{
        //$action = Engine_Api::_()->getItem($subject_type,$subject_id);
        $actionOwner = $action->getOwner();
      }
      if (!$action) {
        $this->view->status = false;
        $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
      }
      
      $body = !empty($_POST['bodymention']) ? $_POST['bodymention'] : $_POST['body'];
      $emoji_id = $_POST['emoji_id'];
      // Check authorization
      if (!$subject_id && !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment'))
        throw new Engine_Exception('This user is not allowed to comment on this item.');

      // Add the comment
      $comment =  $action->comments()->addComment($viewer, $body);
      $typeC = $comment->getType();
      $comment = Engine_Api::_()->getItem($typeC,$comment->comment_id);
      $file_id = trim(str_replace(',,','',$_POST['file_id']),',');
      if($file_id && $file_id != ''){
        $counter = 1;
        $file_ids = explode(',',$file_id);
        $tableCommentFile = Engine_Api::_()->getDbtable('commentfiles', 'sesadvancedcomment');
        foreach($file_ids as $file_id){
          if(!$file_id)
            continue;
          $file = $tableCommentFile->createRow();
          if(strpos($file_id,'_album_photo')){
            $file->type = 'album_photo';
            $file->file_id = str_replace('_album_photo','',$file_id);
          }else{
            $file->type = 'video';
            $file->file_id = str_replace('_video','',$file_id);
          }
          $file->comment_id = $comment->getIdentity();
          $file->save();
          if($counter == 1){
            $comment->file_id = $file_id;
            $comment->save(); 
          }
          $counter++;
        }
      }
      if($emoji_id){
        $comment->emoji_id = $emoji_id;
        $comment->file_id = 0;
        $comment->body = '';
        $comment->save();
      }      
      //fetch link from comment
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $matches);
      if(!empty($matches[0])){
        $preview = $this->previewCommentLink($matches[0],$comment,$viewer); 
        if($preview){
          $comment->preview = $preview;
          $comment->save();  
        } 
      }
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'sesadvancedactivity');
      // Add notification for owner of activity (if user and not viewer)
      if( (!$subject_id && $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) || ($subject_id && !$viewer->isSelf($actionOwner)) )
      {
        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
          'label' => 'post'
        ));
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->comments()->getAllCommentsUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->likes()->getAllLikesUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      //Tagging People by status box
      preg_match_all('/(^|\s)(@\w+)/', $_POST['bodymention'], $result);
      $commentLink = '<a href="' . $comment->getHref() . '">' . "comment" . '</a>';
      foreach($result[2] as $value) {
        $user_id = str_replace('@_user_','',$value);
        $item = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedcomment_tagged_people', array("commentLink" => $commentLink));
      }
      //Tagging People by status box
      
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
      
      $db->commit();
    }

    catch( Exception $e )
    {
      throw $e;
      $db->rollBack();
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_($e);
    }

    // Assign message for json
    $this->view->status = true;
    $this->view->message = 'Comment posted';
    
    $method = 'update';
    $show_all_comments = $this->_getParam('show_all_comments',false);
    if(!$subject_id){
      $commentStats = $this->view->partial('_activitylikereaction.tpl','sesadvancedcomment',array('action'=>$action));
      $this->view->body =  $this->view->partial(
                        '_activitycommentbody.tpl',
                        'sesadvancedcomment',
                        array('comment'=>$comment,'action'=>$action)
                      );
    }else{
      $commentStats = $this->view->partial('list-comment/_subjectlikereaction.tpl','sesadvancedcomment',array('action'=>$action));
      $this->view->body =  $this->view->partial(
                      'list-comment/_subjectcommentbody.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'action'=>$action)
                    );
        
    }
    echo json_encode(array('status'=> $this->view->status,'content'=>$this->view->body,error=>$this->view->error,'commentStats'=>$commentStats),JSON_HEX_QUOT | JSON_HEX_TAG);die;
    
  }
   public function previewCommentLink($url,$comment,$viewer){
    
         $contentLink = Engine_Api::_()->sesadvancedcomment()->getMetaTags($url);
         if(!empty($contentLink['title']) && !empty($contentLink['image'])){
            $image = $contentLink['image'];
            $title = $contentLink['title'];
            if(strpos($contentLink['image'],'http') === false){
              $parseUrl = parse_url($url);
              $image = $parseUrl['scheme'].'://'.$parseUrl['host'].'/'.ltrim($contentLink['image'],'/');
            }
         }
          $table = Engine_Api::_()->getDbtable('links', 'core');
          $link = $table->createRow();
          $data['uri'] = $url;
          $data['title'] = $title;
          $data['parent_type']  = $comment->getType();
          $data['parent_id']  = $comment->getIdentity();
          $data['search']  = 0;
          $data['photo_id']  = 0;
          $link->setFromArray($data);
          $link->owner_type = $viewer->getType();
          $link->owner_id = $viewer->getIdentity();
          $thumbnail = (string) @$image;
          $thumbnail_parsed = @parse_url($thumbnail);
          if( $thumbnail && $thumbnail_parsed ){
            $tmp_path = APPLICATION_PATH . '/temporary/link';
            $tmp_file = $tmp_path . '/' . md5($thumbnail);
              if( is_dir($tmp_path) ) { 
                $src_fh = fopen($thumbnail, 'r');
                $tmp_fh = fopen($tmp_file, 'w');
                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                fclose($src_fh);
                fclose($tmp_fh);
                if( ($info = getimagesize($tmp_file)) && !empty($info[2]) ) {
                  $ext = Engine_Image::image_type_to_extension($info[2]);
                  $thumb_file = $tmp_path . '/thumb_'.md5($thumbnail) . '.'.$ext;
                  $image = Engine_Image::factory();
                  $image->open($tmp_file)
                    ->autoRotate()
                    ->resize(500, 500)
                    ->write($thumb_file)
                    ->destroy();
                  $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                    'parent_type' => $link->getType(),
                    'parent_id' => $link->getIdentity()
                  ));
                  $link->photo_id = $thumbFileRow->file_id;
                  @unlink($thumb_file);
                  @unlink($tmp_file); 
                  $link->save();
                  return $link->getIdentity();                  
                }
              }             
          }
        return false;
   }
   public function editCommentAction()         
  {     
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    
    // Not post
     if( !$this->getRequest()->isPost() )
      {
        $this->view->status = false;
        $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
        return;
      }
      $resource_id = $this->_getParam('resource_id','');
      $resource_type = $this->_getParam('resource_type','');
      $comment_id = $this->view->comment_id = $this->_getParam('comment_id', null);
      $module = $this->_getParam('modulecomment','');
      if(!$resource_id)
        $comment = Engine_Api::_()->getItem($module.'_comment',$comment_id);
      else
        $comment = Engine_Api::_()->getItem('core_comment',$comment_id);
      
      //previous body
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $previousmatches);
      $comment->body = !empty($_POST['bodymention']) ? $_POST['bodymention'] : $_POST['body'];;;
      $execute = false;
      $file_id = trim(str_replace(',,','',$_POST['file_id']),',');
      if($file_id && $file_id != ''){
        $counter = 1;
        $file_ids = explode(',',$file_id);
        $tableCommentFile = Engine_Api::_()->getDbtable('commentfiles', 'sesadvancedcomment');
        $tableCommentFile->delete(array('comment_id =?'=>$comment->comment_id));
        foreach($file_ids as $file_id){
          if(!$file_id)
            continue;
          $file = $tableCommentFile->createRow();
          if(strpos($file_id,'_album_photo')){
            $file->type = 'album_photo';
            $file->file_id = str_replace('_album_photo','',$file_id);
          }else{
            $file->type = 'video';
            $file->file_id = str_replace('_video','',$file_id);
          }
          $file->comment_id = $comment->getIdentity();
          $file->save();
          if($counter == 1){
            $comment->file_id = $file_id;
            $comment->save(); 
          }
          $execute = true;
          $counter++;
        }
      }
      if(!$execute)
      {
        $comment->file_id = 0;
      }
      $emoji_id = $_POST['emoji_id'];
      if($emoji_id){
        $comment->emoji_id = $emoji_id;
        $comment->file_id = 0;
        $comment->body = '';
        $comment->save();
      }
      $comment->save();
      //fetch link from comment
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $matches);
      if(!empty($matches[0]) && $previousmatches != $matches){
         $viewer = Engine_Api::_()->user()->getViewer();
        $preview = $this->previewCommentLink($matches[0],$comment,$viewer); 
        if($preview){
          $comment->preview = $preview;
          $comment->save();  
        } 
      }else if(empty($matches[0]) && $comment->preview){
          $comment->preview = 0;
          $comment->save();
          $link = Engine_Api::_()->getItem('core_link',$comment->preview);
          $link->delete();
      }
    //$showAllComments = $this->_getParam('show_all_comments', false);    
    if(!$resource_id){       
      $this->view->body = $this->view->partial(
                      '_activitycommentcontent.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'nolist'=>true)
                    );
    }else{
      $this->view->body = $this->view->partial(
                      'list-comment/_subjectcommentcontent.tpl',
                      'sesadvancedcomment',
                      array('comment'=>$comment,'nolist'=>true)
                    );
    }
   echo json_encode(array('status'=> 1,'content'=>$this->view->body),JSON_HEX_QUOT | JSON_HEX_TAG);die;
    
  }
  public function editReplyAction()         
  {     
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    
    // Not post
     if( !$this->getRequest()->isPost() )
      {
        $this->view->status = false;
        $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
        return;
      }
      $resource_id = $this->_getParam('resource_id',false);
      $resource_type = $this->_getParam('resource_type',false);
      $comment_id = $this->view->comment_id = $this->_getParam('comment_id', $this->_getParam('comment_id', null));
      if(!$resource_id){
        $module = $this->_getParam('modulecomment','');
        $comment = Engine_Api::_()->getItem($module.'_comment',$comment_id);
      }else
        $comment = Engine_Api::_()->getItem('core_comment',$comment_id);
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $matches);
      $comment->body = !empty($_POST['bodymention']) ? $_POST['bodymention'] : $_POST['body'];;;
      $file_id = $_POST['file_id'];
      
      // Add the comment
      
      $execute = false;
      $file_id = trim(str_replace(',,','',$_POST['file_id']),',');
      if($file_id && $file_id != ''){
        $counter = 1;
        $file_ids = explode(',',$file_id);
        $tableCommentFile = Engine_Api::_()->getDbtable('commentfiles', 'sesadvancedcomment');
        $tableCommentFile->delete(array('comment_id =?'=>$comment->comment_id));
        foreach($file_ids as $file_id){
          if(!$file_id)
            continue;
          $file = $tableCommentFile->createRow();
          if(strpos($file_id,'_album_photo')){
            $file->type = 'album_photo';
            $file->file_id = str_replace('_album_photo','',$file_id);
          }else{
            $file->type = 'video';
            $file->file_id = str_replace('_video','',$file_id);
          }
          $file->comment_id = $comment->getIdentity();
          $file->save();
          if($counter == 1){
            $comment->file_id = $file_id;
            $comment->save(); 
          }
          $execute = true;
          $counter++;
        }
      }
      if(!$execute)
      {
        $comment->file_id = 0;
      }
      $emoji_id = $_POST['emoji_id'];
      if($emoji_id){
        $comment->emoji_id = $emoji_id;
        $comment->file_id = 0;
        $comment->body = '';
        $comment->save();
      }
      $comment->save();
      //fetch link from comment
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $matches);
      if(!empty($matches[0]) && $previousmatches != $matches){
         $viewer = Engine_Api::_()->user()->getViewer();
        $preview = $this->previewCommentLink($matches[0],$comment,$viewer); 
        if($preview){
          $comment->preview = $preview;
          $comment->save();  
        } 
      }else if(empty($matches[0]) && $comment->preview){
          $comment->preview = 0;
          $comment->save();
          $link = Engine_Api::_()->getItem('core_link',$comment->preview);
          $link->delete();
      }
    if(!$resource_id)
    //$showAllComments = $this->_getParam('show_all_comments', false);           
    $this->view->body = $this->view->partial(
                      '_activitycommentreplycontent.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'nolist'=>true)
                    );
    else
      $this->view->body = $this->view->partial(
                      'list-comment/_subjectcommentreplycontent.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'nolist'=>true)
                    );
   echo json_encode(array('status'=> 1,'content'=>$this->view->body),JSON_HEX_QUOT | JSON_HEX_TAG);die;
    
  }
  
  /**
   * Handles HTTP POST request to delete a comment or an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/delete
   *
   * @return void
   */
  function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    
    
    // Identify if it's an action_id or comment_id being deleted
    $this->view->comment_id = $comment_id = (int) $this->_getParam('comment_id', null);
    $this->view->action_id  = $action_id  = (int) $this->_getParam('action_id', null);
    $type = $this->_getParam('type',false);
    if(!$type)
    $action       = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
    else
      $action = Engine_Api::_()->getItem($type,$action_id);
    if (!$action){
      // tell smoothbox to close
      $this->view->status  = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot delete this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }

    // Send to view script if not POST
    if (!$this->getRequest()->isPost())
      return;
      

    // Both the author and the person being written about get to delete the action_id
    if (!$comment_id && (
        $activity_moderate ||
        ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
        ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id)))   // commenter
    {
      // Delete action item and all comments/likes
      $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
      $db->beginTransaction();
      try {
        $action->deleteItem();
        $db->commit();

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');
        $this->view->smoothboxClose = true;
        return $this->render('deletedItem');
      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }

    } elseif ($comment_id) {
        $comment = $action->comments()->getComment($comment_id);
        // allow delete if profile/entry owner
        $db = Engine_Api::_()->getDbtable('comments', 'sesadvancedactivity')->getAdapter();
        $db->beginTransaction();
        if ($type || ($activity_moderate ||
           ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
           ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id)))
        {
          try {
            $action->comments()->removeComment($comment_id);
            
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted');
            
            if($comment->parent_id){
              $parentCommentType = 'core_comment';
        
              if($action->getType() == 'activity_action'){
                $commentType = $action->likes(true);
                if($commentType->getType() == 'activity_action')
                  $parentCommentType = 'activity_comment';
              }
              $parentCommentId = $comment->parent_id;
              $parentComment = Engine_Api::_()->getItem($parentCommentType,$parentCommentId);
              $parentComment->reply_count = new Zend_Db_Expr('reply_count - 1');
              $parentComment->save();
            }
            $this->view->commentCount = Engine_Api::_()->sesadvancedcomment()->commentCount($action,'subject');
            $this->view->action = $action;
            $db->commit();
            return $this->render('deletedComment');
          } catch (Exception $e) {
            $db->rollback();
            throw $e;
            $this->view->status = false;
          }
        } else {
          $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
          return $this->render('deletedComment');
        }
      
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_forward('requireauth', 'error', 'core');
    }

  }
   public function replyAction()         
  {     
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    
    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }

    

    // Start transaction
    $db = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $resource_type = $this->_getParam('resource_type',false);
      if(!$resource_type){
        $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
        $action = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity')->getActionById($action_id);
        $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
      }else{
        $action = Engine_Api::_()->getItem($resource_type,$this->_getParam('resource_id'));  
        $actionOwner = $action->getOwner();
      }
      if (!$action) {
        $this->view->status = false;
        $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
      }
      
      $body = !empty($_POST['bodymention']) ? $_POST['bodymention'] : $_POST['body'];;
      
      // Check authorization
      if (!$resource_type && !Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment'))
        throw new Engine_Exception('This user is not allowed to comment on this item.');

      // Add the comment
      $comment =  $action->comments()->addComment($viewer, $body);
      $typeC = $comment->getType();
      $comment = Engine_Api::_()->getItem($typeC,$comment->comment_id);
       $file_id = trim(str_replace(',,','',$_POST['file_id']),',');
      if($file_id && $file_id != ''){
        $counter = 1;
        $file_ids = explode(',',$file_id);
        $tableCommentFile = Engine_Api::_()->getDbtable('commentfiles', 'sesadvancedcomment');
        foreach($file_ids as $file_id){
          if(!$file_id)
            continue;
          $file = $tableCommentFile->createRow();
          if(strpos($file_id,'_album_photo')){
            $file->type = 'album_photo';
            $file->file_id = str_replace('_album_photo','',$file_id);
          }else{
            $file->type = 'video';
            $file->file_id = str_replace('_video','',$file_id);
          }
          $file->comment_id = $comment->getIdentity();
          $file->save();
          if($counter == 1){
            $comment->file_id = $file_id;
            $comment->save(); 
          }
          $counter++;
        }
      }
      $emoji_id = $_POST['emoji_id'];
      if($emoji_id){
        $comment->emoji_id = $emoji_id;
        $comment->file_id = 0;
        $comment->body = '';
        $comment->save();
      }
      $parentCommentType = 'core_comment';
      
      if($action->getType() == 'activity_action'){
        $commentType = $action->likes(true);
        if($commentType->getType() == 'activity_action')
          $parentCommentType = 'activity_comment';
      }
      $parentCommentId = $this->_getParam('comment_id',false);
      $parentComment = Engine_Api::_()->getItem($parentCommentType,$parentCommentId);
      $parentComment->reply_count = new Zend_Db_Expr('reply_count + 1');
      $parentComment->save();
      $comment->parent_id = $parentCommentId;   
      $comment->save();   
      //fetch link from comment
      $regex = '/https?\:\/\/[^\" ]+/i';
      $string = $comment->body;
      preg_match($regex, $string, $matches);
      if(!empty($matches[0])){
        $preview = $this->previewCommentLink($matches[0],$comment,$viewer); 
        if($preview){
          $comment->preview = $preview;
          $comment->save();  
        } 
      }

      //Tagging People by status box
      preg_match_all('/(^|\s)(@\w+)/', $_POST['bodymention'], $result);
      $commentLink = '<a href="' . $comment->getHref() . '">' . "reply" . '</a>';
      foreach($result[2] as $value) {
        $user_id = str_replace('@_user_','',$value);
        $item = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($item, $viewer, $viewer, 'sesadvancedcomment_taggedreply_people', array("commentLink" => $commentLink));
      }
      //Tagging People by status box
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_($e);
    }

    // Assign message for json
    $this->view->status = true;
    $this->view->message = 'Comment posted';
    
    $method = 'update';
    $show_all_comments = $this->_getParam('show_all_comments',false);
    if(!$resource_type)
    //$showAllComments = $this->_getParam('show_all_comments', false);           
    $this->view->body = $this->view->partial(
                      '_activitycommentreply.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'action'=>$action)
                    );
   else
    $this->view->body = $this->view->partial(
                      'list-comment/_subjectcommentreply.tpl',
                      'sesadvancedcomment',
                      array('commentreply'=>$comment,'action'=>$action)
                    );
   echo json_encode(array('status'=> $this->view->status,'content'=>$this->view->body,error=>$this->view->error),JSON_HEX_QUOT | JSON_HEX_TAG);die;
    
  }
  public function getLikesAction()
  {
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');

    if( !$action_id ||
        !$comment_id ||
        !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
        !($comment = $action->comments()->getComment($comment_id)) ) {
      $this->view->status = false;
      $this->view->body = '-';
      return;
    }

    $likes = $comment->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
      count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }
  public function emojiAction(){
    $this->renderScript('_emoji.tpl');
  }
  //album photo upload function
  public function uploadFileAction() { 
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
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum'))
      $module = 'sesalbum';
    else
      $module = 'album';
    $table = Engine_Api::_()->getDbtable('albums', $module);
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $type = $this->_getParam('type', 'wall');
      if (empty($type))
        $type = 'wall';
      $album = $table->getSpecialAlbum($viewer, $type);
      $photoTable = Engine_Api::_()->getDbtable('photos', $module);
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
      //throw $e;
      $this->view->status = false;
    }
     echo json_encode(array('src'=>$this->view->src ,'photo_id'=>$this->view->photo_id,'status'=>$this->view->status));die;  
      
  }

  
  public function removepreviewAction() {
    $comment_id = $this->_getParam('comment_id', null);
    $type = $this->_getParam('type', null);
    if(empty($type))
      return;
    if($type == 'core_comment') {
      Engine_Api::_()->getDbtable('comments', 'core')->update(array('showpreview' => 1), array('comment_id = ?' => $comment_id));
    } elseif($type == 'activity_comment') {
      Engine_Api::_()->getDbtable('comments', 'sesadvancedactivity')->update(array('showpreview' => 1), array('comment_id = ?' => $comment_id));
    }
  }
}
