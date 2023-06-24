<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sr_sitestoreproduct_dashboard_content">
  <?php
    if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ):
      echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore' => $this->sitestore));
    endif;
    ?>
	<div class="sr_sitestoreproduct_create_list_form">
		<div class="global_form">
			<div>
				<div>
				<h3> <?php echo $this->translate("Edit Product Videos"); ?></h3>
				<p class="form-description"><?php echo $this->translate("Edit and manage the videos of your product below."); ?>
					<?php if($this->slideShowEnanle):?>
            <br />
						<?php echo $this->translate("An attractive Slideshow will be displayed on your Product Profile page. Below, you can select a video to be displayed in that slideshow by using the 'Show in Slideshow' option."); ?>
            <br />
            <b><?php echo $this->translate("Note: ")?></b><?php echo $this->translate("You can choose a snapshot pic for selected video by visiting the 'Photos' section of this Dashboard.")?>
					<?php endif; ?>
        </p>
				
				<div class="clr">
				  <?php if($this->type_video):?>
						<?php echo $this->htmlLink(array('route' => "sitestoreproduct_video_upload", 'action' => 'index', 'product_id' => $this->sitestoreproduct->product_id), $this->translate('Add New Video'), array('class' => 'buttonlink icon_sitestoreproducts_video_new')) ?>
				  <?php else:?>
				  <?php echo $this->htmlLink(array('route' => "sitestoreproduct_video_create", 'product_id' => $this->sitestoreproduct->product_id), $this->translate('Add New Video'), array('class' => 'buttonlink icon_sitestoreproducts_video_new')) ?>
				  <?php endif;?>
				</div>
				
				<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="">
					<div>
						<div>
							<ul class='sr_sitestoreproduct_edit_media' id="video">
								<?php if(!empty($this->count)): ?>
									<?php foreach ($this->videos as $item): ?>
										<li>
											<div class="sitestore_video_thumb_wrapper">
												<?php if ($item->duration): ?>
													<span class="sitestore_video_length">
														<?php
															if ($item->duration > 360)
																$duration = gmdate("H:i:s", $item->duration); else
																$duration = gmdate("i:s", $item->duration);
															if ($duration[0] == '0')
																$duration = substr($duration, 1); echo $duration;
														?>
													</span>
					              <?php endif; ?>
					              <?php 
					                if ($item->photo_id)
					                  echo $this->htmlLink($item->getHref(array('content_id' => $this->content_id)), $this->itemPhoto($item, 'thumb.normal'), array());
					                else
					                  echo '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
					              ?>
				            	</div>
					            <div class="sr_sitestoreproduct_edit_media_info">
					              <?php $key= $item->getGuid();
						              echo $this->form->getSubForm($key)->render($this);
					              ?>
				                <?php if($this->slideShowEnanle):?>
													<div class="sr_sitestoreproduct_edit_media_options">
														<div class="sr_sitestoreproduct_edit_media_options_check">
															<?php if($this->type_video):?>
																<?php $cover = 'corevideo_cover';?>
															<?php else:?>
																<?php $cover = 'reviewvideo_cover';?>
															<?php endif;?>
															<input id="show_slideshow_id_<?php echo $item->video_id ?>" type="radio" name="<?php echo $cover;?>" value="<?php echo $item->video_id ?>" <?php if ($this->main_video_id == $item->video_id): ?> checked="checked"<?php endif; ?> />
														</div>
														<div class="sr_sitestoreproduct_edit_media_options_label">
															<label for="show_slideshow_id_<?php echo $item->video_id ?>" ><?php echo $this->translate('Show in Slideshow'); ?></label>
														</div>
													</div>
				                <?php endif;?>
					            </div>
					          </li>
				          <?php endforeach; ?>
								<?php else:?><br />
									<div class="tip">
										<span>
											<?php if($this->type_video):?>
												<?php $url = $this->url(array('action' => 'index', 'product_id' => $this->sitestoreproduct->product_id, 'content_id' => $this->identity), "sitestoreproduct_video_upload", true);?>
												<?php echo $this->translate('You have not added any video in your product. %s to add your first video.', "<a href='$url'>Click here</a>"); ?>
											<?php else:?>
											<?php $url = $this->url(array('product_id' => $this->sitestoreproduct->product_id,'content_id' => $this->identity), "sitestoreproduct_video_create", true);?>
												<?php echo $this->translate('There are currently no videos for this product. Adding videos for this product will enable you to showcase it better. %s to add your first video.', "<a href='$url'>Click here</a>"); ?>
											<?php endif;?>
										</span>
									</div>
								<?php endif;?>
							</ul>
				      <?php if(!empty($this->count)): ?>
								<?php echo $this->form->button ?>
							<?php endif;?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>				