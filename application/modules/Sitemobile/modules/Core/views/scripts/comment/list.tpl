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

<?php if (!$this->page): ?>
  <div class='comments' id="comments">
  <?php endif; ?>
  <div class='comments_options'>
    <?php if (isset($this->form)): ?>
      <a href='javascript:void(0);'  onclick="$.mobile.activePage.find('#comment-form').css('display', ''); $.mobile.activePage.find('#comment-form').find('#body').focus();"><?php echo $this->translate('Post Comment') ?></a>
    <?php endif; ?>

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
      <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>
        - <a href="javascript:void(0);" onclick="sm4.core.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="sm4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <ul >
    <li>
      <div> </div>
      <div class="comments_likes">
        <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>         
          <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
            <?php
            echo $this->translate(array('%s like', '%s likes', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount()));
            ?>   
          </a> <b class="sep">-</b> 
        <?php endif; ?>
        <span id="comments_count">  
          <?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span></div>    
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
                <a href="javascript:void(0);" onclick="sm4.core.comments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                  <?php echo $this->translate('Delete') ?>
                </a>
                <span class="sep"> -</span>
              <?php endif; ?>
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
              <?php if ($comment->likes()->getLikeCount() > 0): ?>
                <span class="sep"> -</span>
                <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" onclick="sm4.core.comments.comment_likes(<?php echo sprintf("'%d'", $comment->comment_id) ?>)" class="comments_comment_likes">
                  <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                </a>
              <?php endif ?>

              <span class="sep"> -</span>
              <?php echo $this->timestamp($comment->creation_date); ?>
            </div>
            <?php /*
              <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
              -
              <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
              <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
              </a>
              <?php endif ?>
              </div>
              <div class="comments_comment_options">
              <?php if( $canDelete && $this->canComment ): ?>
              -
              <?php endif ?>
              </div>
             *
             */ ?>
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

    <?php endif; ?>

  </ul>
  <?php if (isset($this->form)): ?>
      <div style="display:none">
        <script type="text/javascript">
          sm4.core.runonce.add(function(){
            //sm4.core.comments.attachCreateComment($.mobile.activePage.find('#comment-form'));
            $.mobile.activePage.find('#comment-form').find('#body').autoGrow(); 
            $.mobile.activePage.find('#comment-form').find('textarea').attr('placeholder', sm4.core.language.translate('Write a comment...'))
         //   $.mobile.activePage.find('#comment-form').find('textarea').focus();
          });
        </script>
      </div>
    <div class="sm-comments-post-comment-form">
      <form id="comment-form" enctype="application/x-www-form-urlencoded" style ='display:block;' action="" method="post" data-ajax="false">
        <table cellspacing="0" cellpadding="0">
          <tr>    
            <td class="sm-cmf-left">
              <div>
                <?php
                foreach ($this->form->getElements() as $key => $value):
                  if ($key != "submit") : echo $this->form->$key;
                  endif;
                endforeach;
                ?>
              </div>
            </td>
            <td>
                <button class="ui-btn-default ui-btn-c" data-role="none" type="submit" id="submit" name="submit" onclick="sm4.core.comments.attachCreateComment($('#comment-form')); return false;" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
            </td>
          </tr>
        </table>
      </form>
    </div>	

    <?php //echo $this->form->setAttribs(array('id' => 'comment-form', 'style' => 'display:none;'))->render();
  endif; ?>
  <?php if (!$this->page): ?>
  </div>
<?php endif; ?>
<?php else:

include_once(APPLICATION_PATH ."/application/modules/Sitemobile/modules/Core/views/scripts/photo-comment/list.tpl");
endif; ?>
