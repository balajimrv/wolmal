<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2013-05-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$this->headScriptSM()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/tagger.js');
$this->headScriptSM()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestorealbum/externals/scripts/sitemobile/core.js');
?>
<script type="text/javascript"> 
  
  
  

  sm4.core.runonce.add(function(){
    $('#tagit_<?php echo $this->subject()->getGuid() ?>').hide();
    Tagger.tagger.getTagList('<?php echo $this->subject()->getGuid() ?>',<?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,<?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>);
    
    sm4.core.Module.autoCompleter.attach("tags_<?php echo $this->subject()->getGuid() ?>", '<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'photo', 'action' => 'suggest','sendNow' => true, 'includeSelf' => true, 'store_id' => $this->sitestore->store_id), 'default', true) ?>', {'singletextbox': false, 'limit':10, 'minLength': 1, 'showPhoto' : true, 'search' : 'search'}, "toValues_<?php echo $this->subject()->getGuid() ?>");
    
  }); 
</script>

<div class='sm-ui-photo-view' id='media_photo_div'><a></a>
  <a id='media_photo_next'  href='<?php echo $this->image->getNextCollectible()->getHref() ?>'>
    <?php echo $this->htmlImage($this->image->getPhotoUrl(), $this->image->getTitle(), array(
      'id' => 'media_photo', 'align' => 'center'
    )); ?>
  </a>
</div>

<div class="ui-store-content">
	<?php if ($this->album->count() > 1): ?>
    <div class="sm-ui-photo-view-nav">
      <div class="sm-ui-photo-view-prev">
        <?php echo $this->htmlLink($this->image->getPrevCollectible(), $this->translate('Prev'), array('id' => 'photo_prev')) ?>
      </div>
      <div class="sm-ui-photo-view-next">
        <?php echo $this->htmlLink($this->image->getNextCollectible(), $this->translate('Next'), array('id' => 'photo_next')) ?>
      </div>
      <div id= 'photo_navigation2' class="sm-ui-photo-view-count">
        <?php
        echo $this->translate('%1$s of %2$s', $this->locale()->toNumber($this->image->getCollectionIndex() + 1), $this->locale()->toNumber($this->album->count()))
        ?>
      </div>
    </div>
  <?php endif; ?>
	<?php if ($this->canEdit): ?> 
		<div class="albums_viewmedia_info_actions">
			<a class="buttonlink icon_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).attr('class', 'buttonlink icon_loading');sm4.sitestorealbum.rotate(<?php echo $this->image->getIdentity() ?>, 90, $(this), 'buttonlink icon_photos_rotate_ccw', sm4.core.baseUrl + 'sitestore/photo/rotate');">&nbsp;</a>
			<a class="buttonlink icon_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).attr('class', 'buttonlink icon_loading');sm4.sitestorealbum.rotate(<?php echo $this->image->getIdentity() ?>, 270, $(this), 'buttonlink icon_photos_rotate_cw', sm4.core.baseUrl + 'sitestore/photo/rotate');">&nbsp;</a>
			<a class="buttonlink icon_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).attr('class', 'buttonlink icon_loading');sm4.sitestorealbum.flip(<?php echo $this->image->getIdentity() ?>, 'horizontal', $(this), 'buttonlink icon_photos_flip_horizontal', sm4.core.baseUrl + 'sitestore/photo/flip');">&nbsp;</a>
			<a class="buttonlink icon_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).attr('class', 'buttonlink icon_loading');sm4.sitestorealbum.flip(<?php echo $this->image->getIdentity() ?>, 'vertical', $(this), 'buttonlink icon_photos_flip_vertical', sm4.core.baseUrl + 'sitestore/photo/flip');">&nbsp;</a>
		</div>
	<?php endif; ?>
	<div class="sm-ui-photo-view-info">
		<div class="sm-ui-cont-author-photo">
			<?php echo $this->htmlLink($this->subject()->getOwner(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
		</div> 
		<div class="sm-ui-cont-cont-info">
			<div class="sm-ui-cont-author-name">
				<?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>
			</div>
			<?php if ($this->image->getTitle()): ?>
				<div class="sm-ui-photo-view-photo-title">
					<strong><?php echo $this->image->getTitle(); ?></strong>
				</div>
			<?php endif; ?>
			<?php if ($this->image->getDescription()): ?>
				<div class="sm-ui-photo-view-photo-cap">
					<?php echo $this->image->getDescription() ?>
				</div>
			<?php endif; ?>
			
			<div class="sm-ui-photo-view-info-tags" id="media_tags_<?php echo $this->subject()->getGuid() ?>" class="tag_list" style="display: none;">
				<?php echo $this->translate('Tagged:') ?>
			</div>
			
			<div class="sm-ui-photo-view-info-date">
				<a href="<?php echo $this->album->getHref(); ?>"><?php echo $this->translate('%1$s', "Photos of ". $this->album->getTitle())?></a>
				- <?php echo $this->timestamp($this->image->creation_date) ?>
				
				<?php if ($this->viewer()->getIdentity() && $this->canTag): ?>
				-	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick' => 'Tagger.tagger.addTag("' . $this->subject()->getGuid() . '");')) ?>
				<?php endif; ?>
				-
				<a class ="smapp_download_photo" href="<?php echo $this->image->getPhotoUrl() ?>">
                                    <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                                        <?php echo $this->translate('Download Photo')?>
                                     <?php else:?>
                                    <?php echo $this->translate('View Full Size')?>
                                    <?php endif;?>
                                </a>
			</div>
		</div>
	</div>	
			
	<?php if ($this->canTag): ?>
		<div id="tagit_<?php echo $this->subject()->getGuid() ?>" style="display:none;">
			<div class="global_form_popup sm-ui-photo-add-tag-store">
				<div id="tags_<?php echo $this->subject()->getGuid() ?>-wrapper">
					<div id="to-label">
						<h3>
							<?php echo $this->translate('Add Tag'); ?>
						</h3>
					</div>
					<div class="form-element sm-photo-search-tag" id="tags_<?php echo $this->subject()->getGuid() ?>-element">
						<div>
							<input placeholder="<?php echo $this->translate("Start typing a name..."); ?>" type="text" data-mini="true" autocomplete="off" id="tags_<?php echo $this->subject()->getGuid() ?>" name="tags_<?php echo $this->subject()->getGuid() ?>" />
							<span role="status" aria-live="polite"></span>
						</div>
					</div>
				</div>
					<div class="form-wrapper" id="toValues-wrapper_<?php echo $this->subject()->getGuid() ?>">
						<div class="form-element" id="toValues_<?php echo $this->subject()->getGuid() ?>-element">
							<input type="hidden" id="toValues_<?php echo $this->subject()->getGuid() ?>" value=""  name="toValues_<?php echo $this->subject()->getGuid() ?>" />
						</div>
					</div>
					<div class="ui-store-content sm-photo-form-buttons">
						<a data-role="button" data-inline="true" data-theme="b" name="btnsave" data-mini="true" id="btnsave" onclick="Tagger.tagger.saveTag('<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>','<?php echo $this->subject()->getGuid() ?>',<?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,<?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>);" ><?php echo $this->translate('Save'); ?></a>
						<a data-role="button" data-inline="true" data-mini="true" name="btncancel"  id="btncancel" onclick="Tagger.tagger.cancelTag('<?php echo $this->subject()->getGuid() ?>');" ><?php echo $this->translate('Cancel'); ?></a>
						<div class="tag_loading" id="tag_loading" style="display:none;">
							<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='float:left; margin-right: 5px;' />
						</div>
					</div>
				</div>
			</div>
	<?php endif; ?>
</div>
<script type="text/javascript">
	$(document).bind( "storeinit", function( event, data ) {  
    var $store = $( event.target );   
		$store.find("#media_photo_div").on( "swipeleft swiperight",  function( event ) {
			if ( event.type === "swipeleft"  ) {
				$store.find('#photo_prev').click();
			} else if ( event.type === "swiperight" ) {
				$store.find('#photo_next').click();
			}
		});
	});
</script>