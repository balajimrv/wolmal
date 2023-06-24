<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: outbox.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if (count($this->paginator)): ?>
  <div class="messages_list sm-content-list" id="messages_list">
    <ul class='sm-ui-lists' data-role="listview" data-icon="none" id="smessages_list">
      <?php
      foreach ($this->paginator as $conversation):
        $message = $conversation->getOutboxMessage($this->viewer());
        $recipient = $conversation->getRecipientInfo($this->viewer());
        $resource = "";
        $sender = "";
        if ($conversation->hasResource() &&
                ($resource = $conversation->getResource())) {
          $sender = $resource;
        } else if ($conversation->recipients > 1) {
          $sender = $this->viewer();
        } else {
          foreach ($conversation->getRecipients() as $tmpUser) {
            if ($tmpUser->getIdentity() != $this->viewer()->getIdentity()) {
              $sender = $tmpUser;
              break;
            }
          }
        }
        if ((!isset($sender) || !$sender)) {
          if ($this->viewer()->getIdentity() !== $conversation->user_id) {
            $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
          } else {
            $sender = $this->viewer();
          }
        }
        if (!isset($sender) || !$sender) {
          //continue;
          $sender = new User_Model_User(array());
        }
        ?>

        <li class="sm-ui-browse-items <?php if (!$recipient->inbox_read): ?>messages_list_new<?php endif; ?>" id="message_conversation_<?php echo $conversation->conversation_id ?>">
          <div class="ui-item-member-action" style="display:none;" id="ui-item-member-action">
            <?php
            echo $this->htmlLink(array(
                'action' => 'delete',
                'id' => null,
                'place' => 'view',
                'message_ids' => $conversation->conversation_id,
                    ), $this->translate(''), array(
                'class' => 'smoothbox',
                'data-role' => "button", 'data-icon' => "false", "data-inline" => "true", "data-icon" => "trash", "data-iconpos" => "notext",  "data-mini" => 'true'
            ));
            ?>
          </div>
          <a class="ui-btn" href="<?php echo $conversation->getHref(); ?>">
<!--          <a href="<?php //echo $conversation->getHref(); ?>">-->
            <?php echo $this->itemPhoto($sender, 'thumb.icon') ?>
            <h3>
              <?php
              // ... scary
              ( (isset($message) && '' != ($title = trim($message->getTitle()))) ||
                      (isset($conversation) && '' != ($title = trim($conversation->getTitle()))) ||
                      $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
              ?>
<!--          <a href="<?php //echo $conversation->getHref(); ?>"><strong><?php //echo $title; ?></a></strong>-->
              <?php echo $title; ?>
            </h3>
            <p>
              <?php echo $this->translate("to"); ?> <strong><?php echo $sender->getTitle() ?></strong>
              <?php //echo $this->translate("to"); ?> <?php //echo $this->htmlLink($sender->getHref(), $sender->getTitle()) ?>
            </p>
            <?php  $len=  strlen(html_entity_decode(strip_tags($message->body)));?>
            <p><?php echo Engine_String::substr(strip_tags($message->body), 0, 100).($len>100?  '...':''); ?></p>
            <p class="t_light"><?php echo $this->timestamp(strtotime($message->date)) ?></p>
          </a>  
<!--          </a> -->
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else: ?>
<div class="sm-ui-message-tip">
  <p><?php echo $this->translate(array('%s sent message total', '%s sent messages total', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?></p>
</div>
<!--  <div class="tip">
    <span>
      <?php echo $this->translate('Tip: %1$sClick here%2$s to send your first message!', "<a data-ajax='false' href='" . $this->url(array('action' => 'compose'), 'messages_general') . "'>", '</a>'); ?>
    </span>
  </div>-->
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator); ?>

<script type="text/javascript">
  $(document).bind( "pageshow", function( event, data ) {
    $(event.target).find("#smessages_list li").on( "swipeleft swiperight",  function( event ) {  
      if(event.type === 'swipeleft') {
        $(this).find('.ui-item-member-action').css('display', 'block');
      } else {
        $(this).find('.ui-item-member-action').css('display', 'none');
      }
    });
  });
</script>