<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ses-imageviewer-basic.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="text/javascript">
		function initImage() {
			sesJqueryObject('#gallery-img').apImageZoom({
					cssWrapperClass: 'custom-wrapper-class'
				, autoCenter: false
				, loadingAnimation: 'throbber'
				, minZoom: 'contain'
				, maxZoom: false
				, maxZoom: 1.0
				, hammerPluginEnabled: false
				, hardwareAcceleration: false
			});
		};
</script>
<div class="ses_media_lightbox_left">
  <div class="ses_media_lightbox_item_wrapper">
    <div class="ses_media_lightbox_item">
      <div id="mainImageContainer">
        <div id="media_photo_next_ses" style="display:inline; ">
        <?php if(isset($this->imagePrivateURL)){
                $imageUrl = $this->imagePrivateURL;
                $className = 'ses-private-image';
              }else{
                $imageUrl = $this->child_item->getPhotoUrl();
                $className= '';
              }
          ?>
          <?php echo $this->htmlImage($imageUrl, $this->child_item->getTitle(), array(
                'id' => 'gallery-img',
                'class' =>$className
              )); ?>
        </div>
      </div>
    </div>
  </div>
    <?php if(isset($this->imagePrivateURL)){
          $imageUrl = $this->imagePrivateURL;
         }else
         	$imageUrl =	$this->child_item->getPhotoUrl(); 
          ?>
  <span id="image-src-sesalbum-lightbox-hidden"><?php echo $imageUrl; ?></span>
  <div class="ses_media_lightbox_nav_btns">
    <?php 
        $previousURL = $this->previousPhoto;
        if($previousURL != ''){
        	$previousURL = $previousURL->getHref();
        	if(isset($this->imagePrivateURL))
        	 $previousImageURL = $this->imagePrivateURL;
          else
           $previousImageURL = $this->previousPhoto->getPhotoUrl();
      ?>
    <a href="<?php echo $this->previousPhoto->getHref(); ?>" style="display:block" title="<?php echo $this->translate('Previous'); ?>" onclick="openLightBoxForSesPlugins('<?php echo $previousURL; ?>','<?php echo $previousImageURL; ?>');return false;" class="ses_media_lightbox_nav_btn_prev" id="nav-btn-prev"></a>
    <?php }
        $nextURL = $this->nextPhoto;
        if($nextURL != ''){	
        	$nextURL = $nextURL->getHref();
        	if(isset($this->imagePrivateURL))
        	 $nextImageURL = $this->imagePrivateURL;
          else
           $nextImageURL = $this->nextPhoto->getPhotoUrl();
       ?>
    <a href="<?php echo $this->nextPhoto->getHref(); ?>" style="display:block" title="<?php echo $this->translate('Next'); ?>" onclick="openLightBoxForSesPlugins('<?php echo $nextURL; ?>','<?php echo $nextImageURL ; ?>');return false;" class="ses_media_lightbox_nav_btn_next" id="nav-btn next" ></a>
    <?php } ?>
  </div>
  <div class="ses_media_lightbox_options">
    <div class="ses_media_lightbox_options_owner">
    	<?php $owner_id = isset($this->child_item->owner_id) ? $this->child_item->owner_id : $this->child_item->user_id; ?>
    	<?php $albumUserDetails = Engine_Api::_()->user()->getUser($owner_id); ?>  
      <?php echo $this->htmlLink($albumUserDetails->getHref(), $this->itemPhoto($albumUserDetails, 'thumb.icon'), array('class' => 'userthumb')); ?>
      <?php echo $this->htmlLink($albumUserDetails->getHref(), $albumUserDetails->getTitle()); ?>&nbsp;&nbsp;&bull;&nbsp;&nbsp;
    </div>
    <div class="ses_media_lightbox_options_name">
      <?php echo $this->translate('In %1$s',$this->parent_item->__toString()); ?>
    </div>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
     <div class="ses_media_lightbox_options_btns">
      <?php $LikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($this->child_item->getIdentity(),$this->child_item->getType()); ?>
        <a href="javascript:void(0);" data-src="albumLike" id="sesLightboxLikeUnlikeButton" class="sesbasic_icon_btn sesbasic_icon_like_btn sesalbum_othermodule_like_button nocount<?php echo $LikeStatus ? ' button_active' : '' ;  ?>"><i class="fa fa-thumbs-up"></i><span id="like_unlike_count"></span></a>
      </div>
    <?php } ?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.share',1) == 1){ ?>
      <div class="ses_media_lightbox_options_btn ses_media_lightbox_share_btn">
        <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array("action" => "share", "type" => $this->child_item->getType(), "photo_id" => $this->child_item->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>')"><?php echo $this->translate('Share'); ?></a>
      </div>
    <?php } ?>
    <div class="ses_media_lightbox_options_btn ses_media_lightbox_more_btn">
      <div class="ses_media_lightbox_options_box">
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && $this->canDelete){ ?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.delete',1) == 1){ ?>
            <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('controller' => 'photo', 'action' => 'delete-ses', 'photo_id' => $this->child_item->getIdentity(),'item_type'=>$this->child_item->getType(),'module'=>'sesalbum'),'default',true); ?>')"><?php echo $this->translate('Delete'); ?></a>
          <?php } ?>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.report',1) == 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
          <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->child_item->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>')"><?php echo $this->translate('Report'); ?></a>
        <?php } ?>
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.profilepic',1) == 1){ ?>
          <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->child_item->getGuid(), 'format' => 'smoothbox'),'user_extended',true); ?>')"><?php echo $this->translate('Make Profile Photo'); ?></a>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.download',1) == 1){ ?>
          <a class="ses-album-photo-download" href="<?php echo $this->url(array('module' =>'sesalbum','controller' => 'photo', 'action' => 'download')).'?filePath='.urlencode($this->child_item->getPhotoUrl()) . '&file_id=' . $this->child_item->getIdentity()  ;?>"><?php echo $this->translate('Download'); ?></a>
        <?php } ?>
        <a href="javascript:;" onclick="slideShow()"><?php echo $this->translate('Slideshow'); ?></a>
      </div>
      <a href="javascript:void(0);"><?php echo $this->translate('Options'); ?></a>
    </div>
  </div>
  <div class="ses_media_lightbox_fullscreen_btn">
    <a id="fsbutton" onclick="toogle()" href="javascript:;" title="<?php echo $this->translate('Enter Fullscreen'); ?>"><i class="fa fa-expand"></i></a>
  </div>
</div>
<div class="ses_media_lightbox_information">
<div id="heightOfImageViewerContent">
  <div id="flexcroll" >
    <div class="ses_media_lightbox_media_info" id="ses_media_lightbox_media_info">
      <div class="ses_media_lightbox_information_top sesbasic_clearfix">
        <?php $albumUserDetails = Engine_Api::_()->user()->getUser($this->child_item->user_id); ?>
        <div class="ses_media_lightbox_author_photo">  
          <?php echo $this->htmlLink($albumUserDetails->getHref(), $this->itemPhoto($albumUserDetails, 'thumb.icon')); ?>
        </div>
        <div class="ses_media_lightbox_author_info">
          <span class="ses_media_lightbox_author_name">
            <?php echo $this->htmlLink($albumUserDetails->getHref(), $albumUserDetails->getTitle()); ?>
          </span>
          <span class="ses_media_lightbox_posted_date sesbasic_text_light">
            <?php echo date('F j',strtotime($this->child_item->creation_date)); ?>
          </span>
        </div>
      </div>
      <div class="ses_media_lightbox_item_title" id="ses_title_get"> <?php echo $this->child_item->getTitle(); ?></div>
      <div class="ses_media_lightbox_item_description" id="ses_title_description"><?php echo nl2br($this->child_item->getDescription()) ?></div>
       <div class="ses_media_lightbox_item_tags sesbasic_text_light" id="media_tags_ses" style="display: none;">
        <?php echo $this->translate('Tagged:') ?>
      </div>
      <?php if($this->canEdit){ ?>
        <div class="ses_media_lightbox_item_edit_link">
          <a id="editDetailsLink" href="javascript:void(0)" class="sesbasic_button">
            <i class="fa fa-pencil sesbasic_text_light"></i>  
            <?php echo $this->translate("Edit Details"); ?>
          </a>
        </div>
      <?php } ?>
    </div>
     <?php if($this->canEdit){ ?>
    <div class="ses_media_lightbox_edit_form" id="editDetailsForm" style="display:none;">
      <form id="changePhotoDetails">
        <input  name="title" id="titleSes" type="text" placeholder="<?php echo $this->translate('Title'); ?>" />
        <textarea id="descriptionSes" name="description" value="" placeholder="<?php echo $this->translate('Description'); ?>"></textarea>
        <input type="hidden" id="photo_id_ses" name="item_id" value="<?php echo $this->child_item->{$this->childItemPri}; ?>" />
        <input type="hidden" id="photo_itemType_ses" name="item_type" value="<?php echo $this->child_item->getType(); ?>" />
        <button id="changeSesPhotoDetails"><?php echo $this->translate('Save Changes'); ?></button>
        <button id="cancelDetailsSes"><?php echo $this->translate('Cancel'); ?></button>
      </form>
    </div>
  <?php } ?>
    <div class="ses_media_lightbox_comments clear">
    <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?>
      <?php echo $this->action("list", "comment", "sesadvancedcomment", array("type" => $this->child_item->getType(), "id" => $this->child_item->getIdentity()));
        }else{
           echo $this->action("list", "comment", "core", array("type" => $this->child_item->getType(), "id" => $this->child_item->getIdentity())); 
        }
     ?>
  	</div>
  </div>
  </div>
</div>
<a href="javascript:;" class="cross ses_media_lightbox_close_btn exit_lightbox"><i class="fa fa-close sesbasic_text_light"></i></a>
<a href="javascript:;" class="ses_media_lightbox_close_btn exit_fullscreen" title="<?php echo $this->translate('Exit Fullscreen'); ?>" onclick="toogle()"><i class="fa fa-close sesbasic_text_light"></i></a>