<?php ?>
<script type="text/javascript">

  function viewMore() {
    
    if ($('view_more'))
    $('view_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>"; 
      
    document.getElementById('view_more').style.display = 'none';
    document.getElementById('loading_image').style.display = '';
  
    var id = '<?php echo $this->contest_id; ?>';
    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sescustomize/index/get-users/month/<?php echo $this->month;?>/type/<?php echo $this->type;?>/year/<?php echo $this->year;?> ,
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('like_results').innerHTML = document.getElementById('like_results').innerHTML + responseHTML;
        document.getElementById('view_more').destroy();
        document.getElementById('loading_image').style.display = 'none';
      }
    })).send();
    return false;
  }
</script>

<?php if (empty($this->viewmore)): ?>
  <div class="sesbasic_items_listing_popup">
    <div class="sesbasic_items_listing_header">
         <?php echo $this->translate('People By You Gained Bridges') ?>
      <a class="fa fa-close" href="javascript:;" onclick='smoothboxclose();' title="<?php echo $this->translate('Close') ?>"></a>
    </div>
    <div class="sesbasic_items_listing_cont" id="like_results">
<?php endif; ?>

    <?php if (count($this->paginator) > 0) : ?>
      <?php foreach ($this->paginator as $value): ?>
        <?php if(!$value->user_id) continue;?>
        <?php $user = Engine_Api::_()->getItem('user', $value->user_id); ?>
        <div class="item_list">
          <div class="item_list_thumb">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(), 'target' => '_parent')); ?>
          </div>
          <div class="item_list_info">
            <div class="item_list_title">
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle(), 'target' => '_parent')); ?><br />
              <span><?php if($this->type == 'cb'):?><?php echo 'Business Bridges(BB): '.$value->buyer_bb;?><?php else:?><?php echo  'Collection Bridges(CB): '.$value->buyer_cb;?><?php endif;?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?> 
      <?php endif; ?>     
      
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1 && empty($this->viewmore)): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="sesbasic_view_more sesbasic_load_btn" id="view_more" onclick="viewMore();" >
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="loading_image" style="display: none;">
         <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span>
          <?php echo $this->translate("Loading ...") ?>
        </div>
  <?php endif; ?>
     </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
  function smoothboxclose() {
    parent.Smoothbox.close();
  }
</script>