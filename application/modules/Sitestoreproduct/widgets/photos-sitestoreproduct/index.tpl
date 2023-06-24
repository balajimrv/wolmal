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
      responseContainer :$$('.layout_sitestoreproduct_photos_sitestoreproduct')
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>

<?php if($this->showContent): ?>
  <a id="sitestoreproduct_photo_anchor" class="pabsolute"></a>
  <script type="text/javascript">
    var sitestoreproductPhotoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    var paginateSitestoreproductPhoto = function(page) {
      var params = {
          requestParams :<?php echo json_encode($this->params) ?>,
          responseContainer :$$('.layout_sitestoreproduct_photos_sitestoreproduct')
        }
        params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
        params.requestParams.page = page;
        en4.sitestoreproduct.ajaxTab.sendReq(params);

    }
  </script>
  
  <?php if( $this->can_edit ): ?>
    <script type="text/javascript">
      var SortablesInstance;

      en4.core.runonce.add(function() {
        $$('.thumbs_nocaptions > li').addClass('sortable');
        SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
          clone: true,
          constrain: true,
          //handle: 'span',
          onComplete: function(e) {
            var ids = [];
            $$('.thumbs_nocaptions > li').each(function(el) {
              ids.push(el.get('id').match(/\d+/)[0]);
            });
            //console.log(ids);

            // Send request
            var url = en4.core.baseUrl+'sitestoreproduct/album/order';
            var request = new Request.JSON({
              'url' : url,
              'data' : {
                format : 'json',
                order : ids,
                'subject' : en4.core.subject.guid
              }
            });
            request.send();
          }
        });
      });
    </script>
  <?php endif ?>
    
  <?php if ($this->total_images): ?>
    <?php if ($this->allowed_upload_photo): ?>
      <div class="seaocore_add">
        <a href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id,'content_id' => $this->identity), "sitestoreproduct_photoalbumupload", true) ?>'  class='buttonlink icon_sitestoreproducts_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
        <?php if ($this->can_edit): ?>
        <a href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_albumspecific", true) ?>'  class='buttonlink seaocore_icon_edit'><?php echo $this->translate('Edit Photos'); ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <ul class="sr_sitestoreproduct_thumbs thumbs_nocaptions">
      <?php foreach ($this->paginator as $image): ?>
        <li id="thumbs-photo-<?php echo $image->photo_id ?>">
          <a href="<?php echo $this->url(array('product_id' => $image->product_id, 'photo_id' => $image->photo_id), "sitestoreproduct_image_specific") ?>" <?php if(SEA_LIST_LIGHTBOX) :?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('product_id' => $image->album_id, 'photo_id' => $image->photo_id), "sitestoreproduct_image_specific") ?>");return false;' <?php endif;?> class="thumbs_photo" title="<?php echo $image->title ?>">
            <span style="background-image: url(<?php echo $image->getPhotoUrl('thumb.large'); ?>);"></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <?php if ($this->allowed_upload_photo): ?>
      <div class="seaocore_add">
        <a href='<?php echo $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_photoalbumupload", true) ?>'  class='buttonlink icon_sitestoreproducts_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
      </div>
      <div class="tip">
        <span>
          <?php $url = $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_photoalbumupload", true);?>
          <?php echo $this->translate('You have not added any photo in your product. %s to add your first photo.', "<a href='$url'>Click here</a>");?>
        </span>
			</div>
		<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('There are currently no photos for this product.');?>
				</span>
			</div>
     <?php endif;?>
  <?php endif; ?>
  <?php if ($this->paginator->count() > 1): ?>
     <div >
      <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
         <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSitestoreproductPhoto(sitestoreproductPhotoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
          </div>
      <?php endif; ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSitestoreproductPhoto(sitestoreproductPhotoPage + 1)','class' => 'buttonlink_right icon_next'));?>
          </div>
        <?php endif; ?>
     </div>
  <?php endif; ?>
<?php endif; ?>