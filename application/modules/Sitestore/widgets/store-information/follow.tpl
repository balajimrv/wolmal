<?php
$this->subject = $store;
$this->resource_id = $store->getIdentity();
$this->resource_type = $store->getType();
$this->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($this->resource_type, $this->resource_id);
?>



<script type="text/javascript">

    
  function sitestore_info_content_create_follow( resource_id, resource_type, content_type ) {
    if($(content_type + '_follow_'+ resource_id)) {
      var follow_id = $(content_type + '_follow_'+ resource_id).value
    }
    var request = new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/follow/global-follows',
      data : {
        format : 'json',
        'resource_id' : resource_id,
        'resource_type' : resource_type,	
        'follow_id' : follow_id
      }
    });
    request.send();
    return request;
  }
    
  function sitestore_info_content_type_follows(resource_id, resource_type) {

    content_type_undefined = 0;
    var content_type = resource_type;
	
    // SENDING REQUEST TO AJAX
    var request = sitestore_info_content_create_follow(resource_id, resource_type, content_type);
	
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if (content_type_undefined == 0) {
        if(responseJSON.follow_id )	{
          $(content_type+'_follow_'+ resource_id).value = responseJSON.follow_id;
          $(content_type+'_most_follows_'+ resource_id).style.display = 'none';
          $(content_type+'_unfollows_'+ resource_id).style.display = 'inline-block';

          if($(content_type+'_num_of_follow_'+ resource_id)) {
            $(content_type + '_num_of_follow_'+ resource_id).innerHTML = responseJSON.follow_count;
          }
				
          if($(content_type+'_num_of_follows_'+ resource_id)) { 
            $(content_type + '_num_of_follows_'+ resource_id).innerHTML = responseJSON.follow_count;
          }
        }	else	{
          $(content_type+'_follow_'+ resource_id).value = 0;
          $(content_type+'_most_follows_'+ resource_id).style.display = 'inline-block';
          $(content_type+'_unfollows_'+ resource_id).style.display = 'none';
				
          if($(content_type+'_num_of_follow_'+ resource_id)) {
            $(content_type + '_num_of_follow_'+ resource_id).innerHTML = responseJSON.follow_count;
          }
				
          if($(content_type+'_num_of_follows_'+ resource_id)) {
            $(content_type + '_num_of_follows_'+ resource_id).innerHTML = responseJSON.follow_count;
          }
        }
      }
    });
  }
</script>
<?php if ($this->viewer_id): ?>
  <?php $isFollow = $this->subject->follows()->isFollow($this->viewer); ?>
  <div class="seaocore_follow_button_wrap fleft button seaocore_follow_button_active" id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id; ?>" style =' display:<?php echo $isFollow ? "inline-block" : "none" ?>' >
    <a class="seaocore_follow_button seaocore_follow_button_following" href="javascript:void(0);">
      <i class="following"></i>
      <span><?php echo $this->translate('Following') ?></span>
    </a>

    <a class="seaocore_follow_button seaocore_follow_button_unfollow" href="javascript:void(0);" onclick = "sitestore_info_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
      <i class="unfollow"></i>
      <span><?php echo $this->translate('Unfollow') ?></span>
    </a>

  </div>
  <div class="seaocore_follow_button_wrap fleft" id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id; ?>" style ='display:<?php echo empty($isFollow) ? "inline-block" : "none" ?>'>
    <a class="seaocore_follow_button" href="javascript:void(0);" onclick = "sitestore_info_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
      <i class="follow"></i>
      <span><?php echo $this->translate('Follow') ?></span>
    </a>
  </div>
  <input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id; ?>" value = '<?php echo $isFollow ? $isFollow : 0; ?>' />
<!--  <div class="seaocore_follower_count fleft"  id= "<?php //echo $this->resource_type ?>_num_of_follow_<?php //echo $this->resource_id; ?>">
    <a href="javascript:void(0);" onclick="showSmoothBox('<?php //echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action' => 'get-followers', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php //echo $this->translate(array('%s follower', '%s followers', $this->follow_count), $this->locale()->toNumber($this->follow_count)); ?></a>			
  </div>-->

<?php endif; ?>
<script type="text/javascript">
  function showSmoothBox() {
    Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action' => 'get-followers', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default', true)); ?>');
  }
</script>