<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _activitycommentreply.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $commentreply = $this->commentreply; 
      $action = $this->action;
      $canComment =( $action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment')
             );
      $islanguageTranslate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.translate', 0);
     $languageTranslate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.language', 'en');
?>
<?php if(empty($this->likeOptions)){ ?>
<li id="comment-<?php echo $commentreply->comment_id; ?>">
  <div class="comments_author_photo">
  <?php echo $this->htmlLink($this->item($commentreply->poster_type, $commentreply->poster_id)->getHref(),
    $this->itemPhoto($this->item($commentreply->poster_type, $commentreply->poster_id), 'thumb.icon', $action->getSubject()->getTitle())
  ) ?>
  </div>
  <div class="comments_reply_info comments_info">
  	<div class="sesadvcmt_comments_options">
      <a href="javascript:void(0);" class="sesadvcmt_cmt_hideshow sesadvcmt_comments_options_icon" onclick="showhidecommentsreply('<?php echo $commentreply->comment_id ?>', '<?php echo $action->getIdentity(); ?>')"><i id="hideshow_<?php echo $commentreply->comment_id ?>_<?php echo $action->getIdentity(); ?>" class="fa fa-minus-square-o"></i></a>
       <?php if ( $this->viewer()->getIdentity() &&
                 (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                  ($this->viewer()->getIdentity() == $commentreply->poster_id) ||
                  Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $this->viewer()->level_id, 'activity')  ) ): ?>
      <div class="sesadvcmt_pulldown_wrapper sesact_pulldown_wrapper">
        <a href="javascript:void(0);" class="sesadvcmt_comments_options_icon"><i class="fa fa-angle-down"></i></a>
        <div class="sesadvcmt_pulldown">
          <div class="sesadvcmt_pulldown_cont">
            <ul>
              <li>
               <?php echo $this->htmlLink(array(
                    'route'=>'default',
                    'module'    => 'sesadvancedactivity',
                    'controller'=> 'index',
                    'action'    => 'delete',
                    'action_id' => $action->action_id,
                    'comment_id'=> $commentreply->comment_id,
                    ), $this->translate('Delete'), array('class' => 'sescommentsmoothbox')) ?>
              </li>
              <?php if(!$comment->emoji_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.editenable', 1)){ ?>
              <li><?php echo $this->htmlLink(('javascript:;'), $this->translate('Edit'), array('class' => 'sesadvancedcomment_reply_edit')) ?></li>
              <?php } ?>
              <?php $reportEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.reportenable', 1); ?>
              <?php if($reportEnable) { ?>
                <li>
                  <?php echo $this->htmlLink(Array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $commentreply->getGuid()), '<span>'. $this->translate("Report") . '</span>', array('onclick' => "openSmoothBoxInUrl(this.href);return false;" ,"class" => "")); ?>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
   	<?php endif; ?>
   </div> 
  
   <span class='comments_reply_author comments_author ses_tooltip' data-src="<?php echo $this->item($commentreply->poster_type, $commentreply->poster_id)->getGuid(); ?>">
     <?php echo $this->htmlLink($this->item($commentreply->poster_type, $commentreply->poster_id)->getHref(), $this->item($commentreply->poster_type, $commentreply->poster_id)->getTitle()); ?>
   </span>
    <?php if(strip_tags($commentreply->body) && $islanguageTranslate){ ?>
      <a href="javascript:void(0);" class="comments_translate_link floatR" onClick="socialSharingPopUp('https://translate.google.com/#auto/<?php echo $languageTranslate; ?>/<?php echo urlencode(strip_tags($commentreply->body)); ?>','Google');return false;"><?php echo $this->translate("Translate"); ?></a>
    <?php } ?>
    <?php
        echo $this->partial(
          '_activitycommentreplycontent.tpl',
          'sesadvancedcomment',
          array('commentreply'=>$commentreply)
        );    
?>    
 <?php } ?>
   <ul class="comments_reply_date comments_date" id="comments_reply_<?php echo $commentreply->comment_id; ?>_<?php echo $action->getIdentity(); ?>" style="display:block;">
     
      <?php if( $canComment ):
        $isLiked = $commentreply->likes()->isLike($this->viewer());
      ?> 
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.enablenestedcomments', 1)): ?>
          <li class="comments_reply_btn">
          <?php echo $this->htmlLink('javascript:;', $this->translate('SESADVREPLY'), array('class' => 'sesadvancedcommentreplyreply')) ?>
          </li>
          <li class="sep">&middot;</li>
        <?php endif; ?>
        <li class="comments_reply_like">
          <?php if( !$isLiked ): ?>
            <a href="javascript:void(0)" onclick="sesadvancedcommentlike(<?php echo sprintf("'%d', %d, %s, %d", $action->getIdentity(), $commentreply->getIdentity(),'this',$this->page) ?>)">
              <?php echo $this->translate('SESADVLIKE') ?>
            </a>
          <?php else: ?>
            <a href="javascript:void(0)" onclick="sesadvancedcommentunlike(<?php echo sprintf("'%d', %d, %s, %d", $action->getIdentity(), $commentreply->getIdentity(),'this',$this->page) ?>)">
              <?php echo $this->translate('SESADVUNLIKE') ?>
            </a>
          <?php endif ?>
        </li>                        
      <?php endif ?>                      
      <?php if( $commentreply->likes()->getLikeCount() > 0 ): ?>
        <li class="sep">&middot;</li>
        <li class="comments_reply_likes_total">
          <a href="javascript:;" id="comments_comment_likes_<?php echo $commentreply->comment_id ?>" class="sessmoothbox" data-url="<?php echo $this->url(array('module' => 'sesadvancedactivity', 'controller' => 'ajax', 'action' => 'comment-likes', 'comment_id' => $commentreply->getIdentity(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', true); ?>">
            <i style="background-image:url(<?php echo Engine_Api::_()->sesadvancedcomment()->likeImage(1); ?>)"></i>
            <?php echo $commentreply->likes()->getLikeCount(); ?>
          </a>
        </li>
      <?php endif ?>
      <?php if ( $this->viewer()->getIdentity() &&
               (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                ($this->viewer()->getIdentity() == $commentreply->poster_id) ||
                Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $this->viewer()->level_id, 'activity')  ) ): ?>
      <?php if($commentreply->preview && empty($commentreply->showpreview)) { ?>
        <li id="remove_previewli_<?php echo $commentreply->comment_id ?>" class="sep">&middot;</li>
        <li id="remove_preview_<?php echo $commentreply->comment_id ?>">
          <a  href="javascript:void(0);" onclick="removePreview('<?php echo $commentreply->comment_id; ?>', '<?php echo $commentreply->getType(); ?>')">
            <?php echo $this->translate("Remove Preview"); ?>
          </a>
        </li>
      <?php } endif; ?>
      <?php if( $canComment ){ ?>
      	<li class="sep">&middot;</li>
     <?php } ?>
       <li class="comments_reply_timestamp">
         <?php echo $this->timestamp($commentreply->creation_date); ?>
       </li>       
    </ul>
 <?php if(empty($this->likeOptions)){ ?>
  </div>
</li>
<?php } ?>
