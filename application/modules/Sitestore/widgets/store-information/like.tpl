<?php
$this->resource_id = $store->getIdentity();
$this->resource_type = $store->getType();
?>

<script type="text/javascript">
  //	var seaocore_content_type = '<?php // echo $this->resource_type;   ?>';
  var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';
    
  function sitestore_info_content_create_like( resource_id, resource_type, content_type ) {
    if($(content_type + '_like_'+ resource_id)) {
      var like_id = $(content_type + '_like_'+ resource_id).value
    }
    var request = new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/like/like',
      data : {
        format : 'json',
        'resource_id' : resource_id,
        'resource_type' : resource_type,	
        'like_id' : like_id
      }
    });
    request.send();
    return request;
  }
    
  function sitestore_info_content_type_likes(resource_id, resource_type) {
    content_type_undefined = 0;
    var content_type = resource_type;
	
    // SENDING REQUEST TO AJAX
    var request = sitestore_info_content_create_like(resource_id, resource_type,content_type);
	
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if (content_type_undefined == 0) {
        if(responseJSON.like_id )	{
          if($(content_type+'_like_'+ resource_id))
            $(content_type+'_like_'+ resource_id).value = responseJSON.like_id;
          if($(content_type+'_most_likes_'+ resource_id))
            $(content_type+'_most_likes_'+ resource_id).style.display = 'none';
          if($(content_type+'_unlikes_'+ resource_id))
            $(content_type+'_unlikes_'+ resource_id).style.display = 'inline-block';
          if($(content_type+'_num_of_like_'+ resource_id)) {
            $(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
          }
        }	else	{
          if($(content_type+'_like_'+ resource_id))
            $(content_type+'_like_'+ resource_id).value = 0;
          if($(content_type+'_most_likes_'+ resource_id))
            $(content_type+'_most_likes_'+ resource_id).style.display = 'inline-block';
          if($(content_type+'_unlikes_'+ resource_id))
            $(content_type+'_unlikes_'+ resource_id).style.display = 'none';
          if($(content_type+'_num_of_like_'+ resource_id)) {
            $(content_type + '_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
          }
        }
      }
    });
  }
</script>

<?php if (!empty($this->viewer_id)): ?>
  <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
  <div class="seaocore_like_button" id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id; ?>" style ='display:<?php echo $hasLike ? "inline-block" : "none" ?>' >
    <a href = "javascript:void(0);" onclick = "sitestore_info_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
      <i class="seaocore_like_thumbdown_icon"></i>
      <span><?php echo $this->translate('Unlike') ?></span>
    </a>
  </div>
  <div class="seaocore_like_button" id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "inline-block" : "none" ?>'>
    <a href = "javascript:void(0);" onclick = "sitestore_info_content_type_likes('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
      <i class="seaocore_like_thumbup_icon"></i>
      <span><?php echo $this->translate('Like') ?></span>
    </a>
  </div>
  <input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
<?php endif; ?>