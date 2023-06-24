<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<script type="text/javascript">
  var feedURL = '<?php echo $this->url() ?>';
  var composeInstance;
  //sm4.core.runonce.add(function() {
 
  // @todo integrate this into the composer
    
    
  //});
  
 
   
</script>
<?php if(Engine_Api::_()->sitemobile()->isApp()):?>
  <div class="sm-ui-message-view-action">
    <?php
    echo $this->htmlLink(array(
        'action' => 'delete',
        'id' => null,
        'place' => 'view',
        'message_ids' => $this->conversation->conversation_id,
            ), $this->translate(''), array(
        'class' => ' smoothbox ui-btn-icon-notext', //'buttonlink icon_message_delete',
        'data-inline' => 'true',
        'data-mini' => 'true',
        'data-icon' => 'trash',
        'data-role' => 'button',
        'data-iconpos' => 'notext'
    ))
    ?>
  </div>
<?php endif ?>
<?php if(!Engine_Api::_()->sitemobile()->isApp()):?>
<h3 class="sm-ui-message-view-title">
  <?php if ('' != ($title = trim($this->conversation->getTitle()))): ?>
    <?php echo $title ?>
  <?php else: ?>
    <em>
      <?php echo $this->translate('(No Subject)') ?>
    </em>
  <?php endif; ?>
</h3>
<?php endif;?>
<div class="sm-ui-message-view-between">
  <?php
  // Resource
  if ($this->resource) {
    echo $this->translate('To members of %1$s', $this->resource->toString());
  }
  // Recipients
  else {
    $you = array_shift($this->recipients);
    $you = $this->htmlLink($you->getHref(), ($this->viewer()->isSelf($you) ? $this->translate('You') : $you->getTitle()));
    $them = array();
    foreach ($this->recipients as $r) {
      if ($r != $this->viewer()) {
        $them[] = ($r == $this->blocker ? "<s>" : "") . $this->htmlLink($r->getHref(), $r->getTitle()) . ($r == $this->blocker ? "</s>" : "");
      } else {
        $them[] = $this->htmlLink($r->getHref(), $this->translate('You'));
      }
    }

    if (count($them))
      echo $this->translate('Between %1$s and %2$s', $you, $this->fluentList($them));
    else
      echo 'Conversation with a deleted member.';
  }
  ?>
</div>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="sm-ui-message-view-action">
    <?php
    echo $this->htmlLink(array(
        'action' => 'delete',
        'id' => null,
        'place' => 'view',
        'message_ids' => $this->conversation->conversation_id,
            ), $this->translate('Delete'), array(
        'class' => ' smoothbox', //'buttonlink icon_message_delete',
        'data-inline' => 'true',
        'data-mini' => 'true',
        'data-theme' => 'b',
        'data-role' => 'button',
    ))
    ?>
  </div>
<?php endif ?>

<div class="sm-ui-message-view">
  <ul data-role="listview" data-icon="none">
    <?php foreach ($this->messages as $message): $user = $this->user($message->user_id); ?>
      <li class="sm-ui-browse-items">
        <?php if(Engine_Api::_()->sitemobile()->isApp()):?>
          <p class="sm-ui-message-time"><?php echo $this->timestamp($message->date) ?></p>
        <?php endif ?>  
        <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
        <h3><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></h3>
        <p class="sm-ui-message-body">
          <?php echo nl2br(html_entity_decode($message->body)) ?>
          <?php if (!empty($message->attachment_type) && null !== ($attachment = $this->item($message->attachment_type, $message->attachment_id))): ?>
            <?php $attrs = array(); ?>
            <?php if ($attachment->getType() == 'core_link'): ?>
              <?php $attrs = array('rel' => 'external'); ?>
            <?php elseif ($attachment->getType() == 'album_photo'): ?>
              <?php $attrs = array('class' => 'thumbs_photo'); ?>
            <?php endif; ?>
          <div class="sm-ui-message-view-message-attachment">
            <div class="message_attachment_photo">
              <?php if (null !== $attachment->getPhotoUrl()): ?>
                <?php echo $this->htmlLink($attachment->getHref(array('message' => $message->conversation_id)), $this->itemPhoto($attachment, 'thumb.normal'), $attrs); ?>
              <?php endif; ?>
            </div>
            <div class="message_attachment_info">
              <div class="message_attachment_title">
                <strong><?php echo $this->htmlLink($attachment->getHref(array('message' => $message->conversation_id)), $attachment->getTitle(), $attrs) ?></strong>
              </div>
              <div class="message_attachment_desc">
                <?php echo $attachment->getDescription() ?>
              </div>
            </div>

          </div>  

        <?php endif; ?>
        </p> 
        <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
          <p class="t_light"><?php echo $this->timestamp($message->date) ?></p>
        <?php endif ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php if(Engine_Api::_()->sitemobile()->isApp()):?>
<div class="sm-ui-message-quick-reply">
  <div class="sm-comments-post-comment-form">
    <form id="messages_form_reply" enctype="application/x-www-form-urlencoded"  action="<?php echo $this->url();?>" method="post">
    <table cellspacing="0" cellpadding="0">
      <tr>
        <td class="sm-cmf-left">
          <textarea placeholder="Write a message" name="body" style="height:20px;"></textarea>  
        </td>
        <td>
          <button class="ui-btn-default ui-btn-c" data-role="none" type="submit"><?php echo $this->translate('Send'); ?></button>
        </td>
      </tr>
    </table>
    </form>
  </div>
  
</div>
<?php endif;?>
<?php if (!$this->locked && !Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class='sm-ui-message-quick-reply' >
    <?php if ((!$this->blocked && !$this->viewer_blocked) || (count($this->recipients) > 1)): ?>
      <div id='messages_form_reply_dummy'>
        <input class="seaocore_comment_box seaocore_txt_light" onclick="showMessageReply();" placeholder="<?php echo $this->translate('Write a message...') ?>" data-mini="true" />
      </div>
      <?php echo $this->form->setAttribs(array('id' => 'messages_form_reply', 'style' => 'display:none;', 'action' => $this->url()))->render($this) ?>
    <?php elseif ($this->viewer_blocked): ?>
      <?php echo $this->translate('You can no longer respond to this message because you have blocked %1$s.', $this->viewer_blocker->getTitle()) ?>
    <?php else: ?>
      <?php echo $this->translate('You can no longer respond to this message because %1$s has blocked you.', $this->blocker->getTitle()) ?>
    <?php endif; ?>
  </div>
  <?php //ATTACH THE LINKS ALSO.  ?>
  <div id="activitypost-container-message" class="dnone">
    <div id="composer-options">
      <div id="smactivityoptions-popup" class="sm-post-composer-options">
        <ul class="share-item">
          <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
            <li>
              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'photo');" class="ui-link-inherit">
                <i class="cm-icons cm-icon-photo"></i>
                <span><?php echo $this->translate('Add Photo'); ?></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if (Engine_Api::_()->sitemobile()->enableComposer('video')) : ?>
            <li>
              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'video');" class="ui-link-inherit">
                <i class="cm-icons cm-icon-video"></i>
                <span><?php echo $this->translate('Add Video') ?></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if (Engine_Api::_()->sitemobile()->enableComposer('link')) : ?>
            <li>
              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'link');" class="ui-link-inherit">
                <i class="cm-icons cm-icon-link"></i>
                <span><?php echo $this->translate('Add Link'); ?></span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>     
    </div>
  </div>   
<script type="text/javascript">

  function showMessageReply() {
    $.mobile.activePage.find("#messages_form_reply_dummy").css('display', 'none');
    $.mobile.activePage.find("#messages_form_reply").css('display', 'block');
    $.mobile.activePage.find("#messages_form_reply").find('#body').focus();
    $.mobile.activePage.find('#activitypost-container-temp').after($.mobile.activePage.find('#submit-wrapper'))
    $.mobile.activePage.find('#activitypost-container-temp').removeClass('dnone');
  }

  $(document).bind( "pageshow", function( event, data ) {  
    $.mobile.activePage.find("#messages_form_reply").find('#body').on('blur',function(){ 
      $(this).delay(300).queue(function(){ 
        if(!$.mobile.activePage.find("#messages_form_reply").find('#body').val()) {
          $.mobile.activePage.find("#messages_form_reply_dummy").css('display', 'block');
          $.mobile.activePage.find("#messages_form_reply").css('display', 'none');
          $.mobile.activePage.find("#messages_form_reply").append($.mobile.activePage.find('#activitypost-container-temp').next());
          if (!sm4.activity.composer.active)
            $.mobile.activePage.find('#activitypost-container-temp').addClass('dnone');
        }
        $(this).clearQueue();
      });
    });
  });
  sm4.core.runonce.add(function() {  
                  
     $.mobile.activePage.find('#submit-wrapper').off('click').on('click', function () {
       if(!$.mobile.activePage.find("#messages_form_reply").find('#body').val()) {
         $.mobile.activePage.find("#messages_form_reply_dummy").css('display', 'block');
         $.mobile.activePage.find("#messages_form_reply").css('display', 'none');        
       }
       else { 
         $.mobile.activePage.find('#messages_form_reply').submit();
       }

     });
     sm4.activity.initialize($.mobile.activePage.find('#body'), false);

     var requestOptions = {
       'photourl'  : sm4.core.baseUrl + 'album/album/compose-upload/type/wall',
       'videourl'  : sm4.core.baseUrl + 'video/index/compose-upload/format/json/c_type/wall',
       'videodeleteurl'  : sm4.core.baseUrl + 'video/index/delete',
       'musicurl' : 'music/playlist/add-song/format/json?ul=1&type=wall'
     }
     sm4.activity.composer.init(requestOptions);

     if ($.type($.mobile.activePage) != 'undefined') {


       sm4.activity.advfeed_array[$.mobile.activePage.attr('id') + '_attachmentURL'] = requestOptions;   
     }
     $('#activitypost-container-temp').remove();
     $('#activitypost-container-message').attr('id', 'activitypost-container-temp');
     $.mobile.activePage.find('#messages_form_reply').off('submit').on('submit', function (e) {
       $('#activitypost-container-temp').remove();

     });
   });
</script>
<?php endif ?>