<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?><div class="sesbasic_breadcrumb">
  <a href="<?php echo $this->url(array('action' => 'home'), "sesalbum_general"); ?>"><?php echo $this->translate("Album Home"); ?></a>&nbsp;&raquo;
  <a href="<?php echo $this->url(array('action' => 'browse'), "sesalbum_general"); ?>"><?php echo $this->translate("Browse Albums"); ?></a>&nbsp;&raquo;
  <a href="<?php echo Engine_Api::_()->sesalbum()->getHref($this->photo->album_id); ?>"><?php echo $this->translate("View Album"); ?></a>
 	&nbsp;&raquo;
 <?php if($this->photo->getTitle()){ 
 		 echo $this->photo->getTitle(); 
  }else{
  	echo $this->translate("Photo");
    }
  ?>
</div>