<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore));
    endif;
    ?>
	<div class="clr">
		<div class="global_form">
			<div>
				<div>

					<h3><?php echo $this->translate("Edit Product Photos"); ?></h3>
					<p class="form-description"><?php echo $this->translate("Edit and manage the photos of your product below."); ?>
					<?php if($this->slideShowEnanle):?>
				    <br />
            <?php echo $this->translate("An attractive Slideshow will be displayed on your Product Profile page. Below, you can choose the photos to be displayed in that slideshow by using the 'Show in Slideshow' option."); ?>
            <?php if($this->enableVideoPlugin && $this->allowed_upload_video):?>
							<?php echo $this->translate("You can also choose the photo snapshot pic for the video displayed in the slideshow by using 'Make Video Snapshot' option."); ?>
              <br />
              <b><?php echo $this->translate("Note: ");?></b><?php echo $this->translate("You can select the video to be displayed in the Slideshow from the 'Videos' section of this Dashboard."); ?>
            <?php endif; ?>
						  
					<?php endif; ?></p>
	
					<div class="clr">
						<?php echo $this->htmlLink(array('route' => "sitestoreproduct_photoalbumupload",'album_id' => $this->album_id, 'product_id' => $this->product_id), $this->translate('Add New Photos'), array('class' => 'buttonlink icon_photos_new')) ?>
				  </div>

					<?php if( $this->paginator->count() > 0 ): ?>
						<?php echo $this->paginationControl($this->paginator); ?>
					<?php endif; ?>

					<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
						<?php echo $this->form->album_id; ?>
						<ul class='sr_sitestoreproduct_edit_media' id="photo">
							<?php if(!empty($this->count)): ?>
								<?php foreach ($this->paginator as $photo):?>
									<li>
										<div class="sr_sitestoreproduct_edit_media_thumb"> <?php echo $this->itemPhoto($photo, 'thumb.normal') ?> </div>
				            <div class="sr_sitestoreproduct_edit_media_info">
											<?php
												$key = $photo->getGuid();
												echo $this->form->getSubForm($key)->render($this);
											?>
				              <div class='sr_sitestoreproduct_edit_media_options'>
				                <div class="sr_sitestoreproduct_edit_media_options_check">
				                  <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->sitestoreproduct->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
				                </div>
				                <div class="sr_sitestoreproduct_edit_media_options_label">
				                  <label for="main_photo_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Main Photo'); ?></label>
				                </div>
				             	</div>
				              <?php if($this->enableVideoPlugin && $this->allowed_upload_video): ?>
				              <div class="sr_sitestoreproduct_edit_media_options" class='video_snapshot_id-wrapper'>              
				                <div class="sr_sitestoreproduct_edit_media_options_check">
				                  <input id="video_snapshot_id_<?php echo $photo->photo_id ?>" type="radio" name="video_snapshot_id" value="<?php echo $photo->photo_id ?>" <?php if ($this->sitestoreproduct->video_snapshot_id == $photo->photo_id): ?> checked="checked"<?php endif; ?> />
				                </div>
				                <div class="sr_sitestoreproduct_edit_media_options_label">
				                  <label for="video_snapshot_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Make Video Snapshot');  ?></label>
				                </div>
				              </div>
				              <?php endif; ?>
				            </div>
				          </li>
				        <?php endforeach; ?>
				      <?php else:?><br />
								<div class="tip">
									<span>
                   <?php $url = $this->url(array('product_id' => $this->product_id), 'sitestoreproduct_photoalbumupload', true);?>
										<?php echo $this->translate("There are currently no photos in this product. %s to add photos now!", "<a href='$url'>Click here</a>"); ?>
									</span>
								</div>
							<?php endif;?>
						</ul>
						<?php if(!empty($this->count)): ?>
							<?php echo $this->form->submit->render(); ?>
						<?php endif;?>
					</form>
					<?php if( $this->paginator->count() > 0 ): ?>
						<br />
						<?php echo $this->paginationControl($this->paginator); ?>
					<?php endif; ?>
				</div>		
			</div>
		</div>
	</div>			
</div>
</div>