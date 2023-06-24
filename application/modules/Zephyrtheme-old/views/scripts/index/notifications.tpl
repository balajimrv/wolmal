<?php foreach( $this->notifications as $notification ): ?>
<?php $user = Engine_Api::_()->getItem('user', $notification->subject_id);?>
<li onclick="redirectPage(event);" class="pulldown_item<?php if( !$notification->read ): ?> pulldown_content_item_unread<?php endif; ?>"  value="<?php echo $notification->getIdentity();?>">
  <div class="pulldown_item_photo">
    <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
  </div>
  <div class="pulldown_item_content">
    <span class="pulldown_item_content_title">
      <?php echo $notification->__toString() ?>
    </span>
    <span class="pulldown_item_content_date">
      <?php echo $this->timestamp(strtotime($notification->date)) ?>
    </span>
  </div>
</li>
<?php endforeach; ?>


<script type="text/javascript">
function redirectPage(event) {
    event.stopPropagation();
    var current_link = event.target;

    if(current_link.get('href') == null && $(current_link).get('tag')!='img') {
        var list_item = $(current_link).getParent('li'),
            doc = document.createElement('html');

        doc.innerHTML = list_item.getElements('div:last-child')[0].outerHTML;

        var links = doc.getElementsByTagName('a');
        var url = links[links.length - 1].get('href');

        en4.core.request.send(new Request.JSON({
            url : en4.core.baseUrl + 'zephyrtheme/index/markread',
            data : {
                format     : 'json',
                'notificationid' : list_item.get('value')
            },
            onSuccess : function() {
                window.location = url;
            }
        }));
    }
}
</script>