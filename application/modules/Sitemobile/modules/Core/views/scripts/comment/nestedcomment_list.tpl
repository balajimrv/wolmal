<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
<?php
$this->headTranslate(array(
    'Are you sure you want to delete this?',
));
?>
<?php 
    include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
?>
<?php if (!$this->page): ?>
  <div class='comments' id="comments">
  <?php endif; ?>
  <div class='comments_options feed_item_btm'>   
    <?php if (isset($this->form)): ?>
      <div class="fleft" <?php if($showLikeWithoutIcon == 3 && !$showAsLike):?> style="margin-top:5px;"<?php endif;?>><a href='javascript:void(0);' onclick="$.mobile.activePage.find('#comment-form_'+'<?php echo $this->subject()->getGuid();?>').css('display', ''); $.mobile.activePage.find('#comment-form_'+'<?php echo $this->subject()->getGuid();?>').find('#body').focus();"><?php echo $this->translate('Post Comment') ?></a><span class="sep">-</span>
      </div>
    <?php endif; ?>

    <?php if($showAsLike):?>  
        <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
          <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>
            <div class="fleft"><a href="javascript:void(0);" onclick="sm4.core.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes"><?php echo $this->translate('Unlike This') ?></a></div>
          <?php else: ?>
             <div class="fleft"><a href="javascript:void(0);" onclick="sm4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes"><?php echo $this->translate('Like This') ?></a></div>
          <?php endif; ?>
        <?php endif; ?>
    <?php else:?>
            
        <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
            
            <?php if($showLikeWithoutIcon != 3):?>
                <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?>
                    <a href="javascript:void(0);" onclick="sm4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes"><?php echo $this->translate('Like') ?></a>
                <?php else:?>
                     
                    <a href="javascript:void(0)" class="feed_likes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Like') ?></a>
                <?php endif;?>
                <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($this->subject(), $this->viewer());?>
                <?php if (!$isDisLiked): ?>
                    -
                    <a href="javascript:void(0);" onclick="sm4.core.comments.dislike(<?php echo sprintf("'%s', %d", $this->subject()->getType(), $this->subject()->getIdentity());?>)" class="feed_dislikes"><?php echo $this->translate('Dislike') ?></a>
                <?php else:?>
                    -
                    <a href="javascript:void(0)" class="feed_dislikes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Dislike') ?></a>
                <?php endif;?>
            <?php else:?>    
                <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?>
                     <div class="comments_likes fleft">
                        <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
                            <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_likes_count f_normal"><?php echo $this->likes->getTotalItemCount();?></a>
                        <?php endif; ?>
                        <a href="javascript:void(0);" onclick="sm4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes ui-icon ui-icon-angle-up"></a><span class="sep">- &nbsp;</span>
                    </div>
                <?php else:?>
                     <div class="comments_likes fleft">
                        <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
                            <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_likes_count f_normal"><?php echo $this->likes->getTotalItemCount();?></a>
                        <?php endif; ?>
                        <a href="javascript:void(0)" class="feed_likes ui-icon ui-icon-angle-up" onclick="sm4.activity.changeLikeDislikeColor()"></a>
                     <span class="sep">- &nbsp;</span>
                     </div>
                <?php endif;?>
                <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($this->subject(), $this->viewer());?>
                <?php if (!$isDisLiked): ?>
                  <div class="comments_dislikes fleft">
                    <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject()) > 0): // LIKES ------------- ?>
                       <?php if($showDislikeUsers):?>
                        <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_dislikes_count"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject());?>
                        </a>
                      <?php else:?>
                      <a class="comments_dislikes_count"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject());?></a>
                      <?php endif;?>
                    <?php endif; ?>
                    <a href="javascript:void(0);" onclick="sm4.core.comments.dislike(<?php echo sprintf("'%s', %d", $this->subject()->getType(), $this->subject()->getIdentity());?>)" class="feed_dislikes ui-icon ui-icon-angle-down"></a></div>
                <?php else:?>
                    <div class="comments_dislikes fleft">
                        <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject()) > 0): // LIKES ------------- ?>
                        <?php if($showDislikeUsers):?>
                            <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_dislikes_count"><?php  echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject());?></a> 
                        <?php else:?>
                            <a class="comments_dislikes_count"><?php  echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject());?></a>
                        <?php endif; ?>
                       <?php endif; ?>
                       <a href="javascript:void(0)" class="feed_dislikes ui-icon ui-icon-angle-down" onclick="sm4.activity.changeLikeDislikeColor()"></a></div>
                <?php endif;?>
            <?php endif;?>     
         <?php endif;?> 
    <?php endif;?>
  </div>
  <ul class="clr">
        <li>
            <div></div>
            <?php if($showAsLike):?>
                <div class="comments_likes feed_item_btm">
                   <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
                       <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_likes_count f_normal"><?php echo $this->translate(array('%s likes', '%s like', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount()))?></a><span class="sep">-</span>
                   <?php endif; ?>
                   <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>
                </div>
            <?php else:?>
            <div class="feed_item_btm">
                <?php if($showLikeWithoutIcon != 3):?>
                <div class="comments_likes fleft">
                   <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
                       <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_likes_count f_normal"><?php echo $this->translate(array('%s likes', '%s like', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount()))?></a><span class="sep">-</span>
                   <?php endif; ?>
                </div>
                <div class="comments_dislikes fleft">
                   <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject()) > 0): // LIKES ------------- ?>
                       <?php if($showDislikeUsers):?>
                        <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_dislikes_count"><?php echo $this->translate(array('%s dislikes', '%s dislike', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject())), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject()))) ?></a>
                       <?php else:?>
                        <a class="comments_dislikes_count"><?php echo $this->translate(array('%s dislikes', '%s dislike', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject())), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $this->subject()))) ?></a>
                       <?php endif;?>
                       <span class="sep">-</span>
                   <?php endif; ?>
                </div>
                <?php endif; ?>
                <div><span id="comments_count"><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span></div>
                </div>
            <?php endif;?>
        </li>
       
      <?php if ($this->comments->getTotalItemCount() > 0): // COMMENTS ------- ?>

        <?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
                'onclick' => 'sm4.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page - 1) . '")'
            ))
            ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if (!$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
                'onclick' => 'sm4.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->comments->getCurrentPageNumber()) . '")'
            ))
            ?>
          </div>
        </li>
      <?php endif; ?>

      <?php
      // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if ($this->page):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for (; $i != $e; $i += $d):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
        ?>
        
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div id="comment_information-<?php echo $comment->comment_id ?>" style="display:block">
               <?php if($canDelete):?>
                <div class="feed_items_options_btn">      
                     <a href="javascript:void(0);" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideDown(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideUp(500);" data-role="button" data-icon="carat-d" data-iconpos="notext" data-theme="c" data-inline="true" class="ui-link ui-btn ui-btn-c ui-icon-carat-d ui-btn-icon-notext ui-btn-inline ui-shadow ui-corner-all" role="button"></a>
                </div>  
              <?php endif;?>
          <div class="comments_author_photo">
            <?php
            echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
            )
            ?>
          </div>
          <div class="comments_info">
            <div class='comments_author'>
              <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
            </div>
            <div class="comments_body">
                <?php $item = $comment;?>
                <?php 
                    include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
                ?>      
            </div>
            <?php if (!empty($item->attachment_type) && null !== ($attachment = $this->item($item->attachment_type, $item->attachment_id))): ?>
                <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
                  <div class="seaocore_comments_attachment_photo">
                    <?php if (null !== $attachment->getPhotoUrl()): ?>
                     <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('data-linktype'=> 'photo-gallery')) ?>
                     <?php endif; ?>
                  </div>
                </div>
            <?php endif; ?>
            <div class="comments_date">
               <?php echo $this->timestamp($comment->creation_date); ?> 
               <?php if($this->viewer()->getIdentity() || $comment->likes()->getLikeCount() > 0 || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0) :?>
                 <span class="sep">-</span>
               <?php endif;?>
               <?php if($showAsNested):?>   
                  <?php if (isset($this->form)): ?>
                    <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view','comment_id' => $comment->comment_id, 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>" , "replypopup_<?php echo $comment->comment_id;?>", <?php echo $comment->getIdentity();?>)'>
                        <span><?php echo $this->translate('Reply'); ?></span>
                    </a>   
                    -
                <?php endif;?> 
              <?php endif;?> 
              
              <?php if($showAsLike):?>    
                <?php
                if ($this->canComment):
                  $isLiked = $comment->likes()->isLike($this->viewer());
                  ?>
                  <?php if (!$isLiked): ?>
                    <a href="javascript:void(0)" onclick="sm4.core.comments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes">
                      <?php echo $this->translate('Like') ?>
                    </a>
                    
                  <?php else: ?>
                    <a href="javascript:void(0)" onclick="sm4.core.comments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes">
                      <?php echo $this->translate('Unlike') ?>
                    </a>
                  <?php endif ?>
                <?php endif ?>
                                                        
                <?php if ($comment->likes()->getLikeCount() > 0): ?><span class="sep"> -</span>
                    <a id="comments_comment_likes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_comment_likes"><?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?></a>
                <?php endif ?>
                
                <?php else:?>
                 
                <?php if($canComment):?>
                          <?php $isLiked = $comment->likes()->isLike($this->viewer());?>
                          <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if(!$isLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.core.comments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
                            <?php else:?>
                               <a href="javascript:void(0)" class="comment_likes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Like') ?></a> 
                               <span class="sep">-</span> 
                            <?php endif;?>
                          <?php else:?>
                            <?php if ($comment->likes()->getLikeCount() > 0): ?>
                               <?php if($showLikeWithoutIconInReplies == 3):?>
                                 <a id="comments_comment_likes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_comment_likes"><span><?php echo $comment->likes()->getLikeCount(); ?></span></a>
                               
                                <?php endif;?> 
                            <?php endif ?>
                      
                            <?php if(!$isLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.core.comments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes ui-icon ui-icon-angle-up"></a> <span class="sep">-</span> 
                            <?php else:?>
                               <a href="javascript:void(0)" class="comment_likes ui-icon ui-icon-angle-up" onclick="sm4.activity.changeLikeDislikeColor()"></a> 
                               <span class="sep">-</span> 
                            <?php endif;?>
                          <?php endif;?>
                       <?php endif;?>
                
                        <?php if($canComment):?>
                         
                          <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($comment, $this->viewer());?>
                          
                          <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if(!$isDisLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.core.comments.dislike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes"> <?php echo $this->translate('Dislike') ?></a> <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                              <span class="sep">-</span> 
                              <?php endif;?>
                            <?php else:?>
                              <a href="javascript:void(0)" class="comment_dislikes" onclick="sm4.activity.changeLikeDislikeColor()"> <?php echo $this->translate('Dislike') ?> </a>
                                  <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?>
                            <?php endif;?>
                          <?php else:?>
                          
                             <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                <?php if($showLikeWithoutIconInReplies == 3):?>
                                  <?php if($showDislikeUsers):?>
                                    <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_comment_dislikes"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></a>
                                    <?php else:?>
                                    <a class="comments_comment_dislikes" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></a>
                                    <?php endif;?>
                                <?php endif;?>
                             <?php endif ?>
                        
                             <?php if(!$isDisLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.core.comments.dislike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes ui-icon ui-icon-angle-down"></a> 
                            <?php else:?>
                              <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="comment_dislikes ui-icon ui-icon-angle-down"></a>
                                  
                            <?php endif;?>
                          <?php endif;?>
                       <?php endif;?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                        <?php if($showLikeWithoutIconInReplies != 3):?>
                             <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_comment_likes"><span class="ui-icon ui-icon-thumbs-up-alt"><?php echo $comment->likes()->getLikeCount(); ?></span></a>
                            <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                <span class="sep">-</span> 
                            <?php endif;?>
                        <?php endif;?> 
                      <?php endif; ?>
                      
                      <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                            <?php if($showLikeWithoutIconInReplies != 3):?>
                                <?php if($showDislikeUsers):?>
                                    <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_comment_dislikes"><span class="ui-icon ui-icon-thumbs-down-alt"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span></a>
                                <?php else:?>
                                    <a class="comments_comment_dislikes" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>"><span class="ui-icon ui-icon-thumbs-down-alt"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span></a>
                                <?php endif;?>
                            <?php endif;?>
                        <?php endif; ?>
                      <?php endif; ?>
            </div>
              <?php if($showAsNested):?>
            <?php $replyCount = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->getReplyCount($this->subject(), $comment->getIdentity());?>  
            <?php if($replyCount):?>  
                <div class="feed_item_option" id="reply_list_<?php echo $comment->getIdentity();?>">
                    <div role="navigation" class="ui-navbar" data-role="navbar" data-inset="false">
                    <ul class="ui-grid-b">	
                        <li id="reply_link" style="margin:5px 0 0;">
                            <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view', 'comment_id' => $comment->getIdentity(), 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "replypopup_<?php echo $comment->comment_id;?>", <?php echo $comment->getIdentity();?>)' class="feed_replies">
                                <span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></span>
                            </a>
                        </li>
                    </ul>
                  </div>
                </div>
            <?php else:?>
                <div class="feed_item_option" id="reply_list_<?php echo $comment->getIdentity();?>" style="display:none;">
                    <div role="navigation" class="ui-navbar" data-role="navbar" data-inset="false">
                        <ul class="ui-grid-b">	
                            <li id="reply_link"  style="margin:5px 0 0;">
                                <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view', 'comment_id' => $comment->getIdentity(), 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "replypopup", <?php echo $comment->getIdentity();?>)' class="feed_replies"><span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                 </div>
            <?php endif;?>  
              <?php endif;?>
          </div>
            </div>
            <?php if ($canDelete): ?> 
               <div id="comment-option-<?php echo $comment->comment_id ?>" class="feed_item_option_box" style="display: none;">
                  <div class="feed_overlay"></div>
                  <a class="ui-btn-default ui-link" href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'edit','comment_id' => $comment->comment_id, 'perform' => 'comment-edit', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>" , "editpopup_<?php echo $comment->getIdentity()?>", <?php echo $comment->getIdentity();?>)'>
                    <span><?php echo $this->translate('Edit'); ?></span>
                  </a>   
                  <a class="ui-btn-default ui-link" href="javascript:void(0);" onclick="sm4.core.comments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                    <?php echo $this->translate('Delete') ?>
                  </a>
                  <a href="#" class="ui-btn-default ui-link" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideUp(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideDown(500);"><?php echo $this->translate('Cancel'); ?></a>
                </div>
            <?php endif; ?>
        </li>
      <?php endfor; ?>
      <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
                'onclick' => 'sm4.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page + 1) . '")'
            ))
            ?>
          </div>
        </li>
      <?php endif; ?>
    <?php endif; ?>
  </ul>
  <?php if (isset($this->form)): ?>
      <div style="display:none;">  
        <script type="text/javascript">
          sm4.core.runonce.add(function(){
            //sm4.core.comments.attachCreateComment($.mobile.activePage.find('#comment-form'));
            $.mobile.activePage.find('#comment-form_'+'<?php echo $this->subject()->getGuid();?>').find('#body').autoGrow(); 
            $.mobile.activePage.find('#comment-form_'+'<?php echo $this->subject()->getGuid();?>').find('textarea').attr('placeholder', sm4.core.language.translate('Write a comment...'))
           // $.mobile.activePage.find('#comment-form_'+'<?php echo $this->subject()->getGuid();?>').find('textarea').focus();
          });
        </script>
      </div>
    <div class="sm-comments-post-comment-form" style="margin: 0 -5px -10px;">
        
            <table cellspacing="0" cellpadding="0">
              <tr>    
                <td class="sm-cmf-left">
                  <form id="comment-form_<?php echo $this->subject()->getGuid();?>" enctype="application/x-www-form-urlencoded" style='display:block;' action="" method="post" data-ajax="false">
                    <?php
                    foreach ($this->form->getElements() as $key => $value):
                      if ($key != "submit") : echo $this->form->$key;
                      endif;
                    endforeach;
                    ?>
                  </form>
                </td>
                <td>
                    <button class="ui-btn-default ui-btn-c" data-role="none" type="submit" id="submit" name="submit" onclick="sm4.core.comments.attachCreateComment($('#comment-form_'+'<?php echo $this->subject()->getGuid();?>')); return false;" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
                </td>
              </tr>
            </table>
            
            <?php if($photoEnabled || $smiliesEnabled || $taggingEnabled):?>  
            <div class="cont-sep t_l b_medium"></div>
            <div id="activitypost-container-temp" action-id='comment-form_<?php echo $this->subject()->getGuid();?>'>
            	<div class="compose_buttons">
                <?php if($photoEnabled):?>
                    <div id="composer-nested-comment-options" class="fleft">
                      <!--<div id="smactivityoptions-popup" class="sm-post-composer-options">-->
                          <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
                              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showCommentPluginForm(this, 'photo', '#body');" class="ui-link-inherit">
                                <i class="cm-icons cm-icon-photo"></i>
                              </a> 
                          <?php endif; ?>
                        
                      <!--</div>-->     
                    </div>
                <?php endif; ?>
                <?php if($smiliesEnabled):?>
                    <?php $enableSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
                        $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                        if (in_array("withtags", $enableSettings)):
                          ?>
                          <a href="javascript:void(0);" data-role="none" id="emoticons-button"  class="emoticons-button"  onclick="setEmoticonsBoard();
                              sm4.activity.statusbox.toggleEmotions($(this));" >
                            <i class="cm-icons cm-icon-emoticons"></i>
                          </a>
                     <?php endif; ?>
                <?php endif; ?>
                <?php if($taggingEnabled):?>
                <a id="comment-add-people" href="javascript:void(0);" data-role="none" onclick="          sm4.activity.composer.showCommentPluginForm(this, 'addpeople', '#body');">
                  <i class="cm-icons cm-icon-user"></i>
                </a>
                <?php endif; ?>
              </div>
              <div id="comment_options_box">
                <?php if($smiliesEnabled):?>
                    <div id="emoticons-board" class="compose_embox_cont ui-page-content <?php if(Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer';?>" style="display:none;">
                        <div class="sm-seaocore-embox">
                          <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
                          <?php $comment_box_id = "#body"; ?>
                          <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                            <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIconNestedComment("<?php echo $this->string()->escapeJavascript($tag_key) ?>", "<?php echo $comment_box_id;?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
                              echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                              ?></span>
                          <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($taggingEnabled):?>
                  <div id="adv_comment_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                      <div id="aff_comment_mobile_aft_search-element">
                        <div class="sm-post-search-fields">
                          <table width="100%">
                            <tr>
                              <td class="sm-post-search-fields-left">
                                <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_comment_mobile_aft_search" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                              </td>
                            </tr>
                          </table>			
                          <span role="status" aria-live="polite"></span>
                        </div>
                        <div id="toCommentValues-temp-wrapper" style="border:none;display:none;">
                          <div id="toCommentValues-temp-element">
                            <input type="hidden" id="toCommentValues-temp" value=""  name="toValues-temp" />
                            <input type="hidden" id="toCommentValues" value=""  name="toValues" />
                          </div>
                        </div>
                      </div>
                  </div>
                  <div id="toCommentValuesdone-wrapper" style="display:none;"></div>
                  <div class="ui-header o_hidden" id="ui-post-comment-header-addpeople" style="display:none;">
                      <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_comment_mobile_aft_search').val('');
                              sm4.activity.composer.addpeople.addCommentFriends('#comment-form_'+'<?php echo $this->subject()->getGuid();?>');
                              return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                      <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
                      <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_comment_mobile_aft_search').val(''); sm4.activity.toggleCommentPostArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
                      <?php else: ?>
                       <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_comment_mobile_aft_search').val(''); sm4.activity.toggleCommentPostArea(this, false, 'addpeople');" ></a>
                       <?php endif; ?>
                  </div>
                <?php endif; ?>
               </div>
            </div> 
            <?php endif; ?>
        
      </div>	
    <?php //echo $this->form->setAttribs(array('id' => 'comment-form', 'style' => 'display:none;'))->render();
  endif; ?>
  <?php if (!$this->page): ?>
  </div>
<?php endif; ?>
<?php else:

include_once(APPLICATION_PATH ."/application/modules/Sitemobile/modules/Core/views/scripts/photo-comment/list.tpl");
endif; ?>
