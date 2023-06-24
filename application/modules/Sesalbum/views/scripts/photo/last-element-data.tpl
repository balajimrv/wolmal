<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: last-element-data.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="ses_ml_more_popup_container sesbasic_clearfix">
  	<div class="ses_ml_more_popup_lb">
    	<div class="ses_ml_more_popup_bh">
      	<i class="fa fa-file-image-o"></i>
      	<span><?php echo $this->translate("Popular Albums"); ?></span>
      </div>
      <div class="ses_ml_more_popup_bc sesbasic_clearfix">
      <?php  $albumItems = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbums(array('limit_data'=>6,'order'=>'view_count DESC')); 
        	foreach($albumItems as $albumItem){ ?>
          <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($albumItem); ?>
      	<div class="ses_ml_more_popup_a_list sesbasic_clearfix">
        	<a class="ses-image-viewer" href="<?php echo Engine_Api::_()->sesalbum()->getHref($albumItem->getIdentity(),$albumItem->album_id); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $albumItem->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>','change')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($albumItem->photo_id,$albumItem->album_id); ?>">
        		<span class="ses_ml_more_popup_a_list_img" style="background-image:url(<?php echo $albumItem->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            <span class="ses_ml_more_popup_a_list_title">
            	  <?php echo $this->string()->truncate($albumItem->getTitle(), 30) ; ?>
              <span class="ses_ml_more_popup_a_list_owner"><?php echo $this->translate('by').' '.$albumItem->getOwner()->getTitle(); ?></span>
             </span>
          </a>
        </div>
         <?php } ?>
      </div>
    </div>
    <div class="ses_ml_more_popup_rb">
    	<div class="ses_ml_more_popup_bh">
      	<i class="fa fa-picture-o"></i>
        <span><?php echo $this->translate("Popular Photos"); ?></span>
      </div>
      <div class="ses_ml_more_popup_bc sesbasic_clearfix">
      	<?php  $photoItems = Engine_Api::_()->getDbTable('photos', 'sesalbum')->getPhotoSelect(array('limit_data'=>9,'order'=>'view_count DESC')); 
        			 foreach($photoItems as $photoItem){ ?>
          <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photoItem); ?>
						<a onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photoItem->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>','change')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photoItem->getIdentity(),$photoItem->album_id); ?>" class="ses_ml_more_popup_photo_list ses-image-viewer"><span style="background-image:url(<?php echo $photoItem->getPhotoUrl('thumb.normalmain'); ?>);"></span></a>
        <?php } ?>
      </div>
    </div>
  </div>