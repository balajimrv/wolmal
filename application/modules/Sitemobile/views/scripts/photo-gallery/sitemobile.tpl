<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
    if(Engine_Api::_()->seaocore()->checkEnabledNestedComment($this->subject()->getType())):
        include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';

    if($showAsLike) {
        $showLikeWithoutIcon=1;
    }
    
?>
    <div style="display: none;">
        <script type="text/javascript">
          var enabledCommentClass = 1;
          var enabledModuleForMobile = 1;
          var showAsLike = '<?php echo $showAsLike;?>';
          var showLikeWithoutIconInReplies = '<?php echo $showLikeWithoutIconInReplies;?>';
          var showLikeWithoutIcon = '<?php echo $showLikeWithoutIcon;?>';
          var showDislikeUsers = '<?php echo $showDislikeUsers;?>';
        </script>
    </div>
<?php endif;?>
<ul class="gallery" >
<?php $count=0;?>  
<?php foreach ($this->paginator as $photo):?>
  <?php $title='';?>
  <?php if(isset($this->album->owner_id) &&  !empty($this->album)) :?>
		<?php $title= $this->translate('%1$s\'s Album: %2$s',
			$this->album->getOwner()->getTitle(),
			( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
		);
				if($photo->getTitle()):
					$title .=" - ".$photo->getTitle();
				endif;
		?>
  <?php endif;?>
        <?php 
    $sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
    $ratingStarRate = 0;
    if($sitealbumModule){
    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
    $ratingStarRate = $ratingTable->getRating($photo->getIdentity(), $photo->getType());
    }
    ?>
  <?php $data_disliked = 0;?> 
  <?php if(Engine_Api::_()->seaocore()->checkEnabledNestedComment($this->subject()->getType()) && Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($photo, $this->viewer)) :?>
      <?php $data_disliked = 1;?>
  <?php endif;?>
    
  <li <?php if($photo->photo_id==$this->photo->photo_id):?> class="active" <?php endif; ?> >
    <a  href="<?php echo $photo->getPhotoUrl() ?>" data-related-url="<?php echo $photo->getHref();?>" data-subject="<?php echo $photo->getGuid(); ?>" <?php if($photo->photo_id==$this->photo->photo_id):?> class="active_photo" <?php endif; ?> rel="external" <?php if($this->canComment): ?> data-liked="<?php echo $photo->likes()->isLike($this->viewer) ? "1": "0";?>" data-disliked="<?php echo $data_disliked;?>" <?php endif; ?> data-caption="<?php echo $title;?>" data-count-caption="<?php echo ++$count." - ".$this->paginator->getTotalItemCount();?>" data-count-rating="<?php echo $ratingStarRate; ?>" data-resource-id ="<?php echo $photo->getIdentity() ?>" data-resource-type ="<?php echo $photo->getType() ?>" data-can-rate ="<?php echo $this->canRate; ?>" data-can-see-rating ="<?php echo $this->canSeeRating; ?>"></a>
    </li>
<?php endforeach; ?>
</ul>