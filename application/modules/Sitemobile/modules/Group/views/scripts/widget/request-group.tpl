<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: request-group.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<script type="text/javascript">
  var groupWidgetRequestSend = function(action, group_id, notification_id)
  {
    var url;
    if( action == 'accept' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'accept'), 'group_extended', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'reject'), 'group_extended', true) ?>';
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
        'group_id' : group_id,
        'format' : 'json'
      },
      success: function( responseJSON, textStatus, jqXHR) {
        if( !responseJSON.status )
        {
          $.mobile.activePage.find('#group-widget-request-' + notification_id).html(responseJSON.error);
        }
        else
        {
          $.mobile.activePage.find('#group-widget-request-' + notification_id).html(responseJSON.message);
        }
        $.mobile.activePage.find('#group-widget-request-' + notification_id).removeClass('ui-li-has-thumb');
        $.mobile.activePage.find('#group-widget-request-' + notification_id).closest('ul').listview().listview('refresh');
        $.mobile.activePage.find('#group-widget-request-' + notification_id).find('a').addClass('ui-link');
      }
    });
  }
</script>

<li id="group-widget-request-<?php echo $this->notification->notification_id ?>">
  <div class="ui-btn">
    <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
    <h3>
      <?php echo $this->translate('%1$s has invited you to the group %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
    </h3>
    
     <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
      <p>
        <a class="request_btn" href="javascript:void(0);" onclick='groupWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
          <?php echo $this->translate('Join Group'); ?>
        </a>
        <a class="request_btn ignore_btn" href="javascript:void(0);" onclick='groupWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
          <?php echo $this->translate('ignore request'); ?>
        </a>
      </p>
    <?php else : ?>
     <p class="sm-ui-lists-action">
      <a href="javascript:void(0);" onclick='groupWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <strong><?php echo $this->translate('Join Group'); ?></strong>
      </a>
      <?php echo $this->translate('or'); ?>
      <a href="javascript:void(0);" onclick='groupWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('ignore request'); ?>
      </a>
    </p>
    <?php endif?>
  </div>
</li>