<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _activitycommentcontent.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $comment = $this->comment; ?>
<?php if(empty($this->nolist)){ ?>
<span class="comments_body" id="comments_body_<?php echo $comment->comment_id; ?>" style="display:block;">
<?php } ?>
   <?php echo $this->getCommentContent($comment->body) ?>
   <?php if($comment instanceof Activity_Model_Comment){ $module = 'activity';}else{ $module="core";} ?>
   <?php 
    $mentionUserData = array();
    preg_match_all('/(^|\s)(@\w+)/', $comment->body, $result);
    foreach($result[2] as $value){
        $user_id = str_replace('@_user_','',$value);
        $user = Engine_Api::_()->getItem('user',$user_id);
        if(!$user)
          continue;
        $mentionUserData[] = array(
          'type'  => 'user',
          'id'    => $user->getIdentity(),
          'name' => $user->getTitle(),
          'avatar' => $this->itemPhoto($user, 'thumb.icon'),
        );
    }   
    ?>
   <span id="data-mention" style="display:none;"><?php echo json_encode($mentionUserData,JSON_HEX_QUOT | JSON_HEX_TAG); ?></span>
   <span class="comments_body_actual" rel="<?php echo $module; ?>" style="display:none;"><?php echo $comment->body; ?></span>
     <?php 
     if($comment->file_id){ ?>
     <div class="sescmt_media_container sesbasic_clearfix">
     <?php
       $getFilesForComment = Engine_Api::_()->getDbTable('commentfiles','sesadvancedcomment')->getFiles(array('comment_id'=>$comment->comment_id));
       $counter = 0;
       foreach($getFilesForComment as $fileid){
         if($fileid->type == 'album_photo'){
         try{
          $photo = Engine_Api::_()->getItem('album_photo',$fileid->file_id);
          if($photo){
            $path = $photo->getPhotoUrl('thumb.normalmain');
          ?>
           <div class="comment_image sescmt_media_thumb sesbasic_clearfix" data-fileid="<?php echo $fileid->file_id; ?>" data-type="<?php echo $fileid->type; ?>">
            <img src="<?php echo $path; ?>" style="display:none;">
             <?php $moduleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum'); ?>
              <a <?php if($moduleEnable) { ?> class="ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl(); ?>','<?php echo Engine_Api::_()->sesalbum()->getImageViewerHref($photo) ?>')" <?php } ?> href="<?php echo $photo->getHref() ?>"><span style="background-image:url(<?php echo $path; ?>);"></span></a>
         </div>  
     		<?php 
          }else{?>
          	<div class="comment_image sescmt_media_thumb sesbasic_clearfix">
          		<a href="javascript:void(0)" style="cursor:default;" title="<?php echo $this->translate('Missing Image'); ?>"><span style="background-image:url(application/modules/Sesadvancedcomment/externals/images/blank-photo.png);"></span></a>
         		</div>
          <?php }
          }catch(Exception $e){ ?>
            <div class="comment_image sescmt_media_thumb sesbasic_clearfix">
              <a href="javascript:void(0)" style="cursor:default;" title="<?php echo $this->translate('Missing Image'); ?>"><span style="background-image:url(application/modules/Sesadvancedcomment/externals/images/blank-photo.png);"></span></a>
           </div>
          <?php }
        }else{
        try{
          $video = Engine_Api::_()->getItem('video',$fileid->file_id);
          if($video){
            $path = $video->getPhotoUrl('thumb.normalmain');
          ?>
           <div class="comment_image sescmt_media_thumb sesbasic_clearfix" data-fileid="<?php echo $fileid->file_id; ?>" data-type="<?php echo $fileid->type; ?>">
            <img src="<?php echo $path; ?>" style="display:none;">
              <a class="sesvideo_thumb_img"  href="<?php echo $video->getHref() ?>"><span style="background-image:url(<?php echo $path; ?>);"></span><i class="sescmt_play_btn fa fa-play"></i></a>
           </div>  
     <?php 
          } else { ?>
            <div class="comment_image sescmt_media_thumb sesbasic_clearfix">
              <a href="javascript:void(0)" style="cursor:default;" title="<?php echo $this->translate('Missing Image'); ?>"><span style="background-image:url(application/modules/Sesadvancedcomment/externals/images/blank-photo.png);"></span></a>
           </div>
        <?php }  
        }catch(Exception $e){ ?>
            <div class="comment_image sescmt_media_thumb sesbasic_clearfix">
              <a href="javascript:void(0)" style="cursor:default;" title="<?php echo $this->translate('Missing Image'); ?>"><span style="background-image:url(application/modules/Sesadvancedcomment/externals/images/blank-photo.png);"></span></a>
           </div>
        <?php }
        }
        $counter++;
     }
     ?>

     </div>
     <!--<?php if($counter > 2){ ?>
     	<a href="javascript:void(0);" class="sescmt_media_more"><i class="fa fa-plus"></i><span>See All</span></a>
     <?php } ?>-->

  <?php } 
?>
   <?php if($comment->emoji_id) { ?>
    <div class="emoji">
        <?php 
          $photo = Engine_Api::_()->getItem('sesadvancedcomment_emotionfile',$comment->emoji_id);
          if($photo){ 
            $path = $photo->getPhotoUrl();
          ?>
        <img src="<?php echo $path; ?>" />
        <?php } ?>
    </div>
    <?php } ?>
 <?php if($comment->preview && empty($comment->showpreview)){ ?>
    <div class="sescmt_link_item" id="commentpreview_<?php echo $comment->comment_id; ?>">
        <?php 
          $link = Engine_Api::_()->getItem('core_link',$comment->preview);
          if($link){ 
            $path = $link->getPhotoUrl();
          ?>
       <a href="<?php echo $link->getHref() ?>" target="_blank" class="sesbasic_clearfix">
       <div class="sescmt_link_item_img">
          <img src="<?php echo $path; ?>" />
        </div>
        <div class="sescmt_link_item_cont">
          <div class="sescmt_link_item_title"><?php echo $link->getTitle(); ?></div>
          <?php $parseUrl = parse_url($link->uri); ?>
          <div class="sescmt_link_item_source sesbasic_text_light"><?php echo str_replace(array('www.','demo.'),array('',''),$parseUrl['host']); ?></div>
       </div>
       </a>
        <?php } ?>
    </div>
    <?php } ?>
 <?php if(empty($this->nolist)){ ?>
 </span>
 <?php } ?> 