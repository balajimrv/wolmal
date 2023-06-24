<script type="text/javascript">
  var userWidgetRequestSend = function(action, user_id, notification_id, tokenName, token)
  {
    var url;
    if( action == 'confirm' ) {
      url = "<?php echo $this->url(array('controller' => 'friends', 'action' => 'confirm'), 'user_extended', true) ?>";
    } else if( action == 'reject' ) {
      url = "<?php echo $this->url(array('controller' => 'friends', 'action' => 'reject'), 'user_extended', true) ?>";
    } else {
      return false;
    }
    var data = {
      'user_id' : user_id,
      'format' : 'json',
    };
    data[tokenName] = token;
    (new Request.JSON({
      'url' : url,
      'data' : data,
      'onSuccess' : function(responseJSON) {
        if( !responseJSON.status ) {
          $('user-widget-request-' + notification_id).innerHTML = responseJSON.error;
        } else {
          $('user-widget-request-' + notification_id).innerHTML = responseJSON.message;
        }
      }
    })).send();
  }
</script>

<?php if( $this->friendRequests->getTotalItemCount() > 0 ): ?>  
  <?php foreach( $this->friendRequests as $notification ): ?>
  <?php $user = Engine_Api::_()->getItem('user', $notification->subject_id);?>
    <li id="user-widget-request-<?php echo $notification->notification_id; ?>"  value="<?php echo $notification->getIdentity();?>">
      <div class="pulldown_item_photo">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
      </div>
      <div class="pulldown_item_content">
        <span class="pulldown_item_content_title">
          <?php echo $notification->__toString() ?>
        </span>
        <span class="pulldown_item_content_menu">
          <?php 
          //echo $this->timestamp(strtotime($notification->date)) 
          $current_user_id = $this->string()->escapeJavascript($notification->getSubject()->getIdentity());
          $current_token_id = 'token_' . $notification->getSubject()->getGuid();
          $token = $this->token(null, $current_token_id, $this->appSalt);
          ?>
        <button type="submit" onclick='userWidgetRequestSend("confirm", <?php echo $current_user_id; ?>, <?php echo $notification->notification_id ?>, "token_user_<?php echo $current_user_id; ?>", "<?php echo $token; ?>")'><?php echo $this->translate('Add Friend');?></button> <?php echo $this->translate('or'); ?>
        
        <a href='javascript:void(0);' class="ignore_request" onclick='userWidgetRequestSend("reject", <?php echo $current_user_id; ?>, <?php echo $notification->notification_id ?>, "token_user_<?php echo $current_user_id; ?>", "<?php echo $token; ?>")'><?php echo $this->translate('ignore request');?></a>
        
        </span>
      </div>
    </li>
  <?php endforeach; ?>
<?php else:?>
  <div class="notifications_loading"><?php echo $this->translate('No new requests');?></div>
<?php endif;?>

<script type="text/javascript">
  function redirectPage(event) {    
    event.stopPropagation();
    var url;
    var current_link = event.target;
    var notification_li = $(current_link).getParent('div');
    if(current_link.get('href') == null && $(current_link).get('tag')!='img') {
      if($(current_link).get('tag') == 'li') {
        var element = $(current_link).getElements('div:last-child');
        var html = element[0].outerHTML;
        var doc = document.createElement("html");
        doc.innerHTML = html;
        var links = doc.getElementsByTagName("a");
        var url = links[links.length - 1].getAttribute("href");
      }
      else
      url = $(notification_li).getElements('a:last-child').get('href');
      window.location = url;
    }
  }
</script>