<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $subjectType = $this->subject()->getType();?>
<?php if(isset($this->subject()->listingtype_id)):?>
  <?php $subjectType .= '_' . $this->subject()->listingtype_id;?>
<?php endif;?>


<?php if(Engine_Api::_()->seaocore()->checkEnabledNestedComment($this->subject()->getType())) { 
    include_once 'nestedcomment_index.tpl';
} else { ?>

<script type="text/javascript">
 var contentviewpage_URL = "<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user'), 'default', 'true'); ?>";
 
 <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
 sm4.core.runonce.add(function() {
   sm4.core.comments.preloadComments('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');
   
 });
 <?php endif;?>
</script>

<?php
$this->headTranslate(array(
    'Are you sure you want to delete this?',
));
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if (!$this->page): ?>
  <div class='comments' id="comments">
  <?php endif; ?>
  <div class='comments_options'>   
    <?php if (isset($this->form)): ?>
      <a href='javascript:void(0);' onclick="$.mobile.activePage.find('#comment-form').css('display', ''); $.mobile.activePage.find('#comment-form').find('#body').focus();"><?php echo $this->translate('Post Comment') ?></a>
    <?php endif; ?>

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
      <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>
        - <a href="javascript:void(0);" onclick="sm4.core.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes"><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="sm4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')" class="feed_likes"><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <ul>
    <li>
     <div> </div>
        <div class="comments_likes fleft">
        <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
           <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="comments_likes_count">
            <?php             
            echo $this->translate(array('%s like', '%s likes', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount()))
            ?>
        </a>
        <b class="sep">-</b>
      <?php endif; ?>
        <span id="comments_count"><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>
        </div>
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
									<span class="sep"> -</span>
                <?php else: ?>
                  <a href="javascript:void(0)" onclick="sm4.core.comments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes">
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
            // $.mobile.activePage.find('#comment-form').find('textarea').focus();
           });
         </script>
      </div>
     <div class="sm-comments-post-comment-form" style="margin: 0 -5px -10px;">
        <form id="comment-form" enctype="application/x-www-form-urlencoded" style='display:block;' action="" method="post" data-ajax="false">
        <table>
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
<div data-role="popup" id="popupDialog-Post" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
  
  <div data-role="header" data-theme="a" class="ui-corner-top">
      <h1><?php echo $this->translate('Delete Comment?'); ?></h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
      <h3 class="ui-title"></h3>
      <p><?php echo $this->translate('Are you sure that you want to delete this comment? This action cannot be undone.'); ?></p>              

     <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.core.comments.deleteComment('', '', '')"><?php echo $this->translate("Delete");?></a>
     <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c" onclick="javascript:sm4.core.comments.options.self = false"><?php echo $this->translate("Cancel");?></a>
    </div>   
</div>
<div data-role="popup" id="popupDialogforPhoto" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
    <div data-role="header" data-theme="a" class="ui-corner-top">
        <h1><?php echo $this->translate('Delete Feed?');?></h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
        <h3 class="ui-title"><?php echo $this->translate('Are you sure that you want to delete this activity item?');?></h3>
        <p><?php echo $this->translate('This action cannot be undone.')?></p>        
        <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.core.photocomments.deleteComment('', '', '')"><?php echo $this->translate("Delete");?></a>
        <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c" onclick="javascript:sm4.core.photocomments.options.self = false"><?php echo $this->translate("Cancel");?></a>
    </div>
</div>
<?php endif; ?>
<?php else : ?>
    <div class="likecomment" id="<?php echo 'likecomment-' . $this->subject()->getIdentity(); ?>">
      <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
        <li>
          <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>
            <a href="#" class="ui-link ui-btn-likeunlike" onclick="sm4.core.applikes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')">
              <i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-icon feed-unliked-icon"></i>
            </a>
          <?php else: ?>
            <a href="#" class="ui-link ui-btn-likeunlike" onclick="sm4.core.applikes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')">
              <i class="ui-icon ui-icon-thumbs-up-alt feed-like-icon feed-liked-icon"></i>
            </a>
          <?php endif; ?>    
        </li>
        <li>
          <a href="#" class="ui-link" onclick="sm4.core.comments.comments_likes_popup('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'list'), 'default', 'true'); ?>', true);">
            <i class="ui-icon ui-icon-comment"></i>
          </a>
        </li>
      <?php endif; ?>      
        <?php //if ($this->likes->getTotalItemCount() || $this->comments->getTotalItemCount()): ?>
          <li>
            <a href="#" class="ui-link" onclick="sm4.core.comments.comments_likes_popup('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'list'), 'default', 'true'); ?>', true,'<?php echo $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount()))) ?>');">
              <?php //if ($this->likes->getTotalItemCount()): ?>
                <span class="profileLikes"><?php
                  echo $this->translate(array('%s like', '%s likes', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount()))
                  ?></span>
              <?php //endif; ?>
              <?php //if ($this->comments->getTotalItemCount()): ?>
                <span class="profileComments" id="comments_count"><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>
              <?php //endif; ?>
            </a>        
          </li>
        <?php //endif; ?>
    
  </div>

<?php endif; ?>

 <?php   
}
?>