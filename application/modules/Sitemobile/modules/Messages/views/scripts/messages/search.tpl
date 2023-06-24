<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: search.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() <= 0): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results'); ?>
    </span>
  </div>
  <br />
<?php endif; ?>

<?php if (count($this->paginator)): ?>
  <div class="messages_list">
    <ul data-role="listview" data-icon="false">
      <?php
      foreach ($this->paginator as $message):
        $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);

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
            }
          }
        }
        if ((!isset($sender) || !$sender) && $this->viewer()->getIdentity() !== $conversation->user_id) {
          $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
        }
        if (!isset($sender) || !$sender) {
          //continue;
          $sender = new User_Model_User(array());
        }
        ?>


        <li class="sm-ui-browse-items" <?php if (!$recipient->inbox_read): ?> data-theme="e"<?php endif; ?> id="message_conversation_<?php echo $conversation->conversation_id ?>">

          <a href="<?php echo $conversation->getHref(); ?>">
            <?php echo $this->itemPhoto($sender, 'thumb.icon'); ?>
            <h3>
              <?php
              !( isset($message) && '' != ($title = trim($message->getTitle())) ||
                      !isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
                      $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
              ?>
              <?php echo $title; ?>
            </h3>
            <p>
              <?php echo $this->translate("by"); ?> <strong><?php echo $sender->getTitle() ?></strong>
            </p>
            <p><?php echo html_entity_decode($message->body) ?></p>
          </a> 
        </li>

      <?php endforeach; ?>
    </ul>
  </div>

  <!--
  <br />

  <button id="delete"><?php echo $this->translate('Delete Selected') ?></button>
  <script type="text/javascript">
  $('checkall').addEvent('click', function() {
    var hasUnchecked = false;
    $$('.messages_list input[type="checkbox"]').each(function(el) {
      if( !el.get('checked') ) {
        hasUnchecked = true;
      }
    });
    $$('.messages_list input[type="checkbox"]').set('checked', hasUnchecked);
  });
  $('delete').addEvent('click', function(){
    var selected_ids = new Array();
    $$('div.messages_list input[type=checkbox]').each(function(cBox) {
      if (cBox.checked)
        selected_ids[ selected_ids.length ] = cBox.value;
    });
    var sb_url = '<?php echo $this->url(array('action' => 'delete'), 'messages_general', true) ?>?place=inbox&message_ids='+selected_ids.join(',');
    if (selected_ids.length > 0)
      Smoothbox.open(sb_url);
  });
  </script>
  <br />
  <br />
  -->
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator); ?>
