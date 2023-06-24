
<?php if (empty($this->viewmore)): ?>
  <div class="sesbasic_items_listing_popup">
    <div class="sesbasic_items_listing_header">
         <?php echo $this->type == "bank" ? "Amount Transfer to Bank" : "Amount Redeemed"; ?>
      <a class="fa fa-close" href="javascript:;" onclick='smoothboxclose();' title="<?php echo $this->translate('Close') ?>"></a>
    </div>
    <div class="sesbasic_items_listing_cont" id="like_results">
    <table style="width:100%;">
    <thead>
      <th style="width:50%; text-align:center;">Amount</th>
      <th style="width:50%; text-align:center;">Date</th>
    </thead>
<?php endif; ?>

    <?php if (count($this->paginator) > 0) : ?>
      <?php foreach ($this->paginator as $item): ?>
  	<tr>
      <td style="width:50%; text-align:center;"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->total); ?></td>
      <td style="width:50%; text-align:center;"><?php echo $item->creation_date; ?></td>
    </tr>
    <tbody id="like_results">
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
  </tbody>
      </table>
<?php endif; ?>

<script type="text/javascript">

  function viewMore() {
    
    if ($('view_more'))
    $('view_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>"; 
      
    document.getElementById('view_more').style.display = 'none';
    document.getElementById('loading_image').style.display = '';
  
    var id = '<?php echo $this->contest_id; ?>';
    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sescustomize/index/get-transfer/month/<?php echo $this->month;?>/type/<?php echo $this->type;?>' ,
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
<script type="text/javascript">
  function smoothboxclose() {
    parent.Smoothbox.close();
  }
</script>