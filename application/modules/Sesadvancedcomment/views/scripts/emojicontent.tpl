<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: emojicontent.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $getGallery = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->getGallery(array('fetchAll' => 1)); ?>
<div class="emoji_content ses_emoji_container sesbasic_bxs _emoji_content notclose">
<div class="ses_emoji_container_arrow"></div>
<div class="ses_emoji_container_inner sesbasic_clearfix">
  <div class="ses_emoji_container_header sesbasic_clearfix">
    <?php if(count($getGallery) > 0): ?>
      <a class="_headbtn _headbtn_add sessmoothbox" href="javascript:;" data-url="sesadvancedcomment/ajax/reaction-add"><i></i></a>
    <?php endif; ?>
    <div class="ses_emoji_container_header_tabs">
      <div class="ses_emoji_tabs owl-theme" id="ses_emoji_tabs">
         <a class="_headbtn _headbtn_search sesadv_emotion_btn_clk complete" href="javascript:;" <?php if(count($getGallery) == 0): ?> style="display:none;" <?php endif; ?>><i></i></a>
         <a class="_headbtn _headbtn_smily sesadv_tooltip sesadv_emotion_btn_clk complete" title="<?php echo $this->translate('Wall Smilies'); ?>">
          <img src="application/modules/Sesadvancedcomment/externals/images/smile.png" alt="">
         </a>
        <?php $useremoji = Engine_Api::_()->getDbTable('useremotions','sesadvancedcomment')->getEmotion(); 
              foreach($useremoji as $emoji){ ?>
          <a data-galleryid="<?php echo $emoji->gallery_id; ?>" class="_headbtn sesadv_tooltip sesadv_emotion_btn_clk" title="<?php echo $emoji->title; ?>">
          <img src="<?php echo Engine_Api::_()->storage()->get($emoji->file_id, '')->getPhotoUrl(); ?>" alt="<?php echo $emoji->title; ?>">
          </a>
       <?php } ?>
      </div>
    </div>
  </div>
  <div class="ses_emoji_holder">
    <div class="sesbasic_loading_container empty_cnt" style="height:100%;"></div>
  </div>
</div>
</div>