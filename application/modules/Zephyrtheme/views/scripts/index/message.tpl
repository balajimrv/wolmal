<?php if( $this->paginator->getTotalItemCount() <= 0 ): ?>
  <div class="notifications_loading"><?php echo $this->translate('No new message'); ?></div>
<?php endif; ?>
<?php if( count($this->paginator) ): ?>
  <?php foreach( $this->paginator as $conversation ):
    $message = $conversation->getInboxMessage($this->viewer());
    $recipient = $conversation->getRecipientInfo($this->viewer());
    $resource = "";
    $sender   = "";
    if( $conversation->hasResource() &&
              ($resource = $conversation->getResource()) ) {
      $sender = $resource;
    } else if( $conversation->recipients > 1 ) {
      $sender = $this->viewer();
    } else {
      foreach( $conversation->getRecipients() as $tmpUser ) {
        if( $tmpUser->getIdentity() != $this->viewer()->getIdentity() ) {
          $sender = $tmpUser;
        }
      }
    }
    if( (!isset($sender) || !$sender) && $this->viewer()->getIdentity() !== $conversation->user_id ){
      $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
    }
    if( !isset($sender) || !$sender ) {
      //continue;
      $sender = new User_Model_User(array());
    }
    ?>
    <li class='<?php if( !$recipient->inbox_read ): ?>pulldown_content_item_unread<?php endif; ?>' id="message_conversation_<?php echo $conversation->conversation_id ?>" onclick="messageProfilePage('<?php echo $conversation->getHref(); ?>');">
      <div class="pulldown_item_photo">
        <?php echo $this->htmlLink($sender->getHref(), $this->itemPhoto($sender, 'thumb.icon')) ?>
      </div>
      <div class="pulldown_item_content">
        <span class="pulldown_item_content_title">
          <?php if( !empty($resource) ): ?>
            <b><?php echo $resource->toString() ?></b>
          <?php elseif( $conversation->recipients == 1 ): ?>
            <?php echo $this->htmlLink($sender->getHref(), $sender->getTitle()) ?>
          <?php else: ?>
            <b><?php echo $this->translate(array('%s person', '%s people', $conversation->recipients),
              $this->locale()->toNumber($conversation->recipients)) ?></b>
          <?php endif; ?>
        </span>
        <span class="pulldown_item_content_desc">
          <?php
            ! ( isset($message) && '' != ($title = trim($message->getTitle())) ||
            isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
            $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
          ?>
          <?php echo $this->htmlLink($conversation->getHref(), $title) ?>:
          <?php echo html_entity_decode($message->body) ?>
        </span>
        <span class="pulldown_item_content_date">
          <?php echo $this->timestamp($message->date) ?>
        </span>
      </div>
    </li>
  <?php endforeach; ?>
<?php endif; ?>


<script type="text/javascript">
  function messageProfilePage(pageUrl){
    if(pageUrl != 'null' ) { window.location.href=pageUrl; }
  }
</script>
