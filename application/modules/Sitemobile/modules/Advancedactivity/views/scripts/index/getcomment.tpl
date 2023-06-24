<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getcomment.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php if (!Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')):?>
<?php $actions = $this->actions; ?>

<?php
foreach ($actions as $action):
  $comment = $action->comments()->getComment($this->comment_id);
  $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
          $this->viewer()->getIdentity() &&
          Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') );
endforeach;
?>
<script>
<?php if ($action): ?>
      $('#count-feedcomments').html("<?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); ?>");
       if (typeof $.mobile.activePage.find('#activity-item-' + <?php echo $action->action_id;?>).find('.feed_comments span').get(0) == 'undefined') {
         var commentHTML = '<a href="javascript:void(0);" onclick=\'ActivityAppCommentPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup", "<?php echo $action->action_id;?>")\' class="feed_comments"><span></span></a>';
         if (typeof $.mobile.activePage.find('#activity-item-' + <?php echo $action->action_id;?>).find('.feed_likes span').get(0)  != 'undefined') {
            commentHTML = '<span class="sep">-</span>' + commentHTML;
         }
         $.mobile.activePage.find('#activity-item-' + <?php echo $action->action_id;?>).find('.feed_item_btm').append(commentHTML);       
         
       }
       $.mobile.activePage.find('#activity-item-' + <?php echo $action->action_id;?>).find('.feed_comments span').html("<?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); ?>");
<?php endif; ?>
</script>
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
      <span class="sep">-</span>
    <?php endif; ?>
    <?php
    if ($canComment):
      $isLiked = $comment->likes()->isLike($this->viewer());
      ?>
      <?php if (!$isLiked): ?>
        <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> - 
      <?php else: ?>
        <a href="javascript:void(0)" onclick="sm4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"><?php echo $this->translate('Unlike') ?>
        </a> <span class="sep">-</span> 
      <?php endif ?>
    <?php endif ?>
    <?php if ($comment->likes()->getLikeCount() > 0): ?>
      <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">
        <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
      </a> <span class="sep">-</span> 
    <?php endif ?>
    <?php echo $this->timestamp($comment->creation_date); ?>
  </div>
</div>

 <?php else:?>
 
<?php 
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/getcomment.tpl';
?> 
 
<?php endif;?>