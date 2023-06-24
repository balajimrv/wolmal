<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-linedin-comments.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id='comment-activity-item-linkedin' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper" >
 
    <div class="" id="showhide-comments-linkedin" style="display:block">
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ps-close-popup ui-btn-right ui-link ui-btn ui-icon-remove ui-btn-icon-notext ui-shadow-icon ui-shadow ui-corner-all" onclick="$('.ui-page-active').removeClass('dnone');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
        <?php $commentcount = $this->translate(array('%s comment', '%s comments', $this->commentinfo['@attributes']['total']), $this->locale()->toNumber($this->commentinfo['@attributes']['total']));?>
        <h2 class="ui-title" id="count-feedcomments" data-message="<?php echo $commentcount;?>"><?php echo $commentcount ?></h2>
      </div>
      <div class="sm-ui-popup-container">
        <div class="comments">
          <ul>
            <?php if ($this->like_count > 0): ?>
              <li class="comments_likes">
                <i class="ui-icon ui-icon-thumbs-up"></i>
                <?php echo $this->translate(array('%s person likes this', '%s people like this', $this->like_count), $this->locale()->toNumber($this->like_count)) ?>  

<!--                <a href="javascript:void(0);" onclick="$('#comment-activity-item-' + 'linkedin').css('display', 'none');$('#like-activity-item-' + 'linkedin').css('display', 'block');" class="comment_likes ui-link-inherit">												
                  <i class="ui-icon icon-right ui-icon-arrow-r"></i>
                </a>-->

              </li>	
            <?php endif; ?>

            <?php if (0): ?>
              <li class="comments_likes" onclick="sm4.activity.getOlderComments(this, '<?php echo $action->getObject()->getType() ?>', '<?php echo $action->getObject()->getIdentity() ?>', '2', '<?php echo $action->action_id; ?>');">
                <a href="javascript:void(0);" ><?php echo $this->translate('Load Previous Comments') ?></a>
              </li>
            <?php endif; ?>
            <?php if (!empty($this->commentinfo['@attributes']['total'])): ?>
            
              <?php if ($this->commentinfo['@attributes']['total'] == 1) { 
                $comment = $this->commentinfo['update-comment'];
                $this->commentinfo['update-comment'] = array ('0' => $comment);
                }
              ?>
              <?php foreach ($this->commentinfo['update-comment'] as $comment): ?>
                <li id="comment-<?php echo $comment['id'] ?>">
                  <div class="comments_author_photo">
                    <?php 
											if (isset($comment['person']['picture-url'])) {
													$image_url = $user['person']['picture-url'];

											}
											else {
												$image_url = $this->layout()->staticBaseUrl. 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
											}
											$commenter_photo = '<img src="'.  $image_url. '" alt="" class="thumb_icon item_photo_user" />';
                    ?>
                    <?php echo $this->htmlLink($comment['person']['site-standard-profile-request']['url'], $commenter_photo, array('target' => '_blank') ) ?>
                  </div>
                  <div class="comments_info">
                    <div class='comments_author'>
                      <?php $author_name = $comment['person']['first-name'] . ' ' . $comment['person']['last-name'] ;?>
                      <?php echo $this->htmlLink($comment['person']['site-standard-profile-request']['url'], $author_name, array('target' => '_blank')); ?>
                    </div>
                    <div class="comments_body">
                      <?php echo $this->viewMore($comment['comment']) ?>
                    </div>
                    <div class="comments_date">                      
                      <?php echo $this->timestamp($comment['timestamp']/1000); ?>
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
						$this->commentForm->setActionIdentity('linkedin');
						
			?>
      
      <div style="display:none;"> 
        <script type="text/javascript">
          (function(){
            var post_id = '<?php echo $this->post_id ?>'; 
          })();
        </script>
      </div>
      <div style="display:none;" class="sm-comments-post-comment-form"  id="hide-commentform-linkedin">
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="sm-cmf-left">
                <?php echo $this->commentForm->render(); ?>
            </td>
            <td>
              <button class="ui-btn-default ui-btn-c" data-role="none" type="submit"  onclick="sm4.socialactivity.linkedin.attachComment($('#activity-comment-form-linkedin'), '<?php echo $this->post_id ?>', '<?php echo $this->like_count;?>', 'linkedin', '<?php echo $this->timestamp;?>');"><?php echo $this->translate('Post'); ?></button>
            </td>
          </tr>
        </table>			
        <div style="display:none;"> 
          <script type="text/javascript">
            (function(){
              var post_id = '<?php echo $this->post_id ?>';
              sm4.core.runonce.add(function(){
                $('#activity-comment-body-' + post_id).autoGrow();

              });                   
            })();
          </script>
        </div>
      </div>
      <div class="sm-comments-post-comment" onclick="sm4.activity.toggleCommentArea(this, '<?php echo $this->post_id ?>');">
        <div>
          <input type="text" placeholder="<?php echo $this->translate('Write a comment...'); ?>" data-role="none" class="ui-input-field" />
        </div> 
      </div>
   
</div> <!-- End of Comment Likes -->

<div id='like-activity-item-linkedin' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='like-comment-item-linkedin' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>

<div style="display:none;">        
  <script type="text/javascript">
//     var action_id = '<?php echo $this->post_id ?>';
//     sm4.socialactivity.linkedin.getLikeUsers(action_id, false, 1);     
//     $('.ui-header').children('a').bind('click', function () {$('#jqm_dialog_advancedactivity-index-viewcomment').dialog('close')}) 
  </script>  
</div>        


