<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewcomment.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headTranslate(array(
    'Write a comment...',
    'Unlike',
    '% likes this',
    'like',
    '% like',
    'Like'
));
?>

<?php
//GET VERSION OF SITEMOBILE APP.
$RemoveClassDone = true;
if (Engine_Api::_()->sitemobile()->isApp()) {
    if(Engine_Api::_()->sitemobile()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobileapp')->version, '4.8.6') )
        $RemoveClassDone = false;
}
?>

<?php
$action = $this->action;

$canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->commentForm) );
?>

<?php if (!Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')): ?>
    <div id='comment-activity-item-<?php echo $action->action_id ?>' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper" style="<?php echo $this->translate(($action->commentable) ? 'display:block' : 'display:block;') ?>">

        <?php if ($action->getTypeInfo()->commentable): // Comments - likes   ?>

            <div class="" id="showhide-comments-<?php echo $action->action_id ?>" style="display:block">
                <div class="sm-ui-popup-top ui-header ui-bar-a">
                    <a href="javascript:void(0);" data-role="button" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-btn-right ps-close-popup"></a>
                    <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); ?></h2>
                </div>
                <div class="sm-ui-popup-container">
                    <div class="comments">
                        <ul class="viewcomment">
                            <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
                                <li class="comments_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');
                                        $('#like-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');">
                                    <a href="javascript:void(0);"  >
                                        <i class="ui-icon ui-icon-thumbs-up-alt"></i>
                                        <?php echo $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())) ?>                            
                                    </a>

                                    <a href="javascript:void(0);"  class="comment_likes ui-link-inherit fright">												
                                        <i class="ui-icon icon-right fright ui-icon-arrow-r"></i>
                                    </a>

                                </li>	
                            <?php endif; ?>

                            <?php if ($action->comments()->getCommentCount() > 5 || $this->viewAllComments): ?>
                                <li class="comments_likes" onclick="sm4.activity.getOlderComments(this, '<?php echo $action->getObject()->getType() ?>', '<?php echo $action->getObject()->getIdentity() ?>', '2', '<?php echo $action->action_id; ?>');">
                                    <a href="javascript:void(0);" ><?php echo $this->translate('Load Previous Comments') ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if ($action->comments()->getCommentCount() > 0): ?>
                                <?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
                                    <li id="comment-<?php echo $comment->comment_id ?>">
                                        <div class="comments_author_photo">
                                            <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())) ?>
                                        </div>
                                        <div class="comments_info">
                                            <div class='comments_author'>
                                                <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
                                            </div>
                                            <div class="comments_body">
                                                <?php
                                                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
                                                ?>
                                            </div>
                                            <div class="comments_date">
                                                <?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
                                                    <a href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id, 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.activityremove(this);" data-message="<?php echo $comment->comment_id ?>-<?php echo $action->action_id ?>"><?php echo $this->translate('Delete'); ?></a>
                                                    -
                                                <?php endif; ?>
                                                <?php
                                                if ($canComment):
                                                    $isLiked = $comment->likes()->isLike($this->viewer());
                                                    ?>
                                                    <?php if (!$isLiked): ?>
                                                        <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
                                                    <?php else: ?>
                                                        <a href="javascript:void(0)" onclick="sm4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"><?php echo $this->translate('Unlike') ?>
                                                        </a> <span class="sep">-</span> 
                                                    <?php endif ?>
                                                <?php endif ?>
                                                <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                                    <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');
                                                            $('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');
                                                            sm4.activity.comment_likes('<?php echo $action->action_id ?>', '<?php echo $comment->getIdentity(); ?>', 1)">
                                                    <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                                                    </a> <span class="sep">-</span> 
                <?php endif ?>
                <?php echo $this->timestamp($comment->creation_date); ?>
                                            </div>
                                        </div>
                                    </li>                         
            <?php endforeach; ?>
        <?php else : ?>
                                <li>
                                    <div class="no-comments">
                                        <i class="ui-icon ui-icon-comment-alt"></i>
                                        <span><?php echo $this->translate('No Comments') ?></span>
                                    </div>	
                                </li>
        <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div>
            <?php
            if ($canComment) :
                $this->commentForm->setActionIdentity($action->action_id)
                ?>
                <div style="display:none;" class="sm-comments-post-comment-form"  id="hide-commentform-<?php echo $action->action_id ?>">
                    <table cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="sm-cmf-left">
            <?php echo $this->commentForm->render(); ?>
                            </td>
                            <td>
                                <button class="ui-btn-default ui-btn-c" data-role="none" type="submit"  onclick="sm4.activity.attachComment($('#activity-comment-form-<?php echo $action->action_id ?>'));"><?php echo $this->translate('Post'); ?></button>
                            </td>
                        </tr>
                    </table>			
                    <div style="display:none;"> 
                        <script type="text/javascript">
                            sm4.core.runonce.add(function () {
                                $('#activity-comment-body-<?php echo $action->action_id ?>').autoGrow();
                                $('.sm-comments-post-comment-<?php echo $action->action_id ?>').on('vclick', function () {
                                    sm4.activity.toggleCommentArea(this, '<?php echo $action->action_id ?>');
                                });
            <?php //if ($this->writecomment):  ?>
                                sm4.activity.toggleCommentArea($('.sm-comments-post-comment-<?php echo $action->action_id ?>'), '<?php echo $action->action_id ?>');
            <?php //endif;  ?>
                            });
                        </script>
                    </div>
                </div>
                <div class="sm-comments-post-comment sm-comments-post-comment-<?php echo $action->action_id ?>" >
                    <div>
                        <input type="text" placeholder="<?php echo $this->translate('Write a comment...'); ?>" data-role="none" class="ui-input-field" />
                    </div> 
                </div>
            <?php endif; ?>

    <?php endif; ?>

    </div> <!-- End of Comment Likes -->

    <div id='like-activity-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
    <div id='like-comment-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>

    <div style="display:none;">        
        <script type="text/javascript">
            var action_id = '<?php echo $action->action_id ?>';
            sm4.core.runonce.add(function () {
                $('.ps-close-popup').on('click', function () {
                    sm4.core.closefunctions.trigger();

                });
            });
            sm4.core.closefunctions.add(function () {
                closeFeedPopup();
            });

            var closeFeedPopup = function (target) {

                if (typeof $('#comment-activity-item-' + action_id).html() == 'undefined')
                    return;

                if (sm4.core.isApp()) {
                    if ($.type(sm4.activity.preloadedCommentArray['activity-comments_' + action_id]) != 'undefined')
                        sm4.activity.preloadedCommentArray['activity-comments_' + action_id].find('#comment-activity-item-' + action_id).html($('.ps-close-popup').closest('#feedsharepopup').find('#comment-activity-item-' + action_id).html());
                    else
                        sm4.activity.preloadedCommentString = '';

                }


    <?php if ($RemoveClassDone): ?>
                    $('.ui-page-active').removeClass('dnone');
    <?php else : ?>
                    $('.ui-page-active').removeClass('pop_back_max_height');
    <?php endif; ?>

                $('.ps-close-popup').closest('#feedsharepopup').remove();
                $.mobile.silentScroll(parentScrollTop);
            }
            sm4.activity.getLikeUsers('<?php echo $action->action_id ?>', false, 1);

        </script>  
    </div>   

<?php else: ?>

    <?php
    include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/viewcomment.tpl';
    ?> 

<?php endif; ?>