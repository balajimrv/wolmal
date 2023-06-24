<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _subjectcommentbodyoptions.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php 
  $canComment = $this->canComment;
  $comment = $this->comment;
  $actionBody = $this->actionBody;
?>
<ul class="comments_date" id="comments_reply_<?php echo $comment->comment_id; ?>_<?php echo $actionBody->getIdentity(); ?>" style="display:block;">
  
		<?php if( $canComment ):
      $isLiked = $comment->likes()->isLike($this->viewer());
    ?>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedcomment.enablenestedcomments', 1)): ?>
        <li class="comments_reply">
          <?php echo $this->htmlLink('javascript:;', $this->translate('SESADVREPLY'), array('class' => 'sesadvancedcommentreply')) ?>
        </li>
        <li class="sep">&middot;</li>
      <?php endif; ?>
      <li class="comments_like">
        <?php if( !$isLiked ): ?>
          <a href="javascript:void(0)" onclick="sesadvancedcommentlike(<?php echo sprintf("'%d', %d, %s, %d,%d,'%s',%d", $actionBody->getIdentity(), $comment->getIdentity(),'this',$this->page,1,$actionBody->getType(),$actionBody->getIdentity()) ?>)">
            <?php echo $this->translate('SESADVLIKE') ?>
          </a>
        <?php else: ?>
          <a href="javascript:void(0)" onclick="sesadvancedcommentunlike(<?php echo sprintf("'%d', %d, %s, %d,%d,'%s',%d", $actionBody->getIdentity(), $comment->getIdentity(),'this',$this->page,1,$actionBody->getType(),$actionBody->getIdentity()) ?>)">
            <?php echo $this->translate('SESADVUNLIKE') ?>
          </a>
        <?php endif ?>
      </li>   
    <?php endif ?>                      
    <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
    <li class="sep">&middot;</li> 
      <li class="comments_likes_total">
        <a href="javascript:;" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="sessmoothbox" data-url="<?php echo $this->url(array('module' => 'sesadvancedcomment', 'controller' => 'ajax', 'action' => 'comment-likes', 'comment_id' => $comment->getIdentity(), 'id' => $actionBody->getIdentity(),'resource_type'=>$actionBody->getType(), 'format' => 'smoothbox'), 'default', true); ?>">
           <i style="background-image:url(<?php echo Engine_Api::_()->sesadvancedcomment()->likeImage(1); ?>)"></i>
            <?php echo $comment->likes()->getLikeCount(); ?>
        </a>
      </li>
    <?php endif ?>
    <li class="sep">&middot;</li>
    <li class="comments_timestamp">
    	<?php echo $this->timestamp($comment->creation_date); ?>
   	</li>
  </ul>