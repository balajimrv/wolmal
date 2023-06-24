<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitestoreproduct_video_sitestoreproduct')
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
  
<?php if($this->showContent): ?>

  <?php if ($this->allowed_upload_video): ?>
    <div class="seaocore_add clear">
      <?php if($this->type_video):?>
        <a href='<?php echo $this->url(array('action' => 'index', 'product_id' => $this->sitestoreproduct->product_id, 'content_id' => $this->identity), "sitestoreproduct_video_upload", true) ?>'  class='buttonlink icon_sitestoreproducts_video_new'><?php echo $this->translate('Add Video'); ?></a>
      <?php else:?>
        <?php echo $this->htmlLink(array('route' => "sitestoreproduct_video_create", 'product_id' => $this->sitestoreproduct->product_id,'content_id' => $this->identity), $this->translate('Add Video'), array('class' => 'buttonlink icon_sitestoreproducts_video_new')) ?>
      <?php endif;?>

        <?php if ($this->can_edit && count($this->paginator) > 0): ?>
         <a href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_videospecific", true) ?>'  class='buttonlink seaocore_icon_edit'><?php echo $this->translate('Edit Videos'); ?></a>
        <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php  if(count($this->paginator) > 0):?>
    <ul class="sr_sitestoreproduct_profile_videos">
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <?php $videoEmbedded=null;?>
          <div class="sitestore_video_thumb_wrapper">
            <?php if( $item->duration ): ?>
              <span class="sitestore_video_length">
                <?php
                  if( $item->duration >= 3600 ) {
                    $duration = gmdate("H:i:s", $item->duration);
                  } else {
                    $duration = gmdate("i:s", $item->duration);
                  }
                  echo $duration;
                ?>
              </span>
            <?php endif ?>
            <?php
              if( $item->photo_id ) {
                echo $this->htmlLink($item->getHref(array('content_id' => $this->identity)), $this->itemPhoto($item, 'thumb.normal'));
              } else {
                echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
              }
              ?>
          </div>
          <div class="sr_sitestoreproduct_profile_video_info o_hidden clr">
            <div class="sr_sitestoreproduct_profile_video_title">
              <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->title_truncation), array('class' => 'video_title')) ?>
            </div>
            <div class="sr_sitestoreproduct_profile_video_options clr">
              <?php if (($this->can_edit || ($this->viewer_id) == ($item->owner_id))): ?>
                <?php if (!$this->type_video): ?>
                  <a href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id,'video_id' => $item->video_id,'tab' => $this->identity), "sitestoreproduct_video_edit", true) ?>' title="<?php echo $this->translate('Edit Video'); ?>"><i class="sr_sitestoreproduct_icon seaocore_icon_edit"></i></a>
                <?php elseif($this->can_edit):?>
                  <?php echo $this->htmlLink(Array('action' => 'edit', 'route' => "sitestoreproduct_videospecific", 'product_id' => $this->sitestoreproduct->getIdentity(),'video_id' => $item->video_id), "<i class='sr_sitestoreproduct_icon seaocore_icon_edit'></i>", array('title'=>$this->translate("Edit Video"))); ?>
                <?php endif; ?>
              <?php endif; ?>

              <?php if (($this->can_edit || ($this->viewer_id) == ($item->owner_id))): ?>
                <?php if($this->type_video):?>
                <?php echo $this->htmlLink(Array('action' => 'delete', 'route' => "sitestoreproduct_videospecific", 'product_id' => $this->sitestoreproduct->getIdentity(),'video_id' => $item->video_id), "<i class='sr_sitestoreproduct_icon seaocore_icon_delete'></i>", array('class' => 'smoothbox','title'=>$this->translate("Delete Video"))); ?>
                <?php else: ?>  
                 <?php echo $this->htmlLink(Array('route' => "sitestoreproduct_video_delete", 'product_id' => $this->sitestoreproduct->getIdentity(),'video_id' => $item->video_id,'format'=>'smoothbox'), "<i class='sr_sitestoreproduct_icon seaocore_icon_delete'></i>", array('class' => 'smoothbox','title'=>$this->translate("Delete Video"))); ?>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>	
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else:?>
    <?php if ($this->allowed_upload_video): ?>
      <div class="tip">
        <span>    
          <?php if($this->type_video):?>
            <?php $url = $this->url(array('action' => 'index', 'product_id' => $this->sitestoreproduct->product_id, 'content_id' => $this->identity), "sitestoreproduct_video_upload", true);?>
            <?php echo $this->translate('You have not added any video in your product. %1sClick here%2s to add your first video.', "<a href='$url'>","</a>"); ?>
          <?php else:?>
          <?php $url = $this->url(array('product_id' => $this->sitestoreproduct->product_id,'content_id' => $this->identity), "sitestoreproduct_video_create", true);?>
            <?php echo $this->translate('There are currently no videos for this product. Adding videos for this product will enable you to showcase it better. %1sClick here%2s to add your first video.', "<a href='$url'>","</a>"); ?>
          <?php endif;?>
        </span>
      </div>
      <br />
		<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('There are currently no videos for this product.');?>
				</span>
			</div>
    <?php endif;?>
  <?php endif; ?>

  <div>
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
      <div id="user_group_members_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array( 'onclick' => 'paginateSitestoreproductVideo(sitestoreproductVideoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
      </div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
      <div id="user_group_members_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array( 'onclick' => 'paginateSitestoreproductVideo(sitestoreproductVideoPage + 1)', 'class' => 'buttonlink_right icon_next'));?>
      </div>
    <?php endif; ?>
  </div>

  <a id="sitestoreproduct_video_anchor" style="position:absolute;"></a>

  <script type="text/javascript">
    var sitestoreproductVideoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    var paginateSitestoreproductVideo = function(page) {
      var params = {
          requestParams :<?php echo json_encode($this->params) ?>,
          responseContainer :$$('.layout_sitestoreproduct_video_sitestoreproduct')
        }
        params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
        params.requestParams.page = page;
        en4.sitestoreproduct.ajaxTab.sendReq(params);
    }

    en4.core.runonce.add(function() {
      if(en4.sitevideoview){
        en4.sitevideoview.attachClickEvent(Array('video_title','item_photo_sitestoreproduct_video','item_photo_video'));
      }
    });
  </script>
<?php endif; ?>