<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: share.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smSocialActivity.js'); ?>
<?php

$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
?>
<div class="sm-ui-popup-top ui-header ui-bar-a">
  <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ps-close-popup ui-btn-right ui-link ui-btn ui-icon-remove ui-btn-icon-notext ui-shadow-icon ui-shadow ui-corner-all" onclick="$('.ui-page-active').removeClass('dnone');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
  <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate('Reply to') . ' @' . $this->screen_name; ?></h2>
</div>

  <div id="feedshare" class="sm-share-popup">
    <div class="sm-share-popup-wrapper">
      
      
      <form action="">
      <?php $viewer = Engine_Api::_()->user()->getViewer();?>
        
        <div class="comments_info">
          <textarea rows="10" cols="45" id="activity-comment-body-twitter" class="activity-comment-body-twitter" name="body" onKeyDown="sm4.socialactivity.twitter.limitText($(this),140);"><?php echo '@' . $this->screen_name ;?></textarea>
          
          <div class="fright">
            <div id="show_loading" class="show_loading" style="display: inline-block;"><?php echo 140 - strlen('@' . $this->screen_name);?></div>
          <button type="submit" id="activity-comment-body-twitter-submit" name="submit" style="display: block;" onclick="sm4.socialactivity.twitter.post_status($(this));return false;" data-inline="true"><?php echo $this->translate('Tweet') ?></button>
          </div>
          <input id="tweetobj_id" type="hidden" value="<?php echo $this->tweet_id;?>" />
          <input id="screen_name" type="hidden" value="<?php echo $this->screen_name;?>" />
          
        </div> 
      </form>
      <br />
    </div>
  </div>

<div style="display:none;">
 
</div>