<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AjaxController.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesadvancedcomment_AjaxController extends Core_Controller_Action_Standard
{
 
   public function emojiAction(){
     $this->view->edit = $this->_getParam('edit',false);
    $this->renderScript('_emoji.tpl');
  }
  public function emojiContentAction(){
    $galleryId = $this->_getParam('gallery_id');
    $this->view->files = Engine_Api::_()->getDbTable('emotionfiles','sesadvancedcomment')->getFiles(array('fetchAll'=>true,'gallery_id'=>$galleryId));
  }
  public function searchReactionAction(){
    $text = $this->_getParam('text','');
    $this->view->files = Engine_Api::_()->getDbTable('emotioncategories','sesadvancedcomment')->searchResult($text); 
    return $this->render('emoji-content');
  }
  public function reactionAddAction(){
    
    $this->view->storepopupTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.stickertitle', 'Sticker Store');
    $this->view->storepopupDesciption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.stickerdescription', 'Find new stickers to send to friends');
    $this->view->storebackgroundimage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.backgroundimage', 'public/admin/store-header-bg.png');
    
    $this->view->gallery = Engine_Api::_()->getDbTable('emotiongalleries','sesadvancedcomment')->getGallery(array('fetchAll'=>true));
    $useremotions = Engine_Api::_()->getDbTable('useremotions','sesadvancedcomment')->getEmotion();  
    $useremotionsArray = array();
    foreach($useremotions as $userEmo)
      $useremotionsArray[] = $userEmo->gallery_id;
    $this->view->useremotions = $useremotionsArray;
  }
  public function actionReactionAction(){
    $action = $this->_getParam('actionD',false);
    $gallery_id = $this->_getParam('gallery_id',false);
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();  
    if($action == 'add'){
      $data = array(
          'gallery_id' => $gallery_id,
          'user_id' => $viewer->getIdentity(),
      );
     $db->insert('engine4_sesadvancedcomment_useremotions', $data);
    }else{
      $db->delete('engine4_sesadvancedcomment_useremotions', array(
          'gallery_id =?'      => $gallery_id,
          'user_id =?' => $viewer->getIdentity(),
      ));
    }
     echo true;die;  
  }
  public function previewReactionAction(){
    $gallery_id = $this->_getParam('gallery_id',false);
    $this->view->gallery = Engine_Api::_()->getItem('sesadvancedcomment_emotiongallery',$gallery_id);
    $this->view->useremotions =(int) count(Engine_Api::_()->getDbTable('useremotions','sesadvancedcomment')->getEmotion(array('gallery_id'=>$gallery_id)));  
    $this->view->files = Engine_Api::_()->getDbTable('emotionfiles','sesadvancedcomment')->getFiles(array('fetchAll'=>true,'gallery_id'=>$gallery_id));
  }
  public function commentLikesAction(){
		$this->view->resource_id = $resource_id = $this->_getParam('id');
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax_content',false);
    $this->view->comment_id = $comment_id = $this->_getParam('comment_id');
    $resource_type = 'core_comment';
    $action = Engine_Api::_()->getItem($resource_type,$resource_id);
    
    $table = Engine_Api::_()->getItemTable('core_like'); 
    $this->view->title = $this->view->translate('People Who Like This');
    $this->view->page = $page = $this->_getParam('page',1);
    $select = $table->select()->where('resource_id =?',$comment_id);
    $select->where('resource_type =?',$resource_type);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page',1));
    $users = array();
    foreach( $paginator as $data )
    {
      if( $data['poster_type'] == 'user' )
      {
        $users[] = $data['poster_id'];
      }
    }
    $users = array_values(array_unique($users));
    $this->view->users =  Engine_Api::_()->getItemMulti('user', $users);
    if($is_ajax){
      echo $this->view->partial(
            '_contentlikesuser.tpl',
            'sesadvancedcomment',
            array('users'=>$this->view->users,'paginator'=>$this->view->paginator,'randonNumber'=>'contentlikeusers','resource_id'=>$this->view->resource_id,'resource_type'=>$resource_type,'execute'=>true,'page'=>$this->view->page)
          );die;  
      
    }
	}
	public function likesAction(){
		$this->view->resource_id = $resource_id = $this->_getParam('id');
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax_content',false);
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $this->view->typeSelected = $typeSelected = $this->_getParam('type','all');
    $this->view->item_id = $item_id = $this->_getParam('item_id',false);
    if(!$typeSelected)
      $this->view->typeSelected = $typeSelected  = 'all';
    $action = Engine_Api::_()->getItem($resource_type,$resource_id);
    $table = Engine_Api::_()->getItemTable('core_like'); 
    $this->view->page = $page = $this->_getParam('page',1);
    $select = $table->select()->where('resource_id =?',$item_id);
    $select->where('resource_type =?',$resource_type);
    if($typeSelected != 'all')
      $select->where('type =?',$typeSelected);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page',1));
    $users = array();
    $type = array();
    foreach( $paginator as $data )
    {
      $type[$data['poster_id']] = $data['type'];
      if( $data['poster_type'] == 'user' )
      {
        $users[] = $data['poster_id'];
      }
    }
    $users = array_values(array_unique($users));
    $this->view->type = $type;
    if(!$is_ajax){
      $AllTypesCount = Engine_Api::_()->sesadvancedcomment()->likesGroup($action,'subject');
      $this->view->AllTypesCount = $AllTypesCount['data'];
      $countAllLikes = 0;
      $typesLikeData = array('all'=>'all');
      foreach($this->view->AllTypesCount as $countlikes){
        $typesLikeData[$countlikes['type']] = $countlikes['type'];
       $countAllLikes = $countAllLikes+ $countlikes['counts'];
      }
      
      $this->view->typesLikeData = $typesLikeData;
      $this->view->countAll =$countAllLikes;
    }
    
    $this->view->users =  Engine_Api::_()->getItemMulti('user', $users);
    
    if($is_ajax){
      echo $this->view->partial(
            '_reactionlikesuser.tpl',
            'sesadvancedcomment',
            array('users'=>$this->view->users,'paginator'=>$this->view->paginator,'randonNumber'=>$this->view->typeSelected,'resource_id'=>$this->view->resource_id,'resource_type'=>$this->view->resource_type,'typeSelected'=>$this->view->typeSelected,'execute'=>true,'page'=>$this->view->page,'type'=>$this->view->type,'item_id'=>$item_id)
          );die;  
      
    }
	}
  public function friendsAction(){
    
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      

     
        $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();          
      
         
      
      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
        $select->limit($limit);
      }

      if( null !== ($text = $this->_getParam('query', '')) ) {
        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
      }
      
      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend ) {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'name' => $friend->getTitle(),
          'avatar' => $this->view->itemPhoto($friend, 'thumb.icon'),
        );
      }
    }

      return $this->_helper->json($data);
    
    
  }
}