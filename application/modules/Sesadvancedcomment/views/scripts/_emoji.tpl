<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _emoji.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $getGallery = Engine_Api::_()->getDbTable('emotiongalleries', 'sesadvancedcomment')->getGallery(array('fetchAll' => 1)); ?>
<?php if(!$this->edit){ ?>
<!-- Sickers Search Box -->
<div class="ses_emoji_search_container sesbasic_clearfix emoji_content" <?php if(count($getGallery) == 0): ?> style="display:none;" <?php endif; ?>>
	<div class="ses_emoji_search_bar">
  	<div class="ses_emoji_search_input fa fa-search sesbasic_text_light">
    	<input type="text" placeholder='<?php echo $this->translate("Search stickers");?>' class="search_reaction_adv" />
      <button type="reset" value="Reset" class="fa fa-close sesadvcnt_reset_emoji"></button>
    </div>	
  </div>
  <div class="ses_emoji_search_content sesbasic_custom_scroll sesbasic_clearfix main_search_category_srn">
  	<div class="ses_emoji_search_cat">
     <?php $useremoji = Engine_Api::_()->getDbTable('emotioncategories','sesadvancedcomment')->getCategories(array('fetchAll'=>true)); 
        foreach($useremoji as $cat){
     ?>
    	<div class="ses_emoji_search_cat_item">
      	<a href="javascript:;" data-title="<?php echo $cat->title; ?>" class="sesbasic_animation sesadv_reaction_cat" style="background-color:<?php echo $cat->color ?>;">
        	<img src="<?php echo Engine_Api::_()->storage()->get($cat->file_id, '')->getPhotoUrl(); ?>" alt="<?php echo $cat->title; ?>" />
          <span><?php echo $cat->getTitle() ?></span>
        </a>
      </div>
    <?php } ?>
    </div>
  </div>
    <div style="display:none;position:relative;height:300px;" class="main_search_cnt_srn">
      <div class="sesbasic_loading_container" style="height:100%;"></div>
    </div>
</div>
<?php } ?>
<div style="display:<?php echo ($this->edit || !count($getGallery)) ? 'block' : 'none'; ?>;" class="emoji_content">
  <?php 
    if($this->edit)
      $class="edit";
    else
      $class = '';
    $emojis = Engine_Api::_()->getApi('emoji','sesbasic')->getEmojisArray();?>
    <div class="sesbasic_custom_scroll">
    <ul class="_simemoji">
    <?php
    foreach($emojis as $key=>$emoji){ ?>   
      <li rel="<?php echo $key; ?>"><a href="javascript:;" class="select_emoji_adv<?php echo $class; ?>"><?php echo $emoji; ?></a></li>  
  <?php 
    } ?>
    </ul>
    </div>
    <?php if(!$this->edit){ ?>
    <script type="application/javascript">
    function activityFeedAttachmentEmoji(that){
      var code = sesJqueryObject(that).parent().parent().attr('rel');
      var html = sesJqueryObject('.compose-content').html();
      if(html == '<br>')
        sesJqueryObject('.compose-content').html('');
        composeInstance.setContent(composeInstance.getContent()+' '+code);
      }
      function commentContainerSelectEmoji(that){
        var code = sesJqueryObject(that).parent().parent().attr('rel');
        var elem = sesJqueryObject(clickEmojiContentContainer).parent().parent().parent().find('.body');
        if(elem.html() == '<br>')
         elem.html('');
        elem.val(elem.val()+' '+code);
       // elem.html(elem.html()+' '+code);
         //EditFieldValue = elem.html()+' '+code;
        // sesJqueryObject(elem).mentionsInput("update");
        EditFieldValue = elem.val()
        sesJqueryObject(elem).trigger('focus');
      }
      sesJqueryObject(document).on('click','.select_emoji_adv > img',function(e){
        if(sesJqueryObject(clickEmojiContentContainer).hasClass('activity_emoji_content_a')){
          activityFeedAttachmentEmoji(this);  
        }else
          commentContainerSelectEmoji(this);
        sesJqueryObject('.exit_emoji_btn').trigger('click');
      });
    </script>
    <?php } ?>
  </div>
<?php if(!$this->edit){ ?>
<?php $useremoji = Engine_Api::_()->getDbTable('useremotions','sesadvancedcomment')->getEmotion(); 
    foreach($useremoji as $emoji){
?>
<div style="display:none;position:relative;height:100%;" class="emoji_content"><div class="sesbasic_loading_container" style="height:100%;"></div></div>
<?php } ?>
<?php } ?>