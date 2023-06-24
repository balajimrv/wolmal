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
<?php

$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
?>
<div class="sm-ui-popup-top ui-header ui-bar-a">
  <a href="javascript:void(0);" data-iconpos="notext" data-role="button" data-icon="chevron-left" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-btn-left ps-close-popup" ></a>
  <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate('Share Post'); ?></h2>
    <a data-role="button" class="header_share_submit_button ui-btn-right done" data-rel=""><?php echo $this->translate("Share")  ?></a>
</div>
<?php if (!$this->status): ?>

  <div id="feedshare" class="sm-share-popup">
    <div class="sm-share-popup-wrapper">
      <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
      <br />
      <div class="sharebox">
        <?php if ($this->attachment->getPhotoUrl()): ?>
          <div class="sharebox_photo">
            <?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
          </div>
        <?php endif; ?>
        <div>
          <div class="sharebox_title">
            <?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
          </div>
          <div class="sharebox_description">
            <?php echo $this->attachment->getDescription() ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
else :

  echo $this->message;
endif;
?>
<div style="display:none;">
  <script type="text/javascript">
  sm4.core.runonce.add(function() {
    $('.header_share_submit_button').off('vclick').on('vclick', function() {    
      if($(".ui-page-active").hasClass("dnone")){
        $(".ui-page-active").removeClass("dnone");       
      }
       sm4.activity.feedShare( $('#form_share_creation'));
    });
    $('.ps-close-popup').on('click', function() {
      $('.ui-page-active').removeClass('pop_back_max_height');
      $('.ui-page-active').removeClass('dnone');
      $(this).closest('.sm-ui-popup').remove();
      $.mobile.silentScroll(parentScrollTop);
    });
  });
    //<![CDATA[
    var toggleFacebookShareCheckbox = function(){
      $('span.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
      var checkBox = $('#post_to_facebooks');
      if (checkBox.attr("checked") == 'checked') { 
        checkBox.removeAttr('Checked');
      }
      else {
        checkBox.attr("checked", "checked");
      }
        
    
    }
    var toggleTwitterShareCheckbox = function(){
      $('span.composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
      var checkBox = $('#post_to_twitters');
      if (checkBox.attr("checked") == 'checked') { 
        checkBox.removeAttr('Checked');
      }
      else {
        checkBox.attr("checked", "checked");
      }
    
    }

    var toggleLinkedinShareCheckbox = function(){
      $('span.composer_linkedin_toggle').toggleClass('composer_linkedin_toggle_active');
      var checkBox = $('#post_to_linkedin');
      if (checkBox.attr("checked") == 'checked') { 
        checkBox.removeAttr('Checked');
      }
      else {
        checkBox.attr("checked", "checked");
      }
    
    }

    //]]>
  </script>
</div>