<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: request-event.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<script type="text/javascript">
  var eventWidgetRequestSend = function(action, event_id, notification_id, rsvp)
  {
    var url;
    if( action == 'accept' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'accept'), 'event_extended', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'reject'), 'event_extended', true) ?>';
    }
    else
    {
      return false;
    }

    sm4.core.request.send({
      type: "POST", 
      dataType: "json", 
      url : url,
      data: {
        'event_id' : event_id,
        'format':'json',
        'rsvp' : rsvp
      },
      success: function( responseJSON, textStatus, jqXHR) {
        if( !responseJSON.status )
        {
          $.mobile.activePage.find('#event-widget-request-' + notification_id).html(responseJSON.error);
        }
        else
        {
          $.mobile.activePage.find('#event-widget-request-' + notification_id).html(responseJSON.message);
        }
        $.mobile.activePage.find('#event-widget-request-' + notification_id).removeClass('ui-li-has-thumb');
        $.mobile.activePage.find('#event-widget-request-' + notification_id).closest('ul').listview().listview('refresh');
        $.mobile.activePage.find('#event-widget-request-' + notification_id).find('a').addClass('ui-link');
      }
    });
  }
</script>

<li id="event-widget-request-<?php echo $this->notification->notification_id ?>">
  <div class="ui-btn">
    <?php echo $this->itemPhoto($this->notification->getSubject(), 'thumb.icon') ?>
    <h3>
      <?php echo $this->translate('%1$s has invited you to the event %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
    </h3>
    <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
     <p>
      <a class="request_btn" href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 2)'>
         <?php echo $this->translate('Attending'); ?>
      </a>
      <a class="request_btn" href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 1)'>
        <?php echo $this->translate('Maybe Attending'); ?>
      </a>
      <a class="request_btn ignore_btn" href="javascript:void(0);" onclick='eventWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('Ignore Request'); ?>
      </a>
    </p>
    <?php else : ?>
      <p class="sm-ui-lists-action">
      <a href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 2)'>
        <strong><?php echo $this->translate('Attending'); ?></strong>
      </a>- 
      <a href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 1)'>
        <strong><?php echo $this->translate('Maybe Attending'); ?></strong>
      </a>
      <?php echo $this->translate('or'); ?>
      <a href="javascript:void(0);" onclick='eventWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('Ignore Request'); ?>
      </a>
    </p>
    <?php endif?>
  </div>
</li>