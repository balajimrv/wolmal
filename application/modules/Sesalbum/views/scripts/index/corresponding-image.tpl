<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: corresponding-image.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php if(count($this->paginator) && !empty($this->paginator)){
          foreach($this->paginator as $item){ ?>
						<a data-url="<?php echo $item->photo_id; ?>" class="sesalbum_corresponding_image_album" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($item->photo_id,$item->album_id); ?>">
            	<img src="<?php echo $item->getPhotoUrl('thumb.icon','','string'); ?>"/>
            </a>		
   <?php  }
}

 ?>