<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobileapp.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

$caseTitle = 0;
if (in_array('userName', $this->settings_optional_array) && in_array('albumName', $this->settings_optional_array)):
    $caseTitle = 3;
elseif (in_array('userName', $this->settings_optional_array)):
    $caseTitle = 1;
elseif (in_array('albumName', $this->settings_optional_array)):
    $caseTitle = 2;
endif;

?>
<ul class="gallery" >
    <?php $count = 0; 
    foreach ($this->paginator as $photo): 
        $title = '';
            if (!empty($this->album) && $caseTitle) :
                if ($caseTitle == 3 && isset($this->album->owner_id)):
                    $title = $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->getTitle(), ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
                    );
                elseif ($caseTitle == 2):
                    $title = ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>');
                elseif ($caseTitle == 1 && isset($this->album->owner_id)):
                    $title = $title = $this->translate('%1$s\'s Album', $this->album->getOwner()->getTitle());
                else:
                    $title="";
                endif;
         endif;
           if ($photo->getTitle()):
           $PhotoTitle = $photo->getTitle();
           else:
              $PhotoTitle = ""; 
           endif;
    $likes = $photo->likes()->getLikeCount();
    $comments = $photo->comments()->getCommentCount();
    if($this->canEdit)
    $editPhotoUrl = $this->url(array('controller' => 'photo','action' => 'edit','photo_id' => $photo->photo_id),'album_extended', 'true');
    else
        $editPhotoUrl = "";
    
    if($this->canDelete)
    $deletePhotoUrl = $this->url(array('controller' => 'photo','action' => 'delete','photo_id' => $photo->photo_id),'album_extended', 'true');
else 
    $deletePhotoUrl = "";

    $viewAlbumUrl = $this->url(array('action' => 'view','album_id' => $this->album->album_id),'album_specific', 'true');
    if(!empty($this->viewer_id)){
    $reportPhotoUrl =  $this->url(Array('module' => 'core','controller' => 'report','action' => 'create','subject' => $photo->getGuid()),'default', 'true'); 
    if($this->canEdit)
    $makeProfilePhotoUrl = $this->url(Array('module' => 'user','controller' => 'edit','action' => 'external-photo','photo' => $photo->getGuid()),'user_extended', 'true');
    else
    $makeProfilePhotoUrl = "";
    $sharePhotoUrl = $this->url(Array('module' => 'activity','action' => 'share','type' => $photo->getType(),'id'=>$photo->getIdentity()),'default', 'true');
    }else{
        $reportPhotoUrl = "";
        $makeProfilePhotoUrl = "";
        $sharePhotoUrl = "";
    }   
    $this->showCount = @in_array('photoCount', $this->settings_optional_array);
  ?>
     <?php 
    $sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->enabled;
    if($sitealbumModule){
    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
    $ratingStarRate = $ratingTable->getRating($photo->getIdentity(), $photo->getType());
    }
    ?>
        <li <?php if ($photo->photo_id == $this->photo->photo_id): ?> class="active" <?php endif; ?> >
            <a  href="<?php echo $photo->getPhotoUrl() ?>" data-photo-id="<?php echo $photo->photo_id; ?>"data-like-count="<?php echo $likes;?>" data-comment-count="<?php echo $comments;?>"data-make-profile-photo-url="<?php echo $makeProfilePhotoUrl; ?>"data-photo-caption="<?php echo $photo->getDescription(); ?>"data-photo-title="<?php echo $PhotoTitle; ?>"data-view-album-url="<?php echo $viewAlbumUrl; ?>"data-edit-photo-url="<?php echo $editPhotoUrl; ?>"data-delete-photo-url="<?php echo $deletePhotoUrl; ?>"data-related-url="<?php echo $photo->getHref();?>"data-report-photo-url="<?php echo $reportPhotoUrl; ?>" data-share-photo-url="<?php echo $sharePhotoUrl; ?>" data-subject="<?php echo $photo->getGuid(); ?>" <?php if ($photo->photo_id == $this->photo->photo_id): ?> class="active_photo" <?php endif; ?> rel="external" <?php if ($this->canComment): ?>data-liked="<?php echo $photo->likes()->isLike($this->viewer) ? "1" : "0"; ?>"<?php endif; ?> data-caption="<?php echo $title; ?>" <?php if ($this->showCount) : ?>data-count-caption="<?php echo++$count . " - " . $this->paginator->getTotalItemCount();endif;?>"data-count-rating="<?php echo $ratingStarRate; ?>" data-resource-id ="<?php echo $photo->getIdentity() ?>" data-resource-type ="<?php echo $photo->getType() ?>" data-can-rate ="<?php echo $this->canRate; ?>" data-can-see-rating ="<?php echo $this->canSeeRating; ?>"></a>
        </li>

    <?php endforeach; ?>
</ul>