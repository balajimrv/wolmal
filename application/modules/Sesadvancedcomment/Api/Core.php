<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesadvancedcomment_Api_Core extends Core_Api_Abstract {
  public function getMutualFriendCount($subject, $viewer) {
    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name');
    $col1 = 'resource_id';
    $col2 = 'user_id';
    $select = new Zend_Db_Select($friendsTable->getAdapter());
    $select
            ->from($friendsName, $col1)
            ->join($friendsName, "`{$friendsName}`.`{$col1}`=`{$friendsName}_2`.{$col1}", null)
            ->where("`{$friendsName}`.{$col2} = ?", $viewer->getIdentity())
            ->where("`{$friendsName}_2`.{$col2} = ?", $subject->getIdentity())
            ->where("`{$friendsName}`.active = ?", 1)
            ->where("`{$friendsName}_2`.active = ?", 1)
    ;
    // Now get all common friends
    $uids = array();
    foreach ($select->query()->fetchAll() as $data) {
      $uids[] = $data[$col1];
    }
    // Do not render if nothing to show
    if (count($uids) <= 0) {
      return 0;
    }
    // Get paginator
    $usersTable = Engine_Api::_()->getItemTable('user');
    $select = $usersTable->select()->from($usersTable->info('name'), new Zend_Db_Expr('COUNT(user_id)'))->where('user_id IN(?)', $uids);
    return $select->query()->fetchColumn();
  }
 public function likesGroup($action,$subject = false){
   if(!$subject)
     $resource = $action->likes(true);
   else
    $resource = $action;
   if($resource->getType() == 'activity_action'){
      $table = Engine_Api::_()->getItemTable('activity_like'); 
      $select = $table->select(); 
   }else{
      $table = Engine_Api::_()->getItemTable('core_like'); 
      $select = $table->select(); 
      $select->where('resource_type = ?', $resource->getType());
   }
   $select
      ->where('resource_id = ?', $resource->getIdentity())
      ->order('like_id ASC')
      ->group('type')
      ->from($table->info('name'),array('counts'=>new Zend_Db_Expr('COUNT(like_id)'),'type'));
   return array('data'=>$table->fetchAll($select),'resource_type'=>$resource->getType(),'resource_id'=>$resource->getIdentity());
 }
 public function getReply($comment_id, $page = 'zero',$subject){  
    $select = $subject->comments()->getCommentSelect();
    $select->where('parent_id =?', $comment_id);
    if($page == 'zero'){
       $commentCount = count($select->query()->fetchAll());
       $page = ceil($commentCount/5);
    }
    $select->reset('order');
   
    $select->order('comment_id ASC');
    $comments = Zend_Paginator::factory($select);
    $comments->setCurrentPageNumber($page);
    $comments->setItemCountPerPage(5);
    return $comments;
  }
  public function commentCount($action,$subject = false){
    if(!$subject)
     $resource = $action->comments(true);
    else
      $resource = $action;
   if($resource->getType() == 'activity_action'){
      $table = Engine_Api::_()->getItemTable('activity_comment'); 
      $select = $table->select(); 
   }else{
      $table = Engine_Api::_()->getItemTable('core_comment'); 
      $select = $table->select(); 
      $select->where('resource_type = ?', $resource->getType());
   }
   $select->where('parent_id =?',0);
   $select
      ->where('resource_id = ?', $resource->getIdentity())
      ->group('resource_id')
      ->from($table->info('name'), new Zend_Db_Expr('COUNT(1) as count'));
   $data = $select->query()->fetchAll();
      
   return  (int) $data[0]['count'];
 }
 
 public function likeImage($type = 1) {
 
  $table = Engine_Api::_()->getDbtable('reactions', 'sesadvancedcomment');
  $file_id = $table->select()
    ->from($table->info('name'), 'file_id')
    ->where('enabled = ?', 1)
    ->where('reaction_id = ?', $type)
    ->query()
    ->fetchColumn(0);
  if($file_id) {
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id);
    if($file)
      return $file->map();
  }
//    if($type == 1)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-like.png';
//    elseif ($type == 2)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-love.png';
//    elseif ($type == 3)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-haha.png';
//     elseif ($type == 4)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-wow.png';
//    elseif ($type == 5)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-angery.png';
//    elseif ($type == 6)
//       return 'application/modules/Sesadvancedcomment/externals/images/icon-sad.png';
      
 }
 
 public function likeWord($type = 1) {
 
  $table = Engine_Api::_()->getDbtable('reactions', 'sesadvancedcomment');
  return $table->select()
    ->from($table->info('name'), 'title')
    ->where('enabled = ?', 1)
    ->where('reaction_id = ?', $type)
    ->query()
    ->fetchColumn(0);
    
//    if($type == 1)
//       return 'Like';
//    elseif ($type == 2)
//       return 'Love';
//    elseif ($type == 3)
//       return 'Haha';
//     elseif ($type == 4)
//       return 'Wow';
//    elseif ($type == 5)
//       return 'Angry';
//    elseif ($type == 6)
//       return 'Sad';
 }
 
 function getMetaTags($url = false){
    if(!$url)
      return;
    $doc = new DOMDocument;
    $content = file_get_contents($url);
    preg_match("/<title>(.+)<\/title>/siU", $content, $matches); 
    $title =  !empty($matches[1]) ? $matches[1] : '';
    
    @$doc->loadHTML($content);
    $metas =  $doc->getElementsByTagName('meta');
    $image = '';
    for ($i = 0; $i < $metas->length; $i++)
    {
        $meta = $metas->item($i);
        if($meta->getAttribute('property') == 'og:image'){
          $image = $meta->getAttribute('content');
          break;
        }
    }
    if(!$image){
      $tags = $doc ->getElementsByTagName('img');
      $arr = array();
      $counter = 0;
      foreach ($tags as $tag) {
        $src = $tag->getAttribute('src');
        if(strpos($src,'http') === false){
          $parseUrl = parse_url($url);
          $src = $parseUrl['scheme'].'://'.$parseUrl['host'].'/'.ltrim($src,'/');
         }
          list($width, $height) = getimagesize($src);
          if($width < 100 || $height < 100)
            continue;
          $arr[] = $src ;
          if($counter > 10)
            break;
          $counter++;
      }
      shuffle($arr);
      $image =  $arr[0];
    }
    return array('title'=>$title,'image'=>$image)  ;   
 }
}