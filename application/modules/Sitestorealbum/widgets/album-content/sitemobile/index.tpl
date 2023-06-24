<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php if (empty($this->is_ajax)) : ?>
<?php 
$albumTitle = '' != trim($this->album->getTitle()) ? $this->album->getTitle() : $this->translate('Untitled');
$breadcrumb = array(
    array("href"=>$this->sitestore->getHref(),"title"=>$this->sitestore->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitestore->getHref(array('tab' => $this->tab_selected_id)),"title" => "Albums","icon" => "arrow-r"),
    array("title"=> $albumTitle,"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist")
 );
echo $this->breadcrumb($breadcrumb);?>
<?php endif; ?>

 <?php if (empty($this->is_ajax)) : ?>
   <?php if (!empty($this->total_images)): ?>
      <div class="sitestore_album_box" id="sitestorealbum_content">
        <ul class="thumbs thumbs_nocaptions" id="thumbs_nocaptions">
   <?php endif; ?>
 <?php endif; ?>
                <?php foreach ($this->photos as $photo):  ?> 
                  <li id="thumbs-photo-<?php echo $photo->photo_id ?>">	                   
                    <a href="<?php echo $photo->getHref(); ?>"  class="thumbs_photo">               
                      <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                    </a>
                  </li>
                <?php endforeach; ?>

  <?php if (!empty($this->total_images)): ?>
    <?php if (empty($this->is_ajax)) : ?>
        </ul>
        <div class="feed_viewmore" id="view_more" onclick="viewMorePhoto()" >
          <a href="javascript:void(0);" id="feed_viewmore_link" class="ui-btn-default icon_viewmore" ><?php echo $this->translate('View More')?></a>
        </div>

   <div class="feeds_loading" id="loding_image" style="display: none;">
    <i class="ui-icon-spinner ui-icon icon-spin"></i>
   </div>
      </div>
  <?php endif; ?>
<?php endif; ?>
  <?php if (empty($this->is_ajax)) : ?>
    <?php if (empty($this->total_images)): ?>
                    <div class="tip">
                      <span>
                  <?php echo $this->translate('There are no photos in this store album.') ?>
                      </span>
                    </div>
     <?php endif; ?>
  <?php endif; ?>

  
  
<script type="text/javascript">
  function getNextStore(){
    return <?php echo sprintf('%d', $this->currentStoreNumbers + 1) ?>
  }

  sm4.core.runonce.add(function() { 
    hideViewMoreLink();
  });
  
      function viewMorePhoto()
      {
        $('#view_more').css('display','none');
        $('#loding_image').css('display','');
        $.ajax({
          type: "POST", 
          dataType: "html",
          'url' : sm4.core.baseUrl + 'core/widget/index/mod/sitestorealbum/name/album-content',
          'data' : {
            format : 'html',
            isajax : 1,
            itemCountPerStore : '<?php echo $this->photos_per_store; ?>',
            stores: getNextStore(),
            'store_id': '<?php echo $this->sitestore->store_id; ?>',
            'album_id': '<?php echo $this->album_id; ?>',
            'slug': '<?php echo $this->album->getSlug(); ?>',
            'tab': '<?php echo $this->tab_selected_id; ?>'
          },
          success : function(responseHTML) {
            $.mobile.activePage.find('#sitestorealbum_content').find('.thumbs_nocaptions').append(responseHTML);
            $.mobile.activePage.find('#loding_image').css('display','none');
              hideViewMoreLink();
              }
            });
          sm4.core.runonce.trigger();
          sm4.core.refreshStore();

            return false;

          } 
          
          function hideViewMoreLink(){
            $('#view_more').css('display','<?php echo ( $this->maxstore == $this->currentStoreNumbers || $this->total_images == 0 ? 'none' : '' ) ?>');
          }
</script>