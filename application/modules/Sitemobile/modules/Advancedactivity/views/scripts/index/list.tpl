<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')):?>
<?php if ($this->comments->getTotalItemCount() > 0): // COMMENTS -------   ?>
  <?php $action = $this->action; ?>
  <?php
  $action = $this->action;

  $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
          $this->viewer()->getIdentity() &&
          Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
  ?>
  <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
    <li onclick ="sm4.activity.getOlderComments(this, '<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo ($this->page + 1) ?>', '<?php echo $this->action_id ?>');">
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Load Previous Comments'), array()) ?>
      </div>
    </li>
  <?php endif; ?>
  <?php
  // Iterate over the comments backwards (or forwards!)
  $comments = $this->comments->getIterator();

  $i = count($comments) - 1;
  $l = count($comments);
  $d = -1;
  $e = -1;

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
        </div>
        <div class="comments_body">
            <?php 
                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
            ?>
        </div>
        <div class="comments_date">
          <?php if ($canDelete): ?>
            <a href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id, 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.activityremove(this);" data-message="<?php echo $comment->comment_id ?>-<?php echo $action->action_id ?>"><?php echo $this->translate('Delete'); ?></a>
            -
          <?php endif; ?>
          <?php
          if ($canComment):
            $isLiked = $comment->likes()->isLike($this->viewer());
            ?>
            <?php if (!$isLiked): ?>
              <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)"> <?php echo $this->translate('Like') ?></a> - 
            <?php else: ?>
              <a href="javascript:void(0)" onclick="sm4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)"><?php echo $this->translate('Unlike') ?>
              </a> - 
            <?php endif ?>
          <?php endif ?>
          <?php if ($comment->likes()->getLikeCount() > 0): ?>
            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
              <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
            </a> - 
          <?php endif ?>
          <?php echo $this->timestamp($comment->creation_date); ?>
        </div>
      </div>
    </li>
  <?php endfor; ?>

<?php endif; ?>

<?php else:?>

<?php 
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/list.tpl';
?> 

<?php endif;?>