<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _birthdayActivityText.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $sharesTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity'); ?>
<?php $item = $this->poster[0]; ?>
<li>
  <div class="feed_item_header">
    <div class='feed_item_photo'>
      <?php
      echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', $item->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity())
      )
      ?>
    </div>
    <div class="feed_item_status">
      <div class="feed_item_generated">
<?php
if ($this->countPoster == 1):
  echo $this->translate('%1$s  wrote on %2$s\'s Wall for birthday.', $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sea_add_tooltip_link feed_item_username', 'rel' => $item->getType() . ' ' . $item->getIdentity())), $this->htmlLink($this->mainAction->getObject()->getHref(), $this->mainAction->getObject()->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->mainAction->getObject()->getType() . ' ' . $this->mainAction->getObject()->getIdentity())));
endif;
?>
        <?php
        if ($this->countPoster == 2):
          echo $this->translate('%1$s  and %2$s also wrote on %3$s\'s Wall for birthday.', $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sea_add_tooltip_link feed_item_username', 'rel' => $item->getType() . ' ' . $item->getIdentity())), $this->htmlLink($this->poster[1]->getHref(), $this->poster[1]->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->poster[1]->getType() . ' ' . $this->poster[1]->getIdentity())), $this->htmlLink($this->mainAction->getObject()->getHref(), $this->mainAction->getObject()->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->mainAction->getObject()->getType() . ' ' . $this->mainAction->getObject()->getIdentity())));
        endif;
        ?>
        <?php
        if ($this->countPoster > 2):
          $URL = $this->url(array('module' => 'advancedactivity', 'controller' => 'feed', 'action' =>
              'get-other-post', 'id' => $this->mainAction->getObject()->getIdentity()), 'default', true);

          $otherFriends = '<span class="aaf_feed_show_tooltip_wrapper"><a href=' . $URL . 'class="smoothbox">' . $this->translate('%s other friends', ($this->countPoster - 1)) . '</a>
                        <span class="aaf_feed_show_tooltip" style="margin-left:-8px;">
                          <img src="' . $this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/images/tooltip_arrow.png" />';
          for ($i = 1; $i < count($this->poster); $i++):
            $otherFriends.= $this->poster[$i]->getTitle() . "<br />";
          endfor;
          $otherFriends.='</span>
                      </span>';
          echo $this->translate('%1$s  and %2$s also wrote on %3$s\'s Wall for birthday.', $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sea_add_tooltip_link feed_item_username', 'rel' => $item->getType() . ' ' . $item->getIdentity())), $otherFriends, $this->htmlLink($this->mainAction->getObject()->getHref(), $this->mainAction->getObject()->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $this->mainAction->getObject()->getType() . ' ' . $this->mainAction->getObject()->getIdentity()));

        endif;
        ?>
      </div>
    </div>
  </div>


  <div class='feed_item_body'>

    <?php $object = $this->mainAction->getObject();
    $remove_patern = ' &rarr; ' . $object->toString(array('class' => 'feed_item_username sea_add_tooltip_link', 'rel' => $object->getType() . ' ' . $object->getIdentity())) ?>
    <div class="feed_item_attachments feed_item_birthday">
      <span class="feed_attachment_birthday_link">
        <div>
<?php echo $this->htmlLink($this->mainAction->getObject()->getHref(), $this->itemPhoto($this->mainAction->getObject(), 'thumb.profile', $this->mainAction->getObject()->getTitle())) ?>
          <div>
            <div class="feed_item_link_title"> 
<?php echo $this->htmlLink($this->mainAction->getObject()->getHref(), $this->mainAction->getObject()->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->mainAction->getObject()->getType() . ' ' . $this->mainAction->getObject()->getIdentity())); ?>
            </div>    
            <div class="feed_item_link_desc">  
            <?php echo $this->translate("Birthday:") ?><?php echo $this->birthdate; ?>
            </div>
<?php if ($this->isAbletoWish): ?>
              <div class="feed_item_link_desc">
                <a href="javascript:void(0)" onclick="$('#new_post_bd_<?php echo $this->mainAction->action_id ?>').css('display', ''); $('#activity-bd-write-body-<?php echo $this->mainAction->action_id ?>').focus();"> <?php echo $this->translate("Write on %s's Wall", $this->mainAction->getObject()->getTitle()) ?>  </a>
              </div>
<?php endif; ?>
          </div>
        </div>
      </span>
    </div>

    <ul id="new_post_bd_<?php echo $this->mainAction->action_id ?>" class="feed_item_birthday_post" style="display:none;">
     	<li>
        <span id="add_post">
          <input class="aaf_birthday_wish_input" type="text" id="activity-bd-write-body-<?php echo $this->mainAction->action_id ?>" placeholder='<?php echo $this->string()->escapeJavascript($this->translate("Write on %s's Wall...", $this->mainAction->getObject()->getTitle())); ?>' />
          <button class="ui-btn-default ui-btn-action" data-role="none" type="button"  onclick="postWishFeed(event, '<?php echo $this->mainAction->object_id ?>', '<?php echo $this->mainAction->action_id ?>')"><?php echo $this->translate('Post'); ?></button>
        </span>
      </li>
    </ul>

      <?php $advancedactivityCoreApi = Engine_Api::_()->advancedactivity(); ?>
    <ul id="birthdate_feeds_<?php echo $this->mainAction->action_id ?>" class="feed_item_birthday_feeds">
      <?php $count = 0; ?>
      <?php foreach ($this->birthdayActions as $action): ?>
          <?php $item = $action->getSubject(); ?>
        <li class="clr View_More_Birthday_Feed_<?php echo $this->mainAction->action_id ?> <?php echo 'Hide_' . $item->getType() . "_" . $item->getIdentity() ?>" id="activity-item-<?php echo $action->action_id ?>" style="display:<?php echo $count > 1 ? 'none' : '' ?>;">
          <?php $count++; ?>
  <?php $this->commentForm->setActionIdentity($action->action_id) ?>
          <script type="text/javascript">
            (function(){
              var action_id = '<?php echo $action->action_id ?>';
            })();
          </script>
          <div id="main-feed-<?php echo $action->action_id ?>">
            <div class="feed_item_header"> 
              <?php
              if ($this->viewer()->getIdentity() && (
                      $this->activity_moderate || (
                      $this->allow_delete && (
                      ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                      ('user' == $action->object_type && $this->viewer()->getIdentity() == $action->object_id)
                      )
                      )
                      )):
                ?>

                <div class="feed_items_options_btn">        
                  <a href="javascript:void(0);" onclick="sm4.activity.showOptions('<?php echo $action->action_id ?>')" data-role="button" data-icon="carat-d" data-iconpos="notext" data-theme="c" data-inline="true"></a>
                </div>
                <!--        <div class="feed_item_option_delete aaf_birthday_feed_delete_btn">             
                         
                           
                        </div>-->
                <?php endif; ?> 

              <div class='feed_item_photo'>    
  <?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity())
  ) ?>
              </div>
              <div class="feed_item_status">
                <div class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
  <?php //echo $action->getContent()  ?>
  <?php echo str_replace($remove_patern, "", $this->getContent($action)); ?>
                </div>  
              </div>
            </div>       
            <div class="feed_item_body">
              <?php // Main Content ?>

                <?php // Attachments ?>
                <?php if ($action->getTypeInfo()->attachable && $action->attachment_count > 0): // Attachments ?>
                <div class='feed_item_attachments'>
                  <?php if ($action->attachment_count > 0 && count($action->getAttachments()) > 0): ?>
                    <?php if (count($action->getAttachments()) == 1 &&
                            null != ( $richContent = current($action->getAttachments())->item->getRichContent())): ?>
                        <?php echo $richContent; ?>
                      <?php else: ?>
                        <?php foreach ($action->getAttachments() as $attachment): ?>
                        <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                            <?php if ($attachment->meta->mode == 0): // Silence ?>
                            <?php elseif ($attachment->meta->mode == 1): // Thumb/text/title type actions ?>
                            <div>
                              <?php if ($attachment->item->getPhotoUrl()): ?>
                                <?php
                                if ($attachment->item->getType() == "core_link") {
                                  $attribs = Array('target' => '_blank');
                                } else {
                                  $attribs = Array();
                                }
                                ?>                                    
                                <?php if (SEA_ACTIVITYFEED_LIGHTBOX && strpos($attachment->meta->type, '_photo')): ?>
                                  <?php $attribs = @array_merge($attribs, array('onclick' => 'openSeaocoreLightBox("' . $attachment->item->getHref() . '");return false;')); ?>
                                <?php endif; ?>
                                <?php if (strpos($attachment->meta->type, '_photo')): ?>
                                  <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.medium', $attachment->item->getTitle(), array('class' => 'aaf-feed-photo-1')), $attribs) ?>
                                <?php else: ?>
                                  <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                                <?php endif; ?>
                              <?php endif; ?>
                              <div>
                                <div class='feed_item_link_title'>
                                  <?php
                                  if ($attachment->item->getType() == "core_link") {
                                    $attribs = Array('target' => '_blank');
                                  } else {
                                    $attribs = array('class' => 'sea_add_tooltip_link', 'rel' => $attachment->item->getType() . ' ' . $attachment->item->getIdentity());
                                  }
                                  echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                                  ?>
                                </div>
                                <div class='feed_item_link_desc'>
                                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                                </div>
                              </div>
                            </div>
          <?php endif; ?>
                        </span>
                        <?php endforeach; ?>
                      <?php endif; ?>
                  <?php endif; ?>
                </div>
                <?php endif; ?>

              <?php // Icon, time since, action links  ?>
              <?php
              $canComment = ( $action->getTypeInfo()->commentable &&
                      $this->viewer()->getIdentity() &&
                      Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment') &&
                      !empty($this->commentForm) );
              ?>
            </div>
            <div class="feed_item_btm">
              <span class="feed_item_date">
  <?php echo $this->timestamp($action->getTimeValue()) ?>
              </span>
                <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
                <span class="sep">-</span>
                <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="feed_likes">

                  <span><?php echo $this->translate(array('%s like', '%s likes', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())); ?></span>
                </a>	
    <?php if ($action->comments()->getCommentCount() > 0) : echo '<span class="sep">-</span>' ?> 
                  <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="feed_comments" >

                    <span><?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount()));
    endif; ?></span>
                </a>
              <?php elseif ($action->comments()->getCommentCount() > 0) : ?>
                <span class="sep">-</span>
                <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="feed_comments">

                  <span><?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); ?></span>
                </a>
              <?php endif; ?>
            </div>	


            <div class="feed_item_option">
              <?php if ($canComment || ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && ($action->shareable > 1 || ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()))))): ?>
                <div data-role="navbar" data-inset="false">
                  <ul>
                    <?php if ($canComment): ?>
                      <?php if ($action->likes()->isLike($this->viewer())): ?>
                        <li>
                          <a href="javascript:void(0);" onclick="javascript:sm4.activity.unlike('<?php echo $action->action_id ?>');">
                            <i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-icon feed-unliked-icon"></i>
                            <span><?php echo $this->translate('Like') ?></span>
                          </a>
                        </li>
                      <?php else: ?>
                        <li> 
                          <a href="javascript:void(0);" onclick="javascript:sm4.activity.like('<?php echo $action->action_id ?>');">
                            <i class="ui-icon ui-icon-thumbs-up-alt feed-like-icon"></i>
                            <span><?php echo $this->translate('Like') ?></span>
                          </a>
                        </li>
                      <?php endif; ?>
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes  ?>
                        <li>
                          <a href="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>">
                            <i class="ui-icon ui-icon-comment"></i>
                            <span><?php echo $this->translate('Comment'); ?></span>
                          </a>
                        </li>
                      <?php else: ?>
                        <li>
                          <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>" , "feedsharepopup")'>
                            <i class="ui-icon ui-icon-comment"></i>
                            <span><?php echo $this->translate('Comment'); ?></span>
                          </a>
                        </li>
                      <?php endif; ?>
                    <?php endif; ?>

                    <?php // Share   ?>
                    <?php if ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && $action->shareable): ?>
                      <?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
                        <li>
                          <a href="javascript:void(0);" onclick ='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
                            <i class="ui-icon ui-icon-share-alt"></i>
                            <span><?php echo $this->translate('Share'); ?></span>
                          </a>
                        </li>
                      <?php elseif ($action->getTypeInfo()->shareable == 2): ?>
                        <li>
                          <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' >
                            <i class="ui-icon ui-icon-share-alt"></i>
                            <span><?php echo $this->translate('Share'); ?></span>
                          </a>
                        </li>
                      <?php elseif ($action->getTypeInfo()->shareable == 3): ?>
                        <li>
                          <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
                            <i class="ui-icon ui-icon-share-alt"></i>
                            <span><?php echo $this->translate('Share'); ?></span>
                          </a>
                        </li>
                      <?php elseif ($action->getTypeInfo()->shareable == 4): ?>
                        <li> 
                          <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' >
                            <i class="ui-icon ui-icon-share-alt"></i>
                            <span><?php echo $this->translate('Share'); ?></span>
                          </a>
                        </li>
                      <?php endif; ?>
                    <?php endif; ?>
                  </ul>
                </div>
              <?php endif; ?>
            </div> 
          </div>

          <div id="feed-options-<?php echo $action->action_id ?>" class="feed_item_option_box" style="display:none">
            <a href="javascript:void(0);" title="" class="ui-btn-default ui-btn-danger" onclick="javascript:sm4.activity.activityremove(this);" data-url="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id), 'default', 'true'); ?>" data-message="0-<?php echo $action->action_id ?>">
              <?php echo $this->translate('Delete Post') ?>
            </a>
            <a href="#" class="ui-btn-default"onclick="sm4.activity.hideOptions('<?php echo $action->action_id ?>');">
              <?php echo $this->translate("Cancel"); ?>
            </a>
          </div>



        </li>
      <?php endforeach; ?>
      <?php if ($this->totalFeed > 2): ?>
        <li id="see_more_feed_bd_<?php echo $this->mainAction->action_id ?>" onclick="seeAllBDFeed($(this),<?php echo $this->mainAction->action_id ?>)" class="feed_item_birthday_feeds_more">
          <a href="javascript:void(0)">
            <?php echo $this->translate(array('See %1$s more feed', 'See %1$s more feeds', ($this->totalFeed - 2)), $this->locale()->toNumber(($this->totalFeed - 2))) ?>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</li>
<?php if ($this->isAbletoWish): ?>
  <script type="text/javascript">
    var birthdayPostREQActive=false;
    function postWishFeed(e, users_id, action_id) {
      if(birthdayPostREQActive)
        return;
      $.mobile.showPageLoadingMsg();
      var  text =$('#activity-bd-write-body-' + action_id).val();
      if( text == '' ) {
        return;
      }
          
      url = sm4.core.baseUrl + 'birthday/index/statusubmit';
      birthdayPostREQActive=true;        
      $.ajax({
        type: "POST", 
        dataType: "html", 
        url: url,
        data: {
          format : 'html',
          object_id : users_id,
          body :text
        },
        success:function( responseHTML, textStatus, xhr ) {
          $.mobile.hidePageLoadingMsg();
          $('#birthdate_feeds_'+action_id).prepend(responseHTML);
          $('#new_post_bd_' + action_id).css('display', 'none');
          $('#activity-bd-write-body-' + action_id).val('');
          birthdayPostREQActive=false;
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();         
            
           
        }
      });
    }

  </script>
<?php endif; ?>
<script type="text/javascript">
  function seeAllBDFeed(elm, id){
    elm.css('display', 'none'); 
    var className= '.View_More_Birthday_Feed_'+id;
    var myElements = $(className);                
    for(var i=0;i< myElements.length;i++){
      $(myElements[i]).css('display', ''); 
    }                
  }
</script>