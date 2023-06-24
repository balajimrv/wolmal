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
        include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
    ?>
    <?php $replyCount = $this->comments->getTotalItemCount(); ?>

<?php if (!$this->page): ?>
  <div id="replies">
<?php endif; ?>
    <div class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
        <div class="">
            <div class="sm-ui-popup-top ui-header ui-bar-a">
                <a href="javascript:void(0);" data-role="button" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-btn-right ps-close-popup"></a>
                <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate(array('%s reply', '%s replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></h2>
            </div>
            <div class="sm-ui-popup-container" style="bottom:95px;">
                <div class="comments">
                    <ul class="viewcomment">
                        <?php if ($replyCount): ?>
                            
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
            <div class="comments_author_photo">
              <?php
              echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
              )
              ?>
            </div>
            <div class="comments_info">
              <div class='comments_author'>
                <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
              </div><?php $item = $comment;?>
              <div class="comments_body">
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
                <?php if ($canDelete): ?>
                  <a href="javascript:void(0);" onclick="sm4.core.comments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                    <?php echo $this->translate('Delete') ?>
                  </a>
                  <span class="sep"> -</span>
                <?php endif; ?>
              	<?php if($showAsLike):?>    
                  <?php
                  if ($this->canComment):
                    $isLiked = $comment->likes()->isLike($this->viewer());
                    ?>
                    <?php if (!$isLiked): ?>
                      <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes">
                        <?php echo $this->translate('Like') ?>
         							</a>
                      <span class="sep"> -</span>
                    <?php else: ?>
                      <a href="javascript:void(0)" onclick="sm4.core.comments.unlikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes">
                        <?php echo $this->translate('Unlike') ?>
                      </a>
                      <span class="sep"> -</span>
                    <?php endif ?>
                  <?php endif ?>
                                                          
                  <?php if ($comment->likes()->getLikeCount() > 0): ?>
                    <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" onclick="sm4.core.comments.comment_likes(<?php echo sprintf("'%d'", $comment->comment_id) ?>)" class="comments_comment_likes">
                             <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                    </a>
                    <span class="sep"> -</span>
                  <?php endif ?>
                <?php else:?>
                  <?php if($canComment):?>
                    <?php $isLiked = $comment->likes()->isLike($this->viewer());?>
                    <?php if($showLikeWithoutIconInReplies != 3):?>
                      <?php if(!$isLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
                      <?php else:?>
                         <a href="javascript:void(0)" class="comment_likes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Like') ?></a> 
                         <span class="sep">-</span> 
                      <?php endif;?>
                    <?php else:?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                          <?php if($showLikeWithoutIconInReplies == 3):?>
                              <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="sm4.core.comments.comment_likes(<?php echo sprintf("'%d'", $comment->comment_id) ?>)">
                                <?php echo $this->locale()->toNumber($comment->likes()->getLikeCount()); ?>
                              </a>
                          <?php endif;?> 
                      <?php endif ?>
                
                      <?php if(!$isLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes ui-icon ui-icon-angle-up"></a> <span class="sep">-</span> 
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
                      <a href="javascript:void(0)" onclick="sm4.core.comments.dislikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes"> <?php echo $this->translate('dislike') ?></a> 
                      <span class="sep">-</span> 
                    <?php else:?>
                      <a href="javascript:void(0)" class="comment_dislikes" onclick="sm4.activity.changeLikeDislikeColor()"> <?php echo $this->translate('dislike') ?> </a>
                      <span class="sep">-</span> 
                    <?php endif;?>
                  <?php else:?>
                     <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                        <?php if($showLikeWithoutIconInReplies == 3):?>
                            <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" class="comments_comment_dislikes" <?php if($showDislikeUsers):?>  onclick="sm4.core.comments.comment_dislikes('<?php echo $comment->getIdentity(); ?>')" <?php endif;?>>
                              <?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?>
                            </a>
                        <?php endif;?>
                     <?php endif; ?>
                
                     <?php if(!$isDisLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.dislikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes ui-icon ui-icon-angle-down"></a> 
                        <span class="sep">-</span> 
                     <?php else:?>
                        <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="comment_dislikes ui-icon ui-icon-angle-down"></a>
                        <span class="sep">-</span> 
                     <?php endif;?>
                  <?php endif;?>
               	
               	<?php endif;?>
                
                <?php if ($comment->likes()->getLikeCount() > 0): ?>
                  <?php if($showLikeWithoutIconInReplies != 3):?>
                      <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="sm4.activity.comment_likes('<?php echo $comment->getIdentity(); ?>')">
                        <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                      </a> 
                      <span class="sep">-</span> 
                  <?php endif;?>   
                <?php endif; ?>
              
                <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                    <?php if($showLikeWithoutIconInReplies != 3):?>
                        <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" class="comments_comment_dislikes" <?php if($showDislikeUsers):?>onclick="sm4.core.comments.comment_dislikes('<?php echo $comment->getIdentity(); ?>')" <?php endif;?>>
                          <?php echo $this->translate(array('%s dislikes this', '%s dislike this', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ))) ?>
                        </a> 
                        <span class="sep">-</span> 
                    <?php endif;?>
                <?php endif; ?>
                
              <?php endif; ?>
                <?php echo $this->timestamp($comment->creation_date); ?>
              </div>
            </div>
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
                        <?php else: ?>
                            <li>
                                <div class="no-comments">
                                    <i class="ui-icon ui-icon-comment-alt"></i>
                                    <span><?php echo $this->translate('No Replies') ?></span>
                                </div>	
                            </li>
                        <?php endif; ?>
                    </ul>
               
            </div>
        </div>
        <?php if (isset($this->form)): ?>  
            <div class="sm-comments-post-comment-form">
                    <table cellspacing="0" cellpadding="0">
                        <tr>    
                            <td class="sm-cmf-left">
                                <form id="reply-form" enctype="application/x-www-form-urlencoded" action="" method="post" data-ajax="false">
                                    <?php
                                    foreach ($this->form->getElements() as $key => $value):
                                        if ($key != "submit") : echo $this->form->$key;
                                        endif;
                                    endforeach;
                                    ?>
                                
                                </form>
                            </td>
                            <td>
                                <button class="ui-btn-default ui-btn-c" data-role="none" type="submit" id="submit" name="submit" onclick="sm4.core.comments.attachCreateReply($('#reply-form'));" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
                            </td>
                        </tr>
                    </table>
                    <?php if ($photoEnabled || $smiliesEnabled || $taggingEnabled): ?>  
                        <div class="cont-sep t_l b_medium"></div>
                        <div id="activitypost-container-temp" action-id='comment-form'>
                            <div class="compose_buttons">
                                <?php if ($photoEnabled): ?>
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
                                <?php if ($smiliesEnabled): ?>
                                    <?php
                                    $enableSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
                                    $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                                    if (in_array("withtags", $enableSettings)):
                                        ?>
                                        <a href="javascript:void(0);" data-role="none" id="emoticons-button"  class="emoticons-button"  onclick="setEmoticonsBoard();
                                                  sm4.activity.statusbox.toggleEmotions($(this));" >
                                            <i class="cm-icons cm-icon-emoticons"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
            <?php if ($taggingEnabled): ?>
                                    <a id="comment-add-people" href="javascript:void(0);" data-role="none" onclick="          sm4.activity.composer.showCommentPluginForm(this, 'addpeople', '#body');">
                                        <i class="cm-icons cm-icon-user"></i>
                                    </a>
            <?php endif; ?>
                            </div>
                            <div id="comment_options_box">
            <?php if ($smiliesEnabled): ?>
                                    <div id="emoticons-board" class="compose_embox_cont ui-page-content <?php if (Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer'; ?>" style="display:none;">
                                        <div class="sm-seaocore-embox">
                                            <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
                                            <?php $comment_box_id = "#body"; ?>
                                                <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                                                <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIconNestedComment("<?php echo $this->string()->escapeJavascript($tag_key) ?>", "<?php echo $comment_box_id; ?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
                                                    echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                                                    ?></span>
                <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

            <?php if ($taggingEnabled): ?>
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
                                          sm4.activity.composer.addpeople.addCommentFriends('#activity-comment-form-<?php echo $action->action_id ?>');
                                          return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                                        <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
                                            <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_comment_mobile_aft_search').val('');
                                              sm4.activity.toggleCommentPostArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
                                        <?php else: ?>
                                            <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_comment_mobile_aft_search').val('');
                                               sm4.activity.toggleCommentPostArea(this, false, 'addpeople');" ></a>
                                    <?php endif; ?>
                                    </div>
            <?php endif; ?>
                            </div>
                        </div> 
        <?php endif; ?>
                
            </div>	
    <?php endif; ?>
    </div>
<?php if (!$this->page): ?>
  </div>
<?php endif; ?>        
<?php else: ?>


<?php endif; ?>