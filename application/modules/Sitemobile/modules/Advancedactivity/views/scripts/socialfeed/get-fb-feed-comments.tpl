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
<?php if ($this->showcoments): // COMMENTS -------   ?>
 <div id='comment-activity-item-<?php echo $this->action_id ?>' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
   <div class="" id="showhide-comments-<?php echo $this->action_id ?>" style="display:block">
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-icon-right" onclick="$('.ui-page-active').removeClass('pop_back_max_height');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
        <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate('comments'); ?></h2>
      </div>
   <div class="sm-ui-popup-container">
        <div class="comments">
          <ul>           
            
            <?php if ($this->like_count > 0): ?>
              <li class="comments_likes">
                <i class="ui-icon ui-icon-thumbs-up"></i>
                <a href="javascript:void(0);" onclick="$('#comment-activity-item-' + <?php echo $this->action_id ?>).css('display', 'none');$('#like-activity-item-' + <?php echo $this->action_id ?>).css('display', 'block');" >

                  <?php echo $this->translate(array('%s person likes this', '%s people like this', $this->like_count), $this->locale()->toNumber($this->like_count)) ?>                            
                </a>

                <a href="javascript:void(0);" onclick="$('#comment-activity-item-' + <?php echo $this->action_id ?>).css('display', 'none');$('#like-activity-item-' + <?php echo $this->action_id ?>).css('display', 'block');" class="comment_likes ui-link-inherit">												
                  <i class="ui-icon icon-right ui-icon-arrow-r"></i>
                </a>

              </li>	
            <?php endif; ?>
  <?php
  // Iterate over the comments backwards (or forwards!)


  foreach ($this->fbComments['data'] as $comment):
//    $comment = $comments[$i];
//    $poster = $this->item($comment->poster_type, $comment->poster_id);
//    $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
    ?>
    <li id="comment-<?php echo $comment['id'] ?>">
      <div class="comments_author_photo">
        <a href="https://facebook.com/<?php echo $comment['from']['id']?>" target="_blank">
            <img src="https://graph.facebook.com/<?php echo $comment['from']['id'];?>/picture" alt="" />
        </a>    
      </div>



      <div class="comments_info">
        <div class='comments_author'>
          <a href="https://facebook.com/<?php echo $comment['from']['id']?>" target="_blank">
             <?php echo $comment['from']['name'] ?>
          </a>  
        </div>
        <div class="comments_body">
          <?php echo $this->viewMore($comment['message']) ?>
        </div>
        <div class="comments_date">   
          
          <?php if ($comment['like_count'] > 0): ?>
            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment['id'] ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $this->action_id ?>).css('display', 'none');$('#like-comment-item-' + <?php echo $this->action_id ?>).css('display', 'block');sm4.socialactivity.comment_likes('<?php echo $this->action_id ?>','<?php echo $comment['id']; ?>', 1)">
              <?php echo $this->translate(array('%s likes this', '%s like this', $comment['like_count']), $this->locale()->toNumber($comment['like_count'])) ?>
            </a> - 
          <?php endif ?>
          <?php echo $this->timestamp($comment['created_time']); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
          </ul>
        </div>
   </div>
 </div>
 </div>
<?php endif; ?><!-- End of Comment Likes -->

<div id='like-activity-item-<?php echo $this->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='like-comment-item-<?php echo $this->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>

<div style="display:none;">        
  <script type="text/javascript">
    var action_id = '<?php echo $this->action_id ?>';
    sm4.socialactivity.getLikeFeedUsers(action_id, false, 1);     
    $('.ui-header').children('a').bind('click', function () {$('#jqm_dialog_advancedactivity-socialfeed-getFbFeedComments').dialog('close')}) 
  </script>  
</div>     

    