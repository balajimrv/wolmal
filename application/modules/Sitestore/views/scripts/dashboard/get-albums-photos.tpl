<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-photo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_coverphoto.css'
);
?>
<div class="seaocore_popup">
  <div class="seaocore_popup_top">
    <div class="seaocore_popup_des">
      <?php if ($this->album_id || $this->recentAdded): ?>
        <b><?php echo $this->translate("Choose Store Cover Photo") ?></b>
      <?php else: ?>
         <b><?php echo $this->translate("Select Store Album to choose Store Cover Photo") ?></b>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($this->album_id || $this->recentAdded): ?>
    <div class="seaocore_popup_options">
      <div class="seaocore_popup_options_left">
        <b><?php echo $this->album_id ? $this->translate("%s's Photos", $this->album->getTitle()) : $this->translate("Recent Store Photos"); ?></b>
      </div>
      <div class="fright"><a  class="buttonlink seaocore_profile_cover_icon_photo_view" href="<?php echo $this->url(array('action' => 'get-albums-photos', 'store_id' => $this->sitestore->store_id, 'format' => 'smoothbox'), 'sitestore_dashboard', true); ?>"><b><?php echo $this->translate("View Albums") ?></b></a></div>
    </div>
  <?php endif; ?>
  <div class="seaocore_popup_content">
    <div class="seaocore_popup_content_inner">
      <?php if ($this->album_id || $this->recentAdded): ?>
        <?php if (count($this->paginator) > 0) : ?>
					<div class="clr seaocore_choose_photos_content">
          	<ul class="thumbs">
            <?php foreach ($this->paginator as $photo): ?>
							<li> 
								<a href="<?php echo $this->url(array('action' => 'upload-cover-photo', 'store_id' => $photo->store_id, 'photo_id' => $photo->photo_id, 'format' => 'smoothbox'), 'sitestore_dashboard', true); ?>" title="<?php echo $photo->title; ?>" class="thumbs_photo">
									<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span></a>
							</li>
						<?php endforeach; ?>
          	</ul>
        	</div>
      <?php else: ?>
        <div class="tip" style="margin-top:10px;">
          <span>
            <?php echo $this->translate("There are currently no photos available.") ?>
          </span>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php if (count($this->paginator) > 0) : ?>
        <div class="clr seaocore_choose_photos_content">
          <ul class="thumbs ">
            <?php foreach ($this->paginator as $albums): ?>
              <?php if ($albums->count() < 1): continue;
              endif; ?>
              <li> 
                <?php if ($albums->photo_id != 0): ?>
                  <a href="<?php echo $this->url(array('action' => 'get-albums-photos', 'store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'format' => 'smoothbox'), 'sitestore_dashboard', true); ?>" title="<?php echo $albums->title; ?>" class="thumbs_photo">
                    <span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span></a>
                <?php else: ?>
                  <a href="<?php echo $this->url(array('action' => 'get-albums-photos', 'store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'format' => 'smoothbox'), 'sitestore_dashboard', true); ?>" class="thumbs_photo" title="<?php echo $albums->title; ?>" >
                    <span><?php echo $this->itemPhoto($albums, 'thumb.normal'); ?></span>
                  </a>
                <?php endif; ?>
                <div class="sitestore_profile_album_title">
                  <a href="<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->identity_temp), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title; ?>"><?php echo $albums->title; ?></a>
                </div>
                <!--                <div class="sitestore_profile_album_stat">
                <?php //echo $this->translate(array('%s photo', '%s photos', $albums->count()), $this->locale()->toNumber($albums->count()))  ?>
                
                                </div>-->
              </li>		      
            <?php endforeach; ?>
          </ul>
        </div>
      <?php else: ?>
        <div class="tip">
          <span>
             <?php echo $this->translate("There are currently no photos available.") ?>
          </span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<div class="popup_btm fright">
  <button href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel") ?></button>
</div>
</div>